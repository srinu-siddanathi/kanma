@extends('layouts.main')

@section('title', 'Privacy Policy - Kanma')

@section('content')
<section class="py-5 bg-light">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12 text-center">
                <h1 class="display-4 text-dark">Privacy Policy</h1>
                <p class="text-muted">Your Privacy Matters to Us</p>
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
                            <h2 class="h3 text-dark mb-3">Privacy Policy for Kanma</h2>
                            <div class="row text-muted mb-4">
                                <div class="col-md-6">
                                    <p class="mb-1"><strong>Effective Date:</strong> 06/07/2025</p>
                                    <p class="mb-1"><strong>Business Name:</strong> Kanma (Sole Proprietorship)</p>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-1"><strong>Country:</strong> India</p>
                                    <p class="mb-1"><strong>Contact:</strong> support@kanma.in | +91 8333916492</p>
                                </div>
                            </div>
                        </div>

                        <div class="mb-5">
                            <h3 class="h4 text-dark mb-3">1. Introduction</h3>
                            <p class="text-muted">Kanma ("we," "our," or "us") respects your privacy and is committed to protecting your personal information. This Privacy Policy outlines how we collect, use, and protect your data when you use our mobile application and services.</p>
                        </div>

                        <div class="mb-5">
                            <h3 class="h4 text-dark mb-3">2. What Data We Collect</h3>
                            <p class="text-muted mb-3">We collect limited personal information to provide our services:</p>
                            <ul class="text-muted">
                                <li>Name</li>
                                <li>Phone number</li>
                                <li>Delivery address</li>
                                <li>Transaction information via Razorpay (We do not store card or bank details)</li>
                            </ul>
                        </div>

                        <div class="mb-5">
                            <h3 class="h4 text-dark mb-3">3. How We Use Your Data</h3>
                            <p class="text-muted mb-3">We use your data to:</p>
                            <ul class="text-muted">
                                <li>Deliver orders and services</li>
                                <li>Provide customer support</li>
                                <li>Process payments securely via Razorpay</li>
                                <li>Offer personalized services and subscription benefits</li>
                                <li>Improve the user experience</li>
                            </ul>
                        </div>

                        <div class="mb-5">
                            <h3 class="h4 text-dark mb-3">4. Data Sharing</h3>
                            <p class="text-muted">We do not share your personal data with third parties. All payments are processed securely via Razorpay, and we do not access or store your banking information.</p>
                        </div>

                        <div class="mb-5">
                            <h3 class="h4 text-dark mb-3">5. Data Retention</h3>
                            <p class="text-muted">We retain user data for as long as necessary to provide services and comply with legal obligations.</p>
                        </div>

                        <div class="mb-5">
                            <h3 class="h4 text-dark mb-3">6. User Rights</h3>
                            <p class="text-muted mb-3">You can:</p>
                            <ul class="text-muted">
                                <li>Request access to your data</li>
                                <li>Request deletion of your account</li>
                                <li>Contact support@kanma.in for data-related queries</li>
                            </ul>
                        </div>

                        <div class="mb-4">
                            <h3 class="h4 text-dark mb-3">7. Children's Privacy</h3>
                            <p class="text-muted">Our service does not target children under the age of 13. We do not knowingly collect data from minors.</p>
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