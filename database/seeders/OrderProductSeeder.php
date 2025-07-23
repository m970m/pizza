<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Product;
use Illuminate\Database\Seeder;

class OrderProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $orders = Order::all();
        $products = Product::all();

        if ($orders->isEmpty() || $products->isEmpty())
        {
            OrderProduct::factory(20)->create();
            return;
        }

        foreach ($orders as $order)
        {
            $orderProducts = $products->random(rand(1, min(5, $products->count())));
            foreach ($orderProducts as $product)
            {
                OrderProduct::factory()->create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'name' => $product->name,
                    'type' => $product->type,
                    'price' => $product->price,
                    'description' => $product->description,
                ]);
            }
        }

    }
}
