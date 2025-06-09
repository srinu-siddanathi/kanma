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
                            @if($product->images->isNotEmpty())
                                @foreach($product->images as $img)
                                    <img src="{{ asset($img->image_path) }}" alt="thumb" class="img-thumbnail" style="width: 60px; height: 60px; object-fit: cover;">
                                @endforeach
                            @else
                                <img src="{{ asset('images/no-image.png') }}" alt="thumb" class="img-thumbnail" style="width: 60px; height: 60px; object-fit: cover;">
                            @endif
                        </div>
                        <!-- Main Image -->
                        @if($product->images->isNotEmpty())
                            <img src="{{ asset($product->images->first()->image_path) }}" alt="{{ $product->name }}" class="img-fluid rounded" style="max-height: 350px; object-fit: contain;">
                        @else
                            <img src="{{ asset('images/no-image.png') }}" alt="{{ $product->name }}" class="img-fluid rounded" style="max-height: 350px; object-fit: contain;">
                        @endif
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

                    <!-- Variant Selection -->
                    @if($product->variants->count() > 0)
                        <div class="mb-4">
                            <h5 class="mb-3">Select Variant</h5>
                            <div class="variant-options">
                                @foreach($product->variants as $variant)
                                    <div class="variant-option mb-2 {{ !$variant->is_active || $variant->stock <= 0 ? 'disabled' : '' }}">
                                        <input type="radio" 
                                               class="btn-check" 
                                               name="variant_id" 
                                               id="variant_{{ $variant->id }}" 
                                               value="{{ $variant->id }}"
                                               data-price="{{ $variant->price }}"
                                               data-stock="{{ $variant->stock }}"
                                               {{ !$variant->is_active || $variant->stock <= 0 ? 'disabled' : '' }}
                                               {{ $loop->first ? 'checked' : '' }}>
                                        <label class="btn btn-outline-primary" for="variant_{{ $variant->id }}">
                                            {{ $variant->quantity }} {{ $variant->unit }}
                                            @if(!$variant->is_active || $variant->stock <= 0)
                                                <span class="text-danger">({{ !$variant->is_active ? 'Unavailable' : 'Out of Stock' }})</span>
                                            @endif
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @php
                        $cart = Session::get('cart', []);
                        $selectedVariant = $product->variants->first();
                        $inCart = $selectedVariant && isset($cart[$selectedVariant->id]);
                        $notAvailableReason = '';
                        
                        if (!$selectedVariant) {
                            $notAvailableReason = 'No variants available';
                        } elseif (!$selectedVariant->is_active) {
                            $notAvailableReason = 'Currently unavailable';
                        } elseif ($selectedVariant->stock <= 0) {
                            $notAvailableReason = 'Out of stock';
                        }
                    @endphp

                    <div class="cart-action mb-3">
                        @if($selectedVariant && $selectedVariant->is_active && $selectedVariant->stock > 0)
                            @if($inCart)
                                <div class="product-qty" data-variant-id="{{ $selectedVariant->id }}">
                                    <button type="button" class="btn btn-outline-secondary quantity-left-minus" data-type="minus">-</button>
                                    <input type="text" name="quantity" class="form-control quantity" 
                                           value="{{ $cart[$selectedVariant->id]['quantity'] }}" min="1" max="{{ $selectedVariant->stock }}" readonly>
                                    <button type="button" class="btn btn-outline-secondary quantity-right-plus" data-type="plus">+</button>
                                </div>
                            @else
                                <button class="btn btn-lg btn-pink w-100 add-to-cart" 
                                        data-product-id="{{ $product->id }}" 
                                        data-variant-id="{{ $selectedVariant->id }}"
                                        style="background: #ff3571; color: #fff;">
                                    Add To Cart
                                </button>
                            @endif
                        @else
                            <button class="btn btn-lg btn-secondary w-100" disabled>
                                {{ $notAvailableReason }}
                            </button>
                        @endif
                    </div>

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

.product-qty {
    display: flex;
    align-items: center;
    gap: 10px;
    max-width: 200px;
}

.product-qty .quantity {
    width: 60px;
    text-align: center;
}

.product-qty .btn {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0;
}

.variant-options {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
}

.variant-option.disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.variant-option .btn {
    min-width: 120px;
    text-align: center;
}

