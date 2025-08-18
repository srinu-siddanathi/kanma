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
                @php
                    // Sort delivery boys: working first, then non-working
                    $workingDeliveryBoys = $deliveryBoys->where('is_working_today', true);
                    $nonWorkingDeliveryBoys = $deliveryBoys->where('is_working_today', false);
                @endphp
                
                <!-- Working Delivery Boys -->
                @foreach($workingDeliveryBoys as $deliveryBoy)
                <div class="mb-4 p-3 border rounded bg-green-100 border-green-300">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="font-medium text-green-800">{{ $deliveryBoy->name }}</p>
                            <p class="text-sm text-green-600">Active Orders: {{ $deliveryBoy->active_orders_count }}</p>
                            <p class="text-xs text-green-500">Working Today</p>
                        </div>
                        <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                    </div>
                </div>
                @endforeach
                
                <!-- Non-Working Delivery Boys -->
                @foreach($nonWorkingDeliveryBoys as $deliveryBoy)
                <div class="mb-4 p-3 border rounded bg-red-100 border-red-300">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="font-medium text-red-800">{{ $deliveryBoy->name }}</p>
                            <p class="text-sm text-red-600">Active Orders: {{ $deliveryBoy->active_orders_count }}</p>
                            <p class="text-xs text-red-500">Not Working Today</p>
                        </div>
                        <div class="w-3 h-3 bg-red-500 rounded-full"></div>
                    </div>
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
                                <p class="font-medium">
                                    <button onclick="showOrderDetails({{ $order->id }})" 
                                            class="text-blue-600 hover:text-blue-900 underline">
                                        Order #{{ $order->id }}
                                    </button>
                                </p>
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

    <!-- Order Details Modal -->
    <div id="orderDetailsModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-bold">Order Details</h2>
                <button onclick="closeOrderDetails()" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div id="orderDetailsContent">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function showOrderDetails(orderId) {
    // Show modal
    const modal = document.getElementById('orderDetailsModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');

    // Show loading state
    document.getElementById('orderDetailsContent').innerHTML = 
        '<div class="text-center py-8"><div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto"></div><p class="mt-2 text-gray-600">Loading order details...</p></div>';

    // Fetch order details
    fetch(`/admin/branch/orders/${orderId}/details`, {
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
        .then(response => {
            if (!response.ok) {
                throw new Error('Failed to load order details');
            }
            return response.text();
        })
        .then(html => {
            document.getElementById('orderDetailsContent').innerHTML = html;
        })
        .catch(error => {
            console.error('Error loading order details:', error);
            document.getElementById('orderDetailsContent').innerHTML = 
                '<div class="text-red-500 text-center py-8">Error loading order details. Please try again.</div>';
        });
}

function closeOrderDetails() {
    const modal = document.getElementById('orderDetailsModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

// Close modal when clicking outside
document.getElementById('orderDetailsModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeOrderDetails();
    }
});
</script>
@endpush
@endsection 