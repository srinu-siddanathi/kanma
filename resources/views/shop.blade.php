@extends('layouts.main')

@section('content')
<!-- Shop Banner -->
<section class="py-3 bg-light">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h1 class="display-4 mb-3">Shop</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Shop</li>
                    </ol>
                </nav>
            </div>
            <div class="col-md-6 text-end">
                <span class="text-muted">Showing 1-12 of 36 results</span>
            </div>
        </div>
    </div>
</section>

<!-- Shop Content -->
<section class="py-5">
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar Filters -->
            <div class="col-lg-3">
                <div class="shop-sidebar">
                    <!-- Categories Filter -->
                    <div class="sidebar-widget mb-4">
                        <h3 class="widget-title h5 mb-3">Categories</h3>
                        <div class="widget-content">
                            <ul class="list-unstyled">
                                <li class="mb-2">
                                    <a href="#" class="text-decoration-none d-flex justify-content-between">
                                        <span>Vegetables</span>
                                        <span class="badge bg-light text-dark">15</span>
                                    </a>
                                </li>
                                <li class="mb-2">
                                    <a href="#" class="text-decoration-none d-flex justify-content-between">
                                        <span>Fruits</span>
                                        <span class="badge bg-light text-dark">12</span>
                                    </a>
                                </li>
                                <li class="mb-2">
                                    <a href="#" class="text-decoration-none d-flex justify-content-between">
                                        <span>Meat</span>
                                        <span class="badge bg-light text-dark">8</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Price Filter -->
                    <div class="sidebar-widget mb-4">
                        <h3 class="widget-title h5 mb-3">Price Range</h3>
                        <div class="widget-content">
                            <div class="range-slider">
                                <input type="range" class="form-range" min="0" max="100" id="priceRange">
                            </div>
                            <div class="d-flex justify-content-between mt-2">
                                <span>$0</span>
                                <span>$100</span>
                            </div>
                        </div>
                    </div>

                    <!-- Tags Filter -->
                    <div class="sidebar-widget mb-4">
                        <h3 class="widget-title h5 mb-3">Product Tags</h3>
                        <div class="widget-content">
                            <div class="d-flex flex-wrap gap-2">
                                <a href="#" class="btn btn-sm btn-outline-secondary">Organic</a>
                                <a href="#" class="btn btn-sm btn-outline-secondary">Fresh</a>
                                <a href="#" class="btn btn-sm btn-outline-secondary">Local</a>
                                <a href="#" class="btn btn-sm btn-outline-secondary">Healthy</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Products Grid -->
            <div class="col-lg-9">
                <!-- Sorting Options -->
                <div class="shop-toolbar mb-4">
                    <div class="row align-items-center">
                        <div class="col-md-4">
                            <div class="view-mode">
                                <button class="btn btn-outline-secondary active" data-view="-fill">
                                    <i class="bi bi-grid-fill"></i>
                                </button>
                                <button class="btn btn-outline-secondary" data-view="list">
                                    <i class="bi bi-list"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-8 text-end">
                            <select class="form-select w-auto d-inline-block">
                                <option>Default sorting</option>
                                <option>Sort by popularity</option>
                                <option>Sort by latest</option>
                                <option>Sort by price: low to high</option>
                                <option>Sort by price: high to low</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Products -->
                <div class="row">
                    @for ($i = 1; $i <= 9; $i++) <div class="col-md-4 mb-4">
                        <div class="card product-card border-0 shadow-sm">
                            <div class="card-image position-relative">
                                @if($i % 3 == 0)
                                <div class="badge bg-success position-absolute m-3">-15%</div>
                                @endif
                                <a href="{{ route('product.show', 1) }}"
                                    class="btn-wishlist position-absolute end-0 m-3">
                                    <svg width="24" height="24">
                                        <use xlink:href="#heart"></use>
                                    </svg>
                                </a>
                                <img src="{{ asset('images/product-thumb-' . ($i % 3 + 1) . '.png') }}"
                                    class="card-img-top"  onerror="this.src='{{asset('images/product-thumb-2.png')}}'" alt="Product">
                            </div>
                            <div class="card-body text-center">
                                <h5 class="card-title mb-0">
                                    <a href="{{ route('product.show', 1) }}" class="text-decoration-none text-dark">
                                        Fresh Organic Product {{ $i }}
                                    </a>
                                </h5>
                                <span class="text-muted d-block mb-2">1 Unit</span>
                                <div class="rating mb-2">
                                    @for ($j = 1; $j <= 5; $j++) <svg
                                        class="{{ $j <= 4 ? 'star-solid' : 'star-outline' }}" width="16" height="16">
                                        <use xlink:href="#{{ $j <= 4 ? 'star-solid' : 'star-outline' }}"></use>
                                        </svg>
                                        @endfor
                                </div>
                                <div class="price-box">
                                    <span class="text-primary h5">${{ number_format(rand(10, 50), 2) }}</span>
                                    @if($i % 3 == 0)
                                    <span class="text-muted text-decoration-line-through ms-2">
                                        ${{ number_format(rand(51, 100), 2) }}
                                    </span>
                                    @endif
                                </div>
                                <button class="btn btn-primary mt-3">
                                    <svg width="18" height="18" class="me-2">
                                        <use xlink:href="#cart"></use>
                                    </svg>
                                    Add to Cart
                                </button>
                            </div>
                        </div>
                </div>
                @endfor
            </div>

            <!-- Pagination -->
            <nav class="mt-4">
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

@include('components.home.newsletter')
@endsection

@push('scripts')
<script>
// Grid/List view toggle
document.querySelectorAll('[data-view]').forEach(button => {
    button.addEventListener('click', function() {
        document.querySelectorAll('[data-view]').forEach(btn => btn.classList.remove('active'));
        this.classList.add('active');
        // Add logic for changing view
    });
});
</script>
@endpush