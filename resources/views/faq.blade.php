@extends('layouts.main')

@section('content')
<section class="py-5 bg-light">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12 text-center">
                <h1 class="display-4 text-dark">🙋‍♂️ Frequently Asked Questions (FAQ)</h1>
                <p class="text-muted">Find answers to common questions about KANMA</p>
            </div>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="accordion" id="faqAccordion">
                    
                    <!-- FAQ Item 1 -->
                    <div class="accordion-item mb-3 border rounded">
                        <h2 class="accordion-header" id="faq1">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse1" aria-expanded="false" aria-controls="collapse1">
                                <span class="me-3">❓</span> What is KANMA?
                            </button>
                        </h2>
                        <div id="collapse1" class="accordion-collapse collapse" aria-labelledby="faq1" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                <p class="mb-0">KANMA is an all-in-one quick-commerce and home services app that delivers essentials, groceries, and also connects you with services like laundry, maids, and more — all within minutes.</p>
                            </div>
                        </div>
                    </div>

                    <!-- FAQ Item 2 -->
                    <div class="accordion-item mb-3 border rounded">
                        <h2 class="accordion-header" id="faq2">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse2" aria-expanded="false" aria-controls="collapse2">
                                <span class="me-3">❓</span> What is the "Own Order" feature?
                            </button>
                        </h2>
                        <div id="collapse2" class="accordion-collapse collapse" aria-labelledby="faq2" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                <p class="mb-0">The Own Order feature lets you request anything that isn't already listed in the app. Just use in-app chat or call, and our team will arrange it for you.</p>
                            </div>
                        </div>
                    </div>

                    <!-- FAQ Item 3 -->
                    <div class="accordion-item mb-3 border rounded">
                        <h2 class="accordion-header" id="faq3">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse3" aria-expanded="false" aria-controls="collapse3">
                                <span class="me-3">❓</span> Is account creation mandatory?
                            </button>
                        </h2>
                        <div id="collapse3" class="accordion-collapse collapse" aria-labelledby="faq3" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                <p class="mb-0">Yes, you must create an account to access KANMA's services. This helps us personalize your experience and ensure security.</p>
                            </div>
                        </div>
                    </div>

                    <!-- FAQ Item 4 -->
                    <div class="accordion-item mb-3 border rounded">
                        <h2 class="accordion-header" id="faq4">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse4" aria-expanded="false" aria-controls="collapse4">
                                <span class="me-3">❓</span> What's included in the freemium subscription?
                            </button>
                        </h2>
                        <div id="collapse4" class="accordion-collapse collapse" aria-labelledby="faq4" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                <p class="mb-0">With a free account, you can access most of our services. Our premium plans offer faster delivery, exclusive offers, and priority support.</p>
                            </div>
                        </div>
                    </div>

                    <!-- FAQ Item 5 -->
                    <div class="accordion-item mb-3 border rounded">
                        <h2 class="accordion-header" id="faq5">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse5" aria-expanded="false" aria-controls="collapse5">
                                <span class="me-3">❓</span> Can I order alcohol or restricted items?
                            </button>
                        </h2>
                        <div id="collapse5" class="accordion-collapse collapse" aria-labelledby="faq5" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                <p class="mb-0">No. KANMA strictly prohibits delivery of alcohol, tobacco, or any inappropriate items, in line with our community and legal standards.</p>
                            </div>
                        </div>
                    </div>

                    <!-- FAQ Item 6 -->
                    <div class="accordion-item mb-3 border rounded">
                        <h2 class="accordion-header" id="faq6">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse6" aria-expanded="false" aria-controls="collapse6">
                                <span class="me-3">❓</span> How are payments handled?
                            </button>
                        </h2>
                        <div id="collapse6" class="accordion-collapse collapse" aria-labelledby="faq6" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                <p class="mb-0">Payments are processed securely via <a href="https://razorpay.com" target="_blank" class="text-decoration-none">Razorpay</a>. You can use UPI, credit/debit cards, or net banking.</p>
                            </div>
                        </div>
                    </div>

                    <!-- FAQ Item 7 -->
                    <div class="accordion-item mb-3 border rounded">
                        <h2 class="accordion-header" id="faq7">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse7" aria-expanded="false" aria-controls="collapse7">
                                <span class="me-3">❓</span> Is my personal data safe?
                            </button>
                        </h2>
                        <div id="collapse7" class="accordion-collapse collapse" aria-labelledby="faq7" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                <p class="mb-0">Absolutely. We only collect basic details like name and phone number. Your data is not shared with any third parties.</p>
                            </div>
                        </div>
                    </div>

                    <!-- FAQ Item 8 -->
                    <div class="accordion-item mb-3 border rounded">
                        <h2 class="accordion-header" id="faq8">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse8" aria-expanded="false" aria-controls="collapse8">
                                <span class="me-3">❓</span> How do I contact support?
                            </button>
                        </h2>
                        <div id="collapse8" class="accordion-collapse collapse" aria-labelledby="faq8" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                <p class="mb-3">You can reach us at:</p>
                                <div class="d-flex align-items-center mb-2">
                                    <span class="me-3">📞</span>
                                    <a href="tel:8333916492" class="text-decoration-none">8333916492</a>
                                </div>
                                <div class="d-flex align-items-center">
                                    <span class="me-3">📧</span>
                                    <a href="{{ route('contact') }}" class="text-decoration-none">support@kanma.in</a>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</section>

<!-- Additional Help Section -->
<section class="py-5 bg-light">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center">
                <h3 class="mb-4">Still have questions?</h3>
                <p class="mb-4">Can't find what you're looking for? We're here to help!</p>
                <div class="d-flex justify-content-center gap-3">
                    <a href="tel:8333916492" class="btn btn-primary">
                        <span class="me-2">📞</span> Call Us
                    </a>
                    <a href="contact" class="btn btn-outline-primary">
                        <span class="me-2">📧</span> Email Us
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection 