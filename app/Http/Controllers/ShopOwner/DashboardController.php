<?php

namespace App\Http\Controllers\ShopOwner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $shop = auth()->user()->shop;
        
        $stats = [
            'total_products' => $shop->products()->count(),
            'total_orders' => $shop->orders()->count(),
            'pending_orders' => $shop->orders()->where('status', 'pending')->count(),
            'completed_orders' => $shop->orders()->where('status', 'completed')->count(),
        ];

        $recent_orders = $shop->orders()
            ->with(['user'])
            ->latest()
            ->take(5)
            ->get();

        return view('shop-owner.dashboard', compact('stats', 'recent_orders', 'shop'));
    }
} 