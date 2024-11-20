<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\CustomerAddress;
use App\Models\DiscountCoupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ShippingCharge;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Http\Client\ResponseSequence;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

use function Laravel\Prompts\confirm;

class CartController extends Controller
{
    public function addToCart(Request $request){
        $product = Product::with('product_images')->find($request->id);
        if($product == null){
            return response()->json([
                'status' => false,
                'message' => 'Product not found'
            ]);
        }

        if(Cart::count() > 0){
            //echo "Product already in cart";
            // Products found in Cart
            // Check if this product already in the cart
            // Return a message product already add in your cart
            // If product not found in the cart then add product in cart.
            $cartContent = Cart::content(); // We get the product is add in cart.
            $productAlreadyExist = false;

            foreach($cartContent as $item){
                if($item->id == $product->id){
                    $productAlreadyExist = true;
                }
            }

            if($productAlreadyExist == false){
                Cart::add($product->id, $product->title, 1, $product->price, ['productImage' => (!empty($product->product_images)) ?  $product->product_images->first() : '']);
                $status = true;
                $message = '<strong>'.$product->title.'</strong> added in your cart successfully';
                session()->flash('success', $message);
            }else{
                $status = false;
                $message = $product->title." already added in cart";
            }

        }else{
            //echo "Cart is empty now add product in cart";
            // Cart is empty
            Cart::add($product->id, $product->title, 1, $product->price, ['productImage' => (!empty($product->product_images)) ?  $product->product_images->first() : '']);
            $status = true;
            $message = '<strong>'.$product->title.'</strong> added in your cart successfully';
            session()->flash('success', $message);
        }

        return response()->json([
            'status' => $status,
            'message' =>  $message
        ]);
    }

    public function cart(){
        //dd(Cart::content());
        $data['cartContent'] = Cart::content();
        return view('front.cart', $data);
    }

    public function upDateCart(Request $request){
        $rowId = $request->rowId;
        $qty = $request->qty;

        // Check qty avaliable in stock
        $itemInfo = Cart::get($rowId);
        $product = Product::find($itemInfo->id);
        if($product->track_qty == 'Yes'){
            if($qty  <= $product->qty){
                Cart::update($rowId, $qty);
                $message = 'Cart updated successfully';
                $status = true;
                session()->flash('success', $message);
            }else{
                $message = 'Requested qty ('.$qty.') not avaliable in stock';
                $status = false;
                session()->flash('error', $message);
            }
        }else{
            Cart::update($rowId, $qty);
            $message = 'Cart updated successfully';
            $status = true;
            session()->flash('success', $message);
        }
       
        return response()->json([
            'status' => $status,
            'message' => $message
        ]);
    }

    public function deleteItem(Request $request){
        $rowId = $request->rowId;
        $itemInfo = Cart::get($rowId);
        if($itemInfo == null){
            $errorMessage = 'Item not found in cart';
            session()->flash('error', $errorMessage);
            return response()->json([
                'status' => false,
                'message' => $errorMessage
            ]);
        }
        Cart::remove($request->rowId);
        session()->flash('success', 'Item remove form cart successfully');
        return response()->json([
            'status' => true,
            'message' => 'Item remove form cart successfully'
        ]);
    }

    public function checkout(){
        $discount = 0;
        // If cart is empty redirect to cart
        if(Cart::count() == 0){
            return redirect()->route('front.cart');
        }

        // If user not login then redirect to login page
        if(Auth::check() == false){
            if(!session()->has('url.intended')){
            session(['url.intended' => url()->current()]);
           }
            return redirect()->route('account.login');
        }

        $user = Auth::user()->id;
        $customerAddress = CustomerAddress::where('user_id',$user)->first();
        session()->forget('url.intended');

        $countries = Country::orderBy('name', 'ASC')->get();
        $subTotal = Cart::subtotal(2,'.','');
        // Apply discount here
        if(session()->has('code')){
            $code = session()->get('code');
            if($code->type == 'percent'){
                $discount = ($code->discount_amount/100)*$subTotal;
            }else{
                $discount = $code->discount_amount;

            }
        }

        // Calculate Shipping here
        if($customerAddress != null){
            $userCountry = $customerAddress->country_id;
            $shippingInfo = ShippingCharge::where("country_id",$userCountry)->first();
            //$shippingInfo->amount;
            $totalQty = $totalShippingCharge = $grandTotal = 0;
            foreach(Cart::content() as $item){
                $totalQty += $item->qty;
            }
    
            $totalShippingCharge = $totalQty*$shippingInfo->amount;
            $grandTotal = ($subTotal-$discount)+$totalShippingCharge;
        }else{
            $grandTotal = ($subTotal-$discount);
            $totalShippingCharge = 0;
        }
       
        return view('front.checkout', ['countries' => $countries, 'customerAddress' => $customerAddress, 'totalShippingCharge' => $totalShippingCharge, 'grandTotal' => $grandTotal, 'discount' => $discount]);
    }

