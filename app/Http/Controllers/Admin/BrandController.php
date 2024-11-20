<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BrandController extends Controller
{

    public function index(Request $request){
        $brands = Brand::latest();
        if($request->get('keyword')){
            $brands = $brands->where('name', 'like', '%'.$request->keyword.'%');
        }
        $brands = $brands->paginate(10);
        return view('admin.brands.list',['listBrands' => $brands]);
    }

    public function create(){
        return view('admin.brands.create');
    }

    public function store(Request $request){
       
        $validator = Validator::make($request->all(),[
            'name' => 'required',
            'slug' => 'required | unique:brands'
        ]);  
        if($validator->passes()){
            $brand = new Brand;
            $brand->name = $request->name;
            $brand->slug = $request->slug;
            $brand->status = $request->status;
            $brand->save();
            $request->session()->flash('success', 'Brand Created Successfully');
            
            return response()->json([
                'status' => true,
                'message' => "Brand Created Successfully"
            ]);
        }else{
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }

    public function edit(Request $request, $id){
        $brand = Brand::find($id);
        if(empty($brand)){
            $request->session()->flash('error', 'Record not found.');
            return redirect()->route('brands.index');
        }

        return view('admin.brands.edit', ['brand' => $brand]);
    }

    public function update(Request $request, $id){

        $brand = Brand::find($id);
        if(empty($brand)){
            $request->session()->flash('error', 'Record not found.');
            return response()->json([
                'status' => false,
                'notFound' => true,
                'message' => 'Brand not found'
            ]);
        }

        $validator = Validator::make($request->all(),[
            'name' => 'required',
            'slug' => 'required | unique:brands,slug,'.$brand->id.',id',
        ]);  
        if($validator->passes()){
            $brand->name = $request->name;
            $brand->slug = $request->slug;
            $brand->status = $request->status;
            $brand->save();
            $request->session()->flash('success', 'Brand Updated Successfully');
            
            return response()->json([
                'status' => true,
                'message' => "Brand Updated Successfully"
            ]);
        }else{
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }

    public function destroy(Request $request, $id){
        $brand = Brand::find($id);
        if(empty($brand)){
            $request->session()->flash('error', 'Brand not Found');
            return response()->json([
                'status' => 'true',
                'message' => 'Brand not Found'
            ]);
        }

        $brand->delete();
        $request->session()->flash('success', 'Brand deleted Successfully.');
        return response()->json([
            'status' => true,
            'message' => 'Brand deleted Successfully.'
        ]);
    }


}
