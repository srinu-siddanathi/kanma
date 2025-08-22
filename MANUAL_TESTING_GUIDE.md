# Manual Testing Guide - Shop Order Integration

## Overview
This guide provides step-by-step instructions to manually test the shop order integration feature.

## Prerequisites
1. Laravel application running
2. Database seeded with test data
3. API authentication token

## Test 1: Create Order with Shop ID

### Step 1: Prepare Test Data
First, ensure you have test data in your database:
```bash
php artisan db:seed
```

### Step 2: Get Authentication Token
```bash
# Login and get token
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "test@example.com",
    "password": "password"
  }'
```

### Step 3: Create Order with Shop ID
```bash
curl -X POST http://localhost:8000/api/orders \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Content-Type: application/json" \
  -d '{
    "branch_id": 1,
    "shop_id": 1,
    "delivery_address": "123 Test Street, Test City - 123456",
    "delivery_latitude": 12.9716,
    "delivery_longitude": 77.5946,
    "items": [
      {
        "product_id": 1,
        "quantity": 2,
        "variant_id": null
      }
    ],
    "notes": "Test order with shop",
    "payment_method": "cod",
    "wallet_amount_used": 0
  }'
```

**Expected Response:**
```json
{
  "status": "success",
  "message": "Order created successfully",
  "data": {
    "order": {
      "id": 123,
      "user_id": 1,
      "branch_id": 1,
      "shop_id": 1,
      "status": "pending",
      "total_amount": 200.00,
      "shop": {
        "id": 1,
        "name": "Test Shop",
        "user": {
          "id": 2,
          "name": "Shop Owner"
        }
      }
    }
  }
}
```

## Test 2: Create Order without Shop ID (Backward Compatibility)

```bash
curl -X POST http://localhost:8000/api/orders \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Content-Type: application/json" \
  -d '{
    "branch_id": 1,
    "delivery_address": "123 Test Street, Test City - 123456",
    "delivery_latitude": 12.9716,
    "delivery_longitude": 77.5946,
    "items": [
      {
        "product_id": 1,
        "quantity": 2,
        "variant_id": null
      }
    ],
    "notes": "Test order without shop",
    "payment_method": "cod",
    "wallet_amount_used": 0
  }'
```

**Expected Response:**
```json
{
  "status": "success",
  "message": "Order created successfully",
  "data": {
    "order": {
      "id": 124,
      "user_id": 1,
      "branch_id": 1,
      "shop_id": null,
      "status": "pending",
      "total_amount": 200.00
    }
  }
}
```

## Test 3: Validation Error for Invalid Shop ID

```bash
curl -X POST http://localhost:8000/api/orders \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Content-Type: application/json" \
  -d '{
    "branch_id": 1,
    "shop_id": 99999,
    "delivery_address": "123 Test Street, Test City - 123456",
    "delivery_latitude": 12.9716,
    "delivery_longitude": 77.5946,
    "items": [
      {
        "product_id": 1,
        "quantity": 2,
        "variant_id": null
      }
    ],
    "notes": "Test order with invalid shop",
    "payment_method": "cod",
    "wallet_amount_used": 0
  }'
```

**Expected Response:**
```json
{
  "message": "The selected shop id is invalid.",
  "errors": {
    "shop_id": [
      "The selected shop id is invalid."
    ]
  }
}
```

## Test 4: Admin Order View

1. Login to admin panel
2. Navigate to Orders section
3. Verify that orders show shop information:
   - Shop name column is present
   - Shop owner name is displayed
   - Orders without shop show "-" in shop column

## Test 5: Branch Manager Order View

1. Login as branch manager
2. Navigate to Orders section
3. Verify that orders show shop information:
   - Shop name column is present
   - Shop details are shown in order lists
   - Recent orders on dashboard include shop information

## Test 6: Order Details View

1. Click on any order with shop_id
2. Verify shop information is displayed:
   - Shop name
   - Shop owner name
   - Shop details in order information

## Verification Checklist

### API Level
- [ ] Orders can be created with valid shop_id
- [ ] Orders can be created without shop_id (backward compatibility)
- [ ] Invalid shop_id returns validation error
- [ ] API response includes shop information when shop_id is provided

### Admin Interface
- [ ] Orders index shows shop column
- [ ] Shop name and owner are displayed
- [ ] Orders without shop show "-" in shop column
- [ ] Order details page shows shop information

### Branch Manager Interface
- [ ] Orders index shows shop column
- [ ] Shop information is displayed in order lists
- [ ] Dashboard recent orders include shop information
- [ ] Order details page shows shop information

### Database
- [ ] Orders table has shop_id column
- [ ] shop_id can be null (for regular orders)
- [ ] shop_id foreign key constraint works
- [ ] Order model has shop() relationship

## Troubleshooting

### Common Issues

1. **"shop_id validation error"**
   - Ensure shop with the provided ID exists in database
   - Check that shop is active and verified

2. **"Shop information not showing in admin/branch manager views"**
   - Verify that shop relationship is loaded in controllers
   - Check that shop data exists in database

3. **"API returns 500 error"**
   - Check database migrations are run
   - Verify shop_id column exists in orders table
   - Check server logs for specific error messages

### Database Verification

```sql
-- Check if shop_id column exists
DESCRIBE orders;

-- Check if shop relationship works
SELECT o.id, o.shop_id, s.name as shop_name, u.name as owner_name
FROM orders o
LEFT JOIN shops s ON o.shop_id = s.id
LEFT JOIN users u ON s.user_id = u.id
LIMIT 10;
```

## Success Criteria

The implementation is successful if:
1. ✅ Orders can be created with shop_id parameter
2. ✅ Orders can be created without shop_id (backward compatibility)
3. ✅ Admin and branch manager views show shop information
4. ✅ API validation works correctly
5. ✅ Database relationships function properly
6. ✅ No breaking changes to existing functionality 