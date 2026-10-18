<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
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
                'product_name'=>$item->product->name,
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
public function index(Request $request)
{
    $orders = $request->user()
        ->order()
        ->latest()
        ->paginate(10);

    return OrderResource::collection($orders);
}

public function show(Order $order){
Gate::authorize('view',$order);
$order->load('orderItem.product');
return new OrderResource($order);
}
 
public function cancel(Request $request, Order $order){
Gate::authorize('cancel',$order);
$order->load('orderItem.product');

if($order->status!=='pending'  && $request->user()->role !== 'admin'){
    return response()->json([
        'message'=>'Order connet be cancelled.'
    ],400);
}

DB::transaction(function() use($order){
    foreach($order->orderItem as $item){
        if($item->product){
            $item->product->increment('stock',$item->quantity);  
            }}
            $order->status='cancelled' ; 
            $order->save();
});
return new OrderResource($order->load('OrderItem.product'));
}


public function updateStatus(Request $request,Order $order){
    Gate::authorize('updateStatus',$order);
    $request->validate([
        'status'=>'required|in:pending,processing,shipped,completed'
    ]);
    $order->update([
        'status'=>$request->status
    ]);

    return new OrderResource($order->load('OrderItem.product'));
}
}
