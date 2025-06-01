@extends('layouts.main')

@section('content')
<section class="py-5 bg-light">
    <div class="container">
        <div class="row">
            @include('layouts.partials.user-sidebar')
            
            <div class="col-md-9">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="display-4 mb-0">Address Details</h1>
                    <div>
                        <a href="{{ route('addresses.edit', $address) }}" class="btn btn-primary me-2">Edit Address</a>
                        <a href="{{ route('addresses.index') }}" class="btn btn-outline-secondary">Back to Addresses</a>
                    </div>
                </div>

                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-4">
                                    <h5 class="card-title d-flex justify-content-between align-items-center">
                                        <span>{{ $address->name }}</span>
                                        @if($address->is_default)
                                            <span class="badge bg-primary">Default Address</span>
                                        @endif
                                    </h5>
                                    <p class="text-muted mb-0">{{ ucfirst($address->address_type) }} Address</p>
                                </div>

                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label fw-medium">Phone Number</label>
                                            <p class="mb-0">{{ $address->phone }}</p>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label fw-medium">Address Type</label>
                                            <p class="mb-0">{{ ucfirst($address->address_type) }}</p>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="mb-3">
                                            <label class="form-label fw-medium">Address Line 1</label>
                                            <p class="mb-0">{{ $address->address_line1 }}</p>
                                        </div>
                                    </div>

                                    @if($address->address_line2)
                                    <div class="col-12">
                                        <div class="mb-3">
                                            <label class="form-label fw-medium">Address Line 2</label>
                                            <p class="mb-0">{{ $address->address_line2 }}</p>
                                        </div>
                                    </div>
                                    @endif

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label fw-medium">City</label>
                                            <p class="mb-0">{{ $address->city }}</p>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label fw-medium">State</label>
                                            <p class="mb-0">{{ $address->state }}</p>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label fw-medium">Country</label>
                                            <p class="mb-0">{{ $address->country }}</p>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label fw-medium">Postal Code</label>
                                            <p class="mb-0">{{ $address->postal_code }}</p>
                                        </div>
                                    </div>

                                    @if($address->landmark)
                                    <div class="col-12">
                                        <div class="mb-3">
                                            <label class="form-label fw-medium">Landmark</label>
                                            <p class="mb-0">{{ $address->landmark }}</p>
                                        </div>
                                    </div>
                                    @endif

                                    @if($address->latitude && $address->longitude)
                                    <div class="col-12">
                                        <div class="mb-3">
                                            <label class="form-label fw-medium">Location Coordinates</label>
                                            <p class="mb-0">
                                                Latitude: {{ $address->latitude }}<br>
                                                Longitude: {{ $address->longitude }}
                                            </p>
                                        </div>
                                    </div>
                                    @endif
                                </div>

                                <div class="mt-4">
                                    <form action="{{ route('addresses.destroy', $address) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this address?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger">Delete Address</button>
                                    </form>

                                    @if(!$address->is_default)
                                    <form action="{{ route('addresses.set-default', $address) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-primary">Set as Default</button>
                                    </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection 