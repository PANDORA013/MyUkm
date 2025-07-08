document.addEventListener('DOMContentLoaded', function() {
    // Enable pusher logging - don't include this in production
    Pusher.logToConsole = true;

    try {
        // Initialize Pusher with direct values
        const pusher = new Pusher(window.pusherKey, {
            cluster: window.pusherCluster,
            wsHost: window.pusherHost,
            wsPort: window.pusherPort,
            wssPort: window.pusherPort,
            forceTLS: (window.location.protocol === 'https:' || window.pusherScheme === 'https'),
            enabledTransports: ['ws', 'wss']
        });

        // Subscribe to channel and bind to event
        const channel = pusher.subscribe('test-channel');
        channel.bind('test-event', function(data) {
            const messagesDiv = document.getElementById('messages');
            if (messagesDiv) {
                const messageElement = document.createElement('div');
                messageElement.className = 'bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-4 mb-4';
                messageElement.textContent = 'Received: ' + JSON.stringify(data);
                messagesDiv.prepend(messageElement);
            }
        });

        // Update connection status
        const updateStatus = (text, isError = false) => {
            const statusElement = document.getElementById('connection-status');
            if (statusElement) {
                statusElement.textContent = text;
                statusElement.className = isError 
                    ? 'inline-block px-4 py-2 rounded-full bg-red-100 text-red-800' 
                    : 'inline-block px-4 py-2 rounded-full bg-green-100 text-green-800';
            }
        };

        pusher.connection.bind('connected', () => updateStatus('Connected to Pusher'));
        pusher.connection.bind('error', (err) => 
            updateStatus('Error: ' + (err.error?.data?.message || 'Unknown error'), true)
        );

        // Make sendTestMessage globally available
        window.sendTestMessage = function() {
            fetch('/broadcasting/test', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    message: 'Hello from the web!',
                    time: new Date().toISOString()
                })
            })
            .then(response => response.json())
            .then(data => console.log('Message sent:', data))
            .catch(error => console.error('Error:', error));
        };
    } catch (error) {
        console.error('Pusher initialization error:', error);
        const statusElement = document.getElementById('connection-status');
        if (statusElement) {
            statusElement.textContent = 'Initialization error: ' + error.message;
            statusElement.className = 'inline-block px-4 py-2 rounded-full bg-red-100 text-red-800';
        }
    }
});
