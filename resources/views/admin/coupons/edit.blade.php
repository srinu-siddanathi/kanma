@extends('layouts.admin')

@section('title', 'Edit Coupon')

@section('content')
<div class="max-w-4xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="px-4 py-6 sm:px-0">
        <!-- Header -->
        <div class="mb-6 flex items-center justify-between">
            <h2 class="text-2xl font-bold text-gray-900">Edit Coupon: {{ $coupon->name }}</h2>
            <a href="{{ route('admin.coupons.index') }}" 
               class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to Coupons
            </a>
        </div>

        <div class="bg-white shadow sm:rounded-lg">
            <form action="{{ route('admin.coupons.update', $coupon) }}" method="POST" class="space-y-6 p-6">
                @csrf
                @method('PUT')

                <!-- Basic Information -->
                <div class="border-b border-gray-200 pb-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Basic Information</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="code" class="block text-sm font-medium text-gray-700 mb-1">
                                Coupon Code <span class="text-red-500">*</span>
                            </label>
                            <div class="flex">
                                <input type="text" name="code" id="code" value="{{ old('code', $coupon->code) }}" 
                                       class="flex-1 block w-full px-4 py-3 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 placeholder-gray-400 text-gray-900"
                                       placeholder="Enter coupon code" maxlength="50">
                                <button type="button" id="generate-code" 
                                        class="ml-2 px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 text-sm">
                                    Generate
                                </button>
                            </div>
                            @error('code')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                                Coupon Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="name" id="name" value="{{ old('name', $coupon->name) }}" required
                                   class="block w-full px-4 py-3 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 placeholder-gray-400 text-gray-900"
                                   placeholder="Enter coupon name">
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-6">
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                            Description
                        </label>
                        <textarea name="description" id="description" rows="3"
                                  class="block w-full px-4 py-3 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 placeholder-gray-400 text-gray-900"
                                  placeholder="Enter coupon description">{{ old('description', $coupon->description) }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Discount Configuration -->
                <div class="border-b border-gray-200 pb-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Discount Configuration</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label for="type" class="block text-sm font-medium text-gray-700 mb-1">
                                Discount Type <span class="text-red-500">*</span>
                            </label>
                            <select name="type" id="type" required
                                    class="block w-full px-4 py-3 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-gray-900">
                                <option value="">Select type</option>
                                <option value="percentage" {{ old('type', $coupon->type) === 'percentage' ? 'selected' : '' }}>Percentage</option>
                                <option value="fixed" {{ old('type', $coupon->type) === 'fixed' ? 'selected' : '' }}>Fixed Amount</option>
                            </select>
                            @error('type')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="value" class="block text-sm font-medium text-gray-700 mb-1">
                                Discount Value <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="number" name="value" id="value" value="{{ old('value', $coupon->value) }}" required min="0" step="0.01"
                                       class="block w-full px-4 py-3 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 placeholder-gray-400 text-gray-900"
                                       placeholder="Enter discount value">
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm" id="value-suffix">{{ $coupon->type === 'percentage' ? '%' : '₹' }}</span>
                                </div>
                            </div>
                            @error('value')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="minimum_order_amount" class="block text-sm font-medium text-gray-700 mb-1">
                                Minimum Order Amount <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="number" name="minimum_order_amount" id="minimum_order_amount" value="{{ old('minimum_order_amount', $coupon->minimum_order_amount) }}" required min="0" step="0.01"
                                       class="block w-full px-4 py-3 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 placeholder-gray-400 text-gray-900"
                                       placeholder="Enter minimum order amount">
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">₹</span>
                                </div>
                            </div>
                            @error('minimum_order_amount')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-6">
                        <label for="maximum_discount" class="block text-sm font-medium text-gray-700 mb-1">
                            Maximum Discount (Optional)
                        </label>
                        <div class="relative">
                            <input type="number" name="maximum_discount" id="maximum_discount" value="{{ old('maximum_discount', $coupon->maximum_discount) }}" min="0" step="0.01"
                                   class="block w-full px-4 py-3 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 placeholder-gray-400 text-gray-900"
                                   placeholder="Enter maximum discount amount">
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">₹</span>
                            </div>
                        </div>
                        <p class="mt-1 text-sm text-gray-500">Leave empty for no maximum limit</p>
                        @error('maximum_discount')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Usage Limits -->
                <div class="border-b border-gray-200 pb-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Usage Limits</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="usage_limit" class="block text-sm font-medium text-gray-700 mb-1">
                                Total Usage Limit (Optional)
                            </label>
                            <input type="number" name="usage_limit" id="usage_limit" value="{{ old('usage_limit', $coupon->usage_limit) }}" min="1"
                                   class="block w-full px-4 py-3 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 placeholder-gray-400 text-gray-900"
                                   placeholder="Enter total usage limit">
                            <p class="mt-1 text-sm text-gray-500">Leave empty for unlimited usage</p>
                            @error('usage_limit')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="per_user_limit" class="block text-sm font-medium text-gray-700 mb-1">
                                Per User Limit <span class="text-red-500">*</span>
                            </label>
                            <input type="number" name="per_user_limit" id="per_user_limit" value="{{ old('per_user_limit', $coupon->per_user_limit) }}" required min="1"
                                   class="block w-full px-4 py-3 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 placeholder-gray-400 text-gray-900"
                                   placeholder="Enter per user limit">
                            @error('per_user_limit')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-6">
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="is_first_time_only" value="1" {{ old('is_first_time_only', $coupon->is_first_time_only) ? 'checked' : '' }}
                                   class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            <span class="ml-2 text-sm text-gray-700">First-time customers only</span>
                        </label>
                        @error('is_first_time_only')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Validity Period -->
                <div class="border-b border-gray-200 pb-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Validity Period</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="valid_from" class="block text-sm font-medium text-gray-700 mb-1">
                                Valid From <span class="text-red-500">*</span>
                            </label>
                            <input type="datetime-local" name="valid_from" id="valid_from" 
                                   value="{{ old('valid_from', $coupon->valid_from->format('Y-m-d\TH:i')) }}" required
                                   class="block w-full px-4 py-3 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-gray-900">
                            @error('valid_from')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="valid_until" class="block text-sm font-medium text-gray-700 mb-1">
                                Valid Until <span class="text-red-500">*</span>
                            </label>
                            <input type="datetime-local" name="valid_until" id="valid_until" 
                                   value="{{ old('valid_until', $coupon->valid_until->format('Y-m-d\TH:i')) }}" required
                                   class="block w-full px-4 py-3 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-gray-900">
                            @error('valid_until')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Status -->
                <div class="pb-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Status</h3>
                    
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $coupon->is_active) ? 'checked' : '' }}
                               class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        <span class="ml-2 text-sm text-gray-700">Active</span>
                    </label>
                    @error('is_active')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submit Buttons -->
                <div class="flex justify-end space-x-3">
                    <a href="{{ route('admin.coupons.index') }}" 
                       class="px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                        Update Coupon
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const typeSelect = document.getElementById('type');
    const valueInput = document.getElementById('value');
    const valueSuffix = document.getElementById('value-suffix');
    const generateBtn = document.getElementById('generate-code');
    const codeInput = document.getElementById('code');

    // Update value suffix based on type
    typeSelect.addEventListener('change', function() {
        if (this.value === 'percentage') {
            valueSuffix.textContent = '%';
            valueInput.placeholder = 'Enter percentage (e.g., 10)';
        } else if (this.value === 'fixed') {
            valueSuffix.textContent = '₹';
            valueInput.placeholder = 'Enter amount (e.g., 50)';
        }
    });

    // Generate coupon code
    generateBtn.addEventListener('click', function() {
        fetch('{{ route("admin.coupons.generate-code") }}')
            .then(response => response.json())
            .then(data => {
                codeInput.value = data.code;
            })
            .catch(error => {
                console.error('Error generating code:', error);
            });
    });
});
</script>
@endsection 