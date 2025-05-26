@extends('layouts.main')

@section('hide-menu', true)

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            {{-- SEO Breadcrumbs --}}
            <nav aria-label="breadcrumb" class="mb-3 ps-2" style="font-size: 1rem;" itemscope itemtype="https://schema.org/BreadcrumbList">
                <ol class="breadcrumb bg-white px-3 py-2 rounded shadow-sm align-items-center" style="margin-bottom: 0;">
                    <li class="breadcrumb-item" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                        <a href="{{ url('/') }}" itemprop="item"><span itemprop="name">Home</span></a>
                        <meta itemprop="position" content="1" />
                    </li>
                    @if($product->category)
                    <li class="breadcrumb-item" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                        <a href="{{ route('shop', ['category' => $product->category->slug]) }}" itemprop="item">
                            <span itemprop="name">{{ $product->category->name }}</span>
                        </a>
                        <meta itemprop="position" content="2" />
                    </li>
                    @endif
                    @if($product->subcategory)
                    <li class="breadcrumb-item" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                        <a href="{{ route('shop', ['subcategory' => $product->subcategory->id]) }}" itemprop="item">
                            <span itemprop="name">{{ $product->subcategory->name }}</span>
                        </a>
                        <meta itemprop="position" content="3" />
                    </li>
                    @endif
                    <li class="breadcrumb-item active" aria-current="page" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                        <span itemprop="name">{{ $product->name }}</span>
                        <meta itemprop="position" content="4" />
                    </li>
                </ol>
            </nav>
            <div class="row g-4 bg-white rounded shadow p-4">
                <!-- Image Gallery -->
                <div class="col-md-5">
                    <div class="d-flex flex-column align-items-center">
                        <!-- Thumbnails (if you have multiple images) -->
                        <div class="mb-3 d-flex flex-md-column gap-2">
                            @foreach([$product->image_url] as $img) {{-- Replace with $product->images if available --}}
                                <img src="{{ $img ?? asset('images/no-image.png') }}" alt="thumb" class="img-thumbnail" style="width: 60px; height: 60px; object-fit: cover;">
                            @endforeach
                        </div>
                        <!-- Main Image -->
                        <img src="{{ $product->image_url ?? asset('images/no-image.png') }}" alt="{{ $product->name }}" class="img-fluid rounded" style="max-height: 350px; object-fit: contain;">
                    </div>
                </div>
                <!-- Product Info -->
                <div class="col-md-7">
                    <div class="mb-2 text-muted small">
                        @if($product->category)
                            {{ $product->category->name }}
                            @if($product->subcategory)
                                / {{ $product->subcategory->name }}
                            @endif
                        @endif
                    </div>
                    <h2 class="fw-bold mb-2">{{ $product->name }}</h2>
                    <div class="mb-2">
                        <span class="badge bg-success"><i class="bi bi-star-fill"></i> 4.7</span>
                        <span class="text-muted ms-2">(53.4k)</span>
                        <span class="ms-3 text-secondary">Net Qty: 100g</span>
                    </div>
                    <div class="mb-2">
                        <span class="fs-3 fw-bold text-success">₹{{ number_format($product->price, 2) }}</span>
                        @if($product->original_price)
                            <span class="text-muted text-decoration-line-through ms-2">₹{{ number_format($product->original_price, 2) }}</span>
                            <span class="text-success ms-2">{{ round(100 - ($product->price / $product->original_price * 100)) }}% Off</span>
                        @endif
                    </div>
                    <div class="mb-3">
                        <span class="badge bg-light text-success border border-success">Get in 7 minutes</span>
                    </div>
                    <button class="btn btn-lg btn-pink w-100 mb-3" style="background: #ff3571; color: #fff;">Add To Cart</button>

                    <!-- Product Description, Refund Policy, and Info -->
                    <div class="mt-4">
                        <h5 class="mb-2">Product Description</h5>
                        <p class="mb-3">{{ $product->description ?? 'No description available.' }}</p>
                        <h5 class="mb-2">Refund Policy</h5>
                        <p class="mb-3">This product is not eligible for return or exchange unless it is damaged or defective at the time of delivery. Please check our <a href="{{ url('/terms') }}">Terms & Conditions</a> for more details.</p>
                        <h5 class="mb-2">Info</h5>
                        <ul class="list-unstyled mb-0">
                            <li class="mb-2"><i class="bi bi-info-circle"></i> 100% genuine products</li>
                            <li class="mb-2"><i class="bi bi-truck"></i> Fast delivery available</li>
                            <li class="mb-2"><i class="bi bi-shield-check"></i> Secure payment options</li>
                        </ul>
                    </div>
                </div>
            </div>

            {{-- You Might Also Like --}}
            @if(isset($similarProducts) && $similarProducts->count())
                <div class="mt-5">
                    <h4 class="mb-3">You Might Also Like</h4>
                    <div class="row row-cols-2 row-cols-md-4 g-3">
                        @foreach($similarProducts as $sim)
                            <div class="col">
                                <div class="card h-100">
                                    <a href="{{ route('product.show', $sim->id) }}">
                                        <img src="{{ $sim->image_url ?? asset('images/no-image.png') }}" class="card-img-top" alt="{{ $sim->name }}">
                                    </a>
                                    <div class="card-body p-2">
                                        <a href="{{ route('product.show', $sim->id) }}" class="text-decoration-none text-dark">
                                            <h6 class="card-title mb-1">{{ $sim->name }}</h6>
                                        </a>
                                        <div class="text-success fw-bold">₹{{ number_format($sim->price, 2) }}</div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- SEO Product Schema --}}
            <script type="application/ld+json">
            {
              "@context": "https://schema.org/",
              "@type": "Product",
              "name": "{{ $product->name }}",
              "image": "{{ $product->image_url ?? asset('images/no-image.png') }}",
              "description": "{{ $product->description }}",
              "sku": "{{ $product->id }}",
              "brand": {
                "@type": "Brand",
                "name": "{{ $product->shop->name ?? 'Shop' }}"
              },
              "offers": {
                "@type": "Offer",
                "priceCurrency": "INR",
                "price": "{{ $product->price }}",
                "availability": "https://schema.org/InStock"
              }
            }
            </script>

        </div>
    </div>
</div>

<style>
.btn-pink {
    background: #ff3571;
    color: #fff;
    border: none;
}
.btn-pink:hover {
    background: #e62e63;
    color: #fff;
}
</style>
@endsection 