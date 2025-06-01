@extends('layouts.admin')

@section('content')
<div class="max-w-5xl mx-auto py-4">
    <h1 class="mb-6 text-2xl font-bold text-gray-900">Newsletter Subscribers</h1>
    @if(session('success'))
        <div class="mb-4 p-3 rounded bg-green-100 text-green-800">{{ session('success') }}</div>
    @endif
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-4 gap-2">
        <form method="GET" action="" class="flex items-center gap-2 w-full md:w-auto">
            <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Search by name or email..." class="form-input px-4 py-2 border rounded w-full md:w-64" />
            <button type="submit" class="px-4 py-2 bg-gray-700 text-white rounded hover:bg-gray-900 transition">Search</button>
            @if(request('search'))
                <a href="{{ route('admin.newsletters.index') }}" class="ml-2 text-sm text-blue-600 hover:underline">Clear</a>
            @endif
        </form>
        <a href="{{ route('admin.newsletters.export') }}" class="inline-block px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">Export CSV</a>
    </div>
    <div class="bg-white shadow rounded-lg overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subscribed At</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($subscribers as $subscriber)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $subscriber->id }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $subscriber->name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $subscriber->email }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $subscriber->created_at->format('M d, Y H:i') }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        <form action="{{ route('admin.newsletters.destroy', $subscriber->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this subscriber?');" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="inline-block px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700 transition">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">No subscribers found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
            <div class="flex justify-center">
                {{ $subscribers->links() }}
            </div>
        </div>
    </div>
</div>
@endsection 