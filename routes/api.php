<?php

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

Route::controller(App\Http\Controllers\Api\RegisterController::class)->group(function(){
    Route::post('register', 'register');
    Route::post('login', 'login');
    Route::get('logout','logout');
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
        
Route::middleware('auth:sanctum')->group( function () {
    Route::apiResource('/posts', App\Http\Controllers\Api\PostController::class);
    Route::apiResource('/users', App\Http\Controllers\Api\UserController::class);
});


