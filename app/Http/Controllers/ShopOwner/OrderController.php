<?php

namespace App\Http\Controllers\ShopOwner;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use App\Helpers\NotificationHelper;
use App\Services\Msg91Service;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        $orders = auth()->user()->shop->orders()
            ->with(['user', 'items.product.images'])
            ->latest()
            ->paginate(10);

        return view('shop-owner.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        if ($order->shop_id !== auth()->user()->shop->id) {
            abort(403);
        }

        $order->load(['user', 'items.product.images']);
        return view('shop-owner.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        if ($order->shop_id !== auth()->user()->shop->id) {
            abort(403);
        }

        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,processing,ready,out_for_delivery,delivered,completed,cancelled,failed'
        ]);

        $oldStatus = $order->status;
        
        // Prepare update data
        $updateData = [
            'status' => $validated['status'],
        ];
        
        // Auto-update payment status for COD orders when completed
        if ($validated['status'] === 'completed' && $order->payment_method === 'cod') {
            $updateData['payment_status'] = 'paid';
        }
        
        $order->update($updateData);

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

        return redirect()
            ->back()
            ->with('success', 'Order status updated successfully');
    }
} 