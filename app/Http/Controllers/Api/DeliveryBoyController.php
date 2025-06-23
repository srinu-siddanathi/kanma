<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;
use App\Models\User;
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
        $request->validate([
            'status' => ['required', Rule::in(['pending', 'processing', 'completed'])],
        ]);

        $deliveryBoy = Auth::user();
        $status = $request->status;

        $query = Order::where('delivery_boy_id', $deliveryBoy->id);

        if ($status === 'pending') {
            $query->whereIn('status', ['pending', 'confirmed', 'assigned', 'processing']);
        } elseif ($status === 'processing') {
            $query->whereIn('status', ['shipped', 'out_for_delivery']);
        } elseif ($status === 'completed') {
            $query->whereIn('status', ['delivered', 'completed', 'cancelled']);
        }

        $orders = $query->with('items.product', 'user', 'shop')->latest()->paginate(10);

        return response()->json($orders);
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

        $order->status = $request->status;
        if ($request->has('delivery_notes')) {
            $order->delivery_notes = $request->delivery_notes;
        }
        $order->save();

        // TODO: Add logic for partially delivered orders, e.g., which items.
        // TODO: Fire events for order status updates.

        return response()->json(['message' => 'Order status updated successfully.', 'order' => $order]);
    }
}
