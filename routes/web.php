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
// use App\Http\Controllers\Auth\RegisteredUserController;
// use App\Http\Controllers\Auth\PasswordResetLinkController;
// use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Branch\DeliveryBoyController;
use App\Http\Controllers\Branch\OrderAssignmentController;
use App\Http\Controllers\ShopOwner\DashboardController as ShopOwnerDashboardController;
use App\Http\Controllers\ShopOwner\ProductController as ShopOwnerProductController;
use App\Http\Controllers\ShopOwner\OrderController as ShopOwnerOrderController;
use App\Http\Controllers\ShopOwner\ProfileController as ShopOwnerProfileController;
use App\Http\Controllers\Admin\ShopController as AdminShopController;
use App\Http\Controllers\Admin\AdminProfileController;
use App\Http\Controllers\Admin\AdminSettingsController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\Frontend\ShopController;
use App\Http\Controllers\Frontend\ProductController as FrontendProductController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\Admin\SubscriptionController as AdminSubscriptionController;
use App\Http\Controllers\SubscriptionController;

// Public routes
Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/home', [HomeController::class, 'index'])->name('home.original');

Route::get('/shop', [ShopController::class, 'index'])->name('shop');
Route::get('/product/{id}', [FrontendProductController::class, 'show'])->name('product.show');
Route::get('/shop/{id}', [App\Http\Controllers\ShopController::class, 'show'])->name('shop.show');

// Subscription routes
Route::middleware(['auth'])->group(function () {
    Route::get('/subscription/checkout/{plan}', [SubscriptionController::class, 'checkout'])->name('subscription.checkout');
    Route::post('/subscription/process/{plan}', [SubscriptionController::class, 'process'])->name('subscription.process');
    Route::post('/subscription/verify-payment', [SubscriptionController::class, 'verifyPayment'])->name('subscription.verify-payment');
});

