<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class BranchManagerController extends Controller
{
    public function index()
    {
        return response()->json([
            'data' => User::where('role', 'branch_manager')->with('branch')->paginate(10)
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
            'branch_id' => 'required|exists:branches,id'
        ]);

        $manager = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'branch_manager',
            'branch_id' => $validated['branch_id']
        ]);

        return response()->json([
            'data' => $manager->load('branch'),
            'message' => 'Branch manager created successfully'
        ], 201);
    }
} 