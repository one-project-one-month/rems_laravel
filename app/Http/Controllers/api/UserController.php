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
    /**
     * create new user in storage.
     * post - api/users/register
     */
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
            'token'=>$user->createToken("API TOKEN")->plainTextToken
        ],200);

    }
    /**
     * login user in storage.
     * post - api/users/login
     */
    public function login(Request $request){
        $request->validate([
            'email'=> 'required|email|exists:users,email',
            'password'=>'required'
        ]);
        $user=User::where('email',$request->email)->first();
        if(!$user || !Hash::check( $request->password,$user->password)){
            return response()->json([
                'message'=>"The provided credentials are incorrect"
            ]);
        }
        $token=$user->createToken($user->name);
        return response()->json([
            'message'=>"Login successfully",
            'user'=>$user,
            'token'=>$token->plainTextToken,
        ]);
    }

    /**
     * show all users in storage.
     * get - api/users
     */
    public function index(){
    $users=User::all();
       return response()->json([
        'datas'=>$users],200);
    }

    /**
     * create new user with specifid id  in storage.
     * get - api/users/id
     */
 public function show($id)
 {
     $user=User::find($id);

     if (!$user) {
        abort(404,"use id is not found");
     }
     return response()->json([
         'token'=>$user->createToken("API TOKEN")->plainTextToken,
         'status'=>true,
         'message'=>'found',
         'data'=>$user,
     ],200);
 }

    /**
     * remove user in storage.
     * delete - api/users/id
     */
 public function destroy($id)
   {
     $user = User::findOrFail($id);
     $user->delete();
     return response()->json([
        'message'=>"delete success",
        'data'=>$user,
     ],200);


 }
     /**
     * update user with specified id in storage.
     * put - api/users/id
     */
 public function update(Request $request,  $id){

    $user=User::find($id);
    if($user->fails()){
        return response()->json([
            'message'=>"User is not found",
        ],400);
    }
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
    $user->update($request->all());
        return response()->json([
            'message'=>"User updated successfully",
            'data'=>$user,
        ],200);
    }
    /**
     * logout the user.
     * post - api/users
     */
    public function logout(Request $request){
        $request->user()->tokens()->delete();
        return [
            'message'=>"You are logged out",
        ];
    }

}
