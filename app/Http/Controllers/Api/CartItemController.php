<?php
declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CartItemStoreRequest;
use App\Models\CartItem;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CartItemController extends Controller
{
    public function index()
    {
        return response()->json(CartItem::all());
    }

    public function show($id)
    {
        try
        {
            $cartItem = CartItem::findOrFail($id);
            return response()->json($cartItem);
        } catch (ModelNotFoundException $exception)
        {
            return response()->json(['message' => 'Cart item not found.'], 404);
        } catch (\Exception $exception)
        {
            return response()->json(['message' => 'An error occurred.'], 500);
        }
    }

    public function store(CartItemStoreRequest $request)
    {
        $validatedData = array_merge($request->validated(), ['quantity' => 1]);
        $cartItem = CartItem::create($validatedData);

        return response()->json($cartItem);
    }

    public function destroy($id)
    {
        try
        {
            $cartItem = CartItem::findOrFail($id);
            $cartItem->delete();
            return response()->json([], 204);
        } catch (ModelNotFoundException $exception)
        {
            return response()->json(['message' => 'Model not found.'], 404);
        } catch (\Exception $exception)
        {
            return response()->json(['message' => 'An error occurred.'], 500);
        }
    }
}
