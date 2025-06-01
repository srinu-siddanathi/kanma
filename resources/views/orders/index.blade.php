@extends('layouts.main')

@section('content')
<section class="py-5 bg-light">
    <div class="container">
        <div class="row">
            @include('layouts.partials.user-sidebar')
            
            <div class="col-md-9">
                <h1 class="display-4 mb-3">My Orders</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">My Orders</li>
                    </ol>
                </nav>

                @if($orders->isEmpty())
                    <div class="alert alert-info">
                        You haven't placed any orders yet.
                        <a href="{{ route('shop') }}" class="alert-link">Start shopping</a>
                    </div>
                @else
                    <div class="row g-4">
                        @foreach($orders as $order)
                            @php
                                $status = strtolower($order->status);
                                $statusStyle = match($status) {
                                    'processing' => 'background-color: #0d6efd; color: #fff;',
                                    'completed' => 'background-color: #198754; color: #fff;',
                                    'cancelled', 'failed' => 'background-color: #dc3545; color: #fff;',
                                    'pending' => 'background-color: #212529; color: #fff;',
                                    default => 'background-color: #212529; color: #fff;',
                                };
                                $paymentStatus = strtolower($order->payment_status ?? 'pending');
                                $paymentStyle = $paymentStatus === 'paid'
                                    ? 'background-color: #198754; color: #fff;'
                                    : 'background-color: #212529; color: #fff;';
                            @endphp
                            <div class="col-md-6 col-lg-4">
                                <div class="card shadow-sm h-100">
                                    <div class="card-body d-flex flex-column">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="fw-bold">#{{ $order->id }}</span>
                                            <span class="small text-muted">{{ $order->created_at->format('M d, Y') }}</span>
                                        </div>
                                        <div class="mb-2">
                                            <span class="badge" style="{{ $statusStyle }}">{{ ucfirst($order->status) }}</span>
                                            <span class="badge" style="{{ $paymentStyle }}">{{ ucfirst($order->payment_status ?? 'pending') }}</span>
                                        </div>
                                        <div class="mb-2">
                                            <span class="fw-medium">Total:</span> ₹{{ number_format($order->total_amount, 2) }}
                                        </div>
                                        <div class="mb-2">
                                            <span class="fw-medium">Delivery:</span>
                                            <span class="small text-muted">{{ $order->delivery_address ?? 'N/A' }}</span>
                                        </div>
                                        <div class="mb-3">
                                            <span class="fw-medium">Items:</span>
                                            <span class="small text-muted">
                                                @foreach($order->items->take(2) as $item)
                                                    {{ $item->product->name ?? 'Product' }}@if(!$loop->last), @endif
                                                @endforeach
                                                @if($order->items->count() > 2)
                                                    +{{ $order->items->count() - 2 }} more
                                                @endif
                                            </span>
                                        </div>
                                        <div class="mt-auto">
                                            <button type="button" class="btn btn-outline-primary w-100" data-bs-toggle="modal" data-bs-target="#orderDetailsModal" onclick="loadOrderDetails({{ $order->id }})">
                                                View Details
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="d-flex justify-content-center mt-4">
                        {{ $orders->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>

<!-- Order Details Modal -->
<div class="modal fade" id="orderDetailsModal" tabindex="-1" aria-labelledby="orderDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="orderDetailsModalLabel">Order Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="orderDetailsContent">
                    <div class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function loadOrderDetails(orderId) {
    const modalContent = document.getElementById('orderDetailsContent');
    modalContent.innerHTML = `
        <div class="text-center">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    `;

    fetch(`/orders/${orderId}`)
        .then(response => response.json())
        .then(data => {
            console.log('API Response:', data); // Debug log
            if (data.status === 'success') {
                const order = data.data.order;
                console.log('Order data:', order); // Debug log
                modalContent.innerHTML = `
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="mb-2">Order Information</h6>
                            <p class="mb-1"><strong>Order ID:</strong> #${order.id}</p>
                            <p class="mb-1"><strong>Date:</strong> ${new Date(order.created_at).toLocaleDateString()}</p>
                            <p class="mb-1"><strong>Status:</strong> ${order.status}</p>
                            <p class="mb-1"><strong>Payment Method:</strong> ${order.payment_method || 'N/A'}</p>
                            <p class="mb-1"><strong>Payment Status:</strong> ${order.payment_status || 'N/A'}</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="mb-2">Delivery Address</h6>
                            <p class="mb-1">${order.delivery_address || 'No address provided'}</p>
                            ${order.delivery_latitude ? `<p class="mb-1">Latitude: ${order.delivery_latitude}</p>` : ''}
                            ${order.delivery_longitude ? `<p class="mb-1">Longitude: ${order.delivery_longitude}</p>` : ''}
                        </div>
                    </div>

                    <h6 class="mb-3">Order Items</h6>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Price</th>
                                    <th>Quantity</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${order.items.map(item => `
                                    <tr>
                                        <td>${item.product ? item.product.name : 'Product not found'}</td>
                                        <td>₹${parseFloat(item.price || 0).toFixed(2)}</td>
                                        <td>${item.quantity || 0}</td>
                                        <td>₹${(parseFloat(item.price || 0) * (item.quantity || 0)).toFixed(2)}</td>
                                    </tr>
                                `).join('')}
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3" class="text-end"><strong>Subtotal:</strong></td>
                                    <td>₹${parseFloat(order.total_amount || 0).toFixed(2)}</td>
                                </tr>
                                ${order.delivery_fee ? `
                                <tr>
                                    <td colspan="3" class="text-end"><strong>Delivery Fee:</strong></td>
                                    <td>₹${parseFloat(order.delivery_fee).toFixed(2)}</td>
                                </tr>
                                ` : ''}
                                <tr>
                                    <td colspan="3" class="text-end"><strong>Total:</strong></td>
                                    <td><strong>₹${(parseFloat(order.total_amount || 0) + parseFloat(order.delivery_fee || 0)).toFixed(2)}</strong></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                `;
            } else {
                console.error('API Error:', data); // Debug log
                modalContent.innerHTML = `
                    <div class="alert alert-danger">
                        ${data.message || 'Error loading order details. Please try again.'}
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Fetch Error:', error); // Debug log
            modalContent.innerHTML = `
                <div class="alert alert-danger">
                    Error loading order details. Please try again.
                </div>
            `;
        });
}
</script>
@endpush
@endsection 