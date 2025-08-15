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
            'address' => 'required|string',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'password' => 'required|string|min:8|confirmed',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'pincode' => 'nullable|string|size:6',
            'is_active' => 'boolean'
        ]);

        $validated['is_active'] = $request->has('is_active');

        $branch = Branch::create($validated);
        
        if(!empty($validated['email'])){
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => 'branch_manager',
                'branch_id' => $branch->id
            ]);
            
            // Associate user with branch
            $branch->user()->associate($user);
            $branch->save();
        }

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
            'email' => 'required|email|unique:users,email,' . ($branch->user?->id ?? ''),
            'password' => 'nullable|string|min:8|confirmed',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'pincode' => 'nullable|string|size:6',
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
                'password' => Hash::make($validated['password'] ?? Str::random(12)),
                'role' => 'branch_manager',
                'branch_id' => $branch->id
            ]);

            // Associate user with branch
            $branch->user()->associate($user);
        }

        // Update branch with all validated fields
        $branch->update([
            'name' => $validated['name'],
            'address' => $validated['address'],
            'phone' => $validated['phone'],
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
            'pincode' => $validated['pincode'],
            'is_active' => $request->boolean('is_active', true)
        ]);

        // Save any changes to relationships
        $branch->save();

        return redirect()
            ->route('admin.branches.index')
            ->with('success', 'Branch updated successfully');
    }

    public function destroy(Branch $branch)
    {
        try {
            // Check if branch has orders
            if ($branch->orders()->exists()) {
                return redirect()
                    ->route('admin.branches.index')
                    ->with('error', 'Cannot delete branch. It has associated orders. Please handle the orders first.');
            }

            // Delete branch manager user if exists
            if ($branch->user) {
                $branch->user->delete();
            }
            
            // Delete branch
            $branch->delete();

            return redirect()
                ->route('admin.branches.index')
                ->with('success', 'Branch deleted successfully');
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.branches.index')
                ->with('error', 'Failed to delete branch: ' . $e->getMessage());
        }
    }
} 