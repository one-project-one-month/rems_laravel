<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware as ControllersMiddleware;

class PropertyController extends Controller implements HasMiddleware
{
    public static function middleware()
    {
        return [
            new ControllersMiddleware('auth:sanctum', except: ['show', 'index'])
        ];
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $properties = Property::all();
        return response()->json([
            'message' => 'Properties retrieved successfully',
            'count' => count($properties),
            'datas' => $properties
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'agent_id' => 'required|integer|exists:agents,agent_id',
            'address' => 'required|string|min:200',
            'city' => 'required|string|min:100',
            'state' => 'required|string|min:50',
            'zip_code' => 'required|string|min:10',
            'property_type' => 'required|string|min:50',
            'price' => 'required|integer',
            'size' => 'required|numeric',
            'number_of_bedrooms' => 'required|integer',
            'number_of_bathrooms' => 'required|integer',
            'year_built' => 'required|integer',
            'description' => 'nullable|string',
            'status' => 'required|string|mzx:50',
            'availiablity_type' => 'required|string|max:50',
            'minrental_period' => 'nullable|integer',
            'approvedby' => 'nullable|string|max:50',
            'description' => 'required|string',
             'adddate' => 'nullable|date',
            'editdate' => 'nullable|date',
            
        ]);
        // Store the property in the database
        $data = Property::create($data);
        // Return a success response
        if ($data) {
            return response()->json([
                'message' => 'Property created successfully',
                'data' => $data
            ], 201);
        }
        // Return an error response
        return response()->json([
            'message' => 'Failed to create property'
        ], 500);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $property = Property::findOrFail($id);
        if ($property) {
            return response()->json([
                'message' => 'Property found',
                'data' => $property
            ], 200);
        }
        // Return the property data
        return response()->json([
            'message' => 'Property not found'
        ], 404);

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $data = $request->validate([
            'agent_id' => 'required|integer|exists:agents,agent_id',
            'address' => 'required|string|min:200',
            'city' => 'required|string|min:100',
            'state' => 'required|string|min:50',
            'zip_code' => 'required|string|min:10',
            'property_type' => 'required|string|min:50',
            'price' => 'required|integer',
            'size' => 'required|numeric',
            'number_of_bedrooms' => 'required|integer',
            'number_of_bathrooms' => 'required|integer',
            'year_built' => 'required|integer',
            'description' => 'nullable|string',
            'status' => 'required|string|mzx:50',
            'availiablity_type' => 'required|string|max:50',
            'minrental_period' => 'nullable|integer',
            'approvedby' => 'nullable|string|max:50',
            'description' => 'required|string',
             'adddate' => 'nullable|date',
            'editdate' => 'nullable|date',
        ]);

        $property = Property::findOrFail($id);
        $data = $property->update($data);
        if ($data) {
            return response()->json([
                'message' => 'Property updated successfully',
                'data' => $property
            ], 200);
        }
        return response()->json([
            'message' => 'Property can not be updated successfully',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $property = Property::findOrFail($id);
        $delete = $property->delete();
        if ($delete) {
            return response()->json([
                'message' => 'Property deleted successfully',
            ], 200);
        }
        return response()->json([
            'message' => 'Property can not be deleted successfully',
        ], 500);
    }
}