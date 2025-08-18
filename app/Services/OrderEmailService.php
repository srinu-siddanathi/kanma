<?php

namespace App\Services;

use App\Models\Order;
use App\Mail\OrderConfirmedMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class OrderEmailService
{
    /**
     * Send order confirmation emails to customer and branch manager
     */
    public static function sendOrderConfirmationEmails(Order $order): void
    {
        try {
            // Load relationships
            $order->load(['user', 'branch.user', 'items.product']);

            // Send email to customer
            if ($order->user && $order->user->email) {
                Mail::to($order->user->email)->send(
                    new OrderConfirmedMail($order, 'customer', $order->user->name)
                );
                
                Log::info('Order confirmation email sent to customer', [
                    'order_id' => $order->id,
                    'customer_email' => $order->user->email,
                    'customer_name' => $order->user->name
                ]);
            }

            // Send email to branch manager
            if ($order->branch && $order->branch->user && $order->branch->user->email) {
                Mail::to($order->branch->user->email)->send(
                    new OrderConfirmedMail($order, 'branch_manager', $order->branch->user->name)
                );
                
                Log::info('Order confirmation email sent to branch manager', [
                    'order_id' => $order->id,
                    'branch_manager_email' => $order->branch->user->email,
                    'branch_manager_name' => $order->branch->user->name,
                    'branch_name' => $order->branch->name
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Failed to send order confirmation emails', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
} 