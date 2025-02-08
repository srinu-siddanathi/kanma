<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPlan;
use App\Http\Resources\SubscriptionPlanResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class SubscriptionPlanController extends Controller
{
    /**
     * List all subscription plans (Admin only)
     */
    public function index(): JsonResponse
    {
        $plans = SubscriptionPlan::orderBy('price')->get();
        
        return response()->json([
            'status' => 'success',
            'data' => SubscriptionPlanResource::collection($plans)
        ]);
    }

    /**
     * Get a specific plan details (Admin only)
     */
    public function show(SubscriptionPlan $plan): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => new SubscriptionPlanResource($plan)
        ]);
    }

    /**
     * Create a new subscription plan (Admin only)
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'duration_days' => 'required|integer|min:1',
            'features' => 'required|array',
            'is_popular' => 'boolean',
            'is_active' => 'boolean'
        ]);

        $plan = SubscriptionPlan::create($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Subscription plan created successfully',
            'data' => new SubscriptionPlanResource($plan)
        ], 201);
    }

    /**
     * Update a subscription plan (Admin only)
     */
    public function update(Request $request, SubscriptionPlan $plan): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'price' => 'sometimes|numeric|min:0',
            'duration_days' => 'sometimes|integer|min:1',
            'features' => 'sometimes|array',
            'is_popular' => 'sometimes|boolean',
            'is_active' => 'sometimes|boolean'
        ]);

        $plan->update($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Subscription plan updated successfully',
            'data' => new SubscriptionPlanResource($plan)
        ]);
    }

    /**
     * Delete a subscription plan (Admin only)
     */
    public function destroy(SubscriptionPlan $plan): JsonResponse
    {
        // Check if plan has active subscriptions
        if ($plan->subscriptions()->where('status', 'active')->exists()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Cannot delete plan with active subscriptions'
            ], 422);
        }

        $plan->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Subscription plan deleted successfully'
        ]);
    }

    /**
     * Get current user's subscription details (Any authenticated user)
     */
    public function currentSubscription(Request $request): JsonResponse
    {
        $user = $request->user();
        $subscription = $user->currentSubscription();

        return response()->json([
            'status' => 'success',
            'data' => $subscription
        ]);
    }
} 