.btn-check:checked + .btn-outline-primary {
    background-color: #ff3571;
    border-color: #ff3571;
    color: white;
}
</style>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize cart items on page load
    initializeCartItems();

    // Handle variant selection
    $('input[name="variant_id"]').on('change', function() {
        const variantId = $(this).val();
        const price = parseFloat($(this).data('price'));
        const stock = parseInt($(this).data('stock'));
        const cartAction = $('.cart-action');
        const cart = {!! json_encode($cart) !!};

        // Update price display
        const priceElement = $('.fs-3.fw-bold.text-success');
        priceElement.text('₹' + price.toFixed(2));

        // Update original price if exists
        const originalPriceElement = priceElement.next('.text-muted.text-decoration-line-through');
        if (originalPriceElement.length) {
            const originalPrice = parseFloat(originalPriceElement.text().replace('₹', '').replace(',', ''));
            const discount = Math.round(100 - (price / originalPrice * 100));
            originalPriceElement.next('.text-success').text(discount + '% Off');
        }

        // Update cart action section
        if (cart[variantId]) {
            cartAction.html(`
                <div class="product-qty" data-variant-id="${variantId}">
                    <button type="button" class="btn btn-outline-secondary quantity-left-minus" data-type="minus">-</button>
                    <input type="text" name="quantity" class="form-control quantity" 
                           value="${cart[variantId].quantity}" min="1" max="${stock}" readonly>
                    <button type="button" class="btn btn-outline-secondary quantity-right-plus" data-type="plus">+</button>
                </div>
            `);
        } else {
            cartAction.html(`
                <button class="btn btn-lg btn-pink w-100 add-to-cart" 
                        data-product-id="{{ $product->id }}" 
                        data-variant-id="${variantId}"
                        style="background: #ff3571; color: #fff;">
                    Add To Cart
                </button>
            `);
        }
    });

    // Add to cart functionality
    $(document).on('click', '.add-to-cart', function() {
        const button = $(this);
        const productId = button.data('product-id');
        const variantId = button.data('variant-id');
        const quantity = 1; // Default quantity when adding

        addToCart(productId, variantId, quantity, button);
    });

    // Quantity controls with event delegation
    $(document).on('click', '.quantity-right-plus', function(e) {
        e.preventDefault();
        const input = $(this).closest('.product-qty').find('.quantity');
        const max = parseInt(input.attr('max'));
        const currentQty = parseInt(input.val());
        
        if (currentQty < max) {
            input.val(currentQty + 1);
            updateCartQuantity($(this).closest('.product-qty'));
        }
    });

    $(document).on('click', '.quantity-left-minus', function(e) {
        e.preventDefault();
        const input = $(this).closest('.product-qty').find('.quantity');
        const currentQty = parseInt(input.val());
        
        if (currentQty > 1) {
            input.val(currentQty - 1);
            updateCartQuantity($(this).closest('.product-qty'));
        } else {
            removeFromCart($(this).closest('.product-qty'));
        }
    });

    function updateCartQuantity(productQty) {
        const variantId = productQty.data('variant-id');
        const quantity = productQty.find('.quantity').val();

        $.ajax({
            url: '{{ route("cart.update") }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                variant_id: variantId,
                quantity: quantity
            },
            success: function(response) {
                if (response.success) {
                    $('.cart-count').text(response.cart_count);
                    $(document).trigger('cart:updated', [true]);
                }
            },
            error: function(xhr) {
                // Revert quantity on error
                const oldQuantity = xhr.responseJSON?.old_quantity || 1;
                productQty.find('.quantity').val(oldQuantity);
                toastr.error(xhr.responseJSON?.message || 'Failed to update cart');
            }
        });
    }

    function addToCart(productId, variantId, quantity, button) {
        $.ajax({
            url: '{{ route("cart.add") }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                product_id: productId,
                variant_id: variantId,
                quantity: quantity
            },
            beforeSend: function() {
                button.prop('disabled', true).html('Adding...');
            },
            success: function(response) {
                if (response.success) {
                    $('.cart-count').text(response.cart_count);
                    
                    const cartAction = button.closest('.cart-action');
                    cartAction.html(`
                        <div class="product-qty" data-variant-id="${variantId}">
                            <button type="button" class="btn btn-outline-secondary quantity-left-minus" data-type="minus">-</button>
                            <input type="text" name="quantity" class="form-control quantity" 
                                   value="${quantity}" min="1" max="${response.max_stock}" readonly>
                            <button type="button" class="btn btn-outline-secondary quantity-right-plus" data-type="plus">+</button>
                        </div>
                    `);
                    
                    // Trigger cart update with message
                    $(document).trigger('cart:updated', [true]);
                } else {
                    toastr.error(response.message || 'Failed to add product to cart');
                }
            },
            error: function(xhr) {
                const errorMessage = xhr.responseJSON?.message || 'Failed to add product to cart';
                toastr.error(errorMessage);
            },
            complete: function() {
                button.prop('disabled', false).html('Add To Cart');
            }
        });
    }

    function removeFromCart(productQty) {
        const variantId = productQty.data('variant-id');
        const cartAction = productQty.closest('.cart-action');

        $.ajax({
            url: '{{ route("cart.remove") }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                variant_id: variantId
            },
            beforeSend: function() {
                productQty.find('button').prop('disabled', true);
            },
            success: function(response) {
                if (response.success) {
                    $('.cart-count').text(response.cart_count);
                    cartAction.html(`
                        <button class="btn btn-lg btn-pink w-100 add-to-cart" 
                            data-product-id="{{ $product->id }}"
                            data-variant-id="${variantId}"
                            style="background: #ff3571; color: #fff;">
                            Add To Cart
                        </button>
                    `);
                    
                    // Trigger cart update with message
                    $(document).trigger('cart:updated', [true]);
                }
            },
            error: function(xhr) {
                const errorMessage = xhr.responseJSON?.message || 'Failed to remove item from cart';
                toastr.error(errorMessage);
                productQty.find('button').prop('disabled', false);
            }
        });
    }

    function initializeCartItems() {
        $.get('{{ route("cart.items") }}', function(response) {
            if (response.success && response.items) {
                Object.keys(response.items).forEach(variantId => {
                    const item = response.items[variantId];
                    const cartAction = $('.cart-action');
                    
                    if (cartAction.length) {
                        cartAction.html(`
                            <div class="product-qty" data-variant-id="${variantId}">
                                <button type="button" class="btn btn-outline-secondary quantity-left-minus" data-type="minus">-</button>
                                <input type="text" name="quantity" class="form-control quantity" 
                                       value="${item.quantity}" min="1" max="${item.max_stock}" readonly>
                                <button type="button" class="btn btn-outline-secondary quantity-right-plus" data-type="plus">+</button>
                            </div>
                        `);
                    }
                });
            }
        });
    }

    // Update the cart:updated event listener
    $(document).on('cart:updated', function(event, showMessage = true) {
        if (showMessage) {
            toastr.success('Cart updated successfully');
        }
    });
});
</script>
@endpush
@endsection 