<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\CouponUsage;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CouponController extends Controller
{
    /**
     * Validate a coupon code
     */
    public function validate(Request $request): JsonResponse
    {
        $request->validate([
            'code' => 'required|string',
            'order_amount' => 'required|numeric|min:0',
        ]);

        $code = strtoupper(trim($request->code));
        $orderAmount = (float) $request->order_amount;
        $user = auth()->user();

        $coupon = Coupon::where('code', $code)->first();

        if (!$coupon) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid coupon code.'
            ], 400);
        }

        // Check if coupon is valid for this order
        if (!$coupon->isValidForOrder($orderAmount, $user)) {
            $message = $this->getInvalidReason($coupon, $orderAmount, $user);
            
            return response()->json([
                'status' => 'error',
                'message' => $message
            ], 400);
        }

        // Calculate discount
        $discountAmount = $coupon->calculateDiscount($orderAmount);

        return response()->json([
            'status' => 'success',
            'message' => 'Coupon applied successfully.',
            'data' => [
                'coupon' => [
                    'id' => $coupon->id,
                    'code' => $coupon->code,
                    'name' => $coupon->name,
                    'type' => $coupon->type,
                    'value' => $coupon->value,
                    'minimum_order_amount' => $coupon->minimum_order_amount,
                    'maximum_discount' => $coupon->maximum_discount,
                ],
                'discount_amount' => $discountAmount,
                'final_amount' => $orderAmount - $discountAmount,
            ]
        ]);
    }

    /**
     * Apply coupon to order
     */
    public function apply(Request $request): JsonResponse
    {
        $request->validate([
            'coupon_id' => 'required|exists:coupons,id',
            'order_id' => 'required|exists:orders,id',
            'discount_amount' => 'required|numeric|min:0',
        ]);

        $coupon = Coupon::findOrFail($request->coupon_id);
        $user = auth()->user();

        // Check if user has already used this coupon for this order (any status)
        $existingUsage = CouponUsage::where([
            'coupon_id' => $coupon->id,
            'user_id' => $user->id,
            'order_id' => $request->order_id,
        ])->first();

        if ($existingUsage) {
            if ($existingUsage->isConfirmed()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Coupon already applied to this order.'
                ], 400);
            } elseif ($existingUsage->isPending()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Coupon is already pending for this order.'
                ], 400);
            } elseif ($existingUsage->isFailed()) {
                // Allow re-applying if previous usage failed
                $existingUsage->delete();
            }
        }

        // Create pending usage record
        CouponUsage::create([
            'coupon_id' => $coupon->id,
            'user_id' => $user->id,
            'order_id' => $request->order_id,
            'discount_amount' => $request->discount_amount,
            'status' => CouponUsage::STATUS_PENDING,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Coupon applied successfully. Pending payment confirmation.',
        ]);
    }

    /**
     * Confirm coupon usage after successful payment
     */
    public function confirmUsage(Request $request): JsonResponse
    {
        $request->validate([
            'coupon_id' => 'required|exists:coupons,id',
            'order_id' => 'required|exists:orders,id',
        ]);

        $coupon = Coupon::findOrFail($request->coupon_id);
        $user = auth()->user();

        $coupon->confirmUsage($request->order_id, $user->id);

        return response()->json([
            'status' => 'success',
            'message' => 'Coupon usage confirmed.',
        ]);
    }

    /**
     * Mark coupon usage as failed after payment failure
     */
    public function failUsage(Request $request): JsonResponse
    {
        $request->validate([
            'coupon_id' => 'required|exists:coupons,id',
            'order_id' => 'required|exists:orders,id',
        ]);

        $coupon = Coupon::findOrFail($request->coupon_id);
        $user = auth()->user();

        $coupon->failUsage($request->order_id, $user->id);

        return response()->json([
            'status' => 'success',
            'message' => 'Coupon usage marked as failed.',
        ]);
    }

    /**
     * Get available coupons for user
     */
    public function available(Request $request): JsonResponse
    {
        $request->validate([
            'order_amount' => 'required|numeric|min:0',
        ]);

        $orderAmount = (float) $request->order_amount;
        $user = auth()->user();

        $coupons = Coupon::active()
            ->valid()
            ->where('minimum_order_amount', '<=', $orderAmount)
            ->get()
            ->filter(function ($coupon) use ($user) {
                return $coupon->canBeUsedByUser($user);
            })
            ->map(function ($coupon) use ($orderAmount) {
                return [
                    'id' => $coupon->id,
                    'code' => $coupon->code,
                    'name' => $coupon->name,
                    'description' => $coupon->description,
                    'type' => $coupon->type,
                    'value' => $coupon->value,
                    'minimum_order_amount' => $coupon->minimum_order_amount,
                    'maximum_discount' => $coupon->maximum_discount,
                    'discount_amount' => $coupon->calculateDiscount($orderAmount),
                    'valid_until' => $coupon->valid_until->format('Y-m-d H:i:s'),
                ];
            });

        return response()->json([
            'status' => 'success',
            'data' => $coupons->values()
        ]);
    }

    /**
     * Get invalid reason for coupon
     */
    private function getInvalidReason(Coupon $coupon, float $orderAmount, $user): string
    {
        if (!$coupon->is_active) {
            return 'This coupon is inactive.';
        }

        if ($coupon->isExpired()) {
            return 'This coupon has expired.';
        }

        if ($coupon->isNotStarted()) {
            return 'This coupon is not yet active.';
        }

        if ($coupon->isUsageLimitReached()) {
            return 'This coupon has reached its usage limit.';
        }

        if ($orderAmount < $coupon->minimum_order_amount) {
            return "Minimum order amount of ₹{$coupon->minimum_order_amount} required.";
        }

        if ($user) {
            if ($coupon->getUsageCountForUser($user) >= $coupon->per_user_limit) {
                return 'You have already used this coupon the maximum number of times.';
            }

            if ($coupon->is_first_time_only && $user->orders()->exists()) {
                return 'This coupon is only for first-time customers.';
            }
        }

        return 'This coupon cannot be applied to your order.';
    }
} 