// Category route that redirects to shop with category parameter
Route::get('/category/{category}', function ($category) {
    return redirect()->route('shop', ['category' => $category]);
})->name('category.show');

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

        // Active Subscriptions Management
        Route::get('/subscriptions', [AdminSubscriptionController::class, 'index'])->name('subscriptions.index');
        Route::get('/subscriptions/{subscription}', [AdminSubscriptionController::class, 'show'])->name('subscriptions.show');
        Route::put('/subscriptions/{subscription}/cancel', [AdminSubscriptionController::class, 'cancel'])->name('subscriptions.cancel');

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
        Route::delete('/products/images/{image}', [AdminProductController::class, 'deleteImage'])->name('products.images.delete');

        // Branch management routes (using resource controller)
        Route::resource('branches', BranchController::class);

        // Shop Management
        Route::get('/shops', [AdminShopController::class, 'index'])->name('shops.index');
        Route::get('/shops/{shop}', [AdminShopController::class, 'show'])->name('shops.show');
        Route::post('/shops/{shop}/approve', [AdminShopController::class, 'approve'])->name('shops.approve');
        Route::post('/shops/{shop}/reject', [AdminShopController::class, 'reject'])->name('shops.reject');
        Route::post('/shops/{shop}/verify', [AdminShopController::class, 'verify'])->name('shops.verify');

        // Delivery Boys Management
        Route::get('/delivery-boys', [App\Http\Controllers\Admin\DeliveryBoyController::class, 'index'])->name('delivery-boys.index');
        Route::get('/delivery-boys/create', [App\Http\Controllers\Admin\DeliveryBoyController::class, 'create'])->name('delivery-boys.create');
        Route::post('/delivery-boys', [App\Http\Controllers\Admin\DeliveryBoyController::class, 'store'])->name('delivery-boys.store');
        Route::get('/delivery-boys/{deliveryBoy}/edit', [App\Http\Controllers\Admin\DeliveryBoyController::class, 'edit'])->name('delivery-boys.edit');
        Route::put('/delivery-boys/{deliveryBoy}', [App\Http\Controllers\Admin\DeliveryBoyController::class, 'update'])->name('delivery-boys.update');
        Route::post('/delivery-boys/{deliveryBoy}/toggle-status', [App\Http\Controllers\Admin\DeliveryBoyController::class, 'toggleStatus'])->name('delivery-boys.toggle-status');

        // Newsletter Management
        Route::get('/newsletters', [\App\Http\Controllers\Admin\NewsletterSubscriberController::class, 'index'])->name('newsletters.index');
        Route::get('/newsletters/export', [\App\Http\Controllers\Admin\NewsletterSubscriberController::class, 'exportCsv'])->name('newsletters.export');
        Route::delete('/newsletters/{id}', [\App\Http\Controllers\Admin\NewsletterSubscriberController::class, 'destroy'])->name('newsletters.destroy');

        // Admin Settings Routes
        Route::get('/settings', [App\Http\Controllers\Admin\SettingController::class, 'index'])->name('settings.index');
        Route::put('/settings', [App\Http\Controllers\Admin\SettingController::class, 'update'])->name('settings.update');
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
        Route::put('/password', [ProfileController::class, 'updatePassword'])->name('password.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

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

// Cart routes
Route::post('/cart/add', [CartController::class, 'addToCart'])->name('cart.add');
Route::post('/cart/remove', [CartController::class, 'removeFromCart'])->name('cart.remove');
Route::post('/cart/update', [CartController::class, 'updateCart'])->name('cart.update');
Route::get('/cart/items', [CartController::class, 'getCartItems'])->name('cart.items');

Route::get('/cart', function () {
    return view('cart');
})->name('cart');

// Checkout routes
Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout');
Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');

// Location Modal Routes (public)
Route::post('/location/check-serviceability', [App\Http\Controllers\AddressController::class, 'checkServiceability'])->name('location.check-serviceability');
Route::post('/location/clear', [App\Http\Controllers\AddressController::class, 'clearSelectedAddress'])->name('location.clear');

Route::middleware(['auth'])->group(function () {
    Route::get('/orders', [App\Http\Controllers\OrderController::class, 'index'])->name('orders');
    Route::get('/orders/{order}', [App\Http\Controllers\OrderController::class, 'show'])->name('orders.show');
    Route::get('/subscriptions', [App\Http\Controllers\SubscriptionController::class, 'index'])->name('subscriptions');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/password', [ProfileController::class, 'updatePassword'])->name('password.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Address Management Routes
    Route::get('/addresses', [App\Http\Controllers\AddressController::class, 'index'])->name('addresses.index');
    Route::get('/addresses/create', [App\Http\Controllers\AddressController::class, 'create'])->name('addresses.create');
    Route::post('/addresses', [App\Http\Controllers\AddressController::class, 'store'])->name('addresses.store');
    Route::get('/addresses/{address}', [App\Http\Controllers\AddressController::class, 'show'])->name('addresses.show');
    Route::get('/addresses/{address}/edit', [App\Http\Controllers\AddressController::class, 'edit'])->name('addresses.edit');
    Route::put('/addresses/{address}', [App\Http\Controllers\AddressController::class, 'update'])->name('addresses.update');
    Route::delete('/addresses/{address}', [App\Http\Controllers\AddressController::class, 'destroy'])->name('addresses.destroy');
    Route::post('/addresses/{address}/set-default', [App\Http\Controllers\AddressController::class, 'setDefault'])->name('addresses.set-default');

    // Location Modal Routes (authenticated)
    Route::get('/location/addresses', [App\Http\Controllers\AddressController::class, 'getAddressesForModal'])->name('location.addresses');
});

// Auth routes
Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
Route::post('/login', [AuthenticatedSessionController::class, 'store']);
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
// Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
// Route::post('/register', [RegisteredUserController::class, 'store']);

Route::post('/ajax-login', [App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'ajaxLogin'])->name('ajax.login');

Route::post('/newsletter', [NewsletterController::class, 'store'])->name('newsletter.store');

Route::get('/search', [App\Http\Controllers\Frontend\ShopController::class, 'search'])->name('search');

Route::get('/api/search-suggestions', [App\Http\Controllers\Frontend\ShopController::class, 'searchSuggestions'])->name('search.suggestions');

Route::get('/shops', [App\Http\Controllers\ShopController::class, 'allShops'])->name('shops.all');

// Registration routes
Route::post('/send-otp', [App\Http\Controllers\Auth\RegisterController::class, 'sendOtp'])->name('send.otp');
Route::post('/verify-otp', [App\Http\Controllers\Auth\RegisterController::class, 'verifyOtp'])->name('verify.otp');
Route::post('/register', [App\Http\Controllers\Auth\RegisterController::class, 'register'])->name('register');