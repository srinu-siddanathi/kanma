@extends('layouts.main')

@section('content')
<section class="py-5 bg-light">
    <div class="container">
        <div class="row">
            @include('layouts.partials.user-sidebar')
            
            <div class="col-md-9">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h1 class="display-4 mb-0">My Addresses</h1>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addressModal" onclick="openAddAddressModal()">Add New Address</button>
                </div>
                @if(auth()->user()->addresses->isEmpty())
                    <div class="alert alert-info">No addresses found.</div>
                @else
                    <div class="row g-4">
                        @foreach(auth()->user()->addresses as $address)
                            <div class="col-md-6 col-lg-4">
                                <div class="card shadow-sm h-100">
                                    <div class="card-body d-flex flex-column">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="fw-bold">{{ $address->name }}</span>
                                            @if($address->is_default)
                                                <span class="badge bg-success">Default</span>
                                            @endif
                                        </div>
                                        <div class="mb-2">
                                            <span class="fw-medium">Type:</span> {{ ucfirst($address->address_type) }}
                                        </div>
                                        <div class="mb-2">
                                            <span class="fw-medium">Phone:</span> {{ $address->phone }}
                                        </div>
                                        <div class="mb-2">
                                            <span class="fw-medium">Address:</span>
                                            <span class="small text-muted">
                                                {{ $address->address_line1 }}<br>
                                                {{ $address->address_line2 }}
                                            </span>
                                        </div>
                                        <div class="mb-2">
                                            <span class="fw-medium">City:</span> {{ $address->city }}<br>
                                            <span class="fw-medium">State:</span> {{ $address->state }}<br>
                                            <span class="fw-medium">Country:</span> {{ $address->country }}<br>
                                            <span class="fw-medium">Postal Code:</span> {{ $address->postal_code }}<br>
                                            <span class="fw-medium">Landmark:</span> {{ $address->landmark }}
                                        </div>
                                        <div class="mt-auto d-flex gap-2">
                                            <button type="button" class="btn btn-outline-primary btn-sm flex-fill" data-bs-toggle="modal" data-bs-target="#addressModal" onclick="openEditAddressModal({{ $address->id }})">Edit</button>
                                            <a href="#" class="btn btn-outline-danger btn-sm flex-fill" onclick="deleteAddress({{ $address->id }})">Delete</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>

<!-- Add/Edit Address Modal -->
<div class="modal fade" id="addressModal" tabindex="-1" aria-labelledby="addressModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addressModalLabel">Add New Address</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="addressForm">
        <div class="modal-body">
          <input type="hidden" id="addressId">
          <input type="hidden" id="latitude" name="latitude">
          <input type="hidden" id="longitude" name="longitude">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Name</label>
              <input type="text" id="name" name="name" required class="form-control">
            </div>
            <div class="col-md-6">
              <label class="form-label">Phone</label>
              <input type="tel" id="phone" name="phone" required class="form-control">
            </div>
            <div class="col-md-6">
              <label class="form-label">Address Type</label>
              <select id="address_type" name="address_type" required class="form-select">
                <option value="home">Home</option>
                <option value="work">Work</option>
                <option value="other">Other</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">Address Line 1</label>
              <input type="text" id="address_line1" name="address_line1" required class="form-control">
            </div>
            <div class="col-md-6">
              <label class="form-label">Address Line 2</label>
              <input type="text" id="address_line2" name="address_line2" class="form-control">
            </div>
            <div class="col-md-6">
              <label class="form-label">City</label>
              <input type="text" id="city" name="city" required class="form-control">
            </div>
            <div class="col-md-6">
              <label class="form-label">State</label>
              <input type="text" id="state" name="state" required class="form-control">
            </div>
            <div class="col-md-6">
              <label class="form-label">Country</label>
              <input type="text" id="country" name="country" required class="form-control">
            </div>
            <div class="col-md-6">
              <label class="form-label">Postal Code</label>
              <input type="text" id="postal_code" name="postal_code" required class="form-control">
            </div>
            <div class="col-md-6">
              <label class="form-label">Landmark</label>
              <input type="text" id="landmark" name="landmark" class="form-control">
            </div>
            <div class="col-md-6 d-flex align-items-center">
              <div class="form-check mt-4">
                <input type="checkbox" id="is_default" name="is_default" class="form-check-input">
                <label for="is_default" class="form-check-label">Set as default address</label>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-outline-primary" onclick="fetchCurrentLocation()">Use Current Location</button>
          <button type="submit" class="btn btn-primary">Save</button>
        </div>
      </form>
    </div>
  </div>
</div>

