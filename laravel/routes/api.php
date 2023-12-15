<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\AdminController;
use App\Http\Controllers\api\AuthController;
use App\Http\Controllers\api\CategoryController;
use App\Http\Controllers\api\DefaultCategoryController;
use App\Http\Controllers\api\UsersController;
use App\Http\Controllers\api\VcardController;
use App\Http\Controllers\api\TransactionController;
use App\Models\Transaction;


Route::post('login', [AuthController::class, 'login']);


Route::post('vcard', [VcardController::class, 'store']);

//admins



Route::get('vcards', [VcardController::class, 'index']);
Route::patch('vcards/{phone_number}', [VcardController::class, 'updateByAdmin']);
Route::delete('admin/{email}', [VcardController::class, 'destroy']);
Route::get('admin/default_categories', [DefaultCategoryController::class, 'index']);
Route::delete('admin/default_categories/{category}', [DefaultCategoryController::class, 'destroy']);
Route::post('default_categories', [DefaultCategoryController::class, 'store']);


//Route::patch('admin/{email}', [VcardController::class, 'editMaxDebit']);
//

Route::middleware('auth:api')->group(function () {

    Route::post('logout', [AuthController::class, 'logout']);

    Route::put('activatePiggy/{vcard}', [VcardController::class, 'ActivatePiggy']);
    Route::put('depositar/{vcard}', [VcardController::class, 'add']);
    Route::put('debitar/{vcard}', [VcardController::class, 'remove']);

    Route::get('admins/me', [AdminController::class, 'show_me']);
    Route::get('users/me', [UsersController::class, 'show_me']);
    Route::get('vcard/me', [VcardController::class, 'show_me']);

    Route::resource('admin', AdminController::class)->middleware('can:update,admin');



    Route::get('admin/password/{id}', [AdminController::class, 'showPassword']);
    Route::put('admin/password/{id}', [AdminController::class, 'updatePassword']);
    Route::delete('admins/{admin}', [AdminController::class, 'delete']);
    Route::get('admins', [AdminController::class, 'index']);


    Route::get('vcard/{vcard}', [VcardController::class, 'show'])
        ->middleware('can:view,vcard');


    Route::put('vcard/{phone_number}', [VcardController::class, 'update']);
    Route::delete('vcard/{phoneNumber}', [VcardController::class, 'destroy']);
    Route::get('vcard/password/{phone_number}', [VcardController::class, 'showPassword']);
    Route::put('vcard/password/{phone_number}', [VcardController::class, 'updatePassword']);
    Route::get('vcards/{vcard}/transactions', [VcardController::class, 'getVcardTransactions']);


    //transacoes

    //Route::get('transaction/{id}', [TransactionController::class, 'show_specific']);
    //Route::put('transaction/{id}', [TransactionController::class, 'update']);
    //Route::delete('transactions/{phoneNumber}', [TransactionController::class, 'destroy']);
    Route::post('transaction', [TransactionController::class, 'store']);
    Route::get('vcard/{user}/transactions', [TransactionController::class, 'index']);
    Route::get('vcard/{user}/transactions/{transaction}', [TransactionController::class, 'show']);


    //categorias
    // Route::get('categories/{search}', [CategoryController::class, 'show']);
    Route::delete('vcard/{user}/categories/{category}', [CategoryController::class, 'destroy']);
    Route::get('vcard/{user}/categories', [CategoryController::class, 'index']);
    Route::post('vcard/{user}/categories', [CategoryController::class, 'store']);



    //categorias default
    //Route::get('default_categories/{search}', [DefaultCategoryController::class, 'show']);

});

Route::get('users/{email}', [VcardController::class, 'search']);

Route::post('transactionCredit', [TransactionController::class, 'storeCredit']);
