<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserDeviceToken;
use App\Services\FirebaseNotificationService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class NotificationController extends Controller
{
    protected $firebaseService;

    public function __construct(FirebaseNotificationService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }

    /**
     * Register device token for push notifications
     */
    public function registerDevice(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'device_token' => 'required|string|max:255',
                'device_type' => 'required|in:android,ios,web',
                'app_version' => 'nullable|string|max:20',
                'device_model' => 'nullable|string|max:100'
            ]);

            $user = auth()->user();

            // Check if token already exists for this user
            $existingToken = UserDeviceToken::where('user_id', $user->id)
                ->where('device_token', $validated['device_token'])
                ->first();

            if ($existingToken) {
                // Update existing token
                $existingToken->update([
                    'device_type' => $validated['device_type'],
                    'app_version' => $validated['app_version'],
                    'device_model' => $validated['device_model'],
                    'is_active' => true,
                    'last_used_at' => now()
                ]);

                return response()->json([
                    'status' => 'success',
                    'message' => 'Device token updated successfully',
                    'data' => [
                        'device_token' => $existingToken->device_token,
                        'device_type' => $existingToken->device_type
                    ]
                ]);
            }

            // Create new device token
            $deviceToken = UserDeviceToken::create([
                'user_id' => $user->id,
                'device_token' => $validated['device_token'],
                'device_type' => $validated['device_type'],
                'app_version' => $validated['app_version'],
                'device_model' => $validated['device_model'],
                'is_active' => true,
                'last_used_at' => now()
            ]);

            Log::info('Device token registered successfully', [
                'user_id' => $user->id,
                'device_token' => $deviceToken->device_token,
                'device_type' => $deviceToken->device_type
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Device token registered successfully',
                'data' => [
                    'device_token' => $deviceToken->device_token,
                    'device_type' => $deviceToken->device_type
                ]
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error registering device token', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to register device token'
            ], 500);
        }
    }

    /**
     * Unregister device token
     */
    public function unregisterDevice(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'device_token' => 'required|string|max:255'
            ]);

            $user = auth()->user();

            $deviceToken = UserDeviceToken::where('user_id', $user->id)
                ->where('device_token', $validated['device_token'])
                ->first();

            if (!$deviceToken) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Device token not found'
                ], 404);
            }

            $deviceToken->deactivate();

            Log::info('Device token unregistered successfully', [
                'user_id' => $user->id,
                'device_token' => $deviceToken->device_token
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Device token unregistered successfully'
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error unregistering device token', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to unregister device token'
            ], 500);
        }
    }

    /**
     * Get user's device tokens
     */
    public function getDeviceTokens(): JsonResponse
    {
        try {
            $user = auth()->user();
            $deviceTokens = $user->activeDeviceTokens()
                ->select(['id', 'device_token', 'device_type', 'app_version', 'device_model', 'last_used_at'])
                ->get();

            return response()->json([
                'status' => 'success',
                'data' => [
                    'device_tokens' => $deviceTokens,
                    'total_count' => $deviceTokens->count()
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching device tokens', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch device tokens'
            ], 500);
        }
    }

    /**
     * Test notification to user's devices or specific device token
     */
    public function testNotification(Request $request): JsonResponse
    {
        try {
            $user = auth()->user();
            
            // Check if a specific device token is provided for testing
            $deviceToken = $request->input('device_token');
            $title = $request->input('title', 'Test Notification');
            $body = $request->input('body', 'This is a test notification from your app!');
            
            if ($deviceToken) {
                // Send to specific device token
                Log::info('Sending test notification to specific device token', [
                    'user_id' => $user->id,
                    'device_token' => $deviceToken,
                    'title' => $title,
                    'body' => $body
                ]);
                
                $result = $this->firebaseService->sendToDevice(
                    $deviceToken,
                    $title,
                    $body,
                    [
                        'type' => 'test',
                        'user_id' => $user->id,
                        'timestamp' => now()->toISOString()
                    ]
                );
                
                if ($result) {
                    return response()->json([
                        'status' => 'success',
                        'message' => 'Test notification sent successfully to device token',
                        'data' => [
                            'device_token' => $deviceToken,
                            'title' => $title,
                            'body' => $body
                        ]
                    ]);
                } else {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Failed to send test notification to device token'
                    ], 400);
                }
            } else {
                // Send to user's registered devices
                Log::info('Sending test notification to user\'s registered devices', [
                    'user_id' => $user->id,
                    'title' => $title,
                    'body' => $body
                ]);
                
                $result = $this->firebaseService->sendToUserLatestDevice(
                    $user->id,
                    $title,
                    $body,
                    [
                        'type' => 'test',
                        'timestamp' => now()->toISOString()
                    ]
                );

                if ($result) {
                    return response()->json([
                        'status' => 'success',
                        'message' => 'Test notification sent successfully to user devices'
                    ]);
                } else {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Failed to send test notification. Check if you have registered device tokens.'
                    ], 400);
                }
            }

        } catch (\Exception $e) {
            Log::error('Error sending test notification', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to send test notification: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update device token last used timestamp
     */
    public function updateLastUsed(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'device_token' => 'required|string|max:255'
            ]);

            $user = auth()->user();

            $deviceToken = UserDeviceToken::where('user_id', $user->id)
                ->where('device_token', $validated['device_token'])
                ->first();

            if ($deviceToken) {
                $deviceToken->updateLastUsed();
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Last used timestamp updated'
            ]);

        } catch (\Exception $e) {
            Log::error('Error updating last used timestamp', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update timestamp'
            ], 500);
        }
    }
} 