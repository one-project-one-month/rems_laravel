<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class appointmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $appointments = Appointment::select('appointments.*')->get();

        return response()->json([
            'message' => 'Properties retrieved successfully',
            'count' => count($appointments),
            'data' => $appointments]);
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
        $validator = Validator::make($request->all(),[
            "appointment_date" => "required|string",
            "appointment_time" => "required|string",
            "notes" => "string|max:50"
        ]);

        if($validator->fails()){
            return response()->json([
                'message'=> 'validation error',
                'errors' => $validatedData->errors()
            ],422);
        };
        
        $current_user = auth('sanctum')->user();

        $inputs = [];
        $inputs['agent_id'] = $request['agent_id'];
        $inputs['client_id'] = $current_user;
        $inputs['property_id'] = $request['property_id'];;
        $inputs['appointment_date'] = $request['appointment_date'];
        $inputs['appointment_time'] = $request['appointment_time'];
        $inputs['status'] = "PENDING";
        $inputs['notes'] = $request['notes'];

        Appointment::insert($inputs);

        return response()->json([
            'message' => 'Appointment created successfully.',
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Appointment $appointment)
    {
        $data = Appointment::select('appointments.*')->where('id',$appointment->id)->first();

        return response()->json([
            'data'=>$data,
        ],200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Appointment $appointment)
    {

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Appointment $appointment)
    {
        //
        $current_user = auth('sanctum')->user();
        
        if($current_user->role == 'agent'){
            //if user = agent , can update only status

            $inputs = [];
            $inputs['status'] = $request['status'];

            Appointment::where('id',$appointment->id)->update($inputs);

            return response()->json([
                'message' => "Updated the appointment successfully"
            ]);
        }else if($current_user->role == 'client'){
            //if user = client , can update date\time\notes

            $validator = Validator::make($request->all(),[
                "appointment_date" => "required|string",
                "appointment_time" => "required|string",
                "notes" => "string|max:50"
            ]);
    
            if($validator->fails()){
                return response()->json([
                    'message'=> 'validation error',
                    'errors' => $validatedData->errors()
                ],422);
            };

            $inputs = [];
            $inputs['appointment_date'] = $request['appointment_date'];
            $inputs['appointment_time'] = $request['appointment_time'];
            $inputs['notes'] = $request['notes'];

            Appointment::where('id',$appointment->id)->update($inputs);

            return response()->json([
                'message' => 'Updated the appointment successfully.',
            ]);
        }

        
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Appointment $appointment)
    {
        //only Client can delete the appointment , Agents can only decline using status
        Appointment::where('id',$appointment->id)->delete();

        return response()->json([
            'message' => "Appointment deleted successfully",
        ]);
    }
}
