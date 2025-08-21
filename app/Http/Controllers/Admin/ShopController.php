<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use App\Models\User;
use App\Models\Product;
use Illuminate\Http\Request;


class ShopController extends Controller
{
    public function index()
    {
        $shops = Shop::with('user')
            ->latest()
            ->paginate(10);

        return view('admin.shops.index', compact('shops'));
    }

    public function show(Shop $shop)
    {
        $shop->load('user');
        
        // Get paginated products for this shop
        $products = $shop->products()
            ->with(['category', 'images'])
            ->latest()
            ->paginate(10); // 10 products per page

        return view('admin.shops.show', compact('shop', 'products'));
    }

    public function approve(Shop $shop)
    {
        if (!$shop->is_verified) {
            return redirect()
                ->route('admin.shops.index')
                ->with('error', 'Shop must be verified before it can be approved.');
        }

        $shop->update(['is_active' => true]);
        $shop->user->update(['is_active' => true]);

        return redirect()
            ->route('admin.shops.index')
            ->with('success', 'Shop has been approved successfully');
    }

    public function reject(Shop $shop)
    {
        $shop->update(['is_active' => false]);
        $shop->user->update(['is_active' => false]);

        return redirect()
            ->route('admin.shops.index')
            ->with('success', 'Shop has been rejected');
    }

    public function verify(Shop $shop)
    {
        // Check if all products are verified or rejected
        $pendingProducts = $shop->products()
            ->where('status', Product::STATUS_PENDING)
            ->exists();
        
        if ($pendingProducts) {
            return back()->with('error', 'All products must be verified or rejected before verifying the shop.');
        }

        // Check if there's at least one verified product
        $hasVerifiedProducts = $shop->products()
            ->where('status', Product::STATUS_VERIFIED)
            ->exists();

        if (!$hasVerifiedProducts) {
            return back()->with('error', 'Shop must have at least one verified product to be verified.');
        }

        $shop->update(['is_verified' => true]);

        return redirect()
            ->route('admin.shops.show', $shop)
            ->with('success', 'Shop has been verified successfully');
    }

    public function edit(Shop $shop)
    {
        $shop->load('user');
        return view('admin.shops.edit', compact('shop'));
    }

    public function update(Request $request, Shop $shop)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'address' => 'required|string',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|unique:shops,email,' . $shop->id,
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'working_hours' => 'nullable|array',
            'working_hours.*.open' => 'required_with:working_hours.*.close|string',
            'working_hours.*.close' => 'required_with:working_hours.*.open|string',
            'is_active' => 'boolean',
            'is_verified' => 'boolean',
            'new_password' => 'nullable|string|min:8|confirmed',
            'new_password_confirmation' => 'nullable|string'
        ]);

        // Handle working hours validation
        if (isset($validated['working_hours'])) {
            $workingHours = [];
            foreach ($validated['working_hours'] as $day => $hours) {
                if (!empty($hours['open']) && !empty($hours['close'])) {
                    $workingHours[$day] = $hours;
                }
            }
            $validated['working_hours'] = $workingHours;
        }

        // Update shop details
        $shop->update($validated);

        // Handle password change if provided
        if (!empty($validated['new_password'])) {
            $shop->user->update([
                'password' => bcrypt($validated['new_password'])
            ]);
            
            return redirect()
                ->route('admin.shops.show', $shop)
                ->with('success', 'Shop updated successfully. Password has been changed.');
        }

        return redirect()
            ->route('admin.shops.show', $shop)
            ->with('success', 'Shop updated successfully');
    }
} 