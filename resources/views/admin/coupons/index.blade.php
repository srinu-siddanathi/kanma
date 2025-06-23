@extends('layouts.admin')

@section('title', 'Coupon Management')

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="px-4 py-6 sm:px-0">
        <!-- Header -->
        <div class="mb-6 flex items-center justify-between">
            <h2 class="text-2xl font-bold text-gray-900">Coupon Management</h2>
            <a href="{{ route('admin.coupons.create') }}" 
               class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                <i class="fas fa-plus mr-2"></i>
                Add Coupon
            </a>
        </div>

        @if(session('success'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
            {{ session('success') }}
        </div>
        @endif

        @if(session('error'))
        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
            {{ session('error') }}
        </div>
        @endif

        <!-- Filters -->
        <x-filters :filters="[
            'search' => [
                'type' => 'text',
                'label' => 'Search Coupons',
                'placeholder' => 'Search by code, name, or description'
            ],
            'status' => [
                'type' => 'select',
                'label' => 'Status',
                'options' => [
                    'active' => 'Active',
                    'inactive' => 'Inactive',
                    'expired' => 'Expired',
                    'not_started' => 'Not Started'
                ]
            ],
            'type' => [
                'type' => 'select',
                'label' => 'Type',
                'options' => [
                    'percentage' => 'Percentage',
                    'fixed' => 'Fixed Amount'
                ]
            ]
        ]" />

        <!-- Coupons Table -->
        <div class="bg-white shadow overflow-hidden sm:rounded-md">
            <div class="px-4 py-5 sm:px-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900">
                    Coupons ({{ $coupons->total() }})
                </h3>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Code
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Name
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Type & Value
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Validity
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Usage
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($coupons as $coupon)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $coupon->code }}</div>
                                @if($coupon->is_first_time_only)
                                    <div class="text-xs text-orange-600">First-time only</div>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ $coupon->name }}</div>
                                @if($coupon->description)
                                    <div class="text-sm text-gray-500 truncate max-w-xs">{{ $coupon->description }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    @if($coupon->type === 'percentage')
                                        {{ $coupon->value }}% off
                                    @else
                                        ₹{{ number_format($coupon->value, 2) }} off
                                    @endif
                                </div>
                                <div class="text-xs text-gray-500">
                                    Min: ₹{{ number_format($coupon->minimum_order_amount, 2) }}
                                    @if($coupon->maximum_discount)
                                        <br>Max: ₹{{ number_format($coupon->maximum_discount, 2) }}
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    {{ $coupon->valid_from->format('M d, Y') }}
                                </div>
                                <div class="text-sm text-gray-500">
                                    to {{ $coupon->valid_until->format('M d, Y') }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    {{ $coupon->used_count }}
                                    @if($coupon->usage_limit)
                                        / {{ $coupon->usage_limit }}
                                    @endif
                                </div>
                                <div class="text-xs text-gray-500">
                                    Per user: {{ $coupon->per_user_limit }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    @if($coupon->status_color === 'green') bg-green-100 text-green-800
                                    @elseif($coupon->status_color === 'red') bg-red-100 text-red-800
                                    @elseif($coupon->status_color === 'yellow') bg-yellow-100 text-yellow-800
                                    @elseif($coupon->status_color === 'orange') bg-orange-100 text-orange-800
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                                    {{ $coupon->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <a href="{{ route('admin.coupons.show', $coupon) }}" 
                                       class="text-indigo-600 hover:text-indigo-900 bg-indigo-50 hover:bg-indigo-100 px-2 py-1 rounded text-xs">
                                        <i class="fas fa-eye mr-1"></i> View
                                    </a>
                                    <a href="{{ route('admin.coupons.edit', $coupon) }}" 
                                       class="text-blue-600 hover:text-blue-900 bg-blue-50 hover:bg-blue-100 px-2 py-1 rounded text-xs">
                                        <i class="fas fa-edit mr-1"></i> Edit
                                    </a>
                                    <form action="{{ route('admin.coupons.toggle-status', $coupon) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" 
                                                class="text-{{ $coupon->is_active ? 'orange' : 'green' }}-600 hover:text-{{ $coupon->is_active ? 'orange' : 'green' }}-900 bg-{{ $coupon->is_active ? 'orange' : 'green' }}-50 hover:bg-{{ $coupon->is_active ? 'orange' : 'green' }}-100 px-2 py-1 rounded text-xs">
                                            <i class="fas fa-{{ $coupon->is_active ? 'pause' : 'play' }} mr-1"></i>
                                            {{ $coupon->is_active ? 'Deactivate' : 'Activate' }}
                                        </button>
                                    </form>
                                    @if(!$coupon->usages()->exists())
                                    <form action="{{ route('admin.coupons.destroy', $coupon) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                onclick="return confirm('Are you sure you want to delete this coupon?')"
                                                class="text-red-600 hover:text-red-900 bg-red-50 hover:bg-red-100 px-2 py-1 rounded text-xs">
                                            <i class="fas fa-trash mr-1"></i> Delete
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                                No coupons found
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 border-t border-gray-200">
                <div class="flex justify-center">
                    {{ $coupons->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 