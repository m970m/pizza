<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\CartProduct;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class CartControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function tokenForRole(UserRole $role): string
    {
        $user = User::factory()->create(['role' => $role->value]);
        return JWTAuth::fromUser($user);
    }

    #[Test]
    public function index_requires_authentication()
    {
        $this->getJson('/api/cart')
            ->assertUnauthorized();
    }

    #[Test]
    public function index_requires_customer_role()
    {
        $this->withToken($this->tokenForRole(UserRole::ADMIN))
            ->getJson('/api/cart')
            ->assertForbidden();
    }

    #[Test]
    public function customer_can_index_and_index_returns_cart_product_list()
    {
        $user = User::factory()->create(['role' => UserRole::CUSTOMER]);
        $cartProducts = CartProduct::factory()->count(5)->create(['user_id' => $user->id]);
        $this->withToken(JWTAuth::fromUser($user))
            ->getJson('/api/cart')
            ->assertOk()
            ->assertJson($cartProducts->toArray());
    }

    #[Test]
    public function store_requires_authentication()
    {
        $this->getJson('/api/cart', ['product_id' => 7778])
            ->assertUnauthorized();
    }

    #[Test]
    public function store_requires_customer_role()
    {
        $this->withToken($this->tokenForRole(UserRole::ADMIN))
            ->getJson('/api/cart', ['product_id' => 7778])
            ->assertForbidden();
    }

    #[Test]
    public function store_requires_valid_data()
    {
        $this->withToken($this->tokenForRole(UserRole::CUSTOMER))
            ->postJson('/api/cart', ['product_id' => 7778])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['product_id']);
    }

    #[Test]
    public function customer_can_store_product()
    {
        $user = User::factory()->create(['role' => UserRole::CUSTOMER]);
        $product = Product::factory()->create();
        $this->withToken(JWTAuth::fromUser($user))
            ->postJson('/api/cart', ['product_id' => $product->id])
            ->assertCreated()
            ->assertJson(['product_id' => $product->id]);
        $this->assertDatabaseHas('cart_product', ['product_id' => $product->id]);
    }

    #[Test]
    public function update_requires_authentication()
    {
        $this->putJson('/api/cart', ['product_id' => 7778])
            ->assertUnauthorized();
    }

    #[Test]
    public function update_requires_customer_role()
    {
        $this->withToken($this->tokenForRole(UserRole::ADMIN))
            ->putJson('/api/cart', ['product_id' => 7778])
            ->assertForbidden();
    }

    #[Test]
    public function customer_can_update_cart()
    {
        $user = User::factory()->create(['role' => UserRole::CUSTOMER]);
        CartProduct::factory()->count(5)->create(['user_id' => $user->id]);
        $products = Product::factory()->count(3)->create();

        $payload = [
            'products' => $products->map(fn($product) => [
                'product_id' => $product->id,
                'quantity' => 2,
            ])->toArray(),
        ];

        $this->withToken(JWTAuth::fromUser($user))
            ->putJson('/api/cart', $payload)
            ->assertOk()
            ->assertJson($payload['products']);
        $this->assertDatabaseCount('cart_product', 3);
    }

    #[Test]
    public function remove_product_required_authentication()
    {
        $this->deleteJson('/api/cart/1')
            ->assertUnauthorized();
    }

    #[Test]
    public function remove_product_required_customer_role()
    {
        $this->withToken($this->tokenForRole(UserRole::ADMIN))
            ->deleteJson('/api/cart/1')
            ->assertForbidden();
    }

    #[Test]
    public function customer_can_remove_product()
    {
        $user = User::factory()->create(['role' => UserRole::CUSTOMER]);
        $product = Product::factory()->create();
        $user->cartProducts()->create([
            'product_id' => $product->id,
            'quantity' => 1
        ]);

        $this->withToken(JWTAuth::fromUser($user))
            ->deleteJson("api/cart/$product->id")
            ->assertNoContent();

        $this->assertDatabaseMissing('cart_product', [
            'user_id' => $user->id,
            'product_id' => $product->id
        ]);
    }

    #[Test]
    public function remove_product_required_valid_product_id()
    {
        $user = User::factory()->create(['role' => UserRole::CUSTOMER]);
        $product = Product::factory()->create();
        $user->cartProducts()->create([
            'product_id' => $product->id,
            'quantity' => 1
        ]);
        $this->withToken(JWTAuth::fromUser($user))
            ->deleteJson("api/cart/2")
            ->assertNotFound();
    }

    #[Test]
    public function remove_product_line_required_authentication()
    {
        $this->deleteJson('/api/cart/1/all')
            ->assertUnauthorized();
    }

    #[Test]
    public function remove_product_line_required_customer_role()
    {
        $this->withToken($this->tokenForRole(UserRole::ADMIN))
            ->deleteJson('/api/cart/1/all')
            ->assertForbidden();
    }

    #[Test]
    public function customer_can_remove_product_line()
    {
        $user = User::factory()->create(['role' => UserRole::CUSTOMER]);
        $product = Product::factory()->create();
        $user->cartProducts()->create([
            'product_id' => $product->id,
            'quantity' => 2
        ]);
        $this->withToken(JWTAuth::fromUser($user))
            ->deleteJson("api/cart/$product->id/all")
            ->assertNoContent();

        $this->assertDatabaseMissing('cart_product', [
            'user_id' => $user->id,
            'product_id' => $product->id
        ]);
    }
}
