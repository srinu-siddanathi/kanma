<?php

namespace App\Http\Controllers\Branch;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use App\Helpers\NotificationHelper;
use Illuminate\Http\Request;

class OrderAssignmentController extends Controller
{
    public function index()
    {
        $branch = auth()->user()->branch;
        $unassignedOrders = Order::where('branch_id', $branch->id)
            ->whereNull('delivery_boy_id')
            ->where('status', 'pending')
            ->with(['customer', 'items'])
            ->paginate(10);

        $deliveryBoys = $branch->deliveryBoys()
            ->withCount(['activeOrders' => function($query) {
                $query->whereIn('status', ['assigned', 'picked_up']);
            }])
            ->get();

        return view('branch.order-assignments.index', compact('unassignedOrders', 'deliveryBoys'));
    }

    public function assign(Request $request, Order $order)
    {
        $validated = $request->validate([
            'delivery_boy_id' => 'required|exists:users,id'
        ]);

        $deliveryBoy = User::findOrFail($validated['delivery_boy_id']);

        // Verify delivery boy belongs to this branch
        if ($deliveryBoy->branch_id !== auth()->user()->branch_id) {
            abort(403);
        }

        $oldStatus = $order->status;
        $order->update([
            'delivery_boy_id' => $deliveryBoy->id,
            'status' => 'assigned'
        ]);

        // Send delivery update notification
        if ($oldStatus !== 'assigned') {
            NotificationHelper::sendDeliveryUpdate($order->user_id, $order->id, 'assigned', $deliveryBoy->name);
            
            // Send notification to delivery boy about new order assignment
            NotificationHelper::sendOrderAssignmentToDeliveryBoy(
                $deliveryBoy->id,
                $order->id,
                $order->user->name,
                $order->delivery_address,
                $order->total_amount
            );
        }

        return redirect()
            ->back()
            ->with('success', 'Order assigned successfully');
    }
} 