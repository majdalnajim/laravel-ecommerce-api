<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use App\Models\CartItem;
use App\Models\Cart;

class CartTest extends TestCase
{
        use RefreshDatabase;

    // test add product to cart


public function test_user_can_add_product_to_cart(): void{
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
$response=$this->actingAs($user,'sanctum')->postJson("/api/cart/items",[
    'product_id'=>$product->id,
    'quantity'=>2
]);
// $response=$this->actingAs($user,'sanctum')->postJson("/api/products/{$product->id}/cart",['quantity'=>2]);
$response->assertStatus(201);
$this->assertDatabaseHas('cart_items',[
'product_id'=>$product->id,
'quantity'=>2
]);
    }


     public function test_user_can_increase_existing_cart_item_quantity(): void
    {
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

$this->actingAs($user,'sanctum')->postJson("/api/cart/items",[
    'product_id'=>$product->id,
    'quantity'=>2
]);

$response=$this->actingAs($user,'sanctum')->postJson("/api/cart/items",[
    'product_id'=>$product->id,
    'quantity'=>3
]);

$response->assertStatus(201);
$this->assertDatabaseHas('cart_items',[
'product_id'=>$product->id,
'quantity'=>5

]);
    }


    public function test_quantity_defaults_to_one():void{
        $user=User::factory()->create();
        $category=Category::create([
            'name'=>'Test Category'
        ]);

        $product=Product::create([
'name'=>'Test Product',
'description'=>'Test Descraption',
'price'=>100,
'stock'=>10,
'category_id'=>$category->id
        ]);


        $response = $this->actingAs($user,'sanctum')->postJson('api/cart/items',['product_id'=>$product->id]);
        $response->assertStatus(201);
        $this->assertDatabaseHas('cart_items',[
    'product_id'=>$product->id,
    'quantity'=>1
        ]);
    }

    public function test_user_can_update_cart_item_quantity():void{
        $user=User::factory()->create();
        $category=Category::create([
            'name'=>'Test Category'
        ]);

        $product=Product::create([
            'name'=>'Test Product',
            'description'=>'Test Description',
            'price'=>100 ,
            'stock'=>10,
            'category_id'=>$category->id
            ]);
            $cart=Cart::create([
                'user_id'=>$user->id
            ]);
            $cartItem=CartItem::create([
                'cart_id'=>$cart->id,
                'product_id'=>$product->id,
                'quantity'=>2
            ]);

            $response=$this->actingAs($user,'sanctum')->patchJson("/api/cart/items/{$cartItem->id}",[
            'quantity'=>5]);

            $response->assertStatus(200);
        $this->assertDatabaseHas('cart_items',[
    'id'=>$cartItem->id,
    'quantity'=>5
        ]);
    }

    public function test_user_can_delete_cart_item(): void
{
    $user = User::factory()->create();

    $category = Category::create([
        'name' => 'Test Category',
    ]);

    $product = Product::create([
        'name' => 'Test Product',
        'description' => 'Test Description',
        'price' => 100,
        'stock' => 10,
        'category_id' => $category->id,
    ]);

    $cart = Cart::create([
        'user_id' => $user->id,
    ]);

    $cartItem = CartItem::create([
        'cart_id' => $cart->id,
        'product_id' => $product->id,
        'quantity' => 2,
    ]);
    $response = $this->actingAs($user, 'sanctum')
    ->deleteJson("/api/cart/items/{$cartItem->id}");
     $response->assertStatus(200);
        $this->assertDatabaseMissing('cart_items',[
    'id'=>$cartItem->id,
    
        ]);
    }


}
