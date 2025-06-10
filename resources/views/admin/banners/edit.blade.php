@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8">
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Edit Banner - {{ \App\Models\Banner::getSections()[$banner->section] }}</h3>
        </div>

        <div class="p-6">
            <form action="{{ route('admin.banners.update', $banner) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="hidden" name="section" value="{{ $banner->section }}">

                <div class="space-y-6">
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700">Title <span class="text-red-500">*</span></label>
                        <input type="text" name="title" id="title" 
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-yellow focus:ring focus:ring-brand-yellow focus:ring-opacity-50 @error('title') border-red-500 @enderror" 
                               value="{{ old('title', $banner->title) }}" required>
                        @error('title')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="subtitle" class="block text-sm font-medium text-gray-700">Subtitle</label>
                        <input type="text" name="subtitle" id="subtitle" 
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-yellow focus:ring focus:ring-brand-yellow focus:ring-opacity-50 @error('subtitle') border-red-500 @enderror" 
                               value="{{ old('subtitle', $banner->subtitle) }}">
                        @error('subtitle')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                        <textarea name="description" id="description" rows="3" 
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-yellow focus:ring focus:ring-brand-yellow focus:ring-opacity-50 @error('description') border-red-500 @enderror">{{ old('description', $banner->description) }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="image" class="block text-sm font-medium text-gray-700">Banner Image</label>
                        @if($banner->image_url)
                            <div class="mt-2">
                                <img src="{{ asset($banner->image_url) }}" alt="{{ $banner->title }}" class="h-32 w-auto object-cover rounded">
                            </div>
                        @endif
                        <input type="file" name="image" id="image" 
                               class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-brand-yellow file:text-white hover:file:bg-brand-red @error('image') border-red-500 @enderror">
                        <p class="mt-1 text-sm text-gray-500">
                            @if($banner->section === \App\Models\Banner::SECTION_WEB_HOME)
                                Recommended size: 1920x600 pixels
                            @elseif($banner->section === \App\Models\Banner::SECTION_MOBILE_APP_MAIN)
                                Recommended size: 1080x400 pixels
                            @else
                                Recommended size: 1080x300 pixels
                            @endif
                            . Max file size: 2MB. Leave empty to keep current image.
                        </p>
                        @error('image')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="button_text" class="block text-sm font-medium text-gray-700">Button Text</label>
                        <input type="text" name="button_text" id="button_text" 
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-yellow focus:ring focus:ring-brand-yellow focus:ring-opacity-50 @error('button_text') border-red-500 @enderror" 
                               value="{{ old('button_text', $banner->button_text) }}">
                        @error('button_text')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="button_url" class="block text-sm font-medium text-gray-700">Button URL</label>
                        <input type="text" name="button_url" id="button_url" 
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-yellow focus:ring focus:ring-brand-yellow focus:ring-opacity-50 @error('button_url') border-red-500 @enderror" 
                               value="{{ old('button_url', $banner->button_url) }}">
                        @error('button_url')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="display_order" class="block text-sm font-medium text-gray-700">Display Order</label>
                        <input type="number" name="display_order" id="display_order" 
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-yellow focus:ring focus:ring-brand-yellow focus:ring-opacity-50 @error('display_order') border-red-500 @enderror" 
                               value="{{ old('display_order', $banner->display_order) }}" min="0">
                        @error('display_order')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center">
                        <input type="checkbox" name="is_active" id="is_active" value="1" 
                               class="h-4 w-4 text-brand-yellow focus:ring-brand-yellow border-gray-300 rounded" 
                               {{ old('is_active', $banner->is_active) ? 'checked' : '' }}>
                        <label for="is_active" class="ml-2 block text-sm text-gray-700">Active</label>
                    </div>

                    <div class="flex items-center space-x-4">
                        <button type="submit" 
                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-brand-yellow hover:bg-brand-red focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-yellow">
                            Update Banner
                        </button>
                        <a href="{{ route('admin.banners.index', ['section' => $banner->section]) }}" 
                           class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-yellow">
                            Cancel
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection 