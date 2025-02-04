<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;

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

    public function index()
    {
        $user = auth()->user();
        $query = Order::with(['items.product', 'branch', 'user']);

        // If user is admin, show all orders, otherwise show only user's orders
        if (!$user->isAdmin()) {
            $query->where('user_id', $user->id);
        }

        $orders = $query->latest()->paginate(10);

        return response()->json([
            'data' => $orders,
            'message' => 'Orders retrieved successfully'
        ]);
    }

    public function show(Order $order)
    {
        $user = auth()->user();
        
        // Allow admin to view any order, but regular users can only view their own orders
        if (!$user->isAdmin() && $order->user_id !== $user->id) {
            return response()->json([
                'message' => 'You are not authorized to view this order'
            ], 403);
        }

        return response()->json([
            'data' => $order->load(['items.product', 'branch', 'user']),
            'message' => 'Order retrieved successfully'
        ]);
    }
} 