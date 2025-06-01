@extends('layouts.main')

@section('content')
<div class="container py-5">
    <div class="row mb-4">
        <div class="col-12 text-center">
            <h1 class="section-title">All Shops</h1>
            <p class="text-muted">Browse all our active shops</p>
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
                    No shops found.
                </div>
            </div>
        @endforelse
    </div>
    @if($shops->hasPages())
    <div class="row mt-4">
        <div class="col-12 d-flex justify-content-center">
            {{ $shops->links() }}
        </div>
    </div>
    @endif
</div>

<style>
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
</style>
@endsection 