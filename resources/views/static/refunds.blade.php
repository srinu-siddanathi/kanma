@extends('layouts.main')

@section('title', 'Refunds & Cancellation Policy - Kanma')

@section('content')
<section class="py-5 bg-light">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12 text-center">
                <h1 class="display-4 text-dark">Refunds & Cancellation Policy</h1>
                <p class="text-muted">Understanding Our Refund and Cancellation Terms</p>
            </div>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-5">
                        <div class="mb-5">
                            <h2 class="h3 text-dark mb-3">Refund & Cancellation Policy</h2>
                            <div class="row text-muted mb-4">
                                <div class="col-md-6">
                                    <p class="mb-1"><strong>Effective Date:</strong> 06/07/2025</p>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-1"><strong>Business Name:</strong> Kanma</p>
                                </div>
                            </div>
                        </div>

                        <div class="mb-5">
                            <h3 class="h4 text-dark mb-3">1. Refund Eligibility</h3>
                            <p class="text-muted mb-3">Refunds are applicable only in cases where:</p>
                            <ul class="text-muted">
                                <li>The service/product was not delivered.</li>
                                <li>A duplicate payment was made.</li>
                                <li>Delivery was delayed beyond reasonable time and customer canceled.</li>
                            </ul>
                        </div>

                        <div class="mb-5">
                            <h3 class="h4 text-dark mb-3">2. Non-Refundable Scenarios</h3>
                            <ul class="text-muted">
                                <li>Once the delivery is completed.</li>
                                <li>Subscription fees (partial usage is non-refundable).</li>
                                <li>Orders placed via "Own Order" that are accepted and processed.</li>
                            </ul>
                        </div>

                        <div class="mb-5">
                            <h3 class="h4 text-dark mb-3">3. Cancellation Policy</h3>
                            <ul class="text-muted">
                                <li>Orders can be canceled within 5 minutes of placing them.</li>
                                <li>After dispatch, cancellations are not allowed.</li>
                                <li>For Own Order requests, cancellations depend on confirmation stage.</li>
                            </ul>
                        </div>

                        <div class="mb-5">
                            <h3 class="h4 text-dark mb-3">4. How to Request a Refund</h3>
                            <p class="text-muted mb-3">Email us at support@kanma.in with:</p>
                            <ul class="text-muted">
                                <li>Order ID</li>
                                <li>Reason for refund</li>
                                <li>Phone number and registered email</li>
                            </ul>
                        </div>

                        <div class="mb-4">
                            <h3 class="h4 text-dark mb-3">5. Refund Process Time</h3>
                            <p class="text-muted">Refunds (if approved) will be processed within 7 working days to the original payment method via Razorpay.</p>
                        </div>

                        <div class="text-center mt-5">
                            <a href="{{ route('home') }}" class="btn btn-primary">
                                <svg width="16" height="16" class="me-2">
                                    <use xlink:href="#arrow-right"></use>
                                </svg>
                                Back to Home
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection 