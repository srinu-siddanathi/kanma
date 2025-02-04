<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\SubcategoryResource;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::with('subcategories')
            ->where('is_active', true)
            ->get();

        return CategoryResource::collection($categories);
    }

    public function subcategories(Category $category)
    {
        $subcategories = $category->subcategories()
            ->where('is_active', true)
            ->get();

        return SubcategoryResource::collection($subcategories);
    }
} 