@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="p-6">
                <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="space-y-6">
                        <!-- Basic Information Section -->
                        <div class="bg-gray-50 rounded-lg p-6">
                            <h2 class="text-lg font-medium text-gray-900 mb-6">Basic Information</h2>
                            <div class="grid grid-cols-1 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Product Name</label>
                                    <input type="text" name="name" value="{{ old('name') }}" 
                                           placeholder="Enter product name"
                                           class="block w-full px-4 py-3 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 placeholder-gray-400 text-gray-900" 
                                           required>
                                    @error('name')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                                    <textarea name="description" rows="4" 
                                              placeholder="Enter product description"
                                              class="block w-full px-4 py-3 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 placeholder-gray-400 text-gray-900">{{ old('description') }}</textarea>
                                    @error('description')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                                    <select name="category_id" 
                                            class="block w-full px-4 py-3 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-gray-900" 
                                            required>
                                        <option value="">Select a category</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('category_id')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Product Images -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Product Images</label>
                                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md hover:border-indigo-500 transition-colors duration-200">
                                        <div class="space-y-1 text-center">
                                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" 
                                                      stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                            <div class="flex text-sm text-gray-600">
                                                <label class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                                    <span>Upload files</span>
                                                    <input type="file" name="images[]" class="sr-only" accept="image/*" multiple>
                                                </label>
                                                <p class="pl-1">or drag and drop</p>
                                            </div>
                                            <p class="text-xs text-gray-500">PNG, JPG, GIF up to 2MB each</p>
                                        </div>
                                    </div>
                                    <div id="image-preview" class="mt-4 grid grid-cols-4 gap-4"></div>
                                    @error('images')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Product Options -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label class="inline-flex items-center">
                                            <input type="checkbox" name="is_deal" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                            <span class="ml-2">Is Deal</span>
                                        </label>
                                    </div>
                                    <div>
                                        <label class="inline-flex items-center">
                                            <input type="checkbox" name="is_featured" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                            <span class="ml-2">Is Featured</span>
                                        </label>
                                    </div>
                                </div>

                                <!-- Discount Field (shown when is_deal is checked) -->
                                <div id="deal-fields" class="hidden">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Discount (%)</label>
                                            <input type="number" name="discount" value="{{ old('discount') }}" 
                                                   placeholder="Enter discount percentage"
                                                   class="block w-full px-4 py-3 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 placeholder-gray-400 text-gray-900"
                                                   min="0" max="100" step="0.01">
                                            @error('discount')
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Deal End Date</label>
                                            <input type="datetime-local" name="deal_end_date" value="{{ old('deal_end_date') }}" 
                                                   class="block w-full px-4 py-3 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 placeholder-gray-400 text-gray-900">
                                            @error('deal_end_date')
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Product Variants -->
                                <div class="bg-gray-50 rounded-lg p-6">
                                    <h2 class="text-lg font-medium text-gray-900 mb-6">Product Variants</h2>
                                    <div id="variants-container">
                                        <div class="variant-item space-y-4">
                                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Quantity</label>
                                                    <div class="mt-1 relative rounded-md shadow-sm">
                                                        <input type="number" 
                                                               name="variants[0][quantity]" 
                                                               placeholder="Enter quantity"
                                                               class="block w-full px-4 py-3 rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm placeholder-gray-400" 
                                                               step="0.01" 
                                                               min="0"
                                                               required>
                                                    </div>
                                                </div>
                                                
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Unit</label>
                                                    <select name="variants[0][unit]" 
                                                            class="block w-full px-4 py-3 rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm text-gray-900"
                                                            required>
                                                        <option value="">Select unit</option>
                                                        <option value="g">Grams (g)</option>
                                                        <option value="kg">Kilograms (kg)</option>
                                                        <option value="ml">Milliliters (ml)</option>
                                                        <option value="l">Liters (l)</option>
                                                    </select>
                                                </div>

                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Price</label>
                                                    <div class="mt-1 relative rounded-md shadow-sm">
                                                        <input type="number" 
                                                               name="variants[0][price]" 
                                                               placeholder="Enter price"
                                                               class="block w-full px-4 py-3 rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm placeholder-gray-400" 
                                                               step="0.01" 
                                                               min="0"
                                                               required>
                                                    </div>
                                                </div>

                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Stock</label>
                                                    <div class="mt-1 relative rounded-md shadow-sm">
                                                        <input type="number" 
                                                               name="variants[0][stock]" 
                                                               placeholder="Available quantity"
                                                               class="block w-full px-4 py-3 rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm placeholder-gray-400" 
                                                               min="0"
                                                               required>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <button type="button" id="add-variant" class="mt-4 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        Add Another Variant
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end pt-6">
                            <button type="submit" 
                                    class="inline-flex items-center px-6 py-3 border border-transparent rounded-md shadow-sm text-base font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200">
                                <svg class="-ml-1 mr-3 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Create Product
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
    // Handle multiple image upload preview and removal
    const imageInput = document.querySelector('input[name="images[]"]');
    const imagePreview = document.getElementById('image-preview');
    let selectedFiles = [];

    imageInput.addEventListener('change', function(e) {
        // Get the files from the input
        const files = Array.from(e.target.files);
        
        // Validate file types and sizes
        const validFiles = files.filter(file => {
            const isValidType = ['image/jpeg', 'image/png', 'image/gif'].includes(file.type);
            const isValidSize = file.size <= 2 * 1024 * 1024; // 2MB
            return isValidType && isValidSize;
        });

        if (validFiles.length !== files.length) {
            alert('Some files were invalid. Please only upload JPG, PNG, or GIF files under 2MB.');
        }

        // Append new files to existing selectedFiles array
        selectedFiles = [...selectedFiles, ...validFiles];
        renderImagePreview();
    });

    function renderImagePreview() {
        imagePreview.innerHTML = '';
        selectedFiles.forEach((file, idx) => {
            const reader = new FileReader();
            reader.onload = function(e) {
                const div = document.createElement('div');
                div.className = 'relative';
                div.innerHTML = `
                    <img src="${e.target.result}" class="w-full h-32 object-cover rounded-lg">
                    <button type="button" 
                            data-idx="${idx}" 
                            class="absolute top-2 right-2 bg-red-500 text-white rounded-full p-1 hover:bg-red-600 remove-image-btn"
                            onclick="removeImage(${idx})">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                `;
                imagePreview.appendChild(div);
            }
            reader.readAsDataURL(file);
        });

        // Update the file input with the selected files
        const dataTransfer = new DataTransfer();
        selectedFiles.forEach(file => dataTransfer.items.add(file));
        imageInput.files = dataTransfer.files;
    }

    function removeImage(index) {
        selectedFiles.splice(index, 1);
        renderImagePreview();
    }

    // Form submission validation
    document.querySelector('form').addEventListener('submit', function(e) {
        if (selectedFiles.length === 0) {
            e.preventDefault();
            alert('Please select at least one image for the product.');
            return false;
        }
        return true;
    });

    // Handle deal checkbox and deal fields
    const dealCheckbox = document.querySelector('input[name="is_deal"]');
    const dealFields = document.getElementById('deal-fields');

    dealCheckbox.addEventListener('change', function() {
        dealFields.classList.toggle('hidden', !this.checked);
        const discountInput = dealFields.querySelector('input[name="discount"]');
        const endDateInput = dealFields.querySelector('input[name="deal_end_date"]');
        discountInput.required = this.checked;
        endDateInput.required = this.checked;
    });

    // Handle variant addition
    let variantCount = 1;
    const addVariantBtn = document.getElementById('add-variant');
    const variantsContainer = document.getElementById('variants-container');

    addVariantBtn.addEventListener('click', function() {
        const variantHtml = `
            <div class="variant-item space-y-4 mt-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Quantity</label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <input type="number" 
                                   name="variants[${variantCount}][quantity]" 
                                   placeholder="Enter quantity"
                                   class="block w-full px-4 py-3 rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm placeholder-gray-400" 
                                   step="0.01" 
                                   min="0"
                                   required>
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Unit</label>
                        <select name="variants[${variantCount}][unit]" 
                                class="block w-full px-4 py-3 rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm text-gray-900"
                                required>
                            <option value="">Select unit</option>
                            <option value="g">Grams (g)</option>
                            <option value="kg">Kilograms (kg)</option>
                            <option value="ml">Milliliters (ml)</option>
                            <option value="l">Liters (l)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Price</label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <input type="number" 
                                   name="variants[${variantCount}][price]" 
                                   placeholder="Enter price"
                                   class="block w-full px-4 py-3 rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm placeholder-gray-400" 
                                   step="0.01" 
                                   min="0"
                                   required>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Stock</label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <input type="number" 
                                   name="variants[${variantCount}][stock]" 
                                   placeholder="Available quantity"
                                   class="block w-full px-4 py-3 rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm placeholder-gray-400" 
                                   min="0"
                                   required>
                        </div>
                    </div>
                </div>
                <button type="button" 
                        class="text-red-600 hover:text-red-800 text-sm font-medium remove-variant-btn"
                        onclick="removeVariant(this)">
                    Remove Variant
                </button>
            </div>
        `;
        variantsContainer.insertAdjacentHTML('beforeend', variantHtml);
        variantCount++;
    });

    function removeVariant(button) {
        button.closest('.variant-item').remove();
    }
</script>
@endpush
@endsection 