<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\TaskController;
use App\Http\Controllers\api\ProjectController;
use App\Http\Controllers\api\UserController;
use App\Http\Controllers\api\AuthController;
use App\Http\Controllers\api\VcardController;

Route::post('login', [AuthController::class, 'login']);

Route::middleware('auth:api')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);

    Route::get('user/{id}', [UserController::class, 'show'])
        ->middleware('can:view,user');
    Route::put('user/{id}', [UserController::class, 'update'])
        ->middleware('can:update,user');
    Route::get('vcard/{phoneNumber}', [VcardController::class, 'show'])
        ->middleware('can:view,vcard');
    Route::put('vcard/{phoneNumber}', [VcardController::class, 'update'])
        ->middleware('can:update,vcard');

    Route::get('users', [UsersController::class, 'index']);
    Route::get('users/{user}', [UsersController::class, 'show'])
        ->middleware('can:view,user');
    Route::post('users', [UserController::class, 'store'])
        ->middleware('can:create,user');
    Route::put('users/{user}', [UsersController::class, 'update'])
        ->middleware('can:update,user');
    Route::patch('users/{user}/password', [UsersController::class, 'update_password'])
        ->middleware('can:updatePassword,user');
});
