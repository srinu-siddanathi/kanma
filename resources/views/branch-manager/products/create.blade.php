@extends('layouts.branch-manager')

@section('title', 'Add Product')

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="px-4 py-6 sm:px-0">
        <div class="mb-4">
            <h2 class="text-2xl font-bold text-gray-900">Add New Product</h2>
        </div>

        @if ($errors->any())
        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        @if(session('error'))
        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
            {{ session('error') }}
        </div>
        @endif

        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <form action="{{ route('branch.products.store') }}" method="POST" enctype="multipart/form-data" class="p-6">
                @csrf
                <div class="grid grid-cols-1 gap-6">
                    <div>
                        <label for="category_id" class="block text-sm font-medium text-gray-700">Category</label>
                        <select name="category_id" id="category_id" required
                            class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                            onchange="loadSubcategories(this.value)">
                            <option value="">Select Category</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="subcategory_id" class="block text-sm font-medium text-gray-700">Subcategory</label>
                        <select name="subcategory_id" id="subcategory_id" required
                            class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            <option value="">Select Category First</option>
                        </select>
                    </div>

                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Product Name</label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required
                            class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                        <textarea name="description" id="description" rows="3"
                            class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">{{ old('description') }}</textarea>
                    </div>

                    <div>
                        <label for="price" class="block text-sm font-medium text-gray-700">Price (₹)</label>
                        <input type="number" name="price" id="price" value="{{ old('price') }}" required step="0.01"
                            class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                    </div>

                    <div>
                        <label for="image" class="block text-sm font-medium text-gray-700">Product Image</label>
                        <input type="file" name="image" id="image" accept="image/*"
                            class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300">
                        <p class="mt-1 text-sm text-gray-500">Maximum file size: 2MB. Supported formats: JPG, PNG</p>
                    </div>

                    <div>
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="is_active" value="1" checked
                                class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            <span class="ml-2">Active</span>
                        </label>
                    </div>
                </div>

                <div class="mt-6 flex items-center justify-end">
                    <a href="{{ route('branch.products') }}" class="mr-4 text-sm text-gray-700 hover:text-gray-900">
                        Cancel
                    </a>
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Create Product
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// For debugging
console.log('Categories:', @json($categories));

const categories = @json($categories);

function loadSubcategories(categoryId) {
    console.log('Loading subcategories for category:', categoryId);
    
    if (!categoryId) {
        document.getElementById('subcategory_id').innerHTML = '<option value="">Select Category First</option>';
        return;
    }

    const category = categories.find(c => c.id == categoryId);
    console.log('Found category:', category);
    
    let options = '<option value="">Select Subcategory</option>';
    
    if (category && category.subcategories) {
        console.log('Subcategories:', category.subcategories);
        category.subcategories.forEach(subcategory => {
            options += `<option value="${subcategory.id}">${subcategory.name}</option>`;
        });
    }
    
    document.getElementById('subcategory_id').innerHTML = options;
}

// Load subcategories if category is pre-selected
document.addEventListener('DOMContentLoaded', function() {
    const categoryId = document.getElementById('category_id').value;
    if (categoryId) {
        loadSubcategories(categoryId);
    }
});
</script>
@endpush 