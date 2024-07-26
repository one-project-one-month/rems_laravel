<?php

namespace App\Http\Controllers\api;

use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class transactionController extends Controller
{
    //create
    public function create(Request $request) {
        $validator = $this->validator($request);
        if($validator->fails()){
            return response()->json(['status' => false,'message'=> 'validation error','errors' => $validator->errors()],200);
        }
        $data = $this->Data($request);
        Transaction::create($data);
        return response()->json(['status' => true,'message' => $data],200);

    }

    // view
    public function view($id) {
        $data = Transaction::where('id',$id)->first();
        return response()->json($data, 200);
    }

    // updata
    public function update(Request $request) {

        $transaction = Transaction::where('id',$request->id)->first();



        if (isset($transaction)) {
            $data = $this->Dataupdate($request);
            Transaction::where('id',$request->id)->update($data);
            return response()->json(["Status" => true,"Message" => "Update Success"], 200);
        }
        return response()->json(["Status" => false,"Message" => "Try Again"], 200);
    }

    // delete
    public function delete(Request $request) {
        $data = Transaction::where('id',$request->id)->first();
        if (isset($data)) {
            Transaction::where('id',$request->id)->delete();
            return response()->json(["Status" => true,"Message" => "Delete Success"], 200);
        }
        return response()->json(["Status" => false,"Message" => "Have's Id"], 200);
    }



    // validator
    private function validator($request) {
        $validator = Validator::make($request->all(), [
            'property_id' => 'required',
            'buyer_id' => 'required',
            'agent_id' => 'required',
            'sale_price' => 'required',
            'commission' => 'required',
            'status' => 'required'
        ]);
        return $validator;
    }

    // Data
    private function Data($request) {
        return [
            'property_id' => $request->property_id,
            'buyer_id' => $request->buyer_id,
            'agent_id' => $request->agent_id,
            'transaction_date' => $request->transaction_date,
            'sale_price' => $request->sale_price,
            'commission' => $request->commission,
            'status' => $request->status
        ];
    }



    // Dataupdate
    private function Dataupdate($request) {
        return [
            'id' => $request->id,
            'property_id' => $request->property_id,
            'buyer_id' => $request->buyer_id,
            'agent_id' => $request->agent_id,
            'transaction_date' => $request->transaction_date,
            'sale_price' => $request->sale_price,
            'commission' => $request->commission,
            'status' => $request->status
        ];
    }
}
