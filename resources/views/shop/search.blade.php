@extends('layouts.main')

@section('content')
<div class="container py-5">
    <h2 class="mb-4">Search Results for: <span class="text-primary">{{ $query }}</span></h2>
    @if($products && $products->count())
        <div class="row g-4">
            @foreach($products as $product)
                @php $variant = $product->variants->first(); @endphp
                <div class="col-md-4 col-lg-3">
                    <div class="card h-100 product-item">
                        <a href="{{ route('product.show', $product->id) }}">
                            <img src="{{ asset($product->image_url ?? 'images/no-image.png') }}" class="card-img-top" alt="{{ $product->name }}">
                        </a>
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title mb-2">
                                <a href="{{ route('product.show', $product->id) }}" class="text-decoration-none text-dark">
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
                                            @include('components.cart.quantity-control', ['variant' => $variant, 'max' => $variant->stock ?? 99, 'cart' => $cart])
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
                </div>
            @endforeach
        </div>
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
                        <a class="page-link" href="{{ $products->appends(['q' => $query])->previousPageUrl() }}" rel="prev">
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
                            <a class="page-link" href="{{ $url }}&q={{ urlencode($query) }}">{{ $page }}</a>
                        </li>
                    @endif
                @endforeach

                {{-- Next Page Link --}}
                @if($products->hasMorePages())
                    <li class="page-item">
                        <a class="page-link" href="{{ $products->appends(['q' => $query])->nextPageUrl() }}" rel="next">
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
    @else
        <div class="alert alert-info mt-4">No products found for your search.</div>
    @endif
</div>
@endsection

@include('components.cart.add-to-cart-scripts') 