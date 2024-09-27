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
/**
     * @OA\Get(
     *     path="/api/v1/properties",
     *     summary="Get list of properties",
     *     tags={"Properties"},
     *     @OA\Parameter(
     *         name="city",
     *         in="query",
     *         description="Filter properties by city",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="property_type",
     *         in="query",
     *         description="Filter properties by property type",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="min_price",
     *         in="query",
     *         description="Filter properties by minimum price",
     *         required=false,
     *         @OA\Schema(type="number")
     *     ),
     *     @OA\Parameter(
     *         name="max_price",
     *         in="query",
     *         description="Filter properties by maximum price",
     *         required=false,
     *         @OA\Schema(type="number")
     *     ),
     *     @OA\Parameter(
     *         name="number_of_bedrooms",
     *         in="query",
     *         description="Filter properties by number of bedrooms",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="number_of_bathrooms",
     *         in="query",
     *         description="Filter properties by number of bathrooms",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="sort_by",
     *         in="query",
     *         description="Sort properties by field (price, agent_id)",
     *         required=false,
     *         @OA\Schema(type="string", enum={"price", "agent_id"})
     *     ),
     *     @OA\Parameter(
     *         name="sort_order",
     *         in="query",
     *         description="Order direction (asc, desc)",
     *         required=false,
     *         @OA\Schema(type="string", enum={"asc", "desc"})
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Properties retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Properties retrieved successfully"),
     *             @OA\Property(property="count", type="integer", example=100),
     *             @OA\Property(property="datas", type="array", @OA\Items(type="object")),
     *             @OA\Property(property="current_page", type="integer", example=1),
     *             @OA\Property(property="last_page", type="integer", example=10)
     *         )
     *     )
     * )
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
/**
* @OA\Post(
* path="/api/v1/properties",
* operationId="properties",
* tags={"Properties"},
* summary="properties create",
* description="properties create here",
*     @OA\RequestBody(
*         @OA\JsonContent(),
*         @OA\MediaType(
*            mediaType="multipart/form-data",
*            @OA\Schema(
*               type="object",
*               required={"agent_id","address","city", "state", "zip_code","property_type","price","size","number_of_bedrooms",
*               "number_of_bathrooms","year_built","description","status","availiablity_type","minrental_period","approvedby"},
*               @OA\Property(property="agent_id", type="integer",example="1"),
*               @OA\Property(property="address", type="string",example="Kume"),
*               @OA\Property(property="city", type="string",example="Kume"),
*               @OA\Property(property="state", type="string",example="success"),
*               @OA\Property(property="zip_code", type="string",example="pp3434"),
*               @OA\Property(property="property_type", type="string",example="Khant"),
*               @OA\Property(property="price", type="integer",example="5000"),
*               @OA\Property(property="size", type="string",example="2"),
*               @OA\Property(property="number_of_bedrooms", type="integer",example="5"),
*               @OA\Property(property="number_of_bathrooms", type="integer",example="5"),
*               @OA\Property(property="year_built", type="integer",example="5"),
*               @OA\Property(property="description", type="string",example="description"),
*               @OA\Property(property="status", type="string",example="success"),
*               @OA\Property(property="availiablity_type", type="string",example="ppk344324"),
*               @OA\Property(property="minrental_period", type="integer",example="3"),
*               @OA\Property(property="approvedby", type="string",example="ppk344324"),
*
*            ),
*        ),
*    ),
*      @OA\Response(
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
/**
* @OA\Get(
* path="/api/v1/properties/{id}",
* operationId="properties show",
* tags={"Properties"},
* summary="properties show",
* description="properties show here",
*     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         description="User ID",
 *         required=true,
 *         @OA\Schema(type="string")
 *     ),
*      @OA\Response(
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
/**
 * @OA\Patch(
 *     path="/api/v1/properties/{id}",
 *     summary="Update a property",
 *     operationId="updateProperty",
 *     tags={"Properties"},
 *     description="Updates the details of a property based on the provided ID.",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         description="Property ID",
 *         required=true,
 *         @OA\Schema(type="string")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="application/json",
 *             @OA\Schema(
 *                 required={
 *                     "address", "city", "state", "zip_code", "property_type", "price",
 *                     "size", "number_of_bedrooms", "number_of_bathrooms",
 *                     "year_built", "description", "status", "availability_type"
 *                 },
 *                 @OA\Property(property="address", type="string", example="123 Main St"),
 *                 @OA\Property(property="city", type="string", example="Los Angeles"),
 *                 @OA\Property(property="state", type="string", example="CA"),
 *                 @OA\Property(property="zip_code", type="string", example="90001"),
 *                 @OA\Property(property="property_type", type="string", example="House"),
 *                 @OA\Property(property="price", type="integer", example=500000),
 *                 @OA\Property(property="size", type="integer", example=2500),
 *                 @OA\Property(property="number_of_bedrooms", type="integer", example=4),
 *                 @OA\Property(property="number_of_bathrooms", type="integer", example=3),
 *                 @OA\Property(property="year_built", type="integer", example=1990),
 *                 @OA\Property(property="description", type="string", example="Beautiful home with a big backyard."),
 *                 @OA\Property(property="status", type="string", example="available"),
 *                 @OA\Property(property="availability_type", type="string", example="rental"),
 *                 @OA\Property(property="minrental_period", type="integer", example=6),
 *                 @OA\Property(property="approvedby", type="string", example="Admin")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Property updated successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Updated the property successfully"),
 *             @OA\Property(property="data", type="object",
 *                 @OA\Property(property="agent_id", type="integer", example=1),
 *                 @OA\Property(property="address", type="string", example="123 Main St"),
 *                 @OA\Property(property="city", type="string", example="Los Angeles"),
 *                 @OA\Property(property="state", type="string", example="CA"),
 *                 @OA\Property(property="zip_code", type="string", example="90001"),
 *                 @OA\Property(property="property_type", type="string", example="House"),
 *                 @OA\Property(property="price", type="integer", example=500000),
 *                 @OA\Property(property="size", type="integer", example=2500),
 *                 @OA\Property(property="number_of_bedrooms", type="integer", example=4),
 *                 @OA\Property(property="number_of_bathrooms", type="integer", example=3),
 *                 @OA\Property(property="year_built", type="integer", example=1990),
 *                 @OA\Property(property="description", type="string", example="Beautiful home with a big backyard."),
 *                 @OA\Property(property="status", type="string", example="available"),
 *                 @OA\Property(property="availability_type", type="string", example="rental"),
 *                 @OA\Property(property="minrental_period", type="integer", example=6),
 *                 @OA\Property(property="approvedby", type="string", example="Admin")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validation error",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="validation error"),
 *             @OA\Property(property="errors", type="object")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Property not found",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Property not found")
 *         )
 *     ),
 *     security={{"bearerAuth":{}}}
 * )
 */
