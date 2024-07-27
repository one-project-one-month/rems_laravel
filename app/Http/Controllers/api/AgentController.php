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
            new ControllersMiddleware('auth:sanctum',except: ['show','index'])
        ];
    }

    /**
     * Display a listing of the resource.
     * get - api/agents
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


}
