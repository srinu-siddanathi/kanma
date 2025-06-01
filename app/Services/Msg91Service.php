<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class Msg91Service
{
    protected $authKey;
    protected $templateId;
    protected $testMode;
    protected $baseUrl = 'https://api.msg91.com/api/v5/otp';

    public function __construct()
    {
        $this->authKey = config('services.msg91.auth_key');
        $this->templateId = config('services.msg91.template_id');
        $this->testMode = config('services.msg91.test_mode', true);
    }

    /**
     * Send OTP to the given phone number
     *
     * @param string $phone
     * @param string $otp
     * @return array
     */
    public function sendOtp(string $phone, string $otp): array
    {
        try {
            // In test mode, we'll simulate a successful response
            if ($this->testMode) {
                Log::info('MSG91 Test Mode - OTP Sent', [
                    'phone' => $phone,
                    'otp' => $otp
                ]);

                return [
                    'success' => true,
                    'message' => 'OTP sent successfully (Test Mode)',
                    'otp' => $otp,
                    'debug_info' => [
                        'phone' => $phone,
                        'otp' => $otp,
                        'mode' => 'test'
                    ]
                ];
            }

            $response = Http::withHeaders([
                'authkey' => $this->authKey,
                'Content-Type' => 'application/json'
            ])->post($this->baseUrl, [
                'template_id' => $this->templateId,
                'mobile' => $phone,
                'otp' => $otp
            ]);

            $result = $response->json();

            if ($response->successful() && isset($result['type']) && $result['type'] === 'success') {
                return [
                    'success' => true,
                    'message' => 'OTP sent successfully'
                ];
            }

            Log::error('MSG91 API Error', [
                'response' => $result,
                'phone' => $phone
            ]);

            return [
                'success' => false,
                'message' => $result['message'] ?? 'Failed to send OTP'
            ];
        } catch (\Exception $e) {
            Log::error('MSG91 Service Exception', [
                'message' => $e->getMessage(),
                'phone' => $phone
            ]);

            return [
                'success' => false,
                'message' => 'Failed to send OTP. Please try again later.'
            ];
        }
    }

    /**
     * Verify OTP for the given phone number
     *
     * @param string $phone
     * @param string $otp
     * @return array
     */
    public function verifyOtp(string $phone, string $otp): array
    {
        try {
            // In test mode, we'll verify against the cached OTP
            if ($this->testMode) {
                $cachedOtp = Cache::get('otp_' . $phone);
                
                if (!$cachedOtp) {
                    return [
                        'success' => false,
                        'message' => 'OTP has expired. Please request a new one.'
                    ];
                }

                if ($cachedOtp !== $otp) {
                    return [
                        'success' => false,
                        'message' => 'Invalid OTP'
                    ];
                }

                Log::info('MSG91 Test Mode - OTP Verified', [
                    'phone' => $phone,
                    'otp' => $otp
                ]);

                return [
                    'success' => true,
                    'message' => 'OTP verified successfully (Test Mode)'
                ];
            }

            $response = Http::withHeaders([
                'authkey' => $this->authKey,
                'Content-Type' => 'application/json'
            ])->post($this->baseUrl . '/verify', [
                'mobile' => $phone,
                'otp' => $otp
            ]);

            $result = $response->json();

            if ($response->successful() && isset($result['type']) && $result['type'] === 'success') {
                return [
                    'success' => true,
                    'message' => 'OTP verified successfully'
                ];
            }

            Log::error('MSG91 Verify API Error', [
                'response' => $result,
                'phone' => $phone
            ]);

            return [
                'success' => false,
                'message' => $result['message'] ?? 'Invalid OTP'
            ];
        } catch (\Exception $e) {
            Log::error('MSG91 Verify Service Exception', [
                'message' => $e->getMessage(),
                'phone' => $phone
            ]);

            return [
                'success' => false,
                'message' => 'Failed to verify OTP. Please try again later.'
            ];
        }
    }
} 