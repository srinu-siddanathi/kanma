@php
use Illuminate\Support\Facades\Session;
use App\Models\Address;

$cart = Session::get('cart', []);
$cartCount = count($cart);

// Get selected address from session
$selectedAddressId = Session::get('selected_address_id');
$selectedAddress = null;

if ($selectedAddressId) {
    $selectedAddress = Address::find($selectedAddressId);
}
@endphp

<style>
/* Make form input borders more visible */
.form-control {
    border: 1px solid #ced4da !important;
    background-color: #fff !important;
    color: #212529 !important;
}
.form-control:focus {
    border-color: #80bdff !important;
    box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25) !important;
}
</style>

<header>
    <div class="container-fluid">
        <div class="row py-3 border-bottom align-items-center g-0">
            <div class="col-sm-4 col-lg-2 text-center text-sm-start">
                <div class="main-logo">
                    <a href="{{ route('home') }}">
                        <img src="{{ asset('images/logo.png') }}" alt="logo" class="img-fluid">
                    </a>
                </div>
            </div>

            <div class="col-sm-4 col-lg-2 pe-lg-2">
                <div class="location-selector d-flex align-items-center bg-light p-2 rounded-4" data-bs-toggle="modal"
                    data-bs-target="#locationModal" style="cursor: pointer;">
                    <svg width="24" height="24" class="me-2">
                        <use xlink:href="#location"></use>
                    </svg>
                    <div class="fw-medium text-truncate" id="selected-address-label">
                        @if($selectedAddress)
                            {{ $selectedAddress->address_line1 }}
                        @else
                            Select your location
                        @endif
                    </div>
                    <svg width="16" height="16" class="ms-auto">
                        <use xlink:href="#chevron-down"></use>
                    </svg>
                </div>
            </div>

            <div class="col-sm-6 offset-sm-2 offset-md-0 col-lg-5 d-none d-lg-block">
                <div class="search-bar row bg-light p-2 my-2 rounded-4 position-relative">
                    <div class="col-11">
                        <form id="search-form" class="text-center" action="{{ route('search') }}" method="get">
                            <input type="text" class="form-control border-0 bg-transparent" name="q" id="header-search-input" 
                                   autocomplete="off" placeholder="Search for more than 20,000 products" />
                        </form>
                        <div id="search-suggestions" class="position-absolute w-100 bg-white border rounded shadow-sm mt-1" 
                             style="z-index: 1050; display: none; left: 0; top: 100%;"></div>
                    </div>
                    <div class="col-1">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                            <path fill="currentColor" d="M21.71 20.29L18 16.61A9 9 0 1 0 16.61 18l3.68 3.68a1 1 0 0 0 1.42 0a1 1 0 0 0 0-1.39ZM11 18a7 7 0 1 1 7-7a7 7 0 0 1-7 7Z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="col-sm-4 col-lg-3 d-flex justify-content-end align-items-center">
                <div class="d-flex align-items-center gap-3">
                    <a href="#" class="text-dark position-relative" data-bs-toggle="offcanvas" data-bs-target="#offcanvasCart" aria-controls="offcanvasCart">
                        <svg width="24" height="24">
                            <use xlink:href="#cart"></use>
                        </svg>
                        @if($cartCount > 0)
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill cart-count" style="background-color: #dc3545 !important; opacity: 1 !important;">
                                {{ $cartCount }}
                            </span>
                        @else
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill cart-count" style="display: none; background-color: #dc3545 !important; opacity: 1 !important;">
                                0
                            </span>
                        @endif
                    </a>
                    @auth
                    <div class="dropdown">
                        <button class="btn btn-link text-dark text-decoration-none dropdown-toggle" type="button"
                            id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            {{ Auth::user()->name }}
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                            <li><a class="dropdown-item" href="{{ route('profile.edit') }}">Profile</a></li>
                            <li><a class="dropdown-item" href="{{ route('orders') }}">My Orders</a></li>
                            <li><a class="dropdown-item" href="{{ route('subscriptions') }}">My Subscriptions</a></li>
                            <li><a class="dropdown-item" href="{{ route('addresses.index') }}">Manage Addresses</a></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item">Logout</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                    @else
                    <li>
                        <a href="#" class="btn btn-outline-primary" data-bs-toggle="modal"
                            data-bs-target="#loginModal">Login</a>
                    </li>
                    @endauth
                </div>
            </div>
        </div>
    </div>
