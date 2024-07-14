<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
//use App\Http\Controllers\api\UserController;
use App\Http\Controllers\api\UserController;


// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');
Route::post("register",[UserController::class,"register"]);
Route::get("index",[UserController::class,"index"]);
// Route::post("store",[UserController::class,"store"]);
