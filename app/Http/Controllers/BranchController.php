<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    public function index(Request $request)
    {
        $branches = Branch::query()
            ->when($request->search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('address', 'like', "%{$search}%");
            })
            ->when($request->latitude && $request->longitude, function ($query) use ($request) {
                // Add distance calculation logic here
            })
            ->where('is_active', true)
            ->paginate();

        return response()->json($branches);
    }

    public function show(Branch $branch)
    {
        return response()->json([
            'branch' => $branch->load('products'),
        ]);
    }
} 