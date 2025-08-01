<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrderStoreRequest;
use App\Http\Requests\OrderUpdateRequest;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class OrderController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        return response()->json($request->user()->orders()->get());
    }

    public function store(OrderService $orderService, OrderStoreRequest $request): JsonResponse
    {
        $orderData = $request->validated();
        $order = $orderService->createOrder($request->user(), $orderData);
        return response()->json($order, Response::HTTP_CREATED);
    }

    public function show(Request $request, Order $order): JsonResponse
    {
        $order = $request->user()->orders()->with('orderProducts')->findOrFail($order->id);
        return response()->json(['order' => $order]);
    }

    public function update(Order $order, OrderUpdateRequest $request): JsonResponse
    {
        $order->update($request->validated());
        return response()->json($order);
    }

    public function getAllOrders(): JsonResponse
    {
        return response()->json(Order::all());
    }
}
