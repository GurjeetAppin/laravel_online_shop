<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/* For Form validation Libaray */
use Illuminate\Support\Facades\Validator;
/* Authentcation Libaray*/
use Illuminate\Support\Facades\Auth;


class AdminLoginController extends Controller
{
    //

    public function index(){
        return view('admin.login');
    }

    public function authenticate(Request $request){
       
       /*  $validate = $request->validate([
            'email' => 'required',
            'password' => 'required'
        ]); */

        $validate = Validator::make($request->all(),[
            'email' => 'required',
            'password' => 'required'
        ],
        [
          'email.required' => 'This is not valid email',
          'password.required' => 'Enter the valid password'  
        ]
        );
       
        if($validate->passes()){
            //return "Form valid";
            if(Auth::guard('admin')->attempt(['email' => $request->email, 'password' => $request->password], $request->get('remember'))){
                $admin = Auth::guard('admin')->user();
                if( $admin->role == 2){
                    return redirect()->route('admin.dashboard');
                }else{
                    Auth::guard('admin')->logout();
                    return redirect()->route('admin.login')->with('error', 'You are not authorized to access admin panel.');
                }
            }else{
                return redirect()->route('admin.login')->with('error', 'Either Email/Password is incorrect');
            }
        }else{
            return redirect()->route('admin.login')
            ->withErrors($validate)
            ->withInput($request->only('email'));
        }

        return $request;
    }

   
}
