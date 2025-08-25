<?php

namespace App\Services;

use App\Models\Order;
use App\Mail\OrderPlacedMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class OrderPlacementEmailService
{
    /**
     * Send order placement emails to customer and branch manager for COD orders
     */
    public static function sendOrderPlacementEmails(Order $order): void
    {
        try {
            // Load relationships
            $order->load(['user', 'branch.user', 'items.product']);

            // Send email to customer
            if ($order->user && $order->user->email) {
                $subject = 'Order #' . $order->id . ' Placed Successfully - KANMA';
                
                // Log email before sending
                $emailLog = \App\Services\EmailLogService::logOrderPlacementEmail(
                    'customer',
                    $order->user->email,
                    $order->user->name,
                    $order->id,
                    $subject
                );

                try {
                    Mail::to($order->user->email)->send(
                        new OrderPlacedMail($order, 'customer', $order->user->name)
                    );
                    
                    // Mark as sent
                    \App\Services\EmailLogService::markAsSent($emailLog);
                    
                    Log::info('Order placement email sent to customer', [
                        'order_id' => $order->id,
                        'customer_email' => $order->user->email,
                        'customer_name' => $order->user->name,
                        'payment_method' => $order->payment_method,
                        'email_log_id' => $emailLog->id
                    ]);
                } catch (\Exception $e) {
                    \App\Services\EmailLogService::markAsFailed($emailLog, $e->getMessage());
                    throw $e;
                }
            }

            // Send email to branch manager
            if ($order->branch && $order->branch->user && $order->branch->user->email) {
                $subject = 'New COD Order #' . $order->id . ' Received - KANMA';
                
                // Log email before sending
                $emailLog = \App\Services\EmailLogService::logOrderPlacementEmail(
                    'branch_manager',
                    $order->branch->user->email,
                    $order->branch->user->name,
                    $order->id,
                    $subject
                );

                try {
                    Mail::to($order->branch->user->email)->send(
                        new OrderPlacedMail($order, 'branch_manager', $order->branch->user->name)
                    );
                    
                    // Mark as sent
                    \App\Services\EmailLogService::markAsSent($emailLog);
                    
                    Log::info('Order placement email sent to branch manager', [
                        'order_id' => $order->id,
                        'branch_manager_email' => $order->branch->user->email,
                        'branch_manager_name' => $order->branch->user->name,
                        'branch_name' => $order->branch->name,
                        'payment_method' => $order->payment_method,
                        'email_log_id' => $emailLog->id
                    ]);
                } catch (\Exception $e) {
                    \App\Services\EmailLogService::markAsFailed($emailLog, $e->getMessage());
                    throw $e;
                }
            }

        } catch (\Exception $e) {
            Log::error('Failed to send order placement emails', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Send order placement emails only for COD orders
     */
    public static function sendCodOrderPlacementEmails(Order $order): void
    {
        // Only send immediate emails for COD orders
        if ($order->payment_method === 'cod') {
            self::sendOrderPlacementEmails($order);
        }
    }
} 