<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class AuthController extends Controller
{
   public function register(Request $request){
    $data=$request->validate([
        'name'=>'required|min:3|max:12',
        'email'=>'required|email|unique:users',
        'password'=>'required|min:6'
    ]);

    $user=User::create([
        'name'=>$data['name'],
        'email'=>$data['email'],
        'password'=>Hash::make($data['password']),
    ]);

    $token=$user->createToken('api-token')->plainTextToken;
    return response()->json([
        'token'=>$token,
        'massage'=>'successfuly register'
    ]);
   }

   public function login(Request $request){
    if(!Auth::attempt($request->only('email','password'))){
        return response()->json(['massage'=>'Invalid credentials']);
    }
    $user=Auth::user();
    $token=$user->createToken('api-token')->plainTextToken;
    return response()->json([
        'token'=>$token,
        'massage'=>'success login'
        ]);
   }
}
