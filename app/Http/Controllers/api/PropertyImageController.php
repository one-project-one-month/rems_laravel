<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use App\Models\PropertyImage;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class PropertyImageController extends Controller
{

    public function index()
    {
        $data = PropertyImage::paginate(5);
        return ['data' => $data];
    }


    // Store
    public function store(Request $request)
    {
        $validatedData = Validator::make($request->all(),[
            'property_id' => 'required|exists:properties,id',
            'images.*' => 'required|mimes:jpg,png,jpeg'
        ]);

        if ($validatedData->fails()) {
            return response()->json(['status' => false,'message' => 'validation error','errors' => $validatedData->errors()], 200);
        }

        $images = $request->file('images');
        if (is_null($images) || !is_array($images)) {
            return response()->json(['error' => 'No images uploaded'], 400);
        }
        $property_id = $request->property_id;

        foreach ($images as $image) {
            $fileName = uniqid() . '_' . $image->getClientOriginalName();
            $image->storeAs('public', $fileName);

            PropertyImage::create([
                'property_id' => $property_id,
                'image_path' => $fileName
            ]);
        }
        return response()->json(['message' => 'Images uploaded successfully'], 201);
    }



    // Update
    public function update(Request $request)
    {
        $id = $request->id;
        $property_id = $request->property_id;

        $data = PropertyImage::where('id',$id)->first();

        if (isset($data)) {
            $validatedData = Validator::make($request->all(),[
                'property_id' => 'required|exists:properties,id',
                'images.*' => 'required|mimes:jpg,png,jpeg'
            ]);

            if ($validatedData->fails()) {
                return response()->json(['status' => false,'message' => 'validation error','errors' => $validatedData->errors()], 200);
            }

            $images = $request->file('images');
            if (is_null($images) || !is_array($images)) {
                return response()->json(['error' => 'No images uploaded'], 400);
            }

            foreach ($images as $image) {
                $dbImage = PropertyImage::where('id',$id)->first();
                $dbImage = $dbImage->image_path;

                if ($dbImage != null) {
                    Storage::disk('public')->delete($dbImage);
                }
                $fileName = uniqid() . '_' . $image->getClientOriginalName();
                $image->storeAs('public', $fileName);

                $update = [
                    'property_id' => $property_id,
                    'image_path' => $fileName
                ];
                $update = PropertyImage::where('id',$id)->update($update);
            }
            return response()->json(['message' => 'Update successfully'], 201);
        }
        return response()->json(["Status" => false,"Message" => "Have's Id"], 200);
    }

    // Delete
    public function delete($id)
    {
        $data = PropertyImage::where('id',$id)->first();

        if (isset($data)) {
            $dbImage = PropertyImage::where('id',$id)->first();
            $dbImage = $dbImage->image_path;

            if ($dbImage != null) {
                Storage::disk('public')->delete($dbImage);
            }
            $update = PropertyImage::where('id',$id)->delete();
            return response()->json(['message' => 'Delete successfully'], 201);
        }
        return response()->json(["Status" => false,"Message" => "Have's Id"], 200);

    }
}