</header>

<!-- Location Modal -->
<div class="modal fade" id="locationModal" tabindex="-1" aria-labelledby="locationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="locationModalLabel">Choose Delivery Location</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                @auth
                    <div class="mb-4">
                        <h6 class="mb-3">Your Addresses</h6>
                        <div id="saved-addresses" class="list-group">
                            <!-- Addresses will be loaded here -->
                            <div class="text-center py-3">
                                <div class="spinner-border spinner-border-sm" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="text-center mb-3">
                        <span class="text-muted">or</span>
                    </div>
                @endauth

                
                <div class="d-grid">
                    <button type="button" class="btn btn-primary" onclick="detectCurrentLocation()">
                        <i class="bi bi-geo-alt"></i> Detect current location
                    </button>
                </div>
                <div id="locationError" class="alert alert-danger mt-3" style="display: none;"></div>
            </div>
        </div>
    </div>
</div>

<!-- Login Modal -->
<div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title" id="loginModalLabel">Login to your account</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="loginForm" class="px-3">
                    @csrf
                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" class="form-control" id="email" name="email" required autofocus>
                        <div class="invalid-feedback" id="emailError"></div>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="password" name="password" required>
                            <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                <svg width="16" height="16" class="password-toggle-icon">
                                    <use xlink:href="#eye"></use>
                                </svg>
                            </button>
                        </div>
                        <div class="invalid-feedback" id="passwordError"></div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="remember" name="remember">
                            <label class="form-check-label" for="remember">
                                Remember me
                            </label>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 mb-3">Login</button>

                   

                    <div class="text-center">
                        <span class="text-muted">Don't have an account?</span>
                        <a href="#" class="text-decoration-none" data-bs-toggle="modal" data-bs-target="#registerModal" data-bs-dismiss="modal">Register now</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Registration Modal -->
<div class="modal fade" id="registerModal" tabindex="-1" aria-labelledby="registerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title" id="registerModalLabel">Create an account</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="registerForm" class="px-3">
                    @csrf
                    <div class="mb-3">
                        <label for="reg_name" class="form-label">Full Name</label>
                        <input type="text" class="form-control" id="reg_name" name="name" required>
                        <div class="invalid-feedback" id="reg_nameError"></div>
                    </div>

                    <div class="mb-3">
                        <label for="reg_email" class="form-label">Email Address</label>
                        <input type="email" class="form-control" id="reg_email" name="email" required>
                        <div class="invalid-feedback" id="reg_emailError"></div>
                    </div>

                    <div class="mb-3">
                        <label for="reg_phone" class="form-label">Phone Number</label>
                        <div class="input-group">
                            <input type="tel" class="form-control" id="reg_phone" name="phone" required>
                            <button class="btn btn-outline-primary" type="button" id="sendOtpBtn">Send OTP</button>
                        </div>
                        <div class="invalid-feedback" id="reg_phoneError"></div>
                    </div>

                    <div class="mb-3" id="otpVerificationGroup" style="display: none;">
                        <label for="reg_otp" class="form-label">Enter OTP</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="reg_otp" name="otp" required>
                            <button class="btn btn-outline-primary" type="button" id="verifyOtpBtn">Verify OTP</button>
                        </div>
                        <div class="invalid-feedback" id="reg_otpError"></div>
                    </div>

                    <div class="mb-3">
                        <label for="reg_password" class="form-label">Password</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="reg_password" name="password" required>
                            <button class="btn btn-outline-secondary" type="button" id="toggleRegPassword">
                                <svg width="16" height="16" class="password-toggle-icon">
                                    <use xlink:href="#eye"></use>
                                </svg>
                            </button>
                        </div>
                        <div class="invalid-feedback" id="reg_passwordError"></div>
                    </div>

                    <div class="mb-3">
                        <label for="reg_password_confirmation" class="form-label">Confirm Password</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="reg_password_confirmation" name="password_confirmation" required>
                            <button class="btn btn-outline-secondary" type="button" id="toggleRegPasswordConfirmation">
                                <svg width="16" height="16" class="password-toggle-icon">
                                    <use xlink:href="#eye"></use>
                                </svg>
                            </button>
                        </div>
                        <div class="invalid-feedback" id="reg_password_confirmationError"></div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 mb-3" id="registerSubmitBtn" disabled>Register</button>

                    <div class="text-center">
                        <span class="text-muted">Already have an account?</span>
                        <a href="#" class="text-decoration-none" data-bs-toggle="modal" data-bs-target="#loginModal" data-bs-dismiss="modal">Login</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@include('layouts.partials.cart-offcanvas')

