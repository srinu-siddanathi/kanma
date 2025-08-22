# Shop Order Integration Feature

## Overview
This feature allows orders to be created with an associated shop_id, enabling the system to track which shop an order belongs to. This is particularly useful for shop products where orders need to be associated with specific shops.

## Changes Made

### 1. API Order Creation Endpoint
**File:** `app/Http/Controllers/Api/OrderController.php`

**Changes:**
- Added `shop_id` parameter to validation rules (nullable, exists in shops table)
- Updated order creation to include `shop_id` from request
- Updated response to load shop relationship

**API Request Example:**
```json
{
    "branch_id": 1,
    "shop_id": 1,
    "delivery_address": "123 Main Street, Bangalore, Karnataka - 560001",
    "delivery_latitude": 12.9716,
    "delivery_longitude": 77.5946,
    "items": [
        {
            "product_id": 1,
            "quantity": 2,
            "variant_id": null
        }
    ],
    "notes": "Please deliver in the morning",
    "payment_method": "cod",
    "wallet_amount_used": 0
}
```

### 2. Admin Order Management
**Files:** 
- `app/Http/Controllers/Admin/OrderController.php`
- `resources/views/admin/orders/index.blade.php`
- `resources/views/admin/orders/show.blade.php`

**Changes:**
- Updated controllers to load shop relationship
- Added shop information column to orders index table
- Added shop details to order show page
- Shows shop name and shop owner name when available

### 3. Branch Manager Order Management
**Files:**
- `app/Http/Controllers/BranchManager/OrderController.php`
- `app/Http/Controllers/BranchManager/DashboardController.php`
- `resources/views/branch-manager/orders/index.blade.php`
- `resources/views/branch-manager/orders/show.blade.php`
- `resources/views/branch-manager/dashboard.blade.php`
- `resources/views/branch/orders/history.blade.php`

**Changes:**
- Updated controllers to load shop relationship
- Added shop information column to orders index table
- Created new order show view with shop details
- Added shop information to dashboard recent orders
- Added shop information to order history table
- Added route for order show page

### 4. Database Schema
**File:** `database/migrations/2024_03_11_add_shop_id_to_orders_table.php`

The `shop_id` column was already added to the orders table with:
- Foreign key constraint to shops table
- Nullable field
- Cascade delete set to null

### 5. Model Relationships
**File:** `app/Models/Order.php`

The Order model already had:
- `shop_id` in fillable array
- `shop()` relationship method

### 6. API Documentation Updates
**Files:**
- `ANDROID_CHECKOUT_API_DOCUMENTATION.md`
- `Kanma_Checkout_API.postman_collection.json`

**Changes:**
- Updated API documentation to include `shop_id` parameter
- Updated Postman collection examples with `shop_id`

### 7. Testing
**File:** `tests/Feature/ShopOrderCreationTest.php`

Created comprehensive tests for:
- Order creation with valid shop_id
- Order creation without shop_id (backward compatibility)
- Validation of invalid shop_id

## Features

### 1. Shop Order Creation
- Orders can be created with or without a shop_id
- When shop_id is provided, it's validated to ensure the shop exists
- Orders without shop_id are treated as regular orders

### 2. Admin View
- Admins can see shop information for all orders
- Shop name and shop owner name are displayed
- Orders from shops are clearly identified

### 3. Branch Manager View
- Branch managers can see shop information for orders in their branch
- Shop details are shown in order lists and detail views
- Recent orders on dashboard include shop information

### 4. Backward Compatibility
- Existing orders without shop_id continue to work normally
- API accepts orders with or without shop_id
- All existing functionality remains intact

## Usage

### Creating Orders with Shop
```php
// API call with shop_id
$response = $this->postJson('/api/orders', [
    'branch_id' => 1,
    'shop_id' => 1, // Shop ID for shop products
    'delivery_address' => '...',
    // ... other order data
]);
```

### Creating Regular Orders
```php
// API call without shop_id (existing behavior)
$response = $this->postJson('/api/orders', [
    'branch_id' => 1,
    // shop_id not provided for regular orders
    'delivery_address' => '...',
    // ... other order data
]);
```

## Benefits

1. **Shop Product Tracking**: Orders can be associated with specific shops
2. **Better Order Management**: Admins and branch managers can see shop context
3. **Flexible System**: Supports both shop orders and regular orders
4. **Backward Compatibility**: Existing functionality remains unchanged
5. **Clear Visibility**: Shop information is prominently displayed in all order views

## Testing

Run the tests to verify the implementation:
```bash
php artisan test tests/Feature/ShopOrderCreationTest.php
```

## Future Enhancements

1. **Shop-specific Analytics**: Generate reports for individual shops
2. **Shop Order Filtering**: Filter orders by shop in admin/branch manager views
3. **Shop Notifications**: Send notifications to shop owners for their orders
4. **Shop Commission Tracking**: Track commissions for shop orders 