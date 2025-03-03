<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\BranchController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\BranchManager\DashboardController as BranchManagerDashboardController;
use App\Http\Controllers\BranchManager\OrderController as BranchManagerOrderController;
use App\Http\Controllers\BranchManager\ProductController as BranchManagerProductController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\SubcategoryController;
use App\Http\Controllers\BranchManager\CategoryController as BranchManagerCategoryController;
use App\Http\Controllers\BranchManager\SubcategoryController as BranchManagerSubcategoryController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\SubscriptionPlanController;
use App\Http\Controllers\Branch\ProductController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Branch\DeliveryBoyController;
use App\Http\Controllers\Branch\OrderAssignmentController;
use App\Http\Controllers\ShopOwner\DashboardController as ShopOwnerDashboardController;
use App\Http\Controllers\ShopOwner\ProductController as ShopOwnerProductController;
use App\Http\Controllers\ShopOwner\OrderController as ShopOwnerOrderController;
use App\Http\Controllers\ShopOwner\ProfileController as ShopOwnerProfileController;
use App\Http\Controllers\Admin\ShopController;
use App\Http\Controllers\Admin\AdminProfileController;
use App\Http\Controllers\Admin\AdminSettingsController;

// Public routes
Route::get('/', function () {
    return view('index');
})->name('home');

Route::get('/shop', function () {
    return view('shop');
})->name('shop');

Route::get('/about', function () {
    return view('about');
})->name('about');

Route::get('/contact', function () {
    return view('contact');
})->name('contact');


