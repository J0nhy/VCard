<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\TaskController;
use App\Http\Controllers\api\ProjectController;
use App\Http\Controllers\api\UserController;
use App\Http\Controllers\api\AuthController;
use App\Http\Controllers\api\VcardController;

Route::post('login', [AuthController::class, 'login']);


Route::get('vcard/{phoneNumber}', [VcardController::class, 'show']);
Route::put('vcard/{phoneNumber}', [VcardController::class, 'update']);
Route::delete('vcard/{phoneNumber}', [VcardController::class, 'destroy']);

Route::post('vcard', [VcardController::class, 'store']);
Route::post('users', [UserController::class, 'store']);


//Route::middleware('auth:api')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);

    Route::get('user/{id}', [UserController::class, 'show']);
    Route::put('user/{id}', [UserController::class, 'update']);

Route::get('user/{id}', [UserController::class, 'show']);
Route::put('user/{id}', [UserController::class, 'update']);
Route::delete('user/{id}', [UserController::class, 'destroy']);
Route::get('user/password/{id}', [UserController::class, 'showPassword']);
Route::put('user/password/{id}', [UserController::class, 'updatePassword']);
Route::get('vcard/{phone_number}', [VcardController::class, 'show']);
Route::put('vcard/{phone_number}', [VcardController::class, 'update']);
Route::get('vcard/password/{phone_number}', [VcardController::class, 'showPassword']);
Route::put('vcard/password/{phone_number}', [VcardController::class, 'updatePassword']);

//Route::middleware('auth:api')->group(function () {

    Route::get('users', [UsersController::class, 'index']);
    Route::get('users/{user}', [UsersController::class, 'show']);

    Route::put('users/{user}', [UsersController::class, 'update']);
    Route::patch('users/{user}/password', [UsersController::class, 'update_password']);
//});
