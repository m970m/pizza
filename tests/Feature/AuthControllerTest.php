<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function tokenForRole(UserRole $role): string
    {
        $user = User::factory()->create(['role' => $role->value]);
        return JWTAuth::fromUser($user);
    }

    #[Test]
    public function register_with_valid_data_creates_user_and_returns_token()
    {
        $payload = [
            'first_name' => 'Ivan',
            'last_name' => 'Ivanov',
            'email' => 'ivan@mail.com',
            'password' => 'password'
        ];

        $this->postJson('/api/register', $payload)
            ->assertCreated()
            ->assertJsonStructure(['token']);
        $this->assertDatabaseHas('users', ['email' => 'ivan@mail.com']);
    }

    #[Test]
    public function register_with_invalid_data_return_validation_errors()
    {
        $payload = [
            'first_name' => '',
            'last_name' => '',
            'email' => 'ivanmail.com',
            'password' => 'passw'
        ];

        $this->postJson('/api/register', $payload)
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['first_name', 'last_name', 'email', 'password']);
    }

    #[Test]
    public function login_requires_valid_credentials()
    {
        $payload = [
            'email' => 'invalidemail@mail.com',
            'password' => 'invalidpassword'
        ];

        $this->postJson('api/login', $payload)
            ->assertUnauthorized();
    }

    #[Test]
    public function login_with_valid_credentials_returns_token()
    {
        $payload = [
            'email' => 'user@email.com',
            'password' => 'password'
        ];
        User::factory()->create($payload);

        return $this->postJson('api/login', $payload)
            ->assertOk()
            ->assertJsonStructure(['token']);
    }

    #[Test]
    public function logout_requires_user_token()
    {
        $this->postJson('/api/logout')
            ->assertUnauthorized();
    }

    #[Test]
    public function logout_requires_valid_user_token()
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);
        JWTAuth::setToken($token)->invalidate();

        $this->withToken($token)
            ->postJson('/api/logout')
            ->assertUnauthorized();
    }

    #[Test]
    public function logout_with_valid_token_returns_ok()
    {
        $this->withToken($this->tokenForRole(UserRole::CUSTOMER))
            ->postJson('/api/logout')
            ->assertOk();
    }
}
