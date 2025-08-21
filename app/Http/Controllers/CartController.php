<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CartController extends Controller
{
    public function addToCart(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'variant_id' => 'required|exists:product_variants,id',
            'quantity' => 'required|integer|min:1'
        ]);

        $product = Product::findOrFail($request->product_id);
        $variant = ProductVariant::findOrFail($request->variant_id);

        if (!$product->is_active || !$variant->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'This product is currently unavailable'
            ], 400);
        }

        if ($variant->stock < $request->quantity) {
            return response()->json([
                'success' => false,
                'message' => 'Requested quantity is not available'
            ], 400);
        }

        $cart = Session::get('cart', []);
        
        $cartItemId = $variant->id;

        if (isset($cart[$cartItemId])) {
            $cart[$cartItemId]['quantity'] += intval($request->quantity);
        } else {
            // Handle image: check products table first, then product_images table
            $imagePath = null;
            
            // First check if product has image_path in products table
            if ($product->image_path) {
                $imagePath = $product->image_path;
            }
            // Then check product_images table for primary image
            elseif ($product->images->where('is_primary', true)->first()) {
                $imagePath = $product->images->where('is_primary', true)->first()->image_path;
            }
            // Then check for any image in product_images table
            elseif ($product->images->first()) {
                $imagePath = $product->images->first()->image_path;
            }
            // If no images found, use default
            else {
                $imagePath = 'images/no-image.png';
            }

            $cart[$cartItemId] = [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'variant_id' => $variant->id,
                'quantity' => intval($request->quantity),
                'unit' => $variant->unit,
                'price' => floatval($variant->price),
                'image_path' => $imagePath,
                'discount_percentage' => floatval($variant->discount_percentage),
                'discounted_price' => floatval($variant->discounted_price)
            ];
        }

        Session::put('cart', $cart);

        return response()->json([
            'success' => true,
            'message' => 'Product added to cart successfully',
            'cart_count' => count($cart),
            'max_stock' => $variant->stock
        ]);
    }

    public function getCartItems()
    {
        $cart = Session::get('cart', []);
        $total = 0;
        $formattedItems = [];

        foreach ($cart as $variantId => $item) {
            $price = $item['discount_percentage'] > 0 ? $item['discounted_price'] : $item['price'];
            $total += $price * $item['quantity'];
            
            // Format the item with the correct structure
            $formattedItems[$variantId] = [
                'variant_id' => $variantId,
                'product_id' => $item['product_id'],
                'product_name' => $item['product_name'],
                'quantity' => $item['quantity'],
                'unit' => $item['unit'],
                'price' => $price,
                'image_path' => $item['image_path'],
                'total' => $price * $item['quantity']
            ];
        }

        return response()->json([
            'success' => true,
            'items' => $formattedItems,
            'total' => $total,
            'count' => count($cart)
        ]);
    }

    public function removeFromCart(Request $request)
    {
        $request->validate([
            'variant_id' => 'required|exists:product_variants,id'
        ]);

        $cart = Session::get('cart', []);
        
        if (isset($cart[$request->variant_id])) {
            unset($cart[$request->variant_id]);
            Session::put('cart', $cart);
            
            return response()->json([
                'success' => true,
                'message' => 'Item removed from cart',
                'cart_count' => count($cart)
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Item not found in cart'
        ], 404);
    }

    public function updateCart(Request $request)
    {
        $request->validate([
            'variant_id' => 'required|exists:product_variants,id',
            'quantity' => 'required|integer|min:1'
        ]);

        $variant = ProductVariant::findOrFail($request->variant_id);
        
        if ($request->quantity > $variant->stock) {
            return response()->json([
                'success' => false,
                'message' => 'Requested quantity is not available',
                'old_quantity' => $request->quantity - 1
            ], 400);
        }

        $cart = Session::get('cart', []);
        
        if (isset($cart[$request->variant_id])) {
            $cart[$request->variant_id]['quantity'] = $request->quantity;
            Session::put('cart', $cart);
            
            return response()->json([
                'success' => true,
                'message' => 'Cart updated',
                'cart_count' => count($cart),
                'max_stock' => $variant->stock
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Item not found in cart'
        ], 404);
    }

    public function index()
    {
        $cart = Session::get('cart', []);
        $total = 0;
        $items = [];
        $deliveryFee = 50; // Default delivery fee
        $smallCartFee = 0; // Small cart fee

        foreach ($cart as $variantId => $item) {
            $price = $item['discount_percentage'] > 0 ? $item['discounted_price'] : $item['price'];
            $subtotal = $price * $item['quantity'];
            $total += $subtotal;
            
            $items[] = [
                'variant_id' => $variantId,
                'product_id' => $item['product_id'],
                'product_name' => $item['product_name'],
                'quantity' => $item['quantity'],
                'unit' => $item['unit'],
                'price' => $price,
                'image_path' => $item['image_path'],
                'total' => $subtotal
            ];
        }

        // Calculate small cart fee if order is below minimum amount
        $minimumOrderAmount = Setting::get('minimum_order_amount', 100);
        $smallCartFeeAmount = Setting::get('small_cart_fee', 10);
        
        // Check if user has active membership subscription
        $hasActiveMembership = false;
        if (auth()->check()) {
            $user = auth()->user();
            $activeSubscription = $user->currentSubscription();
            $hasActiveMembership = $activeSubscription && $activeSubscription->status === 'active';
        }
        
        // Only apply small cart fee if user doesn't have active membership
        if (!$hasActiveMembership && $total < $minimumOrderAmount) {
            $smallCartFee = $smallCartFeeAmount;
        }

        // Calculate delivery fee based on subscription plan
        if (auth()->check()) {
            $user = auth()->user();
            $activeSubscription = $user->currentSubscription();
            
            if ($activeSubscription && $activeSubscription->plan) {
                $plan = $activeSubscription->plan;
                
                // Check if user has free orders remaining
                $freeOrdersUsed = $user->orders()
                    ->where('created_at', '>=', $activeSubscription->starts_at)
                    ->where('created_at', '<=', $activeSubscription->ends_at)
                    ->count();
                
                if ($freeOrdersUsed < $plan->free_orders) {
                    $deliveryFee = 0; // Free delivery if free orders are available
                } else {
                    // Calculate delivery fee based on distance and free delivery radius
                    $deliveryFee = 50; // Default delivery fee
                    
                    // If user has free delivery radius, check if delivery is within that radius
                    if ($plan->free_delivery_radius > 0) {
                        // TODO: Implement distance calculation based on user's delivery address
                        // For now, we'll assume delivery is within radius
                        $deliveryFee = 0;
                    }
                }
            }
        }

        return view('cart', compact('items', 'total', 'deliveryFee', 'smallCartFee'));
    }

    public function clearCart(Request $request)
    {
        Session::forget('cart');
        
        return response()->json([
            'success' => true,
            'message' => 'Cart cleared successfully',
            'cart_count' => 0
        ]);
    }
} 