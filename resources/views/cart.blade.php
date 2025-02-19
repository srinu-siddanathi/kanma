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
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="{{ asset('images/product-thumb-1.png') }}" width="80" class="me-3" alt="Product">
                                        <div>
                                            <h6 class="mb-0">Fresh Organic Strawberry</h6>
                                            <small class="text-muted">1 Unit</small>
                                        </div>
                                    </div>
                                </td>
                                <td>$24.99</td>
                                <td>
                                    <div class="input-group product-qty" style="width: 130px;">
                                        <span class="input-group-btn">
                                            <button class="btn btn-outline-secondary btn-minus" type="button">-</button>
                                        </span>
                                        <input type="text" class="form-control text-center" value="1">
                                        <span class="input-group-btn">
                                            <button class="btn btn-outline-secondary btn-plus" type="button">+</button>
                                        </span>
                                    </div>
                                </td>
                                <td>$24.99</td>
                                <td>
                                    <button class="btn btn-link text-danger">
                                        <svg width="24" height="24">
                                            <use xlink:href="#trash"></use>
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                            <!-- More cart items -->
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
                        <button class="btn btn-outline-secondary me-2">Update Cart</button>
                        <button class="btn btn-danger">Clear Cart</button>
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
                            <span>$24.99</span>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span>Shipping</span>
                            <span>$5.00</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-4">
                            <strong>Total</strong>
                            <strong class="text-primary">$29.99</strong>
                        </div>
                        <a href="{{ route('checkout') }}" class="btn btn-primary w-100">Proceed to Checkout</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@include('components.home.newsletter')
@endsection 