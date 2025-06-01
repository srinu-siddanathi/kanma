<?php

namespace App\Http\Controllers;

use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\GeocodingService;

class AddressController extends Controller
{
    protected $geocodingService;

    public function __construct(GeocodingService $geocodingService)
    {
        $this->geocodingService = $geocodingService;
    }

    public function index()
    {
        $addresses = Auth::user()->addresses()->orderBy('is_default', 'desc')->get();
        
        if (request()->ajax()) {
            return response()->json([
                'addresses' => $addresses
            ]);
        }
        
        return view('addresses.index', compact('addresses'));
    }

    public function getAddressesForModal()
    {
        if (!request()->ajax()) {
            abort(404);
        }

        $addresses = Auth::user()->addresses()
            ->orderBy('is_default', 'desc')
            ->get();

        return response()->json([
            'addresses' => $addresses
        ]);
    }

    public function checkServiceability(Request $request)
    {
        \Log::info('CheckServiceability called', [
            'request_data' => $request->all(),
            'session_id' => session()->getId(),
            'session_data' => session()->all(),
            'cookies' => $request->cookies->all()
        ]);

        if (!request()->ajax()) {
            \Log::warning('Non-AJAX request to checkServiceability');
            abort(404);
        }

        $validated = $request->validate([
            'lat' => 'nullable|numeric|between:-90,90',
            'lng' => 'nullable|numeric|between:-180,180',
            'address' => 'required|string',
            'pincode' => 'nullable|string|max:10',
            'address_id' => 'nullable|exists:addresses,id'
        ]);

        \Log::info('Validated data', ['validated' => $validated]);

        // If pincode is provided but no coordinates, get coordinates from pincode
        if (!empty($validated['pincode']) && (empty($validated['lat']) || empty($validated['lng']))) {
            \Log::info('Getting coordinates from pincode', ['pincode' => $validated['pincode']]);
            $coordinates = $this->geocodingService->getCoordinatesFromPincode($validated['pincode']);
            if (!$coordinates) {
                \Log::warning('Could not get coordinates from pincode', ['pincode' => $validated['pincode']]);
                return response()->json([
                    'status' => 'error',
                    'serviceable' => false,
                    'message' => 'Unable to find location for the provided pincode. Please try using coordinates instead.',
                    'error_type' => 'invalid_pincode'
                ], 400);
            }
            $validated['lat'] = $coordinates['latitude'];
            $validated['lng'] = $coordinates['longitude'];
            \Log::info('Got coordinates from pincode', ['coordinates' => $coordinates]);
        }

        // Ensure we have coordinates for distance calculation
        if (empty($validated['lat']) || empty($validated['lng'])) {
            \Log::warning('Missing coordinates', ['validated' => $validated]);
            return response()->json([
                'status' => 'error',
                'serviceable' => false,
                'message' => 'Either coordinates or pincode must be provided',
                'error_type' => 'missing_location'
            ], 400);
        }

        // Get service radius from settings (default to 10km if not set)
        $serviceRadius = \App\Models\Setting::get('service_radius_km', 10);
        \Log::info('Service radius', ['radius' => $serviceRadius]);

        // Using Haversine formula to calculate distance
        $nearestBranch = \App\Models\Branch::where('is_active', true)
            ->select('id', 'name', 'latitude', 'longitude', 'pincode')
            ->selectRaw('
                (6371 * acos(
                    cos(radians(?)) * 
                    cos(radians(latitude)) * 
                    cos(radians(longitude) - radians(?)) + 
                    sin(radians(?)) * 
                    sin(radians(latitude))
                )) AS distance', 
                [$validated['lat'], $validated['lng'], $validated['lat']]
            )
            ->having('distance', '<=', $serviceRadius)
            ->orderBy('distance')
            ->first();

        \Log::info('Nearest branch search result', [
            'found' => !is_null($nearestBranch),
            'branch' => $nearestBranch ? $nearestBranch->toArray() : null
        ]);

        if ($nearestBranch) {
            // Save branch ID to session
            session(['selected_branch_id' => $nearestBranch->id]);
            \Log::info('Saved branch ID to session', [
                'branch_id' => $nearestBranch->id,
                'session_id' => session()->getId(),
                'session_data' => session()->all()
            ]);

            // If this is a saved address, save the address ID to session
            if ($request->has('address_id')) {
                session(['selected_address_id' => $request->address_id]);
                \Log::info('Saved address ID to session', [
                    'address_id' => $request->address_id,
                    'session_id' => session()->getId(),
                    'session_data' => session()->all()
                ]);
            } else {
                // If it's not a saved address, create a temporary address record
                $tempAddress = Address::create([
                    'user_id' => auth()->id(),
                    'name' => 'Temporary Address',
                    'phone' => auth()->user()->phone ?? '',
                    'address_type' => 'other',
                    'address_line1' => $validated['address'],
                    'city' => explode(',', $validated['address'])[1] ?? '',
                    'state' => explode(',', $validated['address'])[2] ?? '',
                    'country' => 'India',
                    'postal_code' => $validated['pincode'] ?? '',
                    'latitude' => $validated['lat'],
                    'longitude' => $validated['lng'],
                    'is_default' => false
                ]);
                session(['selected_address_id' => $tempAddress->id]);
                \Log::info('Created and saved temporary address', [
                    'address_id' => $tempAddress->id,
                    'session_id' => session()->getId(),
                    'session_data' => session()->all()
                ]);
            }

            // Save the session data
            session()->save();
            \Log::info('Session saved', [
                'session_id' => session()->getId(),
                'session_data' => session()->all()
            ]);

            return response()->json([
                'status' => 'success',
                'serviceable' => true,
                'nearest_branch' => [
                    'id' => $nearestBranch->id,
                    'name' => $nearestBranch->name,
                    'pincode' => $nearestBranch->pincode,
                    'distance' => round($nearestBranch->distance, 2)
                ],
                'message' => 'Location is serviceable'
            ]);
        }

        \Log::warning('No serviceable branches found', [
            'coordinates' => ['lat' => $validated['lat'], 'lng' => $validated['lng']],
            'radius' => $serviceRadius
        ]);

        return response()->json([
            'status' => 'success',
            'serviceable' => false,
            'message' => "No branches found within {$serviceRadius}km radius",
            'error_type' => 'no_branches_nearby'
        ]);
    }

