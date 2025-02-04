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
        Route::get('/branches', [BranchController::class, 'list'])->name('branches');
        
        // User management
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

        // Branch management
        Route::get('/branches/create', [BranchController::class, 'create'])->name('branches.create');
        Route::post('/branches', [BranchController::class, 'store'])->name('branches.store');
        Route::get('/branches/{branch}/edit', [BranchController::class, 'edit'])->name('branches.edit');
        Route::put('/branches/{branch}', [BranchController::class, 'update'])->name('branches.update');

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
    });

    // Protected Branch Manager Routes
    Route::middleware(['auth', 'branch.manager'])->prefix('branch')->group(function () {
        Route::get('/dashboard', [BranchManagerDashboardController::class, 'show'])->name('branch.dashboard');
        Route::get('/orders', [BranchManagerOrderController::class, 'list'])->name('branch.orders');
        
        // Product routes
        Route::get('/products', [BranchManagerProductController::class, 'index'])->name('branch.products.index');
        Route::get('/products/create', [BranchManagerProductController::class, 'create'])->name('branch.products.create');
        Route::post('/products', [BranchManagerProductController::class, 'store'])->name('branch.products.store');
        Route::get('/products/{product}/edit', [BranchManagerProductController::class, 'edit'])->name('branch.products.edit');
        Route::put('/products/{product}', [BranchManagerProductController::class, 'update'])->name('branch.products.update');
        Route::delete('/products/{product}', [BranchManagerProductController::class, 'destroy'])->name('branch.products.destroy');

        // Category Management
        Route::get('/categories', [BranchManagerCategoryController::class, 'index'])->name('branch.categories.index');
        Route::get('/categories/create', [BranchManagerCategoryController::class, 'create'])->name('branch.categories.create');
        Route::post('/categories', [BranchManagerCategoryController::class, 'store'])->name('branch.categories.store');
        Route::get('/categories/{category}/edit', [BranchManagerCategoryController::class, 'edit'])->name('branch.categories.edit');
        Route::put('/categories/{category}', [BranchManagerCategoryController::class, 'update'])->name('branch.categories.update');
        Route::delete('/categories/{category}', [BranchManagerCategoryController::class, 'destroy'])->name('branch.categories.destroy');

        // Subcategory Management
        Route::get('/subcategories', [BranchManagerSubcategoryController::class, 'index'])->name('branch.subcategories.index');
        Route::get('/subcategories/create', [BranchManagerSubcategoryController::class, 'create'])->name('branch.subcategories.create');
        Route::post('/subcategories', [BranchManagerSubcategoryController::class, 'store'])->name('branch.subcategories.store');
        Route::get('/subcategories/{subcategory}/edit', [BranchManagerSubcategoryController::class, 'edit'])->name('branch.subcategories.edit');
        Route::put('/subcategories/{subcategory}', [BranchManagerSubcategoryController::class, 'update'])->name('branch.subcategories.update');
        Route::delete('/subcategories/{subcategory}', [BranchManagerSubcategoryController::class, 'destroy'])->name('branch.subcategories.destroy');

        Route::get('/profile', [ProfileController::class, 'edit'])->name('branch.profile.edit');
        Route::put('/profile', [ProfileController::class, 'update'])->name('branch.profile.update');
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