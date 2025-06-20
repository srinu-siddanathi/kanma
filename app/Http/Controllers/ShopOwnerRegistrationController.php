<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules\Password;

class ShopOwnerRegistrationController extends Controller
{
    public function register(Request $request)
    {
        try {
            // Validate the request
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'phone' => 'required|string|max:20|unique:users',
                'password' => ['required', 'confirmed', Password::defaults()],
                'shop_name' => 'required|string|max:255',
                'shop_description' => 'nullable|string|max:1000',
                'shop_address' => 'required|string|max:500',
                'terms' => 'required|accepted',
                'newsletter' => 'nullable|in:0,1',
            ]);

            DB::beginTransaction();

            // Create user
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'password' => Hash::make($validated['password']),
                'role' => 'shop_owner',
                'email_verified_at' => now(), // Auto-verify for shop owners
            ]);

            // Create shop
            Log::info('Creating shop with data', [
                'shop_data' => [
                    'name' => $validated['shop_name'],
                    'description' => $validated['shop_description'],
                    'address' => $validated['shop_address'],
                    'phone' => $validated['phone'],
                    'email' => $validated['email'],
                    'user_id' => $user->id,
                ]
            ]);
            
            $shop = Shop::create([
                'name' => $validated['shop_name'],
                'description' => $validated['shop_description'],
                'address' => $validated['shop_address'],
                'image_path' => null,
                'phone' => $validated['phone'],
                'email' => $validated['email'],
                'user_id' => $user->id,
                'is_active' => false, // Requires admin approval
                'is_verified' => false,
                'approval_status' => 'pending',
            ]);
            
            Log::info('Shop created successfully', ['shop_id' => $shop->id]);

            // Subscribe to newsletter if requested
            if ($request->input('newsletter') === '1') {
                // Add newsletter subscription logic here if needed
                // NewsletterSubscriber::create(['email' => $user->email]);
            }

            // Log the registration
            Log::info('Shop owner registration', [
                'user_id' => $user->id,
                'shop_id' => $shop->id,
                'email' => $user->email,
                'shop_name' => $shop->name,
            ]);

            // Notify admins about new shop registration
            /*
            $adminUsers = User::where('role', 'admin')->get();
            foreach ($adminUsers as $admin) {
                $admin->notify(new \App\Notifications\AdminNotification(
                    'New shop registration: ' . $shop->name . ' by ' . $user->name,
                    route('admin.shops.show', $shop->id)
                ));
            }
            */

            DB::commit();

            // Auto-login the user
            auth()->login($user);

            return response()->json([
                'success' => true,
                'message' => 'Shop registration submitted successfully! Your shop is pending admin approval.',
                'redirect' => route('shop-owner.dashboard')
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Shop owner registration error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->except(['password', 'password_confirmation'])
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Registration failed: ' . $e->getMessage()
            ], 500);
        }
    }
} 