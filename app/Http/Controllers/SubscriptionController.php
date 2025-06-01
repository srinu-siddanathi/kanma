<?php

namespace App\Http\Controllers;

use App\Models\SubscriptionPlan;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Razorpay\Api\Api;

class SubscriptionController extends Controller
{
    public function index()
    {
        $subscriptions = auth()->user()->subscriptions()
            ->with('plan')
            ->latest()
            ->paginate(10);

        return view('subscriptions.index', compact('subscriptions'));
    }

    public function plans()
    {
        $kathaPlans = SubscriptionPlan::where('type', 'katha')
            ->where('is_active', true)
            ->orderBy('price')
            ->get();

        $o2Plans = SubscriptionPlan::where('type', 'o2')
            ->where('is_active', true)
            ->orderBy('price')
            ->get();

        return view('subscription.plans', compact('kathaPlans', 'o2Plans'));
    }

    public function checkout(SubscriptionPlan $plan)
    {
        // Check if user already has an active subscription
        $activeSubscription = auth()->user()->subscriptions()
            ->where('status', 'active')
            ->where('ends_at', '>', now())
            ->first();

        if ($activeSubscription) {
            return redirect()->route('home')->with('warning', 'You already have an active subscription.');
        }

        return view('subscription.checkout', compact('plan'));
    }

    public function process(Request $request, SubscriptionPlan $plan)
    {
        $request->validate([
            'payment_method' => 'required|in:razorpay,wallet',
        ]);

        $user = auth()->user();

        // Check if user already has an active subscription
        $activeSubscription = $user->subscriptions()
            ->where('status', 'active')
            ->where('ends_at', '>', now())
            ->first();

        if ($activeSubscription) {
            return redirect()->route('home')->with('warning', 'You already have an active subscription.');
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

        // Handle payment based on method
        if ($request->payment_method === 'razorpay') {
            try {
                $api = new Api(config('services.razorpay.key'), config('services.razorpay.secret'));

                $orderData = [
                    'receipt'         => 'sub_' . $subscription->id,
                    'amount'          => $plan->price * 100, // Convert to paise
                    'currency'        => 'INR',
                    'payment_capture' => 1
                ];

                $razorpayOrder = $api->order->create($orderData);

                // Update subscription with Razorpay order ID
                $subscription->update([
                    'payment_details' => [
                        'razorpay_order_id' => $razorpayOrder['id'],
                        'amount' => $plan->price,
                        'currency' => 'INR'
                    ]
                ]);

                return response()->json([
                    'id' => $razorpayOrder['id'],
                    'amount' => $razorpayOrder['amount'],
                    'currency' => $razorpayOrder['currency'],
                    'subscription_id' => $subscription->id
                ]);
            } catch (\Exception $e) {
                $subscription->delete();
                return response()->json([
                    'error' => 'Failed to create Razorpay order: ' . $e->getMessage()
                ], 500);
            }
        } else {
            // Handle wallet payment
            if ($user->wallet_balance < $plan->price) {
                $subscription->delete();
                return back()->with('error', 'Insufficient wallet balance.');
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

            return redirect()->route('home')->with('success', 'Subscription activated successfully!');
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
                'success' => true,
                'message' => 'Payment verified successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Payment verification failed: ' . $e->getMessage()
            ], 400);
        }
    }
} 