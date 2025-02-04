<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_orders' => Order::count(),
            'total_revenue' => Order::sum('total_amount'),
            'total_users' => User::where('role', 'customer')->count(),
            'total_customers' => User::where('role', 'customer')->count(),
            'total_branches' => Branch::count(),
            'total_branch_managers' => User::where('role', 'branch_manager')->count(),
            'recent_orders' => Order::with(['user', 'branch'])
                ->latest()
                ->take(5)
                ->get(),
            'order_stats' => [
                'pending' => Order::where('status', 'pending')->count(),
                'processing' => Order::where('status', 'processing')->count(),
                'completed' => Order::where('status', 'completed')->count(),
                'cancelled' => Order::where('status', 'cancelled')->count(),
            ]
        ];

        return view('admin.dashboard', compact('stats'));
    }
} 