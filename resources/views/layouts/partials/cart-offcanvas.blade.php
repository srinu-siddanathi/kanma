<div class="offcanvas offcanvas-end" data-bs-scroll="true" tabindex="-1" id="offcanvasCart" aria-labelledby="My Cart">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title">Your Cart</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <div class="order-md-last">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="text-primary h5 mb-0">Items</span>
                <span class="badge bg-primary rounded-pill cart-count">0</span>
            </div>
            <div id="cartItems">
                <!-- Cart items will be loaded here -->
            </div>
            <!-- <div class="card p-2 mt-3">
                <div class="input-group">
                    <input type="text" class="form-control" placeholder="Promo code">
                    <button type="submit" class="btn btn-secondary">Redeem</button>
                </div>
            </div> -->
            <div class="card p-2 mt-3">
                <div class="d-flex justify-content-between align-items-center">
                    <span>Total (INR)</span>
                    <span class="cart-total">₹0.00</span>
                </div>
                <a href="{{ route('checkout') }}" class="btn btn-primary mt-3 w-100">Proceed to Checkout</a>
            </div>
        </div>
    </div>
</div>

<style>
.offcanvas-header {
    border-bottom: 1px solid #dee2e6;
    padding: 1rem;
}

.offcanvas-header .btn-close {
    margin: 0;
    padding: 0;
}

.offcanvas-title {
    margin-bottom: 0;
}

#offcanvasCart {
    width: 400px;
}

@media (max-width: 576px) {
    #offcanvasCart {
        width: 100%;
    }
}
</style>

@push('scripts')
<script id="cart-item-template" type="text/template">
    <div class="card mb-3 cart-item" data-variant-id="{variant_id}">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <img src="{image_path}" class="img-fluid rounded" 
                         style="width: 60px; height: 60px; object-fit: cover;" 
                         alt="{product_name}">
                    <div class="ms-3">
                        <h6 class="mb-1">{product_name}</h6>
                        <div class="text-muted">
                            <small>{quantity} {unit} × ₹{price}</small>
                        </div>
                    </div>
                </div>
                <div class="text-end">
                    <div class="fw-bold mb-1">₹{total}</div>
                    <button class="btn btn-sm btn-outline-danger remove-from-cart">
                        <svg width="16" height="16"><use xlink:href="#trash"></use></svg>
                    </button>
                </div>
            </div>
        </div>
    </div>
</script>

<script>
// Update all cart count elements on the page
function updateCartCount(count) {
    $('.cart-count').each(function() {
        const $badge = $(this);
        $badge.fadeOut(200, function() {
            $badge.text(count).fadeIn(200);
        });
    });
}

function updateCart() {
    $.get('{{ route("cart.items") }}', function(response) {
        const cartItems = $('#cartItems');
        cartItems.empty();
        
        updateCartCount(response.count);
        $('.cart-total').text('₹' + parseFloat(response.total).toFixed(2));

        const template = $('#cart-item-template').html();
        
        $.each(response.items, function(variantId, item) {
            const price = parseFloat(item.price);
            const total = (price * item.quantity).toFixed(2);
            
            let itemHtml = template
                .replace(/{variant_id}/g, item.variant_id)
                .replace(/{image_path}/g, asset(item.image_path))
                .replace(/{product_name}/g, item.product_name)
                .replace(/{quantity}/g, item.quantity)
                .replace(/{unit}/g, item.unit)
                .replace(/{price}/g, price.toFixed(2))
                .replace(/{total}/g, total);
                
            cartItems.append(itemHtml);
        });
    });
}

// Make sure cart is updated when items are added
$(document).on('cart:updated', updateCart);

// Remove item from cart
$(document).on('click', '.remove-from-cart', function(e) {
    e.preventDefault();
    const variantId = $(this).closest('.cart-item').data('variant-id');
    
    $.ajax({
        url: '{{ route("cart.remove") }}',
        method: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            variant_id: variantId
        },
        success: function(response) {
            if (response.success) {
                updateCart();
                toastr.success('Item removed from cart');
            }
        },
        error: function(xhr) {
            toastr.error('Failed to remove item from cart');
        }
    });
});

// Add asset helper function for JavaScript
function asset(path) {
    return '{{ asset("") }}' + path;
}
</script>
@endpush 