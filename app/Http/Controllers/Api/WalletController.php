<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Razorpay\Api\Api;

class WalletController extends Controller
{
    public function getBalance(Request $request)
    {
        $user = $request->user();
        return response()->json([
            'status' => 'success',
            'data' => [
                'balance' => $user->wallet_balance
            ]
        ]);
    }

    public function getTransactions(Request $request)
    {
        $user = $request->user();
        $transactions = WalletTransaction::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json([
            'status' => 'success',
            'data' => $transactions
        ]);
    }

    public function initiateDeposit(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1'
        ]);

        $user = $request->user();
        $amount = $request->amount;

        try {
            $api = new Api(config('services.razorpay.key'), config('services.razorpay.secret'));
            
            $order = $api->order->create([
                'amount' => $amount * 100, // Amount in paise
                'currency' => 'INR',
                'payment_capture' => 1,
                'notes' => [
                    'user_id' => $user->id,
                    'type' => 'wallet_deposit'
                ]
            ]);

            return response()->json([
                'status' => 'success',
                'data' => [
                    'order_id' => $order['id'],
                    'amount' => $amount,
                    'currency' => 'INR',
                    'key' => config('services.razorpay.key')
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create payment order: ' . $e->getMessage()
            ], 500);
        }
    }

    public function verifyDeposit(Request $request)
    {
        $request->validate([
            'razorpay_payment_id' => 'required|string',
            'razorpay_order_id' => 'required|string',
            'razorpay_signature' => 'required|string',
            'amount' => 'required|numeric|min:1'
        ]);

        try {
            $api = new Api(config('services.razorpay.key'), config('services.razorpay.secret'));

            $attributes = [
                'razorpay_payment_id' => $request->razorpay_payment_id,
                'razorpay_order_id' => $request->razorpay_order_id,
                'razorpay_signature' => $request->razorpay_signature
            ];

            $api->utility->verifyPaymentSignature($attributes);

            $user = $request->user();
            $amount = $request->amount;

            DB::transaction(function () use ($user, $amount, $request) {
                // Create wallet transaction
                WalletTransaction::create([
                    'user_id' => $user->id,
                    'amount' => $amount,
                    'type' => 'credit',
                    'description' => 'Wallet deposit via Razorpay',
                    'reference_type' => 'deposit',
                    'payment_id' => $request->razorpay_payment_id,
                    'status' => 'completed',
                    'metadata' => [
                        'razorpay_order_id' => $request->razorpay_order_id,
                        'razorpay_payment_id' => $request->razorpay_payment_id
                    ]
                ]);

                // Update user's wallet balance
                $user->increment('wallet_balance', $amount);
            });

            return response()->json([
                'status' => 'success',
                'message' => 'Payment verified and wallet credited successfully',
                'data' => [
                    'new_balance' => $user->wallet_balance
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Payment verification failed: ' . $e->getMessage()
            ], 400);
        }
    }
} 