<style>
#addressModal input.form-control, #addressModal select.form-select, #addressModal textarea.form-control {
    background-color: #fff !important;
    border: 1px solid #495057 !important;
    color: #212529 !important;
    box-shadow: none !important;
}
#addressModal input.form-control:disabled, #addressModal select.form-select:disabled, #addressModal textarea.form-control:disabled {
    background-color: #e9ecef !important;
}
</style>

<script>
function clearValidationErrors() {
  document.querySelectorAll('.invalid-feedback').forEach(el => el.remove());
  document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
}

function showValidationErrors(errors) {
  for (const [field, messages] of Object.entries(errors)) {
    const input = document.getElementById(field);
    if (input) {
      input.classList.add('is-invalid');
      const feedback = document.createElement('div');
      feedback.className = 'invalid-feedback';
      feedback.innerText = messages[0];
      input.parentNode.appendChild(feedback);
    }
  }
}

function openAddAddressModal() {
  document.getElementById('addressModalLabel').textContent = 'Add New Address';
  document.getElementById('addressForm').reset();
  document.getElementById('addressId').value = '';
  clearValidationErrors();
}
function openEditAddressModal(addressId) {
  document.getElementById('addressModalLabel').textContent = 'Edit Address';
  clearValidationErrors();
  fetch(`/addresses/${addressId}`, {
    method: 'GET',
    headers: {
      'Accept': 'application/json',
      'X-Requested-With': 'XMLHttpRequest',
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
    }
  })
    .then(response => response.json())
    .then(data => {
      const address = data.data.address;
      document.getElementById('addressId').value = addressId;
      document.getElementById('name').value = address.name;
      document.getElementById('phone').value = address.phone;
      document.getElementById('address_type').value = address.address_type;
      document.getElementById('address_line1').value = address.address_line1;
      document.getElementById('address_line2').value = address.address_line2 || '';
      document.getElementById('city').value = address.city;
      document.getElementById('state').value = address.state;
      document.getElementById('country').value = address.country;
      document.getElementById('postal_code').value = address.postal_code;
      document.getElementById('landmark').value = address.landmark || '';
      document.getElementById('is_default').checked = address.is_default;
      document.getElementById('latitude').value = address.latitude || '';
      document.getElementById('longitude').value = address.longitude || '';
    })
    .catch(error => {
      console.error('Error fetching address:', error);
      alert('Error loading address details. Please try again.');
    });
}

document.getElementById('addressForm').addEventListener('submit', function(e) {
  e.preventDefault();
  clearValidationErrors();
  const addressId = document.getElementById('addressId').value;
  const formData = new FormData(this);
  const data = Object.fromEntries(formData.entries());
  data.is_default = document.getElementById('is_default').checked;
  
  // Add CSRF token and method spoofing for PUT requests
  const headers = {
    'Content-Type': 'application/json',
    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
    'Accept': 'application/json',
    'X-Requested-With': 'XMLHttpRequest'
  };

  if (addressId) {
    // For PUT requests, we need to send the method as _method: 'PUT'
    data._method = 'PUT';
  }

  fetch(addressId ? `/addresses/${addressId}` : '/addresses', {
    method: 'POST', // Always use POST, Laravel will handle the method spoofing
    headers: headers,
    body: JSON.stringify(data)
  })
  .then(async response => {
    if (response.status === 422) {
      const errorData = await response.json();
      showValidationErrors(errorData.errors);
      throw new Error('Validation failed');
    }
    return response.json();
  })
  .then(data => {
    if (data.status === 'success') {
      var modal = bootstrap.Modal.getInstance(document.getElementById('addressModal'));
      modal.hide();
      location.reload();
    } else {
      alert('Error saving address. Please try again.');
    }
  })
  .catch(error => {
    if (error.message !== 'Validation failed') {
      console.error('Error saving address:', error);
      alert('Error saving address. Please try again.');
    }
  });
});

