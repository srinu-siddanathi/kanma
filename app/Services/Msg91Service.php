<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class Msg91Service
{
    protected $authKey;
    protected $testMode;
    protected $baseUrl = 'https://api.msg91.com/api/v5/otp';
    protected $flowBaseUrl = 'https://control.msg91.com/api/v5/flow';

    public function __construct()
    {
        $this->authKey = config('services.msg91.auth_key');
        $this->testMode = config('services.msg91.test_mode', true);
    }

    /**
     * Send OTP to the given phone number
     *
     * @param string $phone
     * @param string $otp
     * @return array
     */
    public function sendOtp(string $phone, string $otp, string $templateId): array
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
                'template_id' => $templateId,
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

    /**
     * Send an SMS using MSG91 Flow (transactional template)
     *
     * @param string $phone E.164 without + (e.g., 9198XXXXXXXX)
     * @param string $flowId MSG91 flow/template ID
     * @param array $variables Key-value pairs for template variables
     * @return array
     */
    public function sendFlowSms(string $phone, string $flowId, array $variables = []): array
    {
        try {
            if ($this->testMode) {
                Log::info('MSG91 Test Mode - Flow SMS Sent', [
                    'phone' => $phone,
                    'flow_id' => $flowId,
                    'variables' => $variables,
                ]);
                return [
                    'success' => true,
                    'message' => 'SMS sent successfully (Test Mode)'
                ];
            }

            $payload = array_merge([
                'flow_id' => $flowId,
                'mobiles' => $phone,
            ], $variables);

            $response = Http::withHeaders([
                'authkey' => $this->authKey,
                'Content-Type' => 'application/json'
            ])->post($this->flowBaseUrl, $payload);

            $result = $response->json();

            if ($response->successful()) {
                return [
                    'success' => true,
                    'message' => 'SMS sent successfully'
                ];
            }

            Log::error('MSG91 Flow API Error', [
                'response' => $result,
                'phone' => $phone,
                'flow_id' => $flowId,
            ]);

            return [
                'success' => false,
                'message' => $result['message'] ?? 'Failed to send SMS'
            ];
        } catch (\Exception $e) {
            Log::error('MSG91 Flow Service Exception', [
                'message' => $e->getMessage(),
                'phone' => $phone,
                'flow_id' => $flowId,
            ]);
            return [
                'success' => false,
                'message' => 'Failed to send SMS. Please try again later.'
            ];
        }
    }

    /**
     * Convenience method to send order status SMS
     */
    public function sendOrderStatusSms(string $phone, string $orderId, string $status, ?string $shopName = null): array
    {
        $flowId = config('services.msg91.order_status_flow_id');
        $variables = [
            'order_id' => $orderId,
            'status' => $status,
        ];
        if ($shopName) {
            $variables['shop_name'] = $shopName;
        }
        return $this->sendFlowSms($phone, $flowId, $variables);
    }
} 