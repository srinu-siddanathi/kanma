<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Otp;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Handle an authentication attempt.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (!Auth::attempt($credentials)) {
            return response()->json([
                'message' => 'The provided credentials do not match our records.',
            ], 401);
        }

        $user = Auth::user();
        $token = $user->createToken('auth-token')->plainTextToken;

        // Handle device token registration if provided
        $deviceTokenRegistered = false;
        if ($request->has('device_token')) {
            $deviceTokenRegistered = $this->registerDeviceToken($user, $request);
        }

        return response()->json([
            'token' => $token,
            'user' => $user,
            'device_token_registered' => $deviceTokenRegistered,
        ]);
    }

    public function logout(Request $request)
    {
        $user = $request->user();
        
        // Handle device token cleanup if provided
        if ($request->has('device_token')) {
            $this->unregisterDeviceToken($user, $request->device_token);
        }
        
        $user->currentAccessToken()->delete();
        
        return response()->json([
            'status' => 'success',
            'message' => 'Logged out successfully',
        ]);
    }

    public function sendOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required|string|max:20',
        ]);

        // Check if user exists
        $userExists = User::where('phone', $request->phone)->exists();

        // Generate OTP
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        
        // Store OTP
        Otp::create([
            'phone' => $request->phone,
            'otp' => $otp,
            'expires_at' => Carbon::now()->addMinutes(5)
        ]);

        // In production, you would integrate with an SMS service here
        return response()->json([
            'status' => 'success',
            'message' => 'OTP sent successfully',
            'is_new_user' => !$userExists,
            'otp' => $otp, // Remove this in production
        ]);
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'otp' => 'required|string|size:6',
        ]);

        $otp = Otp::where('phone', $request->phone)
            ->where('otp', $request->otp)
            ->where('verified', false)
            ->where('expires_at', '>', Carbon::now())
            ->latest()
            ->first();

        if (!$otp) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid or expired OTP'
            ], 422);
        }

        // Mark OTP as verified
        $otp->update(['verified' => true]);

        // Check if user exists
        $user = User::where('phone', $request->phone)->first();
        
        if (!$user) {
            // Create a temporary user
            $user = User::create([
                'phone' => $request->phone,
                'role' => 'customer',
                'is_active' => true,
                'name' => 'Guest User',
                'email' => 'user_' . str_replace(['+', ' '], '', $request->phone) . '@temp.com',
                'password' => bcrypt(Str::random(16))
            ]);
        }

        $token = $user->createToken('auth-token')->plainTextToken;

        // Handle device token registration if provided
        $deviceTokenRegistered = false;
        if ($request->has('device_token')) {
            $deviceTokenRegistered = $this->registerDeviceToken($user, $request);
        }

        return response()->json([
            'status' => 'success',
            'token' => $token,
            'user' => $user->fresh(),
            'is_new_user' => !$user->profile_completed,
            'device_token_registered' => $deviceTokenRegistered,
        ]);
    }

    public function completeProfile(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . auth()->id(),
                'dob' => 'required|date|before:today',
                'gender' => 'required|in:male,female,other',
                'referral_code' => 'nullable|string|exists:users,referral_code'
            ]);

            $user = auth()->user();

            try {
                DB::beginTransaction();

                $updateData = [
                    'name' => $request->name,
                    'email' => $request->email,
                    'dob' => Carbon::parse($request->dob)->format('Y-m-d'),
                    'gender' => $request->gender,
                    'profile_completed' => true
                ];

                // Handle referral code
                if ($request->referral_code) {
                    $referringUser = User::where('referral_code', $request->referral_code)->first();
                    
                    // Check if user is trying to use their own referral code
                    if ($referringUser->id === $user->id) {
                        return response()->json([
                            'status' => 'error',
                            'message' => 'Cannot use own referral code',
                            'errors' => [
                                'referral_code' => ['You cannot use your own referral code.']
                            ]
                        ], 422);
                    }

                    $updateData['referred_by'] = $referringUser->id;
                    
                    // Here you can add any referral rewards logic
                    // For example:
                    // $referringUser->increment('wallet_balance', 50);
                    // $user->increment('wallet_balance', 25);
                }

                $user->update($updateData);

                DB::commit();

                $userData = $user->fresh()->only([
                    'id',
                    'name',
                    'phone',
                    'email',
                    'dob',
                    'gender',
                    'referral_code',
                    'profile_completed',
                    'wallet_balance'
                ]);

                return response()->json([
                    'status' => 'success',
                    'message' => 'Profile completed successfully',
                    'data' => [
                        'user' => $userData
                    ]
                ], 200);

            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json([
                    'status' => 'error',
                    'message' => 'An error occurred while completing profile',
                    'debug' => $e->getMessage() // Remove in production
                ], 500);
                throw $e;
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Profile completion error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while completing profile',
                'debug' => $e->getMessage() // Remove in production
            ], 500);
        }
    }

    private function generateReferralCode()
    {
        do {
            // Generate a random code
            $code = strtoupper(Str::random(6));
        } while (User::where('referral_code', $code)->exists());

        return $code;
    }

    /**
     * Register device token for push notifications
     */
    private function registerDeviceToken(User $user, Request $request): bool
    {
        try {
            $validated = $request->validate([
                'device_token' => 'required|string|max:255',
                'device_type' => 'nullable|in:android,ios,web',
                'app_version' => 'nullable|string|max:20',
                'device_model' => 'nullable|string|max:100'
            ]);

            // Set default device type if not provided
            $deviceType = $validated['device_type'] ?? 'android';

            // Check if token already exists for this user
            $existingToken = \App\Models\UserDeviceToken::where('user_id', $user->id)
                ->where('device_token', $validated['device_token'])
                ->first();

            if ($existingToken) {
                // Update existing token
                $existingToken->update([
                    'device_type' => $deviceType,
                    'app_version' => $validated['app_version'] ?? $existingToken->app_version,
                    'device_model' => $validated['device_model'] ?? $existingToken->device_model,
                    'is_active' => true,
                    'last_used_at' => now()
                ]);

                \Log::info('Device token updated during login', [
                    'user_id' => $user->id,
                    'device_token' => $existingToken->device_token,
                    'device_type' => $existingToken->device_type
                ]);

                return true;
            }

            // Create new device token
            \App\Models\UserDeviceToken::create([
                'user_id' => $user->id,
                'device_token' => $validated['device_token'],
                'device_type' => $deviceType,
                'app_version' => $validated['app_version'],
                'device_model' => $validated['device_model'],
                'is_active' => true,
                'last_used_at' => now()
            ]);

            \Log::info('Device token registered during login', [
                'user_id' => $user->id,
                'device_token' => $validated['device_token'],
                'device_type' => $deviceType
            ]);

            return true;

        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::warning('Device token validation failed during login', [
                'user_id' => $user->id,
                'errors' => $e->errors()
            ]);
            return false;
        } catch (\Exception $e) {
            \Log::error('Error registering device token during login', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Unregister device token during logout
     */
    private function unregisterDeviceToken(User $user, string $deviceToken): void
    {
        try {
            $deviceTokenModel = \App\Models\UserDeviceToken::where('user_id', $user->id)
                ->where('device_token', $deviceToken)
                ->first();

            if ($deviceTokenModel) {
                $deviceTokenModel->deactivate();
                
                \Log::info('Device token deactivated during logout', [
                    'user_id' => $user->id,
                    'device_token' => $deviceToken
                ]);
            }
        } catch (\Exception $e) {
            \Log::error('Error unregistering device token during logout', [
                'user_id' => $user->id,
                'device_token' => $deviceToken,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function register(Request $request): JsonResponse
    {
        try {
            // First check if phone exists in either column
            $existingUser = User::where('phone', $request->phone)
                ->first();

            if ($existingUser) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Phone number already registered',
                    'errors' => [
                        'phone' => ['This phone number is already registered. Please login instead.']
                    ]
                ], 422);
            }

            $validated = $request->validate([
                'username' => 'required|string|min:3|max:50|unique:users,username',
                'password' => 'required|string|min:6',
                'phone' => 'required|string|size:10'
            ]);

            // Store registration data in cache with expiry
            $registrationData = [
                'username' => $validated['username'],
                'password' => Hash::make($validated['password']),
                'phone' => $validated['phone'],
                'role' => 'customer',
                'is_active' => true,
                'profile_completed' => false
            ];
            
            // Generate OTP
            $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            
            // Store OTP and registration data
            $otpModel = Otp::create([
                'phone' => $validated['phone'],
                'otp' => $otp,
                'registration_data' => json_encode($registrationData),
                'type' => 'registration',
                'expires_at' => now()->addMinutes(10)
            ]);

            // Send OTP via SMS using Msg91Service (same as forgotPassword)
            $msg91Service = new \App\Services\Msg91Service();
            $phone = "91".$validated['phone'];
            $templateId = config('services.msg91.registration_template_id');
            $smsResult = $msg91Service->sendOtp($phone, $otp, $templateId);

            if (!$smsResult['success']) {
                \Log::error('SMS sending failed for registration', [
                    'phone' => $validated['phone'],
                    'error' => $smsResult['message'] ?? 'Unknown error'
                ]);

                return response()->json([
                    'status' => 'error',
                    'message' => 'Failed to send OTP. Please try again later.'
                ], 500);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'OTP sent successfully',
                'phone' => $validated['phone'],
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Registration error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Registration failed. Please try again.'
            ], 500);
        }
    }

    /**
     * Send OTP for forgot password functionality
     * This endpoint accepts mobile number and OTP from Android app
     * Verifies mobile number exists and sends OTP via SMS
     */
    public function forgotPassword(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'phone' => 'required|string|size:10',
                'otp' => 'required|string|size:6'
            ]);

            // Check if user exists with this phone number
            $user = User::where('phone', $validated['phone'])->first();

            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No account found with this mobile number. Please check the number or register.',
                    'requires_registration' => true
                ], 404);
            }

            // Store the OTP in database for tracking (optional, for audit purposes)
            Otp::create([
                'phone' => $validated['phone'],
                'otp' => $validated['otp'],
                'type' => 'forgot_password',
                'expires_at' => now()->addMinutes(10),
                'is_used' => false
            ]);

            // Send OTP via SMS using Msg91Service
            $msg91Service = new \App\Services\Msg91Service();
            $phone = "91".$validated['phone'];
            $templateId = config('services.msg91.reset_password_template_id');
            $smsResult = $msg91Service->sendOtp($phone, $validated['otp'], $templateId);

            if (!$smsResult['success']) {
                \Log::error('SMS sending failed for forgot password', [
                    'phone' => $validated['phone'],
                    'error' => $smsResult['message']
                ]);

                return response()->json([
                    'status' => 'error',
                    'message' => 'Failed to send OTP. Please try again later.'
                ], 500);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'OTP sent successfully to your mobile number',
                'data' => [
                    'phone' => $validated['phone'],
                    'user_exists' => true
                ]
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid input data',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Forgot password error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong. Please try again later.'
            ], 500);
        }
    }

    /**
     * Reset password after OTP verification (handled by Android app)
     * This endpoint is called after Android app verifies OTP locally
     */
    public function resetPassword(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'phone' => 'required|string|size:10',
                'otp' => 'required|string|size:6',
                'new_password' => 'required|string|min:6'
            ]);

            // Verify OTP from database (optional verification)
            $otpRecord = Otp::where('phone', $validated['phone'])
                ->where('otp', $validated['otp'])
                ->where('type', 'forgot_password')
                ->where('is_used', false)
                ->where('expires_at', '>', now())
                ->latest()
                ->first();

            if (!$otpRecord) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid or expired OTP'
                ], 422);
            }

            // Find user
            $user = User::where('phone', $validated['phone'])->first();

            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'User not found'
                ], 404);
            }

            // Update password
            $user->update([
                'password' => Hash::make($validated['new_password'])
            ]);

            // Mark OTP as used
            $otpRecord->update(['is_used' => true]);

            // Revoke all existing tokens for security
            $user->tokens()->delete();

            // Generate new token
            $token = $user->createToken('auth-token')->plainTextToken;

            return response()->json([
                'status' => 'success',
                'message' => 'Password reset successfully',
                'data' => [
                    'token' => $token,
                    'user' => $user->fresh()
                ]
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid input data',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Reset password error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to reset password. Please try again.'
            ], 500);
        }
    }

    public function verifyRegistrationOtp(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'phone' => 'required|string|size:10',
                'otp' => 'required|string|size:6',
            ]);

            $otpModel = Otp::where('phone', $validated['phone'])
                ->where('type', 'registration')
                ->where('otp', $validated['otp'])
                ->where('expires_at', '>', now())
                ->where('is_used', false)
                ->first();

            if (!$otpModel) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid or expired OTP'
                ], 400);
            }

            try {
                DB::beginTransaction();

                $registrationData = json_decode($otpModel->registration_data, true);
                $registrationData['referral_code'] = $this->generateReferralCode();
                
                $user = User::create($registrationData);
                $otpModel->update(['is_used' => true]);
                $token = $user->createToken('auth_token')->plainTextToken;

                // Handle device token registration if provided
                $deviceTokenRegistered = false;
                if ($request->has('device_token')) {
                    $deviceTokenRegistered = $this->registerDeviceToken($user, $request);
                }

                DB::commit();

                return response()->json([
                    'status' => 'success',
                    'message' => 'Registration successful',
                    'data' => [
                        'token' => $token,
                        'user' => [
                            'id' => $user->id,
                            'username' => $user->username,
                            'phone' => $user->phone,
                            'referral_code' => $user->referral_code,
                            'profile_completed' => false,
                            'created_at' => $user->created_at
                        ],
                        'device_token_registered' => $deviceTokenRegistered,
                    ]
                ]);

            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Registration verification error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Registration verification failed',
                'debug' => $e->getMessage() // Remove in production
            ], 500);
        }
    }
} 