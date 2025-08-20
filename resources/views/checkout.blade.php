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

@auth
    @if(!auth()->user()->currentSubscription())
    <section class="py-3">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="alert alert-info d-flex align-items-center" role="alert">
                        <i class="bi bi-info-circle-fill me-2"></i>
                        <div>
                            Save on delivery fees with our membership plans! Get free delivery and more benefits.
                            <a href="{{ route('subscription.plans') }}" class="alert-link ms-2">View Membership Plans</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    @endif
@endauth

<section class="py-5">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h3 class="card-title mb-4">Order Summary</h3>
                        
                        <!-- Cart Items -->
                        <div class="order-items mb-4">
                            @foreach($items as $item)
                            <div class="d-flex align-items-center mb-3 pb-3 border-bottom">
                                <img src="{{ asset($item['image_path']) }}" alt="{{ $item['product_name'] }}" class="img-thumbnail" style="width: 80px; height: 80px; object-fit: cover;">
                                <div class="ms-3 flex-grow-1">
                                    <h6 class="mb-1">{{ $item['product_name'] }}</h6>
                                    <p class="mb-0 text-muted">Quantity: {{ $item['quantity'] }} × ₹{{ number_format($item['price'], 2) }}</p>
                                </div>
                                <div class="text-end">
                                    <span class="fw-bold">₹{{ number_format($item['total'], 2) }}</span>
                                </div>
                            </div>
                            @endforeach
                        </div>

                        <!-- Order Totals -->
                        <div class="order-totals mb-4">
                            @auth
                                @php
                                    $user = auth()->user();
                                    $activeSubscription = $user->currentSubscription();
                                    $hasActiveMembership = $activeSubscription && $activeSubscription->status === 'active';
                                @endphp
                                @if($hasActiveMembership)
                                    <div class="alert alert-success mb-3">
                                        <i class="bi bi-check-circle-fill me-2"></i>
                                        <strong>Membership Active!</strong><br>
                                        <small>No small cart fees or minimum order restrictions.</small>
                                    </div>
                                @endif
                            @endauth
                            
                            <div class="d-flex justify-content-between mb-2">
                                <span>Subtotal</span>
                                <span>₹{{ number_format($total, 2) }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Delivery Fee</span>
                                <span>
                                    @if($deliveryFee > 0)
                                        ₹{{ number_format($deliveryFee, 2) }}
                                    @else
                                        <span class="text-success">Free</span>
                                    @endif
                                </span>
                            </div>
                            @if($smallCartFee > 0)
                            <div class="d-flex justify-content-between mb-2">
                                <span>Small Cart Fee</span>
                                <span class="text-warning">₹{{ number_format($smallCartFee, 2) }}</span>
                            </div>
                            @endif
                            <hr>
                            <div class="d-flex justify-content-between mb-4">
                                <strong>Total</strong>
                                <strong class="text-primary">₹{{ number_format($total + $deliveryFee + $smallCartFee, 2) }}</strong>
                            </div>
                        </div>

                        <!-- Payment Options -->
                        <div class="payment-options mb-4">
                            <h5 class="mb-3">Delivery Address</h5>
                            
                            @auth
                                @if(auth()->user()->addresses->isNotEmpty())
                                    <div class="mb-3">
                                        <div class="row g-3">
                                            @foreach(auth()->user()->addresses as $address)
                                                <div class="col-md-6">
                                                    <div class="card h-100 {{ $address->is_default ? 'border-primary' : '' }}">
                                                        <div class="card-body">
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="radio" name="delivery_address" id="address_{{ $address->id }}" value="{{ $address->id }}" {{ $address->is_default ? 'checked' : '' }}>
                                                                <label class="form-check-label" for="address_{{ $address->id }}">
                                                                    <div class="d-flex justify-content-between align-items-start">
                                                                        <div>
                                                                            <strong>{{ $address->name }}</strong>
                                                                            @if($address->is_default)
                                                                                <span class="badge bg-primary ms-2">Default</span>
                                                                            @endif
                                                                            <div class="text-muted small mt-1">
                                                                                {{ $address->address_line1 }}<br>
                                                                                @if($address->address_line2)
                                                                                    {{ $address->address_line2 }}<br>
                                                                                @endif
                                                                                {{ $address->city }}, {{ $address->state }} - {{ $address->postal_code }}<br>
                                                                                {{ $address->country }}
                                                                                @if($address->landmark)
                                                                                    <br>Landmark: {{ $address->landmark }}
                                                                                @endif
                                                                            </div>
                                                                        </div>
                                                                        <span class="badge bg-light text-dark">{{ ucfirst($address->address_type) }}</span>
                                                                    </div>
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <a href="{{ route('addresses.manage') }}" class="btn btn-outline-primary btn-sm">
                                            <i class="bi bi-plus-circle"></i> Add New Address
                                        </a>
                                    </div>
                                @else
                                    <div class="alert alert-info">
                                        <p class="mb-0">No addresses found. Please add a delivery address.</p>
                                        <a href="{{ route('addresses.manage') }}" class="btn btn-primary btn-sm mt-2">Add Address</a>
                                    </div>
                                @endif
                            @else
                                <div class="alert alert-info">
                                    <p class="mb-0">Please login to add and manage delivery addresses.</p>
                                    <button type="button" class="btn btn-primary btn-sm mt-2" data-bs-toggle="modal" data-bs-target="#loginModal">
                                        Login
                                    </button>
                                </div>
                            @endauth

                            <h5 class="mb-3">Payment Method</h5>
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="radio" name="payment_method" id="cod" value="cod" checked>
                                <label class="form-check-label" for="cod">
                                    Cash on Delivery (COD)
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="payment_method" id="razorpay" value="razorpay">
                                <label class="form-check-label" for="razorpay">
                                    Pay with Razorpay
                                </label>
                            </div>
                        </div>

                        <!-- Checkout Button -->
                        <button type="button" class="btn btn-primary w-100" id="checkout-btn">
                            Proceed to Checkout
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Login Modal -->
<div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="loginModalLabel">Login Required</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Please login to continue with your checkout.</p>
                <div class="d-grid gap-2">
                    <a href="{{ route('login', ['redirect' => route('checkout')]) }}" class="btn btn-primary">Login</a>
                    <a href="{{ route('register', ['redirect' => route('checkout')]) }}" class="btn btn-outline-primary">Register</a>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const checkoutBtn = document.getElementById('checkout-btn');
    const loginModal = new bootstrap.Modal(document.getElementById('loginModal'));
    const isAuthenticated = {{ auth()->check() ? 'true' : 'false' }};

    checkoutBtn.addEventListener('click', function() {
        console.log('Checkout button clicked');
        
        if (!isAuthenticated) {
            console.log('User not authenticated, showing login modal');
            loginModal.show();
            return;
        }

        const selectedAddress = document.querySelector('input[name="delivery_address"]:checked');
        if (!selectedAddress) {
            console.log('No delivery address selected');
            alert('Please select a delivery address');
            return;
        }
        console.log('Selected address:', selectedAddress.value);

        const selectedPaymentMethod = document.querySelector('input[name="payment_method"]:checked').value;
        console.log('Selected payment method:', selectedPaymentMethod);
        
        if (selectedPaymentMethod === 'razorpay') {
            console.log('Initializing Razorpay payment');
            // Initialize Razorpay
            const options = {
                key: "{{ config('services.razorpay.key') }}",
                amount: "{{ ($total + $deliveryFee + $smallCartFee) * 100 }}", // Amount in paise
                currency: "INR",
                name: "{{ config('app.name') }}",
                description: "Order Payment",
                order_id: "{{ $razorpayOrder->id ?? '' }}",
                handler: function (response) {
                    console.log('Razorpay payment successful', response);
                    
                    // Show loading state
                    checkoutBtn.disabled = true;
                    checkoutBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...';
                    
                    // Create form and submit
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = "{{ route('checkout.store') }}";

                    // Add CSRF token
                    const csrfToken = document.createElement('input');
                    csrfToken.type = 'hidden';
                    csrfToken.name = '_token';
                    csrfToken.value = "{{ csrf_token() }}";
                    form.appendChild(csrfToken);

                    // Add delivery address
                    const addressInput = document.createElement('input');
                    addressInput.type = 'hidden';
                    addressInput.name = 'delivery_address';
                    addressInput.value = selectedAddress.value;
                    form.appendChild(addressInput);

                    // Add payment method
                    const paymentMethod = document.createElement('input');
                    paymentMethod.type = 'hidden';
                    paymentMethod.name = 'payment_method';
                    paymentMethod.value = 'razorpay';
                    form.appendChild(paymentMethod);

                    // Add Razorpay payment details
                    const razorpayPaymentId = document.createElement('input');
                    razorpayPaymentId.type = 'hidden';
                    razorpayPaymentId.name = 'razorpay_payment_id';
                    razorpayPaymentId.value = response.razorpay_payment_id;
                    form.appendChild(razorpayPaymentId);

                    const razorpayOrderId = document.createElement('input');
                    razorpayOrderId.type = 'hidden';
                    razorpayOrderId.name = 'razorpay_order_id';
                    razorpayOrderId.value = response.razorpay_order_id;
                    form.appendChild(razorpayOrderId);

                    const razorpaySignature = document.createElement('input');
                    razorpaySignature.type = 'hidden';
                    razorpaySignature.name = 'razorpay_signature';
                    razorpaySignature.value = response.razorpay_signature;
                    form.appendChild(razorpaySignature);

                    // Add form to document and submit
                    document.body.appendChild(form);
                    console.log('Submitting form with payment details', {
                        delivery_address: selectedAddress.value,
                        payment_method: 'razorpay',
                        razorpay_payment_id: response.razorpay_payment_id,
                        razorpay_order_id: response.razorpay_order_id,
                        razorpay_signature: response.razorpay_signature
                    });
                    
                    // Submit form using fetch to handle the response
                    fetch(form.action, {
                        method: 'POST',
                        body: new FormData(form),
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => {
                        console.log('Server response received', response);
                        return response.json();
                    })
                    .then(data => {
                        console.log('Server response data:', data);
                        if (data.redirect) {
                            console.log('Redirecting to:', data.redirect);
                            window.location.href = data.redirect;
                        } else {
                            console.log('No redirect URL, going to orders page');
                            alert('Payment successful! Redirecting to orders page...');
                            window.location.href = "{{ route('orders') }}";
                        }
                    })
                    .catch(error => {
                        console.error('Error during form submission:', error);
                        alert('An error occurred. Please try again.');
                        checkoutBtn.disabled = false;
                        checkoutBtn.innerHTML = 'Proceed to Checkout';
                    });
                },
                prefill: {
                    name: "{{ auth()->check() ? auth()->user()->first_name . ' ' . auth()->user()->last_name : '' }}",
                    email: "{{ auth()->check() ? auth()->user()->email : '' }}",
                    contact: "{{ auth()->check() ? auth()->user()->phone : '' }}"
                },
                theme: {
                    color: "#3399cc"
                },
                modal: {
                    ondismiss: function() {
                        console.log('Payment modal dismissed');
                    }
                }
            };

            try {
                const rzp = new Razorpay(options);
                rzp.on('payment.failed', function (response) {
                    console.error('Payment failed', response.error);
                    alert('Payment failed. Please try again.');
                });
                rzp.open();
            } catch (error) {
                console.error('Error initializing Razorpay', error);
                alert('Error initializing payment. Please try again.');
            }
        } else {
            console.log('Processing COD order');
            // For COD, create and submit form
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = "{{ route('checkout.store') }}";

            // Add CSRF token
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = "{{ csrf_token() }}";
            form.appendChild(csrfToken);

            // Add delivery address
            const addressInput = document.createElement('input');
            addressInput.type = 'hidden';
            addressInput.name = 'delivery_address';
            addressInput.value = selectedAddress.value;
            form.appendChild(addressInput);

            // Add payment method
            const paymentMethod = document.createElement('input');
            paymentMethod.type = 'hidden';
            paymentMethod.name = 'payment_method';
            paymentMethod.value = 'cod';
            form.appendChild(paymentMethod);

            // Show loading state
            checkoutBtn.disabled = true;
            checkoutBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...';

            console.log('Submitting COD form with data:', {
                delivery_address: selectedAddress.value,
                payment_method: 'cod'
            });

            // Submit form using fetch to handle the response
            fetch(form.action, {
                method: 'POST',
                body: new FormData(form),
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                console.log('Server response received', response);
                return response.json().then(data => ({
                    status: response.status,
                    data: data
                }));
            })
            .then(response => {
                console.log('Server response data:', response);
                
                // Check if the response indicates an error
                if (response.status >= 400 || !response.data.success) {
                    // Show error message
                    const errorMessage = response.data.message || 'An error occurred while processing your payment.';
                    alert(errorMessage);
                    checkoutBtn.disabled = false;
                    checkoutBtn.innerHTML = 'Proceed to Checkout';
                    return;
                }
                
                // Success case
                if (response.data.redirect) {
                    console.log('Redirecting to:', response.data.redirect);
                    window.location.href = response.data.redirect;
                } else {
                    console.log('No redirect URL, going to orders page');
                    alert('Payment successful! Redirecting to orders page...');
                    window.location.href = "{{ route('orders') }}";
                }
            })
            .catch(error => {
                console.error('Error during form submission:', error);
                alert('An error occurred. Please try again.');
                checkoutBtn.disabled = false;
                checkoutBtn.innerHTML = 'Proceed to Checkout';
            });
        }
    });
});
</script>

<!-- Test Mode Instructions -->
<div class="modal fade" id="testModeModal" tabindex="-1" aria-labelledby="testModeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="testModeModalLabel">Test Mode Instructions</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>You are currently in test mode. Use the following test card details:</p>
                <div class="card mb-3">
                    <div class="card-body">
                        <h6>For Successful Payment:</h6>
                        <ul class="list-unstyled">
                            <li><strong>Card Number:</strong> 4111 1111 1111 1111</li>
                            <li><strong>Expiry:</strong> Any future date</li>
                            <li><strong>CVV:</strong> Any 3 digits</li>
                            <li><strong>Name:</strong> Any name</li>
                        </ul>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <h6>For Failed Payment:</h6>
                        <ul class="list-unstyled">
                            <li><strong>Card Number:</strong> 4111 1111 1111 1111</li>
                            <li><strong>Expiry:</strong> Any past date</li>
                            <li><strong>CVV:</strong> Any 3 digits</li>
                            <li><strong>Name:</strong> Any name</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Add a button to show test mode instructions -->
<button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#testModeModal">
    <i class="bi bi-info-circle"></i> Test Mode Instructions
</button>
@endpush
@endsection 