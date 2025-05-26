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
        </div>
    </div>
</section>