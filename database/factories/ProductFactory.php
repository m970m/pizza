<?php

namespace Database\Factories;

use App\Enums\ProductType;
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
        $type = fake()->randomElement(ProductType::values());
        return [
            'name' => $type . '_' . fake()->word(),
            'type' => $type,
            'price' => fake()->numberBetween(20, 500),
            'description' => fake()->words(3, true),
            'image' => fake()->imageUrl(),
        ];
    }
}
