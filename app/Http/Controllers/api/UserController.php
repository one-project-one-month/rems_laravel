<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Agent;
use App\Models\Client;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function register(Request $request)
    {
        $validatedData = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'role' => 'required|string|max:255',
            'password' => 'required|string|min:8',
        ]);
        if($validatedData->fails()){
                    return response()->json([
                        'status' => false,
                        'message'=> 'validation error',
                        'errors' => $validatedData->errors()
                    ],422);
                }

         $user = User::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'role' => $request->role,
            'password' => Hash::make($request->password)
         ]);
        if ($user->role == 'agent') {
            Agent::create([
                'user_id' => $user->id,
                'agency_name' => $request->input('agency_name'),
                'license_number' => $request->input('license_number'),
                'phone' => $user->phone,
                'email' => $user->email,
                'address' => $request->input('address'),
            ]);
        } elseif ($user->role == 'client') {
            Client::create([
                'user_id' => $user->id,
                'agent_id' => $request->input('agent_id'),
                'first_name' => $request->input('first_name'),
                'last_name' => $request->input('last_name'),
                'phone' => $user->phone,
                'email' => $user->email,
                'address' => $request->input('address'),
            ]);
        }
        return response()->json([
            'status' => true,
            'message'=> 'success',
        ],201);

    }
    public function index(){
    $users=User::all();
       return response()->json(['datas'=>$users],200);
 }

}
