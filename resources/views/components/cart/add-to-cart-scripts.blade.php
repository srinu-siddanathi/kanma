@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize cart items on page load
    initializeCartItems();

    // Add to cart functionality
    $(document).on('click', '.add-to-cart', function() {
        const button = $(this);
        const productItem = button.closest('.product-item');
        const productId = button.data('product-id');
        const variantId = button.data('variant-id');
        const quantity = 1; // Default quantity when adding

        addToCart(productId, variantId, quantity, productItem);
    });

    // Quantity controls with event delegation
    $(document).on('click', '.quantity-right-plus', function(e) {
        e.preventDefault();
        const input = $(this).closest('.product-qty').find('.quantity');
        const max = parseInt(input.attr('max'));
        const currentQty = parseInt(input.val());
        
        if (currentQty < max) {
            input.val(currentQty + 1);
            const productItem = $(this).closest('.product-item');
            updateCartQuantity(productItem);
        }
    });

    $(document).on('click', '.quantity-left-minus', function(e) {
        e.preventDefault();
        const input = $(this).closest('.product-qty').find('.quantity');
        const currentQty = parseInt(input.val());
        
        if (currentQty > 1) {
            input.val(currentQty - 1);
            const productItem = $(this).closest('.product-item');
            updateCartQuantity(productItem);
        } else {
            removeFromCart($(this).closest('.product-item'));
        }
    });

    function updateCartQuantity(productItem) {
        const variantId = productItem.find('.product-qty').data('variant-id');
        const quantity = productItem.find('.quantity').val();

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
                    $(document).trigger('cart:updated');
                    toastr.success('Cart updated');
                }
            },
            error: function(xhr) {
                // Revert quantity on error
                const oldQuantity = xhr.responseJSON?.old_quantity || 1;
                productItem.find('.quantity').val(oldQuantity);
                toastr.error(xhr.responseJSON?.message || 'Failed to update cart');
            }
        });
    }

    function addToCart(productId, variantId, quantity, productItem) {
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
                productItem.find('.add-to-cart').prop('disabled', true).html('Adding...');
            },
            success: function(response) {
                if (response.success) {
                    $('.cart-count').text(response.cart_count);
                    $(document).trigger('cart:updated');
                    
                    const cartAction = productItem.find('.cart-action');
                    cartAction.html(`
                        <div class="product-qty" data-variant-id="${variantId}">
                            <button type="button" class="btn btn-outline-secondary btn-sm quantity-left-minus" data-type="minus">-</button>
                            <input type="text" name="quantity" class="form-control form-control-sm quantity" 
                                   value="${quantity}" min="1" max="${response.max_stock}" readonly>
                            <button type="button" class="btn btn-outline-secondary btn-sm quantity-right-plus" data-type="plus">+</button>
                        </div>
                    `);
                    
                    toastr.success(response.message);
                }
            },
            error: function(xhr) {
                toastr.error(xhr.responseJSON?.message || 'Failed to add product to cart');
            },
            complete: function() {
                productItem.find('.add-to-cart').prop('disabled', false)
                    .html('Add <svg width="16" height="16"><use xlink:href="#cart"></use></svg>');
            }
        });
    }

    function removeFromCart(productItem) {
        const variantId = productItem.find('.product-qty').data('variant-id');

        $.ajax({
            url: '{{ route("cart.remove") }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                variant_id: variantId
            },
            success: function(response) {
                if (response.success) {
                    $('.cart-count').text(response.cart_count);
                    const cartAction = productItem.find('.cart-action');
                    cartAction.html(`
                        <button class="btn btn-warning btn-sm add-to-cart" 
                            data-product-id="${response.product_id}"
                            data-variant-id="${variantId}">
                            Add <svg width="16" height="16"><use xlink:href="#cart"></use></svg>
                        </button>
                    `);
                    
                    $(document).trigger('cart:updated');
                    toastr.success('Item removed from cart');
                }
            },
            error: function(xhr) {
                toastr.error('Failed to remove item from cart');
            }
        });
    }

    function initializeCartItems() {
        $.get('{{ route("cart.items") }}', function(response) {
            if (response.success && response.items) {
                Object.keys(response.items).forEach(variantId => {
                    const item = response.items[variantId];
                    const productItem = $(`.add-to-cart[data-variant-id="${variantId}"]`).closest('.product-item');
                    
                    if (productItem.length) {
                        const cartAction = productItem.find('.cart-action');
                        cartAction.html(`
                            <div class="product-qty" data-variant-id="${variantId}">
                                <button type="button" class="btn btn-outline-secondary btn-sm quantity-left-minus" data-type="minus">-</button>
                                <input type="text" name="quantity" class="form-control form-control-sm quantity" 
                                       value="${item.quantity}" min="1" max="${item.max_stock}" readonly>
                                <button type="button" class="btn btn-outline-secondary btn-sm quantity-right-plus" data-type="plus">+</button>
                            </div>
                        `);
                    }
                });
            }
        });
    }
});
</script>
@endpush 