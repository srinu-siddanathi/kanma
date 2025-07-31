# Refund System Documentation

## Overview

The refund system automatically handles refunds when orders are cancelled, supporting both wallet refunds and Razorpay payment refunds. The system is designed to be robust, secure, and provide comprehensive tracking of all refund activities.

## Features

### ✅ Automatic Refund Processing
- **Wallet Refunds**: Immediate credit to user's wallet balance
- **Razorpay Refunds**: Automatic API calls to Razorpay for payment refunds
- **Mixed Payments**: Handles orders with both wallet and online payments

### ✅ Comprehensive Tracking
- **Refund Status**: Real-time tracking of refund status
- **Error Handling**: Detailed error logging and user feedback
- **Audit Trail**: Complete history of all refund activities

### ✅ Security Features
- **Authorization**: Users can only cancel their own orders
- **Validation**: Prevents duplicate refunds and invalid operations
- **Transaction Safety**: Database transactions ensure data consistency

## API Endpoints

### 1. Cancel Order with Automatic Refunds

**Endpoint:** `POST /api/orders/{order_id}/cancel`

**Authentication:** Required (Bearer Token)

**Description:** Cancels an order and automatically processes refunds for wallet and Razorpay payments.

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
            "wallet_amount_used": 50.00,
            "payment_method": "razorpay",
            "payment_status": "completed",
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
                },
                "refund_errors": []
            }
        }
    }
}
```

### 2. Get Comprehensive Refund Status

**Endpoint:** `GET /api/razorpay/order-refund-status?order_id={order_id}`

**Authentication:** Required (Bearer Token)

**Description:** Returns detailed refund status for both wallet and Razorpay refunds.

**Response:**
```json
{
    "success": true,
    "data": {
        "order_id": 123,
        "order_status": "cancelled",
        "payment_method": "razorpay",
        "payment_status": "completed",
        "total_amount": 200.00,
        "wallet_amount_used": 50.00,
        "refunds": {
            "wallet": {
                "amount": 50.00,
                "status": "processed",
                "processed_at": "2024-01-15T10:30:00Z",
                "transaction_id": 456
            },
            "razorpay": {
                "refund_id": "rfnd_1234567890",
                "amount": 150.00,
                "status": "processed",
                "reason": "Order cancelled by customer",
                "processed_at": "2024-01-15T10:30:00Z",
                "refund_details": {
                    "id": "rfnd_1234567890",
                    "amount": 15000,
                    "currency": "INR",
                    "status": "processed"
                }
            }
        },
        "order_refund_info": {
            "wallet_refunded": true,
            "online_refund_processed": true,
            "refund_errors": []
        }
    }
}
```

### 3. Get Razorpay Refund Status

**Endpoint:** `GET /api/razorpay/refund-status?order_id={order_id}`

**Authentication:** Required (Bearer Token)

**Description:** Returns Razorpay-specific refund status.

## Database Schema

### Orders Table
```sql
ALTER TABLE orders ADD COLUMN refund_info JSON NULL;
ALTER TABLE orders ADD COLUMN cancelled_at TIMESTAMP NULL;
ALTER TABLE orders ADD COLUMN cancellation_reason VARCHAR(255) NULL;
```

### Payments Table
```sql
ALTER TABLE payments ADD COLUMN refund_id VARCHAR(255) NULL;
ALTER TABLE payments ADD COLUMN refund_amount DECIMAL(10,2) NULL;
ALTER TABLE payments ADD COLUMN refund_status VARCHAR(50) NULL;
ALTER TABLE payments ADD COLUMN refund_reason TEXT NULL;
```

### Wallet Transactions Table
```sql
-- Tracks wallet refunds with reference to cancelled orders
CREATE TABLE wallet_transactions (
    id BIGINT PRIMARY KEY,
    user_id BIGINT,
    amount DECIMAL(10,2),
    type ENUM('credit', 'debit'),
    description VARCHAR(255),
    reference_type VARCHAR(255),
    reference_id BIGINT,
    metadata JSON,
    created_at TIMESTAMP
);
```

## Refund Flow

### 1. Order Cancellation Request
```
User requests order cancellation
↓
Validate user authorization
↓
Check order status (must be cancellable)
↓
Begin database transaction
```

### 2. Wallet Refund Processing
```
Check if wallet was used
↓
If yes, credit amount back to wallet
↓
Create wallet transaction record
↓
Log refund activity
```

### 3. Razorpay Refund Processing
```
Check if Razorpay payment was made
↓
If yes, call Razorpay refund API
↓
Update payment record with refund details
↓
Log refund activity
```

### 4. Transaction Completion
```
Update order with refund information
↓
Commit database transaction
↓
Return comprehensive refund status
```

## Error Handling

### Common Error Scenarios

1. **Wallet Refund Failure**
   - Insufficient wallet balance
   - Database connection issues
   - Transaction rollback

2. **Razorpay Refund Failure**
   - Network connectivity issues
   - Invalid payment ID
   - Razorpay API errors
   - Duplicate refund attempts

3. **Order Status Issues**
   - Order already cancelled
   - Order completed (cannot cancel)
   - Order being processed

### Error Response Format
```json
{
    "status": "error",
    "message": "Failed to cancel order. Please try again.",
    "data": {
        "refund_info": {
            "refund_errors": [
                "Online refund failed: Payment not found",
                "Wallet refund failed: Insufficient balance"
            ]
        }
    }
}
```

## Configuration

### Environment Variables
```env
RAZORPAY_KEY=your_razorpay_key
RAZORPAY_SECRET=your_razorpay_secret
```

### Razorpay Refund Settings
- **Refund Speed**: Normal (3-5 business days)
- **Refund Reason**: "Order cancelled by customer"
- **Amount**: Full payment amount or partial amount

## Testing

### Test Scenarios

1. **Wallet-Only Orders**
   - Order with only wallet payment
   - Verify immediate wallet credit

2. **Razorpay-Only Orders**
   - Order with only Razorpay payment
   - Verify Razorpay refund initiation

3. **Mixed Payment Orders**
   - Order with both wallet and Razorpay
   - Verify both refunds process correctly

4. **Error Scenarios**
   - Network failures during Razorpay API calls
   - Database transaction failures
   - Invalid order states

### Test Data
```json
{
    "test_order": {
        "id": 123,
        "total_amount": 200.00,
        "wallet_amount_used": 50.00,
        "payment_method": "razorpay",
        "payment_status": "completed"
    }
}
```

## Monitoring and Logging

### Log Events
- Order cancellation requests
- Wallet refund processing
- Razorpay refund API calls
- Refund success/failure events
- Error conditions

### Log Format
```json
{
    "level": "info",
    "message": "Razorpay refund processed successfully",
    "context": {
        "order_id": 123,
        "refund_id": "rfnd_1234567890",
        "refund_amount": 150.00,
        "user_id": 456
    }
}
```

## Security Considerations

1. **Authorization**: Users can only cancel their own orders
2. **Validation**: Prevents duplicate refunds
3. **Transaction Safety**: Database transactions ensure consistency
4. **API Security**: Razorpay API calls use secure authentication
5. **Audit Trail**: All refund activities are logged

## Support and Troubleshooting

### Common Issues

1. **Refund Not Processed**
   - Check order payment status
   - Verify Razorpay payment exists
   - Check network connectivity

2. **Wallet Refund Issues**
   - Verify wallet transaction creation
   - Check user wallet balance
   - Review database logs

3. **Razorpay API Errors**
   - Check API credentials
   - Verify payment ID format
   - Review Razorpay dashboard

### Debugging Steps

1. Check order status and payment details
2. Review refund information in order record
3. Check wallet transaction history
4. Verify Razorpay refund status
5. Review application logs

## Future Enhancements

1. **Partial Refunds**: Support for partial order cancellations
2. **Refund Notifications**: Email/SMS notifications for refund status
3. **Admin Interface**: Admin panel for refund management
4. **Refund Analytics**: Dashboard for refund statistics
5. **Multiple Payment Methods**: Support for additional payment gateways 