@push('scripts')
<script>
let autocomplete;
let locationModal;

function initializeAutocomplete() {
    const locationSearch = document.getElementById('locationSearch');
    if (!locationSearch) return;

    const options = {
        types: ['geocode'],
        componentRestrictions: { country: 'in' }
    };

    autocomplete = new google.maps.places.Autocomplete(locationSearch, options);
    autocomplete.addListener('place_changed', onPlaceChanged);
}

function onPlaceChanged() {
    const place = autocomplete.getPlace();
    if (!place.geometry) {
        document.getElementById('locationError').textContent = 'Please select a valid location from the suggestions.';
        document.getElementById('locationError').style.display = 'block';
        return;
    }

    const location = {
        lat: place.geometry.location.lat(),
        lng: place.geometry.location.lng(),
        address: place.formatted_address
    };

    checkServiceability(location);
}

function detectCurrentLocation() {
    const errorElement = document.getElementById('locationError');
    errorElement.style.display = 'none';

    if (!navigator.geolocation) {
        errorElement.textContent = 'Geolocation is not supported by your browser.';
        errorElement.style.display = 'block';
        return;
    }

    navigator.geolocation.getCurrentPosition(
        (position) => {
            const lat = position.coords.latitude;
            const lng = position.coords.longitude;

            // Reverse geocode to get address
            const geocoder = new google.maps.Geocoder();
            const latlng = { lat, lng };

            geocoder.geocode({ location: latlng }, (results, status) => {
                if (status === 'OK' && results[0]) {
                    const location = {
                        lat,
                        lng,
                        address: results[0].formatted_address
                    };
                    checkServiceability(location);
                } else {
                    errorElement.textContent = 'Could not determine your address. Please try selecting manually.';
                    errorElement.style.display = 'block';
                }
            });
        },
        (error) => {
            let errorMessage = 'Error getting your location. ';
            switch (error.code) {
                case error.PERMISSION_DENIED:
                    errorMessage += 'Please allow location access or select manually.';
                    break;
                case error.POSITION_UNAVAILABLE:
                    errorMessage += 'Location information is unavailable.';
                    break;
                case error.TIMEOUT:
                    errorMessage += 'Location request timed out.';
                    break;
                default:
                    errorMessage += 'An unknown error occurred.';
            }
            errorElement.textContent = errorMessage;
            errorElement.style.display = 'block';
        }
    );
}

