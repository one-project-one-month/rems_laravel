<?php
use App\Models\User;
use App\Models\Agent;
use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\UserController;
use App\Http\Controllers\api\AgentController;
use App\Http\Controllers\api\ClientController;
use App\Http\Controllers\api\transactionController;



Route::post("register",[UserController::class,"register"]);
Route::post("login",[UserController::class,"login"]);
Route::post("logout",[UserController::class,"logout"])->middleware('auth:sanctum');
Route::apiResource('users',UserController::class);
 Route::apiResource("clients",ClientController::class);
 Route::apiResource("agents",AgentController::class);


 Route::post('transaction/create',[transactionController::class,'create']);
 Route::get('transaction/view',[transactionController::class,'view']);
 Route::get('transaction/view/{id}',[transactionController::class,'view2']);
 Route::post('transaction/delete',[transactionController::class,'delete']);
 Route::post('transaction/update',[transactionController::class,'update']);







//  http://127.0.0.1:8000/api/transaction/create
//  http://127.0.0.1:8000/api/transaction/view
//  http://127.0.0.1:8000/api/transaction/update
//  http://127.0.0.1:8000/api/transaction/delete
