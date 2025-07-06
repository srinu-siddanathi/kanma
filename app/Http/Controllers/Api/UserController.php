<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function profile()
    {
        $user = auth()->user();
        return response()->json([
            'data' => $user->load('branch'),
            'message' => 'Profile retrieved successfully'
        ]);
    }

    public function update(Request $request)
    {
        $user = auth()->user();
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'current_password' => 'nullable|required_with:password|current_password',
            'password' => 'nullable|min:8|confirmed',
        ]);

        $updateData = [
            'name' => $validated['name']
        ];

        if (isset($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $user->update($updateData);

        return response()->json([
            'message' => 'Profile updated successfully',
            'data' => $user->fresh()->load('branch')
        ]);
    }

    public function deleteAccount(Request $request)
    {
        $user = auth()->user();
        
        // Delete user data
        $user->delete();

        // Revoke all tokens
        $user->tokens()->delete();

        return response()->json([
            'message' => 'Account deleted successfully',
            'status' => 'success'
        ]);
    }

    private function storeProfileImage($file)
    {
        // Create directory if it doesn't exist
        $uploadPath = public_path('uploads/profile-images');
        if (!file_exists($uploadPath)) {
            mkdir($uploadPath, 0777, true);
        }

        // Generate unique filename
        $extension = $file->getClientOriginalExtension();
        $filename = Str::random(40) . '.' . $extension;
        
        // Move file to public directory
        $file->move($uploadPath, $filename);
        
        // Return relative path for database storage
        return 'uploads/profile-images/' . $filename;
    }

    private function deleteProfileImage($path)
    {
        $fullPath = public_path($path);
        if (file_exists($fullPath)) {
            unlink($fullPath);
        }
    }

    public function updateProfileImage(Request $request)
    {
        $request->validate([
            'profile_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048' // 2MB max
        ]);

        $user = auth()->user();

        // Delete old profile image if exists
        if ($user->profile_image) {
            $this->deleteProfileImage($user->profile_image);
        }

        // Store new image
        $imagePath = $this->storeProfileImage($request->file('profile_image'));

        // Update user profile
        $user->update(['profile_image' => $imagePath]);

        return response()->json([
            'message' => 'Profile image updated successfully',
            'data' => $user->fresh()->load('branch')
        ]);
    }
} 