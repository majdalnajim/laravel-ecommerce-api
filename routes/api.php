<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\Api\CartController
;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function(){
Route::post('/cart/items',[CartController::class,'store']);
Route::post('/cart',[CartController::class,'show']);
Route::patch('/cart/items/{cartItem}',[CartController::class,'update']);
});




Route::post("/register",[AuthController::class,"register"]);
Route::post("/login",[AuthController::class,"login"]);
Route::apiResource("products",ProductController::class);
Route::apiResource("category",CategoryController::class);

