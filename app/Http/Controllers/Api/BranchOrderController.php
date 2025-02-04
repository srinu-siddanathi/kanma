<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class BranchOrderController extends Controller
{
    public function index()
    {
        $branch = auth()->user()->branch;
        
        $orders = Order::with(['user', 'items.product'])
            ->where('branch_id', $branch->id)
            ->latest()
            ->paginate(10);

        return response()->json([
            'data' => $orders,
            'message' => 'Branch orders retrieved successfully'
        ]);
    }

    public function updateStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,processing,completed,cancelled'
        ]);

        if ($order->branch_id !== auth()->user()->branch_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $order->update(['status' => $validated['status']]);

        return response()->json([
            'data' => $order->fresh(),
            'message' => 'Order status updated successfully'
        ]);
    }
} 