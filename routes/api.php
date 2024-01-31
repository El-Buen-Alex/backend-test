<?php

use App\Http\Controllers\LoginController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
Route::controller( LoginController::class)->group(function(){
    Route::post('login', 'login');
});


Route::group([
    'middleware' => 'jwt.verify',
    'prefix' => 'v1'

], function ($router) {
    Route::apiResource('task', TaskController::class);
    Route::apiResource('user', UserController::class);
});
