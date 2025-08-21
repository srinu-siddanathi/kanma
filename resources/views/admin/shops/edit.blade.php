@extends('layouts.admin')

@section('title', 'Edit Shop')

@section('content')
<div class="container mx-auto px-4">
    <div class="max-w-4xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold">Edit Shop</h2>
            <a href="{{ route('admin.shops.show', $shop) }}" class="text-indigo-600 hover:text-indigo-900">
                ← Back to Shop Details
            </a>
        </div>

        <div class="bg-white shadow rounded-lg overflow-hidden">
            <form action="{{ route('admin.shops.update', $shop) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div class="p-6">
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <!-- Basic Information -->
                        <div class="sm:col-span-2">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Basic Information</h3>
                        </div>

                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">Shop Name</label>
                            <input type="text" name="name" id="name" value="{{ old('name', $shop->name) }}" 
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                            <input type="email" name="email" id="email" value="{{ old('email', $shop->email) }}" 
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            @error('email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700">Phone</label>
                            <input type="text" name="phone" id="phone" value="{{ old('phone', $shop->phone) }}" 
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            @error('phone')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="sm:col-span-2">
                            <label for="address" class="block text-sm font-medium text-gray-700">Address</label>
                            <textarea name="address" id="address" rows="3" 
                                      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">{{ old('address', $shop->address) }}</textarea>
                            @error('address')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="sm:col-span-2">
                            <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                            <textarea name="description" id="description" rows="4" 
                                      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">{{ old('description', $shop->description) }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Location Information -->
                        <div class="sm:col-span-2">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Location Information</h3>
                        </div>

                        <div>
                            <label for="latitude" class="block text-sm font-medium text-gray-700">Latitude</label>
                            <input type="number" name="latitude" id="latitude" step="any" value="{{ old('latitude', $shop->latitude) }}" 
                                   placeholder="e.g., 12.9716" 
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            <p class="mt-1 text-xs text-gray-500">Range: -90 to 90</p>
                            @error('latitude')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="longitude" class="block text-sm font-medium text-gray-700">Longitude</label>
                            <input type="number" name="longitude" id="longitude" step="any" value="{{ old('longitude', $shop->longitude) }}" 
                                   placeholder="e.g., 77.5946" 
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            <p class="mt-1 text-xs text-gray-500">Range: -180 to 180</p>
                            @error('longitude')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Working Hours -->
                        <div class="sm:col-span-2">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Working Hours</h3>
                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                                @php
                                    $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
                                    $workingHours = $shop->working_hours ?? [];
                                @endphp
                                
                                @foreach($days as $day)
                                    <div class="border rounded-lg p-4">
                                        <h4 class="text-sm font-medium text-gray-700 capitalize mb-2">{{ $day }}</h4>
                                        <div class="grid grid-cols-2 gap-2">
                                            <div>
                                                <label for="working_hours[{{ $day }}][open]" class="block text-xs text-gray-500">Open</label>
                                                <input type="time" 
                                                       name="working_hours[{{ $day }}][open]" 
                                                       id="working_hours[{{ $day }}][open]" 
                                                       value="{{ old("working_hours.{$day}.open", $workingHours[$day]['open'] ?? '') }}" 
                                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-xs">
                                            </div>
                                            <div>
                                                <label for="working_hours[{{ $day }}][close]" class="block text-xs text-gray-500">Close</label>
                                                <input type="time" 
                                                       name="working_hours[{{ $day }}][close]" 
                                                       id="working_hours[{{ $day }}][close]" 
                                                       value="{{ old("working_hours.{$day}.close", $workingHours[$day]['close'] ?? '') }}" 
                                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-xs">
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            @error('working_hours')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Status -->
                        <div class="sm:col-span-2">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Status</h3>
                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <div class="flex items-center">
                                    <input type="checkbox" name="is_active" id="is_active" value="1" 
                                           {{ old('is_active', $shop->is_active) ? 'checked' : '' }}
                                           class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                    <label for="is_active" class="ml-2 block text-sm text-gray-900">Active</label>
                                </div>
                                <div class="flex items-center">
                                    <input type="checkbox" name="is_verified" id="is_verified" value="1" 
                                           {{ old('is_verified', $shop->is_verified) ? 'checked' : '' }}
                                           class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                    <label for="is_verified" class="ml-2 block text-sm text-gray-900">Verified</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="px-6 py-3 bg-gray-50 text-right sm:px-6">
                    <button type="submit" 
                            class="bg-indigo-600 border border-transparent rounded-md shadow-sm py-2 px-4 inline-flex justify-center text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Update Shop
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const latitudeInput = document.getElementById('latitude');
    const longitudeInput = document.getElementById('longitude');
    
    // Coordinate validation
    function validateCoordinates() {
        const lat = parseFloat(latitudeInput.value);
        const lng = parseFloat(longitudeInput.value);
        
        if (latitudeInput.value && (lat < -90 || lat > 90)) {
            latitudeInput.setCustomValidity('Latitude must be between -90 and 90');
        } else {
            latitudeInput.setCustomValidity('');
        }
        
        if (longitudeInput.value && (lng < -180 || lng > 180)) {
            longitudeInput.setCustomValidity('Longitude must be between -180 and 180');
        } else {
            longitudeInput.setCustomValidity('');
        }
    }
    
    latitudeInput.addEventListener('input', validateCoordinates);
    longitudeInput.addEventListener('input', validateCoordinates);
    
    // Add a button to open Google Maps for coordinate picking
    const locationSection = document.querySelector('h3');
    if (locationSection && locationSection.textContent.includes('Location Information')) {
        const mapButton = document.createElement('button');
        mapButton.type = 'button';
        mapButton.className = 'mt-2 bg-green-600 text-white px-3 py-1 rounded text-sm hover:bg-green-700';
        mapButton.textContent = 'Pick from Map';
        mapButton.onclick = function() {
            const lat = latitudeInput.value || '12.9716';
            const lng = longitudeInput.value || '77.5946';
            window.open(`https://www.google.com/maps?q=${lat},${lng}`, '_blank');
        };
        
        locationSection.parentElement.appendChild(mapButton);
        
        // Add help text
        const helpText = document.createElement('p');
        helpText.className = 'mt-2 text-xs text-gray-500';
        helpText.innerHTML = '💡 <strong>Tip:</strong> Use the "Pick from Map" button to get coordinates from Google Maps. Right-click on a location and select "What\'s here?" to see coordinates.';
        locationSection.parentElement.appendChild(helpText);
    }
});
</script>
@endpush
@endsection 