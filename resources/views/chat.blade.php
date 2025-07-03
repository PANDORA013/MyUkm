@extends(Auth::user()->role === 'admin_grup' ? 'layouts.admin_grup' : 'layouts.user')

@section('title', 'Chat UKM - ' . $groupName)

@push('styles')
<style>
    .chat-container {
        height: calc(100vh - 160px);
        display: flex;
        flex-direction: column;
        border-radius: 0.5rem;
        overflow: hidden;
    }
    .chat-messages {
        flex: 1;
        overflow-y: auto;
        padding: 12px;
        background-color: #f8f9fa;
    }
    .message-container {
        overflow: hidden;
        margin-bottom: 10px;
        clear: both;
    }
    .message {
        max-width: 80%;
    }
    .message-content {
        padding: 8px 12px;
        border-radius: 1rem;
        position: relative;
        display: inline-block;
    }
    .message-outgoing {
        float: right;
        text-align: right;
    }
    .message-outgoing .message-content {
        background-color: #4e73df;
        color: white;
        border-bottom-right-radius: 0.2rem;
    }
    .message-incoming {
        float: left;
    }
    .message-incoming .message-content {
        background-color: #e9ecef;
        color: #212529;
        border-bottom-left-radius: 0.2rem;
    }
    .message-sender {
        font-size: 0.75rem;
        margin-bottom: 2px;
        color: #6c757d;
    }
    .message-time {
        font-size: 0.7rem;
        color: #adb5bd;
        margin-top: 4px;
        display: block;
    }
    .chat-form {
        padding: 12px;
        background-color: #fff;
        border-top: 1px solid #e3e6f0;
    }
    .chat-header {
        background-color: #fff;
        padding: 12px 15px;
        border-bottom: 1px solid #e3e6f0;
    }
    .chat-online-indicator {
        width: 8px;
        height: 8px;
        background-color: #10b981;
        border-radius: 50%;
        display: inline-block;
        margin-right: 4px;
    }
    .chat-offline-indicator {
        width: 8px;
        height: 8px;
        background-color: #9ca3af;
        border-radius: 50%;
        display: inline-block;
        margin-right: 4px;
    }
    .members-badge {
        background-color: #4e73df;
        color: white;
        padding: 0.2rem 0.5rem;
        border-radius: 0.25rem;
        font-size: 0.75rem;
        margin-left: 0.5rem;
    }
    .typing-indicator {
        font-size: 0.8rem;
        color: #6c757d;
        font-style: italic;
        padding: 0 12px;
        margin: 4px 0;
        min-height: 20px;
    }
    .page-header {
        margin-bottom: 1rem;
        border-bottom: 1px solid #e3e6f0;
        padding-bottom: 0.75rem;
    }
    .card {
        margin-bottom: 0;
        border-radius: 0.5rem;
        overflow: hidden;
    }
</style>
@endpush