function checkServiceability(location) {
    const errorElement = document.getElementById('locationError');
    errorElement.style.display = 'none';

    // Show loading state
    const locationButton = document.querySelector('[data-bs-target="#locationModal"]');
    if (locationButton) {
        locationButton.innerHTML = '<div class="spinner-border spinner-border-sm me-2" role="status"></div> Checking serviceability...';
    }

    // Check if location is serviceable
    fetch('/location/check-serviceability', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            lat: location.lat,
            lng: location.lng,
            address: location.address,
            pincode: location.pincode
        })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        if (data.serviceable) {
            saveLocation(location);
        } else {
            errorElement.textContent = data.message || 'Sorry, we don\'t deliver to this location yet. Please try a different address.';
            errorElement.style.display = 'block';
            
            // Reset location button
            if (locationButton) {
                locationButton.innerHTML = `
                    <svg width="24" height="24" class="me-2">
                        <use xlink:href="#location"></use>
                    </svg>
                    <div class="fw-medium text-truncate" id="selected-address-label">
                        Select your location
                    </div>
                    <svg width="16" height="16" class="ms-auto">
                        <use xlink:href="#chevron-down"></use>
                    </svg>
                `;
            }
        }
    })
    .catch(error => {
        console.error('Error checking serviceability:', error);
        errorElement.textContent = 'Error checking serviceability. Please try again.';
        errorElement.style.display = 'block';
        
        // Reset location button
        if (locationButton) {
            locationButton.innerHTML = `
                <svg width="24" height="24" class="me-2">
                    <use xlink:href="#location"></use>
                </svg>
                <div class="fw-medium text-truncate" id="selected-address-label">
                    Select your location
                </div>
                <svg width="16" height="16" class="ms-auto">
                    <use xlink:href="#chevron-down"></use>
                </svg>
            `;
        }
    });
}

function saveLocation(location) {
    // Save location to localStorage
    localStorage.setItem('userLocation', JSON.stringify(location));

    // Update UI to show selected location
    const locationButton = document.querySelector('[data-bs-target="#locationModal"]');
    if (locationButton) {
        locationButton.innerHTML = `
            <svg width="24" height="24" class="me-2">
                <use xlink:href="#location"></use>
            </svg>
            <div class="fw-medium text-truncate" id="selected-address-label">
                ${location.address.split(',')[0]}
            </div>
            <svg width="16" height="16" class="ms-auto">
                <use xlink:href="#chevron-down"></use>
            </svg>
        `;
    }

    // Close the modal
    const modal = bootstrap.Modal.getInstance(document.getElementById('locationModal'));
    if (modal) {
        modal.hide();
    }

    // Reload the page to update content based on new location
    window.location.reload();
}

document.addEventListener('DOMContentLoaded', function() {
    const locationModal = document.getElementById('locationModal');
    
    if (locationModal) {
        // Listen for the Google Maps API to be loaded
        window.addEventListener('googleMapsLoaded', function() {
            console.log('Google Maps API loaded event received');
            initializeAutocomplete();
        });

        // Initialize autocomplete when modal is shown
        locationModal.addEventListener('shown.bs.modal', function() {
            console.log('Modal shown');
            if (typeof google !== 'undefined' && google.maps) {
                console.log('Initializing autocomplete');
                initializeAutocomplete();
            }
            
            // Load saved addresses if user is logged in
            loadSavedAddresses();
        });
    }

    // Password toggle for login form
    const togglePasswordBtn = document.getElementById('togglePassword');
    if (togglePasswordBtn) {
        togglePasswordBtn.addEventListener('click', function() {
            const passwordInput = document.getElementById('password');
            const type = passwordInput.type === 'password' ? 'text' : 'password';
            passwordInput.type = type;
            this.querySelector('use').setAttribute('xlink:href', type === 'password' ? '#eye' : '#eye-slash');
        });
    }
});

