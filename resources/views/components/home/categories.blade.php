@php
    $categories = \App\Models\Category::where('is_active', 1)->get();
@endphp

<section class="categories-section py-5">
    <div class="container">
        <div class="row mb-4">
            <div class="col-12">
                <h2 class="section-title text-center mb-4">Shop by Category</h2>
                <p class="text-center text-muted">Browse through our wide range of categories</p>
            </div>
        </div>
        
        <div class="row g-4">
            @forelse($categories as $category)
                <div class="col-md-4 col-lg-3">
                    <div class="card h-100 category-card">
                        <div class="card-body d-flex flex-column">
                            <div class="category-image mb-3">
                                <img src="{{ asset($category->image_path ?? 'images/no-image.png') }}" 
                                     class="img-fluid rounded" 
                                     alt="{{ $category->name }}"
                                     style="width: 100%; height: 160px; object-fit: cover;">
                            </div>
                            <h5 class="card-title mb-2">{{ $category->name }}</h5>
                            <p class="card-text text-muted small mb-3">{{ Str::limit($category->description, 80) }}</p>
                            <div class="mt-auto">
                                <a href="{{ route('category.show', $category->id) }}" class="btn btn-outline-primary w-100">
                                    View Products
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-info text-center">
                        No active categories available at the moment.
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</section>

<style>
.categories-section {
    background-color: #fff;
}

.category-card {
    transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
    border: none;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.category-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
}

.category-image {
    position: relative;
    overflow: hidden;
    border-radius: 8px;
}

.category-image img {
    transition: transform 0.3s ease-in-out;
}

.category-card:hover .category-image img {
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