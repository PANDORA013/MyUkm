<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - MyUKM</title>
    @vite('resources/css/app.css')
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="min-h-screen bg-gradient-to-br from-purple-600 to-indigo-700">
    <div class="container mx-auto px-4 py-8 max-w-3xl">
        <div class="bg-white rounded-2xl shadow-2xl overflow-hidden">
            <!-- Header -->
            <div class="bg-purple-600 p-4">
                <div class="flex justify-between items-center">
                    <h2 class="text-xl font-bold text-white">UKM {{ $groupName }} ({{ $groupCode }})</h2>
                    <div class="space-x-2">
                        <a href="{{ route('ukm.index') }}?join=1" class="bg-white text-purple-600 px-4 py-1 rounded-lg text-sm hover:bg-purple-100 transition-colors">
                            + Join UKM Lain
                        </a>
                        <a href="{{ route('ukm.index') }}" class="bg-white text-purple-600 px-4 py-1 rounded-lg text-sm hover:bg-purple-100 transition-colors">
                            Ganti UKM
                        </a>
                    </div>
                </div>
            </div>

            <!-- Chat Area -->
            <div class="p-4">
                <!-- Chat Messages -->
                <div id="chat-box" class="bg-gray-50 rounded-lg p-4 h-[400px] overflow-y-auto mb-4">
                    @foreach($chats as $chat)
                        <div class="chat-message mb-4 {{ $chat->user_id === auth()->id() ? 'text-right' : 'text-left' }}">
                            <div class="inline-block">
                                <div class="font-medium text-sm text-purple-600 mb-1">{{ $chat->user->name }}</div>
                                <div class="{{ $chat->user_id === auth()->id() ? 'bg-purple-500 text-white' : 'bg-gray-200 text-gray-800' }} rounded-lg px-4 py-2">
                                    {{ $chat->message }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Chat Input -->
                <form id="chat-form" class="space-y-4">
                    @csrf
                    <div class="flex space-x-2">
                        <input 
                            type="text" 
                            name="message" 
                            id="message-input"
                            class="flex-1 rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500"
                            placeholder="Tulis pesan..."
                            required
                        >
                        <button 
                            type="submit"
                            class="bg-purple-600 text-white px-6 py-2 rounded-lg hover:bg-purple-700 transition-colors"
                        >
                            Kirim
                        </button>
                    </div>
                </form>
            </div>

            <!-- Footer -->
            <div class="border-t p-4">
                <form method="POST" action="{{ route('logout.group') }}">
                    @csrf
                    <button type="submit" class="w-full bg-red-500 text-white py-2 rounded-lg hover:bg-red-600 transition-colors">
                        Logout UKM
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    <script>
        // Auto-scroll to bottom
        const chatBox = document.getElementById('chat-box');
        chatBox.scrollTop = chatBox.scrollHeight;

        // Initialize Pusher
        const pusher = new Pusher("{{ config('broadcasting.connections.pusher.key') }}", {
            cluster: "{{ config('broadcasting.connections.pusher.options.cluster') }}"
        });

        // Subscribe to the chat channel
        const channel = pusher.subscribe("chat.{{ $groupCode }}");
        channel.bind("ChatMessageSent", function(data) {
            const currentUserId = parseInt("{{ auth()->id() }}");
            if (data.user.id !== currentUserId) {
                const newMessage = document.createElement("div");
                newMessage.className = "chat-message mb-4 text-left";
                newMessage.innerHTML = 
                    '<div class="inline-block">' +
                        '<div class="font-medium text-sm text-purple-600 mb-1">' + data.user.name + '</div>' +
                        '<div class="bg-gray-200 text-gray-800 rounded-lg px-4 py-2">' +
                            data.message +
                        '</div>' +
                    '</div>';
                chatBox.appendChild(newMessage);
                chatBox.scrollTop = chatBox.scrollHeight;
                
                // Show browser notification if permission is granted
                if (Notification.permission === "granted") {
                    new Notification("New Message", { 
                        body: data.user.name + ": " + data.message
                    });
                }
            }
        });

        // Request notification permission
        if (Notification.permission !== 'granted') {
            Notification.requestPermission();
        }

        // AJAX form submission
        document.getElementById('chat-form').addEventListener('submit', function(e) {
            e.preventDefault();

            const input = document.getElementById('message-input');
            const message = input.value;

            fetch("{{ route('chat.send') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ message })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    const newMessage = document.createElement('div');
                    newMessage.className = 'chat-message mb-4 text-right';
                    newMessage.innerHTML = 
                        '<div class="inline-block">' +
                            '<div class="font-medium text-sm text-purple-600 mb-1">{{ auth()->user()->name }}</div>' +
                            '<div class="bg-purple-500 text-white rounded-lg px-4 py-2">' +
                                message +
                            '</div>' +
                        '</div>';
                    chatBox.appendChild(newMessage);
                    chatBox.scrollTop = chatBox.scrollHeight;
                    input.value = '';
                }
            });
        });
    </script>
</body>
</html>