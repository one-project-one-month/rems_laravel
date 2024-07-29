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
    public function index()
    {
        $comments="";
        $ratings="";
        $review=Reviews::all();
        foreach ($review as $r){
            $comments.=$r->comments."<br>";
            $ratings.=$r->rating."<br>";
        }
        return response()->json([
            'message' => 'success',
            'status' => true,
            'comments'=>$comments,
            'rating'=>$ratings
        ]);

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)

    {
        $validatedData = Validator::make($request->all(), [
            'property_id' => 'required',
            'rating' => 'required|integer|between 1,10',
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
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {


        $validatedData = Validator::make($request->all(), [
            'property_id' => 'required',
            'rating' => 'required|integer|between 1,10',
            'comments' => 'required|string',
        ]);
        $reviews=Reviews::find($id);
    //    dd($reviews);
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
