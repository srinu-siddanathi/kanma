@props(['productId', 'variantId'])

<button class="btn btn-warning btn-sm add-to-cart" 
    data-product-id="{{ $productId }}" 
    data-variant-id="{{ $variantId }}">
    Add <svg width="16" height="16"><use xlink:href="#cart"></use></svg>
</button> 