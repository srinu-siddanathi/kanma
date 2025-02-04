<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    public function index()
    {
        $branches = Branch::where('is_active', true)
            ->select('id', 'name', 'address', 'contact_number', 'latitude', 'longitude')
            ->get();

        return response()->json([
            'data' => $branches,
            'message' => 'Branches retrieved successfully'
        ]);
    }

    public function show(Branch $branch)
    {
        if (!$branch->is_active) {
            return response()->json([
                'message' => 'Branch not found'
            ], 404);
        }

        return response()->json([
            'data' => $branch->load(['manager']),
            'message' => 'Branch retrieved successfully'
        ]);
    }
} 