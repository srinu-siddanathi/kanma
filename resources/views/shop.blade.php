@extends('layouts.main')

@section('content')
<!-- Shop Banner -->
<section class="py-4 bg-light">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h1 class="h3 mb-0">Products</h1>
            </div>
            <div class="col-md-6 text-end">
                @if($products instanceof \Illuminate\Pagination\LengthAwarePaginator)
                    <span class="text-muted">Showing {{ $products->firstItem() }}-{{ $products->lastItem() }} of {{ $products->total() }} results</span>
                @else
                    <span class="text-muted">Showing {{ $products->count() }} results</span>
                @endif
            </div>
        </div>
    </div>
</section>

<!-- Shop Content -->
<section class="py-4">
    <div class="container">
        @if(isset($error))
        <div class="alert alert-danger">
            {{ $error }}
        </div>
        @endif

        <div class="row">
            <!-- Sidebar Filters -->
            <div class="col-lg-3">
                <div class="shop-sidebar">
                    <!-- Categories Filter -->
                    <div class="sidebar-widget mb-4">
                        <h3 class="widget-title h6 mb-3">Categories</h3>
                        <div class="widget-content">
                            <ul class="list-unstyled">
                                <li class="mb-2">
                                    <a href="{{ route('shop') }}" 
                                       class="text-decoration-none d-flex align-items-center {{ !request('category') ? 'text-primary fw-bold' : '' }}">
                                        <i class="bi bi-grid me-2"></i>
                                        <span>All</span>
                                        <span class="badge bg-light text-dark ms-auto">{{ $totalProductCount }}</span>
                                    </a>
                                </li>
                                @forelse($categories as $category)
                                <li class="mb-2">
                                    <a href="{{ route('shop', ['category' => $category->slug]) }}" 
                                       class="text-decoration-none d-flex align-items-center {{ request('category') == $category->slug ? 'text-primary fw-bold' : '' }}">
                                        <i class="bi bi-{{ $category->icon ?? 'tag' }} me-2"></i>
                                        <span>{{ $category->name }}</span>
                                        <span class="badge bg-light text-dark ms-auto">{{ $category->products_count }}</span>
                                    </a>
                                </li>
                                @empty
                                <li class="text-muted">No categories found</li>
                                @endforelse
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Products Grid -->
            <div class="col-lg-9">
                <!-- Featured Banners Section -->
                <div class="mb-4">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="promo-banner-card p-4 d-flex flex-column justify-content-between h-100 rounded-4" style="background: linear-gradient(90deg, #b3e0ff 0%, #e0e7ff 100%); min-height: 180px;">
                                <div>
                                    <h4 class="fw-bold mb-2" style="font-size: 1.3rem;">New launches of the season!</h4>
                                    <div class="mb-3 text-muted" style="font-size: 1rem;">UP TO 30% OFF</div>
                                    <a href="#" class="btn btn-dark px-4 py-2 rounded-pill fw-semibold">Order now</a>
                                </div>
                                <img src="https://images.unsplash.com/photo-1504674900247-0877df9cc836?auto=format&fit=cover&w=200&q=80" alt="Banner 1" class="mt-3 align-self-end rounded-3" style="width: 100px; height: 80px; object-fit: cover;">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="promo-banner-card p-4 d-flex flex-column justify-content-between h-100 rounded-4" style="background: linear-gradient(90deg, #e0e7ff 0%, #f0fdfa 100%); min-height: 180px;">
                                <div>
                                    <h4 class="fw-bold mb-2" style="font-size: 1.3rem;">Build your own<br>Home garden</h4>
                                    <div class="mb-3 text-muted" style="font-size: 1rem;">zepto bloom</div>
                                    <a href="#" class="btn btn-dark px-4 py-2 rounded-pill fw-semibold">Order now</a>
                                </div>
                                <img src="https://images.unsplash.com/photo-1464983953574-0892a716854b?auto=format&fit=cover&w=200&q=80" alt="Banner 2" class="mt-3 align-self-end rounded-3" style="width: 100px; height: 80px; object-fit: cover;">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="promo-banner-card p-4 d-flex flex-column justify-content-between h-100 rounded-4" style="background: linear-gradient(90deg, #ffe9b3 0%, #fffbe0 100%); min-height: 180px;">
                                <div>
                                    <h4 class="fw-bold mb-2" style="font-size: 1.3rem;">SEASON OF THE KING</h4>
                                    <div class="mb-3 text-muted" style="font-size: 1rem;">Naturally ripened, from handpicked farms</div>
                                    <a href="#" class="btn btn-success px-4 py-2 rounded-pill fw-semibold">Explore now</a>
                                </div>
                                <img src="https://images.unsplash.com/photo-1502741338009-cac2772e18bc?auto=format&fit=cover&w=200&q=80" alt="Banner 3" class="mt-3 align-self-end rounded-3" style="width: 100px; height: 80px; object-fit: cover;">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Products -->
                <div class="row" id="productsContainer">
                    @forelse($products as $product)
                    <div class="col-md-4 mb-4 product-item">
                        <div class="card product-card border-0 shadow-sm h-100">
                            <div class="card-image position-relative">
                                @php
                                    $variant = $product->variants->first();
                                    $hasDiscount = $variant && $variant->discount_percentage > 0;
                                    $imagePath = $product->image_path ? public_path($product->image_path) : null;
                                    $imageExists = $imagePath && file_exists($imagePath);
                                @endphp
                                @if($hasDiscount)
                                <div class="badge bg-success position-absolute m-3">-{{ $variant->discount_percentage }}%</div>
                                @endif
                                <a href="{{ route('product.show', $product->id) }}" class="btn-wishlist position-absolute end-0 m-3">
                                    <svg width="24" height="24">
                                        <use xlink:href="#heart"></use>
                                    </svg>
                                </a>
                                <img src="{{ $imageExists ? asset($product->image_path) : 'https://placehold.co/400x400/e2e8f0/1e293b?text=No+Image' }}" 
                                     class="card-img-top" alt="{{ $product->name }}"
                                     style="height: 200px; object-fit: cover;">
                            </div>
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title mb-2">
                                    <a href="{{ route('product.show', $product->id) }}" class="text-decoration-none text-dark">
                                        {{ $product->name }}
                                    </a>
                                </h5>
                                <span class="text-muted small mb-2">{{ $variant?->unit ?? 'Unit' }}</span>
                                <div class="rating mb-2">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <svg class="{{ $i <= ($product->rating ?? 0) ? 'star-solid' : 'star-outline' }}" width="16" height="16">
                                            <use xlink:href="#{{ $i <= ($product->rating ?? 0) ? 'star-solid' : 'star-outline' }}"></use>
                                        </svg>
                                    @endfor
                                </div>
                                <div class="price-box mt-auto">
                                    @php
                                        $price = $variant?->price ?? 0;
                                        $discountPrice = $hasDiscount 
                                            ? $price - ($price * $variant->discount_percentage / 100) 
                                            : $price;
                                    @endphp
                                    <span class="text-primary h5">₹{{ number_format($discountPrice, 2) }}</span>
                                    @if($hasDiscount)
                                    <span class="text-muted text-decoration-line-through ms-2">
                                        ₹{{ number_format($price, 2) }}
                                    </span>
                                    @endif
                                </div>
                                @if($variant)
                                <button class="btn btn-primary mt-3 add-to-cart" data-product-id="{{ $product->id }}" data-variant-id="{{ $variant->id }}">
                                    <svg width="18" height="18" class="me-2">
                                        <use xlink:href="#cart"></use>
                                    </svg>
                                    Add to Cart
                                </button>
                                @else
                                <button class="btn btn-secondary mt-3" disabled>
                                    <svg width="18" height="18" class="me-2">
                                        <use xlink:href="#cart"></use>
                                    </svg>
                                    Not Available
                                </button>
                                @endif
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="col-12">
                        <div class="alert alert-info">
                            No products found.
                        </div>
                    </div>
                    @endforelse
                </div>

                <!-- Pagination -->
                @if($products->hasPages())
                <nav class="mt-4">
                    <ul class="pagination justify-content-center">
                        {{-- Previous Page Link --}}
                        @if($products->onFirstPage())
                            <li class="page-item disabled">
                                <span class="page-link">
                                    <i class="bi bi-chevron-left"></i>
                                </span>
                            </li>
                        @else
                            <li class="page-item">
                                <a class="page-link" href="{{ $products->previousPageUrl() }}" rel="prev">
                                    <i class="bi bi-chevron-left"></i>
                                </a>
                            </li>
                        @endif

                        {{-- Pagination Elements --}}
                        @foreach($products->getUrlRange(1, $products->lastPage()) as $page => $url)
                            @if($page == $products->currentPage())
                                <li class="page-item active">
                                    <span class="page-link">{{ $page }}</span>
                                </li>
                            @else
                                <li class="page-item">
                                    <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                                </li>
                            @endif
                        @endforeach

                        {{-- Next Page Link --}}
                        @if($products->hasMorePages())
                            <li class="page-item">
                                <a class="page-link" href="{{ $products->nextPageUrl() }}" rel="next">
                                    <i class="bi bi-chevron-right"></i>
                                </a>
                            </li>
                        @else
                            <li class="page-item disabled">
                                <span class="page-link">
                                    <i class="bi bi-chevron-right"></i>
                                </span>
                            </li>
                        @endif
                    </ul>
                </nav>
                @endif
            </div>
        </div>
    </div>
