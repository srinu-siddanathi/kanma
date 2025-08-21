@extends('layouts.admin')

@section('title', 'Shop Details')

@section('content')
<div class="container mx-auto px-4">
    <div class="max-w-4xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold">Shop Details</h2>
            <div class="flex space-x-3">
                <a href="{{ route('admin.shops.edit', $shop) }}" 
                   class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                    Edit Shop
                </a>
                <a href="{{ route('admin.shops.index') }}" class="text-indigo-600 hover:text-indigo-900">
                    ← Back to Shops
                </a>
            </div>
        </div>

        <div class="bg-white shadow rounded-lg overflow-hidden mb-6">
            <div class="p-6">
                <div class="flex items-center mb-6">
                    @if($shop->image_path)
                        <img src="{{ asset($shop->image_path) }}" 
                             alt="{{ $shop->name }}" 
                             class="h-24 w-24 object-cover rounded-lg mr-6">
                    @endif
                    <div>
                        <h3 class="text-xl font-bold text-gray-900">{{ $shop->name }}</h3>
                        <p class="text-sm text-gray-500">Created {{ $shop->created_at->format('M d, Y') }}</p>
                    </div>
                </div>

                <div class="border-t border-gray-200 pt-6">
                    <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Owner Name</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $shop->user->name }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Email</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $shop->user->email }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Phone</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $shop->phone }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Status</dt>
                            <dd class="mt-1">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    {{ $shop->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $shop->is_active ? 'Active' : 'Pending' }}
                                </span>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Verification</dt>
                            <dd class="mt-1">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    {{ $shop->is_verified ? 'bg-blue-100 text-blue-800' : 'bg-yellow-100 text-yellow-800' }}">
                                    {{ $shop->is_verified ? 'Verified' : 'Unverified' }}
                                </span>
                            </dd>
                        </div>
                        <div class="sm:col-span-2">
                            <dt class="text-sm font-medium text-gray-500">Address</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $shop->address }}</dd>
                        </div>
                        <div class="sm:col-span-2">
                            <dt class="text-sm font-medium text-gray-500">Description</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $shop->description ?? 'No description provided' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Latitude</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $shop->latitude ?? 'Not set' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Longitude</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $shop->longitude ?? 'Not set' }}</dd>
                        </div>
                        @if($shop->latitude && $shop->longitude)
                        <div class="sm:col-span-2">
                            <dt class="text-sm font-medium text-gray-500">Location</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                <a href="https://www.google.com/maps?q={{ $shop->latitude }},{{ $shop->longitude }}" 
                                   target="_blank" 
                                   class="text-indigo-600 hover:text-indigo-900">
                                    View on Google Maps
                                </a>
                            </dd>
                        </div>
                        @endif
                        @if($shop->working_hours)
                        <div class="sm:col-span-2">
                            <dt class="text-sm font-medium text-gray-500">Working Hours</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                <div class="grid grid-cols-1 gap-1 sm:grid-cols-2">
                                    @foreach($shop->working_hours as $day => $hours)
                                        <div class="flex justify-between">
                                            <span class="capitalize">{{ $day }}:</span>
                                            <span>{{ $hours['open'] }} - {{ $hours['close'] }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </dd>
                        </div>
                        @endif
                    </dl>
                </div>
            </div>
        </div>

        <div class="bg-white shadow rounded-lg overflow-hidden">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Products ({{ $products->total() }})</h3>
                    <form action="{{ route('admin.products.verify-multiple') }}" method="POST" id="bulkVerifyForm">
                        @csrf
                        <button type="submit" 
                            class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 hidden" 
                            id="verifySelectedBtn">
                            Verify Selected Products
                        </button>
                    </form>
                </div>
                
                @if($products->isEmpty())
                    <p class="text-gray-500 text-center py-4">No products added yet.</p>
                @else
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <input type="checkbox" id="selectAll" class="rounded border-gray-300">
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($products as $product)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($product->status === 'pending')
                                        <input type="checkbox" 
                                            class="product-checkbox rounded border-gray-300" 
                                            value="{{ $product->id }}">
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex space-x-1 mr-3">
                                            @if($product->image_path)
                                                <img src="{{ asset($product->image_path) }}" 
                                                     alt="{{ $product->name }}" 
                                                     class="h-10 w-10 object-cover rounded border">
                                            @endif
                                            @foreach($product->images->take(2) as $image)
                                                <img src="{{ asset($image->image_path) }}" 
                                                     alt="{{ $product->name }}" 
                                                     class="h-10 w-10 object-cover rounded border">
                                            @endforeach
                                            @if($product->images->count() > 2)
                                                <div class="h-10 w-10 bg-gray-100 rounded border flex items-center justify-center">
                                                    <span class="text-xs text-gray-600">+{{ $product->images->count() - 2 }}</span>
                                                </div>
                                            @endif
                                            @if(!$product->image_path && $product->images->count() == 0)
                                                <div class="h-10 w-10 bg-gray-100 rounded border flex items-center justify-center">
                                                    <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                    </svg>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="text-sm font-medium text-gray-900">{{ $product->name }}</div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $product->category->name }}</div>
                                    
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    ₹{{ number_format($product->price, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        {{ $product->status === 'verified' ? 'bg-green-100 text-green-800' : 
                                           ($product->status === 'rejected' ? 'bg-red-100 text-red-800' : 
                                           'bg-yellow-100 text-yellow-800') }}">
                                        {{ ucfirst($product->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <button onclick="viewProduct({{ $product->id }})" 
                                            class="text-indigo-600 hover:text-indigo-900 mr-3">
                                        View
                                    </button>
                                    @if($product->status === 'pending')
                                        <button onclick="verifyProduct({{ $product->id }})" 
                                                class="text-green-600 hover:text-green-900 mr-3">
                                            Verify
                                        </button>
                                        <button onclick="openRejectModal({{ $product->id }})"
                                                class="text-red-600 hover:text-red-900">
                                            Reject
                                        </button>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <!-- Pagination -->
                    <div class="px-6 py-4">
                        {{ $products->links() }}
                    </div>
                @endif
            </div>
        </div>

        <div class="mt-6">
            <div class="bg-white shadow rounded-lg p-6">
                <div class="flex justify-between items-center">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900">Verification Status</h3>
                        <p class="mt-1 text-sm text-gray-500">
                            All products must be verified before the shop can be approved.
                        </p>
                        <p class="mt-2 text-sm">
                            Products Verified: 
                            <span class="font-medium">
                                {{ $shop->products->where('status', 'verified')->count() }}/{{ $shop->products->count() }}
                            </span>
                        </p>
                    </div>
                    <div class="flex space-x-3">
                        @if(!$shop->is_active)
                            @if($shop->products->count() > 0 && $shop->products->where('status', 'verified')->count() === $shop->products->count())
                                <form action="{{ route('admin.shops.verify', $shop) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                                        Verify Shop
                                    </button>
                                </form>
                            @else
                                <button type="button" 
                                    class="bg-gray-400 text-white px-4 py-2 rounded-md cursor-not-allowed" 
                                    title="All products must be verified first">
                                    Verify Shop
                                </button>
                            @endif
                        @endif

                        @if($shop->is_verified && !$shop->is_active)
                            <form action="{{ route('admin.shops.approve', $shop) }}" method="POST">
                                @csrf
                                <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700">
                                    Approve Shop
                                </button>
                            </form>
                        @elseif($shop->is_active)
                            <form action="{{ route('admin.shops.reject', $shop) }}" method="POST">
                                @csrf
                                <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700">
                                    Reject Shop
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add the Reject Modal -->
<div id="rejectModal" class="fixed inset-0 bg-gray-500 bg-opacity-75 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg p-8 max-w-md w-full mx-4 relative">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Reject Product</h3>
            <form id="rejectForm" method="POST">
                @csrf
                <div class="mb-4">
                    <label for="rejection_reason" class="block text-sm font-medium text-gray-700 mb-2">
                        Reason for Rejection
                    </label>
                    <textarea
                        name="rejection_reason"
                        id="rejection_reason"
                        rows="4"
                        class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                        required
                    ></textarea>
                </div>
                <div class="flex justify-end space-x-3">
                    <button
                        type="button"
                        onclick="closeRejectModal()"
                        class="bg-gray-200 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-300"
                    >
                        Cancel
                    </button>
                    <button
                        type="submit"
                        class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700"
                    >
                        Reject Product
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Product View Modal -->
<div id="productViewModal" class="fixed inset-0 bg-gray-500 bg-opacity-75 hidden" aria-hidden="true">
    <div class="flex items-center justify-center min-h-screen">
        <div class="bg-white rounded-lg overflow-hidden shadow-xl max-w-2xl w-full mx-4">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <h3 class="text-lg font-medium text-gray-900" id="productModalTitle">Product Details</h3>
                    <button onclick="closeProductModal()" class="text-gray-400 hover:text-gray-500">
                        <span class="sr-only">Close</span>
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
            <div class="px-6 py-4" id="productModalContent">
                <div class="flex items-start">
                    <div class="w-1/3" id="productImage">
                        <!-- Product image will be inserted here -->
                    </div>
                    <div class="w-2/3 pl-6">
                        <dl class="space-y-4">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Name</dt>
                                <dd class="mt-1 text-sm text-gray-900" id="productName"></dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Description</dt>
                                <dd class="mt-1 text-sm text-gray-900" id="productDescription"></dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Category</dt>
                                <dd class="mt-1 text-sm text-gray-900" id="productCategory"></dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Price</dt>
                                <dd class="mt-1 text-sm text-gray-900" id="productPrice"></dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Status</dt>
                                <dd class="mt-1" id="productStatus"></dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAll = document.getElementById('selectAll');
    const productCheckboxes = document.querySelectorAll('.product-checkbox');
    const verifySelectedBtn = document.getElementById('verifySelectedBtn');
    const bulkVerifyForm = document.getElementById('bulkVerifyForm');

    // Handle "Select All" checkbox
    selectAll.addEventListener('change', function() {
        productCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        updateVerifyButtonVisibility();
    });

    // Handle individual checkboxes
    productCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updateVerifyButtonVisibility();
            
            // Update "Select All" checkbox
            const allChecked = Array.from(productCheckboxes).every(c => c.checked);
            selectAll.checked = allChecked;
        });
    });

    function updateVerifyButtonVisibility() {
        const checkedBoxes = document.querySelectorAll('.product-checkbox:checked');
        verifySelectedBtn.classList.toggle('hidden', checkedBoxes.length === 0);
    }

    // Handle form submission
    bulkVerifyForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const checkedBoxes = document.querySelectorAll('.product-checkbox:checked');
        if (checkedBoxes.length === 0) {
            return;
        }

        // Create hidden inputs for selected products
        checkedBoxes.forEach(checkbox => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'product_ids[]';
            input.value = checkbox.value;
            this.appendChild(input);
        });

        this.submit();
    });

    // Add these functions to the global scope
    window.verifyProduct = function(productId) {
        if (confirm('Are you sure you want to verify this product?')) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = "{{ route('admin.products.verify', ['product' => ':id']) }}".replace(':id', productId);
            
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';
            
            form.appendChild(csrfToken);
            document.body.appendChild(form);
            form.submit();
        }
    }

    window.openRejectModal = function(productId) {
        const modal = document.getElementById('rejectModal');
        const form = document.getElementById('rejectForm');
        form.action = "{{ route('admin.products.reject', ['product' => ':id']) }}".replace(':id', productId);
        modal.classList.remove('hidden');
    }

    window.closeRejectModal = function() {
        const modal = document.getElementById('rejectModal');
        modal.classList.add('hidden');
        document.getElementById('rejection_reason').value = '';
    }

    // Close modal when clicking outside
    document.getElementById('rejectModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeRejectModal();
        }
    });
});

async function viewProduct(productId) {
    try {
        const response = await fetch(`/admin/products/${productId}`);
        const data = await response.json();
        const product = data.data;

        // Update modal content
        document.getElementById('productName').textContent = product.name;
        document.getElementById('productDescription').textContent = product.description || 'No description available';
        document.getElementById('productCategory').textContent = product.category ? product.category.name : 'No category';
        document.getElementById('productPrice').textContent = `₹${parseFloat(product.price).toFixed(2)}`;
        
        // Update status with badge
        const statusBadge = document.createElement('span');
        statusBadge.className = `px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${
            product.status === 'verified' ? 'bg-green-100 text-green-800' : 
            (product.status === 'rejected' ? 'bg-red-100 text-red-800' : 
            'bg-yellow-100 text-yellow-800')
        }`;
        statusBadge.textContent = product.status.charAt(0).toUpperCase() + product.status.slice(1);
        document.getElementById('productStatus').innerHTML = '';
        document.getElementById('productStatus').appendChild(statusBadge);

        // Update images
        const imageContainer = document.getElementById('productImage');
        let imageHtml = '';
        
        // Show main image if exists
        if (product.image_path) {
            imageHtml += `<img src="${product.image_path.startsWith('http') ? product.image_path : '/' + product.image_path}" alt="${product.name}" class="w-full h-auto rounded-lg mb-4">`;
        }
        
        // Show additional images if any
        if (product.images && product.images.length > 0) {
            imageHtml += '<div class="grid grid-cols-3 gap-2">';
            product.images.forEach(image => {
                imageHtml += `<img src="${image.image_path.startsWith('http') ? image.image_path : '/' + image.image_path}" alt="${product.name}" class="w-full h-24 object-cover rounded border">`;
            });
            imageHtml += '</div>';
        }
        
        // Show placeholder if no images
        if (!product.image_path && (!product.images || product.images.length === 0)) {
            imageHtml = `<div class="w-full h-48 bg-gray-100 rounded-lg flex items-center justify-center">
                <svg class="h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </div>`;
        }
        
        imageContainer.innerHTML = imageHtml;

        // Show modal
        document.getElementById('productViewModal').classList.remove('hidden');
    } catch (error) {
        console.error('Error fetching product details:', error);
    }
}

function closeProductModal() {
    document.getElementById('productViewModal').classList.add('hidden');
}

// Close modal when clicking outside
document.getElementById('productViewModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeProductModal();
    }
});


</script>
@endpush
@endsection 