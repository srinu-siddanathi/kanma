<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Setting;
use App\Models\Branch;
use App\Http\Controllers\Api\RazorpayController;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'delivery_address' => 'required|string',
            'delivery_latitude' => 'required|numeric',
            'delivery_longitude' => 'required|numeric',
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.variant_id' => 'nullable|exists:product_variants,id',
            'notes' => 'nullable|string',
            'payment_method' => 'required|in:cod,razorpay,wallet',
            'wallet_amount_used' => 'nullable|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            $user = auth()->user();
            
            // Calculate subtotal and prepare order items
            $subtotal = 0;
            $orderItems = [];
            
            foreach ($validated['items'] as $item) {
                $product = Product::with(['variants' => function($q) use ($item) {
                    if (isset($item['variant_id'])) {
                        $q->where('id', $item['variant_id']);
                    }
                }])->findOrFail($item['product_id']);
                
                $price = $product->price;
                
                // If variant is specified, use variant price
                if (isset($item['variant_id'])) {
                    $variant = $product->variants->first();
                    if ($variant) {
                        $price = $variant->price;
                    }
                }
                
                // Apply discount if any
                $discountedPrice = $product->discount > 0 
                    ? $price - ($price * ($product->discount / 100))
                    : $price;
                
                $itemTotal = $discountedPrice * $item['quantity'];
                $subtotal += $itemTotal;
                
                $orderItems[] = [
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $discountedPrice,
                    'subtotal' => $itemTotal
                ];
            }

            // Calculate delivery fee and small cart fee based on membership
            $deliveryFee = 50; // Default delivery fee
            $smallCartFee = 0; // Small cart fee
            $hasActiveMembership = false;
            $membershipDetails = null;

            // Check if user has active membership subscription
            $activeSubscription = $user->currentSubscription();
            $hasActiveMembership = $activeSubscription && $activeSubscription->status === 'active';
            
            if ($hasActiveMembership && $activeSubscription->plan) {
                $plan = $activeSubscription->plan;
                $membershipDetails = [
                    'plan_name' => $plan->name,
                    'plan_type' => $plan->type,
                    'free_orders' => $plan->free_orders,
                    'free_delivery_radius' => $plan->free_delivery_radius,
                    'wallet_addon' => $plan->wallet_addon
                ];
                
                // Check if user has free orders remaining
                if ($plan->free_orders > 0) {
                    $freeOrdersUsed = $user->orders()
                        ->where('created_at', '>=', $activeSubscription->starts_at)
                        ->where('created_at', '<=', $activeSubscription->ends_at)
                        ->count();
                    
                    if ($freeOrdersUsed < $plan->free_orders) {
                        $deliveryFee = 0; // Free delivery if free orders are available
                    }
                }
                
                // Check if delivery is within free delivery radius
                if ($plan->free_delivery_radius > 0) {
                    $branch = Branch::find($validated['branch_id']);
                    
                    if ($branch && $branch->latitude && $branch->longitude) {
                        $distance = $this->calculateDistance(
                            $branch->latitude,
                            $branch->longitude,
                            $validated['delivery_latitude'],
                            $validated['delivery_longitude']
                        );
                        
                        if ($distance <= $plan->free_delivery_radius) {
                            $deliveryFee = 0; // Free delivery within radius
                        }
                    }
                }
            }

            // Calculate small cart fee if order is below minimum amount
            $minimumOrderAmount = Setting::get('minimum_order_amount', 100);
            $smallCartFeeAmount = Setting::get('small_cart_fee', 10);
            
            // Only apply small cart fee if user doesn't have active membership
            if (!$hasActiveMembership && $subtotal < $minimumOrderAmount) {
                $smallCartFee = $smallCartFeeAmount;
            }

            // Handle wallet payment
            $walletAmountUsed = 0;
            if ($validated['payment_method'] === 'wallet') {
                $walletAmountUsed = $validated['wallet_amount_used'] ?? 0;
                
                if ($walletAmountUsed > $user->wallet_balance) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Insufficient wallet balance'
                    ], 400);
                }
                
                // Deduct from wallet
                $user->decrement('wallet_balance', $walletAmountUsed);
            }

            $totalAmount = $subtotal + $deliveryFee + $smallCartFee - $walletAmountUsed;

            // Create order
            $order = Order::create([
                'user_id' => $user->id,
                'branch_id' => $validated['branch_id'],
                'status' => 'pending',
                'delivery_address' => $validated['delivery_address'],
                'delivery_latitude' => $validated['delivery_latitude'],
                'delivery_longitude' => $validated['delivery_longitude'],
                'notes' => $validated['notes'] ?? null,
                'total_amount' => $totalAmount,
                'delivery_fee' => $deliveryFee + $smallCartFee,
                'wallet_amount_used' => $walletAmountUsed,
                'payment_method' => $validated['payment_method'],
                'payment_status' => $validated['payment_method'] === 'cod' ? 'pending' : 'completed',
            ]);

            // Create order items
            foreach ($orderItems as $item) {
                $order->items()->create([
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'subtotal' => $item['subtotal']
                ]);
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Order created successfully',
                'data' => [
                    'order' => $order->load('items.product', 'branch'),
                    'breakdown' => [
                        'subtotal' => round($subtotal, 2),
                        'delivery_fee' => round($deliveryFee, 2),
                        'small_cart_fee' => round($smallCartFee, 2),
                        'wallet_amount_used' => round($walletAmountUsed, 2),
                        'total' => round($totalAmount, 2)
                    ],
                    'membership_info' => [
                        'has_active_membership' => $hasActiveMembership,
                        'membership_details' => $membershipDetails
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Error creating order', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Error creating order: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get logged in user's orders
     */
    public function userOrders(Request $request): JsonResponse
    {
        $orders = $request->user()
            ->orders()
            ->with(['items.product', 'branch'])
            ->latest()
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $orders
        ]);
    }

    /**
     * Get specific order details
     */
    public function show(Order $order): JsonResponse
    {
        // Ensure user can only view their own orders
        if ($order->user_id !== auth()->id()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized access to order'
            ], 403);
        }

        $order->load(['items.product', 'branch']);

        return response()->json([
            'status' => 'success',
            'data' => $order
        ]);
    }

    public function cancel(Order $order): JsonResponse
    {
        // Ensure user can only cancel their own orders
        if ($order->user_id !== auth()->id()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized access to order'
            ], 403);
        }

        // Check if order can be cancelled
        if ($order->status === 'cancelled') {
            return response()->json([
                'status' => 'error',
                'message' => 'Order is already cancelled'
            ], 400);
        }

        if ($order->status === 'completed') {
            return response()->json([
                'status' => 'error',
                'message' => 'Cannot cancel completed order'
            ], 400);
        }

        if ($order->status === 'processing') {
            return response()->json([
                'status' => 'error',
                'message' => 'Cannot cancel order that is being processed'
            ], 400);
        }

        try {
            DB::beginTransaction();

            // Update order status to cancelled
            $order->update(['status' => 'cancelled']);

            // Refund wallet amount if wallet was used
            if ($order->wallet_amount_used > 0) {
                $user = auth()->user();
                $user->increment('wallet_balance', $order->wallet_amount_used);
                
                Log::info('Wallet refund processed for cancelled order', [
                    'order_id' => $order->id,
                    'user_id' => $user->id,
                    'refund_amount' => $order->wallet_amount_used
                ]);
            }

            // If payment was made online, initiate refund process
            $refundInfo = [
                'wallet_refunded' => $order->wallet_amount_used > 0,
                'wallet_amount' => $order->wallet_amount_used,
                'online_refund_required' => $order->payment_method === 'razorpay' && $order->payment_status === 'completed',
                'online_refund_processed' => false,
                'online_refund_details' => null
            ];

            if ($order->payment_method === 'razorpay' && $order->payment_status === 'completed') {
                $razorpayController = new RazorpayController();
                $refundResult = $razorpayController->processRefund($order);
                
                if ($refundResult['success']) {
                    $refundInfo['online_refund_processed'] = true;
                    $refundInfo['online_refund_details'] = $refundResult['data'];
                    
                    Log::info('Razorpay refund processed successfully for cancelled order', [
                        'order_id' => $order->id,
                        'refund_id' => $refundResult['data']['refund_id'],
                        'refund_amount' => $refundResult['data']['refund_amount']
                    ]);
                } else {
                    Log::error('Razorpay refund failed for cancelled order', [
                        'order_id' => $order->id,
                        'error' => $refundResult['message']
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Order cancelled successfully',
                'data' => [
                    'order' => $order->fresh()->load('items.product', 'branch'),
                    'refund_info' => $refundInfo
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error cancelling order', [
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to cancel order. Please try again.'
            ], 500);
        }
    }

    /**
     * Calculate distance between two points using Haversine formula
     */
    private function calculateDistance($lat1, $lon1, $lat2, $lon2): float
    {
        $earthRadius = 6371; // Earth's radius in kilometers

        $latDelta = deg2rad($lat2 - $lat1);
        $lonDelta = deg2rad($lon2 - $lon1);

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($lonDelta / 2) * sin($lonDelta / 2);
        
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        
        return $earthRadius * $c;
    }
}