<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;

class OrderController extends Controller
{
   public function index(Request $request){
    $orders = Order::latest('orders.created_at')->select('orders.*','users.name','users.email'); // latest function get the records according to created_at column
    $orders = $orders->leftJoin('users', 'users.id', 'orders.user_id');
    if($request->get('keyword') != ''){
        $orders = $orders->where('users.name','like','%'.$request->get('keyword').'%');
        $orders = $orders->orWhere('users.email','like','%'.$request->get('keyword').'%');
        $orders = $orders->orWhere('orders.id','like','%'.$request->get('keyword').'%');
    }
    $orders = $orders->paginate(10);
    $data['orders'] = $orders;
    return view('admin.orders.list', $data);
   }    

   // Admin Order details
   public function detail($orderId){
    $order  =    Order::select('orders.*','countries.name as countryName')
                ->where('orders.id',$orderId)
                ->leftJoin('countries','countries.id','orders.country_id')
                ->first();
    $orderItems = OrderItem::where('order_id', $orderId)->get();
    return view('admin.orders.detail',['order' => $order, 'orderItems' => $orderItems]);
   }

   public function changeOrderStatus(Request $request, $orderId){
        $order = Order::find($orderId);
        $order->status = $request->status;
        $order->shipped_date = $request->shipped_date;
        $order->save();
        session()->flash('success', 'Order status Updated successfully');
        return response()->json([
            'status' => true,
            'message' => 'Order status Updated successfully'
        ]);
   }

   public function sendInvoiceEmail(Request $request, $orderId){
        orderEmail($orderId, $request->userType);
        session()->flash('success', 'Order Email send successfully');
        return response()->json([
            'status' => true,
            'message' => 'Order Email send successfully'
        ]);        
   }

   
}
