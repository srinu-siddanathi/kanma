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
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-medium text-gray-900">Chat Messages</h2>
                <button id="refreshButton" onclick="manualRefresh()" class="inline-flex items-center px-3 py-1 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    Refresh
                </button>
            </div>
            
            <div id="loadingIndicator" class="hidden text-center py-2">
                <div class="inline-flex items-center text-sm text-gray-500">
                    <svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Checking for new messages...
                </div>
            </div>
            
            <div id="chatMessages" class="space-y-4 mb-4" style="max-height: 500px; overflow-y: auto;">
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
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2 2v12a2 2 0 002 2z" />
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
    let lastMessageCount = {{ $chatOrder->messages->count() }};
    let refreshInterval;
    let isUserTyping = false;

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

    function refreshMessages() {
        // Don't refresh if user is typing
        if (isUserTyping) {
            return;
        }
        
        const loadingIndicator = document.getElementById('loadingIndicator');
        loadingIndicator.classList.remove('hidden');
        
        fetch('{{ route("branch.chat-orders.messages.index", $chatOrder) }}')
            .then(response => response.json())
            .then(data => {
                loadingIndicator.classList.add('hidden');
                
                if (data.status === 'success') {
                    const messages = data.data;
                    const currentMessageCount = messages.length;
                    
                    // Only update if there are new messages
                    if (currentMessageCount > lastMessageCount) {
                        const newMessageCount = currentMessageCount - lastMessageCount;
                        updateChatMessages(messages);
                        lastMessageCount = currentMessageCount;
                        
                        // Scroll to top to show new messages
                        const chatContainer = document.getElementById('chatMessages');
                        chatContainer.scrollTop = 0;
                        
                        // Show a brief notification for new messages
                        showNewMessageNotification(newMessageCount);
                    }
                }
            })
            .catch(error => {
                loadingIndicator.classList.add('hidden');
                console.error('Error fetching messages:', error);
            });
    }

    function showNewMessageNotification(messageCount) {
        // Create a temporary notification
        const notification = document.createElement('div');
        notification.className = 'fixed top-4 right-4 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg z-50';
        notification.textContent = `${messageCount} new message${messageCount > 1 ? 's' : ''} received`;
        
        document.body.appendChild(notification);
        
        // Remove notification after 3 seconds
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 3000);
    }

    function manualRefresh() {
        // Disable the refresh button temporarily
        const refreshButton = document.getElementById('refreshButton');
        refreshButton.disabled = true;
        refreshButton.innerHTML = `
            <svg class="animate-spin h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
            </svg>
            Refreshing...
        `;
        
        // Force refresh messages
        refreshMessages();
        
        // Re-enable the button after 2 seconds
        setTimeout(() => {
            refreshButton.disabled = false;
            refreshButton.innerHTML = `
                <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                Refresh
            `;
        }, 2000);
    }

    function updateChatMessages(messages) {
        const chatContainer = document.getElementById('chatMessages');
        const currentUserId = {{ auth()->id() }};
        
        // Clear existing messages
        chatContainer.innerHTML = '';
        
        // Add messages in the same order as they come from the server (oldest first)
        messages.forEach(message => {
            const messageDiv = document.createElement('div');
            messageDiv.className = `flex ${message.sender_id === currentUserId ? 'justify-end' : 'justify-start'}`;
            
            const messageContent = document.createElement('div');
            messageContent.className = `max-w-lg ${message.sender_id === currentUserId ? 'bg-indigo-100' : 'bg-gray-100'} rounded-lg px-4 py-2`;
            
            // Create timestamp
            const timestamp = document.createElement('div');
            timestamp.className = 'text-xs text-gray-500 mb-1';
            const messageDate = new Date(message.created_at);
            timestamp.textContent = `${message.sender?.name || 'Unknown User'} • ${messageDate.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })} ${messageDate.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' })}`;
            messageContent.appendChild(timestamp);
            
            // Create message content based on type
            if (message.type === 'text') {
                const textContent = document.createElement('p');
                textContent.className = 'text-sm';
                textContent.textContent = message.content;
                messageContent.appendChild(textContent);
            } else if (message.type === 'image') {
                const image = document.createElement('img');
                image.src = `{{ asset('') }}${message.media_path}`;
                image.alt = 'Image';
                image.className = 'max-w-xs rounded';
                messageContent.appendChild(image);
            } else if (message.type === 'voice') {
                const audio = document.createElement('audio');
                audio.controls = true;
                audio.className = 'w-full';
                const source = document.createElement('source');
                source.src = `{{ asset('') }}${message.media_path}`;
                source.type = 'audio/mpeg';
                audio.appendChild(source);
                audio.appendChild(document.createTextNode('Your browser does not support the audio element.'));
                messageContent.appendChild(audio);
            } else if (message.type === 'schedule') {
                const scheduleDiv = document.createElement('div');
                scheduleDiv.className = 'flex items-center space-x-2';
                
                const svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
                svg.setAttribute('class', 'h-5 w-5 text-green-500');
                svg.setAttribute('fill', 'none');
                svg.setAttribute('stroke', 'currentColor');
                svg.setAttribute('viewBox', '0 0 24 24');
                svg.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />';
                
                const scheduleText = document.createElement('p');
                scheduleText.className = 'text-sm font-medium text-green-600';
                scheduleText.textContent = message.content;
                
                scheduleDiv.appendChild(svg);
                scheduleDiv.appendChild(scheduleText);
                messageContent.appendChild(scheduleDiv);
            }
            
            messageDiv.appendChild(messageContent);
            chatContainer.appendChild(messageDiv);
        });
    }

    // Start auto-refresh when page loads
    document.addEventListener('DOMContentLoaded', function() {
        // Start refreshing every 10 seconds
        refreshInterval = setInterval(refreshMessages, 10000);
        
        // Also refresh when the page becomes visible (user switches back to tab)
        document.addEventListener('visibilitychange', function() {
            if (!document.hidden) {
                refreshMessages();
            }
        });
        
        // Handle typing events
        const messageInput = document.getElementById('messageContent');
        if (messageInput) {
            messageInput.addEventListener('focus', function() {
                isUserTyping = true;
            });
            
            messageInput.addEventListener('blur', function() {
                isUserTyping = false;
            });
            
            messageInput.addEventListener('input', function() {
                isUserTyping = true;
                // Reset typing flag after 2 seconds of no input
                clearTimeout(window.typingTimeout);
                window.typingTimeout = setTimeout(() => {
                    isUserTyping = false;
                }, 2000);
            });
        }
    });

    // Clean up interval when page is unloaded
    window.addEventListener('beforeunload', function() {
        if (refreshInterval) {
            clearInterval(refreshInterval);
        }
    });
</script>
@endpush
@endsection 