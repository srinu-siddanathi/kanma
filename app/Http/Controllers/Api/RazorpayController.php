<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Razorpay\Api\Api;

class RazorpayController extends Controller
{
    private $razorpay;

    public function __construct()
    {
        $this->razorpay = new Api(config('services.razorpay.key'), config('services.razorpay.secret'));
    }

    /**
     * Create a new Razorpay order
     */
    public function createOrder(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'currency' => 'required|string|size:3',
            'order_id' => 'required|exists:orders,id',
        ]);

        try {
            $order = Order::findOrFail($request->order_id);
            
            // Create Razorpay Order
            $razorpayOrder = $this->razorpay->order->create([
                'amount' => $request->amount * 100, // Amount in paise
                'currency' => $request->currency,
                'payment_capture' => 1,
                'notes' => [
                    'order_id' => $order->id,
                    'user_id' => $order->user_id
                ]
            ]);

            // Create payment record
            Payment::create([
                'user_id' => $order->user_id,
                'order_id' => $order->id,
                'payment_id' => $razorpayOrder->id,
                'amount' => $request->amount,
                'currency' => $request->currency,
                'status' => 'pending',
                'payment_method' => 'razorpay',
                'payment_details' => $razorpayOrder->toArray()
            ]);

            return response()->json([
                'success' => true,
                'data' => [
                    'order_id' => $razorpayOrder->id,
                    'amount' => $request->amount,
                    'currency' => $request->currency,
                    'key' => config('services.razorpay.key')
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Razorpay order creation failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create payment order',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verify and process Razorpay payment
     */
    public function verifyPayment(Request $request)
    {
        $request->validate([
            'razorpay_payment_id' => 'required|string',
            'razorpay_order_id' => 'required|string',
            'razorpay_signature' => 'required|string',
            'order_id' => 'required|exists:orders,id'
        ]);

        try {
            $attributes = [
                'razorpay_payment_id' => $request->razorpay_payment_id,
                'razorpay_order_id' => $request->razorpay_order_id,
                'razorpay_signature' => $request->razorpay_signature
            ];

            // Verify payment signature
            $this->razorpay->utility->verifyPaymentSignature($attributes);

            // Get payment details from Razorpay
            $payment = $this->razorpay->payment->fetch($request->razorpay_payment_id);
            
            // Update payment record
            $paymentRecord = Payment::where('payment_id', $request->razorpay_order_id)->first();
            if ($paymentRecord) {
                $paymentRecord->update([
                    'status' => 'completed',
                    'transaction_id' => $request->razorpay_payment_id,
                    'payment_details' => array_merge($paymentRecord->payment_details ?? [], [
                        'payment_details' => $payment->toArray()
                    ])
                ]);
            }

            // Update order status
            $order = Order::findOrFail($request->order_id);
            $order->update([
                'payment_status' => 'paid',
                'payment_method' => 'razorpay',
                'payment_id' => $request->razorpay_payment_id,
                'status' => 'confirmed' // Update order status to confirmed after successful payment
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Payment verified successfully',
                'data' => [
                    'order_id' => $order->id,
                    'payment_id' => $request->razorpay_payment_id,
                    'status' => 'paid'
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Razorpay payment verification failed: ' . $e->getMessage());
            
            // Update order and payment status on failure
            try {
                $order = Order::findOrFail($request->order_id);
                $order->update([
                    'payment_status' => 'failed',
                    'status' => 'payment_failed'
                ]);

                $paymentRecord = Payment::where('payment_id', $request->razorpay_order_id)->first();
                if ($paymentRecord) {
                    $paymentRecord->update([
                        'status' => 'failed',
                        'error_message' => $e->getMessage()
                    ]);
                }
            } catch (\Exception $updateError) {
                Log::error('Failed to update order status after payment failure: ' . $updateError->getMessage());
            }

            return response()->json([
                'success' => false,
                'message' => 'Payment verification failed',
                'error' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Handle payment failure
     */
    public function handlePaymentFailure(Request $request)
    {
        $request->validate([
            'razorpay_order_id' => 'required|string',
            'order_id' => 'required|exists:orders,id',
            'error_code' => 'required|string',
            'error_description' => 'required|string'
        ]);

        try {
            $order = Order::findOrFail($request->order_id);
            $order->update([
                'payment_status' => 'failed',
                'status' => 'payment_failed'
            ]);

            $paymentRecord = Payment::where('payment_id', $request->razorpay_order_id)->first();
            if ($paymentRecord) {
                $paymentRecord->update([
                    'status' => 'failed',
                    'error_message' => $request->error_description,
                    'payment_details' => array_merge($paymentRecord->payment_details ?? [], [
                        'error_code' => $request->error_code,
                        'error_description' => $request->error_description
                    ])
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Payment failure recorded successfully',
                'data' => [
                    'order_id' => $order->id,
                    'status' => 'payment_failed'
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to record payment failure: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to record payment failure',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get payment status
     */
    public function getPaymentStatus(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id'
        ]);

        try {
            $order = Order::findOrFail($request->order_id);
            $payment = Payment::where('order_id', $order->id)->latest()->first();

            return response()->json([
                'success' => true,
                'data' => [
                    'order_id' => $order->id,
                    'payment_status' => $order->payment_status,
                    'payment_method' => $order->payment_method,
                    'payment_id' => $order->payment_id,
                    'order_status' => $order->status,
                    'payment_details' => $payment ? $payment->payment_details : null
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Payment status check failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to get payment status',
                'error' => $e->getMessage()
            ], 500);
        }
    }
} 