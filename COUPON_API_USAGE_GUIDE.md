# Coupon API Usage Guide

This guide explains how to use the coupon APIs in your mobile application.

## API Flow Overview

1. **Validate and Apply Coupon** - Before order creation
2. **Checkout Summary** - Include coupon_id to calculate discount
3. **Order Creation** - Attach coupon to order
4. **Payment Outcome** - Confirm or fail coupon usage based on payment result

## API Endpoints

### 1. Validate and Apply Coupon

**Endpoint:** `POST /api/coupons/validate-and-apply`

**Description:** Validates a coupon code and creates a pending usage record. This should be called before creating the order.

**Request Body:**
```json
{
    "coupon_code": "SAVE20",
    "user_id": 1,
    "order_amount": 500.00
}
```

**Response:**
```json
{
    "success": true,
    "message": "Coupon applied successfully",
    "data": {
        "coupon_id": 1,
        "coupon_code": "SAVE20",
        "discount_amount": 100.00,
        "discount_type": "percentage",
        "discount_value": 20,
        "final_amount": 400.00,
        "usage_id": 5
    }
}
```

**Error Response:**
```json
{
    "success": false,
    "message": "Invalid coupon code"
}
```

### 2. Get Available Coupons

**Endpoint:** `GET /api/coupons/available?user_id=1`

**Description:** Returns all available coupons for a user.

**Response:**
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "code": "SAVE20",
            "title": "20% Off",
            "description": "Get 20% off on orders above ₹500",
            "discount_type": "percentage",
            "discount_value": 20,
            "min_order_amount": 500.00,
            "expires_at": "2024-12-31T23:59:59.000000Z",
            "usage_limit": 100,
            "usage_limit_per_user": 1
        }
    ]
}
```

### 3. Confirm Coupon Usage

**Endpoint:** `POST /api/coupons/confirm-usage`

**Description:** Confirms coupon usage after successful payment.

**Request Body:**
```json
{
    "usage_id": 5
}
```

**Response:**
```json
{
    "success": true,
    "message": "Coupon usage confirmed"
}
```

### 4. Fail Coupon Usage

**Endpoint:** `POST /api/coupons/fail-usage`

**Description:** Marks coupon usage as failed after payment failure.

**Request Body:**
```json
{
    "usage_id": 5
}
```

**Response:**
```json
{
    "success": true,
    "message": "Coupon usage marked as failed"
}
```

## Integration with Checkout Flow

### Step 1: Validate and Apply Coupon
```javascript
// Before creating order
const couponResponse = await fetch('/api/coupons/validate-and-apply', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
        coupon_code: 'SAVE20',
        user_id: userId,
        order_amount: cartTotal
    })
});

const couponData = await couponResponse.json();
if (couponData.success) {
    // Store usage_id for later use
    const usageId = couponData.data.usage_id;
    const discountAmount = couponData.data.discount_amount;
    const finalAmount = couponData.data.final_amount;
}
```

### Step 2: Checkout Summary API
```javascript
// Call checkout summary API with coupon_id
const checkoutResponse = await fetch('/api/checkout/calculate-fees', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
        cart_items: cartItems,
        coupon_id: couponData.data.coupon_id, // Include coupon_id
        delivery_latitude: deliveryLat,
        delivery_longitude: deliveryLng,
        branch_id: branchId
    })
});

const checkoutData = await checkoutResponse.json();
if (checkoutData.status === 'success') {
    // Use the calculated totals from checkout summary
    const finalTotal = checkoutData.data.total;
    const couponDiscount = checkoutData.data.coupon_discount;
}
```

### Step 3: Order Creation
```javascript
// Create order with coupon_id attached
const orderResponse = await fetch('/api/orders', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
        branch_id: branchId,
        delivery_address: deliveryAddress,
        delivery_latitude: deliveryLat,
        delivery_longitude: deliveryLng,
        items: cartItems,
        payment_method: 'cod',
        coupon_id: couponData.data.coupon_id, // Attach coupon to order
        // ... other order data
    })
});
```

### Step 4: Payment Processing
```javascript
// Process payment
const paymentResult = await processPayment(orderData);

if (paymentResult.success) {
    // Confirm coupon usage
    await fetch('/api/coupons/confirm-usage', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ usage_id: usageId })
    });
} else {
    // Fail coupon usage
    await fetch('/api/coupons/fail-usage', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ usage_id: usageId })
    });
}
```

## Updated API Responses

### Checkout Summary Response (with coupon)
```json
{
    "status": "success",
    "data": {
        "items": [...],
        "subtotal": 500.00,
        "coupon_discount": 100.00,
        "coupon_details": {
            "id": 1,
            "code": "SAVE20",
            "title": "20% Off",
            "discount_type": "percentage",
            "discount_value": 20,
            "discount_amount": 100.00
        },
        "total_after_coupon": 400.00,
        "delivery_fee": 50.00,
        "small_cart_fee": 0.00,
        "total": 450.00,
        "breakdown": {
            "subtotal": 500.00,
            "coupon_discount": 100.00,
            "total_after_coupon": 400.00,
            "delivery_fee": 50.00,
            "small_cart_fee": 0.00,
            "total": 450.00
        }
    }
}
```

### Order Creation Response (with coupon)
```json
{
    "status": "success",
    "message": "Order created successfully",
    "data": {
        "order": {
            "id": 123,
            "coupon_id": 1,
            "total_amount": 450.00,
            "coupon": {
                "id": 1,
                "code": "SAVE20",
                "title": "20% Off"
            }
        },
        "breakdown": {
            "subtotal": 500.00,
            "coupon_discount": 100.00,
            "total_after_coupon": 400.00,
            "delivery_fee": 50.00,
            "small_cart_fee": 0.00,
            "total": 450.00
        },
        "coupon_info": {
            "id": 1,
            "code": "SAVE20",
            "title": "20% Off",
            "discount_type": "percentage",
            "discount_value": 20,
            "discount_amount": 100.00
        }
    }
}
```

## Important Notes

1. **Usage ID Tracking**: Always store the `usage_id` returned from validate-and-apply API to use in confirm/fail APIs.

2. **Payment Failure Handling**: If payment fails, always call the fail-usage API to prevent coupon misuse.

3. **Order Amount Validation**: The coupon validation checks minimum order amount, so ensure the order_amount parameter is accurate.

4. **User Limits**: The system tracks per-user usage limits, so the same user cannot use a coupon more than the allowed times.

5. **Status Management**: Coupon usages have three states:
   - `pending`: Created when coupon is applied
   - `confirmed`: After successful payment
   - `failed`: After payment failure

6. **Checkout Summary**: Always use the checkout summary API to get accurate totals including coupon discounts before creating the order.

7. **Order Creation**: The order creation API now accepts `coupon_id` and will attach it to the order for tracking.

## Error Handling

Common error scenarios and their responses:

- **Invalid Coupon**: `404` - Coupon code doesn't exist or is inactive
- **Expired Coupon**: `400` - Coupon has passed its expiry date
- **Minimum Amount**: `400` - Order amount below minimum requirement
- **Usage Limit**: `400` - User or total usage limit reached
- **Invalid Status**: `400` - Trying to confirm/fail already processed usage

## Testing

Use the provided Postman collection to test all coupon APIs:

1. Create test coupons in admin panel
2. Test validate-and-apply with valid/invalid codes
3. Test checkout summary with coupon_id
4. Test order creation with coupon_id
5. Test payment success/failure scenarios
6. Verify usage tracking in admin panel 