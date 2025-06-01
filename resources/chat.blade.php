<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Chat Grup {{ $groupName }}</title>
    <script src="https://js.pusher.com/7.2/pusher.min.js"></script>
    <script src="{{ asset('js/app.js') }}"></script>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.3.2/dist/tailwind.min.css" rel="stylesheet" />
</head>
<body class="bg-gray-100">
    <div class="max-w-2xl mx-auto p-4">
        <h1 class="text-2xl font-bold mb-4">Chat Grup {{ $groupName }}</h1>

        <div id="chat-box" class="h-96 overflow-y-auto bg-white border p-3 rounded mb-4">
            @foreach ($chats as $chat)
                <div class="mb-2">
                    <span class="font-semibold">{{ $chat->user->name }}</span>:
                    <span>{{ $chat->message }}</span>
                    <span class="text-xs text-gray-400 float-right">{{ $chat->created_at->format('H:i') }}</span>
                </div>
            @endforeach
        </div>

        <form id="chat-form" class="flex">
            @csrf
            <input type="text" id="message" placeholder="Tulis pesan..." class="flex-grow border rounded p-2" autocomplete="off" />
            <button type="submit" class="ml-2 px-4 py-2 bg-blue-600 text-white rounded">Kirim</button>
        </form>

        <form method="POST" action="{{ route('logout') }}" class="mt-6">
            @csrf
            <button type="submit" class="text-red-600 underline">Logout</button>
        </form>
    </div>

<script>
    const userName = "{{ auth()->user()->name }}";
    const groupCode = "{{ auth()->user()->group_code }}";

    window.Pusher = Pusher;
    const echo = new Echo({
        broadcaster: 'pusher',
        key: '{{ env("PUSHER_APP_KEY") }}',
        cluster: '{{ env("PUSHER_APP_CLUSTER") }}',
        forceTLS: true,
    });

    const chatBox = document.getElementById('chat-box');
    const chatForm = document.getElementById('chat-form');
    const messageInput = document.getElementById('message');

    echo.channel('group-chat.' + groupCode)
        .listen('ChatMessageSent', (e) => {
            const div = document.createElement('div');
            div.classList.add('mb-2');
            div.innerHTML = `<span class="font-semibold">${e.user}</span>: ${e.message} <span class="text-xs text-gray-400 float-right">${e.time}</span>`;
            chatBox.appendChild(div);
            chatBox.scrollTop = chatBox.scrollHeight;

            if (Notification.permission === 'granted' && e.user !== userName) {
                new Notification('Pesan baru dari ' + e.user, { body: e.message });
            }
        });

    chatForm.addEventListener('submit', function(e){
        e.preventDefault();
        const message = messageInput.value.trim();
        if (!message) return;

        fetch("{{ route('chat.send') }}", {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}",
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
            body: JSON.stringify({ message: message }),
        })
        .then(res => res.json())
        .then(data => {
            if(data.status === 'success'){
                messageInput.value = '';
            } else {
                alert('Gagal mengirim pesan.');
            }
        })
        .catch(() => alert('Gagal mengirim pesan.'));
    });

    if (Notification.permission !== 'granted') {
        Notification.requestPermission();
    }

    chatBox.scrollTop = chatBox.scrollHeight;
</script>
</body>
</html>
