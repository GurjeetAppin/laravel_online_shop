<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use App\Models\Category;
use App\Models\TempImage;
// Uploading Images
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
class CategoryController extends Controller
{
    //
    public function index(Request $request){
        /* $categories = Category::orderBy('created_at', 'DESC')->paginate(3); */ // Both are working 
        $categories = Category::latest();
        if(!empty($request->get('keyword'))){
            $categories = $categories->whereAny(['name', 'slug'],'like','%'.$request->get('keyword').'%');
        }
        $categories = $categories->paginate(10);
        return view('admin.category.list', ['categories' => $categories]);
    }

    public function create(){
        return view('admin.category.create');
    }

    public function store(Request $request){
        $validator = Validator::make($request->all(),[
            'name' =>'required',
            'slug' => 'required | unique:categories',
        ],[
            'name.required' => "Please Enter the valid name",
            'slug.required' => "Please Enter Unique Slug name"
        ]);

        if($validator->passes()){
            $category = new Category;
            $category->name = $request->name;
            $category->slug = $request->slug;
            $category->status = $request->status;
            $category->showHome = $request->showHome;
            $category->save();

            // Image 
            if(!empty($request->image_id)){
                 // Upload Images
                $manager = new ImageManager(new Driver());
                $tempImage = TempImage::find($request->image_id);
                $extArray = explode('.',$tempImage->name);
                $ext = last($extArray);
                $newImageName = $category->id.'.'.$ext;
                // Copy image
                $sourcePath = public_path().'/temp/'.$tempImage->name;
                $destinationPath = public_path().'/uploads/category/'.$newImageName;
                File::copy($sourcePath,$destinationPath);
                // Generate Image Thumbnail
                
                    $imageDestination = public_path().'/uploads/category/thumb/'.$newImageName;
                    $img = $manager->read($sourcePath);
                     //$img->resize(450,600);
                    $img->scale(450, 600, function( $constraint){
                        $constraint->upsize();
                    });
                    $img->save($imageDestination); 
               
                $category->image = $newImageName;
                $category->save();
            }
            session()->flash('success', 'Category Added Successfully');
            return response()->json([
                'status' => true,
                'message' => 'Category Added Successfully'
            ]);
        }else{
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }

    public function edit(Request $request, $id){
        $categories = Category::find($id);
        $tempImage = new TempImage;
        if(empty($categories)){
            return redirect()->route('category.index');
        }
        return view('admin.category.edit',['categories' => $categories]);
    }

    public function update(Request $request, $id){
       
        $category = Category::find($id);
       
        if(empty($category)){
            $this->$request->session()->flash('error', 'Category not found.');
            return response()->json([
                'status' => false,
                'notFound' => true,
                'message' => 'Category not found'
            ]);
        }
        $validator = Validator::make($request->all(),[
            'name' => 'required',
            'slug' => 'required | unique:categories,slug,'.$category->id.',id',
        ]);
        
       
        if($validator->passes()){
         
            $category->name = $request->name;
            $category->slug = $request->slug;
            $category->status = $request->status;
            $category->showHome = $request->showHome;
            $category->save();
           
             // Image 
             if(!empty($request->image_id)){
               
                // Upload Images
                $manager = new ImageManager(new Driver());
                
                $tempImage = TempImage::find($request->image_id);
                $extArray = explode('.',$tempImage->name);
                $ext = last($extArray);
                $newImageName = $category->id.'.'.$ext;
               
                // Copy image
                $sourcePath = public_path().'/temp/'.$tempImage->name;
                $destinationPath = public_path().'/uploads/category/'.$newImageName;
                File::copy($sourcePath,$destinationPath);
                // Generate Image Thumbnail
                
                    $imageDestination = public_path().'/uploads/category/thumb/'.$newImageName;
                    $img = $manager->read($sourcePath);
                    
                    //$img->resize(450,600);
                    $img->scale(450, 600, function($constraint){
                        $constraint->upsize();
                    });
                    $img->save($imageDestination); 
               
                $category->image = $newImageName;
                $category->save();
            }



            session()->flash('success', 'Category Update Successfully.');
            return response()->json([
                'status' => true,
                'message' => 'Category Update Successfully'
            ]);
        }else{
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }

        

    }

    public function destroy(Request $request, $id){
        $category = Category::find($id);
        if(empty($category)){
            //return redirect()->route('category.index');
           session()->flash('error','Category not found');
            return response()->json([
                'status' => true,
                'message' => 'Category not found'
            ]);
        }
       /*  
            Remove the image when delete category
        */
        File::delete(public_path().'/uploads/category/thumb/'.$category->image);
        File::delete(public_path().'/uploads/category/'.$category->image); 
        
        $category->delete();
        session()->flash('success','Category Deleted Successfully');
        return response()->json([
            'status' => true,
            'message' => 'Category Deleted Succssefully'
        ]);
    }


}
