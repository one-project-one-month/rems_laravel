<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AppointmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $appointments = Appointment::paginate(10);

        return response()->json([
            'count' => $appointments->total(),
            'data' => $appointments->items(),
            'current_page' => $appointments->currentPage(),
            'last_page' => $appointments->lastPage(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            "appointment_date" => "required|date",
            "appointment_time" => "required|date_format:H:i",
            "notes" => "string|max:50"
        ]);

        if($validator->fails()){
            return response()->json([
                'message'=> 'validation error',
                'errors' => $validator->errors()
            ],422);
        };
        
        //get current user
        $user = $request->user();
        

        //get client id with logged in user
        $client_id = $user->client ? $user->client->id : null;

        $inputs = [];
        $inputs['client_id'] = $client_id;
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
    public function show(string $id)
    {
        $data = Appointment::select('appointments.*')->where('id',$id)->first();

        return response()->json([
            'data'=>$data,
        ],200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $current_user = auth('sanctum')->user();
        
        if($current_user->role == 'agent'){
            //if user = agent , can update only status

            $inputs = [];
            $inputs['status'] = $request['status'];

            Appointment::where('id',$id)->update($inputs);

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
                    'errors' => $validator->errors()
                ],422);
            };

            $inputs = [];
            $inputs['appointment_date'] = $request['appointment_date'];
            $inputs['appointment_time'] = $request['appointment_time'];
            $inputs['notes'] = $request['notes'];

            Appointment::where('id',$id)->update($inputs);

            return response()->json([
                'message' => 'Updated the appointment successfully.',
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //only Client can delete the appointment , Agents can only decline using status
        Appointment::where('id',$id)->delete();

        return response()->json([
            'message' => "Appointment deleted successfully",
        ]);
    }
}
