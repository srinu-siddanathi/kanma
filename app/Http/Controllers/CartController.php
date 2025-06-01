<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductVariant;
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
            $cart[$cartItemId] = [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'variant_id' => $variant->id,
                'quantity' => intval($request->quantity),
                'unit' => $variant->unit,
                'price' => floatval($variant->price),
                'image_path' => $product->image_path,
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
} 