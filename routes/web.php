<?php

use Illuminate\Support\Facades\Route;
//use App\http\Controller\UserController;
use App\Http\Controllers\UserController;

Route::get('/', function () {
    return view('welcome');
});
//Route::post("register",[UserController::class,"register"]);
