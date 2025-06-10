@extends('layouts.main')

@section('content')
<section class="py-5 bg-light">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <h1 class="display-4 mb-3">Shopping Cart</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Cart</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-8">
                <!-- Cart Items -->
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Total</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($items as $item)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="{{ asset($item['image_path']) }}" width="80" class="me-3" alt="{{ $item['product_name'] }}">
                                        <div>
                                            <h6 class="mb-0">{{ $item['product_name'] }}</h6>
                                            <small class="text-muted">{{ $item['quantity'] }} {{ $item['unit'] }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>₹{{ number_format($item['price'], 2) }}</td>
                                <td>
                                    <div class="input-group product-qty" style="width: 130px;">
                                        <span class="input-group-btn">
                                            <button class="btn btn-outline-secondary btn-minus" type="button">-</button>
                                        </span>
                                        <input type="text" class="form-control text-center" value="{{ $item['quantity'] }}">
                                        <span class="input-group-btn">
                                            <button class="btn btn-outline-secondary btn-plus" type="button">+</button>
                                        </span>
                                    </div>
                                </td>
                                <td>₹{{ number_format($item['total'], 2) }}</td>
                                <td>
                                    <button class="btn btn-link text-danger remove-from-cart" data-variant-id="{{ $item['variant_id'] }}">
                                        <svg width="24" height="24">
                                            <use xlink:href="#trash"></use>
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Cart Actions -->
                <div class="d-flex justify-content-between align-items-center mt-4">
                    <div>
                        <a href="{{ route('shop') }}" class="btn btn-outline-primary">
                            <i class="bi bi-arrow-left me-2"></i>Continue Shopping
                        </a>
                    </div>
                    <div>
                        <button class="btn btn-outline-secondary me-2" id="updateCart">Update Cart</button>
                        <button class="btn btn-danger" id="clearCart">Clear Cart</button>
                    </div>
                </div>
            </div>

            <!-- Cart Summary -->
            <div class="col-lg-4">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h3 class="card-title mb-4">Cart Summary</h3>
                        <div class="d-flex justify-content-between mb-3">
                            <span>Subtotal</span>
                            <span>₹{{ number_format($total, 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span>Shipping</span>
                            <span>₹{{ number_format($deliveryFee, 2) }}</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-4">
                            <strong>Total</strong>
                            <strong class="text-primary">₹{{ number_format($total + $deliveryFee, 2) }}</strong>
                        </div>
                        <a href="{{ route('checkout') }}" class="btn btn-primary w-100">Proceed to Checkout</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@include('components.home.newsletter')

@push('scripts')
<script>
$(document).ready(function() {
    // Update cart quantity
    $('.btn-minus, .btn-plus').click(function() {
        const input = $(this).siblings('input');
        const currentVal = parseInt(input.val());
        const newVal = $(this).hasClass('btn-plus') ? currentVal + 1 : currentVal - 1;
        
        if (newVal >= 1) {
            input.val(newVal);
        }
    });

    // Update cart
    $('#updateCart').click(function() {
        const items = [];
        $('.product-qty input').each(function() {
            const variantId = $(this).closest('tr').find('.remove-from-cart').data('variant-id');
            const quantity = parseInt($(this).val());
            items.push({ variant_id: variantId, quantity: quantity });
        });

        $.ajax({
            url: '{{ route("cart.update") }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                items: items
            },
            success: function(response) {
                if (response.success) {
                    window.location.reload();
                }
            }
        });
    });

    // Clear cart
    $('#clearCart').click(function() {
        if (confirm('Are you sure you want to clear your cart?')) {
            $.ajax({
                url: '{{ route("cart.clear") }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        window.location.reload();
                    }
                }
            });
        }
    });

    // Remove item
    $('.remove-from-cart').click(function() {
        const variantId = $(this).data('variant-id');
        
        $.ajax({
            url: '{{ route("cart.remove") }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                variant_id: variantId
            },
            success: function(response) {
                if (response.success) {
                    window.location.reload();
                }
            }
        });
    });
});
</script>
@endpush
@endsection 