    public function processCheckout(Request $request){
        // Step 1 :-  Apply Validation
      $validator = Validator::make($request->all(),[
        'first_name' => 'required|min:5',
        'last_name' => 'required',
        'email' => 'required|email',
        'country' => 'required',
        'address' => 'required',
        'city' => 'required',
        'state' => 'required',
        'zip' => 'required',
        'mobile' => 'required'
      ]);

      if($validator->fails()){
        return response()->json([
            'status' => false,
            'errors' => $validator->errors(),
            'message' => 'Please fix the errors'
        ]);
      }

      // Step 2 :- Save customer address
      $user = Auth::user();     
        CustomerAddress::updateOrCreate(
            // First we check data is exists
            ['user_id' => $user->id],
            // 
            [ 
                'user_id' => $user->id,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'mobile' => $request->mobile,
                'country_id' => $request->country,
                'address' => $request->address,
                'apartment' => $request->apartment,
                'city' => $request->city,
                'state' => $request->state,
                'zip' => $request->zip
            ]
        
        );

        // Step 3 :- Store data in Order table
        // Check the payment methods
        if($request->payment_method == 'cod'){
            // Calculate shipping
            $shipping = 0;
            $discount = 0;
            $discountCodeId = null;
            $promoCode = '';
            $subTotal = Cart::subtotal(2,'.','' );
            // Apply discount here
            if(session()->has('code')){
                $code = session()->get('code');
                if($code->type == 'percent'){
                    $discount = ($code->discount_amount/100)*$subTotal;
                }else{
                    $discount = $code->discount_amount;
                }
                $discountCodeId = $code->id;
                $promoCode = $code->code;
            }

            $totalQty = 0;
            foreach(Cart::content() as $item){
                $totalQty += $item->qty;
            }
            $shippingInfo = ShippingCharge::where('country_id',$request->country)->first();
            if($shippingInfo != null){
                $shipping = $totalQty*$shippingInfo->amount;
                $grandTotal = ($subTotal-$discount)+$shipping;
               
            }else{
                $shippingInfo = ShippingCharge::where('country_id','rest_of_world')->first();
                $shipping = $totalQty*$shippingInfo->amount;
                $grandTotal = ($subTotal-$discount)+$shipping;
            }
           
            $order = new Order;
            $order->subtotal =  $subTotal;
            $order->shipping =  $shipping;
            $order->grand_total =  $grandTotal;
            $order->payment_status =  'not paid';
            $order->status =  'pending  ';
            $order->discount =  $discount;
            $order->coupon_code_id =  $discountCodeId;
            $order->coupon_code =  $promoCode;
            $order->user_id = $user->id;
            $order->first_name =  $request->first_name;
            $order->last_name =  $request->last_name;
            $order->email =  $request->email;
            $order->mobile =  $request->mobile;
            $order->country_id =  $request->country;
            $order->address =  $request->address;
            $order->apartment =  $request->apartment;
            $order->state =  $request->state;
            $order->city =  $request->city;
            $order->zip =  $request->zip;
            $order->notes =  $request->order_notes;
            $order->save();

        // Step :- 4 Store order items in order items table.
            foreach(Cart::content() as $item){
                $orderItem =new OrderItem;
                $orderItem->product_id = $item->id;
                $orderItem->order_id = $order->id;
                $orderItem->name = $item->name;
                $orderItem->qty = $item->qty;
                $orderItem->price = $item->price;
                $orderItem->total = $item->price*$item->qty;
                $orderItem->save();

                // Update product stock.
                $productdata = Product::find($item->id);
                if($productdata->track_qty == 'Yes'){
                    $currentQty = $productdata->qty;
                    $updatedQty = $currentQty-$item->qty;
                    $productdata->qty = $updatedQty;
                    $productdata->save();
                } 
                
            }


            // Send Order Email
            orderEmail($order->id, 'customer');

            session()->flash('success', 'You have successfully placed your order');
            // Remove the item from cart page when the order is placed.
            Cart::destroy();
            // Remove the coupon code from session
            session()->forget('code');
            return response()->json([
                'status' => true,
                'message' => 'Order saved successfully',
                'orderId' => $order->id
            ]);
        }else{

        }
    }

