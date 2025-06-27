<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Pusher Test</title>
    <script src="https://js.pusher.com/8.4.0/pusher.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script>
    // Pass Pusher config from PHP to JavaScript
    window.pusherKey = '{{ addslashes(config("broadcasting.connections.pusher.key")) }}';
    window.pusherCluster = '{{ addslashes(config("broadcasting.connections.pusher.options.cluster")) }}';
    window.pusherHost = '{{ addslashes(config("broadcasting.connections.pusher.options.host")) }}';
    window.pusherPort = parseInt('{{ config("broadcasting.connections.pusher.options.port") }}', 10);
    window.pusherScheme = '{{ addslashes(config("broadcasting.connections.pusher.options.scheme", "https")) }}';
    </script>
    <script src="{{ asset('js/pusher-test.js') }}"></script>
</head>
<body class="bg-gray-100 min-h-screen p-8">
    <div class="max-w-4xl mx-auto bg-white rounded-lg shadow-md p-6">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">Pusher Test</h1>
        
        <div class="mb-6">
            <h2 class="text-xl font-semibold mb-2">Connection Status</h2>
            <div id="connection-status" class="inline-block px-4 py-2 rounded-full bg-green-100 text-green-800">
                Connecting to Pusher...
            </div>
        </div>

        <div class="mb-6">
            <h2 class="text-xl font-semibold mb-2">Test Controls</h2>
            <button onclick="sendTestMessage()" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Send Test Message
            </button>
        </div>

        <div class="mb-6">
            <h2 class="text-xl font-semibold mb-2">Received Messages</h2>
            <div id="messages" class="space-y-4">
                <!-- Messages will appear here -->
                <p class="text-gray-500 italic">No messages received yet. Send a test message to see it here.</p>
            </div>
        </div>

        <div class="mt-8 pt-6 border-t border-gray-200">
            <h2 class="text-xl font-semibold mb-2">Connection Details</h2>
            <div class="bg-gray-50 p-4 rounded-md overflow-x-auto">
                <pre class="text-sm text-gray-700">
Key: {{ config('broadcasting.connections.pusher.key') }}
Cluster: {{ config('broadcasting.connections.pusher.options.cluster'] }}
Host: {{ config('broadcasting.connections.pusher.options.host'] }}
Port: {{ config('broadcasting.connections.pusher.options.port') }}
Scheme: {{ config('broadcasting.connections.pusher.options.scheme') }}
                </pre>
            </div>
        </div>
    </div>

    <script>
        // Update connection status
        pusher.connection.bind('connected', function() {
            document.getElementById('connection-status').textContent = 'Connected to Pusher';
            document.getElementById('connection-status').className = 'inline-block px-4 py-2 rounded-full bg-green-100 text-green-800';
        });

        pusher.connection.bind('error', function(err) {
            document.getElementById('connection-status').textContent = 'Connection Error: ' + err.error.data.message;
            document.getElementById('connection-status').className = 'inline-block px-4 py-2 rounded-full bg-red-100 text-red-800';
        });
    </script>
</body>
</html>
