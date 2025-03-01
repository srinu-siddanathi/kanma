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

    public function history()
    {
        $branch = auth()->user()->branch;
        
        // Get the query builder before executing
        $query = Order::where('branch_id', $branch->id)
            ->with(['user', 'deliveryBoy', 'items.product'])
            ->latest();

        // Debug logging
        \Log::info('Branch ID: ' . $branch->id);
        \Log::info('Orders query:', [
            'sql' => $query->toSql(),
            'bindings' => $query->getBindings()
        ]);

        // Execute the query
        $orders = $query->paginate(10);
        \Log::info('Orders count: ' . $orders->count());

        return view('branch.orders.history', compact('orders'));
    }

    public function details(Order $order)
    {
        if ($order->branch_id !== auth()->user()->branch_id) {
            abort(403);
        }

        return view('branch.orders.details', compact('order'));
    }
} 