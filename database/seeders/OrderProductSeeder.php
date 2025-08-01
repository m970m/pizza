<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;

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
            $randomProducts = $products->random(rand(1, min(5, $products->count())));

            $orderProducts = new Collection;
            foreach ($randomProducts as $product)
            {
                $orderProducts->push([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'name' => $product->name,
                    'type' => $product->type,
                    'price' => $product->price,
                    'description' => $product->description,
                    'quantity' => rand(1, 5),
                ]);
            }
            $order->orderProducts()->createMany($orderProducts->all());
            $total = $orderProducts->sum(fn($p) => $p['price'] * $p['quantity']);
            $order->update(['total' => $total]);
        }
    }
}
