<?php

namespace App\Http\Controllers\ShopOwner;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        $orders = auth()->user()->shop->orders()
            ->with(['user', 'items.product'])
            ->latest()
            ->paginate(10);

        return view('shop-owner.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        if ($order->shop_id !== auth()->user()->shop->id) {
            abort(403);
        }

        $order->load(['user', 'items.product']);
        return view('shop-owner.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        if ($order->shop_id !== auth()->user()->shop->id) {
            abort(403);
        }

        $validated = $request->validate([
            'status' => 'required|in:pending,processing,completed,cancelled'
        ]);

        $order->update($validated);

        return redirect()
            ->back()
            ->with('success', 'Order status updated successfully');
    }
} 