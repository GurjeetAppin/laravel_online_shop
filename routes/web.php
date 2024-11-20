<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Admin\AdminLoginController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\HomeController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DiscountCodeController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ProductImageController;
use App\Http\Controllers\Admin\ProductSubCategoryController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\ShippingController;
use App\Http\Controllers\Admin\TempImagesController;

/* Admin Middleware */
use App\Http\Middleware\AdminAuthenticate;
use App\Http\Middleware\GuestAuthenticate;
use Illuminate\Http\Request;

use Illuminate\Support\Str;

/* Sub Categories */
use App\Http\Controllers\Admin\SubCategoryController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\FrontController;
use App\Http\Controllers\ShopController;

/* Route::get('/', function () {
    return view('welcome');
}); */

/* Route::get('/test', function () {
    orderEmail(9);
}); */

/**************** Frontend Routes *************/
Route::get('/', [FrontController::class,'index'])->name('front.home');
Route::post('add-to-wishlist',[FrontController::class,'addToWishlist'])->name('front.addToWishlist');
Route::get('page/{slug}', [FrontController::class,'page'])->name('front.page');

/************** Send Contact us form Email with data *************/
Route::post('send-contact-email',[FrontController::class,'sendContactEmail'])->name('front.sendContactEmail');

/****************** Forgot Password *****************/
Route::get('forgot-password',[AuthController::class,'forgotPassword'])->name('front.forgotPassword');
Route::post('proccess-forgot-password',[AuthController::class,'proccessForgotPassword'])->name('front.proccessForgotPassword');
Route::get('reset-password/{token}',[AuthController::class,'resetPassword'])->name('front.resetPassword');
Route::post('process-reset-password',[AuthController::class,'processResetPassword'])->name('front.processResetPassword');

/****************** Rating ****************/
Route::post('save-rating/{productId}',[ShopController::class,'saveRating'])->name('front.saveRating');


Route::get('shop/{categorySlug?}/{subCatgorySlug?}', [ShopController::class,'index'])->name('front.shop');
Route::get('product/{slug}', [ShopController::class,'product'])->name('front.product');

Route::get('cart',[CartController::class,'cart'])->name('front.cart');
Route::post('add-to-cart',[CartController::class,'addToCart'])->name('front.addToCart');
Route::post('update-cart',[CartController::class,'upDateCart'])->name('front.upDateCart');
Route::post('delete-item',[CartController::class,'deleteItem'])->name('front.deleteItem.cart');
Route::get('checkout',[CartController::class,'checkout'])->name('front.checkout');
Route::post('process-checkout',[CartController::class,'processCheckout'])->name('front.processCheckout');
Route::get('thanks/{orderId}',[CartController::class,'thankyou'])->name('front.thankyou');
Route::post('get-order-summery',[CartController::class,'getOrderSummery'])->name('front.getOrderSummery');
Route::post('apply-discount',[CartController::class,'applyDiscount'])->name('front.applyDiscount');
Route::post('remove-discount',[CartController::class,'removeCoupon'])->name('front.removeCoupon');



/****************** Frontend Authentication *****************/
Route::group(['prefix' => 'account/'], function(){
    Route::group(['middleware' => 'guest'], function(){
        Route::get('login',[AuthController::class,'login'])->name('account.login');
        Route::post('login',[AuthController::class,'authenticate'])->name('account.authenticate');
        Route::get('register',[AuthController::class,'register'])->name('account.register');
        Route::post('process-register',[AuthController::class,'processRegister'])->name('account.processRegister');
        });
    
    Route::group(['middleware' => 'auth'], function(){
        Route::get('profile',[AuthController::class,'profile'])->name('account.profile');
        Route::get('logout',[AuthController::class,'logout'])->name('account.logout');
        Route::get('myorders',[AuthController::class,'orders'])->name('account.orders');
        Route::get('order-detail/{id}',[AuthController::class,'orderDetail'])->name('account.orderDetail');
        Route::get('my-wishlist',[AuthController::class,'wishList'])->name('account.wishlist');
        Route::post('remove-product-from-wishlist',[AuthController::class,'removeProductFromWishlist'])->name('account.removeProductFromWishlist');
        Route::post('update-profile',[AuthController::class,'updateProfile'])->name('account.updateProfile');
        Route::post('update-address',[AuthController::class,'updateAddress'])->name('account.updateAddress');
        Route::get('change-password',[AuthController::class,'showChangePasswordForm'])->name('account.changePassword');
        Route::post('proccess-change-password',[AuthController::class,'changePassword'])->name('account.proccessChangePassword');
    });
});

