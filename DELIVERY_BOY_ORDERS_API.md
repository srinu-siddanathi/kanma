# Delivery Boy Orders API - Updated

## Overview
The delivery boy orders API has been updated to return all assigned orders in a single response, grouped by status, instead of requiring a status filter parameter.

## Endpoint
**GET** `/api/delivery-boy/orders`

## Authentication
Required - Bearer Token (delivery boy authentication)

## Request
No query parameters required. The API now returns all orders assigned to the authenticated delivery boy.

## Response Format

### Success Response (200 OK)
```json
{
    "data": {
        "pending": [
            {
                "id": 1,
                "status": "pending",
                "total_amount": "150.00",
                "delivery_fee": "10.00",
                "delivery_address": "123 Main St, City",
                "created_at": "2024-01-15T10:30:00Z",
                "items": [...],
                "user": {...},
                "shop": {...}
            }
        ],
        "processing": [
            {
                "id": 2,
                "status": "shipped",
                "total_amount": "200.00",
                "delivery_fee": "10.00",
                "delivery_address": "456 Oak Ave, City",
                "created_at": "2024-01-15T09:15:00Z",
                "items": [...],
                "user": {...},
                "shop": {...}
            }
        ],
        "completed": [
            {
                "id": 3,
                "status": "delivered",
                "total_amount": "175.00",
                "delivery_fee": "10.00",
                "delivery_address": "789 Pine St, City",
                "created_at": "2024-01-14T16:45:00Z",
                "items": [...],
                "user": {...},
                "shop": {...}
            }
        ]
    },
    "counts": {
        "pending": 2,
        "processing": 1,
        "completed": 5,
        "total": 8
    },
    "message": "Orders retrieved successfully"
}
```

## Status Groupings

### Pending Orders
Includes orders with status:
- `pending`
- `confirmed`
- `assigned`
- `processing`

### Processing Orders
Includes orders with status:
- `shipped`
- `out_for_delivery`

### Completed Orders
Includes orders with status:
- `delivered`
- `completed`
- `cancelled`

## Benefits of This Change

1. **Single API Call**: No need to make multiple API calls to get orders of different statuses
2. **Better Performance**: Reduces the number of HTTP requests from the mobile app
3. **Complete Overview**: Delivery boys can see all their orders at once
4. **Count Information**: Provides counts for each status group for UI display
5. **Simplified Integration**: Mobile apps can display all orders in tabs or sections

## Migration from Previous Version

### Before (Required status parameter)
```
GET /api/delivery-boy/orders?status=pending
GET /api/delivery-boy/orders?status=processing
GET /api/delivery-boy/orders?status=completed
```

### After (No parameters needed)
```
GET /api/delivery-boy/orders
```

## Error Responses

### 401 Unauthorized
```json
{
    "message": "Unauthenticated."
}
```

### 403 Forbidden
```json
{
    "message": "You are not authorized to perform this action."
}
```

## Example Usage

### JavaScript/Fetch
```javascript
const response = await fetch('/api/delivery-boy/orders', {
    headers: {
        'Authorization': 'Bearer ' + token,
        'Accept': 'application/json'
    }
});

const data = await response.json();
console.log('Pending orders:', data.data.pending);
console.log('Processing orders:', data.data.processing);
console.log('Completed orders:', data.data.completed);
console.log('Total orders:', data.counts.total);
```

### cURL
```bash
curl -X GET "http://your-domain.com/api/delivery-boy/orders" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Accept: application/json"
``` 