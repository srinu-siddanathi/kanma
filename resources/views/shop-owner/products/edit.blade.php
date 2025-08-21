@extends('layouts.shop-owner')

@section('title', 'Edit Product')

@section('content')
<div class="container mx-auto px-4">
    <div class="max-w-3xl mx-auto">
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Edit Product</h3>

                <form action="{{ route('shop-owner.products.update', $product) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="space-y-6">
                        <!-- Current Image Preview -->
                        @if($product->image_path)
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Current Main Image</label>
                            <div class="mt-1">
                                <img src="{{ asset($product->image_path) }}" 
                                     alt="{{ $product->name }}" 
                                     class="h-32 w-32 object-cover rounded">
                            </div>
                        </div>
                        @endif

                        <!-- Current Product Images -->
                        @if($product->images->count() > 0)
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Current Product Images</label>
                            <div class="mt-1 grid grid-cols-4 gap-2">
                                @foreach($product->images as $image)
                                <div class="relative">
                                    <img src="{{ asset($image->image_path) }}" 
                                         alt="{{ $product->name }}" 
                                         class="h-20 w-full object-cover rounded border">
                                    <div class="text-xs text-gray-600 mt-1 truncate">{{ $product->name }}</div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">Product Name</label>
                            <input type="text" name="name" id="name" 
                                value="{{ old('name', $product->name) }}" required
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                            <textarea name="description" id="description" rows="3" 
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">{{ old('description', $product->description) }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="category_id" class="block text-sm font-medium text-gray-700">Category</label>
                                <select name="category_id" id="category_id" required
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" 
                                            {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label for="price" class="block text-sm font-medium text-gray-700">Price (₹)</label>
                            <input type="number" name="price" id="price" 
                                value="{{ old('price', $product->price) }}" required step="0.01"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            @error('price')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Default Variant Information -->
                        @if($product->variants->count() > 0)
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h4 class="text-sm font-medium text-gray-700 mb-3">Default Variant Information</h4>
                            <div class="grid grid-cols-2 gap-4 text-sm">
                                @foreach($product->variants as $variant)
                                <div class="bg-white p-3 rounded border">
                                    <div class="font-medium text-gray-900">{{ $variant->quantity }} {{ $variant->unit }}</div>
                                    <div class="text-gray-600">Price: ₹{{ number_format($variant->price, 2) }}</div>
                                    <div class="text-gray-600">Stock: {{ $variant->stock }}</div>
                                    <div class="text-gray-600">Status: {{ $variant->is_active ? 'Active' : 'Inactive' }}</div>
                                </div>
                                @endforeach
                            </div>
                            
                        </div>
                        @endif

                        <!-- Image Search Section -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Search Existing Images</label>
                            <div class="flex space-x-2 mb-3">
                                <input type="text" id="imageSearchQuery" placeholder="Search for existing images (e.g., 'fresh eggs' will find 'eggs' too)..."
                                    class="flex-1 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                <button type="button" id="searchImagesBtn" 
                                    class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 text-sm">
                                    Search
                                </button>
                            </div>
                            
                            <!-- Search Results -->
                            <div id="imageSearchResults" class="hidden mb-4">
                                <h4 class="text-sm font-medium text-gray-700 mb-2">Found Images:</h4>
                                <div id="imageResultsGrid" class="grid grid-cols-4 gap-2 max-h-48 overflow-y-auto border border-gray-200 rounded-md p-2">
                                    <!-- Images will be loaded here -->
                                </div>
                            </div>

                            <!-- Selected Images -->
                            <div id="selectedImagesContainer" class="hidden mb-4">
                                <h4 class="text-sm font-medium text-gray-700 mb-2">Selected Images:</h4>
                                <div id="selectedImagesGrid" class="grid grid-cols-4 gap-2 border border-gray-200 rounded-md p-2">
                                    <!-- Selected images will be shown here -->
                                </div>
                            </div>
                        </div>

                        <div>
                            <label for="image" class="block text-sm font-medium text-gray-700">Update Main Image</label>
                            <input type="file" name="image" id="image" accept="image/*"
                                class="mt-1 block w-full border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            <p class="mt-1 text-sm text-gray-500">Leave empty to keep current image. Maximum file size: 2MB. Supported formats: JPG, PNG</p>
                            @error('image')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex justify-end">
                            <a href="{{ route('shop-owner.products.index') }}" 
                               class="bg-gray-200 text-gray-700 px-4 py-2 rounded-md mr-2 hover:bg-gray-300">
                                Cancel
                            </a>
                            <button type="submit" 
                                class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                                Update Product
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchBtn = document.getElementById('searchImagesBtn');
    const searchQuery = document.getElementById('imageSearchQuery');
    const resultsContainer = document.getElementById('imageSearchResults');
    const resultsGrid = document.getElementById('imageResultsGrid');
    const selectedContainer = document.getElementById('selectedImagesContainer');
    const selectedGrid = document.getElementById('selectedImagesGrid');
    const selectedImages = [];

    // Define functions first
    function addHiddenInput(imagePath) {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'selected_images[]';
        input.value = imagePath;
        
        // Target the specific product edit form
        const form = document.querySelector('form[action*="products"][method="POST"]');
        
        if (form) {
            form.appendChild(input);
        }
    }

    function removeHiddenInput(imagePath) {
        const form = document.querySelector('form[action*="products"][method="POST"]');
        const inputs = form.querySelectorAll('input[name="selected_images[]"]');
        inputs.forEach(input => {
            if (input.value === imagePath) {
                input.remove();
            }
        });
    }

    function updateSelectedImagesDisplay() {
        if (selectedImages.length === 0) {
            selectedContainer.classList.add('hidden');
            return;
        }

        selectedGrid.innerHTML = selectedImages.map((image, index) => `
            <div class="relative group">
                <img src="${image.url}" alt="${image.productName}" 
                     class="w-full h-20 object-cover rounded border-2 border-indigo-500">
                <button type="button" onclick="removeSelectedImage(${index})" 
                        class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs hover:bg-red-600">
                    ×
                </button>
                <div class="text-xs text-gray-600 mt-1 truncate">${image.productName}</div>
            </div>
        `).join('');
        
        selectedContainer.classList.remove('hidden');
    }

    // Initialize with existing product images
    @if($product->images->count() > 0)
        @foreach($product->images as $image)
            selectedImages.push({
                path: '{{ $image->image_path }}',
                url: '{{ asset($image->image_path) }}',
                productName: '{{ $product->name }}'
            });
        @endforeach
        updateSelectedImagesDisplay();
        @foreach($product->images as $image)
            addHiddenInput('{{ $image->image_path }}');
        @endforeach
    @endif

    // Search for images when button is clicked
    searchBtn.addEventListener('click', function() {
        const query = searchQuery.value.trim();
        if (query.length < 2) {
            alert('Please enter at least 2 characters to search');
            return;
        }
        
        searchImages(query);
    });

    // Search for images when Enter is pressed
    searchQuery.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            searchBtn.click();
        }
    });

    // Auto-search when product name changes
    const productNameInput = document.getElementById('name');
    productNameInput.addEventListener('input', function() {
        const name = this.value.trim();
        if (name.length >= 2) {
            searchImages(name);
        }
    });

    function searchImages(query) {
        fetch('{{ route("shop-owner.products.search-images") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ query: query })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displaySearchResults(data.images);
            } else {
                alert('Error searching for images');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error searching for images');
        });
    }

    function displaySearchResults(images) {
        if (images.length === 0) {
            resultsGrid.innerHTML = '<p class="text-gray-500 text-sm col-span-4">No images found</p>';
        } else {
            resultsGrid.innerHTML = images.map(image => `
                <div class="relative group cursor-pointer" onclick="selectImage('${image.path}', '${image.url}', '${image.product_name}')">
                    <img src="${image.url}" alt="${image.product_name}" 
                         class="w-full h-20 object-cover rounded border-2 border-gray-200 hover:border-indigo-500">
                    <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-30 transition-all duration-200 rounded flex items-center justify-center">
                        <span class="text-white text-xs opacity-0 group-hover:opacity-100">Click to select</span>
                    </div>
                    <div class="text-xs text-gray-600 mt-1 truncate">${image.product_name}</div>
                </div>
            `).join('');
        }
        resultsContainer.classList.remove('hidden');
    }

    window.selectImage = function(imagePath, imageUrl, productName) {
        // Check if image is already selected
        if (selectedImages.some(img => img.path === imagePath)) {
            alert('This image is already selected');
            return;
        }

        // Add to selected images
        selectedImages.push({
            path: imagePath,
            url: imageUrl,
            productName: productName
        });

        // Update selected images display
        updateSelectedImagesDisplay();
        
        // Add hidden input for form submission
        addHiddenInput(imagePath);
    };

    window.removeSelectedImage = function(index) {
        const imagePath = selectedImages[index].path;
        selectedImages.splice(index, 1);
        
        // Remove hidden input
        removeHiddenInput(imagePath);
        
        // Update display
        updateSelectedImagesDisplay();
    };
});
</script>
@endpush
@endsection 