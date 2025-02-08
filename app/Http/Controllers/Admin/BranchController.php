<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class BranchController extends Controller
{
    public function index()
    {
        $branches = Branch::with('user')->latest()->paginate(10);
        return view('admin.branches.index', compact('branches'));
    }

    public function create()
    {
        return view('admin.branches.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'is_active' => 'boolean'
        ]);

        // Create user for branch manager
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        // Assign branch manager role
        $user->assignRole('branch-manager');

        // Create branch
        $branch = Branch::create([
            'name' => $validated['name'],
            'address' => $validated['address'],
            'phone' => $validated['phone'],
            'user_id' => $user->id,
            'is_active' => $request->boolean('is_active', true)
        ]);

        return redirect()
            ->route('admin.branches.index')
            ->with('success', 'Branch created successfully');
    }

    public function edit(Branch $branch)
    {
        return view('admin.branches.edit', compact('branch'));
    }

    public function update(Request $request, Branch $branch)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:20|regex:/^([0-9\s\-\+\(\)]*)$/',
            'email' => 'required|email|unique:users,email,' . ($branch->user_id ?? ''),
            'password' => 'nullable|string|min:8|confirmed',
            'is_active' => 'boolean'
        ]);

        // Create or update user for branch manager
        if ($branch->user) {
            // Update existing user
            $branch->user->update([
                'name' => $validated['name'],
                'email' => $validated['email']
            ]);

            if ($validated['password']) {
                $branch->user->update(['password' => Hash::make($validated['password'])]);
            }
        } else {
            // Create new user
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password'] ?? Str::random(12))
            ]);

            // Assign branch manager role
            $user->assignRole('branch-manager');

            // Associate user with branch
            $branch->user()->associate($user);
        }

        // Update branch with all validated fields
        $branch->update([
            'name' => $validated['name'],
            'address' => $validated['address'],
            'phone' => $validated['phone'],
            'is_active' => $request->boolean('is_active', true)
        ]);

        // Save any changes to relationships
        $branch->save();

        // Debug log
        \Log::info('Branch update data:', [
            'phone' => $validated['phone'],
            'updated_branch' => $branch->fresh()
        ]);

        return redirect()
            ->route('admin.branches.index')
            ->with('success', 'Branch updated successfully');
    }

    public function destroy(Branch $branch)
    {
        // Delete branch manager user
        $branch->user->delete();
        
        // Delete branch
        $branch->delete();

        return redirect()
            ->route('admin.branches.index')
            ->with('success', 'Branch deleted successfully');
    }
} 