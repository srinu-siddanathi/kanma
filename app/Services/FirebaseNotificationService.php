<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserDeviceToken;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Google\Auth\Credentials\ServiceAccountCredentials;
use Google\Auth\Cache\SymfonyCache;

class FirebaseNotificationService
{
    protected $projectId;
    protected $serviceAccountPath;
    protected $serviceAccountData;
    protected $accessToken;
    protected $tokenExpiry;
    protected $batchSize = 500; // FCM v1 allows max 500 messages per request

    public function __construct()
    {
        $this->projectId = config('services.firebase.project_id');
        $this->serviceAccountPath = config('services.firebase.service_account_path');
        
        // Try to load service account data from environment variable first
        $this->loadServiceAccountData();
        
        // Initialize access token
        $this->initializeAccessToken();
    }

    /**
     * Load service account data from file or environment variable
     */
    protected function loadServiceAccountData(): void
    {
        // First try to load from file (recommended for file-based approach)
        if (file_exists($this->serviceAccountPath)) {
            try {
                $this->serviceAccountData = json_decode(file_get_contents($this->serviceAccountPath), true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    Log::info('Firebase service account loaded from file: ' . $this->serviceAccountPath);
                    return;
                } else {
                    Log::warning('Invalid JSON in Firebase service account file', [
                        'path' => $this->serviceAccountPath,
                        'error' => json_last_error_msg()
                    ]);
                }
            } catch (\Exception $e) {
                Log::warning('Failed to load Firebase service account from file', [
                    'path' => $this->serviceAccountPath,
                    'error' => $e->getMessage()
                ]);
            }
        }

        // Fallback to environment variable if file is not available
        $serviceAccountJson = env('FIREBASE_SERVICE_ACCOUNT_JSON');
        
        if ($serviceAccountJson) {
            try {
                $this->serviceAccountData = json_decode($serviceAccountJson, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    Log::info('Firebase service account loaded from environment variable');
                    return;
                }
            } catch (\Exception $e) {
                Log::warning('Failed to parse FIREBASE_SERVICE_ACCOUNT_JSON from environment', [
                    'error' => $e->getMessage()
                ]);
            }
        }

        // If neither method works, log error but don't throw yet
        Log::error('Firebase service account data not available from file or environment');
    }

