@extends('layouts.admin')

@section('title', 'Coupon Details')

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="px-4 py-6 sm:px-0">
        <!-- Header -->
        <div class="mb-6 flex items-center justify-between">
            <h2 class="text-2xl font-bold text-gray-900">Coupon Details: {{ $coupon->name }}</h2>
            <div class="flex space-x-3">
                <a href="{{ route('admin.coupons.edit', $coupon) }}" 
                   class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    <i class="fas fa-edit mr-2"></i>
                    Edit Coupon
                </a>
                <a href="{{ route('admin.coupons.index') }}" 
                   class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to Coupons
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Coupon Details -->
            <div class="lg:col-span-2">
                <div class="bg-white shadow sm:rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Coupon Information</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Coupon Code</dt>
                                <dd class="mt-1 text-sm text-gray-900 font-mono bg-gray-100 px-3 py-2 rounded">{{ $coupon->code }}</dd>
                            </div>
                            
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Name</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $coupon->name }}</dd>
                            </div>
                            
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Description</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $coupon->description ?: 'No description provided' }}</dd>
                            </div>
                            
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Status</dt>
                                <dd class="mt-1">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        @if($coupon->status_color === 'green') bg-green-100 text-green-800
                                        @elseif($coupon->status_color === 'red') bg-red-100 text-red-800
                                        @elseif($coupon->status_color === 'yellow') bg-yellow-100 text-yellow-800
                                        @elseif($coupon->status_color === 'orange') bg-orange-100 text-orange-800
                                        @else bg-gray-100 text-gray-800
                                        @endif">
                                        {{ $coupon->status }}
                                    </span>
                                </dd>
                            </div>
                            
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Discount Type</dt>
                                <dd class="mt-1 text-sm text-gray-900 capitalize">{{ $coupon->type }}</dd>
                            </div>
                            
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Discount Value</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    @if($coupon->type === 'percentage')
                                        {{ $coupon->value }}%
                                    @else
                                        ₹{{ number_format($coupon->value, 2) }}
                                    @endif
                                </dd>
                            </div>
                            
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Minimum Order Amount</dt>
                                <dd class="mt-1 text-sm text-gray-900">₹{{ number_format($coupon->minimum_order_amount, 2) }}</dd>
                            </div>
                            
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Maximum Discount</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    @if($coupon->maximum_discount)
                                        ₹{{ number_format($coupon->maximum_discount, 2) }}
                                    @else
                                        No limit
                                    @endif
                                </dd>
                            </div>
                            
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Usage Limit</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    @if($coupon->usage_limit)
                                        {{ $coupon->used_count }} / {{ $coupon->usage_limit }}
                                    @else
                                        Unlimited
                                    @endif
                                </dd>
                            </div>
                            
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Per User Limit</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $coupon->per_user_limit }}</dd>
                            </div>
                            
                            <div>
                                <dt class="text-sm font-medium text-gray-500">First-time Only</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    @if($coupon->is_first_time_only)
                                        <span class="text-green-600">Yes</span>
                                    @else
                                        <span class="text-gray-500">No</span>
                                    @endif
                                </dd>
                            </div>
                            
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Valid From</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $coupon->valid_from->format('M d, Y H:i') }}</dd>
                            </div>
                            
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Valid Until</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $coupon->valid_until->format('M d, Y H:i') }}</dd>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Usage Statistics -->
            <div class="lg:col-span-1">
                <div class="bg-white shadow sm:rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Usage Statistics</h3>
                        
                        <div class="space-y-4">
                            <div class="bg-blue-50 p-4 rounded-lg">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-blue-800">Total Usage</p>
                                        <p class="text-2xl font-bold text-blue-900">{{ $coupon->usages->count() }}</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="bg-green-50 p-4 rounded-lg">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-green-800">Total Discount Given</p>
                                        <p class="text-2xl font-bold text-green-900">₹{{ number_format($coupon->usages->sum('discount_amount'), 2) }}</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="bg-purple-50 p-4 rounded-lg">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-purple-800">Unique Users</p>
                                        <p class="text-2xl font-bold text-purple-900">{{ $coupon->usages->unique('user_id')->count() }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Usage History -->
        @if($coupon->usages->count() > 0)
        <div class="mt-6">
            <div class="bg-white shadow sm:rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Usage History</h3>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        User
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Order
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Discount Amount
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Used On
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($coupon->usages as $usage)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $usage->user->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $usage->user->email }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">Order #{{ $usage->order->id }}</div>
                                        <div class="text-sm text-gray-500">₹{{ number_format($usage->order->total_amount, 2) }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-green-600">₹{{ number_format($usage->discount_amount, 2) }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $usage->created_at->format('M d, Y H:i') }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection 