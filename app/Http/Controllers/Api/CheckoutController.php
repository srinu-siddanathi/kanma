<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class CheckoutController extends Controller
{
    /**
     * Calculate checkout details including delivery fee and small cart fee based on membership
     */
    public function calculateFees(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'cart_items' => 'required|array',
            'cart_items.*.product_id' => 'required|exists:products,id',
            'cart_items.*.quantity' => 'required|integer|min:1',
            'cart_items.*.variant_id' => 'nullable|exists:product_variants,id',
            'delivery_latitude' => 'nullable|numeric',
            'delivery_longitude' => 'nullable|numeric',
            'branch_id' => 'nullable|exists:branches,id',
            'coupon_id' => 'nullable|exists:coupons,id'
        ]);

        try {
            $user = auth()->user();
            $cartItems = $validated['cart_items'];
            
            // Calculate subtotal
            $subtotal = 0;
            $items = [];
            
            foreach ($cartItems as $item) {
                $product = \App\Models\Product::with(['variants' => function($q) use ($item) {
                    if (isset($item['variant_id'])) {
                        $q->where('id', $item['variant_id']);
                    }
                }])->findOrFail($item['product_id']);
                
                $price = $product->price;
                $unit = $product->base_unit;
                
                // If variant is specified, use variant price and unit
                if (isset($item['variant_id'])) {
                    $variant = $product->variants->first();
                    if ($variant) {
                        $price = $variant->price;
                        $unit = $variant->unit;
                    }
                }
                
                // Apply discount if any
                $discountedPrice = $product->discount > 0 
                    ? $price - ($price * ($product->discount / 100))
                    : $price;
                
                $itemTotal = $discountedPrice * $item['quantity'];
                $subtotal += $itemTotal;
                
                $items[] = [
                    'product_id' => $item['product_id'],
                    'variant_id' => $item['variant_id'] ?? null,
                    'product_name' => $product->name,
                    'quantity' => $item['quantity'],
                    'unit' => $unit,
                    'price' => $price,
                    'discounted_price' => round($discountedPrice, 2),
                    'discount_percentage' => $product->discount,
                    'total' => round($itemTotal, 2)
                ];
            }

            // Initialize fees
            $deliveryFee = 50; // Default delivery fee
            $smallCartFee = 0; // Small cart fee
            $hasActiveMembership = false;
            $membershipDetails = null;
            $couponDiscount = 0; // Coupon discount
            $couponDetails = null; // Coupon details

            // Apply coupon discount if coupon_id is provided
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
                if (!$hasFreeDelivery && $plan->free_delivery_radius > 0 && 
                    isset($validated['delivery_latitude']) && 
                    isset($validated['delivery_longitude'])) {
                    
                    // Get branch location for distance calculation
                    $branch = null;
                    if (isset($validated['branch_id'])) {
                        $branch = Branch::find($validated['branch_id']);
                    } else {
                        $branch = Branch::where('is_active', true)->first();
                    }
                    
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

            // Calculate final total after coupon discount
            $totalAfterCoupon = $subtotal - $couponDiscount;
            $total = $totalAfterCoupon + $deliveryFee + $smallCartFee;

            return response()->json([
                'status' => 'success',
                'data' => [
                    'items' => $items,
                    'subtotal' => round($subtotal, 2),
                    'coupon_discount' => round($couponDiscount, 2),
                    'coupon_details' => $couponDetails,
                    'total_after_coupon' => round($totalAfterCoupon, 2),
                    'delivery_fee' => round($deliveryFee, 2),
                    'small_cart_fee' => round($smallCartFee, 2),
                    'total' => round($total, 2),
                    'has_active_membership' => $hasActiveMembership,
                    'membership_details' => $membershipDetails,
                    'minimum_order_amount' => $minimumOrderAmount,
                    'small_cart_fee_amount' => $smallCartFeeAmount,
                    'breakdown' => [
                        'subtotal' => round($subtotal, 2),
                        'coupon_discount' => round($couponDiscount, 2),
                        'total_after_coupon' => round($totalAfterCoupon, 2),
                        'delivery_fee' => round($deliveryFee, 2),
                        'small_cart_fee' => round($smallCartFee, 2),
                        'total' => round($total, 2)
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error calculating checkout fees', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Error calculating checkout fees: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get checkout summary with cart items and fees
     */
    public function getCheckoutSummary(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'delivery_latitude' => 'nullable|numeric',
            'delivery_longitude' => 'nullable|numeric',
            'branch_id' => 'nullable|exists:branches,id',
            'coupon_id' => 'nullable|exists:coupons,id'
        ]);

        try {
            $user = auth()->user();
            $cartKey = 'cart_' . $user->id;
            $cartItems = Cache::get($cartKey, []);
            
            if (empty($cartItems)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Cart is empty'
                ], 400);
            }

            // Calculate subtotal
            $subtotal = 0;
            $items = [];
            
            foreach ($cartItems as $item) {
                $itemTotal = $item['discounted_price'] * $item['quantity'];
                $subtotal += $itemTotal;
                
                $items[] = [
                    'product_id' => $item['product_id'],
                    'variant_id' => $item['variant_id'] ?? null,
                    'product_name' => $item['name'],
                    'quantity' => $item['quantity'],
                    'unit' => $item['unit'],
                    'price' => $item['price'],
                    'discounted_price' => $item['discounted_price'],
                    'discount_percentage' => $item['discount'],
                    'total' => round($itemTotal, 2)
                ];
            }

            // Initialize fees
            $deliveryFee = 50; // Default delivery fee
            $smallCartFee = 0; // Small cart fee
            $hasActiveMembership = false;
            $membershipDetails = null;
            $couponDiscount = 0; // Coupon discount
            $couponDetails = null; // Coupon details

            // Apply coupon discount if coupon_id is provided
            if (isset($validated['coupon_id'])) {
                $coupon = \App\Models\Coupon::find($validated['coupon_id']);

                if ($coupon && $coupon->is_active) {
                    // Check if coupon is expired
                    if ($coupon->valid_until && now()->isAfter($coupon->valid_until)) {
                        return response()->json([
                            'status' => 'error',
                            'message' => 'Coupon is expired'
                        ], 400);
                    }
                    
                        // Check minimum order amount
                        if ($subtotal >= $coupon->minimum_order_amount) {
                            // Check usage limits
                            $userUsageCount = \App\Models\CouponUsage::where('coupon_id', $coupon->id)
                                ->where('user_id', $user->id)
                                ->where('status', 'confirmed')
                                ->count();

                            $totalUsageCount = \App\Models\CouponUsage::where('coupon_id', $coupon->id)
                                ->where('status', 'confirmed')
                                ->count();

                            if ($userUsageCount < $coupon->per_user_limit && 
                                ($coupon->usage_limit === null || $totalUsageCount < $coupon->usage_limit)) {
                                
                                // Calculate coupon discount
                                if ($coupon->type === 'percentage') {
                                    $couponDiscount = ($subtotal * $coupon->value) / 100;
                                    // Apply maximum discount limit if set
                                    if ($coupon->maximum_discount) {
                                        $couponDiscount = min($couponDiscount, $coupon->maximum_discount);
                                    }
                                } else {
                                    $couponDiscount = min($coupon->value, $subtotal);
                                }
                                
                                $couponDiscount = round($couponDiscount, 2);
                                
                                $couponDetails = [
                                    'id' => $coupon->id,
                                    'code' => $coupon->code,
                                    'title' => $coupon->name,
                                    'discount_type' => $coupon->type,
                                    'discount_value' => $coupon->value,
                                    'discount_amount' => $couponDiscount
                                ];
                            }
                        }
                    
                }
            }

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
                if (!$hasFreeDelivery && $plan->free_delivery_radius > 0 && 
                    isset($validated['delivery_latitude']) && 
                    isset($validated['delivery_longitude'])) {
                    
                    // Get branch location for distance calculation
                    $branch = null;
                    if (isset($validated['branch_id'])) {
                        $branch = Branch::find($validated['branch_id']);
                    } else {
                        $branch = Branch::where('is_active', true)->first();
                    }
                    
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

            // Calculate final total after coupon discount
            $totalAfterCoupon = $subtotal - $couponDiscount;
            $total = $totalAfterCoupon + $deliveryFee + $smallCartFee;

            return response()->json([
                'status' => 'success',
                'data' => [
                    'items' => $items,
                    'subtotal' => round($subtotal, 2),
                    'coupon_discount' => round($couponDiscount, 2),
                    'coupon_details' => $couponDetails,
                    'total_after_coupon' => round($totalAfterCoupon, 2),
                    'delivery_fee' => round($deliveryFee, 2),
                    'small_cart_fee' => round($smallCartFee, 2),
                    'total' => round($total, 2),
                    'has_active_membership' => $hasActiveMembership,
                    'membership_details' => $membershipDetails,
                    'minimum_order_amount' => $minimumOrderAmount,
                    'small_cart_fee_amount' => $smallCartFeeAmount,
                    'breakdown' => [
                        'subtotal' => round($subtotal, 2),
                        'coupon_discount' => round($couponDiscount, 2),
                        'total_after_coupon' => round($totalAfterCoupon, 2),
                        'delivery_fee' => round($deliveryFee, 2),
                        'small_cart_fee' => round($smallCartFee, 2),
                        'total' => round($total, 2)
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting checkout summary', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Error getting checkout summary: ' . $e->getMessage()
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