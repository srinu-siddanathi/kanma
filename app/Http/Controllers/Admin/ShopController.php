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
        // Add logging to track the request
        \Log::info('Shop update request started', [
            'shop_id' => $shop->id,
            'request_data' => $request->except(['new_password', 'new_password_confirmation']),
            'user_id' => auth()->id(),
            'request_method' => $request->method(),
            'request_url' => $request->url(),
            'user_agent' => $request->userAgent()
        ]);

        try {
            // Log the raw request data
            \Log::info('Raw request data', [
                'all_data' => $request->all(),
                'has_file' => $request->hasFile('image'),
                'content_type' => $request->header('Content-Type')
            ]);

            // Custom validation for working hours
            $this->validateWorkingHours($request);

            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'address' => 'required|string',
                'phone' => 'required|string|max:20',
                'email' => 'required|email|unique:shops,email,' . $shop->id,
                'latitude' => 'nullable|numeric|between:-90,90',
                'longitude' => 'nullable|numeric|between:-180,180',
                'working_hours' => 'nullable|array',
                'is_active' => 'boolean',
                'is_verified' => 'boolean',
                'new_password' => 'nullable|string|min:8|confirmed',
                'new_password_confirmation' => 'nullable|string'
            ]);

            \Log::info('Validation passed', ['validated_data' => $validated]);

            // Handle working hours validation
            if (isset($validated['working_hours'])) {
                $workingHours = [];
                foreach ($validated['working_hours'] as $day => $hours) {
                    // Only include if both open and close times are provided
                    if (!empty($hours['open']) && !empty($hours['close'])) {
                        $workingHours[$day] = [
                            'open' => $hours['open'],
                            'close' => $hours['close']
                        ];
                    }
                }
                $validated['working_hours'] = $workingHours;
            }

            // Handle boolean fields properly
            $validated['is_active'] = $request->has('is_active');
            $validated['is_verified'] = $request->has('is_verified');

            \Log::info('Prepared data for update', ['final_data' => $validated]);

            // Update shop details
            $shop->update($validated);

            \Log::info('Shop updated successfully', ['shop_id' => $shop->id]);

            // Handle password change if provided
            if (!empty($validated['new_password'])) {
                $shop->user->update([
                    'password' => bcrypt($validated['new_password'])
                ]);
                
                \Log::info('Password updated successfully', ['shop_id' => $shop->id]);
                
                return redirect()
                    ->route('admin.shops.show', $shop)
                    ->with('success', 'Shop updated successfully. Password has been changed.');
            }

            return redirect()
                ->route('admin.shops.show', $shop)
                ->with('success', 'Shop updated successfully');

        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Shop update validation failed', [
                'shop_id' => $shop->id,
                'errors' => $e->errors(),
                'request_data' => $request->except(['new_password', 'new_password_confirmation'])
            ]);
            
            return redirect()
                ->back()
                ->withErrors($e->errors())
                ->withInput();
                
        } catch (\Exception $e) {
            \Log::error('Shop update failed with exception', [
                'shop_id' => $shop->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->except(['new_password', 'new_password_confirmation'])
            ]);
            
            return redirect()
                ->back()
                ->with('error', 'An error occurred while updating the shop. Please try again.')
                ->withInput();
        }
    }

    /**
     * Custom validation for working hours
     */
    private function validateWorkingHours($request)
    {
        $workingHours = $request->input('working_hours', []);
        $errors = [];

        foreach ($workingHours as $day => $hours) {
            $open = $hours['open'] ?? '';
            $close = $hours['close'] ?? '';

            // If one time is provided, the other must also be provided
            if (!empty($open) && empty($close)) {
                $errors["working_hours.{$day}.close"] = "Close time is required when open time is provided.";
            }

            if (empty($open) && !empty($close)) {
                $errors["working_hours.{$day}.open"] = "Open time is required when close time is provided.";
            }

            // Validate time format if provided
            if (!empty($open) && !preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/', $open)) {
                $errors["working_hours.{$day}.open"] = "Invalid time format for open time.";
            }

            if (!empty($close) && !preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/', $close)) {
                $errors["working_hours.{$day}.close"] = "Invalid time format for close time.";
            }
        }

        if (!empty($errors)) {
            throw new \Illuminate\Validation\ValidationException(
                new \Illuminate\Validation\Validator(
                    app('translator'),
                    $request->all(),
                    [],
                    []
                ),
                response()->json(['errors' => $errors], 422)
            );
        }
    }
} 