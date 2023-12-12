<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\AdminController;
use App\Http\Controllers\api\AuthController;
use App\Http\Controllers\api\UsersController;
use App\Http\Controllers\api\VcardController;
use App\Http\Controllers\api\TransactionController;
use App\Models\Transaction;

Route::post('login', [AuthController::class, 'login']);


Route::post('vcard', [VcardController::class, 'store']);


Route::post('admin', [AdminController::class, 'store']);


Route::get('admins/gerir', [AdminController::class, 'show_all']);
Route::delete('admins/gerir/{id}', [AdminController::class, 'delete']);
Route::patch('users/block/{phone_number}', [VcardController::class, 'updateBlockedStatus']);

Route::get('users', [VcardController::class, 'show_all']);
Route::delete('users/{email}', [VcardController::class, 'destroy']);
Route::patch('users/{email}', [VcardController::class, 'editMaxDebit']);


Route::get('users/{email}', [VcardController::class, 'search']);

Route::get('transactions/{phone_number}', [TransactionController::class, 'show']);


Route::middleware('auth:api')->group(function () {

    Route::post('logout', [AuthController::class, 'logout']);

    Route::get('admins/me', [AdminController::class, 'show_me']);
    Route::get('users/me', [UsersController::class, 'show_me']);
    Route::get('vcard/me', [VcardController::class, 'show_me']);


    Route::get('admin/{id}', [AdminController::class, 'show']);
    Route::put('admin/{id}', [AdminController::class, 'update']);
    Route::delete('admin/{id}', [AdminController::class, 'destroy']);
    Route::get('admin/password/{id}', [AdminController::class, 'showPassword']);
    Route::put('admin/password/{id}', [AdminController::class, 'updatePassword']);


    Route::get('vcard/{phone_number}', [VcardController::class, 'show']);
    Route::put('vcard/{phone_number}', [VcardController::class, 'update']);
    Route::delete('vcard/{phoneNumber}', [VcardController::class, 'destroy']);
    Route::get('vcard/password/{phone_number}', [VcardController::class, 'showPassword']);
    Route::put('vcard/password/{phone_number}', [VcardController::class, 'updatePassword']);


    Route::get('transaction/{id}', [TransactionController::class, 'show_specific']);
    Route::put('transaction/{id}', [TransactionController::class, 'update']);
    //Route::delete('transactions/{phoneNumber}', [TransactionController::class, 'destroy']);
    Route::post('transaction', [TransactionController::class, 'store']);
});

