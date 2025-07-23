<?php

namespace Database\Seeders;

use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class CartItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $products = Product::all();

        if ($users->isEmpty() || $products->isEmpty())
        {
            CartItem::factory(20)->create();
            return;
        }

        foreach ($users as $user)
        {
            $userProducts = $products->random(rand(0, min(5, $products->count())));
            foreach ($userProducts as $product)
            {
                CartItem::factory()->create([
                    'user_id' => $user->id,
                    'product_id' => $product->id
                ]);
            }
        }
    }
}
