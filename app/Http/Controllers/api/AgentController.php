<?php

namespace App\Http\Controllers\api;
use App\Http\Controllers\Controller;
use App\Http\Controllers\middleware;
use App\Http\Requests\StoreAgentRequest;
use App\Http\Requests\UpdateAgentRequest;
use Illuminate\Http\Request;
use App\Models\Agent;
use App\Models\User;
use App\Models\Client;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware as ControllersMiddleware;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use Illuminate\Support\Facades\Validator;

class AgentController extends Controller  implements HasMiddleware
{
    public static function middleware()
    {
        return [
            new ControllersMiddleware('auth:sanctum',except: ['show','index','search'])
        ];
    }

    /**
     * Display a listing of the resource.
     * get - api/agents
     */
    /**
* @OA\Get(
* path="/api/v1/agents",
* operationId="Agent",
* tags={"Agents"},
* summary="Agents",
* description="Agents here",
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
        $agent=Agent::all();
        return response()->json(['datas'=>$agent]);
    }

    /**
     * Display the specified resource.
     * get - api/agents/id
     */
    /**
* @OA\Get(
* path="/api/v1/agents/{id}",
* operationId="agents",
* tags={"Agents"},
* summary="agents",
* description="agents here",
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
        $agent=Agent::find($id);
        if (!$agent) {
            throw new NotFoundHttpException('agent is not found');
        }

        return response()->json([
            'token'=>$agent->createToken("API TOKEN")->plainTextToken,
            'status'=>true,
            'message'=>'found',
            'data'=>$agent,
        ],200);
    }
    catch (NotFoundHttpException $e) {
        return response()->json(['error' => $e->getMessage()], 404);
    }
}
    /**
     * Update the specified resource in storage.
     * put - api/agents/id
     */
/**
 * @OA\Patch(
 *     path="/api/v1/agents/{id}",
 *     operationId="UpdateAgent",
 *     tags={"Agents"},
 *     summary="Update Agent",
 *     description="Update Agent details",
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
 *             required={"email", "phone", "agency_name", "license_number"},
 *             @OA\Property(property="phone", type="string", example="4384738483"),
 *             @OA\Property(property="email", type="string", example="pyaephyo@gmail.com"),
 *             @OA\Property(property="agency_name", type="string", example="Pyae Phyo"),
 *             @OA\Property(property="license_number", type="string", example="3")
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


    public function update(Request $request,$id)
    {
        try{
        $agent =Agent::findOrFail($id);
        if(!$agent){
            throw new NotFoundHttpException('agent is not found');
        }

        $request->validate(
            [
                'agency_name'=>'string',
                'license_number'=>'string',
                'phone'=>'string',
                'email' =>'string',
            ]);
            $agent->update($request->all());
            return response()->json([
                'message'=>"agent updated successfully",
                'data'=>$agent,
            ],200);
        }catch (NotFoundHttpException $e){
            return response()->json(['error' => $e->getMessage()], 404);
        }
    }


    /**
     * Remove the specified resource from storage.
     * delete - api/agents/id
     */

/**
 * @OA\Delete(
 *     path="/api/v1/agents/{id}",
 *     operationId="DeleteAgent",
 *     tags={"Agents"},
 *     summary="Delete Agent",
 *     description="Delete Agent details",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         description="Agent ID",
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

    public function destroy($id)
    {
        try{
        $agent=Agent::find($id);
        if(!$agent){
            throw new NotFoundHttpException('agent is not found');
        }
        $user=User::findOrFail($agent->user_id);
        $agent->delete();
        $user->delete();
        return response()->json([
            'message'=>'delete success',
            'data'=>$agent,
        ],200);
    }catch (NotFoundHttpException $e){
        return response()->json(['error' => $e->getMessage()], 404);
    }
    }

    /**
 * @OA\Get(
 *     path="/api/v1/search",
 *     operationId="search",
 *     tags={"Agents"},
 *     summary="search",
 *     description="searchs",
 *     @OA\Parameter(
 *         name="agency_name",
 *         in="query",
 *         required=true,
 *         description="The agency name to search for",
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
 * )
 */
    public function search(Request $request)
    {
        $query = $request->input('agency_name');
        $perPage = $request->input('per_page', 5); // Default to 15 results per page if not specified
        $page = $request->input('page', 1); // Default to page 1 if not specified
        if (empty($query)) {
            return response()->json(['error' => 'Name query parameter is required'], 400);
        }
        $people = Agent::where('agency_name', 'like', '%' . $query . '%')
            ->paginate($perPage, ['*'], 'page', $page);
        return response()->json($people);
    }


}
