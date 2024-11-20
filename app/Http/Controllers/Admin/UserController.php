<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function index(Request $request){
        $users = User::latest('created_at');
        if(!empty($request->get('keyword'))){
            $users = $users->whereAny(['name', 'phone'],'like','%'.$request->get('keyword').'%');
        }
        //$users = $users->get();
        $users = $users->paginate(10);
        return view('admin.users.list', ['users' => $users]);
    }

    public function create(Request $request){
        return view('admin.users.create');
    }

    public function store(Request $request){
        $validator = Validator::make($request->all(),[
            'name' => 'required',
            'password' => 'required|min:5',
            'email' => 'required|email|unique:users',
            'phone' => 'required'
        ]);
        if($validator->passes()){
            $user = new User;
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->phone = $request->phone;
            $user->status = $request->status;
            $user->save();
            session()->flash('success', 'User added succssfully');
            return response()->json([
                'status' => true,
                'message' => 'New user added successfully;'
            ]);
        }else{
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }

    public function edit(Request $request, $id){
        $user = User::find($id);
        if($user == null){
            session()->flash('error', 'User not found.');
            return redirect()->route('users.index')->with('User not found.');
        }
        return view('admin.users.edit',['user' => $user]);
    }

    public function update(Request $request, $id){
        $user = User::find($id);
        if($user == null){
            session()->flash('error', 'User not found.');
        return response()->json([
            'status' => true,
            'message' => 'User not found.'
        ]);
        }
        $validator = Validator::make($request->all(),[
            'name' => 'required',
            'email' => 'required|email|unique:users,email,'.$id.',id',
            'phone' => 'required',
        ]);
        if($validator->passes()){
            $user->name = $request->name;
            $user->email = $request->email;
            if($request->password != ''){
                $user->password = Hash::make($request->password);
            }
            $user->phone = $request->phone;
            $user->status = $request->status;
            $user->save();
            session()->flash('success', 'User Update succssfully');
            return response()->json([
                'status' => true,
                'message' => 'User Update successfully;'
            ]);
        }else{
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }

    function destroy(Request $request, $id){
        $user = User::find($id);
        if(empty($user)){
            session()->flash('error', 'User not found.');
            return response()->json([
                'status' => true,
                'message' => 'User not found.'
            ]);
        }
       
        $user->delete();
        session()->flash('success', 'User deleted succssfully.');
        return response()->json([
            'status' => true,
            'message' => 'User deleted successfully.'
        ]);
    }
}
