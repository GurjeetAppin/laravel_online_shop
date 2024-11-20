<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Category;
use App\Models\SubCategory;

class SubCategoryController extends Controller
{
    //

    public function create(){
        $category = Category::orderBy('name', 'ASC')->get();
        $data['categories'] = $category;
        return view('admin.sub_category.create',$data);
    }

    public function index(Request $request){
        $subCategories = SubCategory::select('sub_categories.*','categories.name as categoryName')->latest('id')->leftJoin('categories','categories.id','sub_categories.category_id');
        if(!empty($request->get('keyword'))){
            $subCategories = $subCategories->whereAny(['sub_categories.name', 'sub_categories.slug'],'like','%'.$request->get('keyword').'%');
            $subCategories = $subCategories->orWhere('categories.name','like','%'.$request->get('keyword').'%');
        }
        $subCategories = $subCategories->paginate(10);
        return view('admin.sub_category.list',['subCategories' => $subCategories]);
    }

    public function store(Request $request){
        $validator = Validator::make($request->all(),[
            'name' => 'required',
            'slug' => 'required | unique:sub_categories',
            'status' => 'required',
            'category' => 'required'
        ]);

        if($validator->passes()){
            $subCategories = new SubCategory;
            $subCategories->name = $request->name;
            $subCategories->slug = $request->slug;
            $subCategories->status = $request->status;
            $subCategories->category_id = $request->category;
            $subCategories->showHome = $request->showHome;
            $subCategories->save();
            session()->flash('success', 'Sub Category created successfully.');
            return response()->json([
                'status' => true,
                'message' => 'Sub Category created successfull,'
            ]);
        }else{
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }

    }

    public function edit(Request $request, $id){
        $editSubCategory= SubCategory::find($id);
        
        if(empty($editSubCategory)){
            session()->flash('error', 'Sub Category not found');
            return redirect()->route('sub-categories.index');
        }
        $category = Category::orderBy('name','ASC',)->get();
        $data['categories'] = $category;
        $data['editSubCategory'] = $editSubCategory;
        
        return view('admin.sub_category.edit', $data);
    }

    public function update(Request $request, $id){
        $subCategories = SubCategory::find($id);
        if(empty($subCategories)){
           session()->flash('message','Records not Found');
           return response()->json([
                'status' => false,
                'notFound' => true
           ]);
            //return redirect()->route('sub-categories.index');
        }
        $validator = Validator::make($request->all(),[
                'name' => 'required',
                'slug' => 'required | unique:sub_categories,slug,'.$subCategories->id.'id',
                'status' => 'required',
                'category' => 'required'
            ]);
           

        if($validator->passes()){
            $subCategories->name = $request->name;
            $subCategories->slug = $request->slug;
            $subCategories->status = $request->status;
            $subCategories->category_id = $request->category;
            $subCategories->showHome = $request->showHome;
            $subCategories->save();
            session()->flash('success','Sub Category Updated Successfully');
            return response()->json([
                'status' => true,
                'message' => 'Sub Category Updated Successfully'
            ]);
        }else{
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
                'message' => 'Something went wrong'
            ]);
        }
        
    }


    public function destroy(Request $request, $id){
        $subCategories = SubCategory::find($id);
        if(empty($subCategories)){
            session()->flash('message','Record not found');
            return response()->json([
                'status' => false,
                'notFound' => true
            ]);
        }
        $subCategories->delete();
        session()->flash('success','Sub Category Deleted successfully.');
        return response()->json([
            'status' => true,
            'message' => 'Sub Category Deleted successfully.'
        ]);
    }
}