</section>

@include('components.home.newsletter')
@endsection

@push('styles')
<style>
/* General Styles */
.shop-sidebar {
    background: #fff;
    padding: 1.5rem;
    border-radius: 0.5rem;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

.widget-title {
    color: #1a1a1a;
    font-weight: 600;
}

/* Category List Styles */
.sidebar-widget .list-unstyled li a {
    padding: 0.5rem 0;
    color: #6c757d;
    transition: all 0.2s ease-in-out;
}

.sidebar-widget .list-unstyled li a:hover {
    color: #0d6efd;
}

.sidebar-widget .list-unstyled li a i {
    font-size: 1.1rem;
    width: 20px;
    text-align: center;
}

.sidebar-widget .list-unstyled li a .badge {
    font-size: 0.75rem;
    padding: 0.25rem 0.5rem;
}

/* Product Card Styles */
.product-card {
    transition: transform 0.2s ease-in-out;
}

.product-card:hover {
    transform: translateY(-5px);
}

.card-image {
    overflow: hidden;
}

.card-image img {
    transition: transform 0.3s ease-in-out;
}

.product-card:hover .card-image img {
    transform: scale(1.05);
}

.btn-wishlist {
    background: rgba(255, 255, 255, 0.9);
    border-radius: 50%;
    width: 36px;
    height: 36px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease-in-out;
}

.btn-wishlist:hover {
    background: #fff;
    transform: scale(1.1);
}

/* Pagination Styles */
.pagination {
    margin-bottom: 0;
}

.pagination .page-link {
    color: #6c757d;
    border: 1px solid #dee2e6;
    padding: 0.5rem 0.75rem;
    font-size: 0.875rem;
    line-height: 1.25;
    transition: all 0.2s ease-in-out;
}

.pagination .page-link:hover {
    color: #0d6efd;
    background-color: #e9ecef;
    border-color: #dee2e6;
}

.pagination .page-item.active .page-link {
    background-color: #0d6efd;
    border-color: #0d6efd;
    color: white;
}

.pagination .page-item.disabled .page-link {
    color: #6c757d;
    pointer-events: none;
    background-color: #fff;
    border-color: #dee2e6;
}

.pagination .bi {
    font-size: 0.875rem;
    line-height: 1;
    vertical-align: middle;
}

.promo-banner-card {
    box-shadow: 0 2px 16px 0 rgba(0,0,0,0.07);
    transition: box-shadow 0.2s;
}
.promo-banner-card:hover {
    box-shadow: 0 4px 24px 0 rgba(0,0,0,0.12);
}
</style>
@endpush

@push('scripts')
<script>
// Add to cart
document.querySelectorAll('.add-to-cart').forEach(button => {
    button.addEventListener('click', function() {
        const productId = this.dataset.productId;
        const variantId = this.dataset.variantId;
        
        if (!productId || !variantId) {
            alert('Product or variant not available');
            return;
        }

        fetch('{{ route("cart.add") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                product_id: productId,
                variant_id: variantId,
                quantity: 1
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show success message
                alert('Product added to cart successfully!');
            } else {
                // Show error message
                alert(data.message || 'Failed to add product to cart');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while adding the product to cart');
        });
    });
});
</script>
@endpush