<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Services\GeocodingService;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    protected $geocodingService;

    public function __construct(GeocodingService $geocodingService)
    {
        $this->geocodingService = $geocodingService;
    }

    public function index()
    {
        $branches = Branch::where('is_active', true)
            ->select('id', 'name', 'address', 'phone', 'latitude', 'longitude', 'pincode')
            ->get();

        return response()->json([
            'data' => $branches,
            'message' => 'Branches retrieved successfully'
        ]);
    }

    public function show(Branch $branch)
    {
        if (!$branch->is_active) {
            return response()->json([
                'message' => 'Branch not found'
            ], 404);
        }

        return response()->json([
            'data' => $branch->load(['user']),
            'message' => 'Branch retrieved successfully'
        ]);
    }

    public function checkServiceability(Request $request)
    {
        $validated = $request->validate([
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'pincode' => 'nullable|string|size:6'
        ]);

        // If pincode is provided, get coordinates
        if (!empty($validated['pincode'])) {
            $coordinates = $this->geocodingService->getCoordinatesFromPincode($validated['pincode']);
            if (!$coordinates) {
                return response()->json([
                    'is_serviceable' => false,
                    'message' => 'Unable to find location for the provided pincode. Please try using coordinates instead.',
                    'error_type' => 'invalid_pincode'
                ], 400);
            }
            $validated['latitude'] = $coordinates['latitude'];
            $validated['longitude'] = $coordinates['longitude'];
        }

        // Check if we have coordinates (either provided directly or from pincode)
        if (empty($validated['latitude']) || empty($validated['longitude'])) {
            return response()->json([
                'is_serviceable' => false,
                'message' => 'Either coordinates or pincode must be provided',
                'error_type' => 'missing_location'
            ], 400);
        }

        // Get service radius from settings (default to 10km if not set)
        $serviceRadius = \App\Models\Setting::get('service_radius_km', 10);

        // Using Haversine formula to calculate distance
        $branches = Branch::where('is_active', true)
            ->select('id', 'name', 'latitude', 'longitude', 'pincode')
            ->selectRaw('
                (6371 * acos(
                    cos(radians(?)) * 
                    cos(radians(latitude)) * 
                    cos(radians(longitude) - radians(?)) + 
                    sin(radians(?)) * 
                    sin(radians(latitude))
                )) AS distance', 
                [$validated['latitude'], $validated['longitude'], $validated['latitude']]
            )
            ->having('distance', '<=', $serviceRadius)
            ->orderBy('distance')
            ->first();

        if ($branches) {
            return response()->json([
                'is_serviceable' => true,
                'nearest_branch' => [
                    'id' => $branches->id,
                    'name' => $branches->name,
                    'pincode' => $branches->pincode,
                    'distance' => round($branches->distance, 2)
                ],
                'message' => 'Location is serviceable'
            ]);
        }

        return response()->json([
            'is_serviceable' => false,
            'message' => "No branches found within {$serviceRadius}km radius",
            'error_type' => 'no_branches_nearby'
        ]);
    }
} 