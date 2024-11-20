<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DiscountCoupon;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;

class DiscountCodeController extends Controller
{
    public function index(Request $request){
        $discountCoupons = DiscountCoupon::latest();
        if(!empty($request->get('keyword'))){
            $discountCoupons = $discountCoupons->whereAny(['code', 'name', 'discount_amount'],'like','%'.$request->get('keyword').'%');
        }
        $discountCoupons = $discountCoupons->paginate(10);
        return view('admin.coupon.list', ['discountCoupons' => $discountCoupons]);
    }

    public function create(){
        return view('admin.coupon.create');
    }

    public function store(Request $request){
        $validator = Validator::make($request->all(),[
            'code' => 'required',
            'type' => 'required',
            'discount_amount' => 'required|numeric',
            'status' => 'required'
        ]);

        if($validator->passes()){

            // Starting date must be greater than current date.
            if(!empty($request->starts_at)){
                $now = Carbon::now(); // To get the time/date with Carbon libaray.
                $startAt = Carbon::createFromFormat('Y-m-d H:i:s',$request->starts_at);
                if($startAt->lte($now) == true){
                    return response()->json([
                        'status' => false,
                        'errors' => ['starts_at' => 'Starts date can not be less than current date time']
                    ]);
                }
            }

            // Expire date must be greater than start date.

            if(!empty($request->starts_at) && !empty($request->expires_at)){
                $now = Carbon::now(); // To get the time/date with Carbon libaray.
                $expiresAt = Carbon::createFromFormat('Y-m-d H:i:s',$request->expires_at);
                $startAt = Carbon::createFromFormat('Y-m-d H:i:s',$request->starts_at);
                if($expiresAt->gt($startAt) == false){
                    return response()->json([
                        'status' => false,
                        'errors' => ['expires_at' => 'Expiry date must be greator than start date']
                    ]);
                }
            }

            $discountCode = new DiscountCoupon;
            $discountCode->code = $request->code;
            $discountCode->name = $request->name;
            $discountCode->description = $request->description;
            $discountCode->max_uses = $request->max_uses;
            $discountCode->max_uses_user = $request->max_uses_user;
            $discountCode->type = $request->type;
            $discountCode->discount_amount = $request->discount_amount;
            $discountCode->min_amount = $request->min_amount;
            $discountCode->status = $request->status;
            $discountCode->starts_at = $request->starts_at;
            $discountCode->expires_at = $request->expires_at;
            //print_r($discountCode); die();
            $discountCode->save();
            $message = 'Discount Coupon added successfully';
            session()->flash('success', $message);
            return response()->json([
                'status' => true,
                'message' => $message,
                'data' => $discountCode
            ]);

        }else{
            return response()->json([
                'status' => false,
                'errors'  => $validator->errors()
            ]);
        }

    }

    public function edit(Request $request, $id){
        $data['coupon'] = DiscountCoupon::find($id);
        if($data['coupon'] == null){
            session()->flash('error', 'Discount coupon not found');
            return redirect()->route('coupon.index');
        }
        return view('admin.coupon.edit', $data);

    }

    public function update(Request $request, $id){
        $discountCode = DiscountCoupon::find($id);

        if($discountCode == null){
            session()->flash('error', 'Discount Coupon not found.');
            return response()->json([
                'status' => true
            ]);
        }

        $validator = Validator::make($request->all(),[
            'code' => 'required',
            'type' => 'required',
            'discount_amount' => 'required|numeric',
            'status' => 'required'
        ]);

        if($validator->passes()){
            // Starting date must be greater than current date.
            /* if(!empty($request->starts_at)){
                $now = Carbon::now(); // To get the time/date with Carbon libaray.
                $startAt = Carbon::createFromFormat('Y-m-d H:i:s',$request->starts_at);
                if($startAt->lte($now) == true){
                    return response()->json([
                        'status' => false,
                        'errors' => ['starts_at' => 'Starts date can not be less than current date time']
                    ]);
                }
            } */

            // Expire date must be greater than start date.

            if(!empty($request->starts_at) && !empty($request->expires_at)){
                $now = Carbon::now(); // To get the time/date with Carbon libaray.
                $expiresAt = Carbon::createFromFormat('Y-m-d H:i:s',$request->expires_at);
                $startAt = Carbon::createFromFormat('Y-m-d H:i:s',$request->starts_at);
                if($expiresAt->gt($startAt) == false){
                    return response()->json([
                        'status' => false,
                        'errors' => ['expires_at' => 'Expiry date must be greator than start date']
                    ]);
                }
            }

            
            $discountCode->code = $request->code;
            $discountCode->name = $request->name;
            $discountCode->description = $request->description;
            $discountCode->max_uses = $request->max_uses;
            $discountCode->max_uses_user = $request->max_uses_user;
            $discountCode->type = $request->type;
            $discountCode->discount_amount = $request->discount_amount;
            $discountCode->min_amount = $request->min_amount;
            $discountCode->status = $request->status;
            $discountCode->starts_at = $request->starts_at;
            $discountCode->expires_at = $request->expires_at;
            //print_r($discountCode); die();
            $discountCode->save();
            $message = 'Discount Coupon Updated successfully';
            session()->flash('success', $message);
            return response()->json([
                'status' => true,
                'message' => $message,
                'data' => $discountCode
            ]);

        }else{
            return response()->json([
                'status' => false,
                'errors'  => $validator->errors()
            ]);
        }

    }

    public function destroy(Request $request, $id){
        $discountCode = DiscountCoupon::find($id);
        if($discountCode == null){
            session()->flash('error', 'Discount Coupon not found');
            return response()->json([
                'status' => true
            ]);           
        }
        $discountCode->delete();
        session()->flash('success', 'Discount Coupon deleted successfully.');
        return response()->json([
            'status' => true
        ]);
    }
}
