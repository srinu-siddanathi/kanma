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
                <!-- Featured Products Section -->
                @if($featuredProducts && $featuredProducts->count() > 0)
                <div class="mb-5">
                    <h3 class="h5 mb-4">Featured Products</h3>
                    <div class="featured-products-carousel position-relative">
                        <div class="swiper featured-products-swiper">
                            <div class="swiper-wrapper">
                                @foreach($featuredProducts as $index => $product)
                                <div class="swiper-slide">
                                    <div class="product-card h-100" 
                                         style="background: linear-gradient(135deg, 
                                            {{ $index % 3 == 0 ? '#e6f3ff, #f0f5ff' : 
                                               ($index % 3 == 1 ? '#e8f8f5, #f0f9ff' : '#fff8e6, #fffbf0') }}
                                         );">
                                        <div class="product-content p-4">
                                            <div class="product-image mb-4">
                                                <img src="{{ $product->image_url }}" 
                                                     alt="{{ $product->name }}" 
                                                     class="img-fluid rounded"
                                                     style="width: 100%; height: 200px; object-fit: cover;"
                                                     onerror="this.onerror=null;this.src='{{ asset('images/no-image.png') }}';">
                                            </div>
                                            <h4 class="product-title mb-3">{{ $product->name }}</h4>
                                            @php
                                                $variant = $product->variants->first();
                                                $price = $variant?->price ?? 0;
                                                $hasDiscount = $variant && $variant->discount_percentage > 0;
                                                $discountPrice = $hasDiscount 
                                                    ? $price - ($price * $variant->discount_percentage / 100) 
                                                    : $price;
                                            @endphp
                                            <div class="product-price mb-4">
                                                <span class="price">₹{{ number_format($discountPrice, 2) }}</span>
                                                @if($hasDiscount)
                                                <span class="original-price ms-2">₹{{ number_format($price, 2) }}</span>
                                                @endif
                                            </div>
                                            <div class="d-flex align-items-center">
                                                <a href="{{ route('product.show', $product->id) }}" 
                                                   class="btn btn-dark rounded-pill px-4 flex-grow-1">
                                                    View Product
                                                </a>
                                                <button class="btn btn-outline-dark rounded-circle ms-3 add-to-cart-btn"
                                                        data-product-id="{{ $product->id }}"
                                                        data-variant-id="{{ $variant?->id }}">
                                                    <i class="bi bi-cart-plus"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        
                        
                    </div>
                </div>
                @else
                <div class="mb-4">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        No featured products available at the moment.
                    </div>
                </div>
                @endif

                <!-- Products -->
                <div class="row" id="productsContainer">
                    @forelse($products as $product)
                    <div class="col-md-4 mb-4 product-item">
                        <div class="card product-card border-0 shadow-sm h-100">
                            <div class="card-image position-relative">
                                <a href="{{ route('product.show', $product->id) }}" class="btn-wishlist position-absolute top-0 end-0 m-2" style="width:28px; height:28px;">
                                    <svg width="18" height="18">
                                        <use xlink:href="#heart"></use>
                                    </svg>
                                </a>
                                @php
                                    $variant = $product->variants->first();
                                    $hasDiscount = $variant && $variant->discount_percentage > 0;
                                @endphp
                                @if($hasDiscount)
                                <div class="badge bg-success position-absolute m-3">-{{ $variant->discount_percentage }}%</div>
                                @endif
                                <a href="{{ route('product.show', $product->id) }}">
                                    <img src="{{ $product->image_url }}" class="card-img-top product-image-fit" alt="{{ $product->name }}"
                                         style="height: 200px; object-fit: contain; background: #fff;"
                                         onerror="this.onerror=null;this.src='{{ asset('images/no-image.png') }}';">
                                </a>
                            </div>
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title mb-2">
                                    <a href="{{ route('product.show', $product->id) }}" class="text-decoration-none text-dark">
                                        {{ $product->name }}
                                    </a>
                                </h5>
                                {!! '<span class="text-muted small mb-2">' . ($variant ? ($variant->quantity . ' ' . $variant->unit) : '&nbsp;') . '</span>' !!}
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
                                <div class="cart-action mt-3">
                                    @if($variant)
                                        @php $inCart = isset($cart[$variant->id]); @endphp
                                        @if($inCart)
                                            @include('components.cart.quantity-control', ['max' => $variant->stock ?? 99])
                                        @else
                                            <button class="btn btn-primary add-to-cart" data-product-id="{{ $product->id }}" data-variant-id="{{ $variant->id }}">
                                                <svg width="18" height="18" class="me-2">
                                                    <use xlink:href="#cart"></use>
                                                </svg>
                                                Add to Cart
                                            </button>
                                        @endif
                                    @else
                                        <button class="btn btn-secondary" disabled>
                                            <svg width="18" height="18" class="me-2">
                                                <use xlink:href="#cart"></use>
                                            </svg>
                                            Not Available
                                        </button>
                                    @endif
                                </div>
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
                                <a class="page-link" href="{{ $products->appends(request()->query())->previousPageUrl() }}" rel="prev">
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
                                    <a class="page-link" href="{{ $products->appends(request()->query())->url($page) }}">{{ $page }}</a>
                                </li>
                            @endif
                        @endforeach

                        {{-- Next Page Link --}}
                        @if($products->hasMorePages())
                            <li class="page-item">
                                <a class="page-link" href="{{ $products->appends(request()->query())->nextPageUrl() }}" rel="next">
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
    background: #fff;
    border-radius: 1rem;
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    height: 100%;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    overflow: hidden;
}

