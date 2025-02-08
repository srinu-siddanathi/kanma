<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'delivery_address' => 'required|string',
            'delivery_latitude' => 'required|numeric',
            'delivery_longitude' => 'required|numeric',
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string',
        ]);

        $order = Order::create([
            'user_id' => auth()->id(),
            'branch_id' => $validated['branch_id'],
            'status' => 'pending',
            'delivery_address' => $validated['delivery_address'],
            'delivery_latitude' => $validated['delivery_latitude'],
            'delivery_longitude' => $validated['delivery_longitude'],
            'notes' => $validated['notes'] ?? null,
            'total_amount' => 0,
        ]);

        $total = 0;
        foreach ($validated['items'] as $item) {
            $product = Product::find($item['product_id']);
            $itemTotal = $product->price * $item['quantity'];
            $total += $itemTotal;
            
            $order->items()->create([
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'price' => $product->price,
                'subtotal' => $itemTotal
            ]);
        }

        $order->total_amount = $total;
        $order->save();

        return response()->json([
            'message' => 'Order created successfully',
            'data' => $order->load('items.product', 'branch')
        ]);
    }

    /**
     * Get logged in user's orders
     */
    public function userOrders(Request $request): JsonResponse
    {
        $orders = $request->user()
            ->orders()
            ->with(['items.product', 'branch'])
            ->latest()
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $orders
        ]);
    }

    /**
     * Get specific order details
     */
    public function show(Order $order): JsonResponse
    {
        // Check if order belongs to logged in user
        if ($order->user_id !== auth()->id()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized access to order'
            ], 403);
        }

        $order->load(['items.product', 'branch']);

        return response()->json([
            'status' => 'success',
            'data' => $order
        ]);
    }
} 