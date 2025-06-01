<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Msg91Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class RegisterController extends Controller
{
    protected $msg91Service;

    public function __construct(Msg91Service $msg91Service)
    {
        $this->msg91Service = $msg91Service;
    }

    /**
     * Send OTP to the given phone number
     */
    public function sendOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|regex:/^[0-9]{10}$/'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid phone number format'
            ], 422);
        }

        // Check if phone number is already registered
        if (User::where('phone', $request->phone)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'This phone number is already registered'
            ], 422);
        }

        // Generate OTP
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        
        // Store OTP in cache for 10 minutes
        Cache::put('otp_' . $request->phone, $otp, now()->addMinutes(10));

        // Send OTP via MSG91
        $result = $this->msg91Service->sendOtp($request->phone, $otp);

        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'message' => $result['message']
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'OTP sent successfully',
            'otp' => $otp
        ]);
    }

    /**
     * Verify OTP for the given phone number
     */
    public function verifyOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|regex:/^[0-9]{10}$/',
            'otp' => 'required|digits:6'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid input'
            ], 422);
        }

        // Verify OTP with MSG91
        $result = $this->msg91Service->verifyOtp($request->phone, $request->otp);

        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'message' => $result['message']
            ], 422);
        }

        // Mark phone as verified in cache
        Cache::put('verified_phone_' . $request->phone, true, now()->addMinutes(30));

        return response()->json([
            'success' => true,
            'message' => 'Phone number verified successfully'
        ]);
    }

    /**
     * Handle user registration
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|regex:/^[0-9]{10}$/|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        // Check if phone is verified
        if (!Cache::get('verified_phone_' . $request->phone)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Please verify your phone number first'
            ], 422);
        }

        // Create user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
        ]);

        // Clear OTP and verification cache
        Cache::forget('otp_' . $request->phone);
        Cache::forget('verified_phone_' . $request->phone);

        // Log the user in
        auth()->login($user);

        return response()->json([
            'status' => 'success',
            'message' => 'Registration successful'
        ]);
    }
} 