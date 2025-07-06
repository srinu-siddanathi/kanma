@extends('layouts.main')

@section('content')
<section class="py-5 bg-light">
    <div class="container">
        <div class="row">
            @include('layouts.partials.user-sidebar')
            
            <div class="col-md-9">
                <h1 class="display-4 mb-4">My Profile</h1>
                
                @if (session('status') === 'profile-updated')
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        Profile updated successfully!
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="card shadow-sm">
                    <div class="card-body">
                        <form method="POST" action="{{ route('profile.update') }}" class="row g-3">
                            @csrf
                            @method('patch')

                            <div class="col-md-6">
                                <label for="name" class="form-label">Name</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                    id="name" name="name" value="{{ old('name', $user->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                    id="email" name="email" value="{{ old('email', $user->email) }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="phone" class="form-label">Phone</label>
                                <input type="tel" class="form-control @error('phone') is-invalid @enderror" 
                                    id="phone" name="phone" value="{{ old('phone', $user->phone) }}">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">Update Profile</button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card shadow-sm mt-4">
                    <div class="card-body">
                        <h5 class="card-title mb-4">Update Password</h5>
                        
                        @if (session('status') === 'password-updated')
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                Password updated successfully!
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('password.update') }}" class="row g-3">
                            @csrf
                            @method('put')

                            <div class="col-md-6">
                                <label for="current_password" class="form-label">Current Password</label>
                                <input type="password" class="form-control @error('current_password') is-invalid @enderror" 
                                    id="current_password" name="current_password" required>
                                @error('current_password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="password" class="form-label">New Password</label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                    id="password" name="password" required>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="password_confirmation" class="form-label">Confirm New Password</label>
                                <input type="password" class="form-control" 
                                    id="password_confirmation" name="password_confirmation" required>
                            </div>

                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">Update Password</button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card shadow-sm mt-4">
                    <div class="card-body">
                        <h5 class="card-title mb-4 text-danger">Delete Account</h5>
                        <p class="text-muted">Once your account is deleted, all of its resources and data will be permanently deleted.</p>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="text-dark mb-3">Secure Delete (Recommended)</h6>
                                <p class="text-muted small mb-3">Requires password confirmation for additional security.</p>
                                <form method="POST" action="{{ route('profile.destroy') }}" class="mb-3">
                                    @csrf
                                    @method('delete')
                                    <div class="mb-3">
                                        <label for="delete_password" class="form-label">Current Password</label>
                                        <input type="password" class="form-control @error('delete_password', 'userDeletion') is-invalid @enderror" 
                                            id="delete_password" name="password" required>
                                        @error('delete_password', 'userDeletion')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete your account? This action cannot be undone.')">
                                        Delete Account (Secure)
                                    </button>
                                </form>
                            </div>
                            
                            <div class="col-md-6">
                                <h6 class="text-dark mb-3">Quick Delete</h6>
                                <p class="text-muted small mb-3">For Play Store compliance - no password required.</p>
                                <form method="POST" action="{{ route('profile.delete-account') }}" class="mb-3">
                                    @csrf
                                    @method('delete')
                                    <button type="submit" class="btn btn-outline-danger" onclick="return confirm('Are you sure you want to delete your account? This action cannot be undone.')">
                                        Delete Account (Quick)
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
@endsection 