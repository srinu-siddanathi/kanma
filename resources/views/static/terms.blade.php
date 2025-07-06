@extends('layouts.main')

@section('title', 'Terms & Conditions - Kanma')

@section('content')
<section class="py-5 bg-light">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12 text-center">
                <h1 class="display-4 text-dark">Terms & Conditions</h1>
                <p class="text-muted">Please Read Our Terms Carefully</p>
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
                            <h2 class="h3 text-dark mb-3">Terms & Conditions</h2>
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
                            <h3 class="h4 text-dark mb-3">1. Acceptance of Terms</h3>
                            <p class="text-muted">By downloading, installing, or using the Kanma mobile app, you agree to abide by these Terms & Conditions.</p>
                        </div>

                        <div class="mb-5">
                            <h3 class="h4 text-dark mb-3">2. Service Description</h3>
                            <p class="text-muted mb-3">Kanma is a quick-commerce platform offering:</p>
                            <ul class="text-muted">
                                <li>Home delivery of groceries and essential items</li>
                                <li>On-demand services like maid hiring, laundry</li>
                                <li>"Own Order" feature where users can request any item/service (Subject to approval and availability)</li>
                            </ul>
                        </div>

                        <div class="mb-5">
                            <h3 class="h4 text-dark mb-3">3. Account Registration</h3>
                            <ul class="text-muted">
                                <li>Users must create an account to use our services.</li>
                                <li>You must provide accurate and up-to-date details.</li>
                                <li>You are responsible for maintaining the confidentiality of your login credentials.</li>
                            </ul>
                        </div>

                        <div class="mb-5">
                            <h3 class="h4 text-dark mb-3">4. Acceptable Use</h3>
                            <ul class="text-muted">
                                <li>Alcohol, illegal goods, and offensive items are strictly prohibited.</li>
                                <li>Own Orders will be rejected if they contain inappropriate requests.</li>
                            </ul>
                        </div>

                        <div class="mb-5">
                            <h3 class="h4 text-dark mb-3">5. Subscription & Charges</h3>
                            <p class="text-muted mb-3">Kanma offers both free and paid (subscription) service plans.</p>
                            <ul class="text-muted">
                                <li>Free Users may use the app and place orders, but standard delivery charges will apply.</li>
                                <li>Subscription Users enjoy free delivery, priority support, exclusive offers, and early access to new services.</li>
                                <li>Subscriptions are non-transferable and apply only to the registered account.</li>
                                <li>All payments are handled securely through Razorpay. Kanma does not store or access payment credentials.</li>
                            </ul>
                        </div>

                        <div class="mb-5">
                            <h3 class="h4 text-dark mb-3">6. Delivery & Order Fulfillment</h3>
                            <ul class="text-muted">
                                <li>We deliver from the nearest trusted shops and service providers.</li>
                                <li>Only subscription users are eligible for free delivery. Non-subscribers will be charged a nominal delivery fee depending on the order value and location.</li>
                                <li>There is no minimum order value. Even a ₹1 product can be ordered.</li>
                                <li>Delivery times may vary based on availability, traffic, and weather conditions.</li>
                            </ul>
                        </div>

                        <div class="mb-5">
                            <h3 class="h4 text-dark mb-3">7. Limitation of Liability</h3>
                            <p class="text-muted mb-3">Kanma acts as a facilitator and is not liable for:</p>
                            <ul class="text-muted">
                                <li>Quality of third-party services like maids or laundry</li>
                                <li>Items requested under Own Order not listed on the app</li>
                            </ul>
                            <p class="text-muted">We are not responsible for misuse of service or any third-party issues.</p>
                        </div>

                        <div class="mb-5">
                            <h3 class="h4 text-dark mb-3">8. Termination</h3>
                            <p class="text-muted mb-3">We reserve the right to suspend or terminate any user account for:</p>
                            <ul class="text-muted">
                                <li>Violation of terms</li>
                                <li>Fraudulent activity</li>
                                <li>Abusive behavior toward service agents</li>
                            </ul>
                        </div>

                        <div class="mb-4">
                            <h3 class="h4 text-dark mb-3">9. Governing Law</h3>
                            <p class="text-muted">These terms are governed by the laws of India. Disputes, if any, will be subject to Hyderabad jurisdiction.</p>
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