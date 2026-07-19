<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cart;
class CartController extends Controller
{
    public function store(Request $request){
        $data=$request->validate([
            'product_id'=>'required|exists:products,id',
            'quantity'=>'nullable|integer|min:1'
        ]);
        $user=$request->user();
        $cart=Cart::firstOrCreate([
            'user_id'=>$user->id,
        ]);
        $quantity=$data['quantity']??1;
        $item=$cart->items()->where('product_id',$data['product_id'])->first();
        if($item){
            
        $item->increment('quantity',$quantity);
        
        $item->refresh();
        }else{
            
           $item= $cart->items()->create([
               'product_id' => $data['product_id'],
                'quantity'=>$quantity,
            ]);
        }
        
        return response()->json([
        'massage'=>'Product added to cart successfully',
        'item'=>$item
        ],201);

    }
}
