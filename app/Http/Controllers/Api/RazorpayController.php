<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use App\Helpers\NotificationHelper;
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
            $oldStatus = $order->status;
            $order->update([
                'payment_status' => 'paid',
                'payment_method' => 'razorpay',
                'payment_id' => $request->razorpay_payment_id,
                'status' => 'confirmed' // Update order status to confirmed after successful payment
            ]);

            // Send notifications
            NotificationHelper::sendPaymentSuccess($order->user_id, $order->id, $order->total_amount);
            if ($oldStatus !== 'confirmed') {
                NotificationHelper::sendOrderStatusUpdate($order->user_id, $order->id, 'confirmed');
                
                // Send confirmation emails to customer and branch manager
                \App\Services\OrderEmailService::sendOrderConfirmationEmails($order);
            }

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
                $oldStatus = $order->status;
                $order->update([
                    'payment_status' => 'failed',
                    'status' => 'payment_failed'
                ]);

                // Send payment failure notification
                NotificationHelper::sendPaymentFailure($order->user_id, $order->id, 'Payment verification failed');

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
            $oldStatus = $order->status;
            $order->update([
                'payment_status' => 'failed',
                'status' => 'payment_failed'
            ]);

            // Send payment failure notification
            NotificationHelper::sendPaymentFailure($order->user_id, $order->id, $request->error_description);

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

    /**
     * Process refund for cancelled order
     */
    public function processRefund(Order $order, $refundAmount = null, $refundReason = 'Order cancelled by customer')
    {
        try {
            // Find the payment record for this order
            $payment = Payment::where('order_id', $order->id)
                ->where('status', 'completed')
                ->where('payment_method', 'razorpay')
                ->latest()
                ->first();

            if (!$payment) {
                Log::warning('No completed Razorpay payment found for order cancellation', [
                    'order_id' => $order->id,
                    'user_id' => $order->user_id
                ]);
                return [
                    'success' => false,
                    'message' => 'No completed payment found for refund'
                ];
            }

            // If no refund amount specified, refund the full amount
            $refundAmount = $refundAmount ?? $payment->amount;

            // Check if refund amount is valid
            if ($refundAmount > $payment->amount) {
                Log::error('Refund amount exceeds payment amount', [
                    'order_id' => $order->id,
                    'payment_amount' => $payment->amount,
                    'refund_amount' => $refundAmount
                ]);
                return [
                    'success' => false,
                    'message' => 'Refund amount cannot exceed payment amount'
                ];
            }

            // Check if payment already has a refund
            if ($payment->refund_id) {
                Log::warning('Payment already has a refund', [
                    'order_id' => $order->id,
                    'refund_id' => $payment->refund_id
                ]);
                return [
                    'success' => false,
                    'message' => 'Payment already refunded'
                ];
            }

            // Process refund through Razorpay
            $refundData = [
                'amount' => $refundAmount * 100, // Convert to paise
                'speed' => 'normal', // or 'optimum' for faster refunds
                'notes' => [
                    'order_id' => $order->id,
                    'user_id' => $order->user_id,
                    'reason' => $refundReason
                ]
            ];

            $refund = $this->razorpay->payment->fetch($payment->transaction_id)->refund($refundData);

            // Update payment record with refund information
            $payment->update([
                'refund_id' => $refund->id,
                'refund_amount' => $refundAmount,
                'refund_status' => $refund->status,
                'refund_reason' => $refundReason,
                'payment_details' => array_merge($payment->payment_details ?? [], [
                    'refund_details' => $refund->toArray()
                ])
            ]);

            Log::info('Razorpay refund processed successfully', [
                'order_id' => $order->id,
                'payment_id' => $payment->transaction_id,
                'refund_id' => $refund->id,
                'refund_amount' => $refundAmount,
                'refund_status' => $refund->status
            ]);

            return [
                'success' => true,
                'message' => 'Refund processed successfully',
                'data' => [
                    'refund_id' => $refund->id,
                    'refund_amount' => $refundAmount,
                    'refund_status' => $refund->status,
                    'refund_reason' => $refundReason
                ]
            ];

        } catch (\Exception $e) {
            Log::error('Razorpay refund failed', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'message' => 'Failed to process refund: ' . $e->getMessage(),
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Get refund status
     */
    public function getRefundStatus(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id'
        ]);

        try {
            $order = Order::findOrFail($request->order_id);
            $payment = Payment::where('order_id', $order->id)
                ->where('payment_method', 'razorpay')
                ->latest()
                ->first();

            if (!$payment || !$payment->refund_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'No refund found for this order'
                ], 404);
            }

            // Fetch refund status from Razorpay
            $refund = $this->razorpay->refund->fetch($payment->refund_id);

            return response()->json([
                'success' => true,
                'data' => [
                    'order_id' => $order->id,
                    'refund_id' => $refund->id,
                    'refund_amount' => $refund->amount / 100, // Convert from paise
                    'refund_status' => $refund->status,
                    'refund_reason' => $payment->refund_reason,
                    'refund_processed_at' => $refund->created_at,
                    'refund_details' => $refund->toArray()
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Refund status check failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to get refund status',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get comprehensive refund status for an order
     */
    public function getOrderRefundStatus(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id'
        ]);

        try {
            $order = Order::findOrFail($request->order_id);
            
            // Ensure user can only view their own orders
            if ($order->user_id !== auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access to order'
                ], 403);
            }

            $refundStatus = [
                'order_id' => $order->id,
                'order_status' => $order->status,
                'payment_method' => $order->payment_method,
                'payment_status' => $order->payment_status,
                'total_amount' => $order->total_amount,
                'wallet_amount_used' => $order->wallet_amount_used,
                'refunds' => []
            ];

            // Get wallet refund status
            if ($order->wallet_amount_used > 0) {
                $walletTransaction = \App\Models\WalletTransaction::where('user_id', $order->user_id)
                    ->where('reference_type', 'App\\Models\\Order')
                    ->where('reference_id', $order->id)
                    ->where('type', 'credit')
                    ->where('description', 'like', '%Refund for cancelled order%')
                    ->first();

                $refundStatus['refunds']['wallet'] = [
                    'amount' => $order->wallet_amount_used,
                    'status' => $walletTransaction ? 'processed' : 'pending',
                    'processed_at' => $walletTransaction ? $walletTransaction->created_at : null,
                    'transaction_id' => $walletTransaction ? $walletTransaction->id : null
                ];
            }

            // Get Razorpay refund status
            if ($order->payment_method === 'razorpay' && $order->payment_status === 'completed') {
                $payment = Payment::where('order_id', $order->id)
                    ->where('payment_method', 'razorpay')
                    ->latest()
                    ->first();

                if ($payment && $payment->refund_id) {
                    try {
                        // Fetch refund status from Razorpay
                        $refund = $this->razorpay->refund->fetch($payment->refund_id);
                        
                        $refundStatus['refunds']['razorpay'] = [
                            'refund_id' => $refund->id,
                            'amount' => $refund->amount / 100, // Convert from paise
                            'status' => $refund->status,
                            'reason' => $payment->refund_reason,
                            'processed_at' => $refund->created_at,
                            'refund_details' => $refund->toArray()
                        ];
                    } catch (\Exception $e) {
                        Log::error('Failed to fetch Razorpay refund status', [
                            'order_id' => $order->id,
                            'refund_id' => $payment->refund_id,
                            'error' => $e->getMessage()
                        ]);
                        
                        $refundStatus['refunds']['razorpay'] = [
                            'refund_id' => $payment->refund_id,
                            'amount' => $payment->refund_amount,
                            'status' => 'unknown',
                            'reason' => $payment->refund_reason,
                            'error' => 'Failed to fetch refund status from Razorpay'
                        ];
                    }
                } else {
                    $refundStatus['refunds']['razorpay'] = [
                        'status' => 'not_initiated',
                        'message' => 'No refund initiated for this payment'
                    ];
                }
            }

            // Add order refund info if available
            if ($order->refund_info) {
                $refundStatus['order_refund_info'] = $order->refund_info;
            }

            return response()->json([
                'success' => true,
                'data' => $refundStatus
            ]);

        } catch (\Exception $e) {
            Log::error('Order refund status check failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to get refund status',
                'error' => $e->getMessage()
            ], 500);
        }
    }
} 