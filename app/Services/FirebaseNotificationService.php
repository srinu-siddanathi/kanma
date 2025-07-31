<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserDeviceToken;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class FirebaseNotificationService
{
    protected $serverKey;
    protected $fcmUrl = 'https://fcm.googleapis.com/fcm/send';
    protected $batchSize = 1000; // FCM allows max 1000 tokens per request

    public function __construct()
    {
        $this->serverKey = config('services.firebase.server_key');
    }

    /**
     * Send notification to a specific user
     */
    public function sendToUser(int $userId, string $title, string $body, array $data = []): bool
    {
        try {
            $user = User::find($userId);
            if (!$user) {
                Log::warning('User not found for notification', ['user_id' => $userId]);
                return false;
            }

            $deviceTokens = $user->activeDeviceTokens()->pluck('device_token')->toArray();
            
            if (empty($deviceTokens)) {
                Log::info('No active device tokens found for user', ['user_id' => $userId]);
                return true; // Not an error, just no devices to send to
            }

            return $this->sendToMultipleDevices($deviceTokens, $title, $body, $data);
        } catch (\Exception $e) {
            Log::error('Error sending notification to user', [
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Send notification to multiple users
     */
    public function sendToMultipleUsers(array $userIds, string $title, string $body, array $data = []): array
    {
        $results = [];
        
        foreach ($userIds as $userId) {
            $results[$userId] = $this->sendToUser($userId, $title, $body, $data);
        }

        return $results;
    }

    /**
     * Send notification to a specific device token
     */
    public function sendToDevice(string $deviceToken, string $title, string $body, array $data = []): bool
    {
        return $this->sendToMultipleDevices([$deviceToken], $title, $body, $data);
    }

    /**
     * Send notification to multiple device tokens
     */
    public function sendToMultipleDevices(array $deviceTokens, string $title, string $body, array $data = []): bool
    {
        try {
            if (empty($deviceTokens)) {
                Log::warning('No device tokens provided for notification');
                return false;
            }

            // Remove duplicates and invalid tokens
            $deviceTokens = array_filter(array_unique($deviceTokens));
            
            if (empty($deviceTokens)) {
                Log::warning('No valid device tokens after filtering');
                return false;
            }

            // Split into batches if needed
            $batches = array_chunk($deviceTokens, $this->batchSize);
            $success = true;

            foreach ($batches as $batch) {
                $result = $this->sendBatch($batch, $title, $body, $data);
                if (!$result) {
                    $success = false;
                }
            }

            return $success;
        } catch (\Exception $e) {
            Log::error('Error sending notification to multiple devices', [
                'device_count' => count($deviceTokens),
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Send notification to a topic
     */
    public function sendToTopic(string $topic, string $title, string $body, array $data = []): bool
    {
        try {
            $payload = [
                'to' => '/topics/' . $topic,
                'notification' => [
                    'title' => $title,
                    'body' => $body,
                    'sound' => 'default',
                    'badge' => 1,
                ],
                'data' => $data,
                'priority' => 'high',
                'android' => [
                    'priority' => 'high',
                    'notification' => [
                        'sound' => 'default',
                        'channel_id' => 'default',
                    ]
                ],
                'apns' => [
                    'headers' => [
                        'apns-priority' => '10'
                    ],
                    'payload' => [
                        'aps' => [
                            'sound' => 'default',
                            'badge' => 1
                        ]
                    ]
                ]
            ];

            $response = Http::withHeaders([
                'Authorization' => 'key=' . $this->serverKey,
                'Content-Type' => 'application/json'
            ])->post($this->fcmUrl, $payload);

            if ($response->successful()) {
                $result = $response->json();
                Log::info('FCM topic notification sent successfully', [
                    'topic' => $topic,
                    'message_id' => $result['message_id'] ?? null
                ]);
                return true;
            } else {
                Log::error('FCM topic notification failed', [
                    'topic' => $topic,
                    'response' => $response->body()
                ]);
                return false;
            }
        } catch (\Exception $e) {
            Log::error('Error sending FCM topic notification', [
                'topic' => $topic,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Send a batch of notifications
     */
    protected function sendBatch(array $deviceTokens, string $title, string $body, array $data = []): bool
    {
        try {
            $payload = [
                'registration_ids' => $deviceTokens,
                'notification' => [
                    'title' => $title,
                    'body' => $body,
                    'sound' => 'default',
                    'badge' => 1,
                ],
                'data' => $data,
                'priority' => 'high',
                'android' => [
                    'priority' => 'high',
                    'notification' => [
                        'sound' => 'default',
                        'channel_id' => 'default',
                    ]
                ],
                'apns' => [
                    'headers' => [
                        'apns-priority' => '10'
                    ],
                    'payload' => [
                        'aps' => [
                            'sound' => 'default',
                            'badge' => 1
                        ]
                    ]
                ]
            ];

            $response = Http::withHeaders([
                'Authorization' => 'key=' . $this->serverKey,
                'Content-Type' => 'application/json'
            ])->post($this->fcmUrl, $payload);

            if ($response->successful()) {
                $result = $response->json();
                
                // Handle failed tokens
                if (isset($result['results'])) {
                    $this->handleFailedTokens($deviceTokens, $result['results']);
                }

                Log::info('FCM batch notification sent', [
                    'success_count' => $result['success'] ?? 0,
                    'failure_count' => $result['failure'] ?? 0,
                    'total_count' => count($deviceTokens)
                ]);

                return ($result['success'] ?? 0) > 0;
            } else {
                Log::error('FCM batch notification failed', [
                    'response' => $response->body(),
                    'device_count' => count($deviceTokens)
                ]);
                return false;
            }
        } catch (\Exception $e) {
            Log::error('Error sending FCM batch notification', [
                'error' => $e->getMessage(),
                'device_count' => count($deviceTokens)
            ]);
            return false;
        }
    }

    /**
     * Handle failed tokens by deactivating them
     */
    protected function handleFailedTokens(array $deviceTokens, array $results): void
    {
        foreach ($results as $index => $result) {
            if (isset($result['error']) && in_array($result['error'], [
                'NotRegistered',
                'InvalidRegistration',
                'MismatchSenderId'
            ])) {
                $failedToken = $deviceTokens[$index];
                
                // Deactivate the failed token
                UserDeviceToken::where('device_token', $failedToken)
                    ->update(['is_active' => false]);

                Log::info('Deactivated failed device token', [
                    'device_token' => $failedToken,
                    'error' => $result['error']
                ]);
            }
        }
    }

    /**
     * Subscribe user to a topic
     */
    public function subscribeToTopic(array $deviceTokens, string $topic): bool
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'key=' . $this->serverKey,
                'Content-Type' => 'application/json'
            ])->post('https://iid.googleapis.com/iid/v1:batchAdd', [
                'to' => '/topics/' . $topic,
                'registration_tokens' => $deviceTokens
            ]);

            if ($response->successful()) {
                Log::info('Successfully subscribed devices to topic', [
                    'topic' => $topic,
                    'device_count' => count($deviceTokens)
                ]);
                return true;
            } else {
                Log::error('Failed to subscribe devices to topic', [
                    'topic' => $topic,
                    'response' => $response->body()
                ]);
                return false;
            }
        } catch (\Exception $e) {
            Log::error('Error subscribing devices to topic', [
                'topic' => $topic,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Unsubscribe user from a topic
     */
    public function unsubscribeFromTopic(array $deviceTokens, string $topic): bool
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'key=' . $this->serverKey,
                'Content-Type' => 'application/json'
            ])->post('https://iid.googleapis.com/iid/v1:batchRemove', [
                'to' => '/topics/' . $topic,
                'registration_tokens' => $deviceTokens
            ]);

            if ($response->successful()) {
                Log::info('Successfully unsubscribed devices from topic', [
                    'topic' => $topic,
                    'device_count' => count($deviceTokens)
                ]);
                return true;
            } else {
                Log::error('Failed to unsubscribe devices from topic', [
                    'topic' => $topic,
                    'response' => $response->body()
                ]);
                return false;
            }
        } catch (\Exception $e) {
            Log::error('Error unsubscribing devices from topic', [
                'topic' => $topic,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
} 