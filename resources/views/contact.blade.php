@extends('layouts.main')

@section('content')
<section class="py-5 bg-light">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12 text-center">
                <h1 class="display-4 text-dark">Contact Us</h1>
                <p class="text-muted">Get in touch with our support team</p>
            </div>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="row">
                    <!-- Contact Information -->
                    <div class="col-md-5 mb-4">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body p-4">
                                <h3 class="card-title mb-4">Get in Touch</h3>
                                <p class="text-muted mb-4">Have questions or need assistance? We're here to help you with any inquiries about KANMA services.</p>
                                
                                <div class="contact-info">
                                    <div class="d-flex align-items-center mb-4">
                                        <div class="contact-icon me-3">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" class="text-primary">
                                                <path fill="currentColor" d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24c1.12.37 2.33.57 3.57.57c.55 0 1 .45 1 1V20c0 .55-.45 1-1 1c-9.39 0-17-7.61-17-17c0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1c0 1.25.2 2.45.57 3.57c.11.35.03.74-.25 1.02l-2.2 2.2z"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <h6 class="mb-1">Phone</h6>
                                            <a href="tel:8333916492" class="text-decoration-none">8333916492</a>
                                        </div>
                                    </div>
                                    
                                    <div class="d-flex align-items-center mb-4">
                                        <div class="contact-icon me-3">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" class="text-primary">
                                                <path fill="currentColor" d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5l-8-5V6l8 5l8-5v2z"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <h6 class="mb-1">Email</h6>
                                            <a href="mailto:support@kanma.in" class="text-decoration-none">support@kanma.in</a>
                                        </div>
                                    </div>
                                    
                                    
                                    
                                    <div class="d-flex align-items-center">
                                        <div class="contact-icon me-3">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" class="text-primary">
                                                <path fill="currentColor" d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10s10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5l1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <h6 class="mb-1">Support Hours</h6>
                                            <p class="mb-0 text-muted">24/7 Customer Support</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Contact Form -->
                    <div class="col-md-7">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body p-4">
                                <h3 class="card-title mb-4">Send us a Message</h3>
                                <form id="contactForm" action="{{ route('contact.store') }}" method="POST">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="name" class="form-label">Full Name *</label>
                                            <input type="text" class="form-control" id="name" name="name" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="email" class="form-label">Email Address *</label>
                                            <input type="email" class="form-control" id="email" name="email" required>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="phone" class="form-label">Phone Number</label>
                                            <input type="tel" class="form-control" id="phone" name="phone">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="subject" class="form-label">Subject *</label>
                                            <select class="form-select" id="subject" name="subject" required>
                                                <option value="">Select a subject</option>
                                                <option value="General Inquiry">General Inquiry</option>
                                                <option value="Order Support">Order Support</option>
                                                <option value="Technical Issue">Technical Issue</option>
                                                <option value="Payment Issue">Payment Issue</option>
                                                <option value="Delivery Issue">Delivery Issue</option>
                                                <option value="Account Issue">Account Issue</option>
                                                <option value="Feedback">Feedback</option>
                                                <option value="Other">Other</option>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="message" class="form-label">Message *</label>
                                        <textarea class="form-control" id="message" name="message" rows="5" placeholder="Please describe your inquiry in detail..." required></textarea>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="privacy" name="privacy" required>
                                            <label class="form-check-label" for="privacy">
                                                I agree to the <a href="{{ route('static.privacy') }}" target="_blank" class="text-decoration-none">Privacy Policy</a> and consent to being contacted regarding my inquiry.
                                            </label>
                                        </div>
                                    </div>
                                    
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <span class="me-2">📧</span> Send Message
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FAQ Section -->
<section class="py-5 bg-light">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center">
                <h3 class="mb-4">Frequently Asked Questions</h3>
                <p class="mb-4">Find quick answers to common questions in our FAQ section.</p>
                <a href="{{ route('faq') }}" class="btn btn-outline-primary">
                    <span class="me-2">❓</span> View FAQ
                </a>
            </div>
        </div>
    </div>
</section>

<style>
.contact-icon {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: rgba(13, 110, 253, 0.1);
    border-radius: 50%;
}

.contact-info a {
    color: #0d6efd;
}

.contact-info a:hover {
    color: #0a58ca;
}
</style>

<script>
document.getElementById('contactForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Get form data
    const formData = new FormData(this);
    
    // Show loading state
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span> Sending...';
    submitBtn.disabled = true;
    
    // Submit form via AJAX
    fetch(this.action, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(Object.fromEntries(formData))
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            // Show success message
            alert(data.message);
            
            // Reset form
            this.reset();
        } else {
            // Show error message
            alert(data.message || 'An error occurred. Please try again.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred. Please try again or contact us directly.');
    })
    .finally(() => {
        // Reset button
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
});
</script>
@endsection 