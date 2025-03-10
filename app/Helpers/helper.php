<?php
// Retrun Categories

use App\Mail\OrderEmail;
use App\Models\Category;
use App\Models\Country;
use App\Models\Order;
use App\Models\Page;
use App\Models\ProductImage;
use Illuminate\Support\Facades\Mail;

function getCategories(){
    return  Category::orderBy('name', 'ASC')
            ->with('sub_category')
            ->orderBy('id', 'DESC')
            ->where('status', 1)
            ->where('showHome', 'Yes')
            ->get();
}

function getProductImage($productId){
    return ProductImage::where('product_id', $productId)->first();
}

function orderEmail($orderId, $userType='customer'){
    $order = Order::where('id', $orderId)->with('items')->first();
    
    if($userType == 'customer'){
        $subject = 'Thanks for your order';
        $email = $order->email;
    }else{
        $subject = 'You have recieved an order';
        $email = env('ADMIN_EMAIL');
    }
    //dd($order);
    $mailData = [
        'subject' => $subject,
        'order' => $order,
        'userType' => $userType
    ];
   // dd($email);
    Mail::to($email)->send(new OrderEmail($mailData));
    
}

function getCountryInfo($id){
    return Country::where('id', $id)->first();
}

// Return the pages from backend and showing into the frontend.
function staticPages(){
    $pages = Page::orderBy('name', 'ASC')->get();
    return $pages;
}



?>