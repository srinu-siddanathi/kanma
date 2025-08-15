<section class="py-5 bg-light">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <div class="pe-lg-5">
                    <h2 class="display-6 fw-bold mb-4">Become a Shop Owner</h2>
                    <p class="lead text-muted mb-4">
                        Join our platform and start selling your products to customers in your area. 
                        Set up your shop, manage inventory, and grow your business with our comprehensive tools.
                    </p>
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-primary rounded-circle p-2 me-3">
                                    <i class="bi bi-shop text-white"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1">Easy Setup</h6>
                                    <small class="text-muted">Quick shop registration process</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-success rounded-circle p-2 me-3">
                                    <i class="bi bi-graph-up text-white"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1">Grow Business</h6>
                                    <small class="text-muted">Reach more customers</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-warning rounded-circle p-2 me-3">
                                    <i class="bi bi-tools text-white"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1">Manage Inventory</h6>
                                    <small class="text-muted">Easy product management</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-info rounded-circle p-2 me-3">
                                    <i class="bi bi-headset text-white"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1">24/7 Support</h6>
                                    <small class="text-muted">We're here to help</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary btn-lg" data-bs-toggle="modal" data-bs-target="#shopOwnerModal">
                        <i class="bi bi-shop me-2"></i>
                        Register as Shop Owner
                    </button>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="text-center">
                    <img src="{{ asset('images/shop-owner-registration.jpg') }}" 
                         alt="Shop Owner Registration" 
                         class="img-fluid rounded-3 shadow-lg"
                         style="max-width: 100%; height: auto;"
                         onerror="this.src='{{ asset('images/banner-image-1.jpg') }}';">
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Shop Owner Registration Modal -->
<div class="modal fade" id="shopOwnerModal" tabindex="-1" aria-labelledby="shopOwnerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="shopOwnerModalLabel">
                    <i class="bi bi-shop me-2"></i>
                    Shop Owner Registration
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="shopOwnerForm" action="{{ route('shop-owner.register') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="row">
                        <!-- Personal Information -->
                        <div class="col-md-6">
                            <h6 class="mb-3 text-primary">Personal Information</h6>
                            
                            <div class="mb-3">
                                <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="phone" class="form-label">Phone Number <span class="text-danger">*</span></label>
                                <input type="tel" class="form-control" id="phone" name="phone" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="password" name="password" required>
                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password')">
                                        <svg width="16" height="16" class="password-toggle-icon">
                                            <use xlink:href="#eye"></use>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label">Confirm Password <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password_confirmation')">
                                        <svg width="16" height="16" class="password-toggle-icon">
                                            <use xlink:href="#eye"></use>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Shop Information -->
                        <div class="col-md-6">
                            <h6 class="mb-3 text-primary">Shop Information</h6>
                            
                            <div class="mb-3">
                                <label for="shop_name" class="form-label">Shop Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="shop_name" name="shop_name" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="shop_description" class="form-label">Shop Description</label>
                                <textarea class="form-control" id="shop_description" name="shop_description" rows="3" placeholder="Tell us about your shop..."></textarea>
                            </div>
                            
                            <div class="mb-3">
                                <label for="shop_address" class="form-label">Shop Address <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="shop_address" name="shop_address" rows="3" required></textarea>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-12">
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="terms" name="terms" required>
                                <label class="form-check-label" for="terms">
                                    I agree to the <a href="{{ route('static.terms') }}" target="_blank">Terms and Conditions</a> and <a href="{{ route('static.privacy') }}" target="_blank">Privacy Policy</a> <span class="text-danger">*</span>
                                </label>
                            </div>
                            
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="newsletter" name="newsletter">
                                <label class="form-check-label" for="newsletter">
                                    Subscribe to our newsletter for updates and offers
                                </label>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="shopOwnerForm" class="btn btn-primary">
                    <i class="bi bi-shop me-2"></i>
                    Register Shop
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('shopOwnerForm');
    
    if (!form) {
        console.error('Shop owner form not found');
        return;
    }
    
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Find submit button - it's outside the form but associated via form attribute
        const submitBtn = document.querySelector('button[type="submit"][form="shopOwnerForm"]');
        if (!submitBtn) {
            console.error('Submit button not found');
            return;
        }
        
        const originalText = submitBtn.innerHTML;
        
        // Show loading state
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Registering...';
        
        // Create FormData object
        const formData = new FormData(form);
        
        // Fix checkbox values - convert "on" to boolean
        const newsletterCheckbox = form.querySelector('input[name="newsletter"]');
        if (newsletterCheckbox) {
            formData.set('newsletter', newsletterCheckbox.checked ? '1' : '0');
        }
        
        // Submit form via AJAX
        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show success message
                Swal.fire({
                    icon: 'success',
                    title: 'Registration Successful!',
                    text: data.message || 'Your shop registration has been submitted successfully. We will review and contact you soon.',
                    confirmButtonText: 'OK'
                }).then((result) => {
                    // Close modal and redirect if needed
                    const modal = bootstrap.Modal.getInstance(document.getElementById('shopOwnerModal'));
                    if (modal) {
                        modal.hide();
                    }
                    
                    if (data.redirect) {
                        window.location.href = data.redirect;
                    }
                });
            } else {
                // Show error message
                Swal.fire({
                    icon: 'error',
                    title: 'Registration Failed',
                    text: data.message || 'Something went wrong. Please try again.',
                    confirmButtonText: 'OK'
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Registration Failed',
                text: 'An error occurred. Please try again.',
                confirmButtonText: 'OK'
            });
        })
        .finally(() => {
            // Reset button state
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            }
        });
    });
});
</script>
@endpush 