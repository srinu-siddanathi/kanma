<div class="variant-row bg-white border border-gray-200 rounded-lg shadow-sm hover:shadow-md transition-shadow duration-200">
    <div class="p-6">
        <div class="flex justify-between items-start mb-6">
            <h4 class="text-lg font-medium text-gray-900">Variant #{{ $index + 1 }}</h4>
            <button type="button" onclick="removeVariant(this)" 
                    class="inline-flex items-center text-sm text-red-600 hover:text-red-900 transition-colors duration-200">
                <svg class="h-5 w-5 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
                Remove
            </button>
        </div>

        @php
            \Log::info('Variant data in partial:', [
                'variant' => $variant,
                'index' => $index,
                'variant_id' => $variant->id ?? null
            ]);
        @endphp

        @if(isset($variant) && $variant->id)
            <input type="hidden" name="variants[{{ $index }}][id]" value="{{ $variant->id }}">
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Quantity</label>
                <div class="mt-1 relative rounded-md shadow-sm">
                    <input type="number" 
                           name="variants[{{ $index }}][quantity]" 
                           value="{{ old("variants.$index.quantity", $variant->quantity ?? '') }}"
                           placeholder="Enter quantity"
                           class="block w-full px-4 py-3 rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm placeholder-gray-400" 
                           step="0.01" 
                           min="0"
                           required>
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                        <span class="text-gray-500 sm:text-sm"></span>
                    </div>
                </div>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Unit</label>
                <select name="variants[{{ $index }}][unit]" 
                        class="block w-full px-4 py-3 rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm text-gray-900"
                        required>
                    <option value="">Select unit</option>
                    @foreach(['g' => 'Grams (g)', 'kg' => 'Kilograms (kg)', 'ml' => 'Milliliters (ml)', 'l' => 'Liters (l)', 'pieces' => 'Pieces'] as $value => $label)
                        <option value="{{ $value }}" 
                            {{ old("variants.$index.unit", $variant->unit ?? '') == $value ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Price</label>
                <div class="mt-1 relative rounded-md shadow-sm">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <span class="text-gray-500 sm:text-sm">₹</span>
                    </div>
                    <input type="number" 
                           name="variants[{{ $index }}][price]" 
                           value="{{ old("variants.$index.price", $variant->price ?? '') }}"
                           placeholder="0.00"
                           class="block w-full pl-8 pr-12 py-3 rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm placeholder-gray-400" 
                           step="0.01"
                           min="0" 
                           required>
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                        <span class="text-gray-500 sm:text-sm">INR</span>
                    </div>
                </div>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Stock</label>
                <div class="mt-1 relative rounded-md shadow-sm">
                    <input type="number" 
                           name="variants[{{ $index }}][stock]" 
                           value="{{ old("variants.$index.stock", $variant->stock ?? '') }}"
                           placeholder="Available quantity"
                           class="block w-full px-4 py-3 rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm placeholder-gray-400" 
                           min="0"
                           required>
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                        <span class="text-gray-500 sm:text-sm">units</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> 