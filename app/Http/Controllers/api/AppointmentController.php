<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AppointmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */

 /**
* @OA\Get(
* path="/api/v1/appointments",
* operationId="appointments",
* tags={"Appointments"},
* summary="appointments",
* description="appointments here",
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
/**
* @OA\Post(
* path="/api/v1/appointments",
* operationId="appointmentsCreate",
* tags={"Appointments"},
* summary="appointments Create",
* description="appointments here",
*     @OA\RequestBody(
*         @OA\JsonContent(),
*         @OA\MediaType(
*            mediaType="multipart/form-data",
*            @OA\Schema(
*               type="object",
*               required={"property_id","client_id", "appointment_date", "appointment_time","status","notes"},
*               @OA\Property(property="property_id", type="integer",example="1"),
*               @OA\Property(property="client_id", type="integer",example="1"),
*               @OA\Property(property="appointment_date", type="Date",example="2024-09-10"),
*               @OA\Property(property="appointment_time", type="time",example="12:00"),
*               @OA\Property(property="status", type="string",example="success"),
*               @OA\Property(property="notes", type="string",example="notes")
*            ),
*        ),
*    ),
*       @OA\Response(
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
            "property_id" => "required",
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


        $user = $request->user();
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
  /**
* @OA\Get(
* path="/api/v1/appointments/{id}",
* operationId="appointments show",
* tags={"Appointments"},
* summary="appointments",
* description="appointments here",
*     @OA\RequestBody(
*         @OA\JsonContent(),
*         @OA\MediaType(
*            mediaType="multipart/form-data",
*        ),
*    ),
*      @OA\Parameter(
 *         name="id",
 *         in="path",
 *         description="Appointment ID",
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
        $data = Appointment::select('appointments.*')->where('id',$id)->first();

        return response()->json([
            'data'=>$data,
        ],200);
    }

    /**
     * Update the specified resource in storage.
     */
/**
 * @OA\Patch(
 *     path="/api/v1/appointments/{id}",
 *     operationId="UpdateAppointments",
 *     tags={"Appointments"},
 *     summary="Update Appointments",
 *     description="Update Appointment details",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         description="Agent ID",
 *         required=true,
 *         @OA\Schema(type="string")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             type="object",
 *             required={"appointment_date","appointment_time","notes"},
 *             @OA\Property(property="appointment_date", type="Date",example="2024-09-10"),
 *             @OA\Property(property="appointment_time", type="time",example="12:00"),
 *             @OA\Property(property="notes", type="string",example="notes")
 *         )
 *     ),
 *     @OA\Response(
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
/**
 * @OA\Delete(
 *     path="/api/v1/appointments/{id}",
 *     operationId="DeleteAppointments",
 *     tags={"Appointments"},
 *     summary="Delete Appointments",
 *     description="Delete Appointments details",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         description="Appointments ID",
 *         required=true,
 *         @OA\Schema(type="string")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Agent deleted successfully",
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
    public function destroy(string $id)
    {
        //only Client can delete the appointment , Agents can only decline using status
        Appointment::where('id',$id)->delete();

        return response()->json([
            'message' => "Appointment deleted successfully",
        ]);
    }
}