    /**
     * Initialize OAuth2 access token for Firebase
     */
    protected function initializeAccessToken(): void
    {
        try {
            // Check if we have a valid cached token
            $cachedToken = Cache::get('firebase_access_token');
            if ($cachedToken && $cachedToken['expires_at'] > time()) {
                $this->accessToken = $cachedToken['token'];
                $this->tokenExpiry = $cachedToken['expires_at'];
                return;
            }

            // Get new access token
            $this->refreshAccessToken();
        } catch (\Exception $e) {
            Log::error('Failed to initialize Firebase access token', [
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Refresh the OAuth2 access token
     */
    protected function refreshAccessToken(): void
    {
        try {
            if (!$this->serviceAccountData) {
                throw new \Exception('Firebase service account data not available. Please configure FIREBASE_SERVICE_ACCOUNT_JSON in your .env file or ensure the service account file is accessible.');
            }

            $credentials = new ServiceAccountCredentials(
                'https://www.googleapis.com/auth/firebase.messaging',
                $this->serviceAccountData
            );

            $token = $credentials->fetchAuthToken();
            
            if (isset($token['access_token'])) {
                $this->accessToken = $token['access_token'];
                $this->tokenExpiry = time() + ($token['expires_in'] ?? 3600);
                
                // Cache the token for reuse
                Cache::put('firebase_access_token', [
                    'token' => $this->accessToken,
                    'expires_at' => $this->tokenExpiry
                ], $this->tokenExpiry - time() - 300); // Cache 5 minutes less than expiry
                
                Log::info('Firebase access token refreshed successfully');
            } else {
                throw new \Exception('Failed to obtain access token from service account');
            }
        } catch (\Exception $e) {
            Log::error('Failed to refresh Firebase access token', [
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Ensure we have a valid access token
     */
    protected function ensureValidToken(): void
    {
        if (!$this->accessToken || $this->tokenExpiry <= time()) {
            $this->refreshAccessToken();
        }
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
     * Send notification to user's latest device only
     */
    public function sendToUserLatestDevice(int $userId, string $title, string $body, array $data = []): bool
    {
        try {
            $user = User::find($userId);
            if (!$user) {
                Log::warning('User not found for notification', ['user_id' => $userId]);
                return false;
            }

            // Get the latest active device token
            $latestDeviceToken = $user->activeDeviceTokens()
                ->orderBy('last_used_at', 'desc')
                ->orderBy('created_at', 'desc')
                ->first();

            if (!$latestDeviceToken) {
                Log::info('No active device tokens found for user', ['user_id' => $userId]);
                return true; // Not an error, just no devices to send to
            }

            Log::info('Sending notification to user\'s latest device', [
                'user_id' => $userId,
                'device_token' => substr($latestDeviceToken->device_token, 0, 20) . '...',
                'title' => $title
            ]);

            return $this->sendToDevice($latestDeviceToken->device_token, $title, $body, $data);
        } catch (\Exception $e) {
            Log::error('Error sending notification to user\'s latest device', [
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
            $this->ensureValidToken();

            $payload = [
                'message' => [
                    'topic' => $topic,
                    'notification' => [
                        'title' => $title,
                        'body' => $body
                    ],
                    'data' => $this->sanitizeDataPayload($data),
                    'android' => [
                        'priority' => 'high',
                        'notification' => [
                            'sound' => 'default',
                            'channel_id' => 'default'
                        ]
                    ],
                    'apns' => [
                        'headers' => [
                            'apns-priority' => '10'
                        ],
                        'payload' => [
                            'aps' => [
                                'sound' => 'default',
                                'badge' => 1,
                                'alert' => [
                                    'title' => $title,
                                    'body' => $body
                                ]
                            ]
                        ]
                    ]
                ]
            ];

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->accessToken,
                'Content-Type' => 'application/json'
            ])->post("https://fcm.googleapis.com/v1/projects/{$this->projectId}/messages:send", $payload);

            if ($response->successful()) {
                $result = $response->json();
                Log::info('FCM v1 topic notification sent successfully', [
                    'topic' => $topic,
                    'message_id' => $result['name'] ?? null
                ]);
                return true;
            } else {
                Log::error('FCM v1 topic notification failed', [
                    'topic' => $topic,
                    'response' => $response->body(),
                    'status' => $response->status()
                ]);
                return false;
            }
        } catch (\Exception $e) {
            Log::error('Error sending FCM v1 topic notification', [
                'topic' => $topic,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Send a batch of notifications using FCM v1 API
     */
    protected function sendBatch(array $deviceTokens, string $title, string $body, array $data = []): bool
    {
        try {
            $this->ensureValidToken();

            $successCount = 0;
            $failureCount = 0;

            // Send individual messages for each device token
            foreach ($deviceTokens as $token) {
                $payload = [
                    'message' => [
                        'token' => $token,
                        'notification' => [
                            'title' => $title,
                            'body' => $body
                        ],
                        'data' => $this->sanitizeDataPayload($data),
                        'android' => [
                            'priority' => 'high',
                            'notification' => [
                                'sound' => 'default',
                                'channel_id' => 'default'
                            ]
                        ],
                        'apns' => [
                            'headers' => [
                                'apns-priority' => '10'
                            ],
                            'payload' => [
                                'aps' => [
                                    'sound' => 'default',
                                    'badge' => 1,
                                    'alert' => [
                                        'title' => $title,
                                        'body' => $body
                                    ]
                                ]
                            ]
                        ]
                    ]
                ];

                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $this->accessToken,
                    'Content-Type' => 'application/json'
                ])->post("https://fcm.googleapis.com/v1/projects/{$this->projectId}/messages:send", $payload);

                if ($response->successful()) {
                    $result = $response->json();
                    if (isset($result['name'])) {
                        $successCount++;
                        Log::info('FCM v1 notification sent successfully', [
                            'device_token' => substr($token, 0, 20) . '...',
                            'message_id' => $result['name']
                        ]);
                    } else {
                        $failureCount++;
                        Log::warning('FCM v1 notification failed', [
                            'device_token' => substr($token, 0, 20) . '...',
                            'response' => $result
                        ]);
                    }
                } else {
                    $failureCount++;
                    Log::error('FCM v1 notification failed', [
                        'device_token' => substr($token, 0, 20) . '...',
                        'response' => $response->body(),
                        'status' => $response->status()
                    ]);
                }
            }

            Log::info('FCM v1 batch notification completed', [
                'success_count' => $successCount,
                'failure_count' => $failureCount,
                'total_count' => count($deviceTokens)
            ]);

            return $successCount > 0;
        } catch (\Exception $e) {
            Log::error('Error sending FCM v1 batch notification', [
                'error' => $e->getMessage(),
                'device_count' => count($deviceTokens)
            ]);
            return false;
        }
    }

    /**
     * Ensure FCM data payload values are strings as required by FCM
     */
    protected function sanitizeDataPayload(array $data): array
    {
        $sanitized = [];
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $sanitized[$key] = json_encode($value);
            } elseif (is_null($value)) {
                $sanitized[$key] = '';
            } else {
                $sanitized[$key] = (string) $value;
            }
        }
        return $sanitized;
    }

    /**
     * Handle failed tokens by deactivating them
     */
    protected function handleFailedTokens(array $deviceTokens, array $responses): void
    {
        foreach ($responses as $index => $response) {
            if (!isset($response['name']) && isset($response['error'])) {
                $error = $response['error'];
                $errorCode = $error['code'] ?? null;
                
                // Check for token-related errors
                if ($errorCode === 3 || // INVALID_ARGUMENT
                    $errorCode === 5 || // NOT_FOUND
                    $errorCode === 7) { // PERMISSION_DENIED
                    
                    $failedToken = $deviceTokens[$index];
                    
                    // Deactivate the failed token
                    UserDeviceToken::where('device_token', $failedToken)
                        ->update(['is_active' => false]);

                    Log::info('Deactivated failed device token', [
                        'device_token' => $failedToken,
                        'error_code' => $errorCode,
                        'error_message' => $error['message'] ?? 'Unknown error'
                    ]);
                }
            }
        }
    }

    /**
     * Subscribe user to a topic using FCM v1 API
     */
    public function subscribeToTopic(array $deviceTokens, string $topic): bool
    {
        try {
            $this->ensureValidToken();

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->accessToken,
                'Content-Type' => 'application/json'
            ])->post("https://iid.googleapis.com/v1/projects/{$this->projectId}/topics/{$topic}:batchAdd", [
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
                    'response' => $response->body(),
                    'status' => $response->status()
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
     * Unsubscribe user from a topic using FCM v1 API
     */
    public function unsubscribeFromTopic(array $deviceTokens, string $topic): bool
    {
        try {
            $this->ensureValidToken();

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->accessToken,
                'Content-Type' => 'application/json'
            ])->post("https://iid.googleapis.com/v1/projects/{$this->projectId}/topics/{$topic}:batchRemove", [
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
                    'response' => $response->body(),
                    'status' => $response->status()
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