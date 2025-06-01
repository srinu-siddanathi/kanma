@extends('layouts.main')

@section('content')
<div class="container py-5">
    <div class="row mb-4">
        <div class="col-md-4">
            <img src="{{ asset($shop->image_path ?? 'images/no-image.png') }}" 
                 class="img-fluid rounded" 
                 alt="{{ $shop->name }}"
                 style="width: 100%; height: 300px; object-fit: cover;">
        </div>
        <div class="col-md-8">
            <h1 class="mb-3">{{ $shop->name }}</h1>
            <p class="text-muted mb-4">{{ $shop->description }}</p>
            
            @if($shop->address)
            <div class="mb-3">
                <h5>Location</h5>
                <p class="text-muted mb-0">{{ $shop->address }}</p>
            </div>
            @endif
            
            @if($shop->phone)
            <div class="mb-3">
                <h5>Contact</h5>
                <p class="text-muted mb-0">{{ $shop->phone }}</p>
            </div>
            @endif
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-12">
            <h2 class="section-title">Products</h2>
        </div>
    </div>

    <div class="row g-4">
        @forelse($products as $product)
            @php $variant = $product->variants->first(); @endphp
            <div class="col-md-4 col-lg-3">
                <div class="card h-100 product-item">
                    <a href="{{ route('product.show', $product->id) }}">
                        <img src="{{ asset($product->image_url ?? 'images/no-image.png') }}" 
                             class="card-img-top" 
                             alt="{{ $product->name }}">
                    </a>
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title mb-2">
                            <a href="{{ route('product.show', $product->id) }}" 
                               class="text-decoration-none text-dark">
                                {{ $product->name }}
                            </a>
                        </h5>
                        <p class="card-text text-muted small">{{ Str::limit($product->description, 80) }}</p>
                        <div class="mt-auto d-flex align-items-center justify-content-between">
                            <span class="fw-bold">₹{{ number_format($product->price, 2) }}</span>
                            <div class="cart-action">
                                @if($variant)
                                    @php $inCart = isset($cart[$variant->id]); @endphp
                                    @if($inCart)
                                        @include('components.cart.quantity-control', [
                                            'variant' => $variant, 
                                            'max' => $variant->stock ?? 99, 
                                            'cart' => $cart
                                        ])
                                    @else
                                        <button class="btn btn-primary add-to-cart" 
                                                data-product-id="{{ $product->id }}" 
                                                data-variant-id="{{ $variant->id }}">
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
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info">
                    No products available in this shop at the moment.
                </div>
            </div>
        @endforelse
    </div>

    @if($products->hasPages())
    <div class="row mt-4">
        <div class="col-12">
            {{ $products->links() }}
        </div>
    </div>
    @endif
</div>

<style>
.section-title {
    font-size: 1.75rem;
    font-weight: 600;
    color: #333;
    position: relative;
    padding-bottom: 10px;
}

.section-title::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 60px;
    height: 3px;
    background-color: #ff3571;
}

.product-item {
    transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
    border: none;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.product-item:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
}

.product-item .card-img-top {
    height: 200px;
    object-fit: cover;
}
</style>
@endsection

@include('components.cart.add-to-cart-scripts') 