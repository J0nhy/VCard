<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\TaskController;
use App\Http\Controllers\api\ProjectController;
use App\Http\Controllers\api\UserController;
use App\Http\Controllers\api\AuthController;


Route::post('login', [AuthController::class, 'login']);

Route::post('users', [UserController::class, 'store']);


Route::middleware('auth:api')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('users/me', [UserController::class, 'show_me']);

    Route::get('users', [UserController::class, 'index']);
    Route::get('users/{user}', [UserController::class, 'show'])
        ->middleware('can:view,user');
    Route::put('users/{user}', [UserController::class, 'update'])
        ->middleware('can:update,user');
    Route::patch('users/{user}/password', [UserController::class, 'update_password'])
        ->middleware('can:updatePassword,user');
});
