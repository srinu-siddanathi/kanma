<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount(['subcategories', 'products'])
            ->latest()
            ->paginate(10);

        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.categories.create');
    }

    public function store(Request $request)
    {
        \Log::info('Category store request received');
        \Log::info('Request has file: ' . ($request->hasFile('image') ? 'yes' : 'no'));
        \Log::info('All request data:', $request->all());
        \Log::info('All files:', $request->allFiles());

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'is_active' => 'boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        try {
            $data = $request->only(['name', 'description', 'is_active']);
            
            if ($request->hasFile('image')) {
                \Log::info('Processing image upload');
                \Log::info('Original name: ' . $request->file('image')->getClientOriginalName());
                \Log::info('MIME type: ' . $request->file('image')->getMimeType());
                \Log::info('Size: ' . $request->file('image')->getSize());
                
                $image = $request->file('image');
                $imageName = time() . '_' . $image->getClientOriginalName();
                
                \Log::info('Attempting to store image as: ' . $imageName);
                // Store directly in public directory
                $path = 'uploads/categories/' . $imageName;
                $image->move(public_path('uploads/categories'), $imageName);
                \Log::info('Image stored at path: ' . $path);
                
                if (!file_exists(public_path($path))) {
                    \Log::error('Image file not found after storage at: ' . $path);
                    throw new \Exception('Failed to store image file');
                }
                
                $data['image_url'] = $path;
                \Log::info('Image URL set to: ' . $path);
            }

            $category = Category::create($data);
            \Log::info('Category created successfully with ID: ' . $category->id);

            return redirect()->route('admin.categories.index')
                ->with('success', 'Category created successfully.');
        } catch (\Exception $e) {
            \Log::error('Failed to create category: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            if (isset($path) && file_exists(public_path($path))) {
                unlink(public_path($path));
            }
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create category: ' . $e->getMessage());
        }
    }

    public function edit(Category $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'description' => 'required|string',
            'is_active' => 'boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        try {
            $data = $request->only(['name', 'description', 'is_active']);
            $data['is_active'] = $request->has('is_active');
            
            if ($request->hasFile('image')) {
                \Log::info('Processing image upload for update');
                \Log::info('Original name: ' . $request->file('image')->getClientOriginalName());
                
                // Delete old image if exists
                if ($category->image_url) {
                    $oldPath = public_path($category->image_url);
                    if (file_exists($oldPath)) {
                        unlink($oldPath);
                    }
                }
                
                $image = $request->file('image');
                $imageName = time() . '_' . $image->getClientOriginalName();
                
                \Log::info('Attempting to store image as: ' . $imageName);
                $path = 'uploads/categories/' . $imageName;
                $image->move(public_path('uploads/categories'), $imageName);
                \Log::info('Image stored at path: ' . $path);
                
                if (!file_exists(public_path($path))) {
                    \Log::error('Image file not found after storage at: ' . $path);
                    throw new \Exception('Failed to store image file');
                }
                
                $data['image_url'] = $path;
                \Log::info('Image URL set to: ' . $path);
            }

            $category->update($data);
            \Log::info('Category updated successfully with ID: ' . $category->id);

            return redirect()->route('admin.categories.index')
                ->with('success', 'Category updated successfully');
        } catch (\Exception $e) {
            \Log::error('Failed to update category: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            if (isset($path) && file_exists(public_path($path))) {
                unlink(public_path($path));
            }
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update category: ' . $e->getMessage());
        }
    }

    public function destroy(Category $category)
    {
        try {
            // Delete the category image if it exists
            if ($category->image_url) {
                $imagePath = public_path($category->image_url);
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }

            $category->delete();

            return redirect()->route('admin.categories.index')
                ->with('success', 'Category deleted successfully.');
        } catch (\Exception $e) {
            \Log::error('Failed to delete category: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return redirect()->back()
                ->with('error', 'Failed to delete category: ' . $e->getMessage());
        }
    }
} 