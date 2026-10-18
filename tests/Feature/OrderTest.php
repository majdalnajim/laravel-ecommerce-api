<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use App\Models\Order;
use App\Models\OrderItem;

class OrderTest extends TestCase
{
    use RefreshDatabase;


    // 1- test the create order
    public function test_user_can_create_order():void{
        $user=User::factory()->create();
        $category=Category::create([
            'name'=>'Test Category'
        ]);

        $product=Product::create([
'name'=>'Test Product',
'description'=>'Test Description',
'price'=>100,
'stock'=>10,
'category_id'=>$category->id,
        ]);

        $cart=Cart::create([
            'user_id'=>$user->id
        ]);

        $cartItem=CartItem::create([
            'cart_id'=>$cart->id,
            'product_id'=>$product->id,
            'quantity'=>2
        ]);

        $response=$this->actingAs($user,'sanctum')->postJson('/api/orders');
        $response->assertStatus(201);
        $this->assertDatabaseHas('orders',[
            'user_id'=>$user->id,
            'status'=>'pending'
        ]);
        $this->assertDatabaseHas('order_items',[
            'product_id'=>$product->id,
            'quantity'=>2
        ]);
        $this->assertDatabaseHas('products',[
            'id'=>$product->id,
            'stock'=>8
        ]);

    }
// 2- test the cancel order
        public function test_user_can_cancel_order():void{
            $user=User::factory()->create();

            $category=Category::create([
                'name'=>'category Test'
            ]);
            $product=Product::create([
                'name'=>'Test Product',
                'description'=>'Test Description',
                'price'=>100,
                'stock'=>10,
                'category_id'=>$category->id,
            ]);

        $cart=Cart::create([
            'user_id'=>$user->id
        ]);
        
        

        $cartItem=CartItem::create([
            'cart_id'=>$cart->id,
            'product_id'=>$product->id,
            'quantity'=>2
        ]);
        $order=Order::create([
            'user_id'=>$user->id,
            'total_price'=>200,
            'status'=>'pending'
        ]);
        $orderItem=OrderItem::create([
            'order_id'=>$order->id,
            'product_id'=>$product->id,
            'quantity'=>2,
            'price'=>100
        ]);
$product->decrement('stock', 2);
        $response=$this->actingAs($user,'sanctum')->deleteJson("/api/orders/{$order->id}");

$response->assertStatus(200);
        $this->assertDatabaseHas('orders',[
            'user_id'=>$user->id,
            'status'=>'cancelled'
        ]);
       
        $this->assertDatabaseHas('products',[
            'id'=>$product->id,
            'stock'=>10
        ]);
        }
// 3- test the authorized user
        public function test_user_auth():void{
            $user=User::factory()->create();
            $otherUser=User::factory()->create();

            $category=Category::create([
                'name'=>'category Test'
            ]);
            $product=Product::create([
                'name'=>'Test Product',
                'description'=>'Test Description',
                'price'=>100,
                'stock'=>10,
                'category_id'=>$category->id,
            ]);

        $cart=Cart::create([
            'user_id'=>$user->id
        ]);
        
        

        $cartItem=CartItem::create([
            'cart_id'=>$cart->id,
            'product_id'=>$product->id,
            'quantity'=>2
        ]);
        $order=Order::create([
            'user_id'=>$otherUser->id,
            'total_price'=>200,
            'status'=>'pending'
        ]);
        $orderItem=OrderItem::create([
            'order_id'=>$order->id,
            'product_id'=>$product->id,
            'quantity'=>2,
            'price'=>100
        ]);
$product->decrement('stock', 2);
        $response=$this->actingAs($user,'sanctum')->deleteJson("/api/orders/{$order->id}");

$response->assertStatus(403);
        $this->assertDatabaseHas('orders',[
            'user_id'=>$otherUser->id,
            'status'=>'pending'
        ]);
       
        $this->assertDatabaseHas('products',[
            'id'=>$product->id,
            'stock'=>8
        ]);
        }
// 4- test the user can't cancel non pending order
        public function test_user_connot_cancel_non_pending_order():void{
            $user=User::factory()->create();

            $category=Category::create([
                'name'=>'category Test'
            ]);
            $product=Product::create([
                'name'=>'Test Product',
                'description'=>'Test Description',
                'price'=>100,
                'stock'=>10,
                'category_id'=>$category->id,
            ]);

        $cart=Cart::create([
            'user_id'=>$user->id
        ]);
        

        $cartItem=CartItem::create([
            'cart_id'=>$cart->id,
            'product_id'=>$product->id,
            'quantity'=>2
        ]);
        $order=Order::create([
            'user_id'=>$user->id,
            'total_price'=>200,
            'status'=>'processing'
        ]);
        $orderItem=OrderItem::create([
            'order_id'=>$order->id,
            'product_id'=>$product->id,
            'quantity'=>2,
            'price'=>100
        ]);
$product->decrement('stock', 2);
        $response=$this->actingAs($user,'sanctum')->deleteJson("/api/orders/{$order->id}");

$response->assertStatus(400);
        $this->assertDatabaseHas('orders',[
            'user_id'=>$user->id,
            'status'=>'processing'
        ]);
       
        $this->assertDatabaseHas('products',[
            'id'=>$product->id,
            'stock'=>8
        ]);
        }
// 5- test admin can update the  order satus
      public function test_admin_can_update_order_status():void{
            $admin=User::factory()->create([
                'role'=>'admin'
            ]);
            $user=User::factory()->create([
                'role'=>'user'
            ]);

             $order=Order::create([
            'user_id'=>$user->id,
            'total_price'=>200,
            'status'=>'pending'
        ]);
            
   $response=$this->actingAs($admin,'sanctum')->patchJson("/api/orders/{$order->id}/status", [
            'status' => 'processing'
        ]);

$response->assertStatus(200);
        $this->assertDatabaseHas('orders',[
            'user_id'=>$user->id,
            'status'=>'processing'
        ]);
       
       }
    //    6- test the user can't update the order status
      public function test_user_canot_update_order_status():void{
            
            $user=User::factory()->create([
                'role'=>'user'
            ]);

             $order=Order::create([
            'user_id'=>$user->id,
            'total_price'=>200,
            'status'=>'pending'
        ]);
            
   $response=$this->actingAs($user,'sanctum')->patchJson("/api/orders/{$order->id}/status", [
            'status' => 'processing'
        ]);

$response->assertStatus(403);
        $this->assertDatabaseHas('orders',[
            'user_id'=>$user->id,
            'status'=>'pending'
        ]);
       
       }
//   7- test the user can't show auther user order
public function test_user_canot_show_auther_user_order():void{
            $user=User::factory()->create();
            $otherUser=User::factory()->create();

             $order=Order::create([
            'user_id'=>$user->id,
            'total_price'=>200,
            'status'=>'pending'
        ]);
            
   $response=$this->actingAs($otherUser,'sanctum')->getJson("/api/orders/{$order->id}");

$response->assertStatus(403);
        $this->assertDatabaseHas('orders',[
            'user_id'=>$user->id,
            'status'=>'pending'
        ]);
       
       }
}
