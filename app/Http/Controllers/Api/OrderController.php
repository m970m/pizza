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
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        return response()->json($request->user()->orders()->get());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(OrderService $orderService, OrderStoreRequest $request): JsonResponse
    {
        $orderData = $request->validated();
        $order = $orderService->createOrder($request->user(), $orderData);
        return response()->json($order, Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order, Request $request): JsonResponse
    {
        $order = $request->user()->orders()->find($order)->firstOrFail();
        return response()->json($order);
    }

    /**
     * Update the specified resource in storage.
     */
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
