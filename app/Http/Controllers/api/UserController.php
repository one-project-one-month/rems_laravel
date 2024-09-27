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

/**
 * @OA\Info(
 *     title="Laravel REMS",
 *     version="1.0.1"
 * )
 * @OA\SecurityScheme(
 *     type="http",
 *     securityScheme="bearerAuth",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 */

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
* @OA\Post(
* path="/api/v1/registerClient",
* operationId="registerClient",
* tags={"Users"},
* summary="registerClient",
* description="registerClient here",
*     @OA\RequestBody(
*         @OA\JsonContent(),
*         @OA\MediaType(
*            mediaType="multipart/form-data",
*            @OA\Schema(
*               type="object",
*               required={"name","email", "password", "role","first_name","last_name","address","phone"},
*               @OA\Property(property="name", type="string",example="Pyae Phyo Khant"),
*               @OA\Property(property="phone", type="string",example="4384738483"),
*               @OA\Property(property="email", type="string",example="pyaephyo@gmail.com"),
*               @OA\Property(property="first_name", type="string",example="Pyae Phyo"),
*               @OA\Property(property="last_name", type="string",example="Khant"),
*               @OA\Property(property="address", type="string",example="Kume"),
*               @OA\Property(property="role", type="string",example="client"),
*               @OA\Property(property="password", type="string",example="ppk344324"),
*
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
*      @OA\Response(response=404, description="Resource Not Found")
* )
*/

    public function registerClient(Request $request)
    {
        $validatedData = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'first_name'=>'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'address'=>'required|string|max:255',
            'role' => 'required|string|max:255',
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

            $client=Client::create([
                'user_id' => $user->id,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'phone' => $request->phone,
                'email' => $request->email,
                'address' => $request->address,
            ]);

        return response()->json([
            'status' => true,
            'message'=> 'Clients account created successfully',
            'token'=>$user->createToken("API TOKEN")->plainTextToken
        ],200);

    }

/**
* @OA\Post(
* path="/api/v1/registerAgent",
* operationId="registerAgent",
* tags={"Users"},
* summary="registerAgent",
* description="registerAgent here",
*     @OA\RequestBody(
*         @OA\JsonContent(),
*         @OA\MediaType(
*            mediaType="multipart/form-data",
*            @OA\Schema(
*               type="object",
*               required={"name","email", "password", "role","address","phone","agency_name","license_number"},
*               @OA\Property(property="name", type="string",example="Pyae Phyo Khant"),
*               @OA\Property(property="phone", type="string",example="4384738483"),
*               @OA\Property(property="email", type="string",example="pyaephyo@gmail.com"),
*               @OA\Property(property="agency_name", type="string",example="Pyae Phyo"),
*               @OA\Property(property="license_number", type="string",example="3"),
*               @OA\Property(property="address", type="string",example="Kume"),
*               @OA\Property(property="role", type="string",example="Agent"),
*               @OA\Property(property="password", type="string",example="ppk344324"),
*
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

    public function registerAgent(Request $request)
    {
        $validatedData = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'agency_name'=>'required|string|max:255',
            'license_number' => 'required|string|max:255',
            'address'=>'required|string|max:255',
            'role' => 'required|string|max:255',
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

            Agent::create([
                'user_id' => $user->id,
                'agency_name' => $request->input('agency_name'),
                'license_number' => $request->input('license_number'),
                'phone' => $user->phone,
                'email' => $user->email,
                'address' => $request->input('address'),
            ]);


        return response()->json([
            'status' => true,
            'message'=> 'Agents account created successfully',
            'token'=>$user->createToken("API TOKEN")->plainTextToken
        ],200);

    }
    /**
     * login user in storage.
     * post - api/users/login
     */
/**
* @OA\Post(
* path="/api/v1/login",
* operationId="login",
* tags={"Users"},
* summary="login",
* description="login here",
*     @OA\RequestBody(
*         @OA\JsonContent(),
*         @OA\MediaType(
*            mediaType="multipart/form-data",
*            @OA\Schema(
*               type="object",
*               required={"email", "password"},
*               @OA\Property(property="email", type="string",example="pyaephyo@gmail.com"),
*               @OA\Property(property="password", type="string",example="ppk344324"),
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
    /**
 * /**
 * @OA\Get(
 *     path="/api/v1/users/{id}",
 *     summary="Show user",
 *     tags={"Users"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         description="User ID",
 *         required=true,
 *         @OA\Schema(type="string")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="User found",
 *         @OA\JsonContent()
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="User not found",
 *         @OA\JsonContent()
 *     )
 * )
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
/**
 * @OA\POST(
 *     path="/api/v1/logout",
 *     summary="Logout user",
 *     tags={"Users"},
 *     @OA\Response(
 *         response=200,
 *         description="You are logged out",
 *         @OA\JsonContent()
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Unauthorized",
 *         @OA\JsonContent()
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validation error",
 *         @OA\JsonContent()
 *     ),
 *     security={{"bearerAuth":{}}}
 * )
 */


    public function logout(Request $request){
        $request->user()->tokens()->delete();
        return [
            'message'=>"You are logged out",
        ];
    }

}
