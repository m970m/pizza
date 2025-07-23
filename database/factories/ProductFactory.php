<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = fake()->randomElement(['pizza', 'drink']);
        return [
            'name' => $type . '_' . fake()->word(),
            'type' => $type,
            'price' => fake()->numberBetween(20, 500),
            'description' => fake()->paragraph(),
            'image' => fake()->imageUrl(),
        ];
    }
}
