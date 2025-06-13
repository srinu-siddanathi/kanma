<?php

namespace App\Http\Controllers\Branch;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class DeliveryBoyController extends Controller
{
    public function index()
    {
        $deliveryBoys = auth()->user()->branch->deliveryBoys()->paginate(10);
        return view('branch.delivery-boys.index', compact('deliveryBoys'));
    }

    public function create()
    {
        return view('branch.delivery-boys.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|string|max:20|unique:users',
            'bike_number' => 'nullable|string|max:255',
            'password' => 'required|string|min:8',
        ]);

        $deliveryBoy = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'bike_number' => $validated['bike_number'] ?? null,
            'password' => Hash::make($validated['password']),
            'role' => 'delivery_boy',
            'branch_id' => auth()->user()->branch_id,
        ]);

        return redirect()
            ->route('branch.delivery-boys.index')
            ->with('success', 'Delivery boy added successfully');
    }

    public function edit(User $deliveryBoy)
    {
        if ($deliveryBoy->branch_id !== auth()->user()->branch_id) {
            abort(403);
        }
        return view('branch.delivery-boys.edit', compact('deliveryBoy'));
    }

    public function update(Request $request, User $deliveryBoy)
    {
        if ($deliveryBoy->branch_id !== auth()->user()->branch_id) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $deliveryBoy->id,
            'phone' => 'required|string|max:20|unique:users,phone,' . $deliveryBoy->id,
            'bike_number' => 'nullable|string|max:255',
            'password' => 'nullable|string|min:8',
        ]);

        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'bike_number' => $validated['bike_number'] ?? null,
        ];

        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $deliveryBoy->update($updateData);

        return redirect()
            ->route('branch.delivery-boys.index')
            ->with('success', 'Delivery boy updated successfully');
    }

    public function toggleStatus(Request $request, User $deliveryBoy)
    {
        // Verify this delivery boy belongs to the branch
        if ($deliveryBoy->branch_id !== auth()->user()->branch_id) {
            abort(403);
        }

        // Verify this is actually a delivery boy
        if ($deliveryBoy->role !== 'delivery_boy') {
            abort(403);
        }

        $validated = $request->validate([
            'is_working_today' => 'required|boolean'
        ]);

        $deliveryBoy->update([
            'is_working_today' => $validated['is_working_today'],
            'last_status_update' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Status updated successfully'
        ]);
    }
} 