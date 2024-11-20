<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductImage;
use Illuminate\Database\Eloquent\Casts\Json;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
// Uploading Images
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class ProductImageController extends Controller
{
    public function update(Request $request){
        // Upload Images
        $manager = new ImageManager(new Driver());
        $image = $request->image;
        $ext = $image->getClientOriginalExtension();
        $sourcePath = $image->getPathName();

        // Save Image in Product Image table
        $productImage = new ProductImage();
        $productImage->product_id = $request->product_id;
        $productImage->image = 'Null';
        $productImage->save();

        // Unique Image name
        $imageName = $request->product_id.'-'.$productImage->id.'-'.time().'.'.$ext;
        $productImage->image = $imageName;
        $productImage->save();

        // Generate Product Thumbnails
                    
        // Large Image
        $destPath = public_path().'/uploads/products/large/'.$imageName;
        $image = $manager->read($sourcePath);
        $image->scale(1400, null, function($constrant){
            $constrant->aspectRatio();
        });
        $image->save($destPath);
    
        // Small Image
        $destSmallPath = public_path().'/uploads/products/small/'.$imageName;
        $image = $manager->read($sourcePath);
        $image->scale(300, 300);
        $image->save($destSmallPath);

        return response()->json([
            'status' => true,
            'image_id' => $productImage->id,
            'imagePath' => asset('uploads/products/small/'.$productImage->image),
            'message' => 'Image saved successfully.'
        ]);
    }

    public function destroy(Request $request){
        $productImage = ProductImage::find($request->id);
        if(empty($productImage)){
            return response()->json([
                'status' => false,
                'message' => 'Image not found'
            ]);
        }
        // Delete Images from Folder
        File::delete(public_path('uploads/products/large/'.$productImage->image));
        File::delete(public_path('uploads/products/small/'.$productImage->image));

        // Delete Image form database
        $productImage->delete();
        return response()->json([
            'status' => true,
            'message' => 'Image Deleted successfully.'
        ]);
    }
}
