<div class="space-y-4">
    <div class="grid grid-cols-2 gap-4">
        <div>
            <h3 class="font-semibold text-gray-600">Order Information</h3>
            <p>Order ID: #{{ $order->id }}</p>
            <p>Date: {{ $order->created_at->format('M d, Y H:i') }}</p>
            <p>Status: {{ ucfirst($order->status) }}</p>
            <p>Total Amount: ₹{{ number_format($order->total_amount, 2) }}</p>
            @if($order->delivery_fee)
                <p>Delivery Fee: ₹{{ number_format($order->delivery_fee, 2) }}</p>
            @endif
            @if($order->wallet_amount_used)
                <p>Wallet Amount Used: ₹{{ number_format($order->wallet_amount_used, 2) }}</p>
            @endif
        </div>
        <div>
            <h3 class="font-semibold text-gray-600">Customer Information</h3>
            <p>Name: {{ $order->user ? $order->user->name : 'N/A' }}</p>
            <p>Phone: {{ $order->user ? $order->user->phone : 'N/A' }}</p>
            <p>Delivery Address: {{ $order->delivery_address ?? 'N/A' }}</p>
        </div>
    </div>

    <div>
        <h3 class="font-semibold text-gray-600 mb-2">Order Items</h3>
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Item</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Quantity</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Price</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @php
                    $subtotal = 0;
                @endphp
                @foreach($order->items as $item)
                    @php
                        $itemTotal = $item->price * $item->quantity;
                        $subtotal += $itemTotal;
                    @endphp
                    <tr>
                        <td class="px-4 py-2">{{ $item->product->name }}</td>
                        <td class="px-4 py-2">{{ $item->quantity }}</td>
                        <td class="px-4 py-2">₹{{ number_format($item->price, 2) }}</td>
                        <td class="px-4 py-2">₹{{ number_format($itemTotal, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot class="bg-gray-50">
                <tr>
                    <td colspan="3" class="px-4 py-2 text-right font-medium">Subtotal:</td>
                    <td class="px-4 py-2 font-medium">₹{{ number_format($subtotal, 2) }}</td>
                </tr>
                @if($order->delivery_fee)
                <tr>
                    <td colspan="3" class="px-4 py-2 text-right font-medium">Delivery Fee:</td>
                    <td class="px-4 py-2">₹{{ number_format($order->delivery_fee, 2) }}</td>
                </tr>
                @endif
                @if($order->wallet_amount_used)
                <tr>
                    <td colspan="3" class="px-4 py-2 text-right font-medium">Wallet Amount Used:</td>
                    <td class="px-4 py-2">-₹{{ number_format($order->wallet_amount_used, 2) }}</td>
                </tr>
                @endif
                <tr class="border-t-2 border-gray-200">
                    <td colspan="3" class="px-4 py-2 text-right font-bold">Total:</td>
                    <td class="px-4 py-2 font-bold">₹{{ number_format($order->total_amount, 2) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>

    @if($order->deliveryBoy)
    <div>
        <h3 class="font-semibold text-gray-600">Delivery Information</h3>
        <p>Delivery Boy: {{ $order->deliveryBoy->name }}</p>
        <p>Phone: {{ $order->deliveryBoy->phone }}</p>
    </div>
    @endif
</div> 