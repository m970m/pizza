<?php
declare(strict_types=1);

namespace App\Services;

use App\Exceptions\EmptyCartException;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function createOrder(User $user, array $orderData): Order
    {
        $cartProducts = $user->cartProducts()->with('product')->get();

        if ($cartProducts->isEmpty())
        {
            throw new EmptyCartException();
        }

        return DB::transaction(function() use ($orderData, $user, $cartProducts) {
            $order = Order::factory()->create([
                'user_id' => $user->id,
                'status' => 'new',
                'phone_number' => $orderData['phone_number'],
                'delivery_address' => $orderData['delivery_address'],
                'delivery_time' => $orderData['delivery_time']
            ]);

            $orderProductData = $this->buildOrderProductData($cartProducts);
            $order->orderProducts()->createMany($orderProductData);
            $user->cartProducts()->delete();

            return $order;
        });
    }

    private function buildOrderProductData(Collection $cartProducts): array
    {
        $orderProductData = [];
        foreach ($cartProducts as $cartProduct)
        {
            $orderProductData[] = [
                'product_id' => $cartProduct->product->id,
                'name' => $cartProduct->product->name,
                'type' => $cartProduct->product->type,
                'price' => $cartProduct->product->price,
                'description' => $cartProduct->product->description,
                'quantity' => $cartProduct->quantity
            ];
        }

        return $orderProductData;
    }
}