// Admin & Branch Manager Auth Routes
Route::prefix('admin')->group(function () {
    // Auth routes (no middleware)
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('admin.login');
    Route::post('/login', [AuthController::class, 'login'])->name('admin.login.submit');
    Route::post('/logout', [AuthController::class, 'logout'])->name('admin.logout')->middleware('auth');

    // Protected Admin Routes
    Route::middleware(['auth', 'admin'])->name('admin.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/orders', [OrderController::class, 'index'])->name('orders');
        
        // User management
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

        // Category Management
        Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
        Route::get('/categories/create', [CategoryController::class, 'create'])->name('categories.create');
        Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
        Route::get('/categories/{category}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
        Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
        Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');

        // Subcategory Management
        Route::get('/subcategories', [SubcategoryController::class, 'index'])->name('subcategories.index');
        Route::get('/subcategories/create', [SubcategoryController::class, 'create'])->name('subcategories.create');
        Route::post('/subcategories', [SubcategoryController::class, 'store'])->name('subcategories.store');
        Route::get('/subcategories/{subcategory}/edit', [SubcategoryController::class, 'edit'])->name('subcategories.edit');
        Route::put('/subcategories/{subcategory}', [SubcategoryController::class, 'update'])->name('subcategories.update');
        Route::delete('/subcategories/{subcategory}', [SubcategoryController::class, 'destroy'])->name('subcategories.destroy');

        // Profile routes
        Route::get('/profile', [AdminProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profile', [AdminProfileController::class, 'update'])->name('profile.update');
        
        // Settings routes
        Route::get('/settings', [AdminSettingsController::class, 'edit'])->name('settings.edit');
        Route::put('/settings', [AdminSettingsController::class, 'update'])->name('settings.update');

        // Subscription Plans Management
        Route::get('/subscription-plans', [SubscriptionPlanController::class, 'index'])->name('subscription-plans.index');
        Route::get('/subscription-plans/create', [SubscriptionPlanController::class, 'create'])->name('subscription-plans.create');
        Route::post('/subscription-plans', [SubscriptionPlanController::class, 'store'])->name('subscription-plans.store');
        Route::get('/subscription-plans/{plan}/edit', [SubscriptionPlanController::class, 'edit'])->name('subscription-plans.edit');
        Route::put('/subscription-plans/{plan}', [SubscriptionPlanController::class, 'update'])->name('subscription-plans.update');
        Route::delete('/subscription-plans/{plan}', [SubscriptionPlanController::class, 'destroy'])->name('subscription-plans.destroy');

        // Order routes
        Route::get('/orders', [OrderController::class, 'index'])->name('orders');
        Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
        Route::put('/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.update-status');

        // Product Management
        Route::get('/products', [AdminProductController::class, 'index'])->name('products.index');
        Route::get('/products/create', [AdminProductController::class, 'create'])->name('products.create');
        Route::post('/products', [AdminProductController::class, 'store'])->name('products.store');
        Route::get('/products/{product}/edit', [AdminProductController::class, 'edit'])->name('products.edit');
        Route::put('/products/{product}', [AdminProductController::class, 'update'])->name('products.update');
        Route::delete('/products/{product}', [AdminProductController::class, 'destroy'])->name('products.destroy');
        Route::post('/products/{product}/verify', [AdminProductController::class, 'verify'])->name('products.verify');
        Route::post('/products/verify-multiple', [AdminProductController::class, 'verifyMultiple'])->name('products.verify-multiple');
        Route::post('/products/{product}/reject', [AdminProductController::class, 'reject'])->name('products.reject');
        Route::get('/products/{product}', [AdminProductController::class, 'show'])->name('products.show');

        // Branch management routes (using resource controller)
        Route::resource('branches', BranchController::class);

        // Shop Management
        Route::get('/shops', [ShopController::class, 'index'])->name('shops.index');
        Route::get('/shops/{shop}', [ShopController::class, 'show'])->name('shops.show');
        Route::post('/shops/{shop}/approve', [ShopController::class, 'approve'])->name('shops.approve');
        Route::post('/shops/{shop}/reject', [ShopController::class, 'reject'])->name('shops.reject');
        Route::post('/shops/{shop}/verify', [ShopController::class, 'verify'])->name('shops.verify');
    });

    // Protected Branch Manager Routes
    Route::middleware(['auth', 'branch.manager'])->prefix('branch')->name('branch.')->group(function () {
        Route::get('/dashboard', [BranchManagerDashboardController::class, 'show'])->name('dashboard');
        Route::get('/orders', [BranchManagerOrderController::class, 'list'])->name('orders');
        
        // Product routes
        Route::get('/products', [ProductController::class, 'index'])->name('products.index');
        Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
        Route::post('/products', [ProductController::class, 'store'])->name('products.store');
        Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
        Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update');
        
        // Available Products
        Route::get('/available-products', [ProductController::class, 'available'])->name('products.available');
        Route::post('/products/add', [ProductController::class, 'addToBranch'])->name('products.add');
        Route::put('/products/{product}/price', [ProductController::class, 'updatePrice'])->name('products.update-price');
        Route::put('/products/{product}/toggle', [ProductController::class, 'toggleStatus'])->name('products.toggle');

        // Category Management
        Route::get('/categories', [BranchManagerCategoryController::class, 'index'])->name('categories.index');
        Route::get('/categories/create', [BranchManagerCategoryController::class, 'create'])->name('categories.create');
        Route::post('/categories', [BranchManagerCategoryController::class, 'store'])->name('categories.store');
        Route::get('/categories/{category}/edit', [BranchManagerCategoryController::class, 'edit'])->name('categories.edit');
        Route::put('/categories/{category}', [BranchManagerCategoryController::class, 'update'])->name('categories.update');
        Route::delete('/categories/{category}', [BranchManagerCategoryController::class, 'destroy'])->name('categories.destroy');

        // Subcategory Management
        Route::get('/subcategories', [BranchManagerSubcategoryController::class, 'index'])->name('subcategories.index');
        Route::get('/subcategories/create', [BranchManagerSubcategoryController::class, 'create'])->name('subcategories.create');
        Route::post('/subcategories', [BranchManagerSubcategoryController::class, 'store'])->name('subcategories.store');
        Route::get('/subcategories/{subcategory}/edit', [BranchManagerSubcategoryController::class, 'edit'])->name('subcategories.edit');
        Route::put('/subcategories/{subcategory}', [BranchManagerSubcategoryController::class, 'update'])->name('subcategories.update');
        Route::delete('/subcategories/{subcategory}', [BranchManagerSubcategoryController::class, 'destroy'])->name('subcategories.destroy');

        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

        // Delivery Boy Management
        Route::resource('delivery-boys', DeliveryBoyController::class);

        // Order Assignment
        Route::get('order-assignments', [OrderAssignmentController::class, 'index'])->name('order-assignments.index');
        Route::post('order-assignments/{order}/assign', [OrderAssignmentController::class, 'assign'])->name('order-assignments.assign');

        // Order History
        Route::get('/order-history', [BranchManagerOrderController::class, 'history'])->name('orders.history');

        // Order Details
        Route::get('/orders/{order}/details', [BranchManagerOrderController::class, 'details'])
            ->name('orders.details')
            ->where('order', '[0-9]+');

        // Toggle Delivery Boy Status
        Route::post('delivery-boys/{deliveryBoy}/toggle-status', [DeliveryBoyController::class, 'toggleStatus'])
            ->name('delivery-boys.toggle-status');
    });

    // Protected Shop Owner Routes
    Route::middleware(['auth', 'shop_owner'])->prefix('shop-owner')->name('shop-owner.')->group(function () {
        Route::get('/dashboard', [ShopOwnerDashboardController::class, 'index'])->name('dashboard');
        
        // Profile Management
        Route::get('/profile', [ShopOwnerProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profile', [ShopOwnerProfileController::class, 'update'])->name('profile.update');
        
        // Product Management
        Route::get('/products', [ShopOwnerProductController::class, 'index'])->name('products.index');
        Route::get('/products/create', [ShopOwnerProductController::class, 'create'])->name('products.create');
        Route::post('/products', [ShopOwnerProductController::class, 'store'])->name('products.store');
        
        // Add explicit route model binding with shop scope
        Route::get('/products/{product}', [ShopOwnerProductController::class, 'edit'])
            ->name('products.edit')
            ->middleware('can:view,product');
        Route::put('/products/{product}', [ShopOwnerProductController::class, 'update'])
            ->name('products.update')
            ->middleware('can:update,product');
        Route::delete('/products/{product}', [ShopOwnerProductController::class, 'destroy'])
            ->name('products.destroy')
            ->middleware('can:delete,product');
        
        // Order Management
        Route::get('/orders', [ShopOwnerOrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{order}', [ShopOwnerOrderController::class, 'show'])->name('orders.show');
    });
});

// Static Pages
Route::get('/terms', function () {
    return view('static.terms');
})->name('static.terms');

Route::get('/privacy', function () {
    return view('static.privacy');
})->name('static.privacy');

Route::get('/product/{id}', function ($id) {
    return view('product');
})->name('product.show');

Route::get('/cart', function () {
    return view('cart');
})->name('cart');

Route::get('/checkout', function () {
    return view('checkout');
})->name('checkout');

Route::get('/orders', function () {
    return view('orders');
})->name('orders');