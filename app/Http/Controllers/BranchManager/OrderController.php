<?php

namespace App\Http\Controllers\BranchManager;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function list()
    {
        $branch = auth()->user()->branch;
        $orders = $branch->orders()
            ->with(['user', 'items.product'])
            ->latest()
            ->paginate(10);

        return view('branch-manager.orders.index', compact('orders', 'branch'));
    }

    public function show(Order $order)
    {
        // Ensure the order belongs to the branch manager's branch
        if ($order->branch_id !== auth()->user()->branch_id) {
            abort(403);
        }

        return view('branch-manager.orders.show', [
            'order' => $order->load(['user', 'items.product']),
        ]);
    }

    public function updateStatus(Request $request, Order $order)
    {
        // Ensure the order belongs to the branch manager's branch
        if ($order->branch_id !== auth()->user()->branch_id) {
            abort(403);
        }

        $validated = $request->validate([
            'status' => 'required|in:pending,processing,completed,cancelled',
        ]);

        $order->update($validated);

        return back()->with('success', 'Order status updated successfully');
    }
} 