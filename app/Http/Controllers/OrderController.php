<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Http\Resources\OrderResource;

class OrderController extends Controller
{
    public function store(Request $request){
//bring the basket 
        $cart=$request->user()
        ->cart()
        ->with('items.product')
        ->first();
        if(!$cart || $cart->items->isEmpty() ){
            return response()->json([
                'message'=>"Cart is empty"
            ],400);
        }
// check the stock
        foreach($cart->items as $item){
                if($item->quantity>$item->product->stock){
                    return response()->json([
                        'message'=>$item->product->name.'is out of stock'
                    ],400);
                }
        } 
// transaction function
      $order=DB::transaction(function() use($request,$cart){

        $order=Order::create([
            'user_id'=>$request->user()->id,
            'total_price'=> 0.0,
            'status'=>'pending',
        ]);
        $totalPrice=0;
        foreach($cart->items as $item){
        $orderItem=OrderItem::create([
                'order_id'=>$order->id,
                'product_id'=>$item->product_id,
                'quantity'=>$item->quantity,
                'price'=>$item->product->price
            
        ]);
        $totalPrice +=$item->product->price * $item->quantity;
        $item->product->decrement('stock',$item->quantity);
        
        }

        $order->update([
            'total_price'=>$totalPrice
        ]);

            return $order;
        } );

        $cart->items()->delete();
        $order->load('orderItem.product');
        return new OrderResource(
            $order
        );
    }
}
