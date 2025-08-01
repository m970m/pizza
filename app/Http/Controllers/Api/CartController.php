<?php
declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CartStoreRequest;
use App\Http\Requests\CartUpdateRequest;
use App\Services\CartService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CartController extends Controller
{
    public function __construct(
        private readonly CartService $cartService
    ) {}

    public function index(Request $request): JsonResponse
    {
        return response()->json($request->user()->cartProducts()->get());
    }

    public function store(CartStoreRequest $request): JsonResponse
    {
        $productData = $request->validated();
        $product = $this->cartService->addProduct($request->user(), $productData);

        return response()->json($product, Response::HTTP_CREATED);
    }

    public function update(CartUpdateRequest $request): JsonResponse
    {
        $cartProductsData = $request->validated()['products'];
        $updatedCartProducts = $this->cartService->updateCart($request->user(), $cartProductsData);

        return response()->json($updatedCartProducts);
    }

    public function removeProduct(Request $request, string $productId): JsonResponse
    {
        $this->cartService->removeProduct($request->user(), $productId);

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    public function removeProductLine(Request $request, string $productId): JsonResponse
    {
        $this->cartService->removeProductLine($request->user(), $productId);

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
