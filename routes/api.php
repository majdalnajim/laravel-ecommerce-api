<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\OrderController;



Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


// Route Cart
Route::middleware('auth:sanctum')->group(function(){
Route::post('/cart/items',[CartController::class,'store']);
Route::post('/cart',[CartController::class,'show']);
Route::patch('/cart/items/{cartItem}',[CartController::class,'update']);
Route::delete('/cart/items/{cartItem}',[CartController::class,'destroy']);
Route::delete('/cart',[CartController::class,'clear']);
});

// Route Order
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/orders', [OrderController::class, 'store']);
    Route::get('/orders', [OrderController::class, 'index']);
    Route::get('/orders/{order}', [OrderController::class, 'show']);
    Route::patch('/orders/{order}/status', [OrderController::class, 'updateStatus']);
    Route::delete('/orders/{order}', [OrderController::class, 'cancel']);
});

// Route Auth
Route::post("/register",[AuthController::class,"register"]);
Route::post("/login",[AuthController::class,"login"]);
Route::apiResource("products",ProductController::class);
Route::apiResource("category",CategoryController::class);

