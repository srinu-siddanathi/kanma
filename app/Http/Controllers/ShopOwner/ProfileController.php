<?php

namespace App\Http\Controllers\ShopOwner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function edit()
    {
        $shop = auth()->user()->shop;
        return view('shop-owner.profile.edit', compact('shop'));
    }

    public function update(Request $request)
    {
        $shop = auth()->user()->shop;
        $user = auth()->user();
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'address' => 'required|string',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|unique:shops,email,' . $shop->id,
            'image' => 'nullable|image|max:2048',
            'current_password' => 'nullable|string',
            'new_password' => 'nullable|string|min:8|confirmed',
            'new_password_confirmation' => 'nullable|string'
        ]);

        // Handle password change if provided
        if (!empty($validated['new_password'])) {
            // Verify current password
            if (!Hash::check($validated['current_password'], $user->password)) {
                return redirect()
                    ->back()
                    ->withErrors(['current_password' => 'Current password is incorrect'])
                    ->withInput();
            }

            // Update user password
            $user->update([
                'password' => Hash::make($validated['new_password'])
            ]);
        }

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($shop->image_path) {
                $oldImagePath = public_path($shop->image_path);
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }
            $filename = time() . '_' . $request->file('image')->getClientOriginalName();
            $request->file('image')->move(public_path('uploads/shops'), $filename);
            $validated['image_path'] = 'uploads/shops/' . $filename;
        }

        // Update shop details
        $shop->update($validated);

        $message = 'Shop profile updated successfully';
        if (!empty($validated['new_password'])) {
            $message .= '. Password has been changed successfully.';
        }

        return redirect()
            ->back()
            ->with('success', $message);
    }
} 