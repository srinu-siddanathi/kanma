<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use App\Models\Settings;
use Illuminate\Support\Facades\Log;

class LocationController extends Controller
{
    /**
     * Get all addresses for the authenticated user
     */
    public function getAddresses(): JsonResponse
    {
        if (!Auth::check()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized'
            ], 401);
        }

        $addresses = auth()->user()->addresses()
            ->orderBy('is_default', 'desc')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => [
                'addresses' => $addresses
            ]
        ]);
    }

    /**
     * Check if a location is serviceable
     */
    public function checkServiceability(Request $request): JsonResponse
    {
        if (!Auth::check()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized'
            ], 401);
        }

        $validated = $request->validate([
            'lat' => 'required|numeric|between:-90,90',
            'lng' => 'required|numeric|between:-180,180',
            'address' => 'required|string',
            'pincode' => 'nullable|string|max:10'
        ]);

        // TODO: Implement your serviceability logic here
        // For now, we'll just return true
        $isServiceable = true;

        return response()->json([
            'status' => 'success',
            'data' => [
                'serviceable' => $isServiceable
            ]
        ]);
    }

    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // Radius of the earth in km

        $latDelta = deg2rad($lat2 - $lat1);
        $lonDelta = deg2rad($lon2 - $lon1);

        $a = sin($latDelta/2) * sin($latDelta/2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($lonDelta/2) * sin($lonDelta/2);
            
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        $distance = $earthRadius * $c;

        return $distance; // Distance in km
    }
} 