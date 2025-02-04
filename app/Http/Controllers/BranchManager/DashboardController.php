<?php

namespace App\Http\Controllers\BranchManager;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Category;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function show()
    {
        $branch = auth()->user()->branch;

        $stats = [
            'total_orders' => Order::where('branch_id', $branch->id)->count(),
            'total_products' => Product::where('branch_id', $branch->id)->count(),
            'active_categories' => Category::where('is_active', true)->count(),
            'todays_orders' => Order::where('branch_id', $branch->id)
                ->whereDate('created_at', Carbon::today())
                ->count(),
        ];

        $recent_orders = Order::with(['user'])
            ->where('branch_id', $branch->id)
            ->latest()
            ->take(5)
            ->get();

        return view('branch-manager.dashboard', compact('stats', 'recent_orders'));
    }
} 