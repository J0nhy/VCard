<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\AdminController;
use App\Http\Controllers\api\AuthController;
use App\Http\Controllers\api\UsersController;
use App\Http\Controllers\api\VcardController;

Route::post('login', [AuthController::class, 'login']);


Route::get('vcard/{phoneNumber}', [VcardController::class, 'show']);
Route::put('vcard/{phoneNumber}', [VcardController::class, 'update']);
Route::delete('vcard/{phoneNumber}', [VcardController::class, 'destroy']);
Route::get('vcard/{phone_number}', [VcardController::class, 'show']);
Route::put('vcard/{phone_number}', [VcardController::class, 'update']);
Route::get('vcard/password/{phone_number}', [VcardController::class, 'showPassword']);
Route::put('vcard/password/{phone_number}', [VcardController::class, 'updatePassword']);
Route::post('vcard', [VcardController::class, 'store']);


Route::post('admin', [AdminController::class, 'store']);



Route::get('admin/{id}', [AdminController::class, 'show']);
Route::put('admin/{id}', [AdminController::class, 'update']);

Route::get('admin/{id}', [AdminController::class, 'show']);
Route::put('admin/{id}', [AdminController::class, 'update']);
Route::delete('admin/{id}', [AdminController::class, 'destroy']);
Route::get('admin/password/{id}', [AdminController::class, 'showPassword']);
Route::put('admin/password/{id}', [AdminController::class, 'updatePassword']);

Route::middleware('auth:api')->group(function () {

    Route::post('logout', [AuthController::class, 'logout']);

    Route::get('admins/me', [AdminController::class, 'show_me']);
    Route::get('users/me', [UsersController::class, 'show_me']);
    Route::get('vcard/me', [VcardController::class, 'show_me']);


});

