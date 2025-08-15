@extends('layouts.branch-manager')

@section('content')
<div class="container mx-auto px-4">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Delivery Boys</h1>
        <a href="{{ route('branch.delivery-boys.create') }}" 
           class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
            Add New Delivery Boy
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    @if($deliveryBoys->isEmpty())
        <div class="text-center py-8">
            <p class="text-gray-500 text-lg">No delivery boys found.</p>
        </div>
    @else
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Phone</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bike Number</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Working Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Updated</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($deliveryBoys as $deliveryBoy)
                    <tr>
                        <td class="px-6 py-4">{{ $deliveryBoy->name }}</td>
                        <td class="px-6 py-4">{{ $deliveryBoy->email }}</td>
                        <td class="px-6 py-4">{{ $deliveryBoy->phone }}</td>
                        <td class="px-6 py-4">{{ $deliveryBoy->bike_number }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                {{ $deliveryBoy->is_working_today ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $deliveryBoy->is_working_today ? 'Working Today' : 'Not Working' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            {{ $deliveryBoy->last_status_update ? $deliveryBoy->last_status_update->diffForHumans() : 'Never' }}
                        </td>
                        <td class="px-6 py-4">
                            <a href="{{ route('branch.delivery-boys.edit', $deliveryBoy) }}" 
                               class="text-blue-500 hover:underline mr-3">Edit</a>
                            <form action="{{ route('branch.delivery-boys.destroy', $deliveryBoy) }}" 
                                  method="POST" 
                                  onsubmit="return confirm('Are you sure you want to delete this delivery boy? This action cannot be undone.');"
                                  class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                            </form>
                            <button onclick="toggleWorkingStatus({{ $deliveryBoy->id }}, {{ $deliveryBoy->is_working_today ? 'false' : 'true' }})" 
                                    class="text-indigo-600 hover:text-indigo-900 ml-3">
                                {{ $deliveryBoy->is_working_today ? 'Mark Not Working' : 'Mark Working' }}
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $deliveryBoys->links() }}
        </div>
    @endif
</div>

@push('scripts')
<script>
function toggleWorkingStatus(id, status) {
    fetch(`/admin/branch/delivery-boys/${id}/toggle-status`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ is_working_today: status })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.reload();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to update status');
    });
}
</script>
@endpush
@endsection 