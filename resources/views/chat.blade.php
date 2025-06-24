@extends('layouts.app')

@section('content')
<div class="py-8">
    <div class="max-w-4xl mx-auto bg-white border border-gray-200 rounded-xl shadow-sm p-6">
        <h1 class="text-2xl font-semibold text-gray-800 mb-4">Chat – {{ $groupName }}</h1>

        {{-- Chat messages --}}
        <div id="messages" class="h-80 overflow-y-auto border border-gray-100 rounded-lg p-4 mb-4 bg-gray-50">
            {{-- Messages will be appended here by JS --}}
        </div>

        {{-- Message input --}}
        <form id="chat-form" action="{{ route('chat.send') }}" method="POST" class="flex space-x-3">
            @csrf
            <input type="hidden" name="group_code" value="{{ $groupCode }}">
            <input type="text" id="message-input" name="message" placeholder="Ketik pesan..." autocomplete="off" class="flex-grow border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md font-medium">Kirim</button>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
// PHP to JavaScript data passing
const groupId = parseInt('{{ $groupId }}');
const groupCode = '{{ addslashes($groupCode) }}';
const currentUserId = parseInt('{{ auth()->id() }}');

// For debugging
console.log('Group ID:', groupId, 'Group Code:', groupCode, 'User ID:', currentUserId);

const form = document.getElementById('chat-form');
const input = document.getElementById('message-input');
const messagesDiv = document.getElementById('messages');

// Function to show error message
function showError(message) {
    const errorDiv = document.createElement('div');
    errorDiv.className = 'bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4';
    errorDiv.textContent = message;
    messagesDiv.appendChild(errorDiv);
    console.error(message);
}

function appendMessage(sender, message, time = null) {
    const div = document.createElement('div');
    div.className = 'mb-2';
    div.innerHTML = `<span class="font-semibold">${sender}:</span> ${message}${time ? ' <span class="text-xs text-gray-400 ml-2">' + time + '</span>' : ''}`;
    messagesDiv.appendChild(div);
    messagesDiv.scrollTop = messagesDiv.scrollHeight;
}

// Debug information
console.log('Initializing chat...');
console.log('Pusher available:', typeof window.Pusher !== 'undefined');
console.log('Echo available:', typeof window.Echo !== 'undefined');
console.log('Pusher config:', {
    key: window.PUSHER_APP_KEY,
    cluster: window.PUSHER_APP_CLUSTER
});

// Initialize Pusher and Echo
function initializeChat() {
    if (typeof window.Echo === 'undefined') {
        console.log('Echo not available yet, retrying...');
        setTimeout(initializeChat, 100);
        return;
    }

    try {
        console.log('Pusher version:', window.Pusher ? window.Pusher.VERSION : 'Not available');
        
        // Listen for new messages via Echo
        window.Echo.private(`chat.${groupId}`)
            .listen('.chat.message', (e) => {
                console.log('New message received:', e);
                appendMessage(e.user.id === currentUserId ? 'Anda' : e.user.name, e.message, e.time);
            })
            .error((error) => {
                const errorMsg = error && error.message ? error.message : 'Unknown error';
                showError(`Error connecting to chat: ${errorMsg}`);
                console.error('Echo channel error:', error);
            });
            
        console.log('Chat listener initialized successfully');
    } catch (error) {
        const errorMessage = `Failed to initialize chat: ${error && error.message ? error.message : 'Unknown error'}`;
        showError(errorMessage);
        console.error('Chat initialization error:', error);
    }
}

// Start initialization
setTimeout(initializeChat, 100);

// Handle sending messages
form.addEventListener('submit', async (e) => {
    e.preventDefault();
    const message = input.value.trim();
    if (!message) return;

    // Optimistically append your own message
    appendMessage('Anda', message);

    try {
        const response = await fetch("{{ route('chat.send') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name=\"csrf-token\"]').getAttribute('content')
            },
            body: JSON.stringify({ message, group_code: groupCode })
        });
        const data = await response.json();
        if (data.status !== 'success') {
            alert(data.message || 'Gagal mengirim pesan');
        }
    } catch (err) {
        console.error(err);
        alert('Gagal mengirim pesan');
    } finally {
        input.value = '';
        input.focus();
    }
});
</script>
@endpush
