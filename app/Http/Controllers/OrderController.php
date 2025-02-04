<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'order_type' => 'required|in:regular,o2',
            'delivery_address' => 'required|string',
            'delivery_latitude' => 'required|numeric',
            'delivery_longitude' => 'required|numeric',
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string',
        ]);

        try {
            $order = DB::transaction(function () use ($request, $validated) {
                // Calculate total amount
                $total = 0;
                $items = collect($request->items)->map(function ($item) use (&$total) {
                    $product = Product::findOrFail($item['product_id']);
                    $subtotal = $product->price * $item['quantity'];
                    $total += $subtotal;
                    
                    return [
                        'product_id' => $product->id,
                        'quantity' => $item['quantity'],
                        'price' => $product->price,
                        'subtotal' => $subtotal,
                    ];
                });

                // Create order
                $order = Order::create([
                    'user_id' => $request->user()->id,
                    'branch_id' => $validated['branch_id'],
                    'order_type' => $validated['order_type'],
                    'delivery_address' => $validated['delivery_address'],
                    'delivery_latitude' => $validated['delivery_latitude'],
                    'delivery_longitude' => $validated['delivery_longitude'],
                    'total_amount' => $total,
                    'notes' => $validated['notes'] ?? null,
                    'status' => 'pending',
                ]);

                // Create order items
                $order->items()->createMany($items);

                return $order;
            });

            return response()->json([
                'message' => 'Order created successfully',
                'order' => $order->load(['items.product', 'branch']),
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create order',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function index(Request $request)
    {
        $orders = $request->user()->orders()
            ->with(['branch'])
            ->latest()
            ->paginate();

        return response()->json($orders);
    }

    public function show(Order $order)
    {
        $this->authorize('view', $order);

        return response()->json([
            'order' => $order->load(['branch', 'items.product']),
        ]);
    }
} 