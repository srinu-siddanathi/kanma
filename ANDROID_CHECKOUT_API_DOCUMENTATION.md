# Android Checkout API Documentation

This document describes the new API endpoints for calculating delivery fees and small cart fees based on user membership status, similar to the web checkout functionality.

## Overview

The checkout APIs now include membership-based calculations for:
- **Delivery Fee**: Calculated based on user's subscription plan (free orders, free delivery radius)
- **Small Cart Fee**: Applied when order is below minimum amount (waived for members)
- **Total Calculation**: Includes all fees and discounts

## Base URL
```
https://your-domain.com/api
```

## Authentication
All API endpoints require authentication using Bearer token in the Authorization header:
```
Authorization: Bearer {your_token}
```

## API Endpoints

### 1. Calculate Checkout Fees

**Endpoint:** `POST /checkout/calculate-fees`

**Authentication:** Required (Bearer Token)

**Description:** Calculate delivery fee and small cart fee based on cart items and user membership.

**Request Body:**
```json
{
    "cart_items": [
        {
            "product_id": 1,
            "quantity": 2,
            "variant_id": 5
        },
        {
            "product_id": 3,
            "quantity": 1
        }
    ],
    "delivery_latitude": 12.9716,
    "delivery_longitude": 77.5946,
    "branch_id": 1
}
```

**Response:**
```json
{
    "status": "success",
    "data": {
        "items": [
            {
                "product_id": 1,
                "variant_id": 5,
                "product_name": "Fresh Apples",
                "quantity": 2,
                "unit": "kg",
                "price": 120.00,
                "discounted_price": 108.00,
                "discount_percentage": 10,
                "total": 216.00
            }
        ],
        "subtotal": 216.00,
        "delivery_fee": 0.00,
        "small_cart_fee": 0.00,
        "total": 216.00,
        "has_active_membership": true,
        "membership_details": {
            "plan_name": "Premium Plan",
            "plan_type": "katha",
            "free_orders": 5,
            "free_delivery_radius": 10,
            "wallet_addon": 100
        },
        "minimum_order_amount": 100,
        "small_cart_fee_amount": 10,
        "breakdown": {
            "subtotal": 216.00,
            "delivery_fee": 0.00,
            "small_cart_fee": 0.00,
            "total": 216.00
        }
    }
}
```

### 2. Get Checkout Summary

**Endpoint:** `GET /checkout/summary`

**Authentication:** Required (Bearer Token)

**Description:** Get checkout summary using items from user's cart cache.

**Query Parameters:**
- `delivery_latitude` (optional): Delivery latitude
- `delivery_longitude` (optional): Delivery longitude  
- `branch_id` (optional): Branch ID for distance calculation

**Response:** Same structure as calculate-fees endpoint

### 3. Create Order (Updated)

**Endpoint:** `POST /orders`

**Authentication:** Required (Bearer Token)

**Description:** Create order with membership-based fee calculations.

**Request Body:**
```json
{
    "branch_id": 1,
    "shop_id": 1,
    "delivery_address": "123 Main St, City, State - 123456",
    "delivery_latitude": 12.9716,
    "delivery_longitude": 77.5946,
    "items": [
        {
            "product_id": 1,
            "quantity": 2,
            "variant_id": 5
        }
    ],
    "notes": "Please deliver in the morning",
    "payment_method": "cod",
    "wallet_amount_used": 0
}
```

**Response:**
```json
{
    "status": "success",
    "message": "Order created successfully",
    "data": {
        "order": {
            "id": 123,
            "user_id": 1,
            "branch_id": 1,
            "status": "pending",
            "total_amount": 216.00,
            "delivery_fee": 0.00,
            "wallet_amount_used": 0.00,
            "payment_method": "cod",
            "payment_status": "pending",
            "items": [...]
        },
        "breakdown": {
            "subtotal": 216.00,
            "delivery_fee": 0.00,
            "small_cart_fee": 0.00,
            "wallet_amount_used": 0.00,
            "total": 216.00
        },
        "membership_info": {
            "has_active_membership": true,
            "membership_details": {
                "plan_name": "Premium Plan",
                "plan_type": "katha",
                "free_orders": 5,
                "free_delivery_radius": 10,
                "wallet_addon": 100
            }
        }
    }
}
```

### 4. Cancel Order

**Endpoint:** `POST /orders/{order_id}/cancel`

**Authentication:** Required (Bearer Token)

**Description:** Cancels an existing order with proper refund handling including automatic Razorpay refunds.

**Parameters:**
- `order_id` (path parameter): The ID of the order to cancel

**Response:**
```json
{
    "status": "success",
    "message": "Order cancelled successfully",
    "data": {
        "order": {
            "id": 123,
            "status": "cancelled",
            "total_amount": 200.00,
            "delivery_fee": 0.00,
            "wallet_amount_used": 50.00,
            "payment_method": "razorpay",
            "payment_status": "completed",
            "items": [...],
            "branch": {...}
        },
        "refund_info": {
            "wallet_refunded": true,
            "wallet_amount": 50.00,
            "online_refund_required": true,
            "online_refund_processed": true,
            "online_refund_details": {
                "refund_id": "rfnd_1234567890",
                "refund_amount": 150.00,
                "refund_status": "processed",
                "refund_reason": "Order cancelled by customer"
            }
        }
    }
}
```

