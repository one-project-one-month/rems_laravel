<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Reviews;
use http\Message;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ReviewsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
 /**
* @OA\Get(
* path="/api/v1/reviews",
* operationId="Reviews",
* tags={"Reviews"},
* summary="Reviews",
* description="Reviews here",
*      @OA\Response(
 *         response=200,
 *         description="Reviews successfully",
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
 *     security={{"bearerAuth":{}}}
* )
*/
    public function index()
    {
        $comments="";
        $ratings="";
        $review=Reviews::paginate(10);
        foreach ($review as $r){
            $comments.=$r->comments."<br>";
            $ratings.=$r->rating."<br>";
        }
        return response()->json([
            'review' => $review,
            'message' => 'success',
            'status' => true,
            'comments'=>$comments,
            'rating'=>$ratings,
            'count' => $review->total(),
            'currentPage' => $review->currentPage(),
            'lastPage' => $review->lastPage()
        ]);

    }

    /**
     * Store a newly created resource in storage.
     */
/**
 * * @OA\Post(
* path="/api/v1/reviews",
* operationId="reviews Create",
* tags={"Reviews"},
* summary="reviews create",
* description="reviews create here",
*     @OA\RequestBody(
*         @OA\JsonContent(),
*         @OA\MediaType(
*            mediaType="multipart/form-data",
*            @OA\Schema(
*               type="object",
*               required={"user_id","property_id", "rating", "comments"},
*               @OA\Property(property="user_id", type="integer",example="1"),
*               @OA\Property(property="property_id", type="integer",example="1"),
*               @OA\Property(property="rating", type="integer",example="3"),
*               @OA\Property(property="comments", type="text",example="text"),
*            ),
*        ),
*    ),
*      @OA\Response(
 *         response=200,
 *         description="Reviews successfully",
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
 *     security={{"bearerAuth":{}}}
* )
*/
    public function store(Request $request)

    {
        $validatedData = Validator::make($request->all(), [
            'property_id' => 'required',
            'rating' => 'required|integer',
            'comments' => 'required|string',
        ]);
        if ($validatedData->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'validation error',
                'errors' => $validatedData->errors()
            ], 422);
        }

        Reviews::create([
            'user_id' => Auth::user()->id,
            'property_id' => $request->input('property_id'),
            'rating' => $request->input('rating'),
            'comments' => $request->input('comments')
        ]);
        return response()->json([
            'message' => 'success',
            'status' => true
        ]);
    }


    /**
     * Display the specified resource.
     */

/**
* @OA\Get(
* path="/api/v1/reviews/{id}",
* operationId="Reviews show",
* tags={"Reviews"},
* summary="Reviews show",
* description="Reviews here",
*      @OA\RequestBody(
*         @OA\JsonContent(),
*         @OA\MediaType(
*            mediaType="multipart/form-data",
*        ),
*    ),
*      @OA\Parameter(
 *         name="id",
 *         in="path",
 *         description="User ID",
 *         required=true,
 *         @OA\Schema(type="string")
 *     ),
*      @OA\Response(
 *         response=200,
 *         description="Reviews successfully",
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
 *     security={{"bearerAuth":{}}}
* )
*/
    public function show(string $id)
    {
        $review = Reviews::find($id);

      //  dd($review);
        return response()->json([
            "status" => "true",
            "message" => "success",
            "rating" => $review->rating,
            "comment" => $review->comments,
            "user" => $review->users->name,
            "user_id" => $review->users->id,
            "property" => $review->properties->property_type,
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */

/**
 * @OA\Patch(
 *     path="/api/v1/reviews/{id}",
 *     operationId="UpdateReviews",
 *     tags={"Reviews"},
 *     summary="Update Reviews",
 *     description="Update Reviews details",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         description="Agent ID",
 *         required=true,
 *         @OA\Schema(type="string")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             type="object",
 *             required={"property_id", "rating", "comments"},
 *               @OA\Property(property="property_id", type="integer",example="1"),
 *               @OA\Property(property="rating", type="integer",example="3"),
 *               @OA\Property(property="comments", type="text",example="text"),
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Agent updated successfully",
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
 *     security={{"bearerAuth":{}}}
 * )
 */

    public function update(Request $request, string $id)
    {

        $validatedData = Validator::make($request->all(), [
            'property_id' => 'required',
            'rating' => 'required|integer',
            'comments' => 'required|string',
        ]);
        $reviews=Reviews::find($id);
//        dd($reviews);
        $reviews->comments=$request->comments;
        $reviews->property_id=$request->property_id;
        $reviews->rating=$request->rating;
        $reviews->update();
        return response()->json([
            "status" => "true",
            "message" => "updated",
        ], 200);

    }

    /**
     * Remove the specified resource from storage.
     */
/**
 * @OA\Delete(
 *     path="/api/v1/reviews/{id}",
 *     operationId="DeleteReview",
 *     tags={"Reviews"},
 *     summary="Delete Review",
 *     description="Delete Review details",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         description="Review ID",
 *         required=true,
 *         @OA\Schema(type="string")
 *     ),
 *     @OA\Response(
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
 *     security={{"bearerAuth":{}}}
 * )
 */
    public function destroy(string $id)
    {
        $review=Reviews::find($id);
        $review->delete();
        return response()->json([
            "status" => "true",
            "message" => "deleted",
        ], 200);

    }
}
