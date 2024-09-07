<?php

namespace App\Http\Controllers\api;

use Validator;
use App\Models\Reviews;
use App\Models\Property;

use Illuminate\Http\Request;
use App\Models\PropertyImage;
use App\Http\Controllers\Controller;
use Symfony\Component\Console\Input\Input;
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
    public function index(Request $request)

    {
        $query = Property::query();




        if ($request->has('city')) {
            $query->where('city', 'like', '%' . $request->city . '%');
        }

        if ($request->has('property_type')) {
            $query->where('property_type', 'like', '%' . $request->property_type . '%');
        }

        if ($request->has('min_price') || $request->has('max_price')) {
            $minPrice = $request->query('min_price');
            $maxPrice = $request->query('max_price');
            $products = Property::whereBetween('price', [$minPrice, $maxPrice])->get();
            return response()->json($products);
        }

        if ($request->has('number_of_bedrooms')) {
            $query->where('number_of_bedrooms', $request->number_of_bedrooms);
        }

        if ($request->has('number_of_bathrooms')) {
            $query->where('number_of_bathrooms', $request->number_of_bathrooms);
        }

         // Sorting
         if ($request->has('sort_by') && $request->has('sort_order')) {
            $sortBy = $request->input('sort_by');
            $sortOrder = $request->input('sort_order');
            if (in_array($sortBy, ['price', 'agent_id']) && in_array($sortOrder, ['asc', 'desc'])) {
                $query->orderBy($sortBy, $sortOrder);
            }
        }



        $properties = $query->paginate(10);

        return response()->json([
            'message' => 'Properties retrieved successfully',
            'count' => $properties->total(),
            'datas' => $properties->items(),
            'current_page' => $properties->currentPage(),
            'last_page' => $properties->lastPage(),


        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $validator = Validator::make($request->all(),[
            'address' => 'required|string|max:200',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:50',
            'zip_code' => 'required|string|min:5',
            'property_type' => 'required|string|max:50',
            'price' => 'required|integer',
            'size' => 'required|numeric',
            'number_of_bedrooms' => 'required|integer',
            'number_of_bathrooms' => 'required|integer',
            'year_built' => 'required|integer',
            'description' => 'nullable|string',
            'status' => 'required|string|max:50',
            'availiablity_type' => 'required|string|max:50',
            'minrental_period' => 'nullable|integer',
            'approvedby' => 'nullable|string|max:50',
        ]);

        //return error response
        if($validator->fails()){
            return response()->json([
                'message'=> 'validation error',
                'errors' => $validator->errors()
            ],422);
        };

        // Store the property in the database
        $user = $request->user();

        $agent_id = $user->agent ? $user->agent->id : null;

        $inputs = [];
        $inputs['agent_id'] = $agent_id;
        $inputs['address'] = $request['address'];;
        $inputs['city'] = $request['city'];
        $inputs['state'] = $request['state'];
        $inputs['zip_code'] = $request['zip_code'];
        $inputs['property_type'] = $request['property_type'];
        $inputs['price'] = $request['price'];
        $inputs['size'] = $request['size'];
        $inputs['number_of_bedrooms'] = $request['number_of_bedrooms'];
        $inputs['number_of_bathrooms'] = $request['number_of_bathrooms'];
        $inputs['year_built'] = $request['year_built'];
        $inputs['description'] = $request['description'];
        $inputs['status'] = $request['status'];
        $inputs['availiablity_type'] = $request['availiablity_type'];
        $inputs['minrental_period'] = $request['minrental_period'];
        $inputs['approvedby'] = $request['approvedby'];

        $data = Property::insert($inputs);

        // Return a success response
        return response()->json([
            'message' => 'Property created successfully',
            'data' => $inputs
        ], 201);
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
        $validator = Validator::make($request->all(),[
            'address' => 'required|string|max:200',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:50',
            'zip_code' => 'required|string|min:5',
            'property_type' => 'required|string|max:50',
            'price' => 'required|integer',
            'size' => 'required|numeric',
            'number_of_bedrooms' => 'required|integer',
            'number_of_bathrooms' => 'required|integer',
            'year_built' => 'required|integer',
            'status' => 'required|string|max:50',
            'availiablity_type' => 'required|string|max:50',
            'minrental_period' => 'nullable|integer',
            'approvedby' => 'nullable|string|max:50',
            'description' => 'required|string',
        ]);

        // calculate average rating
        $property = Property::find($id);
        if($property && !is_null($property->rating)){
            Property::find($id)->avg('rating');
        };

        //get current user
        $user = $request->user();
        $agent_id = $user->agent ? $user->agent->id : null;

        $inputs = [];
        $inputs['agent_id'] = $agent_id;
        $inputs['address'] = $request['address'];;
        $inputs['city'] = $request['city'];
        $inputs['state'] = $request['state'];
        $inputs['zip_code'] = $request['zip_code'];
        $inputs['property_type'] = $request['property_type'];
        $inputs['price'] = $request['price'];
        $inputs['size'] = $request['size'];
        $inputs['number_of_bedrooms'] = $request['number_of_bedrooms'];
        $inputs['number_of_bathrooms'] = $request['number_of_bathrooms'];
        $inputs['year_built'] = $request['year_built'];
        $inputs['description'] = $request['description'];
        $inputs['status'] = $request['status'];
        $inputs['availiablity_type'] = $request['availiablity_type'];
        $inputs['minrental_period'] = $request['minrental_period'];
        $inputs['approvedby'] = $request['approvedby'];

        // update property
        Property::where('id',$id)->update($inputs);

        if($validator->fails()){
            return response()->json([
                'message'=> 'validation error',
                'errors' => $validator->errors()
            ],422);

        };

        return response()->json([
            'message' => "Updated the property successfully"
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