public function update(Request $request, string $id)
{
    $validator = Validator::make($request->all(), [
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
        'availability_type' => 'required|string|max:50',
        'minrental_period' => 'nullable|integer',
        'approvedby' => 'nullable|string|max:50',
        'description' => 'required|string',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'message' => 'validation error',
            'errors' => $validator->errors()
        ], 422);
    }

    // Find and update the property
    $property = Property::find($id);
    if (!$property) {
        return response()->json(['message' => 'Property not found'], 404);
    }

    $user = $request->user();
    $agent_id = $user->agent ? $user->agent->id : null;

    $update = [
        'agent_id' => $agent_id,
        'address' => $request->address,
        'city' => $request->city,
        'state' => $request->state,
        'zip_code' => $request->zip_code,
        'property_type' => $request->property_type,
        'price' => $request->price,
        'size' => $request->size,
        'number_of_bedrooms' => $request->number_of_bedrooms,
        'number_of_bathrooms' => $request->number_of_bathrooms,
        'year_built' => $request->year_built,
        'status' => $request->status,
        'availability_type' => $request->availability_type,
        'minrental_period' => $request->minrental_period,
        'approvedby' => $request->approvedby,
        'description' => $request->description,
    ];

    $property->update($update);

    return response()->json([
        'message' => "Updated the property successfully",
        'data' => $update
    ], 200);
}

    /**
     * Remove the specified resource from storage.
     */
   /**
 * @OA\Delete(
 *     path="/api/v1/properties/{id}",
 *     summary="Delete a property",
 *     operationId="DeleteeProperty",
 *     tags={"Properties"},
 *     description="Delete the details of a property based on the provided ID.",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         description="Property ID",
 *         required=true,
 *         @OA\Schema(type="string")
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validation error",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="validation error"),
 *             @OA\Property(property="errors", type="object")
 *         )
 *     ),
 *       @OA\Response(
 *         response=200,
 *         description="Agent deleted successfully",
 *         @OA\JsonContent()
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Property not found",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Property not found")
 *         )
 *     ),
 *     security={{"bearerAuth":{}}}
 * )
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
