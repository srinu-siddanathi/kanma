<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Placed - KANMA</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .container {
            background-color: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px 20px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }
        .header p {
            margin: 10px 0 0 0;
            opacity: 0.9;
        }
        .content {
            padding: 30px 20px;
        }
        .greeting {
            font-size: 18px;
            margin-bottom: 20px;
            color: #2c3e50;
        }
        .order-info {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
            border-left: 4px solid #667eea;
        }
        .order-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin: 20px 0;
        }
        .detail-item {
            background-color: white;
            padding: 15px;
            border-radius: 6px;
            border: 1px solid #e9ecef;
        }
        .detail-label {
            font-weight: 600;
            color: #495057;
            font-size: 12px;
            text-transform: uppercase;
            margin-bottom: 5px;
        }
        .detail-value {
            color: #2c3e50;
            font-size: 14px;
        }
        .order-items {
            margin: 20px 0;
        }
        .item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #e9ecef;
        }
        .item:last-child {
            border-bottom: none;
        }
        .item-name {
            flex: 1;
            font-weight: 500;
        }
        .item-quantity {
            color: #6c757d;
            margin: 0 15px;
        }
        .item-price {
            font-weight: 600;
            color: #2c3e50;
        }
        .total-section {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .total-row {
            display: flex;
            justify-content: space-between;
            margin: 10px 0;
        }
        .total-row.final {
            font-weight: 600;
            font-size: 18px;
            color: #2c3e50;
            border-top: 2px solid #667eea;
            padding-top: 10px;
            margin-top: 15px;
        }
        .delivery-info {
            background-color: #e8f5e8;
            border: 1px solid #c3e6c3;
            border-radius: 6px;
            padding: 15px;
            margin: 20px 0;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            color: #6c757d;
            font-size: 14px;
        }
        .cta-button {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            margin: 20px 0;
        }
        .status-badge {
            display: inline-block;
            background-color: #ffc107;
            color: #212529;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎉 Order Placed Successfully!</h1>
            <p>Thank you for choosing KANMA</p>
        </div>
        
        <div class="content">
            <div class="greeting">
                Hi {{ $recipientName }},
            </div>

            @if($recipientType === 'customer')
                <p>Your order has been placed successfully! We're excited to prepare your order and get it delivered to you as soon as possible.</p>
                @if($order->payment_method === 'cod')
                    <p><strong>Payment Method:</strong> Cash on Delivery (COD) - Payment will be collected upon delivery.</p>
                @endif
            @else
                <p>A new order has been placed and requires your attention. Please review the order details and confirm it as soon as possible.</p>
                @if($order->payment_method === 'cod')
                    <p><strong>⚠️ COD Order:</strong> This is a Cash on Delivery order. Payment will be collected upon delivery.</p>
                @endif
            @endif

            <div class="order-info">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                    <h3 style="margin: 0; color: #2c3e50;">Order #{{ $order->id }}</h3>
                    <span class="status-badge">{{ ucfirst($order->status) }}</span>
                </div>
                
                <div class="order-details">
                    <div class="detail-item">
                        <div class="detail-label">Order Date</div>
                        <div class="detail-value">{{ $order->created_at->format('M j, Y \a\t g:i A') }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Payment Method</div>
                        <div class="detail-value">{{ ucfirst($order->payment_method) }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Payment Status</div>
                        <div class="detail-value">{{ ucfirst($order->payment_status) }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Branch</div>
                        <div class="detail-value">{{ $order->branch->name ?? 'N/A' }}</div>
                    </div>
                </div>
            </div>

            <div class="order-items">
                <h4 style="color: #2c3e50; margin-bottom: 15px;">Order Items</h4>
                @foreach($order->items as $item)
                    <div class="item">
                        <div class="item-name">{{ $item->product->name ?? 'Product' }}</div>
                        <div class="item-quantity">x{{ $item->quantity }}</div>
                        <div class="item-price">₹{{ number_format($item->subtotal, 2) }}</div>
                    </div>
                @endforeach
            </div>

            <div class="total-section">
                <div class="total-row">
                    <span>Subtotal:</span>
                    <span>₹{{ number_format($order->items->sum('subtotal'), 2) }}</span>
                </div>
                @if($order->delivery_fee > 0)
                <div class="total-row">
                    <span>Delivery Fee:</span>
                    <span>₹{{ number_format($order->delivery_fee, 2) }}</span>
                </div>
                @endif
                @if($order->wallet_amount_used > 0)
                <div class="total-row">
                    <span>Wallet Used:</span>
                    <span>-₹{{ number_format($order->wallet_amount_used, 2) }}</span>
                </div>
                @endif
                <div class="total-row final">
                    <span>Total Amount:</span>
                    <span>₹{{ number_format($order->total_amount, 2) }}</span>
                </div>
            </div>

            @if($order->delivery_address)
            <div class="delivery-info">
                <h4 style="margin: 0 0 10px 0; color: #2c3e50;">📍 Delivery Address</h4>
                <p style="margin: 0; color: #2c3e50;">{{ $order->delivery_address }}</p>
            </div>
            @endif

            @if($order->notes)
            <div style="background-color: #fff3cd; border: 1px solid #ffeaa7; border-radius: 6px; padding: 15px; margin: 20px 0;">
                <h4 style="margin: 0 0 10px 0; color: #856404;">📝 Order Notes</h4>
                <p style="margin: 0; color: #856404;">{{ $order->notes }}</p>
            </div>
            @endif

            @if($recipientType === 'customer')
                <div style="text-align: center;">
                    <a href="{{ route('orders') }}" class="cta-button">View Order Details</a>
                </div>
                <p style="text-align: center; color: #6c757d; font-size: 14px;">
                    We'll keep you updated on your order status. You can track your order anytime from your account dashboard.
                </p>
            @else
                <div style="text-align: center;">
                    <a href="{{ route('branch.orders.show', $order->id) }}" class="cta-button">Review Order</a>
                </div>
                <p style="text-align: center; color: #6c757d; font-size: 14px;">
                    Please review and confirm this order as soon as possible to ensure timely delivery.
                </p>
            @endif
        </div>
        
        <div class="footer">
            <p>Thank you for choosing KANMA!</p>
            <p>If you have any questions, please contact our support team.</p>
            <p style="font-size: 12px; margin-top: 15px;">
                This email was sent to {{ $recipientType === 'customer' ? 'the customer' : 'the branch manager' }} for order #{{ $order->id }}.
            </p>
        </div>
    </div>
</body>
</html> 