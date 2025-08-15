@extends('layouts.branch-manager')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Chat Order #{{ $chatOrder->id }}</h1>
            <p class="text-sm text-gray-500">
                Customer: {{ $chatOrder->user?->name ?? 'Unknown User' }} ({{ $chatOrder->user?->email ?? 'No email available' }})
            </p>
        </div>
        <div class="flex items-center space-x-4">
            <form action="{{ route('branch.chat-orders.update-status', $chatOrder) }}" method="POST" class="flex items-center">
                @csrf
                @method('PUT')
                <select name="status" onchange="this.form.submit()" 
                    class="rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    <option value="pending" {{ $chatOrder->status === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="confirmed" {{ $chatOrder->status === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                    <option value="preparing" {{ $chatOrder->status === 'preparing' ? 'selected' : '' }}>Preparing</option>
                    <option value="ready" {{ $chatOrder->status === 'ready' ? 'selected' : '' }}>Ready</option>
                    <option value="delivered" {{ $chatOrder->status === 'delivered' ? 'selected' : '' }}>Delivered</option>
                    <option value="cancelled" {{ $chatOrder->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </form>
            <a href="{{ route('branch.chat-orders.index') }}" 
               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Back to List
            </a>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="p-4 border-b">
            <h2 class="text-lg font-medium text-gray-900">Order Details</h2>
            <div class="mt-2 grid grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-500">Name</p>
                    <p class="text-sm font-medium">{{ $chatOrder->name }}</p>
                </div>
                @if($chatOrder->notes)
                <div class="col-span-2">
                    <p class="text-sm text-gray-500">Notes</p>
                    <p class="text-sm font-medium">{{ $chatOrder->notes }}</p>
                </div>
                @endif
            </div>
        </div>

        <div class="p-4">
            <h2 class="text-lg font-medium text-gray-900 mb-4">Chat Messages</h2>
            
            <div class="space-y-4 mb-4" style="max-height: 500px; overflow-y: auto;">
                @foreach($chatOrder->messages->reverse() as $message)
                <div class="flex {{ $message->sender_id === auth()->id() ? 'justify-end' : 'justify-start' }}">
                    <div class="max-w-lg {{ $message->sender_id === auth()->id() ? 'bg-indigo-100' : 'bg-gray-100' }} rounded-lg px-4 py-2">
                        <div class="text-xs text-gray-500 mb-1">
                            {{ $message->sender?->name ?? 'Unknown User' }} • {{ $message->created_at->format('M d, Y H:i') }}
                        </div>
                        
                        @if($message->type === 'text')
                            <p class="text-sm">{{ $message->content }}</p>
                        @elseif($message->type === 'image')
                            <img src="{{ asset($message->media_path) }}" alt="Image" class="max-w-xs rounded">
                        @elseif($message->type === 'voice')
                            <audio controls class="w-full">
                                <source src="{{ asset($message->media_path) }}" type="audio/mpeg">
                                Your browser does not support the audio element.
                            </audio>
                        @elseif($message->type === 'schedule')
                            <div class="flex items-center space-x-2">
                                <svg class="h-5 w-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <p class="text-sm font-medium text-green-600">{{ $message->content }}</p>
                            </div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>

            <div class="bg-white rounded-lg shadow p-4">
                <form id="chatForm" action="{{ route('branch.chat-orders.messages.store', $chatOrder) }}" method="POST" class="flex items-center space-x-4">
                    @csrf
                    <div class="flex-1">
                        <input type="text" name="content" id="messageContent" placeholder="Type your message..." class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        <input type="hidden" name="type" id="messageType" value="text">
                    </div>
                    <button type="button" onclick="sendScheduleMessage()" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                        Schedule
                    </button>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                        Send
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function sendScheduleMessage() {
        const messageTypeInput = document.getElementById('messageType');
        const contentInput = document.getElementById('messageContent');
        const form = document.getElementById('chatForm');
        
        if (messageTypeInput && contentInput && form) {
            // Set the message type to schedule
            messageTypeInput.value = 'schedule';
            // Set a default message for scheduling
            contentInput.value = 'Please schedule this order';
            
            // Submit the form
            form.submit();
        } else {
            console.error('Required form elements not found');
        }
    }
</script>
@endpush
@endsection 