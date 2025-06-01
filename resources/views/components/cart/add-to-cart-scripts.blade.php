@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize cart items on page load
    initializeCartItems();

    // Remove any existing handlers first
    $(document).off('click', '.quantity-right-plus');
    $(document).off('click', '.quantity-left-minus');
    $(document).off('click', '.add-to-cart');

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
        e.stopPropagation(); // Prevent event bubbling
        
        console.log('Plus button clicked');
        const productQty = $(this).closest('.product-qty');
        const input = productQty.find('.quantity');
        const max = parseInt(input.attr('max')) || 99;
        const currentQty = parseInt(input.val()) || 1;
        
        console.log('Current quantity:', currentQty, 'Max:', max);
        
        if (currentQty < max) {
            const newQty = currentQty + 1;
            console.log('Updating to new quantity:', newQty);
            input.val(newQty);
            updateCartQuantity(productQty);
        } else {
            console.log('Max quantity reached');
            toastr.warning('Maximum quantity reached');
        }
    });

    $(document).on('click', '.quantity-left-minus', function(e) {
        e.preventDefault();
        e.stopPropagation(); // Prevent event bubbling
        
        console.log('Minus button clicked');
        const productQty = $(this).closest('.product-qty');
        const input = productQty.find('.quantity');
        const currentQty = parseInt(input.val()) || 1;
        
        if (currentQty > 1) {
            const newQty = currentQty - 1;
            console.log('Updating to new quantity:', newQty);
            input.val(newQty);
            updateCartQuantity(productQty);
        } else {
            console.log('Removing item from cart');
            removeFromCart($(this).closest('.product-item'));
        }
    });

    function updateCartQuantity(productQty) {
        const variantId = productQty.data('variant-id');
        const quantity = parseInt(productQty.find('.quantity').val()) || 1;
        console.log('Updating cart quantity:', { variantId, quantity });

        // Disable buttons during update
        productQty.find('button').prop('disabled', true);

        $.ajax({
            url: '{{ route("cart.update") }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                variant_id: variantId,
                quantity: quantity
            },
            success: function(response) {
                console.log('Cart update success:', response);
                if (response.success) {
                    $('.cart-count').text(response.cart_count);
                    $(document).trigger('cart:updated');
                    toastr.success('Cart updated');
                }
            },
            error: function(xhr) {
                console.error('Cart update error:', xhr.responseJSON);
                // Revert quantity on error
                const oldQuantity = xhr.responseJSON?.old_quantity || 1;
                productQty.find('.quantity').val(oldQuantity);
                toastr.error(xhr.responseJSON?.message || 'Failed to update cart');
            },
            complete: function() {
                // Re-enable buttons after update
                productQty.find('button').prop('disabled', false);
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
                    .html('<svg width="18" height="18" class="me-2"><use xlink:href="#cart"></use></svg>Add to Cart');
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