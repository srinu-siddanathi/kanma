<?php

namespace App\Http\Controllers\BranchManager;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Helpers\NotificationHelper;
use App\Services\Msg91Service;
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
            'status' => 'required|in:pending,confirmed,processing,ready,out_for_delivery,delivered,completed,cancelled,failed',
        ]);

        $oldStatus = $order->status;
        $order->update($validated);

        if ($oldStatus !== $validated['status']) {
            // Push notification
            NotificationHelper::sendOrderStatusUpdate($order->user_id, $order->id, $validated['status']);

            // Send confirmation emails when order is confirmed
            if ($validated['status'] === 'confirmed') {
                \App\Services\OrderEmailService::sendOrderConfirmationEmails($order);
            }

            // SMS notification
            try {
                $msg91 = new Msg91Service();
                $userPhone = $order->user->phone;
                $phoneWithCountry = '91' . preg_replace('/^\+?91?/', '', $userPhone);
                \Log::info('Attempting to send order status SMS', [
                    'order_id' => $order->id,
                    'user_id' => $order->user_id,
                    'phone' => $phoneWithCountry,
                    'status' => $validated['status'],
                ]);
                $smsResult = $msg91->sendOrderStatusSms($phoneWithCountry, (string)$order->id, $validated['status'], optional($order->shop)->name);
                \Log::info('Order status SMS result', [
                    'order_id' => $order->id,
                    'success' => $smsResult['success'] ?? null,
                    'message' => $smsResult['message'] ?? null,
                ]);
            } catch (\Throwable $e) {
                \Log::error('Failed to send order status SMS', [
                    'order_id' => $order->id,
                    'user_id' => $order->user_id,
                    'status' => $validated['status'],
                    'error' => $e->getMessage(),
                ]);
            }
        }

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