@extends('layouts.main')

@section('content')
<section class="py-5">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-6">
                <div class="product-image-gallery">
                    <div class="swiper product-gallery-top">
                        <div class="swiper-wrapper">
                            <div class="swiper-slide">
                                <img src="{{ asset('images/product-large-1.jpg') }}" class="img-fluid" alt="Product">
                            </div>
                            <div class="swiper-slide">
                                <img src="{{ asset('images/product-large-2.jpg') }}" class="img-fluid" alt="Product">
                            </div>
                            <div class="swiper-slide">
                                <img src="{{ asset('images/product-large-3.jpg') }}" class="img-fluid" alt="Product">
                            </div>
                        </div>
                    </div>
                    <div class="swiper product-gallery-thumbs mt-4">
                        <div class="swiper-wrapper">
                            <div class="swiper-slide">
                                <img src="{{ asset('images/product-thumb-1.jpg') }}" class="img-fluid" alt="Product Thumbnail">
                            </div>
                            <div class="swiper-slide">
                                <img src="{{ asset('images/product-thumb-2.jpg') }}" class="img-fluid" alt="Product Thumbnail">
                            </div>
                            <div class="swiper-slide">
                                <img src="{{ asset('images/product-thumb-3.jpg') }}" class="img-fluid" alt="Product Thumbnail">
                            </div>
                        </div>
                        <div class="swiper-button-next"></div>
                        <div class="swiper-button-prev"></div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="product-info p-4">
                    <div class="badge bg-success mb-3">In Stock</div>
                    <h1 class="product-title h2 mb-3">Fresh Organic Strawberry</h1>
                    <div class="product-rating d-flex align-items-center mb-4">
                        <div class="rating me-2">
                            <svg class="star-solid" width="16" height="16">
                                <use xlink:href="#star-solid"></use>
                            </svg>
                            <svg class="star-solid" width="16" height="16">
                                <use xlink:href="#star-solid"></use>
                            </svg>
                            <svg class="star-solid" width="16" height="16">
                                <use xlink:href="#star-solid"></use>
                            </svg>
                            <svg class="star-solid" width="16" height="16">
                                <use xlink:href="#star-solid"></use>
                            </svg>
                            <svg class="star-outline" width="16" height="16">
                                <use xlink:href="#star-outline"></use>
                            </svg>
                        </div>
                        <span class="rating-count text-muted">(24 Reviews)</span>
                    </div>
                    <div class="product-price mb-4">
                        <span class="h3 text-primary">$24.99</span>
                        <span class="h5 text-muted text-decoration-line-through ms-2">$29.99</span>
                    </div>
                    <div class="product-description mb-4">
                        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco.</p>
                    </div>
                    <div class="product-variations mb-4">
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label">Weight</label>
                                <select class="form-select">
                                    <option>250g</option>
                                    <option>500g</option>
                                    <option>1kg</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="product-actions mb-4">
                        <div class="row align-items-center">
                            <div class="col-md-4">
                                <div class="input-group product-qty">
                                    <span class="input-group-btn">
                                        <button type="button" class="quantity-left-minus btn btn-danger btn-number">
                                            <svg width="16" height="16">
                                                <use xlink:href="#minus"></use>
                                            </svg>
                                        </button>
                                    </span>
                                    <input type="text" id="quantity" name="quantity" class="form-control input-number" value="1">
                                    <span class="input-group-btn">
                                        <button type="button" class="quantity-right-plus btn btn-success btn-number">
                                            <svg width="16" height="16">
                                                <use xlink:href="#plus"></use>
                                            </svg>
                                        </button>
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <button type="button" class="btn btn-primary w-100">
                                    <svg width="18" height="18" class="me-2">
                                        <use xlink:href="#cart"></use>
                                    </svg>
                                    Add to Cart
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="product-meta">
                        <div class="row mb-2">
                            <div class="col-md-3">
                                <span class="text-muted">SKU:</span>
                            </div>
                            <div class="col-md-9">
                                FWM15VKT
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-md-3">
                                <span class="text-muted">Category:</span>
                            </div>
                            <div class="col-md-9">
                                <a href="#" class="text-decoration-none">Fruits</a>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <span class="text-muted">Tags:</span>
                            </div>
                            <div class="col-md-9">
                                <a href="#" class="text-decoration-none">Organic</a>,
                                <a href="#" class="text-decoration-none">Fresh</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-5 bg-light">
    <div class="container-fluid">
        <ul class="nav nav-tabs mb-4" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" data-bs-toggle="tab" href="#description">Description</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#additional">Additional Information</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#reviews">Reviews (24)</a>
            </li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane fade show active" id="description">
                <h3>Product Description</h3>
                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>
            </div>
            <div class="tab-pane fade" id="additional">
                <h3>Additional Information</h3>
                <table class="table">
                    <tbody>
                        <tr>
                            <th>Weight</th>
                            <td>250g, 500g, 1kg</td>
                        </tr>
                        <tr>
                            <th>Origin</th>
                            <td>Local Farm</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="tab-pane fade" id="reviews">
                <h3>Customer Reviews</h3>
                <!-- Reviews content -->
            </div>
        </div>
    </div>
</section>

@include('components.home.trend-products')
@endsection

@push('scripts')
<script>
    // Product gallery swiper
    var galleryThumbs = new Swiper('.product-gallery-thumbs', {
        spaceBetween: 10,
        slidesPerView: 4,
        watchSlidesVisibility: true,
        watchSlidesProgress: true,
        navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev',
        },
    });

    var galleryTop = new Swiper('.product-gallery-top', {
        spaceBetween: 10,
        thumbs: {
            swiper: galleryThumbs
        }
    });
</script>
@endpush 