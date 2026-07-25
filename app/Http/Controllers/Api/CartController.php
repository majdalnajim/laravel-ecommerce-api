<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\CartItem;
use App\Http\Resources\CartItemResource;
use App\Http\Resources\CartResource;


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
        'item'=>new CartItemResource($item)
        ],201);

    }
    public function show(Request $request){
        $cart=$request->user()->cart()->with('items.product')->first();
        if(!$cart){
            return response()->json([
                'massage'=>'Cart is empty.'
            ],404);
        }
        return new CartResource($cart);
    }
    public function update(Request $request,CartItem $cartItem){
        $data=$request->validate([
            'quantity'=>'required|integer|min:1'
        ]);

        if($cartItem->cart->user_id !== $request->user()->id){
            abort(403);
        }
        
        $cartItem->update([
            'quantity'=>$data['quantity']
        ]);
        $cartItem->load('product');
        return new CartItemResource($cartItem);

    }
}
