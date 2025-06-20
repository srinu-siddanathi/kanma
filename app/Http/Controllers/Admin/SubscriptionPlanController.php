<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;

class SubscriptionPlanController extends Controller
{
    public function index(Request $request)
    {
        $query = SubscriptionPlan::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status);
        }

        $plans = $query->latest()->paginate(10);
        return view('admin.subscription-plans.index', compact('plans'));
    }

    public function create()
    {
        return view('admin.subscription-plans.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'validity_days' => 'required|integer|min:1',
            'wallet_addon' => 'nullable|string',
            'free_orders' => 'nullable|integer|min:0',
            'free_delivery_radius' => 'nullable|integer|min:0',
            'is_active' => 'boolean'
        ]);

        SubscriptionPlan::create($validated);
        return redirect()->route('admin.subscription-plans.index')->with('success', 'Plan created successfully');
    }

    public function edit(SubscriptionPlan $plan)
    {
        return view('admin.subscription-plans.edit', compact('plan'));
    }

    public function update(Request $request, SubscriptionPlan $plan)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'validity_days' => 'required|integer|min:1',
            'wallet_addon' => 'nullable|string',
            'free_orders' => 'nullable|integer|min:0',
            'free_delivery_radius' => 'nullable|integer|min:0',
            'is_active' => 'boolean'
        ]);

        $plan->update($validated);
        return redirect()->route('admin.subscription-plans.index')->with('success', 'Plan updated successfully');
    }

    public function destroy(SubscriptionPlan $plan)
    {
        $plan->delete();
        return redirect()->route('admin.subscription-plans.index')->with('success', 'Plan deleted successfully');
    }
} 