.promo-banner-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 16px rgba(0,0,0,0.1);
}

.promo-banner-card h4 {
    color: #2d3436;
    margin-bottom: 0.75rem;
    line-height: 1.3;
}

.promo-banner-card .text-warning {
    color: #ffc107 !important;
}

.btn-outline-warning {
    color: #ffc107;
    border-color: #ffc107;
}

.btn-outline-warning:hover {
    color: #000;
    background-color: #ffc107;
    border-color: #ffc107;
}

.btn-dark {
    background-color: #2d3436;
    border: none;
}

.btn-dark:hover {
    background-color: #1e2527;
}

/* Featured Products Styles */
.featured-products-carousel {
    position: relative;
    padding: 0 50px;
}

.featured-products-swiper {
    overflow: hidden;
}

.swiper-slide {
    height: auto;
}

.product-card {
    border-radius: 20px;
    overflow: hidden;
    transition: all 0.3s ease;
    height: 100%;
    margin: 0 10px;
}

.product-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.1);
}

.product-title {
    font-size: 1.25rem;
    font-weight: 600;
    color: #2d3436;
    line-height: 1.4;
    height: 2.8em;
    overflow: hidden;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
}

.product-price {
    display: flex;
    align-items: center;
}

.price {
    font-size: 1.5rem;
    font-weight: 700;
    color: #2d3436;
}

.original-price {
    font-size: 1rem;
    color: #a0a0a0;
    text-decoration: line-through;
}

.btn-dark {
    background: #2d3436;
    border: none;
    padding: 0.75rem 1.5rem;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn-dark:hover {
    background: #1e2527;
    transform: translateY(-2px);
}

.add-to-cart-btn {
    width: 40px;
    height: 40px;
    padding: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
}

.add-to-cart-btn:hover {
    background: #2d3436;
    color: white;
    transform: translateY(-2px);
}

/* Swiper Navigation Styles */
.featured-swiper-next,
.featured-swiper-prev {
    width: 40px;
    height: 40px;
    background: #ffc107;
    border-radius: 50%;
    color: #000;
    font-size: 18px;
    transition: all 0.3s ease;
}

.featured-swiper-next:hover,
.featured-swiper-prev:hover {
    background: #ffca2c;
    transform: scale(1.1);
}

.featured-swiper-next::after,
.featured-swiper-prev::after {
    font-size: 16px;
    font-weight: bold;
}

/* Swiper Pagination Styles */
.featured-swiper-pagination {
    position: relative;
    margin-top: 20px;
}

.featured-swiper-pagination .swiper-pagination-bullet {
    width: 10px;
    height: 10px;
    background: #dee2e6;
    opacity: 1;
    transition: all 0.3s ease;
}

.featured-swiper-pagination .swiper-pagination-bullet-active {
    background: #ffc107;
    transform: scale(1.2);
}

@media (max-width: 768px) {
    .featured-products-carousel {
        padding: 0 30px;
    }
    
    .product-title {
        font-size: 1.1rem;
    }
    
    .price {
        font-size: 1.25rem;
    }
    
    .btn-dark {
        padding: 0.5rem 1rem;
        font-size: 0.9rem;
    }
    
    .featured-swiper-next,
    .featured-swiper-prev {
        width: 35px;
        height: 35px;
    }
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Featured Products Swiper
    const featuredSwiper = new Swiper('.featured-products-swiper', {
        slidesPerView: 1,
        spaceBetween: 20,
        loop: true,
        autoplay: {
            delay: 3000,
            disableOnInteraction: false,
        },
        pagination: {
            el: '.featured-swiper-pagination',
            clickable: true,
        },
        navigation: {
            nextEl: '.featured-swiper-next',
            prevEl: '.featured-swiper-prev',
        },
        breakpoints: {
            768: {
                slidesPerView: 2,
                spaceBetween: 20,
            },
            992: {
                slidesPerView: 3,
                spaceBetween: 30,
            }
        },
        on: {
            init: function() {
                console.log('Featured products swiper initialized');
            }
        }
    });
    
    // Add to cart functionality
    document.addEventListener('click', function(e) {
        if (e.target.closest('.add-to-cart-btn')) {
            const btn = e.target.closest('.add-to-cart-btn');
            const productId = btn.dataset.productId;
            const variantId = btn.dataset.variantId;
            
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
        }
    });
    
    // Pause autoplay on hover
    const swiperContainer = document.querySelector('.featured-products-swiper');
    if (swiperContainer) {
        swiperContainer.addEventListener('mouseenter', function() {
            featuredSwiper.autoplay.stop();
        });
        
        swiperContainer.addEventListener('mouseleave', function() {
            featuredSwiper.autoplay.start();
        });
    }
});
</script>
@endpush