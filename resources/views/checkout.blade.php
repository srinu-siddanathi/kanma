@extends('layouts.main')

@section('content')
<section class="py-5 bg-light">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <h1 class="display-4 mb-3">Checkout</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('cart') }}">Cart</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Checkout</li>
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
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <h3 class="card-title mb-4">Billing Details</h3>
                        <form>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">First Name *</label>
                                    <input type="text" class="form-control" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Last Name *</label>
                                    <input type="text" class="form-control" required>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Company Name (Optional)</label>
                                    <input type="text" class="form-control">
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Street Address *</label>
                                    <input type="text" class="form-control mb-2" placeholder="House number and street name" required>
                                    <input type="text" class="form-control" placeholder="Apartment, suite, unit etc. (optional)">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">City *</label>
                                    <input type="text" class="form-control" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">State *</label>
                                    <input type="text" class="form-control" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Zip *</label>
                                    <input type="text" class="form-control" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Phone *</label>
                                    <input type="tel" class="form-control" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Email Address *</label>
                                    <input type="email" class="form-control" required>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Payment Methods -->
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h3 class="card-title mb-4">Payment Method</h3>
                        <div class="payment-methods">
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="radio" name="payment" id="credit-card" checked>
                                <label class="form-check-label" for="credit-card">
                                    Credit Card
                                </label>
                                <div class="mt-3 payment-form">
                                    <div class="row">
                                        <div class="col-md-12 mb-3">
                                            <label class="form-label">Card Number *</label>
                                            <input type="text" class="form-control" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Expiry Date *</label>
                                            <input type="text" class="form-control" placeholder="MM/YY" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">CVV *</label>
                                            <input type="text" class="form-control" required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="payment" id="paypal">
                                <label class="form-check-label" for="paypal">
                                    PayPal
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="col-lg-4">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h3 class="card-title mb-4">Order Summary</h3>
                        <div class="order-items mb-4">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Fresh Organic Strawberry × 1</span>
                                <span>$24.99</span>
                            </div>
                            <!-- More items -->
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal</span>
                            <span>$24.99</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Shipping</span>
                            <span>$5.00</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-4">
                            <strong>Total</strong>
                            <strong class="text-primary">$29.99</strong>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Place Order</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection 