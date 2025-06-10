@extends('layouts.branch-manager')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Chat Orders</h1>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        @if($chatOrders->isEmpty())
            <div class="p-4 text-center text-gray-500">
                No chat orders found.
            </div>
        @else
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Message</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unread</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($chatOrders as $chatOrder)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            #{{ $chatOrder->id }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">
                                {{ $chatOrder->user->name }}
                            </div>
                            <div class="text-sm text-gray-500">
                                {{ $chatOrder->user->email }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                @if($chatOrder->status === 'delivered') bg-green-100 text-green-800
                                @elseif($chatOrder->status === 'cancelled') bg-red-100 text-red-800
                                @else bg-yellow-100 text-yellow-800
                                @endif">
                                {{ ucfirst($chatOrder->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            @if($chatOrder->messages->isNotEmpty())
                                @php
                                    $lastMessage = $chatOrder->messages->first();
                                    $isFromCustomer = $lastMessage->sender_id !== auth()->id();
                                @endphp
                                <div class="flex items-center space-x-2">
                                    @if($isFromCustomer)
                                        <span class="text-red-500">Customer:</span>
                                    @else
                                        <span class="text-blue-500">You:</span>
                                    @endif
                                    <span>
                                        @if($lastMessage->type === 'text')
                                            {{ Str::limit($lastMessage->content, 30) }}
                                        @elseif($lastMessage->type === 'image')
                                            [Image]
                                        @elseif($lastMessage->type === 'voice')
                                            [Voice Message]
                                        @elseif($lastMessage->type === 'schedule')
                                            [Schedule Request]
                                        @endif
                                    </span>
                                </div>
                            @else
                                No messages
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            @if($chatOrder->unread_count > 0)
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                    {{ $chatOrder->unread_count }}
                                </span>
                            @else
                                -
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $chatOrder->created_at->format('M d, Y H:i') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="{{ route('branch.chat-orders.show', $chatOrder) }}" 
                               class="text-indigo-600 hover:text-indigo-900">
                                View
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="px-6 py-4">
                {{ $chatOrders->links() }}
            </div>
        @endif
    </div>
</div>
@endsection 