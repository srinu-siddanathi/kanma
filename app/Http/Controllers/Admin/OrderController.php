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
        $query = Order::with(['user', 'branch', 'items.product']);

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
        $order->load(['user', 'branch', 'items.product', 'coupon']);
        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,processing,ready,out_for_delivery,delivered,completed,cancelled,failed',
        ]);

        $oldStatus = $order->status;
        $order->update([
            'status' => $validated['status'],
        ]);

        // Send notification to user about order status update
        if ($oldStatus !== $validated['status']) {
            NotificationHelper::sendOrderStatusUpdate($order->user_id, $order->id, $validated['status']);

            // Send confirmation emails when order is confirmed
            if ($validated['status'] === 'confirmed') {
                \App\Services\OrderEmailService::sendOrderConfirmationEmails($order);
            }

            // Send SMS to user
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
            
            // Send delivery-specific notification for delivery statuses
            if (in_array($validated['status'], ['out_for_delivery', 'delivered'])) {
                $deliveryBoyName = null;
                if ($order->delivery_boy_id) {
                    $deliveryBoy = User::find($order->delivery_boy_id);
                    $deliveryBoyName = $deliveryBoy ? $deliveryBoy->name : null;
                }
                NotificationHelper::sendDeliveryUpdate($order->user_id, $order->id, $validated['status'], $deliveryBoyName);
            }
        }

        return back()->with('success', 'Order status updated successfully');
    }
} 