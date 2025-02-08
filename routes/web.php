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

// Redirect root to customer login (we'll create this later)
Route::get('/', function () {
    return view('welcome');
})->name('home');

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

        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

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

        // Branch management routes (using resource controller)
        Route::resource('branches', BranchController::class);
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
    });
});

// Static Pages
Route::get('/about', function () {
    return view('static.about');
})->name('static.about');

Route::get('/terms', function () {
    return view('static.terms');
})->name('static.terms');

Route::get('/privacy', function () {
    return view('static.privacy');
})->name('static.privacy');