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
    public function index()
    {
        $client=Client::all();
        return response()->json(['datas'=>$client]);
    }
    /**
     * Display the specified resource.
     * get - api/clients/id
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
