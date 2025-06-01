<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class GeocodingService
{
    protected $apiKey;
    protected $baseUrl = 'https://maps.googleapis.com/maps/api/geocode/json';

    public function __construct()
    {
        $this->apiKey = config('services.google.maps_api_key');
    }

    public function getCoordinatesFromPincode(string $pincode)
    {
        // Check cache first
        $cacheKey = "pincode_coordinates_{$pincode}";
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        try {
            // Format the address specifically for Indian pincode
            $address = "{$pincode}, India";
            
            Log::info('Geocoding request:', [
                'pincode' => $pincode,
                'formatted_address' => $address
            ]);

            $response = Http::get($this->baseUrl, [
                'address' => $address,
                'key' => $this->apiKey,
                'components' => 'country:IN|postal_code:' . $pincode
            ]);

            Log::info('Geocoding response:', [
                'status' => $response['status'],
                'response' => $response->json()
            ]);

            if ($response->successful()) {
                if ($response['status'] === 'OK' && !empty($response['results'])) {
                    $location = $response['results'][0]['geometry']['location'];
                    $coordinates = [
                        'latitude' => $location['lat'],
                        'longitude' => $location['lng']
                    ];

                    // Cache the result for 30 days
                    Cache::put($cacheKey, $coordinates, now()->addDays(30));

                    return $coordinates;
                } else {
                    Log::warning('Geocoding API returned non-OK status', [
                        'pincode' => $pincode,
                        'status' => $response['status'],
                        'error_message' => $response['error_message'] ?? 'No error message'
                    ]);
                }
            } else {
                Log::error('Geocoding API request failed', [
                    'pincode' => $pincode,
                    'status_code' => $response->status(),
                    'response' => $response->json()
                ]);
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Geocoding error: ' . $e->getMessage(), [
                'pincode' => $pincode,
                'exception' => $e
            ]);
            return null;
        }
    }

    public function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // Radius of the earth in km

        $latDelta = deg2rad($lat2 - $lat1);
        $lonDelta = deg2rad($lon2 - $lon1);

        $a = sin($latDelta/2) * sin($latDelta/2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($lonDelta/2) * sin($lonDelta/2);
        
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        
        return $earthRadius * $c;
    }
} 