<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controller\Api\AuthController;

Route::middleware('auth:sanctum')->get('\user',function (Request $request){
return $request->user();
});
// Route::post('register',[AuthController::class,'register']);
// Route::post('login',[AuthController::class,'login']);