@section('content')
<div class="container-fluid p-0">
    <h4 class="page-header">
        <i class="fas fa-comments me-2"></i>Chat UKM {{ $groupName }}
    </h4>
    
    <div class="row g-0">
        <div class="col-12">
            <div class="card">
                <div class="chat-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0">{{ $groupName }}</h5>
                            <div class="text-muted small">
                                <span class="chat-online-indicator"></span> <span id="onlineCount">0</span> online
                                <span class="members-badge"><i class="fas fa-users me-1"></i> <span id="totalMembers">0</span></span>
                            </div>
                        </div>
                        <a href="{{ route('ukm.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-arrow-left me-1"></i> Kembali
                        </a>
                    </div>
                </div>

                <div class="card-body p-0">
                    <div class="chat-container">
                        <div class="chat-messages" id="chat-messages">
                            <!-- Chat messages will be loaded here dynamically -->
                            <div class="text-center my-3 text-muted">
                                <div class="spinner-border spinner-border-sm me-2" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                Memuat pesan...
                            </div>
                        </div>
                        
                        <div class="typing-indicator" id="typing-indicator"></div>
                        
                        @if(isset($isMuted) && $isMuted)
                            <div class="alert alert-warning m-2" role="alert">
                                <i class="fas fa-volume-mute me-2"></i> Halo, kamu lagi di-mute dulu, biar suasana grup tetap adem kayak es kopi susu~ Balik ngobrol lagi nanti ya!
                            </div>
                        @endif
                        
                        <div class="chat-form">
                            <form id="chat-form" class="d-flex">
                                <input type="hidden" id="group-id" value="{{ $groupId }}">
                                <input type="hidden" id="group-code" value="{{ $groupCode }}">
                                <input type="hidden" id="is-muted" value="{{ isset($isMuted) && $isMuted ? 'true' : 'false' }}">
                                <input type="text" class="form-control me-2" id="message-input" placeholder="{{ isset($isMuted) && $isMuted ? 'Anda sedang dimute dan tidak dapat mengirim pesan' : 'Ketik pesan Anda...' }}" autocomplete="off" {{ isset($isMuted) && $isMuted ? 'disabled' : '' }}>
                                <button type="submit" class="btn btn-primary" {{ isset($isMuted) && $isMuted ? 'disabled' : '' }} aria-label="Kirim pesan" title="Kirim pesan">
                                    <i class="fas fa-paper-plane" aria-hidden="true"></i>
                                    <span class="visually-hidden">Kirim</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://js.pusher.com/7.0/pusher.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const groupId = document.getElementById('group-id').value;
        const groupCode = document.getElementById('group-code').value;
        const messageInput = document.getElementById('message-input');
        const chatForm = document.getElementById('chat-form');
        const chatMessages = document.getElementById('chat-messages');
        const typingIndicator = document.getElementById('typing-indicator');
        const sendButton = document.querySelector('#chat-form button[type="submit"]');
        const isMuted = document.getElementById('is-muted').value === 'true';
        
        // Variable to store current CSRF token
        let csrfToken = '{{ csrf_token() }}';
        
        // Refresh CSRF token and keep session alive every 10 minutes
        setInterval(refreshCsrfToken, 10 * 60 * 1000);

        // Also refresh session on user interaction (typing, clicking)
        document.addEventListener('click', function() {
            // Throttle to avoid too many requests - only refresh if last refresh was more than 5 minutes ago
            if (!window.lastRefreshTime || (Date.now() - window.lastRefreshTime) > 5 * 60 * 1000) {
                refreshCsrfToken();
            }
        });

        // Debounce for typing and send typing indicator
        let typingRefreshTimeout;
        let typingIndicatorTimeout;
        messageInput.addEventListener('input', function() {
            // Handle CSRF token refresh
            clearTimeout(typingRefreshTimeout);
            typingRefreshTimeout = setTimeout(() => {
                if (!window.lastRefreshTime || (Date.now() - window.lastRefreshTime) > 5 * 60 * 1000) {
                    refreshCsrfToken();
                }
            }, 5000);
            
            // Handle typing indicator
            clearTimeout(typingIndicatorTimeout);
            
            fetch('{{ route('chat.typing') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    group_id: groupId
                })
            })
            .then(response => {
                if (response.headers.get('content-type')?.includes('application/json')) {
                    return safeJsonParse(response);
                }
                return response;
            })
            .catch(error => console.error('Error sending typing indicator:', error));
            
            typingIndicatorTimeout = setTimeout(() => {}, 3000);
        });
        
        // Function to refresh CSRF token and keep session alive
        function refreshCsrfToken() {
            window.lastRefreshTime = Date.now();
            return fetch('/csrf-refresh', {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                // Include credentials to ensure cookies are sent
                credentials: 'same-origin'
            })
            .then(response => {
                // Check for HTML response
                const contentType = response.headers.get('content-type');
                if (contentType && contentType.includes('text/html')) {
                    console.error('Received HTML instead of JSON when refreshing CSRF token. Session might have expired.');
                    throw new Error('Session expired');
                }
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.token) {
                    csrfToken = data.token;
                    console.log('CSRF token refreshed at ' + new Date().toLocaleTimeString());
                    
                    // Update meta tag for future requests
                    const metaTag = document.querySelector('meta[name="csrf-token"]');
                    if (metaTag) {
                        metaTag.setAttribute('content', data.token);
                    }
                    
                    // If we previously had an error, hide it now
                    const errorBanner = document.getElementById('connection-error');
                    if (errorBanner && !errorBanner.classList.contains('hidden')) {
                        errorBanner.classList.add('hidden');
                    }
                    
                    // Re-load messages if we had a session expired error before
                    if (window.hadSessionExpiredError) {
                        window.hadSessionExpiredError = false;
                        console.log('Attempting to reload messages after session refresh');
                        loadMessages();
                    }
                    
                    // Reset session expired attempts counter
                    window.sessionExpiredAttempts = 0;
                    
                    return data.token;
                } else {
                    throw new Error('No token received');
                }
            })
            .catch(error => {
                console.warn('Failed to refresh CSRF token:', error);
                if (error.message === 'Session expired' || error.message.includes('HTTP 401')) {
                    window.hadSessionExpiredError = true;
                    showConnectionError('Sesi Anda telah berakhir. Mencoba memperbarui otomatis...');
                    
                    // Try to redirect to login if after multiple attempts we still have session issues
                    window.sessionExpiredAttempts = (window.sessionExpiredAttempts || 0) + 1;
                    if (window.sessionExpiredAttempts >= 3) {
                        showConnectionError('Sesi berakhir. Mengarahkan ke halaman login...');
                        setTimeout(() => {
                            window.location.href = '{{ route('login') }}?redirect={{ urlencode(request()->fullUrl()) }}';
                        }, 2000);
                    } else {
                        // Try again in 5 seconds
                        setTimeout(refreshCsrfToken, 5000);
                    }
                } else {
                    showConnectionError('Gagal memperbarui sesi. Mencoba lagi...');
                    setTimeout(refreshCsrfToken, 5000);
                }
                throw error;
            });
        }
        
        // Helper function for safe JSON parsing that handles HTML responses
        function safeJsonParse(response) {
            const contentType = response.headers.get('content-type');
            if (contentType && contentType.includes('text/html')) {
                console.error('Received HTML instead of JSON. Session might have expired.');
                showConnectionError('Sesi Anda mungkin telah berakhir. Silakan refresh halaman.');
                throw new Error('Session expired');
            }
            return response.json();
        }
        
        // Pusher setup with error handling
        let pusher;
        let channel;
        
        try {
            pusher = new Pusher('{{ env('PUSHER_APP_KEY') }}', {
                cluster: '{{ env('PUSHER_APP_CLUSTER') }}',
                forceTLS: true
            });
            
            // Subscribe to the channel for this group
            channel = pusher.subscribe('group.' + groupCode);
            
            // Add connection error handling
            pusher.connection.bind('error', function(err) {
                console.warn('Pusher connection error:', err);
                // Show a small notification that real-time updates are not available
                showConnectionWarning();
            });
        } catch (error) {
            console.warn('Pusher initialization error:', error);
            showConnectionWarning();
        }
        
        // Function to show connection warning
        function showConnectionWarning() {
            const warningEl = document.createElement('div');
            warningEl.className = 'alert alert-warning small m-1';
            warningEl.innerHTML = '<i class="fas fa-exclamation-triangle me-1"></i> Notifikasi real-time tidak tersedia. Refresh halaman untuk melihat pesan terbaru.';
            
            // Insert before the chat form
            document.querySelector('.chat-form').before(warningEl);
            
            // Auto-hide after 10 seconds
            setTimeout(() => {
                warningEl.style.opacity = '0';
                warningEl.style.transition = 'opacity 1s';
                setTimeout(() => warningEl.remove(), 1000);
            }, 10000);
        }
        
        // Only set up event handlers if channel exists (real-time available)
        if (channel) {
            // Handle incoming messages
            channel.bind('new-message', function(data) {
                appendMessage(data);
                scrollToBottom();
            });
            
            // Handle typing indicators
            channel.bind('typing', function(data) {
                if (data.user_id !== {{ Auth::id() }}) {
                    typingIndicator.textContent = data.name + ' sedang mengetik...';
                    setTimeout(() => {
                        typingIndicator.textContent = '';
                    }, 3000);
                }
            });
            
            // Handle online status updates
            channel.bind('user-online', function(data) {
                document.getElementById('onlineCount').textContent = data.online_count;
                document.getElementById('totalMembers').textContent = data.total_members;
            });
            
            // Handle user mute status changes
            channel.bind('user-mute-status', function(data) {
                if (data.user_id === {{ Auth::id() }}) {
                    if (data.is_muted) {
                        // User was muted
                        showMutedAlert('Anda telah di-mute oleh admin grup.');
                        document.getElementById('message-input').disabled = true;
                        document.getElementById('message-input').placeholder = 'Anda sedang dimute dan tidak dapat mengirim pesan';
                        document.querySelector('#chat-form button[type="submit"]').disabled = true;
                        document.getElementById('is-muted').value = 'true';
                        window.isMuted = true;
                    } else {
                        // User was unmuted
                        document.getElementById('message-input').disabled = false;
                        document.getElementById('message-input').placeholder = 'Ketik pesan Anda...';
                        document.querySelector('#chat-form button[type="submit"]').disabled = false;
                        document.getElementById('is-muted').value = 'false';
                        window.isMuted = false;
                        
                        // Show unmuted message
                        let alertDiv = document.createElement('div');
                        alertDiv.className = 'alert alert-success alert-dismissible fade show';
                        alertDiv.setAttribute('role', 'alert');
                        alertDiv.innerHTML = `
                            <i class="fas fa-volume-up me-2"></i> Anda telah di-unmute dan dapat mengirim pesan kembali.
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        `;
                        
                        // Insert before chat form
                        let chatForm = document.querySelector('.chat-form');
                        chatForm.parentNode.insertBefore(alertDiv, chatForm);
                        
                        // Auto dismiss after 5 seconds
                        setTimeout(() => {
                            alertDiv.classList.remove('show');
                            setTimeout(() => alertDiv.remove(), 500);
                        }, 5000);
                    }
                }
            });
        }
        
        // Send message
        chatForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Prevent sending if user is muted
            if (isMuted) {
                // Show alert if needed
                showMutedAlert();
                return;
            }
            
            const message = messageInput.value.trim();
            if (message) {
                // Disable input while sending
                messageInput.disabled = true;
                sendButton.disabled = true;
                
                retryWithTokenRefresh(async () => {
                    const response = await fetch('{{ route('chat.send') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({
                            message: message,
                            group_id: groupId,
                            group_code: groupCode
                        })
                    });
                    
                    if (!response.ok) {
                        // Try to parse the error response safely
                        const data = await safeJsonParse(response).catch(e => {
                            // If JSON parsing fails, return a generic error
                            return { status: 'error', message: 'Server error: ' + response.status };
                        });
                        
                        if (response.status === 403 && data.message && data.message.includes('di-mute')) {
                            showMutedAlert(data.message);
                            throw new Error(data.message);
                        } else if (response.status === 401 || response.status === 419) {
                            throw new Error('Session expired');
                        } else {
                            throw new Error(data.message || 'Error: ' + response.status);
                        }
                    }
                    
                    return await safeJsonParse(response);
                })
                .then(data => {
                    if (data.status === 'success') {
                        messageInput.value = '';
                        // Message will appear via Pusher or polling
                    } else {
                        throw new Error(data.message || 'Unknown error');
                    }
                })
                .catch(error => {
                    console.error('Error sending message:', error);
                    if (error.message !== 'Session expired') {
                        showConnectionError('Gagal mengirim pesan: ' + error.message);
                    }
                })
                .finally(() => {
                    // Re-enable input
                    messageInput.disabled = false;
                    sendButton.disabled = false;
                    messageInput.focus();
                });
            }
        });
        
        // Function to show connection error
        function showConnectionError(message) {
            const errorEl = document.createElement('div');
            errorEl.className = 'alert alert-danger small m-1 alert-session-error';
            errorEl.innerHTML = '<i class="fas fa-exclamation-circle me-1"></i> ' + message + ' <a href="javascript:window.location.reload()" class="alert-link">Refresh</a>';
            
            // Remove existing error alerts
            document.querySelectorAll('.alert-session-error').forEach(el => el.remove());
            
            // Insert before the chat form
            document.querySelector('.chat-form').before(errorEl);
            
            // Auto-hide after 15 seconds
            setTimeout(() => {
                errorEl.style.opacity = '0';
                errorEl.style.transition = 'opacity 1s';
                setTimeout(() => errorEl.remove(), 1000);
            }, 15000);
        }
        
        // Function to show muted alert
        function showMutedAlert(message) {
            let alertMessage = message || 'Halo, kamu lagi di-mute dulu, biar suasana grup tetap adem kayak es kopi susu~ Balik ngobrol lagi nanti ya!';
            
            // Check if alert already exists
            let existingAlert = document.querySelector('.alert-muted-warning');
            if (!existingAlert) {
                let alertDiv = document.createElement('div');
                alertDiv.className = 'alert alert-warning alert-dismissible fade show alert-muted-warning';
                alertDiv.setAttribute('role', 'alert');
                alertDiv.innerHTML = `
                    <i class="fas fa-volume-mute me-2"></i> ${alertMessage}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                `;
                
                // Insert before chat form
                let chatForm = document.querySelector('.chat-form');
                chatForm.parentNode.insertBefore(alertDiv, chatForm);
                
                // Auto dismiss after 5 seconds
                setTimeout(() => {
                    alertDiv.classList.remove('show');
                    setTimeout(() => alertDiv.remove(), 500);
                }, 5000);
            }
        }
        
        // Load messages
        async function loadMessages() {
            try {
                await retryWithTokenRefresh(async () => {
                    const response = await fetch(`{{ route('chat.messages') }}?group_id=${groupId}`, {
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    
                    const data = await safeJsonParse(response);
                    
                    if (data.status === 'success') {
                        chatMessages.innerHTML = '';
                        data.messages.forEach(message => appendMessage(message));
                        scrollToBottom();
                    } else {
                        throw new Error(data.message || 'Failed to load messages');
                    }
                    
                    return data;
                });
            } catch (error) {
                console.error('Error loading messages:', error);
                if (error.message !== 'Session expired') {
                    showConnectionError('Gagal memuat pesan. Mencoba lagi...');
                    setTimeout(loadMessages, 5000);
                } else {
                    showConnectionError('Gagal memuat pesan. Coba refresh halaman.');
                }
            }
        }
        
        // Join chat room
        joinChatRoom();
        
        // Load messages
        loadMessages();
        
        // Handle window unload
        window.addEventListener('beforeunload', leaveChatRoom);
        
        // Message template function
        function appendMessage(data) {
            const isCurrentUser = data.user_id === {{ Auth::id() }};
            const messageContainer = document.createElement('div');
            messageContainer.className = 'message-container';
            
            const messageDiv = document.createElement('div');
            messageDiv.className = `message ${isCurrentUser ? 'message-outgoing' : 'message-incoming'}`;
            
            if (!isCurrentUser) {
                const senderDiv = document.createElement('div');
                senderDiv.className = 'message-sender';
                senderDiv.textContent = data.name;
                messageDiv.appendChild(senderDiv);
            }
            
            const contentDiv = document.createElement('div');
            contentDiv.className = 'message-content';
            contentDiv.innerHTML = data.message;
            messageDiv.appendChild(contentDiv);
            
            const timeDiv = document.createElement('div');
            timeDiv.className = 'message-time';
            timeDiv.textContent = formatTime(data.created_at || new Date());
            messageDiv.appendChild(timeDiv);
            
            messageContainer.appendChild(messageDiv);
            chatMessages.appendChild(messageContainer);
        }
        
        function formatTime(timestamp) {
            const date = new Date(timestamp);
            return date.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
        }
        
        function scrollToBottom() {
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }
        
        function joinChatRoom() {
            fetch('{{ route('chat.join') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    group_id: groupId
                })
            })
            .then(response => {
                if (response.headers.get('content-type')?.includes('application/json')) {
                    return safeJsonParse(response);
                }
                return response;
            })
            .catch(error => console.error('Error joining chat room:', error));
        }
        
        function leaveChatRoom() {
            fetch('{{ route('chat.logout') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    group_id: groupId
                })
            })
            .then(response => {
                if (response.headers.get('content-type')?.includes('application/json')) {
                    return safeJsonParse(response);
                }
                return response;
            })
            .catch(error => console.error('Error leaving chat room:', error));
        }
        
        // Helper function to retry API calls with token refresh
        async function retryWithTokenRefresh(fetchFunction, maxRetries = 2) {
            for (let attempt = 1; attempt <= maxRetries; attempt++) {
                try {
                    return await fetchFunction();
                } catch (error) {
                    console.warn(`API call attempt ${attempt} failed:`, error);
                    
                    // If this is a session/auth error and we have retries left
                    if ((error.message === 'Session expired' || error.message.includes('401') || error.message.includes('419')) && attempt < maxRetries) {
                        console.log(`Attempting to refresh CSRF token before retry ${attempt + 1}`);
                        try {
                            await refreshCsrfToken();
                            console.log('Token refreshed, retrying API call...');
                            // Continue to next iteration for retry
                        } catch (tokenError) {
                            console.error('Failed to refresh token:', tokenError);
                            throw error; // Give up if token refresh fails
                        }
                    } else {
                        throw error; // No more retries or different error
                    }
                }
            }
        }
    });
</script>
@endpush
