<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use App\Helpers\NotificationHelper;
use App\Services\Msg91Service;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['user', 'branch', 'shop', 'items.product']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                  ->orWhereHas('user', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('order_type')) {
            $query->where('order_type', $request->order_type);
        }

        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $orders = $query->latest()->paginate(10);
        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $order->load(['user', 'branch', 'shop', 'items.product', 'coupon']);
        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,processing,ready,out_for_delivery,delivered,completed,cancelled,failed',
        ]);

        $oldStatus = $order->status;
        $requestId = uniqid('order_status_', true);
        
        \Log::info('Admin updating order status', [
            'request_id' => $requestId,
            'order_id' => $order->id,
            'old_status' => $oldStatus,
            'new_status' => $validated['status'],
            'user_id' => auth()->id(),
            'request_data' => $request->all()
        ]);
        
        // Prepare update data
        $updateData = [
            'status' => $validated['status'],
        ];
        
        // Auto-update payment status for COD orders when completed
        if ($validated['status'] === 'completed' && $order->payment_method === 'cod') {
            $updateData['payment_status'] = 'paid';
        }
        
        $order->update($updateData);

        \Log::info('Order status updated in database', [
            'request_id' => $requestId,
            'order_id' => $order->id,
            'old_status' => $oldStatus,
            'new_status' => $validated['status'],
            'status_changed' => $oldStatus !== $validated['status']
        ]);

        // Send notification to user about order status update
        if ($oldStatus !== $validated['status']) {
            \Log::info('Sending notifications for status change', [
                'request_id' => $requestId,
                'order_id' => $order->id,
                'old_status' => $oldStatus,
                'new_status' => $validated['status']
            ]);

            NotificationHelper::sendOrderStatusUpdate($order->user_id, $order->id, $validated['status']);

            // Send confirmation emails when order is confirmed
            if ($validated['status'] === 'confirmed') {
                \Log::info('Sending confirmation emails', [
                    'request_id' => $requestId,
                    'order_id' => $order->id,
                    'status' => $validated['status']
                ]);
                \App\Services\OrderEmailService::sendOrderConfirmationEmails($order);
            }

            // Send SMS to user
            try {
                $msg91 = new Msg91Service();
                $userPhone = $order->user->phone;
                $phoneWithCountry = '91' . preg_replace('/^\+?91?/', '', $userPhone);
                \Log::info('Attempting to send order status SMS', [
                    'request_id' => $requestId,
                    'order_id' => $order->id,
                    'user_id' => $order->user_id,
                    'phone' => $phoneWithCountry,
                    'status' => $validated['status'],
                    'old_status' => $oldStatus,
                    'new_status' => $validated['status'],
                    'timestamp' => now()->toISOString()
                ]);
                
                $smsResult = $msg91->sendOrderStatusSms($phoneWithCountry, (string)$order->id, $validated['status'], optional($order->shop)->name);
                \Log::info('Order status SMS result', [
                    'request_id' => $requestId,
                    'order_id' => $order->id,
                    'success' => $smsResult['success'] ?? null,
                    'message' => $smsResult['message'] ?? null,
                ]);
            } catch (\Throwable $e) {
                \Log::error('Failed to send order status SMS', [
                    'request_id' => $requestId,
                    'order_id' => $order->id,
                    'user_id' => $order->user_id,
                    'status' => $validated['status'],
                    'error' => $e->getMessage(),
                ]);
            }
            
            // Send delivery-specific notification for delivery statuses
            if (in_array($validated['status'], ['out_for_delivery', 'delivered'])) {
                $deliveryBoyName = null;
                if ($order->delivery_boy_id) {
                    $deliveryBoy = User::find($order->delivery_boy_id);
                    $deliveryBoyName = $deliveryBoy ? $deliveryBoy->name : null;
                }
                NotificationHelper::sendDeliveryUpdate($order->user_id, $order->id, $validated['status'], $deliveryBoyName);
            }
        } else {
            \Log::info('No status change detected, skipping notifications', [
                'request_id' => $requestId,
                'order_id' => $order->id,
                'old_status' => $oldStatus,
                'new_status' => $validated['status']
            ]);
        }

        return back()->with('success', 'Order status updated successfully');
    }
} 