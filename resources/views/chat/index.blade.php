@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-100">
    <div class="max-w-4xl mx-auto py-6">
        <!-- Connection Status -->
        <div id="connection-status" class="mb-4 p-2 text-sm rounded hidden">
            <span class="text-yellow-800 bg-yellow-100 px-3 py-1 rounded-full">Menghubungkan kembali...</span>
        </div>
        
        <div class="bg-white rounded-lg shadow" 
             data-user-id="{{ auth()->id() }}" 
             data-group-id="{{ $groupId }}"
             data-typing-timeout="{{ $typingTimeout }}">
            <!-- Header -->
            <div class="border-b p-4 flex justify-between items-center">
                <div>
                    <h1 class="text-xl font-semibold">{{ $groupName }}</h1>
                    <p class="text-sm text-gray-600">
                        <span id="online-count">{{ $memberCount }}</span> Anggota
                        <span id="online-members" class="text-green-600"></span>
                    </p>
                </div>
                <div class="flex items-center gap-4">
                    <button id="scroll-bottom" class="text-blue-600 hover:text-blue-800 hidden">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 13l-7 7-7-7m14-8l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <a href="{{ route('ukm.index') }}" class="text-blue-600 hover:text-blue-800">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                    </a>
                </div>
            </div>

            <!-- Chat Messages -->
            <div id="chat-messages" class="h-[500px] overflow-y-auto p-4 space-y-3">
                @foreach($chats as $chat)
                    <div class="flex {{ $chat->user_id === auth()->id() ? 'justify-end' : 'justify-start' }}"
                         data-message-id="{{ $chat->id }}">
                        <div class="max-w-[80%]">
                            <div class="flex items-center gap-2 {{ $chat->user_id === auth()->id() ? 'justify-end' : 'justify-start' }}">
                                <span class="text-sm text-gray-600">{{ $chat->user->name }}</span>
                                @if($chat->user_id === auth()->id())
                                    <span class="read-status text-xs text-gray-400">
                                        {{ $chat->read_at ? '✓✓' : '✓' }}
                                    </span>
                                @endif
                            </div>
                            <div class="{{ $chat->user_id === auth()->id() ? 'bg-blue-500 text-white' : 'bg-gray-100' }} rounded-lg px-3 py-2">
                                {!! $chat->message !!}
                            </div>
                            <div class="text-xs text-gray-500 mt-1">{{ $chat->created_at->format('H:i') }}</div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Typing Indicator -->
            <div id="typing-indicator" class="px-4 py-2 text-gray-500 text-sm hidden">
                <span class="animate-pulse"></span>
            </div>

            <!-- Message Form -->
            <div class="border-t p-4">
                <div id="error-message" class="hidden mb-2 p-2 text-sm text-red-600 bg-red-100 rounded"></div>
                <form id="message-form" class="flex gap-2">
                    @csrf
                    <input type="text" 
                           id="message" 
                           class="flex-1 border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="Ketik pesan..."
                           maxlength="1000"
                           autocomplete="off"
                           required>
                    <button type="submit" 
                            id="send-button"
                            class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 disabled:opacity-50">
                        <span id="button-text">Kirim</span>
                        <span id="button-loading" class="hidden">
                            <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const messageForm = document.getElementById('message-form');
    const messageInput = document.getElementById('message');
    const chatMessages = document.getElementById('chat-messages');
    const errorMessage = document.getElementById('error-message');
    const sendButton = document.getElementById('send-button');
    const buttonText = document.getElementById('button-text');
    const buttonLoading = document.getElementById('button-loading');
    const typingIndicator = document.getElementById('typing-indicator');
    const connectionStatus = document.getElementById('connection-status');
    const scrollBottomButton = document.getElementById('scroll-bottom');
    const userId = parseInt(document.querySelector('[data-user-id]').dataset.userId);
    const groupId = parseInt(document.querySelector('[data-group-id]').dataset.groupId);
    const typingTimeout = parseInt(document.querySelector('[data-typing-timeout]').dataset.typingTimeout);
    
    let typingTimer;
    let reconnectAttempts = 0;
    let isNearBottom = true;
    const maxReconnectAttempts = 5;

    // Scroll handling
    function isUserNearBottom() {
        const threshold = 100;
        const position = chatMessages.scrollHeight - chatMessages.scrollTop - chatMessages.clientHeight;
        return position < threshold;
    }

    function updateScrollButtonVisibility() {
        isNearBottom = isUserNearBottom();
        scrollBottomButton.classList.toggle('hidden', isNearBottom);
    }

    function scrollToBottom(force = false) {
        if (force || isNearBottom) {
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }
    }

    chatMessages.addEventListener('scroll', updateScrollButtonVisibility);
    scrollBottomButton.addEventListener('click', () => scrollToBottom(true));

    // Initialize scroll position
    scrollToBottom(true);

    // Echo setup with reconnection handling
    window.Echo.connector.pusher.connection.bind('state_change', (states) => {
        const { current } = states;
        connectionStatus.classList.toggle('hidden', current === 'connected');
        
        if (current === 'connected') {
            reconnectAttempts = 0;
        }
    });

    // Channel subscription
    const channel = window.Echo.private('chat.' + groupId);

    // Message handling
    channel.listen('.chat.message', (data) => {
        const { message, user, chat } = data;
        
        const messageHtml = `
            <div class="flex justify-start" data-message-id="${chat.id}">
                <div class="max-w-[80%]">
                    <div class="flex items-center gap-2">
                        <span class="text-sm text-gray-600">${user.name}</span>
                    </div>
                    <div class="bg-gray-100 rounded-lg px-3 py-2">
                        ${message}
                    </div>
                    <div class="text-xs text-gray-500 mt-1">${new Date(chat.created_at).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' })}</div>
                </div>
            </div>
        `;
        
        chatMessages.insertAdjacentHTML('beforeend', messageHtml);
        scrollToBottom();

        // Mark message as read
        fetch('/chat/mark-read', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                message_id: chat.id
            })
        });
    });

    // Typing indicator
    channel.listenForWhisper('typing', (data) => {
        const typingText = `${data.user} sedang mengetik...`;
        typingIndicator.querySelector('span').textContent = typingText;
        typingIndicator.classList.remove('hidden');

        // Clear previous timeout
        if (window.typingTimeout) {
            clearTimeout(window.typingTimeout);
        }

        // Hide typing indicator after timeout
        window.typingTimeout = setTimeout(() => {
            typingIndicator.classList.add('hidden');
        }, typingTimeout * 1000);
    });

    // Form submission
    messageForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const message = messageInput.value.trim();
        if (!message) return;

        // Disable form
        messageInput.disabled = true;
        sendButton.disabled = true;
        buttonText.classList.add('hidden');
        buttonLoading.classList.remove('hidden');
        errorMessage.classList.add('hidden');

        try {
            const response = await fetch('/chat/send', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    message,
                    group_code: '{{ $groupCode }}'
                })
            });

            const result = await response.json();

            if (response.ok) {
                messageInput.value = '';
                const messageHtml = `
                    <div class="flex justify-end" data-message-id="${result.data.id}">
                        <div class="max-w-[80%]">
                            <div class="flex items-center gap-2 justify-end">
                                <span class="text-sm text-gray-600">{{ auth()->user()->name }}</span>
                                <span class="read-status text-xs text-gray-400">✓</span>
                            </div>
                            <div class="bg-blue-500 text-white rounded-lg px-3 py-2">
                                ${result.data.message}
                            </div>
                            <div class="text-xs text-gray-500 mt-1">${result.data.time}</div>
                        </div>
                    </div>
                `;
                chatMessages.insertAdjacentHTML('beforeend', messageHtml);
                scrollToBottom(true);
            } else {
                throw new Error(result.message);
            }
        } catch (error) {
            errorMessage.textContent = error.message || 'Terjadi kesalahan. Silakan coba lagi.';
            errorMessage.classList.remove('hidden');
        } finally {
            messageInput.disabled = false;
            sendButton.disabled = false;
            buttonText.classList.remove('hidden');
            buttonLoading.classList.add('hidden');
            messageInput.focus();
        }
    });

    // Typing event
    let typingThrottle;
    messageInput.addEventListener('input', () => {
        clearTimeout(typingThrottle);
        typingThrottle = setTimeout(() => {
            channel.whisper('typing', {
                user: '{{ auth()->user()->name }}'
            });
        }, 300);
    });

    // Read receipt handling
    channel.listen('.message.read', (data) => {
        const { message_id } = data;
        const messageElement = document.querySelector(`[data-message-id="${message_id}"]`);
        if (messageElement) {
            const readStatus = messageElement.querySelector('.read-status');
            if (readStatus) {
                readStatus.textContent = '✓✓';
            }
        }
    });
});
</script>
@endpush
@endsection