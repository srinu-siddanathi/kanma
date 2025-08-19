<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use App\Models\User;
use App\Models\Product;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index()
    {
        $shops = Shop::with('user')
            ->latest()
            ->paginate(10);

        return view('admin.shops.index', compact('shops'));
    }

    public function show(Shop $shop)
    {
        $shop->load('user');
        
        // Get paginated products for this shop
        $products = $shop->products()
            ->with(['category'])
            ->latest()
            ->paginate(10); // 10 products per page

        return view('admin.shops.show', compact('shop', 'products'));
    }

    public function approve(Shop $shop)
    {
        if (!$shop->is_verified) {
            return redirect()
                ->route('admin.shops.index')
                ->with('error', 'Shop must be verified before it can be approved.');
        }

        $shop->update(['is_active' => true]);
        $shop->user->update(['is_active' => true]);

        return redirect()
            ->route('admin.shops.index')
            ->with('success', 'Shop has been approved successfully');
    }

    public function reject(Shop $shop)
    {
        $shop->update(['is_active' => false]);
        $shop->user->update(['is_active' => false]);

        return redirect()
            ->route('admin.shops.index')
            ->with('success', 'Shop has been rejected');
    }

    public function verify(Shop $shop)
    {
        // Check if all products are verified or rejected
        $pendingProducts = $shop->products()
            ->where('status', Product::STATUS_PENDING)
            ->exists();
        
        if ($pendingProducts) {
            return back()->with('error', 'All products must be verified or rejected before verifying the shop.');
        }

        // Check if there's at least one verified product
        $hasVerifiedProducts = $shop->products()
            ->where('status', Product::STATUS_VERIFIED)
            ->exists();

        if (!$hasVerifiedProducts) {
            return back()->with('error', 'Shop must have at least one verified product to be verified.');
        }

        $shop->update(['is_verified' => true]);

        return redirect()
            ->route('admin.shops.show', $shop)
            ->with('success', 'Shop has been verified successfully');
    }
} 