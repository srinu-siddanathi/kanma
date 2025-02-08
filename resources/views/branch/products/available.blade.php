@extends('layouts.branch-manager')

@section('content')
<div class="container mx-auto px-4">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Available Products</h1>
        <a href="{{ route('branch.products.index') }}" class="text-blue-500 hover:underline">
            Back to Products
        </a>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Base Price</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($availableProducts as $product)
                <tr>
                    <td class="px-6 py-4">{{ $product->name }}</td>
                    <td class="px-6 py-4">{{ $product->category->name }}</td>
                    <td class="px-6 py-4">{{ $product->price }}</td>
                    <td class="px-6 py-4">
                        <form action="{{ route('branch.products.add') }}" method="POST" class="flex items-center">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            <input type="number" name="price" value="{{ $product->price }}" 
                                   class="w-24 rounded border-gray-300 mr-2" 
                                   placeholder="Set Price">
                            <button type="submit" 
                                class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                                Add to Branch
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $availableProducts->links() }}
    </div>
</div>
@endsection 