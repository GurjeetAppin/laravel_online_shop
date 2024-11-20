<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductRating;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ShopController extends Controller
{
    public function index(Request $request, $categorySlug = null, $subCategroy = null){ 
        $categorySelected = '';
        $subCategorySelected = '';
        // Pass the selected brand url
        $brandsArray = [];
        if(!empty($request->get('brand'))){
            $brandsArray = explode(',', $request->get('brand'));
        }
        $data['categories'] = Category::orderBy('name', 'ASC')->with('sub_category')->where('status', 1)->get();
        $data['brands'] = Brand::orderBy('name', 'ASC')->where('status', 1)->get();
        $products = Product::where('status', 1);

        // Apply filters here
        // Categroy filter
        if(!empty($categorySlug)){
            $category = Category::where('slug', $categorySlug)->first();
            $products = $products->where('category_id',  $category->id);
            $categorySelected = $category->id;
        }
        // Subcatgory filter
        if(!empty($subCategroy)){
            $categorySub = SubCategory::where('slug', $subCategroy)->first();
            $products = $products->where('sub_category_id', $categorySub->id);
            $subCategorySelected = $categorySub->id;
        }

        // Brand Filter

        if(!empty($request->get('brand'))){
            $brandsArray = explode(',', $request->get('brand'));
            $products = $products->whereIn('brand_id', $brandsArray);
        }

        // Range filter

        if($request->get('price_max') != '' && $request->get('price_min') != ''){
            // To get the value above the 1000
            if($request->get('price_max') == 1000){
                $products = $products->whereBetween('price', [intval($request->get('price_min')), 50000000]);
            }else{
                $products = $products->whereBetween('price', [intval($request->get('price_min')), intval($request->get('price_max'))]);
            }
        }

        // Search bar in frontend navigation menu

        if(!empty($request->get('search'))){
            $products = $products->where('title','like','%'.$request->get('search').'%');
        }


        // Sorting 

        if($request->get('sort') != ''){
            if($request->get('sort') == 'latest'){
                $products = $products->orderBy('id', 'DESC');
            }else if($request->get('sort') == 'price_asc'){
                $products = $products->orderBy('price', 'ASC');
            }else{
                $products = $products->orderBy('price', 'DESC');
            }
        }else{
            $products = $products->orderBy('id', 'DESC');
        }

        $products = $products->orderBy('id', 'DESC');
        //$products = $products->get();
        $products = $products->paginate(12);
        $data['products'] = $products;
        $data['categorySelected'] = $categorySelected;
        $data['subCategorySelected'] = $subCategorySelected;
        $data['brandsArray'] = $brandsArray;
        $data['priceMax'] = (intval($request->get('price_max')) == 0) ? 1000 : $request->get('price_max');
        $data['priceMin'] = intval($request->get('price_min'));
        $data['sort'] = $request->get('sort');
        //$data['products'] = Product::orderBy('id', 'DESC')->where('status', 1)->get();
        return view('front.shop', $data);
    }

    public function product(Request $request, $slug){
        //echo $slug;
        //$product = Product::where('slug', $slug)->with('product_images')->first();
        $product =  Product::where('slug', $slug)
                    ->withCount('product_ratings')
                    ->withSum('product_ratings', 'rating')
                    ->with(['product_images','product_ratings'])
                    ->first();
        // If slug not found then 404 message is showing
        if($product == null){
            abort(404);
        }

        // Get related products
        $relatedProducts = [];
        if($product->related_products != ''){
            $productArray = explode(',',$product->related_products);
            $relatedProducts = Product::whereIn('id',$productArray)->where('status', 1)->with('product_images')->get();
        }

        $data['product'] = $product;
        $data['relatedProducts'] = $relatedProducts;

        // Rating Calculation
        $avgRating = '0.00';
        $avgRatingPercentage = 0;
        if($product->product_ratings_count > 0){
            $avgRating = number_format(($product->product_ratings_sum_rating/$product->product_ratings_count),2);
            $avgRatingPercentage = ($avgRating*100)/5;
        }
        $data['avgRating'] = $avgRating;
        $data['avgRatingPercentage'] = $avgRatingPercentage;
        return view('front.product', $data);
    }

    public function saveRating(Request $request, $id){
        $validation = Validator::make($request->all(),[
            'name' => 'required',
            'email' => 'required|email',
            'comment' => 'required|min:10',
            'rating' => 'required'
        ]);

        if($validation->fails()){
            return response()->json([
                'status' => false,
                'errors' => $validation->errors()
            ]);
        }else{
            $countEmail = ProductRating::where('email', $request->email)->count();
            if($countEmail > 0){
                session()->flash('error', 'You already rated this product');
                return response()->json([
                    'status' => true,
                ]);
            }
            $productRating = new ProductRating;
            $productRating->product_id = $id;
            $productRating->username = $request->name;
            $productRating->email = $request->email;
            $productRating->comment = $request->comment;
            $productRating->rating = $request->rating;
            $productRating->status = 0;
            $productRating->save();
            session()->flash('success', 'Thanks for your rating');
            return response()->json([
                'status' => true,
                'message' => 'Thanks for your rating'
            ]);
        }
    }
}
