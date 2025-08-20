<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class AddressController extends Controller
{
    /**
     * The middleware that should be applied to all routes.
     *
     * @var array
     */
    protected $middleware = ['auth:sanctum'];

    /**
     * List all addresses for the authenticated user
     */
    public function index(): JsonResponse
    {
        if (!Auth::check()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized'
            ], 401);
        }

        $addresses = auth()->user()->addresses()->latest()->get();

        return response()->json([
            'status' => 'success',
            'data' => [
                'addresses' => $addresses
            ]
        ]);
    }

    /**
     * Store a new address
     */
    public function store(Request $request): JsonResponse
    {
        if (!Auth::check()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized'
            ], 401);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address_line1' => 'required|string|max:255',
            'address_line2' => 'nullable|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'country' => 'required|string|max:100',
            'postal_code' => 'required|string|max:20',
            'is_default' => 'boolean',
            'address_type' => 'required|in:home,work,other',
            'landmark' => 'nullable|string|max:255',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric'
        ]);

        // If coordinates are not provided, try to get them from postal code
        if (empty($validated['latitude']) || empty($validated['longitude'])) {
            if (!empty($validated['postal_code'])) {
                $geocodingService = app(\App\Services\GeocodingService::class);
                $coordinates = $geocodingService->getCoordinatesFromPincode($validated['postal_code']);
                if ($coordinates) {
                    $validated['latitude'] = $coordinates['latitude'];
                    $validated['longitude'] = $coordinates['longitude'];
                }
            }
        }

        // If this is set as default, unset any existing default address
        if ($validated['is_default'] ?? false) {
            auth()->user()->addresses()->update(['is_default' => false]);
        }

        $address = auth()->user()->addresses()->create($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Address added successfully',
            'data' => [
                'address' => $address
            ]
        ], 201);
    }

    /**
     * Show address details
     */
    public function show(Address $address): JsonResponse
    {
        if (!Auth::check()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized'
            ], 401);
        }

        // Ensure the address belongs to the authenticated user
        if ($address->user_id !== auth()->id()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Address not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'address' => $address
            ]
        ]);
    }

    /**
     * Update an address
     */
    public function update(Request $request, Address $address): JsonResponse
    {
        if (!Auth::check()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized'
            ], 401);
        }

        // Ensure the address belongs to the authenticated user
        if ($address->user_id !== auth()->id()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Address not found'
            ], 404);
        }

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'phone' => 'sometimes|required|string|max:20',
            'address_line1' => 'sometimes|required|string|max:255',
            'address_line2' => 'nullable|string|max:255',
            'city' => 'sometimes|required|string|max:100',
            'state' => 'sometimes|required|string|max:100',
            'country' => 'sometimes|required|string|max:100',
            'postal_code' => 'sometimes|required|string|max:20',
            'is_default' => 'boolean',
            'address_type' => 'sometimes|required|in:home,work,other',
            'landmark' => 'nullable|string|max:255',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric'
        ]);

        // If this is set as default, unset any existing default address
        if ($validated['is_default'] ?? false) {
            auth()->user()->addresses()->where('id', '!=', $address->id)->update(['is_default' => false]);
        }

        $address->update($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Address updated successfully',
            'data' => [
                'address' => $address
            ]
        ]);
    }

    /**
     * Delete an address
     */
    public function destroy(Address $address): JsonResponse
    {
        if (!Auth::check()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized'
            ], 401);
        }

        // Ensure the address belongs to the authenticated user
        if ($address->user_id !== auth()->id()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Address not found'
            ], 404);
        }

        $address->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Address deleted successfully'
        ]);
    }

    /**
     * Set an address as default
     */
    public function setDefault(Address $address): JsonResponse
    {
        if (!Auth::check()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized'
            ], 401);
        }

        // Ensure the address belongs to the authenticated user
        if ($address->user_id !== auth()->id()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Address not found'
            ], 404);
        }

        // Unset any existing default address
        auth()->user()->addresses()->update(['is_default' => false]);

        // Set this address as default
        $address->update(['is_default' => true]);

        return response()->json([
            'status' => 'success',
            'message' => 'Default address updated successfully',
            'data' => [
                'address' => $address
            ]
        ]);
    }
} 