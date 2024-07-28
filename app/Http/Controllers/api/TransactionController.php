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
        $data = Transaction::all();
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

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
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

         $searchId = Transaction::where('id',$id)->first();
         if(isset($searchId)) {
             $update = Transaction::where('id',$id)->update($data);
             return response()->json(['message' => 'Update Success'], 200);
         }
         return response()->json(['status' => False ,'message' => 'Try Again'],200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $searchId = Transaction::where('id',$id)->first();
        if(isset($searchId)) {
            $data = Transaction::where('id',$id)->delete();
            return response()->json(['message' => True,], 200);
        }
        return response()->json(['status' => False ,'message' => 'Try Again'],200);
    }
}
