@extends('layouts.main')

@section('content')
<section class="py-5 bg-light">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <h1 class="display-4 mb-3">My Orders</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Order History</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-lg-3">
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <h5 class="card-title mb-4">My Account</h5>
                        <ul class="nav flex-column nav-pills">
                            <li class="nav-item mb-2">
                                <a href="{{ route('profile') }}" class="nav-link">
                                    <i class="bi bi-person me-2"></i>Profile
                                </a>
                            </li>
                            <li class="nav-item mb-2">
                                <a href="{{ route('orders') }}" class="nav-link active">
                                    <i class="bi bi-bag me-2"></i>Orders
                                </a>
                            </li>
                            <li class="nav-item mb-2">
                                <a href="#" class="nav-link">
                                    <i class="bi bi-heart me-2"></i>Wishlist
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                    <i class="bi bi-box-arrow-right me-2"></i>Logout
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Order History -->
            <div class="col-lg-9">
                <!-- Order Filters -->
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <div class="input-group">
                                    <input type="text" class="form-control" placeholder="Search orders...">
                                    <button class="btn btn-outline-secondary" type="button">
                                        <i class="bi bi-search"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <select class="form-select">
                                    <option value="">All Orders</option>
                                    <option value="processing">Processing</option>
                                    <option value="shipped">Shipped</option>
                                    <option value="delivered">Delivered</option>
                                    <option value="cancelled">Cancelled</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Orders List -->
                <div class="orders-list">
                    <!-- Order Item -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-md-3">
                                    <span class="text-muted">Order ID</span>
                                    <p class="mb-0">#ORD-2024-001</p>
                                </div>
                                <div class="col-md-2">
                                    <span class="text-muted">Date</span>
                                    <p class="mb-0">Jan 15, 2024</p>
                                </div>
                                <div class="col-md-2">
                                    <span class="text-muted">Total</span>
                                    <p class="mb-0">$89.99</p>
                                </div>
                                <div class="col-md-3">
                                    <span class="text-muted">Status</span>
                                    <p class="mb-0">
                                        <span class="badge bg-success">Delivered</span>
                                    </p>
                                </div>
                                <div class="col-md-2 text-end">
                                    <button class="btn btn-outline-primary btn-sm" type="button" data-bs-toggle="collapse" data-bs-target="#order1Details">
                                        View Details
                                    </button>
                                </div>
                            </div>

                            <!-- Order Details Collapse -->
                            <div class="collapse mt-4" id="order1Details">
                                <hr>
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6>Shipping Address</h6>
                                        <p class="mb-0">John Doe<br>
                                            123 Main St<br>
                                            New York, NY 10001<br>
                                            United States
                                        </p>
                                    </div>
                                    <div class="col-md-6">
                                        <h6>Payment Method</h6>
                                        <p class="mb-0">
                                            <i class="bi bi-credit-card me-2"></i>
                                            Visa ending in 4242
                                        </p>
                                    </div>
                                </div>
                                <hr>
                                <h6>Order Items</h6>
                                <div class="table-responsive">
                                    <table class="table table-borderless">
                                        <tbody>
                                            <tr>
                                                <td style="width: 80px">
                                                    <img src="{{ asset('images/product-thumb-1.png') }}" class="img-fluid" alt="Product">
                                                </td>
                                                <td>
                                                    <h6 class="mb-0">Fresh Organic Strawberry</h6>
                                                    <small class="text-muted">1 Unit</small>
                                                </td>
                                                <td class="text-end">$24.99</td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <img src="{{ asset('images/product-thumb-2.png') }}" class="img-fluid" alt="Product">
                                                </td>
                                                <td>
                                                    <h6 class="mb-0">Fresh Vegetables Bundle</h6>
                                                    <small class="text-muted">2 Units</small>
                                                </td>
                                                <td class="text-end">$65.00</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="text-end">
                                    <button class="btn btn-outline-secondary btn-sm me-2">Download Invoice</button>
                                    <button class="btn btn-primary btn-sm">Track Order</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Repeat Order Items for more orders -->
                </div>

                <!-- Pagination -->
                <nav>
                    <ul class="pagination justify-content-center">
                        <li class="page-item disabled">
                            <a class="page-link" href="#" tabindex="-1">Previous</a>
                        </li>
                        <li class="page-item active"><a class="page-link" href="#">1</a></li>
                        <li class="page-item"><a class="page-link" href="#">2</a></li>
                        <li class="page-item"><a class="page-link" href="#">3</a></li>
                        <li class="page-item">
                            <a class="page-link" href="#">Next</a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
    // Add any page-specific scripts here
    document.querySelectorAll('[data-bs-toggle="collapse"]').forEach(button => {
        button.addEventListener('click', function() {
            const icon = this.querySelector('i');
            if (icon) {
                icon.classList.toggle('bi-chevron-down');
                icon.classList.toggle('bi-chevron-up');
            }
        });
    });
</script>
@endpush 