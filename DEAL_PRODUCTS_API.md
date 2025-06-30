# Deal Products API Documentation

## Overview
The Deal Products API provides access to products that are currently on sale/deal with their discount information and deal end dates.

## Endpoint
```
GET /api/products/deals
```

## Description
This endpoint returns all active deal products that have not expired yet. Products are filtered by:
- `is_active = true`
- `is_deal = true`
- `deal_end_date` is not null and greater than current time

## Query Parameters

| Parameter | Type | Description | Example |
|-----------|------|-------------|---------|
| `category_id` | integer | Filter deals by category ID | `?category_id=1` |
| `search` | string | Search deals by product name | `?search=apple` |
| `page` | integer | Page number for pagination | `?page=2` |

## Response Format

### Success Response (200)
```json
{
    "status": "success",
    "data": {
        "deals": [
            {
                "id": 1,
                "name": "Fresh Apples",
                "description": "Sweet and juicy apples",
                "image": "https://example.com/images/apples.jpg",
                "category": {
                    "id": 1,
                    "name": "Fruits"
                },
                "price": 100.00,
                "discounted_price": 80.00,
                "discount_percentage": 20.00,
                "unit": "1kg",
                "deal_end_date": "2024-01-15 23:59:59",
                "deal_end_date_formatted": "Jan 15, 2024 23:59",
                "time_remaining": {
                    "days": 5,
                    "hours": 12,
                    "minutes": 30
                },
                "is_available": true
            }
        ],
        "pagination": {
            "current_page": 1,
            "last_page": 3,
            "per_page": 20,
            "total": 45
        }
    }
}
```

### Error Response (500)
```json
{
    "status": "error",
    "message": "Failed to fetch deal products"
}
```

## Response Fields

| Field | Type | Description |
|-------|------|-------------|
| `id` | integer | Product ID |
| `name` | string | Product name |
| `description` | string | Product description |
| `image` | string | Primary product image URL |
| `category` | object | Category information (id, name) |
| `price` | decimal | Original price |
| `discounted_price` | decimal | Price after discount |
| `discount_percentage` | decimal | Discount percentage |
| `unit` | string | Product unit (e.g., "1kg", "500ml") |
| `deal_end_date` | string | Deal end date in Y-m-d H:i:s format |
| `deal_end_date_formatted` | string | Human-readable deal end date |
| `time_remaining` | object | Time remaining until deal expires |
| `is_available` | boolean | Whether product is available |

## Time Remaining Object
The `time_remaining` object contains:
- `days`: Number of days remaining
- `hours`: Number of hours remaining (0-23)
- `minutes`: Number of minutes remaining (0-59)

## Examples

### Get all deal products
```bash
GET /api/products/deals
```

### Get deal products in a specific category
```bash
GET /api/products/deals?category_id=1
```

### Search for deal products
```bash
GET /api/products/deals?search=apple
```

### Get deal products with pagination
```bash
GET /api/products/deals?page=2
```

### Combine filters
```bash
GET /api/products/deals?category_id=1&search=fruit&page=1
```

## Notes
- Only active deals (not expired) are returned
- Products are sorted by deal end date (earliest first)
- Default pagination is 20 items per page
- The API automatically calculates discounted prices based on the discount percentage
- Time remaining is calculated in real-time based on the current server time 