**Error Responses:**
```json
{
    "status": "error",
    "message": "Cannot cancel completed order"
}
```

```json
{
    "status": "error",
    "message": "Cannot cancel order that is being processed"
}
```

```json
{
    "status": "error",
    "message": "Order is already cancelled"
}
```

### 5. Get Refund Status

**Endpoint:** `GET /razorpay/refund-status`

**Authentication:** Required (Bearer Token)

**Description:** Retrieves the status of a refund for a specific order.

**Query Parameters:**
- `order_id` (required): The ID of the order to check refund status

**Response:**
```json
{
    "success": true,
    "data": {
        "order_id": 123,
        "refund_id": "rfnd_1234567890",
        "refund_amount": 150.00,
        "refund_status": "processed",
        "refund_reason": "Order cancelled by customer",
        "refund_processed_at": "2024-01-15T10:30:00Z",
        "refund_details": {
            "id": "rfnd_1234567890",
            "amount": 15000,
            "currency": "INR",
            "status": "processed",
            "created_at": 1705312200
        }
    }
}
```

**Error Response:**
```json
{
    "success": false,
    "message": "No refund found for this order"
}
```

### 6. Get User Orders

**Endpoint:** `GET /orders`

**Authentication:** Required (Bearer Token)

**Description:** Retrieves all orders for the authenticated user.

**Query Parameters:**
- `status` (optional): Filter by order status (pending, processing, completed, cancelled)
- `page` (optional): Page number for pagination

**Response:**
```json
{
    "status": "success",
    "data": {
        "current_page": 1,
        "data": [
            {
                "id": 123,
                "status": "pending",
                "total_amount": 200.00,
                "delivery_fee": 0.00,
                "payment_method": "cod",
                "payment_status": "pending",
                "created_at": "2024-01-15T10:30:00Z",
                "items": [...],
                "branch": {...}
            }
        ],
        "total": 10,
        "per_page": 15
    }
}
```

### 7. Get Order Details

**Endpoint:** `GET /orders/{order_id}`

**Authentication:** Required (Bearer Token)

**Description:** Retrieves detailed information about a specific order.

**Response:**
```json
{
    "status": "success",
    "data": {
        "id": 123,
        "status": "pending",
        "total_amount": 200.00,
        "delivery_fee": 0.00,
        "wallet_amount_used": 0.00,
        "delivery_address": "123 Main St, City",
        "delivery_latitude": 12.9716,
        "delivery_longitude": 77.5946,
        "notes": "Please deliver before 6 PM",
        "payment_method": "cod",
        "payment_status": "pending",
        "created_at": "2024-01-15T10:30:00Z",
        "items": [
            {
                "id": 1,
                "product_id": 1,
                "quantity": 2,
                "price": 100.00,
                "subtotal": 200.00,
                "product": {
                    "id": 1,
                    "name": "Fresh Apples",
                    "image": "products/apples.jpg",
                    "price": 100.00
                }
            }
        ],
        "branch": {
            "id": 1,
            "name": "Main Branch",
            "address": "456 Branch St",
            "phone": "+1234567890"
        }
    }
}
```

## Membership Logic

### Delivery Fee Calculation

1. **Default Delivery Fee:** ₹50
2. **Free Orders:** If user has active membership with free orders remaining
3. **Free Delivery Radius:** If delivery is within the plan's free delivery radius
4. **Distance Calculation:** Uses Haversine formula to calculate distance between branch and delivery location

### Small Cart Fee Calculation

1. **Default Small Cart Fee:** ₹10
2. **Minimum Order Amount:** ₹100 (configurable)
3. **Membership Waiver:** Small cart fee is waived for users with active membership
4. **Application:** Applied only when subtotal < minimum order amount AND user has no active membership

### Membership Benefits

- **Free Orders:** Number of free delivery orders per subscription period
- **Free Delivery Radius:** Free delivery within specified radius (in kilometers)
- **Small Cart Fee Waiver:** No small cart fee regardless of order amount
- **Wallet Addon:** Additional wallet balance added on subscription

## Error Responses

### Validation Errors
```json
{
    "status": "error",
    "message": "The cart items field is required."
}
```

### Insufficient Wallet Balance
```json
{
    "status": "error",
    "message": "Insufficient wallet balance"
}
```

### Server Errors
```json
{
    "status": "error",
    "message": "Error calculating checkout fees: [error details]"
}
```

## Implementation Examples

### Android/Kotlin Example

