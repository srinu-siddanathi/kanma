<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class CartController extends Controller
{
    private function getCartKey(): string
    {
        return 'cart_' . auth()->id();
    }

    /**
     * Get cart contents
     */
    public function index(): JsonResponse
    {
        $cartItems = Cache::get($this->getCartKey(), []);
        
        $cartTotal = collect($cartItems)->sum(function($item) {
            return $item['discounted_price'] * $item['quantity'];
        });

        return response()->json([
            'status' => 'success',
            'data' => [
                'items' => array_values($cartItems),
                'total' => round($cartTotal, 2),
                'total_items' => collect($cartItems)->sum('quantity')
            ]
        ]);
    }

    /**
     * Add/Update cart item
     */
    public function mutate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:0',
            'variant_id' => 'nullable|exists:product_variants,id'
        ]);

        $cartKey = $this->getCartKey();
        $cartItems = Cache::get($cartKey, []);

        // Find product with variant if variant_id is provided
        $product = Product::with(['images' => function($q) {
            $q->where('is_primary', true);
        }]);

        if ($request->has('variant_id')) {
            $product->with(['variants' => function($q) use ($validated) {
                $q->where('id', $validated['variant_id']);
            }]);
        }

        $product = $product->findOrFail($validated['product_id']);

        if (!$product->is_active) {
            return response()->json([
                'status' => 'error',
                'message' => 'Product is not available'
            ], 400);
        }

        // Check variant availability if variant_id is provided
        if ($request->has('variant_id')) {
            $variant = $product->variants->first();
            if (!$variant || !$variant->is_active || $variant->stock < $validated['quantity']) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Product variant not available or insufficient stock'
                ], 400);
            }
        }

        $itemKey = $validated['product_id'] . ($request->has('variant_id') ? '_' . $validated['variant_id'] : '');

        if ($validated['quantity'] > 0) {
            // Calculate discounted price
            $price = $request->has('variant_id') ? $variant->price : $product->price;
            $discountedPrice = $product->discount > 0 
                ? $price - ($price * ($product->discount / 100))
                : $price;

            // Add or update item
            $cartItems[$itemKey] = [
                'product_id' => $validated['product_id'],
                'variant_id' => $request->has('variant_id') ? $validated['variant_id'] : null,
                'quantity' => $validated['quantity'],
                'name' => $product->name,
                'unit' => $request->has('variant_id') ? $variant->unit : $product->base_unit,
                'price' => $price,
                'discounted_price' => round($discountedPrice, 2),
                'image_url' => $product->images->first()?->image_url,
                'discount' => $product->discount,
                'deal' => $product->is_deal,
                'stock' => $request->has('variant_id') ? $variant->stock : null
            ];
        } else {
            // Remove item if quantity is 0
            unset($cartItems[$itemKey]);
        }

        Cache::put($cartKey, $cartItems, now()->addDays(7));

        $cartTotal = collect($cartItems)->sum(function($item) {
            return $item['discounted_price'] * $item['quantity'];
        });

        return response()->json([
            'status' => 'success',
            'data' => [
                'items' => array_values($cartItems),
                'total' => round($cartTotal, 2),
                'total_items' => collect($cartItems)->sum('quantity')
            ]
        ]);
    }

    /**
     * Empty cart
     */
    public function empty(): JsonResponse
    {
        Cache::forget($this->getCartKey());

        return response()->json([
            'status' => 'success',
            'message' => 'Cart emptied successfully'
        ]);
    }
} 