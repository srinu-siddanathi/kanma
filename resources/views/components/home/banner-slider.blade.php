<section class="py-3"
    style="background-image: url('images/background-pattern.jpg');background-repeat: no-repeat;background-size: cover;">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="banner-blocks">
                    <div class="banner-ad large bg-info block-1">
                        <div class="swiper main-swiper">
                            <div class="swiper-wrapper">
                                @foreach($banners as $banner)
                                <div class="swiper-slide">
                                    <div class="row banner-content p-5">
                                        <div class="content-wrapper col-md-7">
                                            <div class="categories my-3">{{ $banner->subtitle }}</div>
                                            <h3 class="display-4">{{ $banner->title }}</h3>
                                            <p>{{ $banner->description }}</p>
                                            <a href="{{ $banner->button_url }}" 
                                               class="btn btn-outline-dark btn-lg text-uppercase fs-6 rounded-1 px-4 py-3 mt-3">
                                                {{ $banner->button_text }}
                                            </a>
                                        </div>
                                        <div class="img-wrapper col-md-5">
                                            <img src="{{ asset($banner->image_url) }}" class="img-fluid">
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            <div class="swiper-pagination"></div>
                        </div>
                    </div>
                    <div class="banner-ad bg-success-subtle block-2"
                        style="background:url('images/ad-image-1.png') no-repeat;background-position: right bottom">
                        <div class="row banner-content p-5">
                            <div class="content-wrapper col-md-7">
                                <div class="categories sale mb-3 pb-3">20% off</div>
                                <h3 class="banner-title">Fruits & Vegetables</h3>
                                <a href="#" class="d-flex align-items-center nav-link">Shop Collection <svg width="24"
                                        height="24">
                                        <use xlink:href="#arrow-right"></use>
                                    </svg></a>
                            </div>
                        </div>
                    </div>
                    <div class="banner-ad bg-danger block-3"
                        style="background:url('images/ad-image-2.png') no-repeat;background-position: right bottom">
                        <div class="row banner-content p-5">
                            <div class="content-wrapper col-md-7">
                                <div class="categories sale mb-3 pb-3">15% off</div>
                                <h3 class="item-title">Baked Products</h3>
                                <a href="#" class="d-flex align-items-center nav-link">Shop Collection <svg width="24"
                                        height="24">
                                        <use xlink:href="#arrow-right"></use>
                                    </svg></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
</section>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    new Swiper(".main-swiper", {
        speed: 500,
        pagination: {
            el: ".swiper-pagination",
            clickable: true,
        },
        autoplay: {
            delay: 5000,
            disableOnInteraction: false,
        },
        loop: true,
        effect: 'fade',
        fadeEffect: {
            crossFade: true
        }
    });
});
</script>
@endpush