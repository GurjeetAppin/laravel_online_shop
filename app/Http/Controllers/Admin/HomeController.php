<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\TempImage;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

/* Authentcation Libaray*/
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class HomeController extends Controller
{
    //
    public function index(){
        $data['totalOrders'] = Order::where('status','!=','cancelled')->count();
        $data['totalProducts'] = Product::count();
        $data['totalCustomers'] = User::where('role', 1)->count();
        // Total Revenue
        $data['totalRevenue'] = Order::where('status','!=','cancelled')->sum('grand_total');
        // This month Revenue
        $startOfMonth = Carbon::now()->startOfMonth()->format('Y-m-d');
        $currentDate = Carbon::now()->format('Y-m-d');
        $data['revenueThisMonth'] = Order::where('status','!=','cancelled')
                                ->whereDate('created_at','>=',$startOfMonth)
                                ->whereDate('created_at','<=',$currentDate)
                                ->sum('grand_total');
        // Last month Revenue
        $lastMonthStartDate = Carbon::now()->subMonth()->startOfMonth()->format('Y-m-d');
        $lastMonthEndDate = Carbon::now()->subMonth()->endOfMonth()->format('Y-m-d');
        $data['lastMonthName'] = Carbon::now()->subDays(30)->format('M');
        $data['revenueLastMonth'] = Order::where('status','!=','cancelled')
                                    ->whereDate('created_at','>=', $lastMonthStartDate)
                                    ->whereDate('created_at','<=', $lastMonthEndDate)
                                    ->sum('grand_total');
                            

        // Last 30 days Revenue
        $lastThirtyDaysStartDate = Carbon::now()->subDays(30)->format('Y-m-d');
        $data['revenueLastThirtyDays'] = Order::where('status','!=','cancelled')
                                    ->whereDate('created_at','>=', $lastThirtyDaysStartDate)
                                    ->whereDate('created_at','<=', $currentDate)
                                    ->sum('grand_total');

        // delete temp images here
        $dayBeforeToday = Carbon::now()->subDays(1)->format('Y-m-d H:i:s');
        $tempImages = TempImage::where('created_at','<=',$dayBeforeToday)->get();
        foreach($tempImages as $tempImage){
            $path = public_path('/temp/'.$tempImage->name);
            $thumbPath = public_path('/temp/thumb/'.$tempImage->name);
            // Delete main image
            if(File::exists($path)){
                File::delete($path);
            }
             // Delete thumb image
             if(File::exists($thumbPath)){
                File::delete($thumbPath);
            }
           
            TempImage::where('id', $tempImage->id)->delete();

        }


    /*  $admin = Auth::guard('admin')->user();
        echo "Welcome :- ".$admin->name.'<a href="'.route('admin.logout').'">Logout</a>'; */
        return view('admin.dashboard', $data);
    }

    public function logout(){
        Auth::guard('admin')->logout();
        return redirect()->route('admin.login');
    }
}