    public function show(Address $address)
    {
        if ($address->user_id !== Auth::id()) {
            abort(403);
        }

        if (request()->ajax()) {
            return response()->json([
                'status' => 'success',
                'data' => [
                    'address' => $address
                ]
            ]);
        }

        return view('addresses.show', compact('address'));
    }

    public function create()
    {
        return view('addresses.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address_type' => 'required|string|in:home,work,other',
            'address_line1' => 'required|string|max:255',
            'address_line2' => 'nullable|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'country' => 'required|string|max:100',
            'postal_code' => 'required|string|max:10',
            'landmark' => 'nullable|string|max:255',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'is_default' => 'boolean'
        ]);

        if ($validated['is_default']) {
            Auth::user()->addresses()->update(['is_default' => false]);
        }

        $address = Auth::user()->addresses()->create($validated);

        if ($request->ajax()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Address added successfully',
                'data' => [
                    'address' => $address
                ]
            ]);
        }

        return redirect()->route('addresses.index')
            ->with('success', 'Address added successfully');
    }

    public function edit(Address $address)
    {
        if ($address->user_id !== Auth::id()) {
            abort(403);
        }

        return view('addresses.edit', compact('address'));
    }

    public function update(Request $request, Address $address)
    {
        if ($address->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address_type' => 'required|string|in:home,work,other',
            'address_line1' => 'required|string|max:255',
            'address_line2' => 'nullable|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'country' => 'required|string|max:100',
            'postal_code' => 'required|string|max:10',
            'landmark' => 'nullable|string|max:255',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'is_default' => 'boolean'
        ]);

        if ($validated['is_default']) {
            Auth::user()->addresses()->where('id', '!=', $address->id)->update(['is_default' => false]);
        }

        $address->update($validated);

        if ($request->ajax()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Address updated successfully',
                'data' => [
                    'address' => $address
                ]
            ]);
        }

        return redirect()->route('addresses.index')
            ->with('success', 'Address updated successfully');
    }

    public function destroy(Address $address)
    {
        if ($address->user_id !== Auth::id()) {
            abort(403);
        }

        $address->delete();

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Address deleted successfully'
            ]);
        }

        return redirect()->route('addresses.index')
            ->with('success', 'Address deleted successfully');
    }

    public function setDefault(Address $address)
    {
        if ($address->user_id !== Auth::id()) {
            abort(403);
        }

        Auth::user()->addresses()->update(['is_default' => false]);
        $address->update(['is_default' => true]);

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Default address updated successfully'
            ]);
        }

        return redirect()->route('addresses.index')
            ->with('success', 'Default address updated successfully');
    }

    public function clearSelectedAddress()
    {
        \Log::info('Clearing selected address', [
            'session_id' => session()->getId(),
            'session_data' => session()->all(),
            'cookies' => request()->cookies->all()
        ]);

        // Clear both session variables
        session()->forget(['selected_address_id', 'selected_branch_id']);
        
        // Save the session
        session()->save();
        
        \Log::info('Selected address cleared', [
            'session_id' => session()->getId(),
            'session_data' => session()->all()
        ]);
        
        if (request()->ajax()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Selected address cleared successfully'
            ]);
        }
        
        return redirect()->back()->with('success', 'Selected address cleared successfully');
    }
} 