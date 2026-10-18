<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    private function createCategory(): Category
    {
        return Category::create([
            'name' => 'Test Category',
        ]);
    }

    private function createProduct(Category $category): Product
    {
        return Product::create([
            'name' => 'Test Product',
            'description' => 'Test Description',
            'price' => 100,
            'stock' => 10,
            'category_id' => $category->id,
        ]);
    }

    public function test_user_can_get_all_products(): void
    {
        $category = $this->createCategory();

        $this->createProduct($category);
        $user=User::factory()->create();
        $response = $this->actingAs($user,'sanctum')->getJson('/api/products');

        $response->assertOk();
    }

    public function test_user_can_get_single_product(): void
    {
        $category = $this->createCategory();
        $product = $this->createProduct($category);
        $user=User::factory()->create();

        $response = $this->actingAs($user,'sanctum')->getJson("/api/products/{$product->id}");

        $response->assertOk();
    }

    public function test_user_can_create_product(): void
    {
        $user = User::factory()->create();
        $category = $this->createCategory();

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/products', [
                'name' => 'New Product',
                'description' => 'New Description',
                'price' => 150,
                'stock' => 20,
                'category_id' => $category->id,
            ]);

        $response->assertSuccessful();

        $this->assertDatabaseHas('products', [
            'name' => 'New Product',
            'price' => 150,
            'stock' => 20,
            'category_id' => $category->id,
        ]);
    }

    public function test_user_can_update_product(): void
    {
        $user = User::factory()->create();
        $category = $this->createCategory();
        $product = $this->createProduct($category);

        $response = $this->actingAs($user, 'sanctum')
            ->putJson("/api/products/{$product->id}", [
                'name' => 'Updated Product',
                'description' => 'Updated Description',
                'price' => 200,
                'stock' => 15,
                'category_id' => $category->id,
            ]);

        $response->assertSuccessful();

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Updated Product',
            'price' => 200,
            'stock' => 15,
        ]);
    }

    public function test_user_can_delete_product(): void
    {
        $user = User::factory()->create();
        $category = $this->createCategory();
        $product = $this->createProduct($category);

        $response = $this->actingAs($user, 'sanctum')
            ->deleteJson("/api/products/{$product->id}");

        $response->assertSuccessful(200);

        $this->assertSoftDeleted('products', [
            'id' => $product->id,
        ]);
    }
}