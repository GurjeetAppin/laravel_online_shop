<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PageController extends Controller
{
    public function index(Request $request){
        $pages =  Page::latest();
        if($request->keyword != ''){
            $pages = $pages->where('name','like','%'.$request->keyword.'%');
        }
        $pages = $pages->paginate(10);
        return view('admin.pages.list', ['pages' => $pages]);
    }

    public function create(Request $request){
        return view('admin.pages.create');
    }

    public function store(Request $request){
        $validator = Validator::make($request->all(),[
            'name' => "required",
            'slug' => 'required'
        ]);
        if($validator->passes()){
            $pages =new Page;
            $pages->name = $request->name;
            $pages->slug = $request->slug;
            $pages->content = $request->content;
            $pages->save();

            session()->flash('success', 'Pages created successfully');
            return response()->json([
                'status' => true,
                'message' => "Page created successfully."
            ]);
        }else{
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }

    public function edit($id){
        $page = Page::find($id);
        if(empty($page)){
            session()->flash('error', 'Page not found');
            return redirect()->route('pages.index')->with('Page not found');
        }
        return view('admin.pages.edit', ['page' => $page]);
    }

    public function update(Request $request, $id){
        $page = Page::find($id);
        if($page == null){
            session()->flash('error', 'Page not found.');
            return response()->json([
                'status' => true
            ]);
        }
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'slug' => 'required|unique:pages,slug,'.$page->id.',id'
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }else{
            $page->name = $request->name;
            $page->slug = $request->slug;
            $page->content = $request->content;
            $page->save();
            session()->flash('success', "Page updated successfully.");
            return response()->json([
                'status' => true,
                'message' => 'Page updated successfully'
            ]);
        }
    }

    public function destroy($id){
        $page = Page::find($id);
        if(empty($page)){
            session()->flash('error', 'Record not found.');
            return response()->json([
                'status' => true,
            ]);
        }
        $page->delete();
        session()->flash('success', 'Page deleted successfully.');
        return response()->json([
            'status' => true,
            'message' => 'Page deleted successfully.'
        ]);
        
    }
}
