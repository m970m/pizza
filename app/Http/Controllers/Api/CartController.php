<?php
declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CartStoreRequest;
use App\Http\Requests\CartUpdateRequest;
use App\Models\CartProduct;
use App\Services\CartService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CartController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        return response()->json($request->user()->cartProducts()->get());
    }

    public function store(CartService $cartProductService, CartStoreRequest $request): JsonResponse
    {
        $user = $request->user();
        $productData = $request->validated();
        $product = $cartProductService->addProduct($user, $productData);

        return response()->json($product, Response::HTTP_CREATED);
    }

    public function update(CartService $cartService, CartUpdateRequest $request): JsonResponse
    {
        $user = $request->user();
        $cartProductsData = $request->validated()['products'];
        $updatedCartProducts = $cartService->updateProducts($user, $cartProductsData);

        return response()->json($updatedCartProducts);
    }


    public function remove(Request $request, string $productId): JsonResponse
    {
        $cartProduct = CartProduct::where('id', $productId)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();
        if ($cartProduct->quantity > 1)
        {
            --$cartProduct->quantity;
            $cartProduct->save();
        } else
        {
            $cartProduct->delete();
        }

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    public function removeAll(Request $request, string $productId): JsonResponse
    {
        $cartProducts = CartProduct::where('product_id', $productId)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();
        $cartProducts->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
