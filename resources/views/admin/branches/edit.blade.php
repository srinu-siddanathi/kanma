@extends('layouts.admin')

@section('title', 'Edit Branch')

@section('content')
<div class="container mx-auto px-4">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Edit Branch</h1>
        <a href="{{ route('admin.branches.index') }}" class="text-blue-500 hover:underline">
            Back to Branches
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-lg p-6">
        <form action="{{ route('admin.branches.update', $branch) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Name</label>
                    <input type="text" name="name" value="{{ old('name', $branch->name) }}" 
                           class="mt-1 block w-full rounded-md border-gray-300" required>
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Address</label>
                    <textarea name="address" rows="3" 
                              class="mt-1 block w-full rounded-md border-gray-300">{{ old('address', $branch->address) }}</textarea>
                    @error('address')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Phone</label>
                    <input type="text" name="phone" value="{{ old('phone', $branch->phone) }}" 
                           class="mt-1 block w-full rounded-md border-gray-300">
                    @error('phone')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" name="email" value="{{ old('email', $branch->user?->email) }}" 
                           class="mt-1 block w-full rounded-md border-gray-300" required>
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Password</label>
                    <input type="password" name="password" 
                           class="mt-1 block w-full rounded-md border-gray-300">
                    <p class="mt-1 text-sm text-gray-500">Leave blank to keep current password</p>
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Confirm Password</label>
                    <input type="password" name="password_confirmation" 
                           class="mt-1 block w-full rounded-md border-gray-300">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Status</label>
                    <div class="mt-2">
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="is_active" value="1" 
                                   class="rounded border-gray-300 text-blue-600"
                                   {{ old('is_active', $branch->is_active) ? 'checked' : '' }}>
                            <span class="ml-2">Active</span>
                        </label>
                    </div>
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                        Update Branch
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection 