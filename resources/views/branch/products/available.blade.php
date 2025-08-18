@extends('layouts.branch-manager')

@section('content')
<div class="container mx-auto px-4">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Available Products</h1>
        <div class="flex items-center space-x-4">
            <!-- Search Form -->
            <form method="GET" action="{{ route('branch.products.available') }}" class="flex items-center">
                <div class="relative">
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}"
                           placeholder="Search products..." 
                           class="w-64 pl-10 pr-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                </div>
                <button type="submit" 
                        class="ml-2 inline-flex items-center px-3 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    Search
                </button>
                @if(request('search'))
                    <a href="{{ route('branch.products.available') }}" 
                       class="ml-2 inline-flex items-center px-3 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        Clear
                    </a>
                @endif
            </form>
            
            <a href="{{ route('branch.products.index') }}" class="text-blue-500 hover:underline">
                Back to Products
            </a>
        </div>
    </div>

    @if(request('search'))
    <div class="mb-4 bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded">
        <div class="flex items-center justify-between">
            <span>Search results for: "<strong>{{ request('search') }}</strong>" ({{ $availableProducts->total() }} products found)</span>
            <a href="{{ route('branch.products.available') }}" class="text-blue-800 hover:text-blue-900 underline">Clear search</a>
        </div>
    </div>
    @endif

    @if($availableProducts->isEmpty())
        <div class="text-center py-8">
            <p class="text-gray-500 text-lg">No available products found.</p>
        </div>
    @else
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
                        <td class="px-6 py-4">{{ $product->category ? $product->category->name : 'No Category' }}</td>
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
    @endif
</div>
@endsection 