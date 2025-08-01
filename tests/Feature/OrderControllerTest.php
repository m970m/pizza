<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Enums\UserRole;
use App\Models\CartProduct;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class OrderControllerTest extends TestCase
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
        $this->getJson('/api/orders')
            ->assertUnauthorized();
    }

    #[Test]
    public function index_requires_customer_role()
    {
        $this->withToken($this->tokenForRole(UserRole::ADMIN))
            ->getJson('/api/orders')
            ->assertForbidden();
    }

    #[Test]
    public function customer_can_index_and_index_returns_order_list()
    {
        $user = User::factory()->create(['role' => UserRole::CUSTOMER]);
        Order::factory()->count(3)->create(['user_id' => $user->id]);

        $this->withToken(JWTAuth::fromUser($user))
            ->getJson('/api/orders')
            ->assertOk()
            ->assertJsonStructure(['*' => [
                'id', 'user_id', 'status', 'delivery_address', 'phone_number', 'delivery_time', 'total'
            ]]);
    }

    #[Test]
    public function store_requires_authentication()
    {
        $this->getJson('/api/orders')
            ->assertUnauthorized();
    }

    #[Test]
    public function store_requires_customer_role()
    {
        $this->withToken($this->tokenForRole(UserRole::ADMIN))
            ->getJson('/api/cart')
            ->assertForbidden();
    }

    #[Test]
    public function customer_can_store_order()
    {
        $user = User::factory()->create(['role' => UserRole::CUSTOMER]);
        CartProduct::factory()->count(3)->create(['user_id' => $user->id]);
        $payload = [
            'delivery_address' => 'Russia, Volgograd, Pushkina, 23',
            'phone_number' => '880005553535',
            'delivery_time' => '2025-08-05 20:25',
        ];

        $this->withToken(JWTAuth::fromUser($user))
            ->postJson('/api/orders', $payload)
            ->assertCreated();
        $this->assertDatabaseHas('orders', ['user_id' => $user->id]);
    }

    #[Test]
    public function show_required_authentication()
    {
        $this->getJson('/api/orders/1')
            ->assertUnauthorized();
    }

    #[Test]
    public function show_required_customer_role()
    {
        $order = Order::factory()->create();

        $this->withToken($this->tokenForRole(UserRole::ADMIN))
            ->getJson("/api/orders/{$order->id}")
            ->assertForbidden();
    }

    #[Test]
    public function customer_can_show()
    {
        $user = User::factory()->create(['role' => UserRole::CUSTOMER]);
        $order = Order::factory()->create(['user_id' => $user->id]);

        $this->withToken(JWTAuth::fromUser($user))
            ->getJson("/api/orders/{$order->id}")
            ->assertOk()
            ->assertJsonStructure(['order' => ['order_products']]);
    }

    #[Test]
    public function update_required_authentication()
    {
        $this->patchJson("/api/orders/1")
            ->assertUnauthorized();
    }

    #[Test]
    public function update_required_admin_role()
    {
        $order = Order::factory()->create();

        $this->withToken($this->tokenForRole(UserRole::CUSTOMER))
            ->patchJson("/api/orders/$order->id")
            ->assertForbidden();
    }

    #[Test]
    public function admin_can_update_order()
    {
        $order = Order::factory()->create();
        $this->withToken($this->tokenForRole(UserRole::ADMIN))
            ->patchJson("/api/orders/$order->id", ['status' => OrderStatus::CANCELLED])
            ->assertOk()
            ->assertJson(['status' => OrderStatus::CANCELLED->value]);

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => OrderStatus::CANCELLED->value
        ]);
    }

    #[Test]
    public function get_all_orders_requires_authentication()
    {
        $this->getJson('/api/orders/all')
            ->assertUnauthorized();
    }

    #[Test]
    public function get_all_orders_requires_admin_role()
    {
        Order::factory()->count(3)->create();

        $this->withToken($this->tokenForRole(UserRole::CUSTOMER))
            ->getJson('/api/orders/all')
            ->assertForbidden();
    }

    #[Test]
    public function admin_can_get_all_orders()
    {
        Order::factory()->count(3)->create();

        $this->withToken($this->tokenForRole(UserRole::ADMIN))
            ->getJson('/api/orders/all')
            ->assertOk()
            ->assertJson(Order::all()->toArray());
    }
}
