<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use App\Models\PropertyImage;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class PropertyImageController extends Controller
{

// Store
        /**
         * @OA\Get(
         *     path="/api/v1/property-images/show",
         *     summary="Upload property images show",
         *     tags={"Property Images"},
         *     summary="property-images",
*           description="property-images here",
*      @OA\Response(
*          response=201,
*          description="Register Successfully",
*          @OA\JsonContent()
*       ),
*      @OA\Response(
*          response=200,
*          description="Register Successfully",
*          @OA\JsonContent()
*       ),
*      @OA\Response(
*          response=422,
*          description="Unprocessable Entity",
*          @OA\JsonContent()
*       ),
*      @OA\Response(response=400, description="Bad request"),
*      @OA\Response(response=404, description="Resource Not Found"),
* )
         */
    public function index()
    {
        $data = PropertyImage::paginate(5);
        return ['data' => $data];
    }


    // Store
        /**
         * @OA\Post(
         *     path="/api/v1/property-images",
         *     summary="Upload property images",
         *     tags={"Property Images"},
         *     @OA\RequestBody(
         *         required=true,
         *         @OA\MediaType(
         *             mediaType="multipart/form-data",
         *             @OA\Schema(
         *                 required={"property_id", "images[]"},
         *                 @OA\Property(
         *                     property="property_id",
         *                     type="integer",
         *                     description="The ID of the property"
         *                 ),
         *                 @OA\Property(
         *                     property="images[]",
         *                     type="array",
         *                     @OA\Items(
         *                         type="string",
         *                         format="binary",
         *                         description="Image files"
         *                     ),
         *                     description="Array of images to upload"
         *                 )
         *             )
         *         )
         *     ),
         *     @OA\Response(
         *         response=201,
         *         description="Images uploaded successfully",
         *         @OA\JsonContent(
         *             @OA\Property(property="message", type="string", example="Images uploaded successfully")
         *         )
         *     ),
         *     @OA\Response(
         *         response=400,
         *         description="No images uploaded",
         *         @OA\JsonContent(
         *             @OA\Property(property="error", type="string", example="No images uploaded")
         *         )
         *     ),
         *     @OA\Response(
         *         response=422,
         *         description="Validation error",
         *         @OA\JsonContent(
         *             @OA\Property(property="status", type="boolean", example=false),
         *             @OA\Property(property="message", type="string", example="validation error"),
         *             @OA\Property(
         *                 property="errors",
         *                 type="object",
         *                 @OA\Property(
         *                     property="images.0",
         *                     type="array",
         *                     @OA\Items(
         *                         type="string",
         *                         example="The images.0 field must be a file of type: jpg, png, jpeg."
         *                     )
         *                 )
         *             )
         *         )
         *     ),
         *     security={
         *         {"bearerAuth": {}}
         *     }
         * )
         */
        public function store(Request $request)
        {
            $validatedData = Validator::make($request->all(),[
                'property_id' => 'required|exists:properties,id',
                'images.*' => 'required|mimes:jpg,png,jpeg'
            ]);

            if ($validatedData->fails()) {
                return response()->json(['status' => false, 'message' => 'validation error', 'errors' => $validatedData->errors()], 422);
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
/**
         * @OA\Post(
         *     path="/api/v1/property-images/update",
         *     summary="Upload property images update",
         *     tags={"Property Images"},
         *     @OA\RequestBody(
         *         required=true,
         *         @OA\MediaType(
         *             mediaType="multipart/form-data",
         *             @OA\Schema(
         *                 required={"property_id", "images[]"},
         *                 @OA\Property(
         *                     property="id",
         *                     type="integer",
         *                     description="ID"
         *                 ),
         *                 @OA\Property(
         *                     property="property_id",
         *                     type="integer",
         *                     description="The ID of the property"
         *                 ),
         *                 @OA\Property(
         *                     property="images[]",
         *                     type="array",
         *                     @OA\Items(
         *                         type="string",
         *                         format="binary",
         *                         description="Image files"
         *                     ),
         *                     description="Array of images to upload"
         *                 )
         *             )
         *         )
         *     ),
         *     @OA\Response(
         *         response=201,
         *         description="Images uploaded successfully",
         *         @OA\JsonContent(
         *             @OA\Property(property="message", type="string", example="Images uploaded successfully")
         *         )
         *     ),
         *     @OA\Response(
         *         response=400,
         *         description="No images uploaded",
         *         @OA\JsonContent(
         *             @OA\Property(property="error", type="string", example="No images uploaded")
         *         )
         *     ),
         *     @OA\Response(
         *         response=422,
         *         description="Validation error",
         *         @OA\JsonContent(
         *             @OA\Property(property="status", type="boolean", example=false),
         *             @OA\Property(property="message", type="string", example="validation error"),
         *             @OA\Property(
         *                 property="errors",
         *                 type="object",
         *                 @OA\Property(
         *                     property="images.0",
         *                     type="array",
         *                     @OA\Items(
         *                         type="string",
         *                         example="The images.0 field must be a file of type: jpg, png, jpeg."
         *                     )
         *                 )
         *             )
         *         )
         *     ),
         *     security={
         *         {"bearerAuth": {}}
         *     }
         * )
         */
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
/**
         * @OA\Delete(
         *     path="/api/v1/property-images/{id}",
         *     summary="property images delete",
         *     tags={"Property Images"},
         *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         description="Agent ID",
 *         required=true,
 *         @OA\Schema(type="string")
 *     ),
         *    @OA\Response(
 *         response=200,
 *         description="Agent deleted successfully",
 *         @OA\JsonContent()
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Unprocessable Entity",
 *         @OA\JsonContent()
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Bad request",
 *         @OA\JsonContent()
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Resource not found",
 *         @OA\JsonContent()
 *     ),
*     security={
*         {"bearerAuth": {}}
*     }
* )
*/
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
