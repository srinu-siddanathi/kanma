@extends('layouts.shop-owner')

@section('title', 'Order Details')

@section('content')
<div class="container mx-auto px-4">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold">Order #{{ $order->id }}</h2>
            <a href="{{ route('shop-owner.orders.index') }}" 
               class="text-indigo-600 hover:text-indigo-900">
                ← Back to Orders
            </a>
        </div>

        @if(session('success'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
            {{ session('success') }}
        </div>
        @endif

        <!-- Order Status Update -->
        <div class="bg-white shadow rounded-lg mb-6">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Update Order Status</h3>
                <form action="{{ route('shop-owner.orders.update-status', $order) }}" method="POST" class="flex gap-4">
                    @csrf
                    @method('PUT')
                    <select name="status" class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="confirmed" {{ $order->status == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                        <option value="processing" {{ $order->status == 'processing' ? 'selected' : '' }}>Processing</option>
                        <option value="completed" {{ $order->status == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                    <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                        Update Status
                    </button>
                </form>
            </div>
        </div>

        <!-- Order Details -->
        <div class="bg-white shadow rounded-lg mb-6">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Customer Information</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Name</p>
                        <p class="mt-1 text-sm text-gray-900">{{ $order->user->name }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Email</p>
                        <p class="mt-1 text-sm text-gray-900">{{ $order->user->email }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Phone</p>
                        <p class="mt-1 text-sm text-gray-900">{{ $order->user->phone_number ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Order Date</p>
                        <p class="mt-1 text-sm text-gray-900">{{ $order->created_at->format('M d, Y H:i') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delivery Information -->
        <div class="bg-white shadow rounded-lg mb-6">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Delivery Information</h3>
                <div class="space-y-4">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Delivery Address</p>
                        <p class="mt-1 text-sm text-gray-900">{{ $order->delivery_address }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Notes</p>
                        <p class="mt-1 text-sm text-gray-900">{{ $order->notes ?? 'No notes provided' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Order Items -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Order Items</h3>
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
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    @if($item->product->image_path)
                                        <img src="{{ Storage::url($item->product->image_path) }}" 
                                             alt="{{ $item->product->name }}" 
                                             class="h-10 w-10 object-cover rounded mr-3">
                                    @endif
                                    <div class="text-sm font-medium text-gray-900">{{ $item->product->name }}</div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                ₹{{ number_format($item->price, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $item->quantity }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                ₹{{ number_format($item->price * $item->quantity, 2) }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50">
                        <tr>
                            <td colspan="3" class="px-6 py-4 text-sm font-medium text-gray-900 text-right">Subtotal</td>
                            <td class="px-6 py-4 text-sm text-gray-900">₹{{ number_format($order->total_amount - ($order->delivery_fee ?? 0), 2) }}</td>
                        </tr>
                        @if($order->delivery_fee)
                        <tr>
                            <td colspan="3" class="px-6 py-4 text-sm font-medium text-gray-900 text-right">Delivery Fee</td>
                            <td class="px-6 py-4 text-sm text-gray-900">₹{{ number_format($order->delivery_fee, 2) }}</td>
                        </tr>
                        @endif
                        <tr>
                            <td colspan="3" class="px-6 py-4 text-sm font-medium text-gray-900 text-right">Total</td>
                            <td class="px-6 py-4 text-sm font-bold text-gray-900">₹{{ number_format($order->total_amount, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection 