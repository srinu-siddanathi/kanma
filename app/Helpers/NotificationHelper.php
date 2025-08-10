<?php

namespace App\Helpers;

use App\Services\FirebaseNotificationService;
use Illuminate\Support\Facades\Log;

class NotificationHelper
{
    protected static $firebaseService;

    /**
     * Get Firebase service instance
     */
    protected static function getFirebaseService(): FirebaseNotificationService
    {
        if (!self::$firebaseService) {
            self::$firebaseService = app(FirebaseNotificationService::class);
        }
        return self::$firebaseService;
    }

    /**
     * Send order status update notification
     */
    public static function sendOrderStatusUpdate(int $userId, int $orderId, string $status): bool
    {
        try {
            $statusMessages = [
                'pending' => 'Your order has been placed successfully!',
                'confirmed' => 'Your order has been confirmed.',
                'processing' => 'Your order is updated to processing.',
                'ready' => 'Your order is ready for pickup/delivery!',
                'out_for_delivery' => 'Your order is out for delivery!',
                'delivered' => 'Your order has been delivered.',
                'cancelled' => 'Your order has been cancelled.',
                'failed' => 'Your order could not be processed.'
            ];

            $message = $statusMessages[$status] ?? "Your order status has been updated to: {$status}";

            return self::getFirebaseService()->sendToUserLatestDevice(
                $userId,
                'Order Status Update',
                $message,
                [
                    'type' => 'order_status_update',
                    'order_id' => $orderId,
                    'status' => $status,
                    'timestamp' => now()->toISOString()
                ]
            );
        } catch (\Exception $e) {
            Log::error('Error sending order status notification', [
                'user_id' => $userId,
                'order_id' => $orderId,
                'status' => $status,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Send payment success notification
     */
    public static function sendPaymentSuccess(int $userId, int $orderId, float $amount): bool
    {
        try {
            return self::getFirebaseService()->sendToUserLatestDevice(
                $userId,
                'Payment Successful',
                "Payment of ₹{$amount} for order #{$orderId} has been processed successfully.",
                [
                    'type' => 'payment_success',
                    'order_id' => $orderId,
                    'amount' => $amount,
                    'timestamp' => now()->toISOString()
                ]
            );
        } catch (\Exception $e) {
            Log::error('Error sending payment success notification', [
                'user_id' => $userId,
                'order_id' => $orderId,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Send payment failure notification
     */
    public static function sendPaymentFailure(int $userId, int $orderId, string $reason): bool
    {
        try {
            return self::getFirebaseService()->sendToUserLatestDevice(
                $userId,
                'Payment Failed',
                "Payment for order #{$orderId} failed. Reason: {$reason}",
                [
                    'type' => 'payment_failure',
                    'order_id' => $orderId,
                    'reason' => $reason,
                    'timestamp' => now()->toISOString()
                ]
            );
        } catch (\Exception $e) {
            Log::error('Error sending payment failure notification', [
                'user_id' => $userId,
                'order_id' => $orderId,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Send delivery update notification
     */
    public static function sendDeliveryUpdate(int $userId, int $orderId, string $status, string $deliveryBoyName = null): bool
    {
        try {
            $message = "Your order #{$orderId} delivery status: {$status}";
            if ($deliveryBoyName) {
                $message .= " - {$deliveryBoyName}";
            }

            return self::getFirebaseService()->sendToUserLatestDevice(
                $userId,
                'Delivery Update',
                $message,
                [
                    'type' => 'delivery_update',
                    'order_id' => $orderId,
                    'status' => $status,
                    'delivery_boy_name' => $deliveryBoyName,
                    'timestamp' => now()->toISOString()
                ]
            );
        } catch (\Exception $e) {
            Log::error('Error sending delivery update notification', [
                'user_id' => $userId,
                'order_id' => $orderId,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Send promotional notification
     */
    public static function sendPromotionalNotification(int $userId, string $title, string $message, array $data = []): bool
    {
        try {
            return self::getFirebaseService()->sendToUserLatestDevice(
                $userId,
                $title,
                $message,
                array_merge($data, [
                    'type' => 'promotional',
                    'timestamp' => now()->toISOString()
                ])
            );
        } catch (\Exception $e) {
            Log::error('Error sending promotional notification', [
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Send wallet update notification
     */
    public static function sendWalletUpdate(int $userId, float $amount, string $type, string $description): bool
    {
        try {
            $title = $type === 'credit' ? 'Wallet Credited' : 'Wallet Debited';
            $message = "₹{$amount} has been {$type}ed to your wallet. {$description}";

            return self::getFirebaseService()->sendToUserLatestDevice(
                $userId,
                $title,
                $message,
                [
                    'type' => 'wallet_update',
                    'amount' => $amount,
                    'transaction_type' => $type,
                    'description' => $description,
                    'timestamp' => now()->toISOString()
                ]
            );
        } catch (\Exception $e) {
            Log::error('Error sending wallet update notification', [
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Send coupon notification
     */
    public static function sendCouponNotification(int $userId, string $couponCode, string $discount, string $validUntil): bool
    {
        try {
            return self::getFirebaseService()->sendToUserLatestDevice(
                $userId,
                'New Coupon Available!',
                "Use code {$couponCode} to get {$discount} off. Valid until {$validUntil}",
                [
                    'type' => 'coupon',
                    'coupon_code' => $couponCode,
                    'discount' => $discount,
                    'valid_until' => $validUntil,
                    'timestamp' => now()->toISOString()
                ]
            );
        } catch (\Exception $e) {
            Log::error('Error sending coupon notification', [
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Send general notification
     */
    public static function sendGeneralNotification(int $userId, string $title, string $message, array $data = []): bool
    {
        try {
            return self::getFirebaseService()->sendToUserLatestDevice(
                $userId,
                $title,
                $message,
                array_merge($data, [
                    'type' => 'general',
                    'timestamp' => now()->toISOString()
                ])
            );
        } catch (\Exception $e) {
            Log::error('Error sending general notification', [
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Send notification to multiple users (latest device only)
     */
    public static function sendToMultipleUsers(array $userIds, string $title, string $message, array $data = []): array
    {
        try {
            $results = [];
            foreach ($userIds as $userId) {
                $results[$userId] = self::getFirebaseService()->sendToUserLatestDevice(
                    $userId,
                    $title,
                    $message,
                    $data
                );
            }
            return $results;
        } catch (\Exception $e) {
            Log::error('Error sending notification to multiple users', [
                'user_count' => count($userIds),
                'error' => $e->getMessage()
            ]);
            return array_fill_keys($userIds, false);
        }
    }

    /**
     * Send notification to topic
     */
    public static function sendToTopic(string $topic, string $title, string $message, array $data = []): bool
    {
        try {
            return self::getFirebaseService()->sendToTopic($topic, $title, $message, $data);
        } catch (\Exception $e) {
            Log::error('Error sending topic notification', [
                'topic' => $topic,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
} 