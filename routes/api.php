<?php

use App\Models\User;
use App\Models\Agent;
use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\UserController;
use App\Http\Controllers\api\AgentController;
use App\Http\Controllers\api\ClientController;
use App\Http\Controllers\api\ReviewsController;
use App\Http\Controllers\api\PropertyController;
use App\Http\Controllers\api\AppointmentController;
use App\Http\Controllers\api\TransactionController;
use App\Http\Controllers\api\PropertyImageController;

Route::prefix('v1')->group(function () {
Route::post("register", [UserController::class, "register"]);
Route::post("registerAgent", [UserController::class, "registerAgent"]);
Route::post("registerClient", [UserController::class, "registerClient"]);
Route::post("login", [UserController::class, "login"]);
Route::post("logout", [UserController::class, "logout"])->middleware('auth:sanctum');
Route::apiResource('users', UserController::class);
Route::apiResource("clients", ClientController::class);
Route::apiResource("agents", AgentController::class);
Route::apiResource("properties", PropertyController::class)->middleware('auth:sanctum');
Route::get("search", [AgentController::class, "search"]);
Route::apiResource("appointments",AppointmentController::class)->middleware('auth:sanctum');
Route::apiResource("reviews", ReviewsController::class)->middleware('auth:sanctum');
Route::apiResource("transaction",TransactionController::class);
Route::get('/property-images/show', [PropertyImageController::class, 'index']);
Route::post('/property-images', [PropertyImageController::class, 'store']);
Route::post('/property-images/update', [PropertyImageController::class, 'update']);
Route::delete('/property-images/{id}', [PropertyImageController::class, 'delete']);
});
