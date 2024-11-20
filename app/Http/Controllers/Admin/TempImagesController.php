<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TempImage;
//use Image;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
class TempImagesController extends Controller
{
    //
    public function create(Request $request){
        $manager = new ImageManager(new Driver());
        $image = $request->image;
        if(!empty($image)){
            $extension = $image->getClientOriginalExtension();
            $newName = time().'.'.$extension;
            $tempImage = new TempImage;
            $tempImage->name = $newName;
            $tempImage->save();
            $image->move(public_path().'/temp', $newName);

            // Generate Thumbnail
            $sourcePath = public_path().'/temp/'.$newName;
            $destPath = public_path().'/temp/thumb/'.$newName;
            $image = $manager->read($sourcePath);
            $image->scale(300, 275);
            $image->save($destPath);


            return response()->json([
                'status' => true,
                'image_id' => $tempImage->id,
                'imagePath' => asset('/temp/thumb/'.$newName),
                'message' => 'Image uploaded successfully'
            ]);
        }

    }
}
