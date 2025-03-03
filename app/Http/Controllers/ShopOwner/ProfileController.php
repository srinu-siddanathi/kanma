<?php

namespace App\Http\Controllers\ShopOwner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'address' => 'required|string',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|unique:shops,email,' . $shop->id,
            'image' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($shop->image_path) {
                Storage::delete($shop->image_path);
            }
            $validated['image_path'] = $request->file('image')->store('shops', 'public');
        }

        $shop->update($validated);

        return redirect()
            ->back()
            ->with('success', 'Shop profile updated successfully');
    }
} 