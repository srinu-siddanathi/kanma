@extends('layouts.admin')

@section('title', 'Create Subscription Plan')

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="px-4 py-6 sm:px-0">
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-900">Create New Subscription Plan</h2>
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

        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <form action="{{ route('admin.subscription-plans.store') }}" method="POST" class="p-6">
                @csrf
                <div class="grid grid-cols-1 gap-6">
                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700">Plan Type</label>
                        <select name="type" id="type" required class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            <option value="">Select Plan Type</option>
                            <option value="{{ App\Models\SubscriptionPlan::TYPE_KATHA }}">Katha Plan</option>
                            <option value="{{ App\Models\SubscriptionPlan::TYPE_O2 }}">O2 Plan</option>
                        </select>
                    </div>

                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required
                            class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                        <textarea name="description" id="description" rows="3" required
                            class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">{{ old('description') }}</textarea>
                    </div>

                    <div>
                        <label for="price" class="block text-sm font-medium text-gray-700">Price (₹)</label>
                        <input type="number" name="price" id="price" value="{{ old('price') }}" required min="0" step="0.01"
                            class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                    </div>

                    <div>
                        <label for="duration_in_days" class="block text-sm font-medium text-gray-700">Duration (Days)</label>
                        <input type="number" name="duration_in_days" id="duration_in_days" value="{{ old('duration_in_days') }}" required min="1"
                            class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                    </div>

                    <div>
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="is_active" value="1" checked
                                class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            <span class="ml-2">Active</span>
                        </label>
                    </div>

                    <div class="katha-only">
                        <label for="wallet_addon" class="block text-sm font-medium text-gray-700">Wallet Addon (₹)</label>
                        <input type="number" name="wallet_addon" id="wallet_addon" value="{{ old('wallet_addon', 0) }}" min="0" step="0.01"
                            class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                    </div>

                    <div class="katha-only">
                        <label for="free_orders" class="block text-sm font-medium text-gray-700">Free Orders</label>
                        <input type="number" name="free_orders" id="free_orders" value="{{ old('free_orders', 0) }}" min="0"
                            class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                    </div>

                    <div class="katha-only">
                        <label for="free_delivery_radius" class="block text-sm font-medium text-gray-700">Free Delivery Radius (KM)</label>
                        <input type="number" name="free_delivery_radius" id="free_delivery_radius" value="{{ old('free_delivery_radius', 0) }}" min="0"
                            class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                    </div>
                </div>

                <div class="mt-6 flex items-center justify-end">
                    <a href="{{ route('admin.subscription-plans.index') }}" class="mr-4 text-sm text-gray-700 hover:text-gray-900">
                        Cancel
                    </a>
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Create Plan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const typeSelect = document.getElementById('type');
    const kathaFields = document.querySelectorAll('.katha-only');
    
    function toggleKathaFields() {
        const isKatha = typeSelect.value === '{{ App\Models\SubscriptionPlan::TYPE_KATHA }}';
        kathaFields.forEach(field => {
            field.style.display = isKatha ? 'block' : 'none';
            // Make fields not required when hidden
            const inputs = field.querySelectorAll('input');
            inputs.forEach(input => {
                if (!isKatha) {
                    input.value = '0';
                }
            });
        });
    }

    // Initial state
    toggleKathaFields();
    
    // On change
    typeSelect.addEventListener('change', toggleKathaFields);
});
</script>
@endpush
@endsection 