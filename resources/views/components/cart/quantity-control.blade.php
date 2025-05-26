<div class="product-qty">
    <button type="button" class="btn btn-outline-secondary btn-sm quantity-left-minus" data-type="minus">
        -
    </button>
    <input type="text" name="quantity" class="form-control form-control-sm quantity" 
           value="1" min="1" max="{{ $max ?? 99 }}" readonly>
    <button type="button" class="btn btn-outline-secondary btn-sm quantity-right-plus" data-type="plus">
        +
    </button>
</div> 