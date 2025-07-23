<?php
declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductStoreRequest;
use App\Http\Requests\ProductUpdateRequest;
use App\Models\Product;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    public function index(): JsonResponse
    {
        $products = Product::all();
        return response()->json($products);
    }

    public function store(ProductStoreRequest $request): JsonResponse
    {
        $validatedData = $request->validated();
        $product = Product::create($validatedData);

        return response()->json($product);
    }

    public function show($id)
    {
        try
        {
            $product = Product::findOrFail($id);
            return response()->json($product);
        } catch (ModelNotFoundException $exception)
        {
            return response()->json(['message' => 'Product not found.'], 404);
        } catch (\Exception $exception)
        {
            return response()->json(['message' => 'An error occurred.'], 500);
        }
    }

    public function update(ProductUpdateRequest $request, $id)
    {
        $validatedData = $request->validated();
        $product = Product::findOrFail($id);
        $product->update($validatedData);

        return response()->json($product);
    }

    public function destroy($id)
    {
        try
        {
            $product = Product::findOrFail($id);
            $product->delete();
            return response()->json([], 204);
        } catch (ModelNotFoundException $exception)
        {
            return response()->json(['message' => 'Product not found'], 404);
        } catch (\Exception $exception)
        {
            return response()->json(['message' => 'An error occurred.'], 500);
        }
    }
}
