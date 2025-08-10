<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\WalletTransaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RefundController extends Controller
{
    /**
     * Get refund history for the authenticated user
     */
    public function history(Request $request): JsonResponse
    {
        $user = $request->user();

        // Wallet refunds (credits) possibly from order cancellations
        $walletRefunds = WalletTransaction::where('user_id', $user->id)
            ->where('type', 'credit')
            ->where(function ($q) {
                $q->where('description', 'like', 'Refund for cancelled order%')
                  ->orWhere('reference_type', 'App\\Models\\Order');
            })
            ->get()
            ->map(function (WalletTransaction $tx) {
                $orderId = $tx->reference_type === 'App\\Models\\Order' ? $tx->reference_id : ($tx->metadata['order_id'] ?? null);
                $formattedTime = $tx->created_at->format('M jS Y H:i');
                return [
                    'id' => 'wallet_' . $tx->id,
                    'order_id' => $orderId,
                    'source' => 'wallet',
                    'amount' => (float) $tx->amount,
                    'currency' => 'INR',
                    'timestamp' => $tx->created_at->toISOString(),
                    'display' => "Refunded of amount on {$formattedTime} - Rs. {$tx->amount}",
                    'details' => [
                        'description' => $tx->description,
                        'status' => $tx->status,
                    ],
                ];
            })
            ->values()
            ->all();

        // Online payment refunds (e.g., Razorpay)
        $onlineRefunds = Payment::where('user_id', $user->id)
            ->whereNotNull('refund_id')
            ->orderByDesc('updated_at')
            ->get()
            ->map(function (Payment $payment) {
                $processedAt = $payment->payment_details['refund_details']['created_at'] ?? $payment->updated_at;
                // created_at from Razorpay is epoch seconds; normalize if numeric
                if (is_numeric($processedAt)) {
                    $processedAt = now()->setTimestamp((int) $processedAt);
                } else {
                    $processedAt = \Carbon\Carbon::parse($processedAt);
                }
                $formattedTime = $processedAt->format('M jS Y H:i');
                return [
                    'id' => 'online_' . $payment->id,
                    'order_id' => $payment->order_id,
                    'source' => $payment->payment_method ?? 'online',
                    'amount' => (float) ($payment->refund_amount ?? 0),
                    'currency' => 'INR',
                    'timestamp' => $processedAt->toISOString(),
                    'display' => "Refunded of amount on {$formattedTime}",
                    'details' => [
                        'refund_id' => $payment->refund_id,
                        'status' => $payment->refund_status,
                        'reason' => $payment->refund_reason,
                    ],
                ];
            })
            ->values()
            ->all();

        // Merge and sort by timestamp desc
        $all = collect(array_merge($walletRefunds, $onlineRefunds))
            ->sortByDesc('timestamp')
            ->values();

        return response()->json([
            'status' => 'success',
            'data' => $all,
        ]);
    }
}


