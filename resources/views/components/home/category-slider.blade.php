<section class="py-5">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="section-header d-flex justify-content-between mb-5">
                    <h2 class="section-title">Categories</h2>
                    <div class="d-flex align-items-center">
                        <div class="swiper-buttons">
                            <button class="swiper-prev categories-carousel-prev btn btn-primary">❮</button>
                            <button class="swiper-next categories-carousel-next btn btn-primary">❯</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="categories-carousel swiper">
                    <div class="swiper-wrapper">
                        @foreach($categories as $category)
                        <div class="swiper-slide">
                            <div class="category-item">
                                <a href="/category/{{ $category->slug }}" class="category-link">
                                    <figure class="category-image">
                                        <img src="{{ asset($category->image_url) }}" alt="{{ $category->name }}" class="img-fluid rounded">
                                        <figcaption class="category-caption">
                                            <h3>{{ $category->name }}</h3>
                                            <p>{{ $category->description }}</p>
                                        </figcaption>
                                    </figure>
                                </a>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
.category-item {
    position: relative;
    overflow: hidden;
    border-radius: 8px;
    transition: transform 0.3s ease;
}

.category-item:hover {
    transform: translateY(-5px);
}

.category-image {
    position: relative;
    margin: 0;
}

.category-image img {
    width: 100%;
    height: 200px;
    object-fit: cover;
}

.category-caption {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    padding: 15px;
    background: linear-gradient(to top, rgba(0,0,0,0.7), transparent);
    color: white;
}

.category-caption h3 {
    margin: 0;
    font-size: 1.2rem;
    font-weight: 600;
}

.category-caption p {
    margin: 5px 0 0;
    font-size: 0.9rem;
    opacity: 0.9;
}

.category-link {
    text-decoration: none;
    color: inherit;
}

.category-link:hover {
    color: inherit;
}
</style>

@push('scripts')
<script>
    new Swiper('.categories-carousel', {
        slidesPerView: 1,
        spaceBetween: 20,
        navigation: {
            nextEl: '.categories-carousel-next',
            prevEl: '.categories-carousel-prev',
        },
        breakpoints: {
            576: {
                slidesPerView: 2,
            },
            768: {
                slidesPerView: 3,
            },
            992: {
                slidesPerView: 4,
            },
            1200: {
                slidesPerView: 6,
            }
        }
    });
</script>
@endpush