function loadSavedAddresses() {
    const savedAddressesContainer = document.getElementById('saved-addresses');
    if (!savedAddressesContainer) return;

    // Show loading state
    savedAddressesContainer.innerHTML = `
        <div class="text-center py-3">
            <div class="spinner-border spinner-border-sm" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    `;

    // Add CSRF token to headers
    const token = document.querySelector('meta[name="csrf-token"]')?.content;
    
    // Make the AJAX call
    fetch('/addresses', {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': token
        },
        credentials: 'same-origin'
    })
    .then(response => {
        console.log('Response status:', response.status);
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        console.log('Address data:', data);
        if (data.addresses && data.addresses.length > 0) {
            savedAddressesContainer.innerHTML = data.addresses.map(address => `
                <button type="button" class="list-group-item list-group-item-action" 
                        onclick="selectSavedAddress(${JSON.stringify({
                            id: address.id,
                            latitude: address.latitude,
                            longitude: address.longitude,
                            address: `${address.address_line1}${address.address_line2 ? ', ' + address.address_line2 : ''}, ${address.city}, ${address.state} - ${address.postal_code}`,
                            pincode: address.postal_code
                        }).replace(/"/g, '&quot;')})">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <div class="fw-medium">${address.address_line1}</div>
                            ${address.address_line2 ? `<div class="small text-muted">${address.address_line2}</div>` : ''}
                            <div class="small text-muted">${address.city}, ${address.state} - ${address.postal_code}</div>
                        </div>
                        ${address.is_default ? '<span class="badge bg-primary ms-2">Default</span>' : ''}
                    </div>
                </button>
            `).join('');
        } else {
            savedAddressesContainer.innerHTML = `
                <div class="text-center py-3">
                    <div class="text-muted mb-3">No saved addresses found</div>
                    <a href="{{ route('addresses.index') }}" class="btn btn-primary btn-sm">Add New Address</a>
                </div>
            `;
        }
    })
    .catch(error => {
        console.error('Error loading addresses:', error);
        savedAddressesContainer.innerHTML = `
            <div class="text-center py-3 text-danger">
                <div class="mb-2">Error loading addresses: ${error.message}</div>
                <button type="button" class="btn btn-sm btn-outline-primary" onclick="loadSavedAddresses()">
                    Try Again
                </button>
            </div>
        `;
    });
}

function selectSavedAddress(address) {
    console.log('Selecting saved address:', address);
    
    // Create location object
    const location = {
        lat: address.latitude,
        lng: address.longitude,
        address: address.address,
        pincode: address.pincode,
        address_id: address.id
    };
    
    // Show loading state
    const locationButton = document.querySelector('[data-bs-target="#locationModal"]');
    const addressLabel = locationButton.querySelector('#selected-address-label');
    const originalContent = addressLabel.innerHTML;
    addressLabel.innerHTML = `
        <div class="spinner-border spinner-border-sm" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    `;

    // First check serviceability
    fetch('/location/check-serviceability', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify(location)
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Failed to check serviceability');
        }
        return response.json();
    })
    .then(data => {
        if (!data.serviceable) {
            throw new Error(data.message || 'This location is not serviceable');
        }

        // If serviceable, proceed to set the address
        return fetch(`/addresses/${address.id}/set-default`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Failed to update address');
        }
        return response.json();
    })
    .then(data => {
        // Save to localStorage
        localStorage.setItem('userLocation', JSON.stringify(location));

        // Update UI with selected address
        addressLabel.innerHTML = address.address.split(',')[0];

        // Close the modal
        const modal = bootstrap.Modal.getInstance(document.getElementById('locationModal'));
        if (modal) {
            modal.hide();
        }

        // Trigger any necessary UI updates
        if (typeof updateLocationDependentElements === 'function') {
            updateLocationDependentElements(location);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        addressLabel.innerHTML = originalContent;
        
        // Show error in modal
        const errorElement = document.getElementById('locationError');
        if (errorElement) {
            errorElement.textContent = error.message;
            errorElement.style.display = 'block';
        } else {
            alert(error.message);
        }
    });
}

// Function to update elements that depend on location
function updateLocationDependentElements(location) {
    // Update any elements that need to be refreshed based on location
    // For example, if you have a cart or product list that depends on location
    const cartElement = document.getElementById('cart-items');
    if (cartElement) {
        // Refresh cart items
        fetch('/cart', {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            // Update cart UI
            if (data.html) {
                cartElement.innerHTML = data.html;
            }
        });
    }

    // Update any other location-dependent elements
    // For example, if you have a product list that shows delivery availability
    const productList = document.querySelector('.product-list');
    if (productList) {
        // Refresh product list
        fetch('/products', {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            // Update product list UI
            if (data.html) {
                productList.innerHTML = data.html;
            }
        });
    }
}

