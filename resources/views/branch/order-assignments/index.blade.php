@extends('layouts.branch-manager')

@section('content')
<div class="container mx-auto px-4">
    <div class="mb-6">
        <h1 class="text-2xl font-bold">Order Assignments</h1>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Delivery Boys Status -->
        <div class="md:col-span-1">
            <div class="bg-white rounded-lg shadow p-4">
                <h2 class="text-lg font-semibold mb-4">Delivery Boys Status</h2>
                @foreach($deliveryBoys as $deliveryBoy)
                <div class="mb-4 p-3 border rounded {{ $deliveryBoy->active_orders_count >= 5 ? 'bg-red-50' : 'bg-green-50' }}">
                    <p class="font-medium">{{ $deliveryBoy->name }}</p>
                    <p class="text-sm text-gray-600">Active Orders: {{ $deliveryBoy->active_orders_count }}</p>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Unassigned Orders -->
        <div class="md:col-span-2">
            <div class="bg-white rounded-lg shadow">
                <h2 class="text-lg font-semibold p-4 border-b">Unassigned Orders</h2>
                
                @if($unassignedOrders->isEmpty())
                    <div class="p-4 text-center text-gray-500">
                        No unassigned orders found.
                    </div>
                @else
                    @foreach($unassignedOrders as $order)
                    <div class="p-4 border-b">
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <p class="font-medium">Order #{{ $order->id }}</p>
                                <p class="text-sm text-gray-600">
                                    {{ $order->customer ? $order->customer->name : 'No customer name' }}
                                </p>
                                <p class="text-sm text-gray-500">Total: ₹{{ $order->total_amount }}</p>
                                <p class="text-sm text-gray-500">Status: {{ ucfirst($order->status) }}</p>
                                @if($order->delivery_address)
                                    <p class="text-sm text-gray-500">Address: {{ $order->delivery_address }}</p>
                                @endif
                            </div>
                            <form action="{{ route('branch.order-assignments.assign', $order) }}" method="POST" class="flex items-center">
                                @csrf
                                <select name="delivery_boy_id" class="rounded border-gray-300 mr-2">
                                    @foreach($deliveryBoys as $deliveryBoy)
                                    <option value="{{ $deliveryBoy->id }}" 
                                        {{ $deliveryBoy->active_orders_count >= 5 || !$deliveryBoy->is_working_today ? 'disabled' : '' }}>
                                        {{ $deliveryBoy->name }} 
                                        ({{ $deliveryBoy->active_orders_count }} orders)
                                        {{ !$deliveryBoy->is_working_today ? '- Not Working Today' : '' }}
                                    </option>
                                    @endforeach
                                </select>
                                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                                    Assign
                                </button>
                            </form>
                        </div>
                    </div>
                    @endforeach

                    <div class="p-4">
                        {{ $unassignedOrders->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection 