<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\ShippingCharge;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ShippingController extends Controller
{
    public function create(){
        $country = Country::get();
        $data['shippingCharger'] = ShippingCharge::select('shipping_charges.*','countries.name')
                                ->leftJoin('countries','countries.id','shipping_charges.country_id')
                                ->get();
        $data['countries'] = $country;
        return view('admin/shipping/create', $data);
    }

    public function store(Request $request){
        $countryCheck = ShippingCharge::where('country_id', $request->country)->count();
        
        $validator = Validator::make($request->all(),[
            'country' => 'required',
            'amount' => 'required|numeric'
        ]);
        if($validator->passes()){
            if($countryCheck > 0){
                session()->flash('error', 'Shipping already added.');
                return response()->json([
                    'status' => true,
                    'message' => 'Shipping already added.'
                ]);
            }
            $shipping = new ShippingCharge;
            $shipping->country_id = $request->country;
            $shipping->amount = $request->amount;
            $shipping->save();
            session()->flash('success', 'Shipping chargers store successfully');
            return response()->json([
                'status' => true,
                'message' => 'Shipping chargers store successfully'
            ]);
        }else{
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }

    public function edit(Request $request, $id){

        $country = Country::get();
        $data['shippingCharger'] = ShippingCharge::find($id);
        $data['countries'] = $country;

        return view('admin.shipping.edit', $data);
    }

    public function update(Request $request, $id){
        $shipping =  ShippingCharge::find($id);
        $validator = Validator::make($request->all(),[
            'country' => 'required',
            'amount' => 'required|numeric'
        ]);
        if($validator->passes()){
            if($shipping == null){
                session()->flash('error', 'Shipping not found.');
                return response()->json([
                    'status' => true,
                    'message' => 'Shipping not found.'
                ]);
            }
            
            $shipping->country_id = $request->country;
            $shipping->amount = $request->amount;
            $shipping->save();
            session()->flash('success', 'Shipping chargers updated successfully');
            return response()->json([
                'status' => true,
                'message' => 'Shipping chargers updated successfully'
            ]);
        }else{
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }

    public function destroy($id){
        $shippingCharge = ShippingCharge::find($id);
        if($shippingCharge == null){
            session()->flash('error', 'Shipping not found.');
            return response()->json([
                'status' => true,
                'message' => 'Shipping not found.'
            ]);
        }
        $shippingCharge->delete();
        session()->flash('success', 'Shipping deleted successfully.');
        return response()->json([
            'status' => true,
            'message' => 'Shipping deleted successfully.'
        ]);
    }

}