// Handle login form submission
const loginForm = document.getElementById('loginForm');
if (loginForm) {
    loginForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Clear previous errors
        document.querySelectorAll('.invalid-feedback').forEach(el => el.remove());
        document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        
        const formData = new FormData(this);
        
        fetch('/ajax-login', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(Object.fromEntries(formData))
        })
        .then(async response => {
            if (response.status === 422) {
                const errorData = await response.json();
                for (const [field, messages] of Object.entries(errorData.errors)) {
                    const input = document.getElementById(field);
                    if (input) {
                        input.classList.add('is-invalid');
                        const feedback = document.createElement('div');
                        feedback.className = 'invalid-feedback';
                        feedback.innerText = messages[0];
                        input.parentNode.appendChild(feedback);
                    }
                }
                throw new Error('Validation failed');
            }
            return response.json();
        })
        .then(data => {
            if (data.status === 'success') {
                // Close the modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('loginModal'));
                if (modal) {
                    modal.hide();
                }
                // Reload the page to reflect the logged-in state
                window.location.reload();
            } else {
                throw new Error(data.message || 'Login failed');
            }
        })
        .catch(error => {
            if (error.message !== 'Validation failed') {
                console.error('Login error:', error);
                alert('An error occurred during login. Please try again.');
            }
        });
    });
}

