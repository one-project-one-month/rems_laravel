<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Listing;
use Illuminate\Http\Request;
    

class ListingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Listing::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'property_id' => 'required|integer|exists:properties,property_id',
            'agent_id' => 'required|integer|exists:agents,agent_id',
            'listing_price' => 'required|numeric',
            'status' => 'required|string|max:50',
            'description' => 'nullable|string',
        ]);

        $listing = Listing::create($validatedData);

        return response()->json($listing, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        
        return Listing::findOrFail($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $listing = Listing::findOrFail($id);

        $validatedData = $request->validate([
            'property_id' => 'required|integer|exists:properties,property_id',
            'agent_id' => 'required|integer|exists:agents,agent_id',
            'listing_price' => 'required|numeric',
            'status' => 'required|string|max:50',
            'description' => 'nullable|string',
        ]);

        $listing->update($validatedData);

        return response()->json($listing);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $listing = Listing::findOrFail($id);
        $listing->delete();

        return response()->json(null, 204);
    }
}
