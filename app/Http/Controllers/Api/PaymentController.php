<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    protected $juspayBaseUrl;
    protected $apiKey;

    public function __construct()
    {
        $this->juspayBaseUrl = config('services.juspay.base_url');
        $this->apiKey = config('services.juspay.api_key');
    }

    /**
     * Initialize a payment session
     */
    public function initializePayment(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'currency' => 'required|string|size:3',
            'order_id' => 'required|string|unique:payments,order_id',
            'customer_id' => 'required|string',
            'customer_email' => 'required|email',
            'customer_phone' => 'required|string',
            'return_url' => 'required|url',
        ]);

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Basic ' . base64_encode($this->apiKey . ':'),
                'Content-Type' => 'application/json',
            ])->post($this->juspayBaseUrl . '/payment/init', [
                'amount' => $request->amount,
                'currency' => $request->currency,
                'order_id' => $request->order_id,
                'customer_id' => $request->customer_id,
                'customer_email' => $request->customer_email,
                'customer_phone' => $request->customer_phone,
                'return_url' => $request->return_url,
                'payment_page_client_id' => config('services.juspay.client_id'),
            ]);

            if ($response->successful()) {
                return response()->json($response->json());
            }

            return response()->json([
                'message' => 'Payment initialization failed',
                'error' => $response->json()
            ], 400);

        } catch (\Exception $e) {
            Log::error('Payment initialization error: ' . $e->getMessage());
            return response()->json([
                'message' => 'Payment initialization failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get payment status
     */
    public function getPaymentStatus(Request $request)
    {
        $request->validate([
            'order_id' => 'required|string'
        ]);

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Basic ' . base64_encode($this->apiKey . ':'),
            ])->get($this->juspayBaseUrl . '/payment/status/' . $request->order_id);

            if ($response->successful()) {
                return response()->json($response->json());
            }

            return response()->json([
                'message' => 'Failed to get payment status',
                'error' => $response->json()
            ], 400);

        } catch (\Exception $e) {
            Log::error('Payment status check error: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to get payment status',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Process payment callback from Juspay
     */
    public function handleCallback(Request $request)
    {
        try {
            // Verify the signature from Juspay
            $signature = $request->header('x-juspay-signature');
            $payload = $request->getContent();
            
            // Verify signature logic here
            // if (!$this->verifySignature($signature, $payload)) {
            //     return response()->json(['message' => 'Invalid signature'], 400);
            // }

            $data = $request->all();
            
            // Update payment status in your database
            // Payment::where('order_id', $data['order_id'])->update([
            //     'status' => $data['status'],
            //     'payment_id' => $data['payment_id'],
            //     'payment_method' => $data['payment_method'],
            // ]);

            return response()->json(['message' => 'Callback processed successfully']);

        } catch (\Exception $e) {
            Log::error('Payment callback error: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to process callback',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get saved payment methods for a user
     */
    public function getSavedPaymentMethods()
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Basic ' . base64_encode($this->apiKey . ':'),
            ])->get($this->juspayBaseUrl . '/cards', [
                'customer_id' => Auth::id()
            ]);

            if ($response->successful()) {
                return response()->json($response->json());
            }

            return response()->json([
                'message' => 'Failed to get saved payment methods',
                'error' => $response->json()
            ], 400);

        } catch (\Exception $e) {
            Log::error('Get saved payment methods error: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to get saved payment methods',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a saved payment method
     */
    public function deletePaymentMethod(Request $request)
    {
        $request->validate([
            'card_id' => 'required|string'
        ]);

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Basic ' . base64_encode($this->apiKey . ':'),
            ])->delete($this->juspayBaseUrl . '/cards/' . $request->card_id);

            if ($response->successful()) {
                return response()->json(['message' => 'Payment method deleted successfully']);
            }

            return response()->json([
                'message' => 'Failed to delete payment method',
                'error' => $response->json()
            ], 400);

        } catch (\Exception $e) {
            Log::error('Delete payment method error: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to delete payment method',
                'error' => $e->getMessage()
            ], 500);
        }
    }
} 