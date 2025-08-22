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

<!-- SVG Icons for Password Toggle -->
<svg style="display: none;">
    <defs>
        <symbol id="eye" viewBox="0 0 24 24" fill="currentColor">
            <path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/>
        </symbol>
        <symbol id="eye-slash" viewBox="0 0 24 24" fill="currentColor">
            <path d="M12 7c2.76 0 5 2.24 5 5 0 .65-.13 1.26-.36 1.83l2.92 2.92c1.51-1.26 2.7-2.89 3.43-4.75-1.73-4.39-6-7.5-11-7.5-1.4 0-2.74.25-3.98.7l2.16 2.16C10.74 7.13 11.35 7 12 7zM2 4.27l2.28 2.28.46.46C3.08 8.3 1.78 10.02 1 12c1.73 4.39 6 7.5 11 7.5 1.55 0 3.03-.3 4.38-.84l.42.42L19.73 22 21 20.73 3.27 3 2 4.27zM7.53 9.8l1.55 1.55c-.05.21-.08.43-.08.65 0 1.66 1.34 3 3 3 .22 0 .44-.03.65-.08l1.55 1.55c-.67.33-1.41.53-2.2.53-2.76 0-5-2.24-5-5 0-.79.2-1.53.53-2.2zm4.31-.78l3.15 3.15.02-.16c0-1.66-1.34-3-3-3l-.17.01z"/>
        </symbol>
    </defs>
</svg>

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
                                <label for="shop_owner_password" class="form-label">Password <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="shop_owner_password" name="password" required>
                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('shop_owner_password')">
                                        <svg width="16" height="16" class="password-toggle-icon">
                                            <use xlink:href="#eye"></use>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="shop_owner_password_confirmation" class="form-label">Confirm Password <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="shop_owner_password_confirmation" name="password_confirmation" required>
                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('shop_owner_password_confirmation')">
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
                            
                            <!-- Captcha Section -->
                            <div class="mb-3 captcha-container">
                                <label class="form-label">Security Verification <span class="text-danger">*</span></label>
                                <div class="row align-items-end">
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center">
                                            <span class="me-2">What is</span>
                                            <span id="captchaQuestion" class="captcha-question mx-2"></span>
                                            <span class="me-2">?</span>
                                            <button type="button" class="btn btn-sm btn-outline-secondary ms-2 captcha-refresh-btn" id="refreshCaptcha" title="Refresh Captcha">
                                                <i class="bi bi-arrow-clockwise"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <input type="number" class="form-control captcha-input" id="captchaAnswer" name="captcha_answer" placeholder="Enter your answer" required>
                                        <input type="hidden" id="captchaKey" name="captcha_key">
                                    </div>
                                </div>
                                <div class="form-text">Please solve this simple math problem to verify you're human.</div>
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
// Password toggle function
function togglePassword(inputId) {
    const passwordInput = document.getElementById(inputId);
    const toggleBtn = passwordInput.nextElementSibling;
    const icon = toggleBtn.querySelector('use');
    
    const type = passwordInput.type === 'password' ? 'text' : 'password';
    passwordInput.type = type;
    
    // Update icon
    icon.setAttribute('xlink:href', type === 'password' ? '#eye' : '#eye-slash');
}

// Captcha functionality
function loadCaptcha() {
    fetch('{{ route("captcha.new") }}')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('captchaQuestion').textContent = data.question;
                document.getElementById('captchaKey').value = data.captcha_key;
            }
        })
        .catch(error => {
            console.error('Error loading captcha:', error);
        });
}

document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('shopOwnerForm');
    
    if (!form) {
        console.error('Shop owner form not found');
        return;
    }
    
    // Load initial captcha when modal opens
    const modal = document.getElementById('shopOwnerModal');
    if (modal) {
        modal.addEventListener('shown.bs.modal', function() {
            loadCaptcha();
        });
    }
    
    // Refresh captcha button
    const refreshCaptchaBtn = document.getElementById('refreshCaptcha');
    if (refreshCaptchaBtn) {
        refreshCaptchaBtn.addEventListener('click', function() {
            loadCaptcha();
            document.getElementById('captchaAnswer').value = '';
        });
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
                
                // If captcha error, refresh captcha
                if (data.message && data.message.includes('captcha')) {
                    loadCaptcha();
                    document.getElementById('captchaAnswer').value = '';
                }
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
            
            // Refresh captcha on any error
            loadCaptcha();
            document.getElementById('captchaAnswer').value = '';
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