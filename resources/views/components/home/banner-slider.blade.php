<section class="py-3"
    style="background-image: url('images/background-pattern.jpg');background-repeat: no-repeat;background-size: cover;">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <!-- Full-width Banner Slider -->
                <div class="banner-ad large bg-info block-1">
                    <!-- Glide.js Banner Slider -->
                    <!-- Glide.js CSS -->
                    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@glidejs/glide/dist/css/glide.core.min.css">
                    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@glidejs/glide/dist/css/glide.theme.min.css">
                    <div class="glide" id="bannerGlide">
                        <div class="glide__track" data-glide-el="track">
                            <ul class="glide__slides">
                                @foreach($banners as $banner)
                                <li class="glide__slide">
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
                                </li>
                                @endforeach
                            </ul>
                        </div>
                        <div class="glide__bullets" data-glide-el="controls[nav]">
                            @foreach($banners as $i => $banner)
                                <button class="glide__bullet" data-glide-dir="={{ $i }}"></button>
                            @endforeach
                        </div>
                    </div>
                    <!-- End Glide.js Banner Slider -->
                </div>
                <!-- End Full-width Banner Slider -->
            </div>
        </div>
    </div>
</section>

@push('scripts')
<!-- Glide.js JS -->
<script src="https://cdn.jsdelivr.net/npm/@glidejs/glide/dist/glide.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    new Glide('#bannerGlide', {
        type: 'carousel',
        autoplay: 5000,
        hoverpause: true,
        perView: 1,
        animationDuration: 800,
    }).mount();
});
</script>
@endpush