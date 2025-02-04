<?php

namespace App\Http\Controllers\BranchManager;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Subcategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SubcategoryController extends Controller
{
    public function index()
    {
        $subcategories = Subcategory::with('category')
            ->withCount('products')
            ->latest()
            ->paginate(10);

        return view('branch-manager.subcategories.index', compact('subcategories'));
    }

    public function create()
    {
        $categories = Category::where('is_active', true)->get();
        return view('branch-manager.subcategories.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_active'] = $request->has('is_active');

        Subcategory::create($validated);

        return redirect()
            ->route('branch.subcategories.index')
            ->with('success', 'Subcategory created successfully');
    }

    public function edit(Subcategory $subcategory)
    {
        $categories = Category::where('is_active', true)->get();
        return view('branch-manager.subcategories.edit', compact('subcategory', 'categories'));
    }

    public function update(Request $request, Subcategory $subcategory)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_active'] = $request->has('is_active');

        $subcategory->update($validated);

        return redirect()
            ->route('branch.subcategories.index')
            ->with('success', 'Subcategory updated successfully');
    }

    public function destroy(Subcategory $subcategory)
    {
        if ($subcategory->products()->exists()) {
            return back()->with('error', 'Cannot delete subcategory with associated products');
        }

        $subcategory->delete();

        return back()->with('success', 'Subcategory deleted successfully');
    }
} 