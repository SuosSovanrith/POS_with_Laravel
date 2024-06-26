<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CartApiController;
use App\Http\Controllers\AuthApiController;
use App\Http\Controllers\OrderApiController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/login', [AuthApiController::class, 'Login']);

Route::post('/cart', [CartApiController::class, 'CartView']);
Route::post('/addcart', [CartApiController::class, 'AddCart']);
Route::post('/addcartimage', [CartApiController::class, 'AddCartImage']);
Route::post('/updatecartquantity', [CartApiController::class, 'UpdateCartQuantity']);
Route::post('/deletecart', [CartApiController::class, 'DeleteCart']);
Route::post('/clearcart', [CartApiController::class, 'ClearCart']);
Route::post('/searchproduct', [CartApiController::class, 'SearchProduct']);

Route::post('/addorder', [OrderApiController::class, 'AddOrder']);
Route::post('/getlastorder', [OrderApiController::class, 'GetLastOrder']);