// Search autocomplete functionality
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('header-search-input');
    const searchSuggestions = document.getElementById('search-suggestions');
    let searchTimeout;

    if (searchInput) {
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            const query = this.value.trim();

            if (query.length < 2) {
                searchSuggestions.style.display = 'none';
                return;
            }

            searchTimeout = setTimeout(() => {
                fetch(`/api/search-suggestions?q=${encodeURIComponent(query)}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data && Object.keys(data).length > 0) {
                            searchSuggestions.innerHTML = Object.entries(data)
                                .map(([id, name]) => `
                                    <a href="/search?q=${encodeURIComponent(name)}" 
                                       class="d-block px-3 py-2 text-decoration-none text-dark hover-bg-light">
                                        ${name}
                                    </a>
                                `).join('');
                            searchSuggestions.style.display = 'block';
                        } else {
                            searchSuggestions.style.display = 'none';
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching search suggestions:', error);
                        searchSuggestions.style.display = 'none';
                    });
            }, 300);
        });

        // Hide suggestions when clicking outside
        document.addEventListener('click', function(e) {
            if (!searchInput.contains(e.target) && !searchSuggestions.contains(e.target)) {
                searchSuggestions.style.display = 'none';
            }
        });

        // Handle suggestion clicks
        searchSuggestions.addEventListener('click', function(e) {
            if (e.target.tagName === 'A') {
                searchSuggestions.style.display = 'none';
            }
        });
    }
});

// Update cart count badge
function updateCartCount(count) {
    const badge = document.querySelector('.cart-count');
    if (badge) {
        badge.textContent = count;
        badge.style.display = count > 0 ? 'block' : 'none';
    }
}

// Listen for cart updates
$(document).on('cart:updated', function(event) {
    // Fetch updated cart count
    $.get('{{ route("cart.items") }}', function(response) {
        if (response.success) {
            updateCartCount(response.count);
        }
    });
});

// Initialize cart count on page load
$(document).ready(function() {
    $.get('{{ route("cart.items") }}', function(response) {
        if (response.success) {
            updateCartCount(response.count);
        }
    });
});

// Registration form handling
const registerForm = document.getElementById('registerForm');
if (registerForm) {
    const sendOtpBtn = document.getElementById('sendOtpBtn');
    const verifyOtpBtn = document.getElementById('verifyOtpBtn');
    const otpVerificationGroup = document.getElementById('otpVerificationGroup');
    const registerSubmitBtn = document.getElementById('registerSubmitBtn');
    let otpVerified = false;

    // Handle send OTP
    sendOtpBtn.addEventListener('click', function() {
        const phone = document.getElementById('reg_phone').value;
        if (!phone) {
            document.getElementById('reg_phoneError').textContent = 'Please enter your phone number';
            document.getElementById('reg_phone').classList.add('is-invalid');
            return;
        }

        // Disable button and show loading state
        sendOtpBtn.disabled = true;
        sendOtpBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Sending...';

        fetch('/send-otp', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ phone })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                otpVerificationGroup.style.display = 'block';
                sendOtpBtn.innerHTML = 'Resend OTP';
                sendOtpBtn.disabled = false;

                // Auto-fill OTP for testing (only if present in response)
                if (data.otp) {
                    document.getElementById('reg_otp').value = data.otp;
                }
            } else {
                throw new Error(data.message || 'Failed to send OTP');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('reg_phoneError').textContent = error.message;
            document.getElementById('reg_phone').classList.add('is-invalid');
            sendOtpBtn.innerHTML = 'Send OTP';
            sendOtpBtn.disabled = false;
        });
    });

    // Handle OTP verification
    verifyOtpBtn.addEventListener('click', function() {
        const phone = document.getElementById('reg_phone').value;
        const otp = document.getElementById('reg_otp').value;

        if (!otp) {
            document.getElementById('reg_otpError').textContent = 'Please enter the OTP';
            document.getElementById('reg_otp').classList.add('is-invalid');
            return;
        }

        // Disable button and show loading state
        verifyOtpBtn.disabled = true;
        verifyOtpBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Verifying...';

        fetch('/verify-otp', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ phone, otp })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                otpVerified = true;
                verifyOtpBtn.innerHTML = 'Verified ✓';
                verifyOtpBtn.disabled = true;
                registerSubmitBtn.disabled = false;
                document.getElementById('reg_otp').classList.remove('is-invalid');
            } else {
                throw new Error(data.message || 'Invalid OTP');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('reg_otpError').textContent = error.message;
            document.getElementById('reg_otp').classList.add('is-invalid');
            verifyOtpBtn.innerHTML = 'Verify OTP';
            verifyOtpBtn.disabled = false;
        });
    });

    // Handle registration form submission
    registerForm.addEventListener('submit', function(e) {
        e.preventDefault();

        if (!otpVerified) {
            document.getElementById('reg_otpError').textContent = 'Please verify your phone number first';
            document.getElementById('reg_otp').classList.add('is-invalid');
            return;
        }

        // Clear previous errors
        document.querySelectorAll('.invalid-feedback').forEach(el => el.remove());
        document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));

        const formData = new FormData(this);
        
        // Disable submit button and show loading state
        registerSubmitBtn.disabled = true;
        registerSubmitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Registering...';

        fetch('/register', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(Object.fromEntries(formData))
        })
        .then(async response => {
            if (response.status === 422) {
                const errorData = await response.json();
                for (const [field, messages] of Object.entries(errorData.errors)) {
                    const input = document.getElementById(`reg_${field}`);
                    if (input) {
                        input.classList.add('is-invalid');
                        const feedback = document.createElement('div');
                        feedback.className = 'invalid-feedback';
                        feedback.innerText = messages[0];
                        input.parentNode.appendChild(feedback);
                    }
                }
                throw new Error('Validation failed');
            }
            return response.json();
        })
        .then(data => {
            if (data.status === 'success') {
                // Close the modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('registerModal'));
                if (modal) {
                    modal.hide();
                }
                // Reload the page to reflect the logged-in state
                window.location.reload();
            } else {
                throw new Error(data.message || 'Registration failed');
            }
        })
        .catch(error => {
            if (error.message !== 'Validation failed') {
                console.error('Registration error:', error);
                alert('An error occurred during registration. Please try again.');
            }
            registerSubmitBtn.disabled = false;
            registerSubmitBtn.innerHTML = 'Register';
        });
    });

    // Toggle password visibility
    document.getElementById('toggleRegPassword').addEventListener('click', function() {
        const passwordInput = document.getElementById('reg_password');
        const type = passwordInput.type === 'password' ? 'text' : 'password';
        passwordInput.type = type;
        this.querySelector('use').setAttribute('xlink:href', type === 'password' ? '#eye' : '#eye-slash');
    });

    document.getElementById('toggleRegPasswordConfirmation').addEventListener('click', function() {
        const passwordInput = document.getElementById('reg_password_confirmation');
        const type = passwordInput.type === 'password' ? 'text' : 'password';
        passwordInput.type = type;
        this.querySelector('use').setAttribute('xlink:href', type === 'password' ? '#eye' : '#eye-slash');
    });
}
</script>
@endpush