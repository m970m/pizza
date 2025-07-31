<?php
declare(strict_types=1);

namespace App\Services;

use App\Enums\ProductType;
use App\Exceptions\LimitProductException;
use App\Models\CartProduct;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class CartService
{
    public function addProduct(User $user, array $productData): CartProduct
    {
        if (!$this->validProductQuantity($user, $productData))
        {
            throw new LimitProductException();
        }

        $product = $user->cartProducts()->where('product_id', $productData['product_id'])->first();
        if ($product)
        {
            ++$product->quantity;
            $product->save();

            return $product;
        }

        return $user->cartProducts()->create([
            'product_id' => $productData['product_id'],
            'quantity' => 1
        ]);
    }

    public function updateProducts(User $user, array $cartProductsData): Collection
    {
        $products = Product::whereIn('id', array_column($cartProductsData, 'product_id'))->get();

        $cartProductCollection = collect($cartProductsData)->keyBy('product_id');

        $quantityByType = $products
            ->groupBy('type')
            ->map(fn($group) => $group->sum(
                fn($product) => $cartProductCollection[$product->id]['quantity']
            ));

        foreach ($quantityByType as $type => $quantity)
        {
            if ($quantity > config("order.limits.$type"))
            {
                throw new LimitProductException();
            }
        }

        return DB::transaction(function() use ($user, $cartProductsData) {
            $user->cartProducts()->delete();
            return $user->cartProducts()->createMany($cartProductsData);
        });
    }

    private function validProductQuantity(User $user, array $productData): bool
    {
        $productType = ProductType::from(Product::findOrFail($productData['product_id'])->type);
        $productQuantity = $user->cartProducts()
            ->whereHas('product', fn($q) => $q->where('type', $productType->value))
            ->sum('quantity');

        return (!$productQuantity || $productQuantity < config("order.limits.$productType->value"));
    }
}
