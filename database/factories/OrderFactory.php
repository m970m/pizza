<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $statuses = ['processing', 'shipped', 'delivered', 'cancelled'];
        return [
            'user_id' => User::factory(),
            'status' => fake()->randomElement($statuses),
            'delivery_address' => fake()->address(),
            'phone_number' => fake()->phoneNumber(),
            'delivery_time' => fake()->dateTimeBetween('now', '+1 days')
        ];
    }
}
