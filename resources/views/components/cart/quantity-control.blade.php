<div class="product-qty d-flex align-items-center justify-content-center gap-1" data-variant-id="{{ $variant->id ?? '' }}">
    <button type="button" class="btn btn-outline-secondary btn-sm quantity-left-minus" data-type="minus">-</button>
    <input type="text" name="quantity" class="form-control form-control-sm quantity" 
           value="{{ isset($cart[$variant->id]) ? $cart[$variant->id]['quantity'] : 1 }}" min="1" max="{{ $max ?? 99 }}" readonly style="width: 40px; text-align: center; border-radius: 0.5rem; padding: 0; height: 32px;">
    <button type="button" class="btn btn-outline-secondary btn-sm quantity-right-plus" data-type="plus">+</button>
</div> 