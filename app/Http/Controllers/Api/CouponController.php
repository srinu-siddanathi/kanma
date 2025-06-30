<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\CouponUsage;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CouponController extends Controller
{
    /**
     * Validate and apply coupon
     * This creates a pending usage that will be confirmed or failed based on payment outcome
     */
    public function validateAndApply(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'coupon_code' => 'required|string|max:50',
            'user_id' => 'required|exists:users,id',
            'order_amount' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $coupon = Coupon::where('code', $request->coupon_code)
                ->where('is_active', 1)
                ->first();

            if (!$coupon) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid coupon code'
                ], 404);
            }

            // Check if coupon is expired
            if ($coupon->valid_until && now()->isAfter($coupon->valid_until)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Coupon has expired'
                ], 400);
            }

            // Check minimum order amount
            if ($request->order_amount < $coupon->minimum_order_amount) {
                return response()->json([
                    'success' => false,
                    'message' => "Minimum order amount required: ₹{$coupon->minimum_order_amount}"
                ], 400);
            }

            // Check usage limits
            $userUsageCount = CouponUsage::where('coupon_id', $coupon->id)
                ->where('user_id', $request->user_id)
                ->where('status', 'confirmed')
                ->count();

            if ($userUsageCount >= $coupon->per_user_limit) {
                return response()->json([
                    'success' => false,
                    'message' => 'You have already used this coupon maximum times'
                ], 400);
            }

            // Check total usage limit
            $totalUsageCount = CouponUsage::where('coupon_id', $coupon->id)
                ->where('status', 'confirmed')
                ->count();

            if ($coupon->usage_limit !== null && $totalUsageCount >= $coupon->usage_limit) {
                return response()->json([
                    'success' => false,
                    'message' => 'Coupon usage limit reached'
                ], 400);
            }

            // Calculate discount
            $discountAmount = $this->calculateDiscount($coupon, $request->order_amount);

            // Create pending usage
            $couponUsage = CouponUsage::create([
                'coupon_id' => $coupon->id,
                'user_id' => $request->user_id,
                'order_amount' => $request->order_amount,
                'discount_amount' => $discountAmount,
                'status' => 'pending'
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Coupon applied successfully',
                'data' => [
                    'coupon_id' => $coupon->id,
                    'coupon_code' => $coupon->code,
                    'discount_amount' => $discountAmount,
                    'discount_type' => $coupon->type,
                    'discount_value' => $coupon->value,
                    'final_amount' => $request->order_amount - $discountAmount,
                    'usage_id' => $couponUsage->id
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to apply coupon',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get available coupons for user
     */
    public function getAvailableCoupons(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $coupons = Coupon::where('is_active', 1)
                ->where(function ($query) {
                    $query->whereNull('valid_until')
                        ->orWhere('valid_until', '>', now());
                })
                ->get();
            
            $filteredCoupons = [];
            
            foreach ($coupons as $coupon) {
                // Check if user can use this coupon
                $userUsageCount = CouponUsage::where('coupon_id', $coupon->id)
                    ->where('user_id', $request->user_id)
                    ->where('status', 'confirmed')
                    ->count();

                $totalUsageCount = CouponUsage::where('coupon_id', $coupon->id)
                    ->where('status', 'confirmed')
                    ->count();
                
                $isAvailable = $userUsageCount < $coupon->per_user_limit && 
                              ($coupon->usage_limit === null || $totalUsageCount < $coupon->usage_limit);
                
                if ($isAvailable) {
                    $filteredCoupons[] = [
                        'id' => $coupon->id,
                        'code' => $coupon->code,
                        'title' => $coupon->name,
                        'description' => $coupon->description,
                        'discount_type' => $coupon->type,
                        'discount_value' => $coupon->value,
                        'min_order_amount' => $coupon->minimum_order_amount,
                        'expires_at' => $coupon->valid_until,
                        'usage_limit' => $coupon->usage_limit,
                        'usage_limit_per_user' => $coupon->per_user_limit
                    ];
                }
            }

            return response()->json([
                'success' => true,
                'data' => $filteredCoupons
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch coupons',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Confirm coupon usage after successful payment
     */
    public function confirmUsage(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'usage_id' => 'required|exists:coupon_usages,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $usage = CouponUsage::findOrFail($request->usage_id);

            if ($usage->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid usage status'
                ], 400);
            }

            $usage->update(['status' => 'confirmed']);

            return response()->json([
                'success' => true,
                'message' => 'Coupon usage confirmed'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to confirm usage',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Fail coupon usage after payment failure
     */
    public function failUsage(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'usage_id' => 'required|exists:coupon_usages,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $usage = CouponUsage::findOrFail($request->usage_id);

            if ($usage->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid usage status'
                ], 400);
            }

            $usage->update(['status' => 'failed']);

            return response()->json([
                'success' => true,
                'message' => 'Coupon usage marked as failed'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to mark usage as failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Calculate discount amount based on coupon type
     */
    private function calculateDiscount(Coupon $coupon, float $orderAmount): float
    {
        if ($coupon->type === 'percentage') {
            $discount = ($orderAmount * $coupon->value) / 100;
            // Apply maximum discount limit if set
            if ($coupon->maximum_discount) {
                $discount = min($discount, $coupon->maximum_discount);
            }
            return round($discount, 2);
        } else {
            return min($coupon->value, $orderAmount);
        }
    }
} 