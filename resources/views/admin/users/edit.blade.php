@extends('layouts.admin')

@section('title', 'Edit User')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="bg-white rounded-lg shadow-lg p-6">
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Edit User</h2>
    </div>

    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.users.update', $user) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Name -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-yellow focus:ring-brand-yellow">
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-yellow focus:ring-brand-yellow">
                @error('email')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password -->
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">Password (leave blank to keep current)</label>
                <input type="password" name="password" id="password"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-yellow focus:ring-brand-yellow">
                @error('password')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Phone -->
            <div>
                <label for="phone" class="block text-sm font-medium text-gray-700">Phone</label>
                <input type="text" name="phone" id="phone" value="{{ old('phone', $user->phone) }}"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-yellow focus:ring-brand-yellow">
                @error('phone')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Role -->
            <div>
                <label for="role" class="block text-sm font-medium text-gray-700">Role</label>
                <select name="role" id="role"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-yellow focus:ring-brand-yellow">
                    <option value="">Select Role</option>
                    @foreach($roles as $value => $label)
                        <option value="{{ $value }}" {{ old('role', $user->role) == $value ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
                @error('role')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Branch (shown only for branch manager role) -->
            <div id="branchField" class="{{ $user->role === 'branch_manager' ? '' : 'hidden' }}">
                <label for="branch_id" class="block text-sm font-medium text-gray-700">Branch</label>
                <select name="branch_id" id="branch_id"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-yellow focus:ring-brand-yellow">
                    <option value="">Select Branch</option>
                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}" {{ old('branch_id', $user->branch_id) == $branch->id ? 'selected' : '' }}>
                            {{ $branch->name }}
                        </option>
                    @endforeach
                </select>
                @error('branch_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Status -->
            <div>
                <label class="flex items-center">
                    <input type="checkbox" name="is_active" value="1" 
                        {{ old('is_active', $user->is_active) ? 'checked' : '' }}
                        class="rounded border-gray-300 text-brand-yellow shadow-sm focus:border-brand-yellow focus:ring-brand-yellow">
                    <span class="ml-2 text-sm text-gray-600">Active</span>
                </label>
            </div>
        </div>

        <div class="flex justify-end space-x-4">
            <a href="{{ route('admin.users.index') }}"
                class="bg-gray-100 hover:bg-gray-200 text-gray-800 font-bold py-2 px-4 rounded transition duration-200">
                Cancel
            </a>
            <button type="submit"
                class="bg-brand-yellow hover:bg-brand-red text-white font-bold py-2 px-4 rounded transition duration-200">
                Update User
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const roleSelect = document.getElementById('role');
        const branchField = document.getElementById('branchField');

        function toggleBranchField() {
            if (roleSelect.value === 'branch_manager') {
                branchField.classList.remove('hidden');
            } else {
                branchField.classList.add('hidden');
            }
        }

        roleSelect.addEventListener('change', toggleBranchField);

        const form = document.querySelector('form');
        form.addEventListener('submit', function(e) {
            console.log('Form submitted');
            const formData = new FormData(this);
            console.log('Form data:', Object.fromEntries(formData));
        });
    });
</script>
@endpush
@endsection 