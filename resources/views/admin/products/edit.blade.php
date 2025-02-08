@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Edit Product</h1>
        <a href="{{ route('admin.products.index') }}" class="text-blue-500 hover:underline">
            Back to Products
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-lg p-6">
        <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Name</label>
                    <input type="text" name="name" value="{{ old('name', $product->name) }}" 
                           class="mt-1 block w-full rounded-md border-gray-300" required>
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Description</label>
                    <textarea name="description" rows="3" 
                              class="mt-1 block w-full rounded-md border-gray-300">{{ old('description', $product->description) }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Price</label>
                    <input type="number" name="price" value="{{ old('price', $product->price) }}" 
                           step="0.01" class="mt-1 block w-48 rounded-md border-gray-300" required>
                    @error('price')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Category</label>
                    <select name="category_id" class="mt-1 block w-full rounded-md border-gray-300" required>
                        <option value="">Select Category</option>
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

                <div>
                    <label class="block text-sm font-medium text-gray-700">Subcategory</label>
                    <select name="subcategory_id" class="mt-1 block w-full rounded-md border-gray-300" required>
                        <option value="">Select Subcategory</option>
                        @foreach($categories as $category)
                            @foreach($category->subcategories as $subcategory)
                                <option value="{{ $subcategory->id }}" 
                                        data-category="{{ $category->id }}"
                                        {{ old('subcategory_id', $product->subcategory_id) == $subcategory->id ? 'selected' : '' }}>
                                    {{ $subcategory->name }}
                                </option>
                            @endforeach
                        @endforeach
                    </select>
                    @error('subcategory_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Branches</label>
                    <div class="mt-2 space-y-2">
                        @foreach($branches as $branch)
                            <div class="flex items-center">
                                <input type="checkbox" name="branches[]" value="{{ $branch->id }}"
                                       class="rounded border-gray-300 text-blue-600"
                                       {{ in_array($branch->id, old('branches', $product->branches->pluck('id')->toArray())) ? 'checked' : '' }}>
                                <label class="ml-2 text-sm text-gray-700">{{ $branch->name }}</label>
                            </div>
                        @endforeach
                    </div>
                    @error('branches')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Status</label>
                    <div class="mt-2">
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="is_active" value="1" 
                                   class="rounded border-gray-300 text-blue-600"
                                   {{ old('is_active', $product->is_active) ? 'checked' : '' }}>
                            <span class="ml-2">Active</span>
                        </label>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Product Image</label>
                    @if($product->image_url)
                        <div class="mt-2">
                            <img src="{{ $product->image_url }}" alt="{{ $product->name }}" 
                                 class="h-32 w-32 object-cover rounded">
                        </div>
                    @endif
                    <div class="mt-1 flex items-center">
                        <input type="file" name="image" accept="image/*"
                               class="block w-full text-sm text-gray-500
                                      file:mr-4 file:py-2 file:px-4
                                      file:rounded-full file:border-0
                                      file:text-sm file:font-semibold
                                      file:bg-blue-50 file:text-blue-700
                                      hover:file:bg-blue-100">
                    </div>
                    @error('image')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                        Update Product
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    // Add dynamic subcategory filtering based on selected category
    const categorySelect = document.querySelector('select[name="category_id"]');
    const subcategorySelect = document.querySelector('select[name="subcategory_id"]');
    const subcategoryOptions = Array.from(subcategorySelect.options);

    categorySelect.addEventListener('change', function() {
        const selectedCategory = this.value;
        
        subcategorySelect.innerHTML = '<option value="">Select Subcategory</option>';
        
        subcategoryOptions.forEach(option => {
            if (option.dataset.category === selectedCategory) {
                subcategorySelect.appendChild(option.cloneNode(true));
            }
        });
    });
</script>
@endpush
@endsection 