<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;
use App\Models\User;
use App\Helpers\NotificationHelper;
use App\Services\Msg91Service;
use Carbon\Carbon;
use Illuminate\Validation\Rule;

class DeliveryBoyController extends Controller
{
    /**
     * Toggle the working status of the delivery boy.
     */
    public function toggleWorkingStatus(Request $request)
    {
        $request->validate([
            'is_working_today' => 'required|boolean',
        ]);

        $deliveryBoy = Auth::user();
        $deliveryBoy->is_working_today = $request->is_working_today;
        $deliveryBoy->last_status_update = Carbon::now();
        $deliveryBoy->save();

        return response()->json(['message' => 'Working status updated successfully.', 'status' => $deliveryBoy->is_working_today]);
    }

    /**
     * Get the orders assigned to the delivery boy.
     */
    public function getOrders(Request $request)
    {
        $deliveryBoy = Auth::user();

        $query = Order::where('delivery_boy_id', $deliveryBoy->id);

        // Get all orders with relationships
        $allOrders = $query->with('items.product', 'user', 'shop')->latest()->get();

        // Group orders by status
        $groupedOrders = [
            'pending' => $allOrders->filter(function ($order) {
                return in_array($order->status, ['pending', 'confirmed', 'assigned', 'processing']);
            })->values(),
            'processing' => $allOrders->filter(function ($order) {
                return in_array($order->status, ['shipped', 'out_for_delivery']);
            })->values(),
            'completed' => $allOrders->filter(function ($order) {
                return in_array($order->status, ['delivered', 'completed', 'cancelled']);
            })->values(),
        ];

        // Add counts for each status
        $response = [
            'data' => $groupedOrders,
            'counts' => [
                'pending' => $groupedOrders['pending']->count(),
                'processing' => $groupedOrders['processing']->count(),
                'completed' => $groupedOrders['completed']->count(),
                'total' => $allOrders->count(),
            ],
            'message' => 'Orders retrieved successfully'
        ];

        return response()->json($response);
    }

    /**
     * Get the details of a specific order.
     */
    public function getOrderDetails(Order $order)
    {
        $deliveryBoy = Auth::user();

        if ($order->delivery_boy_id != $deliveryBoy->id) {
            return response()->json(['message' => 'This order is not assigned to you.'], 403);
        }

        $order->load('items.product', 'user', 'shop', 'address');

        return response()->json($order);
    }

    /**
     * Update the status of an order.
     */
    public function updateOrderStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => ['required', Rule::in(['delivered', 'partially_delivered', 'cancelled'])],
            'delivery_notes' => 'nullable|string|max:255',
        ]);

        $deliveryBoy = Auth::user();

        if ($order->delivery_boy_id != $deliveryBoy->id) {
            return response()->json(['message' => 'This order is not assigned to you.'], 403);
        }

        $oldStatus = $order->status;
        $order->status = $request->status;
        if ($request->has('delivery_notes')) {
            $order->delivery_notes = $request->delivery_notes;
        }
        $order->save();

        // Send notification to user about order status update
        if ($oldStatus !== $request->status) {
            NotificationHelper::sendOrderStatusUpdate($order->user_id, $order->id, $request->status);
            
            // Send delivery-specific notification for delivery statuses
            if (in_array($request->status, ['delivered', 'partially_delivered'])) {
                $deliveryBoyName = $deliveryBoy->name;
                NotificationHelper::sendDeliveryUpdate($order->user_id, $order->id, $request->status, $deliveryBoyName);
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
                    'status' => $request->status,
                ]);
                $smsResult = $msg91->sendOrderStatusSms($phoneWithCountry, (string)$order->id, $request->status, optional($order->shop)->name);
                \Log::info('Order status SMS result', [
                    'order_id' => $order->id,
                    'success' => $smsResult['success'] ?? null,
                    'message' => $smsResult['message'] ?? null,
                ]);
            } catch (\Throwable $e) {
                \Log::error('Failed to send order status SMS', [
                    'order_id' => $order->id,
                    'user_id' => $order->user_id,
                    'status' => $request->status,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        // TODO: Add logic for partially delivered orders, e.g., which items.

        return response()->json(['message' => 'Order status updated successfully.', 'order' => $order]);
    }
}
