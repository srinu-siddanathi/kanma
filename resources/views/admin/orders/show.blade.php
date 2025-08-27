@extends('layouts.admin')

@section('title', 'Order Details')

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="px-4 py-6 sm:px-0">
        <!-- Back Button -->
        <div class="mb-6">
            <a href="{{ route('admin.orders') }}" class="text-indigo-600 hover:text-indigo-900">
                ← Back to Orders
            </a>
        </div>

        <!-- Order Header -->
        <div class="bg-white shadow overflow-hidden sm:rounded-lg mb-6">
            <div class="px-4 py-5 sm:px-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900">
                    Order #{{ $order->id }}
                </h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">
                    Placed on {{ $order->created_at->format('M d, Y h:i A') }}
                </p>
            </div>
            <div class="border-t border-gray-200 px-4 py-5 sm:px-6">
                <dl class="grid grid-cols-1 gap-x-4 gap-y-8 sm:grid-cols-2">
                    <div class="sm:col-span-1">
                        <dt class="text-sm font-medium text-gray-500">Customer</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $order->user->name }}</dd>
                    </div>
                    <div class="sm:col-span-1">
                        <dt class="text-sm font-medium text-gray-500">Branch</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $order->branch->name }}</dd>
                    </div>
                    @if($order->shop)
                    <div class="sm:col-span-1">
                        <dt class="text-sm font-medium text-gray-500">Shop</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $order->shop->name }}</dd>
                    </div>
                    <div class="sm:col-span-1">
                        <dt class="text-sm font-medium text-gray-500">Shop Owner</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $order->shop->user->name ?? 'N/A' }}</dd>
                    </div>
                    @endif
                    <div class="sm:col-span-1">
                        <dt class="text-sm font-medium text-gray-500">Status</dt>
                        <dd class="mt-1">
                            <form action="{{ route('admin.orders.update-status', $order) }}" method="POST" class="inline-flex">
                                @csrf
                                @method('PUT')
                                <select name="status" onchange="this.form.submit()" 
                                    class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                                    <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="confirmed" {{ $order->status === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                                    <option value="processing" {{ $order->status === 'processing' ? 'selected' : '' }}>Processing</option>
                                    <option value="delivered" {{ $order->status === 'delivered' ? 'selected' : '' }}>Delivered</option>
                                    <option value="completed" {{ $order->status === 'completed' ? 'selected' : '' }}>Completed</option>
                                    <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                </select>
                            </form>
                        </dd>
                    </div>
                    <div class="sm:col-span-1">
                        <dt class="text-sm font-medium text-gray-500">Order Type</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ ucfirst($order->order_type) }}</dd>
                    </div>
                    <div class="sm:col-span-1">
                        <dt class="text-sm font-medium text-gray-500">Payment Method</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ ucfirst($order->payment_method ?? 'Not specified') }}</dd>
                    </div>
                    <div class="sm:col-span-1">
                        <dt class="text-sm font-medium text-gray-500">Payment Status</dt>
                        <dd class="mt-1">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                {{ $order->payment_status === 'paid' ? 'bg-green-100 text-green-800' : 
                                   ($order->payment_status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                                   'bg-red-100 text-red-800') }}">
                                {{ ucfirst($order->payment_status ?? 'Unknown') }}
                            </span>
                        </dd>
                    </div>
                    <div class="sm:col-span-1">
                        <dt class="text-sm font-medium text-gray-500">Delivery Fee</dt>
                        <dd class="mt-1 text-sm text-gray-900">₹{{ number_format($order->delivery_fee ?? 0, 2) }}</dd>
                    </div>
                    @if($order->wallet_amount_used > 0)
                    <div class="sm:col-span-1">
                        <dt class="text-sm font-medium text-gray-500">Wallet Amount Used</dt>
                        <dd class="mt-1 text-sm text-gray-900">₹{{ number_format($order->wallet_amount_used, 2) }}</dd>
                    </div>
                    @endif
                    @if($order->coupon)
                    <div class="sm:col-span-1">
                        <dt class="text-sm font-medium text-gray-500">Coupon Applied</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $order->coupon->code }} ({{ $order->coupon->discount_type === 'percentage' ? $order->coupon->discount_value . '%' : '₹' . $order->coupon->discount_value }})</dd>
                    </div>
                    @endif
                    <div class="sm:col-span-2">
                        <dt class="text-sm font-medium text-gray-500">Delivery Address</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $order->delivery_address }}</dd>
                    </div>
                </dl>
            </div>
        </div>

        <!-- Order Items -->
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Order Items</h3>
            </div>
            <div class="border-t border-gray-200">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($order->items as $item)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $item->product->name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                ₹{{ number_format($item->price, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $item->quantity }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                ₹{{ number_format($item->subtotal, 2) }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50">
                        <tr>
                            <td colspan="3" class="px-6 py-4 text-right text-sm font-medium text-gray-900">Subtotal:</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                ₹{{ number_format($order->items->sum(function($item) { return $item->price * $item->quantity; }), 2) }}
                            </td>
                        </tr>
                        @if($order->delivery_fee > 0)
                        <tr>
                            <td colspan="3" class="px-6 py-2 text-right text-sm text-gray-600">Delivery Fee:</td>
                            <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-600">
                                ₹{{ number_format($order->delivery_fee, 2) }}
                            </td>
                        </tr>
                        @endif
                        @if($order->coupon)
                        <tr>
                            <td colspan="3" class="px-6 py-2 text-right text-sm text-green-600">Coupon Discount:</td>
                            <td class="px-6 py-2 whitespace-nowrap text-sm text-green-600">
                                -₹{{ number_format($order->coupon->discount_type === 'percentage' ? 
                                    ($order->items->sum(function($item) { return $item->price * $item->quantity; }) * $order->coupon->discount_value / 100) : 
                                    $order->coupon->discount_value, 2) }}
                            </td>
                        </tr>
                        @endif
                        @if($order->wallet_amount_used > 0)
                        <tr>
                            <td colspan="3" class="px-6 py-2 text-right text-sm text-blue-600">Wallet Amount Used:</td>
                            <td class="px-6 py-2 whitespace-nowrap text-sm text-blue-600">
                                -₹{{ number_format($order->wallet_amount_used, 2) }}
                            </td>
                        </tr>
                        @endif
                        <tr class="border-t border-gray-200">
                            <td colspan="3" class="px-6 py-4 text-right text-lg font-bold text-gray-900">Total Amount:</td>
                            <td class="px-6 py-4 whitespace-nowrap text-lg font-bold text-gray-900">
                                ₹{{ number_format($order->total_amount, 2) }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection 