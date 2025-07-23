<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OrderProduct>
 */
class OrderProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'order_id' => Order::factory(),
            'product_id' => Product::factory(),
            'name' => fn(array $attr) => Product::find($attr['product_id'])->name,
            'type' => fn(array $attr) => Product::find($attr['product_id'])->type,
            'price' => fn(array $attr) => Product::find($attr['product_id'])->price,
            'description' => fn(array $attr) => Product::find($attr['product_id'])->description,
            'quantity' => fake()->numberBetween(1, 5)
        ];
    }
}
