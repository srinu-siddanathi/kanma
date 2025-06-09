<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPlan;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Razorpay\Api\Api;

class MembershipController extends Controller
{
    public function getPlans()
    {
        $kathaPlans = SubscriptionPlan::where('type', 'katha')
            ->where('is_active', true)
            ->orderBy('price')
            ->get();

        $o2Plans = SubscriptionPlan::where('type', 'o2')
            ->where('is_active', true)
            ->orderBy('price')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => [
                'katha_plans' => $kathaPlans,
                'o2_plans' => $o2Plans
            ]
        ]);
    }

    public function getCurrentSubscription()
    {
        $user = auth()->user();
        $subscription = $user->currentSubscription();

        if (!$subscription) {
            return response()->json([
                'status' => 'success',
                'data' => null,
                'message' => 'No active subscription found'
            ]);
        }

        return response()->json([
            'status' => 'success',
            'data' => $subscription->load('plan')
        ]);
    }

    public function getMySubscription()
    {
        if (!auth()->check()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthenticated'
            ], 401);
        }

        $user = auth()->user();
        $subscription = $user->currentSubscription();

        if (!$subscription) {
            return response()->json([
                'status' => 'success',
                'data' => null,
                'message' => 'No active subscription found'
            ]);
        }

        return response()->json([
            'status' => 'success',
            'data' => $subscription->load('plan')
        ]);
    }

    public function subscribe(Request $request)
    {
        if (!auth()->check()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthenticated'
            ], 401);
        }

        $request->validate([
            'plan_id' => 'required|exists:subscription_plans,id',
            'payment_method' => 'required|in:razorpay,wallet'
        ]);

        $user = auth()->user();
        $plan = SubscriptionPlan::findOrFail($request->plan_id);

        // Check if user already has an active subscription
        $activeSubscription = $user->currentSubscription();
        if ($activeSubscription) {
            return response()->json([
                'status' => 'error',
                'message' => 'You already have an active subscription'
            ], 400);
        }

        // Create subscription record
        $subscription = new Subscription([
            'user_id' => $user->id,
            'subscription_plan_id' => $plan->id,
            'starts_at' => now(),
            'ends_at' => now()->addDays($plan->validity_days),
            'status' => 'pending',
            'payment_method' => $request->payment_method,
        ]);

        $subscription->save();

        if ($request->payment_method === 'razorpay') {
            // Initialize Razorpay payment
            $api = new Api(config('services.razorpay.key'), config('services.razorpay.secret'));
            
            $order = $api->order->create([
                'amount' => $plan->price * 100, // Amount in paise
                'currency' => 'INR',
                'payment_capture' => 1
            ]);

            return response()->json([
                'status' => 'success',
                'data' => [
                    'subscription_id' => $subscription->id,
                    'order_id' => $order['id'],
                    'amount' => $plan->price,
                    'currency' => 'INR',
                    'key' => config('services.razorpay.key')
                ]
            ]);
        } else {
            // Handle wallet payment
            if ($user->wallet_balance < $plan->price) {
                $subscription->delete();
                return response()->json([
                    'status' => 'error',
                    'message' => 'Insufficient wallet balance'
                ], 400);
            }

            // Deduct from wallet
            $user->decrement('wallet_balance', $plan->price);

            // Update subscription status
            $subscription->update([
                'status' => 'active',
                'payment_details' => [
                    'amount' => $plan->price,
                    'paid_at' => now(),
                    'transaction_id' => 'WALLET-' . uniqid()
                ]
            ]);

            // Add wallet addon if applicable
            if ($plan->wallet_addon > 0) {
                $user->increment('wallet_balance', $plan->wallet_addon);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Subscription activated successfully',
                'data' => $subscription->load('plan')
            ]);
        }
    }

    public function verifyPayment(Request $request)
    {
        $request->validate([
            'razorpay_payment_id' => 'required|string',
            'razorpay_order_id' => 'required|string',
            'razorpay_signature' => 'required|string',
            'subscription_id' => 'required|exists:subscriptions,id'
        ]);

        try {
            $api = new Api(config('services.razorpay.key'), config('services.razorpay.secret'));

            $attributes = [
                'razorpay_payment_id' => $request->razorpay_payment_id,
                'razorpay_order_id' => $request->razorpay_order_id,
                'razorpay_signature' => $request->razorpay_signature
            ];

            $api->utility->verifyPaymentSignature($attributes);

            $subscription = Subscription::with('plan')->findOrFail($request->subscription_id);
            $plan = $subscription->plan;
            $user = $subscription->user;

            if (!$plan) {
                throw new \Exception('Subscription plan not found');
            }

            // Update subscription status
            $subscription->update([
                'status' => 'active',
                'payment_details' => array_merge($subscription->payment_details ?? [], [
                    'razorpay_payment_id' => $request->razorpay_payment_id,
                    'paid_at' => now()
                ])
            ]);

            // Add wallet addon if applicable
            if ($plan->wallet_addon > 0) {
                $user->increment('wallet_balance', $plan->wallet_addon);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Payment verified successfully',
                'data' => $subscription->load('plan')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Payment verification failed: ' . $e->getMessage()
            ], 400);
        }
    }

    public function getSubscriptionHistory()
    {
        $user = auth()->user();
        $subscriptions = $user->subscriptions()
            ->with('plan')
            ->latest()
            ->paginate(10);

        return response()->json([
            'status' => 'success',
            'data' => $subscriptions
        ]);
    }
} 