```kotlin
// Calculate fees
fun calculateCheckoutFees(cartItems: List<CartItem>, latitude: Double, longitude: Double) {
    val requestBody = mapOf(
        "cart_items" to cartItems.map { 
            mapOf(
                "product_id" to it.productId,
                "quantity" to it.quantity,
                "variant_id" to it.variantId
            )
        },
        "delivery_latitude" to latitude,
        "delivery_longitude" to longitude,
        "branch_id" to 1
    )
    
    apiService.calculateFees(requestBody).enqueue(object : Callback<CheckoutResponse> {
        override fun onResponse(call: Call<CheckoutResponse>, response: Response<CheckoutResponse>) {
            if (response.isSuccessful) {
                val checkoutData = response.body()?.data
                // Update UI with calculated fees
                updateCheckoutUI(checkoutData)
            }
        }
        
        override fun onFailure(call: Call<CheckoutResponse>, t: Throwable) {
            // Handle error
        }
    })
}

// Create order
fun createOrder(orderRequest: OrderRequest) {
    apiService.createOrder(orderRequest).enqueue(object : Callback<OrderResponse> {
        override fun onResponse(call: Call<OrderResponse>, response: Response<OrderResponse>) {
            if (response.isSuccessful) {
                val orderData = response.body()?.data
                // Handle successful order creation
                handleOrderSuccess(orderData)
            }
        }
        
        override fun onFailure(call: Call<OrderResponse>, t: Throwable) {
            // Handle error
        }
    })
}
```

### React Native Example

```javascript
// Calculate fees
const calculateCheckoutFees = async (cartItems, latitude, longitude) => {
  try {
    const response = await api.post('/checkout/calculate-fees', {
      cart_items: cartItems.map(item => ({
        product_id: item.productId,
        quantity: item.quantity,
        variant_id: item.variantId
      })),
      delivery_latitude: latitude,
      delivery_longitude: longitude,
      branch_id: 1
    });
    
    const checkoutData = response.data.data;
    // Update UI with calculated fees
    setCheckoutData(checkoutData);
  } catch (error) {
    console.error('Error calculating fees:', error);
  }
};

// Create order
const createOrder = async (orderData) => {
  try {
    const response = await api.post('/orders', orderData);
    const orderResult = response.data.data;
    // Handle successful order creation
    handleOrderSuccess(orderResult);
  } catch (error) {
    console.error('Error creating order:', error);
  }
};
```

## Testing

### Test Cases

1. **User without membership:**
   - Order < ₹100: Should include small cart fee
   - Order ≥ ₹100: No small cart fee
   - Delivery fee: ₹50 (default)

2. **User with membership:**
   - Small cart fee: Always waived
   - Free orders remaining: Delivery fee = ₹0
   - Within free delivery radius: Delivery fee = ₹0
   - Outside radius + no free orders: Delivery fee = ₹50

3. **Wallet payment:**
   - Insufficient balance: Should return error
   - Sufficient balance: Should deduct from wallet

### Sample Test Data

```json
{
  "test_cart_items": [
    {
      "product_id": 1,
      "quantity": 1,
      "variant_id": null
    }
  ],
  "test_delivery_coordinates": {
    "latitude": 12.9716,
    "longitude": 77.5946
  }
}
```

## Configuration

The following settings can be configured in the admin panel:

- `minimum_order_amount`: Minimum order amount before small cart fee (default: ₹100)
- `small_cart_fee`: Amount charged for small orders (default: ₹10)
- Default delivery fee: ₹50 (hardcoded)

## Notes

1. **Distance Calculation:** Uses Haversine formula for accurate distance calculation
2. **Caching:** Cart items are cached using user ID as key
3. **Membership Status:** Checked in real-time for each request
4. **Error Handling:** Comprehensive error handling with detailed messages
5. **Logging:** All errors are logged for debugging purposes

## Support

For any questions or issues with the API implementation, please contact the development team.

## Refund Process

### Automatic Refund Handling

When an order is cancelled, the system automatically handles refunds based on the payment method:

1. **Wallet Payments:**
   - Automatically refunded to user's wallet balance
   - Immediate credit to account
   - No external processing required

2. **Razorpay Payments:**
   - Automatic refund processing through Razorpay API
   - Full or partial refund support
   - Refund status tracking available
   - Refund processed in normal speed (3-5 business days)

3. **Cash on Delivery (COD):**
   - No refund required
   - Payment not yet collected

### Refund Status Tracking

- **Refund ID:** Unique identifier for tracking refund
- **Refund Status:** processed, pending, failed
- **Refund Amount:** Amount being refunded
- **Refund Reason:** Reason for cancellation
- **Processing Time:** 3-5 business days for Razorpay refunds

### Refund Response Details

The cancel order API returns detailed refund information:

```json
{
    "refund_info": {
        "wallet_refunded": true,
        "wallet_amount": 50.00,
        "online_refund_required": true,
        "online_refund_processed": true,
        "online_refund_details": {
            "refund_id": "rfnd_1234567890",
            "refund_amount": 150.00,
            "refund_status": "processed",
            "refund_reason": "Order cancelled by customer"
        }
    }
}
```

## Error Handling 