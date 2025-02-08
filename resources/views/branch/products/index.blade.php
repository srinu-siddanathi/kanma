@extends('layouts.branch-manager')

@section('content')
<div class="container mx-auto px-4">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Branch Products</h1>
        <div class="space-x-4">
            <a href="{{ route('branch.products.create') }}" class="bg-green-500 text-white px-4 py-2 rounded">
                Create New Product
            </a>
            <a href="{{ route('branch.products.available') }}" class="bg-blue-500 text-white px-4 py-2 rounded">
                Add Existing Products
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
        {{ session('success') }}
    </div>
    @endif

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Image
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($products as $product)
                <tr>
                    <td class="px-6 py-4">
                        @if($product->image_url)
                        <img src="{{ $product->image_url }}" alt="{{ $product->name }}"
                            class="h-12 w-12 object-cover rounded">
                        @else
                        <div class="h-12 w-12 bg-gray-100 rounded flex items-center justify-center">
                            <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        @endif
                    </td>
                    <td class="px-6 py-4">{{ $product->name }}</td>
                    <td class="px-6 py-4">
                        {{ $product->category->name }}
                        <span class="text-gray-500 text-sm">({{ $product->subcategory->name }})</span>
                    </td>
                    <td class="px-6 py-4">
                        <form action="{{ route('branch.products.update-price', $product) }}" method="POST"
                            class="flex items-center">
                            @csrf
                            @method('PUT')
                            <input type="number" name="price" value="{{ $product->pivot->price }}" step="0.01"
                                class="w-24 rounded border-gray-300 mr-2">
                            <button type="submit" class="text-blue-600 hover:text-blue-900">Update</button>
                        </form>
                    </td>
                    <td class="px-6 py-4">
                        <form action="{{ route('branch.products.toggle', $product) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <button type="submit"
                                class="{{ $product->pivot->is_active ? 'text-green-600' : 'text-red-600' }} hover:underline">
                                {{ $product->pivot->is_active ? 'Active' : 'Inactive' }}
                            </button>
                        </form>
                    </td>
                    <td class="px-6 py-4 text-sm">
                        <div class="flex space-x-3">
                            @if($product->branch_id == auth()->user()->branch->id)
                            <a href="{{ route('branch.products.edit', $product) }}"
                                class="text-indigo-600 hover:text-indigo-900">Edit</a>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $products->links() }}
    </div>
</div>
@endsection