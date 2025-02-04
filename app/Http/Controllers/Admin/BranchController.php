<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    public function list()
    {
        $branches = Branch::withCount(['orders', 'products'])
            ->latest()
            ->paginate(10);

        return view('admin.branches.index', compact('branches'));
    }

    public function show(Branch $branch)
    {
        return view('admin.branches.show', [
            'branch' => $branch->load(['manager', 'products', 'orders']),
        ]);
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
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'contact_number' => 'required|string|max:20',
            'email' => 'required|email|unique:branches,email',
        ]);

        Branch::create($validated);

        return redirect()
            ->route('admin.branches')
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
            'address' => 'required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'contact_number' => 'required|string|max:20',
            'email' => 'required|email|unique:branches,email,' . $branch->id,
            'is_active' => 'boolean',
        ]);

        $branch->update($validated);

        return redirect()
            ->route('admin.branches')
            ->with('success', 'Branch updated successfully');
    }
} 