<div class="col-md-3">
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="list-group list-group-flush">
                <a href="{{ route('profile.edit') }}" class="list-group-item list-group-item-action {{ request()->routeIs('profile.edit') ? 'active' : '' }}">
                    <i class="bi bi-person-circle me-2"></i> My Profile
                </a>
                <a href="{{ route('orders') }}" class="list-group-item list-group-item-action {{ request()->routeIs('orders*') ? 'active' : '' }}">
                    <i class="bi bi-bag me-2"></i> My Orders
                </a>
                <a href="{{ route('addresses.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('addresses*') ? 'active' : '' }}">
                    <i class="bi bi-geo-alt me-2"></i> Manage Addresses
                </a>
                <form method="POST" action="{{ route('logout') }}" class="list-group-item list-group-item-action text-danger">
                    @csrf
                    <button type="submit" class="btn btn-link text-danger p-0 border-0 bg-transparent">
                        <i class="bi bi-box-arrow-right me-2"></i> Logout
                    </button>
                </form>
            </div>
        </div>
    </div>
</div> 