/* Admin Middleware route */
Route::group(['prefix' => 'admin/'], function(){

    // User Dashboard Routes
    Route::group(['middleware'=>'guest-auth'],function(){
        Route::get('login',[AdminLoginController::class,'index'])->name('admin.login');
        Route::post('authenticate',[AdminLoginController::class,'authenticate'])->name('admin.authenticate');
    });
    

    // Admin Dashboard Routes
    Route::group(['middleware'=>'admin-auth'],function(){
        Route::get('dashboard',[HomeController::class,'index'])->name('admin.dashboard');
        Route::get('logout',[HomeController::class,'logout'])->name('admin.logout');

        // Categroy Routes
        Route::get('categories/create',[CategoryController::class,'create'])->name('categories.create'); // Add category Page
        Route::get('categories',[CategoryController::class,'index'])->name('category.index'); // Listing Categories
        Route::post('categories',[CategoryController::class,'store'])->name('categories.store'); // Insert Category

        // Get Slug
        Route::get('slug',function(Request $request){
            $slug = '';
            if(!empty($request->title)){
                $slug = Str::slug($request->title);
            }
            return response()->json([
                'status' => true,
                'slug' => $slug
            ]);
        })->name('getSlug');
        
        // Category Routes
        Route::get('categories/{id}/edit',[CategoryController::class,'edit'])->name('categories.edit');
        Route::put('categories/{id}',[CategoryController::class,'update'])->name('categories.update');
        Route::delete('categories/{id}',[CategoryController::class,'destroy'])->name('categories.delete');

         // temp-images.create
         Route::post('upload-temp-image',[TempImagesController::class,'create'])->name('temp-images.create');

        // Sub Category Routes
        Route::get('sub-categories/create',[SubCategoryController::class,'create'])->name('sub-categories.create');
        Route::post('sub-categories',[SubCategoryController::class,'store'])->name('sub-categories.store');
        Route::get('sub-categories',[SubCategoryController::class,'index'])->name('sub-categories.index');
        Route::get('sub-categories/{id}/edit',[SubCategoryController::class,'edit'])->name('sub-categories.edit');
        Route::put('sub-categories/{id}',[SubCategoryController::class,'update'])->name('sub-categories.update');
        Route::delete('sub-categories/{id}',[SubCategoryController::class,'destroy'])->name('sub-categories.delete');

        // Brand
        Route::get('brands',[BrandController::class,'index'])->name('brands.index');
        Route::get('brands/create', [BrandController::class, 'create'])->name('brands.create');
        Route::post('brands',[BrandController::class,'store'])->name('brands.store');
        Route::get('brands/{id}/edit', [BrandController::class, 'edit'])->name('brands.edit');
        Route::put('brands/{id}',[BrandController::class,'update'])->name('brands.update');
        Route::delete('brands/{id}',[BrandController::class,'destroy'])->name('brands.delete');

        // Products
        Route::get('products', [ProductController::class,'index'])->name('products.index');
        Route::get('products/create', [ProductController::class, 'create'])->name('products.create');
        Route::post('products',[ProductController::class,'store'])->name('products.store');
        Route::get('products/{id}/edit', [ProductController::class, 'edit'])->name('products.edit');
        Route::put('products/{id}', [ProductController::class, 'update'])->name('products.update');
        Route::delete('products/{id}', [ProductController::class, 'destroy'])->name('products.delete');
        Route::delete('products-images',[ProductImageController::class,'destroy'])->name('products-image-destroy');
        // Product Images Upload
        Route::post('products-images/update',[ProductImageController::class,'update'])->name('products-images.update');
        // Get Products SubCategories
        Route::get('product-subcategories', [ProductSubCategoryController::class,'index'])->name('product-subcategories.index');
        // Related Products
        Route::get('get-products', [ProductController::class, 'getProducts'])->name('products.getProducts');

        // Shipping Routes
        Route::get('shipping/create',[ShippingController::class,'create'])->name('shipping.create');
        Route::post('shipping',[ShippingController::class,'store'])->name('shipping.store');
        Route::get('shipping/{id}',[ShippingController::class,'edit'])->name('shipping.edit');
        Route::put('shipping/{id}',[ShippingController::class,'update'])->name('shipping.update');
        Route::delete('shipping/{id}', [ShippingController::class,'destroy'])->name('shipping.delete');

        // Coupon Code Routes
        Route::get('coupons',[DiscountCodeController::class,'index'])->name('coupons.index');
        Route::get('coupons/create',[DiscountCodeController::class,'create'])->name('coupons.create');
        Route::post('coupons',[DiscountCodeController::class,'store'])->name('coupons.store');
        Route::get('coupons/{id}/edit',[DiscountCodeController::class,'edit'])->name('coupons.edit');
        Route::put('coupons/{id}',[DiscountCodeController::class,'update'])->name('coupons.update');
        Route::delete('coupons/{id}',[DiscountCodeController::class,'destroy'])->name('coupons.delete');

        // Orders Details Routes
        Route::get('orders',[OrderController::class,'index'])->name('orders.index');
        Route::get('orders/{id}',[OrderController::class,'detail'])->name('orders.detail');
        Route::post('orders/change-status/{id}',[OrderController::class,'changeOrderStatus'])->name('orders.changeOrderStatus');
        
        // Send Inovice Email
        Route::post('orders/send-email/{id}',[OrderController::class,'sendInvoiceEmail'])->name('orders.sendInvoiceEmail');

        // Users Routes
        Route::get('users',[UserController::class,'index'])->name('users.index');
        Route::get('users/create',[UserController::class,'create'])->name('users.create');
        Route::post('users',[UserController::class,'store'])->name('users.store');
        Route::get('users/{id}/edit',[UserController::class,'edit'])->name('users.edit');
        Route::put('users/{id}',[UserController::class,'update'])->name('users.update');
        Route::delete('users/{id}',[UserController::class,'destroy'])->name('users.destroy');

        // Pages
        Route::get('pages',[PageController::class,'index'])->name('pages.index');
        Route::get('pages/create', [PageController::class,'create'])->name('pages.create');
        Route::post('pages', [PageController::class,'store'])->name('pages.store');
        Route::get('pages/{id}/edit',[PageController::class,'edit'])->name('pages.edit');
        Route::put('pages/{id}',[PageController::class,'update'])->name('pages.update');
        Route::delete('pages/{id}',[PageController::class,'destroy'])->name('pages.destroy');

        //  Setting and Change Password
        Route::get('change-password',[SettingController::class,'showChangePasswordForm'])->name('admin.showChangePasswordForm');
        Route::post('process-change-password',[SettingController::class,'processChangePassword'])->name('admin.processChangePassword');

        // Products Ratings.
        Route::get('ratings',[ProductController::class,'productRatings'])->name('products.productRatings');
        Route::get('change-rating-status',[ProductController::class,'changeRatingStatus'])->name('products.changeRatingStatus');
 


          
    });



});


