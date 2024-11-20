<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductRating;
use App\Models\SubCategory;
use App\Models\TempImage;
use Illuminate\Support\Facades\File;
// use Faker\Core\File;
use Illuminate\Http\Client\ResponseSequence;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
// Uploading Images
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class ProductController extends Controller
{

    public function index(Request $request){
        //$data['products'] = Product::latest('id')->with('product_images')->paginate();
        $products = Product::latest('id')->with('product_images');
        //dd($product);
        if($request->get('keyword') != ''){
            $products = $products->whereAny(['title','price','sku'],'like','%'.$request->keyword.'%');
        }
        $products = $products->paginate();
        return view('admin.products.list', ['products' => $products]);
    }
    
    public function create(Request $request){
        $data = [];
        $categories = Category::orderBy('name', 'ASC')->get();
        $brands = Brand::orderBy('name', 'ASC')->get();
        $data['categories'] = $categories;
        $data['brands'] = $brands;
        return view('admin.products.create', $data);   
    }

    

    public function store(Request $request){
        $rules = [
                    'title' => 'required',
                    'slug' => 'required|unique:products',
                    'price' => 'required|numeric',
                    'sku' => 'required|unique:products',
                    'track_qty' => 'required|in:Yes,No',
                    'category' => 'required|numeric',
                    'is_featured' => 'required|in:Yes,No'
                ];

        if(!empty($request->track_qty) && $request->track_qty == 'Yes'){
            $rules['qty'] =  'required|numeric';
        }
        $validator = Validator::make($request->all(), $rules);
        if($validator->passes()){
            $product = new Product;
            $product->title = $request->title;
            $product->slug = $request->slug;
            $product->description = $request->description;
            $product->short_description = $request->short_description;
            $product->shipping_returns = $request->shipping_returns;
            $product->price = $request->price;
            $product->compare_price = $request->compare_price;
            $product->sku = $request->sku;
            $product->barcode = $request->barcode;
            $product->track_qty = $request->track_qty;
            $product->qty = $request->qty;
            $product->status = $request->status;
            $product->category_id = $request->category;
            $product->sub_category_id = $request->sub_category;
            $product->brand_id = $request->brand;
            $product->is_featured = $request->is_featured;
            $product->related_products = (!empty($request->related_products)) ? implode(',',$request->related_products) : '';
            //dd($product);
            $product->save();

            // Store Gallery Images
            if(!empty($request->image_array)){
                foreach($request->image_array as $temp_image_id){
                    // Upload Images
                    $manager = new ImageManager(new Driver());
                    // Get data from data base
                    $tempImageInfo = TempImage::find($temp_image_id);
                    $extArr = explode('.', $tempImageInfo->name);
                    $ext = last($extArr); // Get extension like jpg,png, gif etc.

                    // Save Image in Product Image table
                    $productImage = new ProductImage();
                    $productImage->product_id = $product->id;
                    $productImage->image = 'Null';
                    $productImage->save();
                    
                    // Unique Image name
                    $imageName = $product->id.'-'.$productImage->id.'-'.time().'.'.$ext;
                    $productImage->image = $imageName;
                    $productImage->save();

                    // Generate Product Thumbnails
                    
                    // Large Image folder
                    $sourcePath = public_path().'/temp/'.$tempImageInfo->name;
                    $destPath = public_path().'/uploads/products/large/'.$imageName;
                    $image = $manager->read($sourcePath);
                    $image->scale(1400, null, function($constrant){
                        $constrant->aspectRatio();
                    });
                    $image->save($destPath);
                
                    // Small Image folder
                    $destSmallPath = public_path().'/uploads/products/small/'.$imageName;
                    $image = $manager->read($sourcePath);
                    $image->scale(300, 300);
                    $image->save($destSmallPath);
                }
            }

            session()->flash('success', 'Product added successfull');
            return response()->json([
                'status' => true,
                'message' => 'Product added successfully'
            ]);
        }else{
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }

    public function edit(Request $response, $id){
        $data = [];
        $product = Product::find($id);
        if(empty($product)){
            return redirect()->route('products.index')->with('error', 'Product not found');
        }
        // fetch Product image
        $productImages = ProductImage::where('product_id', $product->id)->get();  
        // Fetch related products
        $relatedProducts = [];
        if($product->related_products != ''){
            $productArray = explode(',', $product->related_products);
            $relatedProducts = Product::whereIn('id', $productArray)->get();
        }
        $data['product'] = $product;
        $subCategory = SubCategory::where('category_id', $product->category_id)->get();
        $data['subCategory'] = $subCategory;
        $categories = Category::orderBy('name', 'ASC')->get();
        $brands = Brand::orderBy('name', 'ASC')->get();
        $data['categories'] = $categories;
        $data['brands'] = $brands;
        $data['productImages'] = $productImages;
        $data['relatedProducts'] = $relatedProducts;
        return view('admin.products.edit', $data);
    }

    public function update(Request $request, $id){
        $product = Product::find($id);
        $rules = [
            'title' => 'required',
            'slug' => 'required|unique:products,slug,'.$product->id.'id',
            'price' => 'required|numeric',
            'sku' => 'required|unique:products,sku,'.$product->id.'id',
            'track_qty' => 'required|in:Yes,No',
            'category' => 'required|numeric',
            'is_featured' => 'required|in:Yes,No'
        ];
        if(!empty($request->track_qty) && $request->track_qty == 'Yes'){
            $rules['qty'] =  'required|numeric';
        }
        $validator = Validator::make($request->all(), $rules);
        if($validator->passes()){
            $product->title = $request->title;
            $product->slug = $request->slug;
            $product->description = $request->description;
            $product->short_description = $request->short_description;
            $product->shipping_returns = $request->shipping_returns;
            $product->price = $request->price;
            $product->compare_price = $request->compare_price;
            $product->sku = $request->sku;
            $product->barcode = $request->barcode;
            $product->track_qty = $request->track_qty;
            $product->qty = $request->qty;
            $product->status = $request->status;
            $product->category_id = $request->category;
            $product->sub_category_id = $request->sub_category;
            $product->brand_id = $request->brand;
            $product->is_featured = $request->is_featured;
            $product->related_products = (!empty($request->related_products)) ? implode(',',$request->related_products) : '';
            //dd($product);
            $product->save();

            session()->flash('success', 'Product Update successfull');
            return response()->json([
                'status' => true,
                'productData' => $product,
                'message' => 'Product Update successfully'
            ]);
        }else{
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }

    public function destroy(Request $request, $id){
        $product = Product::find($id);
        if(empty($product)){
            session()->flash('error', 'Product not found');
            return response()->json([
                'status' => false,
                'notFound' => true
            ]);
        }
        $productImages = ProductImage::where('product_id',$id)->get();
        if(!empty($productImages)){
            foreach($productImages as $productImage){
                File::delete(public_path('uploads/products/large/'.$productImage->image));
                File::delete(public_path('uploads/products/small/'.$productImage->image));
            }
            ProductImage::where('product_id', $id)->delete();
        }
        $product->delete();
        session()->flash('success', 'Product deleted successfully');
        return response()->json([
            'status' => true,
            'message' => 'Product Deleted successfully'
        ]);
    }

    // Related Products
    public function getProducts(Request $request){
        $tempProduct = [];
        if($request->term != ''){
            $products = Product::where('title','like','%'.$request->term.'%')->get();
            if($products != null){
                foreach($products as $product){
                    $tempProduct[] = array('id' => $product->id, 'text' => $product->title);
                }
            }
        }
        //print_r($tempProduct);
        return response()->json([
            'tags' => $tempProduct,
            'status' => true
        ]);
    }

    // Product Rating section
    public function productRatings(Request $request){
        $ratings = ProductRating::select('product_ratings.*','products.title as productTitle')->orderBy('product_ratings.created_at', 'DESC');
        $ratings = $ratings->leftJoin('products', 'products.id','product_ratings.product_id');
        if($request->get('keyword')){
            $ratings = $ratings->orWhere('products.title','like','%'.$request->keyword.'%');
            $ratings = $ratings->orWhere('product_ratings.username','like','%'.$request->keyword.'%');
        }
        $ratings = $ratings->paginate(10);
        return view('admin.products.ratings', ['ratings' => $ratings]);
    }

    public function changeRatingStatus(Request $request){
        $productRating = ProductRating::find($request->id);
        $productRating->status = $request->status;
        $productRating->save();
        session()->flash('success', 'Product rating status change successfully');
        return response()->json([
            'status' => true,
            'message' => 'Product rating status change'
        ]);

    }


    
}
