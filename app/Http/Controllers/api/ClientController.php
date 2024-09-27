<?php


namespace App\Http\Controllers\api;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreClientRequest;
use App\Http\Requests\UpdateClientRequest;
use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\User;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware as ControllersMiddleware;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Support\Facades\Validator;

class ClientController extends Controller implements HasMiddleware
{
    public static function middleware()
    {
        return [
            new ControllersMiddleware('auth:sanctum',except: ['show','index'])
        ];
    }
    /**
     * Display a listing of the resource.
     * get - api/clients
     */
/**
* @OA\Get(
* path="/api/v1/clients",
* operationId="Client",
* tags={"Clients"},
* summary="Clients",
* description="Clients here",
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
    public function index()
    {
        $client=Client::all();
        return response()->json(['datas'=>$client]);
    }
    /**
     * Display the specified resource.
     * get - api/clients/id
     */
/**
* @OA\Get(
* path="/api/v1/clients/{id}",
* operationId="clients",
* tags={"Clients"},
* summary="clients",
* description="clients here",
*     @OA\RequestBody(
*         @OA\JsonContent(),
*         @OA\MediaType(
*            mediaType="multipart/form-data",
*        ),
*    ),
*      @OA\Parameter(
 *         name="id",
 *         in="path",
 *         description="User ID",
 *         required=true,
 *         @OA\Schema(type="string")
 *     ),
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
    public function show($id)
    {
        try{
        $client=Client::find($id);
        if (!$client) {
            throw new NotFoundHttpException('client is not found');
        }
        return response()->json([
            'token'=>$client->createToken("API TOKEN")->plainTextToken,
            'status'=>true,
            'message'=>'found',
            'data'=>$client,
        ],200);
    } catch (NotFoundHttpException $e) {
        return response()->json(['error' => $e->getMessage()], 404);
    }
    }

    /**
     * Update the specified resource in storage.
     * put - api/clients/id
     */
/**
 * @OA\Patch(
 *     path="/api/v1/clients/{id}",
 *     operationId="UpdateClient",
 *     tags={"Clients"},
 *     summary="Update Client",
 *     description="Update Client details",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         description="Client ID",
 *         required=true,
 *         @OA\Schema(type="string")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             type="object",
 *             required={"email", "phone", "first_name", "last_name","address"},
 *             @OA\Property(property="phone", type="string", example="4384738483"),
 *             @OA\Property(property="email", type="string", example="pyaephyo@gmail.com"),
 *             @OA\Property(property="first_name", type="string", example="Pyae Phyo"),
 *             @OA\Property(property="last_name", type="string", example="Khant"),
 *             @OA\Property(property="address", type="string", example="Mandalay"),
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


    public function update(Request $request,  $id)
    {   try{
        $client=Client::find($id);
        if (!$client) {
            throw new NotFoundHttpException('client is not found');
        }
        $validatedData = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
           'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'address' => 'required|string|max:255',
           'phone' => 'required|string',
        ]);

        $client->update($request->all());
        return response()->json([
            'message'=>"Client updated successfully",
            'data'=>$client,
        ],200);
    }catch (NotFoundHttpException $e) {
        return response()->json(['error' => $e->getMessage()], 404);
    }

    }

    /**
     * Remove the specified resource from storage.
     * delete - api/clients/id
     */

/**
 * @OA\Delete(
 *     path="/api/v1/clients/{id}",
 *     operationId="DeleteClient",
 *     tags={"Clients"},
 *     summary="Delete Client",
 *     description="Delete Client details",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         description="Client ID",
 *         required=true,
 *         @OA\Schema(type="string")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Client deleted successfully",
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

    public function destroy($id)
    {   try{
        $client=Client::find($id);
        if (!$client) {
            throw new NotFoundHttpException('client is not found');
        }
        $user=User::findOrFail($client->user_id);
        $client->delete();
        $user->delete();
        return response()->json([
            'message'=>'delete success',
            'data'=>$client,
        ],200);

    } catch (NotFoundHttpException $e){
    return response()->json(['error' => $e->getMessage()], 404);
    }
}
}
