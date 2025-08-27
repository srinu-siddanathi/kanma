<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Setting;
use App\Models\Branch;
use App\Http\Controllers\Api\RazorpayController;
use App\Helpers\NotificationHelper;
use App\Services\OrderEmailService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        // Debug logging to see if this method is being called
        \Log::info('API OrderController store method called', [
            'request_data' => $request->all(),
            'headers' => $request->headers->all(),
            'user_authenticated' => auth()->check(),
            'user_id' => auth()->id()
        ]);

        $validated = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'shop_id' => 'nullable|exists:shops,id',
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
            'coupon_id' => 'nullable|integer|min:0',
        ]);

        // Ensure coupon_id is set (default to null if not provided)
        if (!isset($validated['coupon_id'])) {
            $validated['coupon_id'] = null;
        }
        
        // Handle coupon_id = 0 as null (no coupon)
        if ($validated['coupon_id'] == 0) {
            $validated['coupon_id'] = null;
        }

        // Validate coupon_id exists if it's not null
        if ($validated['coupon_id'] !== null) {
            $request->validate([
                'coupon_id' => 'exists:coupons,id'
            ]);
        }

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

            // Calculate coupon discount if coupon_id is provided
            $couponDiscount = 0;
            $couponDetails = null;
            
            if (isset($validated['coupon_id'])) {
                $coupon = \App\Models\Coupon::find($validated['coupon_id']);
                if ($coupon && $coupon->status === 'active') {
                    // Check if coupon is expired
                    if (!$coupon->expires_at || now()->isBefore($coupon->expires_at)) {
                        // Check minimum order amount
                        if ($subtotal >= $coupon->min_order_amount) {
                            // Check usage limits
                            $userUsageCount = \App\Models\CouponUsage::where('coupon_id', $coupon->id)
                                ->where('user_id', $user->id)
                                ->where('status', 'confirmed')
                                ->count();

                            $totalUsageCount = \App\Models\CouponUsage::where('coupon_id', $coupon->id)
                                ->where('status', 'confirmed')
                                ->count();

                            if ($userUsageCount < $coupon->usage_limit_per_user && 
                                ($coupon->usage_limit === null || $totalUsageCount < $coupon->usage_limit)) {
                                
                                // Calculate coupon discount
                                if ($coupon->discount_type === 'percentage') {
                                    $couponDiscount = ($subtotal * $coupon->discount_value) / 100;
                                    // Apply maximum discount limit if set
                                    if ($coupon->max_discount_amount) {
                                        $couponDiscount = min($couponDiscount, $coupon->max_discount_amount);
                                    }
                                } else {
                                    $couponDiscount = min($coupon->discount_value, $subtotal);
                                }
                                
                                $couponDiscount = round($couponDiscount, 2);
                                
                                $couponDetails = [
                                    'id' => $coupon->id,
                                    'code' => $coupon->code,
                                    'title' => $coupon->title,
                                    'discount_type' => $coupon->discount_type,
                                    'discount_value' => $coupon->discount_value,
                                    'discount_amount' => $couponDiscount
                                ];
                            }
                        }
                    }
                }
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
                
                $hasFreeDelivery = false;
                
                // Check if user has free orders remaining
                if ($plan->free_orders > 0) {
                    $freeOrdersUsed = $user->orders()
                        ->where('created_at', '>=', $activeSubscription->starts_at)
                        ->where('created_at', '<=', $activeSubscription->ends_at)
                        ->count();
                    if ($freeOrdersUsed < $plan->free_orders) {
                        $hasFreeDelivery = true; // Free delivery if free orders are available
                    }
                }
                
                // If no free orders remaining, check if delivery is within free delivery radius
                if (!$hasFreeDelivery && $plan->free_delivery_radius > 0) {
                    // Get branch location for distance calculation
                    $branch = Branch::find($validated['branch_id']);
                    
                    if ($branch && $branch->latitude && $branch->longitude) {
                        $distance = $this->calculateDistance(
                            $branch->latitude,
                            $branch->longitude,
                            $validated['delivery_latitude'],
                            $validated['delivery_longitude']
                        );
                        if ($distance <= $plan->free_delivery_radius) {
                            $hasFreeDelivery = true; // Free delivery within radius
                        }
                    }
                }
                
                // Apply free delivery if any condition is met
                if ($hasFreeDelivery) {
                    $deliveryFee = 0;
                }
            }

            // Calculate small cart fee if order is below minimum amount
            $minimumOrderAmount = Setting::get('minimum_order_amount', 100);
            $smallCartFeeAmount = Setting::get('small_cart_fee', 10);
            
            // Only apply small cart fee if user doesn't have active membership
            if (!$hasActiveMembership && $subtotal < $minimumOrderAmount) {
                $smallCartFee = $smallCartFeeAmount;
            }

            // Calculate wallet amount used
            $walletAmountUsed = $validated['wallet_amount_used'] ?? 0;
            
            // Calculate final total after coupon discount
            $totalAfterCoupon = $subtotal - $couponDiscount;
            $orderTotal = $totalAfterCoupon + $deliveryFee + $smallCartFee;
            
            // Validate wallet amount doesn't exceed order total
            if ($walletAmountUsed > $orderTotal) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Wallet amount cannot exceed order total. Order total: ' . $orderTotal . ', Wallet amount: ' . $walletAmountUsed
                ], 400);
            }
            
            // Validate wallet balance if wallet amount is being used
            if ($walletAmountUsed > 0) {
                if ($walletAmountUsed > $user->wallet_balance) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Insufficient wallet balance. Available: ' . $user->wallet_balance . ', Required: ' . $walletAmountUsed
                    ], 400);
                }
            }

            // Calculate final total after wallet deduction
            $totalAmount = $orderTotal - $walletAmountUsed;

            // Create order
            $order = Order::create([
                'user_id' => $user->id,
                'branch_id' => $validated['branch_id'],
                'shop_id' => $validated['shop_id'] ?? null,
                'status' => 'pending',
                'delivery_address' => $validated['delivery_address'],
                'delivery_latitude' => $validated['delivery_latitude'],
                'delivery_longitude' => $validated['delivery_longitude'],
                'notes' => $validated['notes'] ?? null,
                'total_amount' => $totalAmount,
                'delivery_fee' => $deliveryFee + $smallCartFee,
                'wallet_amount_used' => $walletAmountUsed,
                'payment_method' => $validated['payment_method'],
                'payment_status' => 'pending',
                'coupon_id' => $validated['coupon_id'] ?? null,
            ]);

            // Deduct wallet amount and create transaction if wallet is used
            if ($walletAmountUsed > 0) {
                try {
                    $user->deductFromWallet(
                        $walletAmountUsed,
                        "Payment for order #{$order->id}",
                        'App\\Models\\Order',
                        $order->id,
                        [
                            'order_id' => $order->id,
                            'payment_method' => $validated['payment_method']
                        ]
                    );
                    
                    // If entire order amount is paid by wallet, automatically confirm the order
                    if ($walletAmountUsed >= $orderTotal) {
                        $order->update([
                            'status' => 'confirmed',
                            'payment_status' => 'paid'
                        ]);
                        
                        // Send notifications for confirmed order
                        NotificationHelper::sendOrderStatusUpdate($order->user_id, $order->id, 'confirmed');
                        
                        // Send confirmation emails
                        \App\Services\OrderEmailService::sendOrderConfirmationEmails($order);
                    }
                } catch (\Exception $e) {
                    // If wallet deduction fails, rollback the transaction
                    DB::rollBack();
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Failed to deduct wallet amount: ' . $e->getMessage()
                    ], 400);
                }
            }

            // Create order items
            foreach ($orderItems as $item) {
                $order->items()->create([
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'subtotal' => $item['subtotal']
                ]);
            }

            // Clear the user's cart
            $cartKey = 'cart_' . $user->id;
            Cache::forget($cartKey);

            // Send immediate emails for COD orders
            if ($validated['payment_method'] === 'cod') {
                \App\Services\OrderPlacementEmailService::sendCodOrderPlacementEmails($order);
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Order created successfully',
                'data' => [
                    'order' => $order->load('items.product', 'branch', 'shop', 'coupon'),
                    'breakdown' => [
                        'subtotal' => round($subtotal, 2),
                        'coupon_discount' => round($couponDiscount, 2),
                        'total_after_coupon' => round($totalAfterCoupon, 2),
                        'delivery_fee' => round($deliveryFee, 2),
                        'small_cart_fee' => round($smallCartFee, 2),
                        'order_total_before_wallet' => round($orderTotal, 2),
                        'wallet_amount_used' => round($walletAmountUsed, 2),
                        'total' => round($totalAmount, 2)
                    ],
                    'membership_info' => [
                        'has_active_membership' => $hasActiveMembership,
                        'membership_details' => $membershipDetails
                    ],
                    'coupon_info' => $couponDetails
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
            ->with(['items.product', 'branch', 'shop'])
            ->latest()
            ->get();

        // Transform orders to include consistent timezone formatting
        $transformedOrders = $orders->map(function ($order) {
            return [
                'id' => $order->id,
                'status' => $order->status,
                'total_amount' => $order->total_amount+$order->wallet_amount_used,
                'payment_method' => $order->payment_method,
                'payment_status' => $order->payment_status,
                'delivery_address' => $order->delivery_address,
                'created_at' => $order->getApiDate('created_at'),
                'created_at_utc' => $order->getUtcDate('created_at'),
                'created_at_asia_kolkata' => $order->getAsiaKolkataDate('created_at'),
                'updated_at' => $order->getApiDate('updated_at'),
                'items' => $order->items->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'product' => [
                            'id' => $item->product->id,
                            'name' => $item->product->name,
                            'image_path' => $item->product->image_url,
                        ],
                        'quantity' => $item->quantity,
                        'price' => $item->price,
                        'subtotal' => $item->subtotal,
                    ];
                }),
                'branch' => $order->branch ? [
                    'id' => $order->branch->id,
                    'name' => $order->branch->name,
                ] : null,
                'shop' => $order->shop ? [
                    'id' => $order->shop->id,
                    'name' => $order->shop->name,
                    'image_path' => $order->shop->image_url,
                    'address' => $order->shop->address
                ] : null,
            ];
        });

        return response()->json([
            'status' => 'success',
            'data' => $transformedOrders,
            'timezone_info' => [
                'api_timezone' => config('timezone.api_timezone', 'Asia/Kolkata'),
                'database_timezone' => config('timezone.database_timezone', 'UTC'),
                'app_timezone' => config('timezone.app_timezone', 'Asia/Kolkata'),
            ]
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

        $order->load(['items.product', 'branch', 'shop', 'deliveryBoy']);

        // Return the order with both original format (for Android compatibility) and formatted dates
        $orderData = $order->toArray();
        
        // Add formatted dates while keeping the original created_at for backward compatibility
        $orderData['created_at'] = $order->getApiDate('created_at');
        $orderData['updated_at'] = $order->getApiDate('updated_at');
        
        if ($order->cancelled_at) {
            $orderData['cancelled_at_formatted'] = $order->getApiDate('cancelled_at');
        }

        return response()->json([
            'status' => 'success',
            'data' => $orderData,
            'timezone_info' => [
                'api_timezone' => config('timezone.api_timezone', 'Asia/Kolkata'),
                'database_timezone' => config('timezone.database_timezone', 'UTC'),
                'app_timezone' => config('timezone.app_timezone', 'Asia/Kolkata'),
            ]
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

            // Initialize refund information
            $refundInfo = [
                'wallet_refunded' => false,
                'wallet_amount' => 0,
                'online_refund_required' => false,
                'online_refund_processed' => false,
                'online_refund_details' => null,
                'refund_errors' => []
            ];

            // Refund wallet amount if wallet was used
            if ($order->wallet_amount_used > 0) {
                try {
                    $user = auth()->user();
                    $user->addToWallet(
                        $order->wallet_amount_used,
                        "Refund for cancelled order #{$order->id}",
                        'App\\Models\\Order',
                        $order->id,
                        [
                            'order_id' => $order->id,
                            'refund_type' => 'order_cancellation',
                            'cancelled_at' => now()->toISOString()
                        ]
                    );
                    
                    $refundInfo['wallet_refunded'] = true;
                    $refundInfo['wallet_amount'] = $order->wallet_amount_used;
                    
                    Log::info('Wallet refund processed for cancelled order', [
                        'order_id' => $order->id,
                        'user_id' => $user->id,
                        'refund_amount' => $order->wallet_amount_used
                    ]);
                } catch (\Exception $e) {
                    Log::error('Wallet refund failed for cancelled order', [
                        'order_id' => $order->id,
                        'error' => $e->getMessage()
                    ]);
                    $refundInfo['refund_errors'][] = 'Wallet refund failed: ' . $e->getMessage();
                }
            }

            // Check if online refund is required
            $refundInfo['online_refund_required'] = $order->payment_method === 'razorpay' && $order->payment_status === 'paid';

            // If payment was made online, initiate refund process
            if ($refundInfo['online_refund_required']) {
                try {
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
                        $refundInfo['refund_errors'][] = 'Online refund failed: ' . $refundResult['message'];
                    }
                } catch (\Exception $e) {
                    Log::error('Razorpay refund exception for cancelled order', [
                        'order_id' => $order->id,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                    $refundInfo['refund_errors'][] = 'Online refund exception: ' . $e->getMessage();
                }
            }

            // Update order with refund information for tracking
            $order->update([
                'refund_info' => $refundInfo,
                'cancelled_at' => now()
            ]);

            DB::commit();

            // Prepare response message based on refund status
            $message = 'Order cancelled successfully';
            if (!empty($refundInfo['refund_errors'])) {
                $message .= '. Some refunds may require manual processing.';
            }

            return response()->json([
                'status' => 'success',
                'message' => $message,
                'data' => [
                    'order' => $order->fresh()->load('items.product', 'branch'),
                    'refund_info' => $refundInfo
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error cancelling order', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
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