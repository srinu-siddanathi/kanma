@php
    $cart = Session::get('cart', []);
@endphp

<section class="py-5 overflow-hidden">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="section-header d-flex justify-content-between mb-5">
                    <h2 class="section-title">Best selling products</h2>
                    <div class="d-flex align-items-center">
                        <div class="swiper-buttons">
                            <button class="swiper-prev products-carousel-prev btn btn-primary">❮</button>
                            <button class="swiper-next products-carousel-next btn btn-primary">❯</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="products-carousel swiper">
                    <div class="swiper-wrapper">
                        @foreach($products as $product)
                        <div class="swiper-slide">
                            <div class="product-item">
                                <a href="/product/{{ $product->id }}" class="btn-wishlist">
                                    <svg width="24" height="24"><use xlink:href="#heart"></use></svg>
                                </a>
                                <figure>
                                    <a href="/product/{{ $product->id }}" title="{{ $product->name }}">
                                        <img src="{{ asset($product->image_path) }}" class="tab-image" alt="{{ $product->name }}">
                                    </a>
                                </figure>
                                <h3>{{ $product->name }}</h3>
                                @if($product->variants->isNotEmpty())
                                    @php 
                                        $variant = $product->variants->first(); 
                                        $inCart = isset($cart[$variant->id]);
                                    @endphp
                                    <span class="qty">{{ $variant->quantity }} {{ $variant->unit }}</span>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="price">
                                            @if($variant->discount_percentage > 0)
                                                <span class="old-price">₹{{ number_format($variant->price, 2) }}</span>
                                                ₹{{ number_format($variant->discounted_price, 2) }}
                                            @else
                                                ₹{{ number_format($variant->price, 2) }}
                                            @endif
                                        </span>
                                        <div class="cart-action">
                                            @if($inCart)
                                                <div class="product-qty" data-variant-id="{{ $variant->id }}">
                                                    <button type="button" class="btn btn-outline-secondary btn-sm quantity-left-minus" data-type="minus">-</button>
                                                    <input type="text" name="quantity" class="form-control form-control-sm quantity" 
                                                           value="{{ $cart[$variant->id]['quantity'] }}" min="1" max="{{ $variant->stock }}" readonly>
                                                    <button type="button" class="btn btn-outline-secondary btn-sm quantity-right-plus" data-type="plus">+</button>
                                                </div>
                                            @else
                                                <x-cart.add-to-cart-button 
                                                    :product-id="$product->id" 
                                                    :variant-id="$variant->id"
                                                />
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>