<?php

namespace Tests\Feature;

use App\Enums\ProductType;
use App\Enums\UserRole;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class ProductControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function tokenForRole(UserRole $role): string
    {
        $user = User::factory()->create(['role' => $role->value]);
        return JWTAuth::fromUser($user);
    }

    #[Test]
    public function index_returns_paginated_products()
    {
        Product::factory()->count(20)->create();

        $this->getJson('api/products')
            ->assertOk()
            ->assertJsonStructure([
                'current_page',
                'data',
                'from',
                'last_page',
                'total'
            ]);
    }

    #[Test]
    public function show_returns_single_product()
    {
        $product = Product::factory()->create();

        $this->getJson("api/products/{$product->id}")
            ->assertOk()
            ->assertJson([
                'id' => $product->id,
                'name' => $product->name,
                'type' => $product->type,
                'price' => $product->price,
                'description' => $product->description,
                'image' => $product->image,
            ]);
    }

    #[Test]
    public function store_requires_authentication()
    {
        $payload = Product::factory()->make()->toArray();

        $this->postJson('/api/products', $payload)
            ->assertUnauthorized();
    }

    #[Test]
    public function store_requires_admin_role()
    {
        $payload = Product::factory()->make()->toArray();

        $this->withToken($this->tokenForRole(UserRole::CUSTOMER))
            ->postJson('/api/products', $payload)
            ->assertForbidden();
    }

    #[Test]
    public function admin_can_store_a_product()
    {
        $payload = [
            'name' => 'New Pizza',
            'type' => ProductType::PIZZA->value,
            'price' => 100,
            'description' => 'new pizza description',
            'image' => 'new_pizza.jpg',
        ];

        $this->withToken($this->tokenForRole(UserRole::ADMIN))
            ->postJson('/api/products', $payload)
            ->assertCreated()
            ->assertJson([
                'name' => $payload['name'],
                'type' => $payload['type'],
                'price' => $payload['price'],
                'description' => $payload['description'],
                'image' => $payload['image'],
            ]);
        $this->assertDatabaseHas('products', $payload);
    }

    #[Test]
    public function update_requires_authentication()
    {
        $product = Product::factory()->create();
        $payload = ['name' => 'Updated pizza'];

        $this->patchJson("/api/products/{$product->id}", $payload)
            ->assertUnauthorized();
    }

    #[Test]
    public function update_requires_admin_role()
    {
        $product = Product::factory()->create();
        $payload = ['name' => 'Updated pizza'];

        $this->withToken($this->tokenForRole(UserRole::CUSTOMER))
            ->patchJson("/api/products/{$product->id}", $payload)
            ->assertForbidden();
    }

    #[Test]
    public function admin_can_update_a_product()
    {
        $product = Product::factory()->create(['name' => 'Test Product']);

        $this->withToken($this->tokenForRole(UserRole::ADMIN))
            ->putJson("/api/products/{$product->id}", [
                'name' => 'New name'
            ])
            ->assertOk()
            ->assertJson(['name' => 'New name']);
        $this->assertDatabaseHas('products', [
                'id' => $product->id,
                'name' => 'New name']
        );
    }

    #[Test]
    public function destroy_requires_authentication()
    {
        $product = Product::factory()->create();

        $this->deleteJson("/api/products/{$product->id}")
            ->assertUnauthorized();
    }

    #[Test]
    public function destroy_requires_admin_role()
    {
        $product = Product::factory()->create();

        $this->withToken($this->tokenForRole(UserRole::CUSTOMER))
            ->deleteJson("/api/products/{$product->id}")
            ->assertForbidden();
    }

    #[Test]
    public function admin_can_destroy_a_product()
    {
        $product = Product::factory()->create();

        $this->withToken($this->tokenForRole(UserRole::ADMIN))
            ->deleteJson("/api/products/{$product->id}")
            ->assertNoContent();
        $this->assertDatabaseMissing('products', [
            'id' => $product->id
        ]);
    }

    #[Test]
    public function validation_errors_on_store_return_unprocessable()
    {
        $payload = [
            'name' => '',
            'type' => 'invalid',
            'price' => -1,
            'description' => '',
            'image' => '',
        ];

        $this->withToken($this->tokenForRole(UserRole::ADMIN))
            ->postJson('/api/products', $payload)
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['name', 'type', 'price', 'description', 'image']);
    }
}
