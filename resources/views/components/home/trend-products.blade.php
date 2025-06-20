<section class="py-5">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="bootstrap-tabs product-tabs">
                    <div class="tabs-header d-flex justify-content-between border-bottom my-5">
                        <h3>Trending Products</h3>
                    </div>
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="nav-all">
                            <div class="product-grid row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">
                                @php
                                    $cart = Session::get('cart', []);
                                @endphp
                                @foreach($products as $product)
                                <div class="col">
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
                                                <img src="{{ $product->image_url }}" class="card-img-top" alt="{{ $product->name }}"
                                                     style="height: 200px; object-fit: cover;"
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
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>