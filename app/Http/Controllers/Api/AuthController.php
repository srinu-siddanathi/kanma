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

        return response()->json([
            'token' => $token,
            'user' => $user,
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        
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

        return response()->json([
            'status' => 'success',
            'token' => $token,
            'user' => $user->fresh(),
            'is_new_user' => !$user->profile_completed
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

            return response()->json([
                'status' => 'success',
                'message' => 'OTP sent successfully',
                'data' => [
                    'phone' => $validated['phone'],
                    'otp' => $otp // Remove this in production
                ]
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
                'message' => 'Registration failed',
                'debug' => $e->getMessage() // Remove in production
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
                        ]
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