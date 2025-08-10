<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\Setting;
use App\Helpers\NotificationHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Razorpay\Api\Api;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\OrderItem;
use App\Models\Branch;

class CheckoutController extends Controller
{
    private $razorpay;

    public function __construct()
    {
        $this->razorpay = new Api(config('services.razorpay.key'), config('services.razorpay.secret'));
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
            $total += $price * $item['quantity'];
            
            $items[] = [
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

        // Create Razorpay Order
        $razorpayOrder = null;
        if ($total > 0) {
            $razorpayOrder = $this->razorpay->order->create([
                'amount' => ($total + $deliveryFee + $smallCartFee) * 100, // Amount in paise
                'currency' => 'INR',
                'payment_capture' => 1
            ]);
        }

        return view('checkout', compact('items', 'total', 'deliveryFee', 'smallCartFee', 'razorpayOrder'));
    }

    public function store(Request $request)
    {
        Log::info('Checkout store request started', [
            'user_id' => auth()->id(),
            'payment_method' => $request->payment_method,
            'delivery_address' => $request->delivery_address,
            'razorpay_payment_id' => $request->razorpay_payment_id,
            'razorpay_order_id' => $request->razorpay_order_id,
            'razorpay_signature' => $request->razorpay_signature,
            'cart' => session()->get('cart'),
            'request_data' => $request->all()
        ]);

        try {
            $request->validate([
                'payment_method' => 'required|in:cod,razorpay',
                'delivery_address' => 'required|exists:addresses,id',
            ]);

            $cart = session()->get('cart', []);
            Log::info('Cart data retrieved', ['cart' => $cart]);

            if (empty($cart)) {
                Log::warning('Empty cart during checkout', ['user_id' => auth()->id()]);
                return redirect()->route('cart')->with('error', 'Your cart is empty');
            }

            // Calculate delivery fee
            $deliveryFee = 50; // Default delivery fee
            $smallCartFee = 0; // Small cart fee
            $user = auth()->user();
            $activeSubscription = $user->currentSubscription();
            
            Log::info('Subscription check', [
                'has_subscription' => !is_null($activeSubscription),
                'subscription_data' => $activeSubscription ? $activeSubscription->toArray() : null
            ]);
            
            if ($activeSubscription && $activeSubscription->plan) {
                $plan = $activeSubscription->plan;
                Log::info('Plan details', ['plan' => $plan->toArray()]);
                
                // Check if user has free orders available
                if ($plan->free_orders > 0) {
                    $freeOrdersUsed = $user->orders()
                        ->where('created_at', '>=', $activeSubscription->starts_at)
                        ->where('created_at', '<=', $activeSubscription->ends_at)
                        ->count();
                    
                    Log::info('Free orders check', [
                        'free_orders_allowed' => $plan->free_orders,
                        'free_orders_used' => $freeOrdersUsed
                    ]);
                    
                    if ($freeOrdersUsed < $plan->free_orders) {
                        $deliveryFee = 0;
                    }
                }
                
                // Check if delivery is within free delivery radius
                if ($plan->free_delivery_radius > 0) {
                    // TODO: Calculate distance between user's address and store location
                    // For now, assume delivery is within radius
                    $deliveryFee = 0;
                }
            }

            // Get the selected address
            $address = \App\Models\Address::findOrFail($request->delivery_address);
            Log::info('Address found', ['address' => $address->toArray()]);

            // Calculate total
            $total = 0;
            $orderItems = [];
            foreach ($cart as $variantId => $item) {
                $price = $item['discount_percentage'] > 0 ? $item['discounted_price'] : $item['price'];
                $subtotal = $price * $item['quantity'];
                $total += $subtotal;
                
                $orderItems[] = [
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $price,
                    'subtotal' => $subtotal
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

            Log::info('Order calculation', [
                'total' => $total,
                'delivery_fee' => $deliveryFee,
                'small_cart_fee' => $smallCartFee,
                'final_total' => $total + $deliveryFee + $smallCartFee,
                'order_items' => $orderItems
            ]);

            DB::beginTransaction();

            // Create order
            $orderData = [
                'user_id' => auth()->id(),
                'branch_id' => Branch::where('is_active', true)->first()->id,
                'total_amount' => $total + $deliveryFee + $smallCartFee,
                'status' => 'pending',
                'payment_method' => $request->payment_method,
                'payment_status' => $request->payment_method === 'cod' ? 'pending' : 'completed',
                'delivery_address' => $address->address_line1 . ', ' . 
                    ($address->address_line2 ? $address->address_line2 . ', ' : '') . 
                    $address->city . ', ' . 
                    $address->state . ' - ' . 
                    $address->postal_code,
                'delivery_fee' => $deliveryFee + $smallCartFee,
                'delivery_latitude' => $address->latitude,
                'delivery_longitude' => $address->longitude,
                'razorpay_payment_id' => $request->razorpay_payment_id,
                'razorpay_order_id' => $request->razorpay_order_id,
                'razorpay_signature' => $request->razorpay_signature,
            ];

            Log::info('Creating order with data', ['order_data' => $orderData]);

            $order = Order::create($orderData);
            Log::info('Order created successfully', ['order_id' => $order->id, 'order' => $order->toArray()]);

            // Create order items
            foreach ($orderItems as $item) {
                $orderItem = OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'subtotal' => $item['subtotal'],
                ]);
                Log::info('Order item created', ['order_item' => $orderItem->toArray()]);
            }

            // Send notification to user about order creation
            NotificationHelper::sendOrderStatusUpdate($order->user_id, $order->id, 'pending');

            // Notify all admins about the new order
            $adminUsers = \App\Models\User::where('role', 'admin')->get();
            foreach ($adminUsers as $admin) {
                $admin->notify(new \App\Notifications\AdminNotification(
                    'New order placed: #' . $order->id . ' by ' . $user->name,
                    route('admin.orders.show', $order->id)
                ));
            }

            // Clear cart
            session()->forget('cart');
            Log::info('Cart cleared', ['user_id' => auth()->id()]);

            DB::commit();
            Log::info('Transaction committed successfully', ['order_id' => $order->id]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'redirect' => route('orders')
                ]);
            } else {
                return redirect()->route('orders')->with('success', 'Order placed successfully!');
            }

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating order', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'cart' => session()->get('cart'),
                'request_data' => $request->all()
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create order. Please try again.'
                ], 500);
            } else {
                return redirect()->back()->with('error', 'Failed to create order. Please try again.');
            }
        }
    }
} 