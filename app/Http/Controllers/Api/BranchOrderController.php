<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use App\Helpers\NotificationHelper;
use App\Services\Msg91Service;
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
            'status' => 'required|in:pending,confirmed,processing,ready,out_for_delivery,delivered,completed,cancelled,failed'
        ]);

        if ($order->branch_id !== auth()->user()->branch_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $oldStatus = $order->status;
        $order->update(['status' => $validated['status']]);

        // Send notification to user about order status update
        if ($oldStatus !== $validated['status']) {
            NotificationHelper::sendOrderStatusUpdate($order->user_id, $order->id, $validated['status']);
            
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

        return response()->json([
            'data' => $order->fresh(),
            'message' => 'Order status updated successfully'
        ]);
    }
} 