@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8">
    <div class="bg-white rounded-lg shadow-sm">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg font-medium leading-6 text-gray-900 mb-6">Delivery Settings</h3>
            
            <form action="{{ route('admin.settings.update') }}" method="POST">
                @csrf
                @method('PUT')

                @foreach($settings['delivery'] as $setting)
                <div class="mb-6">
                    <label for="{{ $setting->key }}" class="block text-sm font-medium text-gray-700 mb-1">
                        {{ ucwords(str_replace('_', ' ', $setting->key)) }}
                        @if($setting->description)
                            <span class="ml-1 text-gray-400 cursor-help" title="{{ $setting->description }}">
                                <svg class="h-4 w-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </span>
                        @endif
                    </label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                        <input type="number" 
                               class="block w-full pr-12 rounded-md border-gray-300 focus:ring-brand-yellow focus:border-brand-yellow sm:text-sm @error('settings.' . $setting->key) border-red-300 text-red-900 placeholder-red-300 focus:ring-red-500 focus:border-red-500 @enderror" 
                               id="{{ $setting->key }}" 
                               name="settings[{{ $setting->key }}]" 
                               value="{{ old('settings.' . $setting->key, $setting->value) }}" 
                               step="0.01" 
                               min="0">
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 sm:text-sm">
                                @if($setting->key === 'delivery_charge_per_km')
                                    ₹/km
                                @elseif($setting->key === 'service_radius_km')
                                    km
                                @endif
                            </span>
                        </div>
                    </div>
                    @error('settings.' . $setting->key)
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                @endforeach

                <div class="flex justify-end">
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-brand-yellow hover:bg-brand-red focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-yellow">
                        Save Settings
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection 