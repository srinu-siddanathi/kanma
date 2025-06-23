<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CouponController extends Controller
{
    public function index(Request $request)
    {
        $query = Coupon::query();

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            switch ($request->status) {
                case 'active':
                    $query->active();
                    break;
                case 'inactive':
                    $query->where('is_active', false);
                    break;
                case 'expired':
                    $query->where('valid_until', '<', now());
                    break;
                case 'not_started':
                    $query->where('valid_from', '>', now());
                    break;
            }
        }

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $coupons = $query->latest()->paginate(15);
        
        return view('admin.coupons.index', compact('coupons'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();
        $products = Product::orderBy('name')->get();
        
        return view('admin.coupons.create', compact('categories', 'products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:coupons,code',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:percentage,fixed',
            'value' => 'required|numeric|min:0',
            'minimum_order_amount' => 'required|numeric|min:0',
            'maximum_discount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'per_user_limit' => 'required|integer|min:1',
            'valid_from' => 'required|date',
            'valid_until' => 'required|date|after:valid_from',
            'is_active' => 'boolean',
            'is_first_time_only' => 'boolean',
            'applicable_categories' => 'nullable|array',
            'excluded_categories' => 'nullable|array',
            'applicable_products' => 'nullable|array',
            'excluded_products' => 'nullable|array',
        ]);

        // Generate code if not provided
        if (empty($validated['code'])) {
            $validated['code'] = strtoupper(Str::random(8));
        } else {
            $validated['code'] = strtoupper($validated['code']);
        }

        // Convert arrays to JSON for storage
        $validated['applicable_categories'] = $request->applicable_categories ?: null;
        $validated['excluded_categories'] = $request->excluded_categories ?: null;
        $validated['applicable_products'] = $request->applicable_products ?: null;
        $validated['excluded_products'] = $request->excluded_products ?: null;

        Coupon::create($validated);

        return redirect()->route('admin.coupons.index')
            ->with('success', 'Coupon created successfully.');
    }

    public function show(Coupon $coupon)
    {
        $coupon->load(['usages.user', 'usages.order']);
        
        return view('admin.coupons.show', compact('coupon'));
    }

    public function edit(Coupon $coupon)
    {
        $categories = Category::orderBy('name')->get();
        $products = Product::orderBy('name')->get();
        
        return view('admin.coupons.edit', compact('coupon', 'categories', 'products'));
    }

    public function update(Request $request, Coupon $coupon)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:coupons,code,' . $coupon->id,
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:percentage,fixed',
            'value' => 'required|numeric|min:0',
            'minimum_order_amount' => 'required|numeric|min:0',
            'maximum_discount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'per_user_limit' => 'required|integer|min:1',
            'valid_from' => 'required|date',
            'valid_until' => 'required|date|after:valid_from',
            'is_active' => 'boolean',
            'is_first_time_only' => 'boolean',
            'applicable_categories' => 'nullable|array',
            'excluded_categories' => 'nullable|array',
            'applicable_products' => 'nullable|array',
            'excluded_products' => 'nullable|array',
        ]);

        // Convert to uppercase
        $validated['code'] = strtoupper($validated['code']);

        // Convert arrays to JSON for storage
        $validated['applicable_categories'] = $request->applicable_categories ?: null;
        $validated['excluded_categories'] = $request->excluded_categories ?: null;
        $validated['applicable_products'] = $request->applicable_products ?: null;
        $validated['excluded_products'] = $request->excluded_products ?: null;

        $coupon->update($validated);

        return redirect()->route('admin.coupons.index')
            ->with('success', 'Coupon updated successfully.');
    }

    public function destroy(Coupon $coupon)
    {
        // Check if coupon has been used
        if ($coupon->usages()->exists()) {
            return redirect()->route('admin.coupons.index')
                ->with('error', 'Cannot delete coupon that has been used.');
        }

        $coupon->delete();

        return redirect()->route('admin.coupons.index')
            ->with('success', 'Coupon deleted successfully.');
    }

    public function toggleStatus(Coupon $coupon)
    {
        $coupon->update(['is_active' => !$coupon->is_active]);

        $status = $coupon->is_active ? 'activated' : 'deactivated';
        
        return redirect()->route('admin.coupons.index')
            ->with('success', "Coupon {$status} successfully.");
    }

    public function generateCode()
    {
        do {
            $code = strtoupper(Str::random(8));
        } while (Coupon::where('code', $code)->exists());

        return response()->json(['code' => $code]);
    }

    public function usageStats(Coupon $coupon)
    {
        $stats = [
            'total_usage' => $coupon->usages()->count(),
            'total_discount_given' => $coupon->usages()->sum('discount_amount'),
            'unique_users' => $coupon->usages()->distinct('user_id')->count(),
            'usage_by_date' => $coupon->usages()
                ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
                ->groupBy('date')
                ->orderBy('date')
                ->get()
        ];

        return response()->json($stats);
    }
} 