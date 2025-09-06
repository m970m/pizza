<?php
declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductStoreRequest;
use App\Http\Requests\ProductUpdateRequest;
use App\Http\Resources\ProductResource;
use App\Http\Resources\ProductsCollection;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use OpenApi\Attributes as OA;

class ProductController extends Controller
{
    #[OA\Get(
        path: "/api/products",
        summary: "Get a list of all products",
        tags: ['Products'],
        parameters: [
            new OA\Parameter(
                parameter: 'page',
                name: 'page',
                description: 'page',
                in: 'query',
                required: false,
                schema: new OA\Schema(
                    type: 'integer',
                    example: 2
                )
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Get a list of all products",
                content: new OA\JsonContent(
                    ref: '#/components/schemas/ProductsCollection'
                )
            )
        ],
    )]
    public function index(): JsonResponse
    {
        $paginatedProducts = Cache::remember('products_all', 60, function() {
            return Product::paginate(10);
        });

        return response()->json(new ProductsCollection($paginatedProducts));
    }

    #[OA\Post(
        path: "/api/products",
        summary: "Create product",
        security: [
            ['bearerAuth' => []]
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                ref: '#/components/schemas/ProductStoreRequest'
            )
        ),
        tags: ['Products'],
        responses: [
            new OA\Response(
                response: 201,
                description: "Get created product",
                content: new OA\JsonContent(
                    ref: '#/components/schemas/ProductResource'
                )
            ),
            new OA\Response(
                response: 401,
                description: 'unauthenticated'
            )
        ]
    )]
    public function store(ProductStoreRequest $request): JsonResponse
    {
        $product = Product::create($request->validated());
        Cache::forget('products_all');

        return response()->json(new ProductResource($product), Response::HTTP_CREATED);
    }

    #[OA\Get(
        path: "/api/products/{product}",
        summary: "Get product by id",
        tags: ['Products'],
        parameters: [
            new OA\Parameter(
                parameter: 'product',
                name: 'product',
                description: 'Get a single product by id',
                in: 'path',
                required: true,
                schema: new OA\Schema(
                    type: 'integer',
                    example: 2
                )
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Get a single product by id",
                content: new OA\JsonContent(
                    ref: '#/components/schemas/ProductResource'
                )
            )
        ]
    )]
    public function show(Product $product): JsonResponse
    {
        $product = Cache::remember("products_$product->id", 60, function() use ($product) {
            return $product;
        });
        return response()->json(new ProductResource($product));
    }

    public function update(ProductUpdateRequest $request, Product $product): JsonResponse
    {
        $product->update($request->validated());
        Cache::forget('products_all');

        return response()->json(new ProductResource($product));
    }

    public function destroy(Product $product): JsonResponse
    {
        $product->delete();
        Cache::forget('products_all');

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
