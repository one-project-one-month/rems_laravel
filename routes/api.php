<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Controller;

use App\Http\Controllers\api\UserController;
use App\Http\Controllers\api\ClientController;
use App\Http\Controllers\api\AgentController;
use App\Http\Controllers\api\appointmentController;
use App\Models\Agent;
use App\Models\User;



Route::post("register",[UserController::class,"register"]);
Route::post("login",[UserController::class,"login"]);
Route::post("logout",[UserController::class,"logout"])->middleware('auth:sanctum');
Route::apiResource('users',UserController::class);
 Route::apiResource("clients",ClientController::class);
 Route::apiResource("agents",AgentController::class);

Route::apiResource("appointments",appointmentController::class);

