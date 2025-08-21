<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ChatOrder;
use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ChatOrderController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $user = Auth::user();
        $orders = ChatOrder::with(['shop', 'branch', 'messages'])
            ->where('user_id', $user->id)
            ->latest()
            ->paginate(10);

        return response()->json([
            'status' => 'success',
            'data' => $orders
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'branch_id' => 'required|exists:branches,id',
            'notes' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $order = ChatOrder::create([
            'user_id' => Auth::id(),
            'branch_id' => $request->branch_id,
            'name' => $request->name,
            'notes' => $request->notes,
            'status' => 'pending'
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Chat order created successfully',
            'data' => $order
        ], 201);
    }

    public function show(ChatOrder $chatOrder)
    {
        $this->authorize('view', $chatOrder);

        $chatOrder->load(['shop', 'branch', 'messages.sender']);

        return response()->json([
            'status' => 'success',
            'data' => $chatOrder
        ]);
    }

    public function update(Request $request, ChatOrder $chatOrder)
    {
        $this->authorize('update', $chatOrder);

        $validator = Validator::make($request->all(), [
            'scheduled_at' => 'nullable|date|after:now',
            'notes' => 'nullable|string',
            'status' => 'nullable|in:pending,confirmed,preparing,ready,delivered,cancelled'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $chatOrder->update($request->only(['scheduled_at', 'notes', 'status']));

        return response()->json([
            'status' => 'success',
            'message' => 'Chat order updated successfully',
            'data' => $chatOrder
        ]);
    }

    public function destroy(ChatOrder $chatOrder)
    {
        $this->authorize('delete', $chatOrder);

        $chatOrder->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Chat order deleted successfully'
        ]);
    }

    public function getAvailableSlots(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'shop_id' => 'required|exists:shops,id',
            'date' => 'required|date|after_or_equal:today'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $shop = Shop::findOrFail($request->shop_id);
        $date = $request->date;
        $dayOfWeek = strtolower(date('l', strtotime($date)));

        // Check if shop is open on this day
        if (!isset($shop->working_hours[$dayOfWeek])) {
            return response()->json([
                'status' => 'error',
                'message' => 'Shop is closed on ' . $dayOfWeek
            ], 422);
        }

        $workingHours = $shop->working_hours[$dayOfWeek];
        $startTime = strtotime($workingHours['open']);
        $endTime = strtotime($workingHours['close']);

        // Get booked slots for the date
        $bookedSlots = ChatOrder::where('shop_id', $shop->id)
            ->whereDate('scheduled_at', $date)
            ->pluck('scheduled_at')
            ->map(function ($slot) {
                return $slot->format('H:i');
            });

        // Generate available slots (30-minute intervals)
        $availableSlots = [];
        for ($time = $startTime; $time < $endTime; $time += 1800) {
            $slot = date('H:i', $time);
            if (!in_array($slot, $bookedSlots->toArray())) {
                $availableSlots[] = $slot;
            }
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'date' => $date,
                'available_slots' => $availableSlots,
                'working_hours' => $workingHours
            ]
        ]);
    }

    public function schedule(Request $request, ChatOrder $chatOrder)
    {
        $this->authorize('update', $chatOrder);

        $validator = Validator::make($request->all(), [
            'scheduled_at' => 'required|date|after:now'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $chatOrder->update([
            'scheduled_at' => $request->scheduled_at
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Order scheduled successfully',
            'data' => $chatOrder
        ]);
    }
} 