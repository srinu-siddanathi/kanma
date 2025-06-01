<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\BranchController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\BranchProductController;
use App\Http\Controllers\Api\BranchOrderController;
use App\Models\Category;
use App\Http\Controllers\Api\SubscriptionPlanController;
use App\Http\Controllers\Api\ShopController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\Api\AddressController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\RazorpayController;
use App\Http\Controllers\Api\LocationController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public routes
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login/otp/send', [AuthController::class, 'sendOtp']);
Route::post('/login/otp/verify', [AuthController::class, 'verifyOtp']);
Route::post('/verify-registration', [AuthController::class, 'verifyRegistrationOtp']);

// Public Product & Category Routes
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{product}', [ProductController::class, 'show']);
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/{category}/subcategories', [CategoryController::class, 'subcategories']);
Route::get('/branches', [BranchController::class, 'index']);
Route::post('/branches/check-serviceability', [BranchController::class, 'checkServiceability']);
Route::get('/branches/{branch}', [BranchController::class, 'show']);
Route::get('/shops/nearby', [ShopController::class, 'nearby']);
Route::get('/home', [HomeController::class, 'index']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    
    // User routes
    Route::get('user/profile', [UserController::class, 'profile']);
    Route::put('user/profile', [UserController::class, 'update']);
    
    // Address Management
    Route::get('/addresses', [AddressController::class, 'index']);
    Route::post('/addresses', [AddressController::class, 'store']);
    Route::put('/addresses/{address}', [AddressController::class, 'update']);
    Route::delete('/addresses/{address}', [AddressController::class, 'destroy']);
    Route::post('/addresses/{address}/set-default', [AddressController::class, 'setDefault']);
    
    // Orders
    Route::get('/orders', [OrderController::class, 'userOrders']);
    Route::post('/orders', [OrderController::class, 'store']);
    Route::get('/orders/{order}', [OrderController::class, 'show']);
    
    // Branch Products
    Route::get('/branch/{branch}/products', [BranchProductController::class, 'index']);
    Route::get('/branch/products/{product}', [BranchProductController::class, 'show']);

    // Branch Orders
    Route::get('/branch/{branch}/orders', [BranchOrderController::class, 'index']);
    Route::put('/branch/orders/{order}/status', [BranchOrderController::class, 'updateStatus']);

    // Admin routes
    Route::prefix('admin')->middleware(['auth:sanctum', 'admin'])->group(function () {
        Route::get('dashboard', [App\Http\Controllers\Api\Admin\DashboardController::class, 'index']);
        
        // Branch Management
        Route::apiResource('branches', App\Http\Controllers\Admin\BranchController::class);
        
        // Branch Manager Management
        Route::apiResource('branch-managers', App\Http\Controllers\Admin\BranchManagerController::class);
        
        // User Management
        Route::get('users', [App\Http\Controllers\Admin\UserController::class, 'index']);
        Route::get('users/{user}', [App\Http\Controllers\Admin\UserController::class, 'show']);
        Route::put('users/{user}', [App\Http\Controllers\Admin\UserController::class, 'update']);
        Route::delete('users/{user}', [App\Http\Controllers\Admin\UserController::class, 'destroy']);
        
        // Order Management
        Route::get('orders', [App\Http\Controllers\Admin\OrderController::class, 'index']);
        Route::get('orders/{order}', [App\Http\Controllers\Admin\OrderController::class, 'show']);
        Route::put('orders/{order}/status', [App\Http\Controllers\Admin\OrderController::class, 'updateStatus']);
    });
    
    // Branch manager routes
    Route::middleware('branch.manager')->group(function () {
        Route::apiResource('branch/products', BranchProductController::class);
        Route::get('branch/orders', [BranchOrderController::class, 'index']);
        Route::put('branch/orders/{order}/status', [BranchOrderController::class, 'updateStatus']);
        Route::post('branch/products/add', [BranchProductController::class, 'addProduct']);
        Route::put('branch/products/{product}', [BranchProductController::class, 'updateProduct']);
    });

    // Admin only routes
    Route::middleware('admin')->group(function () {
        // Subscription Plans
        Route::get('/plans', [SubscriptionPlanController::class, 'index']);
        Route::get('/plans/{plan}', [SubscriptionPlanController::class, 'show']);
        Route::post('/plans/create', [SubscriptionPlanController::class, 'store']);
        Route::put('/plans/{plan}', [SubscriptionPlanController::class, 'update']);
        Route::delete('/plans/{plan}', [SubscriptionPlanController::class, 'destroy']);
    });

    // User subscription
    Route::get('/subscription', [SubscriptionPlanController::class, 'currentSubscription']);

    Route::post('/profile/complete', [AuthController::class, 'completeProfile']);

    // Cart routes
    Route::get('/cart', [CartController::class, 'index']);
    Route::post('/cart/mutate', [CartController::class, 'mutate']);
    Route::post('/cart/empty', [CartController::class, 'empty']);

    // Payment routes
    Route::post('/payments/initialize', [PaymentController::class, 'initializePayment']);
    Route::get('/payments/status', [PaymentController::class, 'getPaymentStatus']);
    Route::get('/payments/methods', [PaymentController::class, 'getSavedPaymentMethods']);
    Route::delete('/payments/methods/{card_id}', [PaymentController::class, 'deletePaymentMethod']);

    // Razorpay Payment Routes
    Route::post('/razorpay/create-order', [RazorpayController::class, 'createOrder']);
    Route::post('/razorpay/verify-payment', [RazorpayController::class, 'verifyPayment']);
    Route::post('/razorpay/payment-failure', [RazorpayController::class, 'handlePaymentFailure']);
    Route::get('/razorpay/payment-status', [RazorpayController::class, 'getPaymentStatus']);

    // Location Modal API Routes
    Route::get('/location/addresses', [App\Http\Controllers\Api\AddressController::class, 'index'])->name('api.location.addresses');
    Route::post('/location/check-serviceability', [App\Http\Controllers\Api\AddressController::class, 'checkServiceability'])->name('api.location.check-serviceability');
});

// Juspay Callback Route (no auth required as it's called by Juspay)
Route::post('/payments/callback', [PaymentController::class, 'handleCallback']);

Route::get('/categories/{category}/subcategories', function (Category $category) {
    return $category->subcategories()->where('is_active', true)->get();
});

// Location routes
Route::post('/check-serviceability', [LocationController::class, 'checkServiceability']); 