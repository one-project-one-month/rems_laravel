<?php

namespace App\Http\Controllers\api;

use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class TransactionController extends Controller
{
     public static function middleware()
    {
        return [
            new ControllersMiddleware('auth:sanctum',except: ['show','index'])
        ];
    }
    /**
     * Display a listing of the resource.
     */
/**
     * @OA\Get(
     *     path="/api/v1/transaction",
     *     summary="Get list of Transactions",
     *     tags={"Transactions"},
     *     @OA\Parameter(
     *         name="client_id",
     *         in="query",
     *         description="Filter properties by client_id",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="property_id",
     *         in="query",
     *         description="Filter properties by property_id",
     *         required=false,
     *         @OA\Schema(type="string")
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
    // Transaction query
    $query = Transaction::query();

    // Apply filters for transaction
    if ($request->has('client_id')) {
        $query->where('client_id', 'like', '%' . $request->client_id . '%');
    }

    if ($request->has('property_id')) {
        $query->where('property_id', 'like', '%' . $request->property_id . '%');
    }

    // Main transaction data
    $transaction = $query->first();

    // Paginate properties
    $properties = $query->paginate(10);

    // Query for property join
    $propertyQuery = Transaction::query();

    if ($request->has('client_id')) {
        $propertyQuery->where('client_id', 'like', '%' . $request->client_id . '%');
    }

    if ($request->has('property_id')) {
        $propertyQuery->where('property_id', 'like', '%' . $request->property_id . '%');
    }

    $property = $propertyQuery->select('properties.*')
        ->leftJoin('properties', 'transactions.property_id', 'properties.id')
        ->first();

    // Query for client join
    $clientQuery = Transaction::query();

    if ($request->has('client_id')) {
        $clientQuery->where('client_id', 'like', '%' . $request->client_id . '%');
    }

    if ($request->has('property_id')) {
        $clientQuery->where('property_id', 'like', '%' . $request->property_id . '%');
    }

    $client = $clientQuery->select('clients.*')
        ->leftJoin('clients', 'transactions.client_id', 'clients.id')
        ->first();

    // Return response
    return response()->json([
        'current_page' => $properties->currentPage(),
        'last_page' => $properties->lastPage(),
        'property' => $property,
        'client' => $client,
        'transaction' => $transaction,
        'message' => true
    ]);
}


    /**
     * Store a newly created resource in storage.
     */
/**
* @OA\Post(
* path="/api/v1/transaction",
* operationId="transaction",
* tags={"Transactions"},
* summary="transaction",
* description="transaction here",
*     @OA\RequestBody(
*         @OA\JsonContent(),
*         @OA\MediaType(
*            mediaType="multipart/form-data",
*            @OA\Schema(
*               type="object",
*               required={"property_id","client_id","rental_period","transaction_date","sale_price","commission","status"},
*               @OA\Property(property="property_id", type="integer",example="1"),
*               @OA\Property(property="client_id", type="integer",example="1"),
*               @OA\Property(property="rental_period", type="integer",example="6"),
*               @OA\Property(property="transaction_date", type="date",example="2024.9.9"),
*               @OA\Property(property="sale_price", type="integer",example="150000"),
*               @OA\Property(property="commission", type="integer",example="10000"),
*               @OA\Property(property="status", type="string",example="success")
*            ),
*        ),
*    ),
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
    public function store(Request $request)
    {
        $validatedData = Validator::make($request->all(),[
            'property_id' => 'required|exists:properties,id',
            'client_id' => 'required|exists:clients,id',
            'rental_period' => 'required',
            'transaction_date' => 'required',
            'sale_price' => 'required',
            'commission' => 'required',
            'status' => 'required'
         ]);

         if ($validatedData->fails()) {
            return response()->json(['status' => false,'message' => 'validation error','errors' => $validatedData->errors()], 200);
        }

         $create = Transaction::create([
            'property_id' => $request->property_id,
            'client_id' => $request->client_id,
            'rental_period' => $request->rental_period,
            'transaction_date' => $request->transaction_date,
            'sale_price' => $request->sale_price,
            'commission' => $request->commission,
            'status' => $request->status
         ]);
        return response()->json(['status' => true,'message'=> $create],200);
    }

    /**
     * Display the specified resource.
     */
/**
 * @OA\Get(
* path="/api/v1/transaction/{id}",
* operationId="transaction show",
* tags={"Transactions"},
* summary="Transactions",
* description="Transactions here",
*      @OA\Parameter(
 *         name="id",
 *         in="path",
 *         description="User ID",
 *         required=true,
 *         @OA\Schema(type="string")
 *     ),
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
    public function show(string $id)
    {
        $searchId = Transaction::where('id',$id)->first();
        if(isset($searchId)) {
            $data = Transaction::where('id',$id)->first();
            return response()->json($data, 200);
        }
        return response()->json(['status' => False ,'message' => 'Try Again'],200);
    }

}