    public function thankyou($id){
        return view('front.thankyou', ['id' => $id]);
    }

    public function getOrderSummery(Request $request){
        $subTotal = Cart::subtotal(2,'.','');
        $discount = 0;
        $discountString = '';
        // Apply discount here
        if(session()->has('code')){
            $code = session()->get('code');
            if($code->type == 'percent'){
                $discount = ($code->discount_amount/100)*$subTotal;
            }else{
                $discount = $code->discount_amount;
            }
        $discountString =   '<div class="mt-4" id="discount-response">
                                <strong>'.session()->get('code')->code.'</strong>
                                <a class="btn btn-sm btn-danger" id="remove-discount"><i class="fa fa-times"></i>*</a>
                            </div>';
        }

        

        if($request->country_id > 0){
            $totalQty = 0;
            foreach(Cart::content() as $item){
                $totalQty += $item->qty;
            }
            $shippingInfo = ShippingCharge::where('country_id',$request->country_id)->first();
            if($shippingInfo != null){
                $shippingCharge = $totalQty*$shippingInfo->amount;
                $grandTotal = ($subTotal-$discount)+$shippingCharge;
                return response()->json([
                    'status' => true,
                    'grandTotal' => number_format($grandTotal,2),
                    'discount' => number_format($discount,2),
                    'discountString' => $discountString,
                    'shippingCharge' => number_format($shippingCharge,2)
                ]);
            }else{
                $shippingInfo = ShippingCharge::where('country_id','rest_of_world')->first();
                $shippingCharge = $totalQty*$shippingInfo->amount;
                $grandTotal = ($subTotal-$discount)+$shippingCharge;
                return response()->json([
                    'status' => true,
                    'grandTotal' => number_format($grandTotal,2),
                    'discount' => number_format($discount,2),
                    'discountString' => $discountString,
                    'shippingCharge' => number_format($shippingCharge,2)
                ]);
            }
        }else{
            return response()->json([
                'status' => true,
                'grandTotal' => number_format($subTotal-$discount,2),
                'discount' => number_format($discount,2),
                'discountString' => $discountString,
                'shippingCharge' => number_format(0,2)
            ]);           
        }        
    }

    public function applyDiscount(Request $request){
        $code = DiscountCoupon::where('code', $request->code)->first();
        $subTotal = Cart::subtotal(2,'.','');
        if($code == null){
            return response()->json([
                'status' => false,
                'message' => 'Invalid discount coupon'
            ]);
        }

        // Check coupon start date valid or not
        $now = Carbon::now();
       // echo $now->format('Y-m-d H:i:s');

        if($code->starts_at != ''){
            $startDate = Carbon::createFromFormat('Y-m-d H:i:s',$code->starts_at);
            if($now->lt($startDate)){
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid discount coupon'
                ]);
            }
        }

        if($code->expires_at != ''){
            $endDate = Carbon::createFromFormat('Y-m-d H:i:s',$code->expires_at);
            if($now->gt($endDate)){
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid discount coupon end date'
                ]);
            }
        }
        
        // Return how many times coupon use
        if($code->max_uses > 0){
            $couponUse = Order::where('coupon_code_id', $code->id)->count();
            if($couponUse >= $code->max_uses){
                return response()->json([
                    'status' => false,
                    'message' => 'Coupon code is access his limit'
                ]);
            }
        }
        
        // Use coupon code how many time user. Max uses user check.
        if($code->max_uses_user > 0){
            $couponUsedByUser = Order::where(['coupon_code_id' => $code->id, 'user_id' => Auth::user()->id])->count();
            if($couponUsedByUser >= $code->max_uses_user){
                return response()->json([
                    'status' => false,
                    'message' => 'You used already this coupon.'
                ]);
            }
        }
        
        // Minimum amount condition check
        if($code->min_amount > 0){
            if($subTotal < $code->min_amount){
                return  response()->json([
                    'status' => false,
                    'message' => 'Your min amount must be $'.$code->min_amount.'.'
                ]);
            }

        }

        session()->put('code', $code);
        return $this->getOrderSummery($request);
    }

    // Remove coupon code from checkout
    public function removeCoupon(Request $request){
        session()->forget('code');
        return $this->getOrderSummery($request);
    }


}

?>