function fetchCurrentLocation() {
    if (!navigator.geolocation) {
        alert('Geolocation is not supported by your browser');
        return;
    }

    // Show loading state
    const useLocationBtn = document.querySelector('button[onclick="fetchCurrentLocation()"]');
    const originalBtnText = useLocationBtn.innerHTML;
    useLocationBtn.disabled = true;
    useLocationBtn.innerHTML = `
        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
        Fetching location...
    `;

    navigator.geolocation.getCurrentPosition(
        (position) => {
            const { latitude, longitude } = position.coords;
            
            // Store coordinates in hidden fields
            document.getElementById('latitude').value = latitude;
            document.getElementById('longitude').value = longitude;
            
            // Use Google Maps Geocoding API to get address details
            fetch(`https://maps.googleapis.com/maps/api/geocode/json?latlng=${latitude},${longitude}&key={{ config('services.google.maps_api_key') }}`)
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'OK') {
                        const addressComponents = data.results[0].address_components;
                        const formattedAddress = data.results[0].formatted_address;
                        
                        // Parse address components
                        let streetNumber = '';
                        let route = '';
                        let city = '';
                        let state = '';
                        let postalCode = '';
                        let country = '';
                        let landmark = '';
                        let sublocality = '';
                        let sublocalityLevel1 = '';
                        let sublocalityLevel2 = '';
                        let sublocalityLevel3 = '';
                        
                        addressComponents.forEach(component => {
                            if (component.types.includes('street_number')) {
                                streetNumber = component.long_name;
                            }
                            if (component.types.includes('route')) {
                                route = component.long_name;
                            }
                            if (component.types.includes('locality')) {
                                city = component.long_name;
                            }
                            if (component.types.includes('administrative_area_level_1')) {
                                state = component.long_name;
                            }
                            if (component.types.includes('postal_code')) {
                                postalCode = component.long_name;
                            }
                            if (component.types.includes('country')) {
                                country = component.long_name;
                            }
                            if (component.types.includes('point_of_interest')) {
                                landmark = component.long_name;
                            }
                            if (component.types.includes('sublocality_level_1')) {
                                sublocalityLevel1 = component.long_name;
                            }
                            if (component.types.includes('sublocality_level_2')) {
                                sublocalityLevel2 = component.long_name;
                            }
                            if (component.types.includes('sublocality_level_3')) {
                                sublocalityLevel3 = component.long_name;
                            }
                            if (component.types.includes('sublocality')) {
                                sublocality = component.long_name;
                            }
                        });

                        // Fill the form fields
                        document.getElementById('address_line1').value = `${streetNumber} ${route}`.trim();
                        
                        // Combine sublocality information for address line 2
                        const sublocalityParts = [
                            sublocalityLevel3,
                            sublocalityLevel2,
                            sublocalityLevel1,
                            sublocality
                        ].filter(Boolean); // Remove empty values
                        
                        document.getElementById('address_line2').value = sublocalityParts.join(', ');
                        document.getElementById('city').value = city;
                        document.getElementById('state').value = state;
                        document.getElementById('postal_code').value = postalCode;
                        document.getElementById('country').value = country;
                        if (landmark) {
                            document.getElementById('landmark').value = landmark;
                        }

                        // Show success message
                        const successAlert = document.createElement('div');
                        successAlert.className = 'alert alert-success mt-3';
                        successAlert.innerHTML = 'Location details fetched successfully!';
                        useLocationBtn.parentNode.insertBefore(successAlert, useLocationBtn.nextSibling);
                        setTimeout(() => successAlert.remove(), 3000);
                    } else {
                        throw new Error('Could not find address details for your location');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    const errorAlert = document.createElement('div');
                    errorAlert.className = 'alert alert-danger mt-3';
                    errorAlert.innerHTML = error.message || 'Error fetching address details';
                    useLocationBtn.parentNode.insertBefore(errorAlert, useLocationBtn.nextSibling);
                    setTimeout(() => errorAlert.remove(), 3000);
                })
                .finally(() => {
                    // Reset button state
                    useLocationBtn.disabled = false;
                    useLocationBtn.innerHTML = originalBtnText;
                });
        },
        (error) => {
            console.error('Error:', error);
            const errorAlert = document.createElement('div');
            errorAlert.className = 'alert alert-danger mt-3';
            errorAlert.innerHTML = 'Error getting your location. Please check your browser settings.';
            useLocationBtn.parentNode.insertBefore(errorAlert, useLocationBtn.nextSibling);
            setTimeout(() => errorAlert.remove(), 3000);
            
            // Reset button state
            useLocationBtn.disabled = false;
            useLocationBtn.innerHTML = originalBtnText;
        }
    );
}

function deleteAddress(addressId) {
  if (!confirm('Are you sure you want to delete this address?')) return;
  fetch(`/addresses/${addressId}`, {
    method: 'DELETE',
    headers: {
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
      'Accept': 'application/json',
    },
  })
  .then(response => response.json())
  .then(data => {
    if (data.status === 'success' || data.message === 'Address deleted successfully') {
      location.reload();
    } else {
      alert(data.message || 'Failed to delete address.');
    }
  })
  .catch(() => alert('Failed to delete address.'));
}
</script>

@push('styles')
<style>
#addressModal .form-control, #addressModal .form-select {
    background-color: #fff !important;
    border: 1px solid #ced4da !important;
    color: #212529;
}
#addressModal .form-control:disabled, #addressModal .form-select:disabled {
    background-color: #e9ecef !important;
}
</style>
@endpush
@endsection 