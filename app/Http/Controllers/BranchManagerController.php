<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class BranchManagerController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
            'phone_number' => 'required|string|max:20',
            'branch_id' => 'required|exists:branches,id',
        ]);

        $manager = User::create([
            ...$validated,
            'password' => Hash::make($validated['password']),
            'role' => 'branch_manager',
        ]);

        return response()->json([
            'message' => 'Branch manager created successfully',
            'manager' => $manager,
        ], 201);
    }

    // Add other CRUD methods as needed
} 