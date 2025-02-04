<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    public function profile(Request $request)
    {
        return response()->json([
            'user' => $request->user()->load('branch'),
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'phone_number' => 'sometimes|string|max:20',
        ]);

        $request->user()->update($validated);

        return response()->json([
            'user' => $request->user()->fresh(),
            'message' => 'Profile updated successfully',
        ]);
    }
} 