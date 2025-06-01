@php
    $shops = \App\Models\Shop::where('is_active', 1)
        ->get();
@endphp

<section class="shops-section py-5">
    <div class="container">
        <div class="row mb-4">
            <div class="col-12">
                <h2 class="section-title text-center mb-4">Our Shops</h2>
                <p class="text-center text-muted">Discover our carefully curated selection of shops</p>
            </div>
        </div>
        
        <div class="row g-4">
            @forelse($shops as $shop)
                <div class="col-md-4 col-lg-3">
                    <div class="card h-100 shop-card">
                        <div class="card-body d-flex flex-column">
                            <div class="shop-image mb-3">
                                <img src="{{ asset($shop->image_path ?? 'images/no-image.png') }}" 
                                     class="img-fluid rounded" 
                                     alt="{{ $shop->name }}"
                                     style="width: 100%; height: 200px; object-fit: cover;">
                            </div>
                            <h5 class="card-title mb-2">{{ $shop->name }}</h5>
                            <p class="card-text text-muted small mb-3">{{ Str::limit($shop->description, 100) }}</p>
                            <div class="mt-auto">
                                <a href="{{ route('shop.show', $shop->id) }}" class="btn btn-outline-primary w-100">
                                    Visit Shop
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-info text-center">
                        No active shops available at the moment.
                    </div>
                </div>
            @endforelse
        </div>
        <div class="row mt-4">
            <div class="col-12 text-center">
                <a href="{{ route('shops.all') }}" class="btn btn-primary btn-lg px-5">
                    View All Shops
                </a>
            </div>
        </div>
    </div>
</section>

<style>
.shops-section {
    background-color: #f8f9fa;
}

.shop-card {
    transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
    border: none;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.shop-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
}

.shop-image {
    position: relative;
    overflow: hidden;
    border-radius: 8px;
}

.shop-image img {
    transition: transform 0.3s ease-in-out;
}

.shop-card:hover .shop-image img {
    transform: scale(1.05);
}

.section-title {
    font-size: 2rem;
    font-weight: 600;
    color: #333;
    position: relative;
    padding-bottom: 15px;
}

.section-title::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 50%;
    transform: translateX(-50%);
    width: 60px;
    height: 3px;
    background-color: #ff3571;
}
</style> 