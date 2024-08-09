<?php

namespace App\Http\Controllers\api;

use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

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
        $data = $request->validate([
            'property_id' => 'required',
            'client_id' => 'required',
            'rental_period' => 'required',
            'transaction_date' => 'required',
            'sale_price' => 'required',
            'commission' => 'required',
            'status' => 'required'
         ]);
         $create = Transaction::create($data);
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
