@php
    $maxStock = $variant->stock ?? 99;
    $currentQty = isset($cart[$variant->id]) ? $cart[$variant->id]['quantity'] : 1;
@endphp
<div class="product-qty d-flex align-items-center justify-content-center gap-1" data-variant-id="{{ $variant->id }}">
    <button type="button" class="btn btn-outline-secondary btn-sm quantity-left-minus" data-type="minus">-</button>
    <input type="number" name="quantity" class="form-control form-control-sm quantity" 
           value="{{ $currentQty }}" 
           min="1" 
           max="{{ $maxStock }}" 
           readonly 
           style="width: 40px; text-align: center; border-radius: 0.5rem; padding: 0; height: 32px;">
    <button type="button" class="btn btn-outline-secondary btn-sm quantity-right-plus" data-type="plus">+</button>
</div> 