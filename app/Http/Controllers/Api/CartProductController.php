<?php
declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CartProductStoreRequest;
use App\Models\CartProduct;
use Illuminate\Http\Response;

class CartProductController extends Controller
{
    public function index()
    {
        return response()->json(CartProduct::all());
    }

    public function show(CartProduct $cartProduct)
    {
        return response()->json($cartProduct);
    }

    public function store(CartProductStoreRequest $request)
    {
        $validatedData = array_merge($request->validated(), ['quantity' => 1]);
        return response()->json(CartProduct::create($validatedData), Response::HTTP_CREATED);
    }

    public function destroy(CartProduct $cartProduct)
    {
        $cartProduct->delete();
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
