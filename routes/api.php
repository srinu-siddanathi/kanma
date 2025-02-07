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

// Public routes
Route::post('/login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    
    // User routes
    Route::get('user/profile', [UserController::class, 'profile']);
    Route::put('user/profile', [UserController::class, 'update']);
    
    // Branch routes
    Route::get('branches', [BranchController::class, 'index']);
    Route::get('branches/{branch}', [BranchController::class, 'show']);
    
    // Products
    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/products/{product}', [ProductController::class, 'show']);
    
    // Categories
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/categories/{category}/subcategories', [CategoryController::class, 'subcategories']);
    
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
});

Route::get('/categories/{category}/subcategories', function (Category $category) {
    return $category->subcategories()->where('is_active', true)->get();
}); 