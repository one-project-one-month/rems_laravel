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
    public function index()
    {
        $data = Transaction::paginate(5);
        return ['data' => $data];
    }

    /**
     * Store a newly created resource in storage.
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
