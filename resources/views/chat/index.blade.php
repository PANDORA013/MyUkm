@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-100">
    <div class="max-w-4xl mx-auto py-6">
        <div class="bg-white rounded-lg shadow">
            {{-- Chat Header --}}
            <div class="border-b px-6 py-4 flex justify-between items-center">
                <div>
                    <h1 class="text-xl font-semibold">{{ $groupName }}</h1>
                    <p class="text-sm text-gray-600">Kode: {{ $groupCode }}</p>
                </div>
                <a href="{{ route('ukm.index') }}" class="text-blue-600 hover:text-blue-800">
                    &larr; Kembali ke Daftar UKM
                </a>
            </div>

            {{-- Chat Messages --}}
            <div id="chat-messages" class="h-[500px] overflow-y-auto p-6 space-y-4">
                @foreach($chats as $chat)
                    <div class="flex {{ $chat->user_id === auth()->id() ? 'justify-end' : 'justify-start' }}">
                        <div class="{{ $chat->user_id === auth()->id() ? 'bg-blue-500 text-white' : 'bg-gray-100 text-gray-900' }} rounded-lg px-4 py-2 max-w-[70%]">
                            @if($chat->user_id !== auth()->id())
                                <p class="text-xs font-semibold mb-1">{{ $chat->user->name }}</p>
                            @endif
                            <p class="break-words">{{ $chat->message }}</p>
                            <p class="text-xs mt-1 {{ $chat->user_id === auth()->id() ? 'text-blue-100' : 'text-gray-500' }}">
                                {{ $chat->created_at->format('H:i') }}
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Chat Input --}}
            <div class="border-t px-6 py-4">
                <form id="chat-form" class="flex gap-4">
                    @csrf
                    <input type="hidden" name="group_code" value="{{ $groupCode }}">
                    <input type="text" 
                           name="message" 
                           placeholder="Tulis pesan..." 
                           class="flex-1 rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200"
                           required>
                    <button type="submit" 
                            class="bg-blue-500 text-white px-6 py-2 rounded-lg hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        Kirim
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    const chatForm = document.getElementById('chat-form');
    const chatMessages = document.getElementById('chat-messages');

    // Scroll to bottom of chat
    function scrollToBottom() {
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }
    scrollToBottom();

    // Handle form submission
    chatForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const formData = new FormData(chatForm);
        try {
            const response = await fetch('{{ route("chat.send") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    message: formData.get('message'),
                    group_code: formData.get('group_code')
                })
            });

            const result = await response.json();
            if (response.ok) {
                chatForm.reset();
                scrollToBottom();
            } else {
                alert(result.message);
            }
        } catch (error) {
            alert('Error sending message');
        }
    });

    // Listen for new messages
    window.Echo.private('chat.{{ $groupCode }}')
        .listen('ChatMessageSent', (e) => {
            const messageHtml = `
                <div class="flex justify-start">
                    <div class="bg-gray-100 text-gray-900 rounded-lg px-4 py-2 max-w-[70%]">
                        <p class="text-xs font-semibold mb-1">${e.user.name}</p>
                        <p class="break-words">${e.message}</p>
                        <p class="text-xs mt-1 text-gray-500">${e.time}</p>
                    </div>
                </div>
            `;
            chatMessages.insertAdjacentHTML('beforeend', messageHtml);
            scrollToBottom();
        });
</script>
@endpush
@endsection