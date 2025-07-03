

<?php $__env->startSection('title', 'Chat UKM - ' . $groupName); ?>

<?php $__env->startPush('styles'); ?>
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
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid p-0">
    <h4 class="page-header">
        <i class="fas fa-comments me-2"></i>Chat UKM <?php echo e($groupName); ?>

    </h4>
    
    <div class="row g-0">
        <div class="col-12">
            <div class="card">
                <div class="chat-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0"><?php echo e($groupName); ?></h5>
                            <div class="text-muted small">
                                <span class="chat-online-indicator"></span> <span id="onlineCount">0</span> online
                                <span class="members-badge"><i class="fas fa-users me-1"></i> <span id="totalMembers">0</span></span>
                            </div>
                        </div>
                        <a href="<?php echo e(route('ukm.index')); ?>" class="btn btn-outline-secondary btn-sm">
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
                        
                        <?php if(isset($isMuted) && $isMuted): ?>
                            <div class="alert alert-warning m-2" role="alert">
                                <i class="fas fa-volume-mute me-2"></i> Halo, kamu lagi di-mute dulu, biar suasana grup tetap adem kayak es kopi susu~ Balik ngobrol lagi nanti ya!
                            </div>
                        <?php endif; ?>
                        
                        <div class="chat-form">
                            <form id="chat-form" class="d-flex">
                                <input type="hidden" id="group-id" value="<?php echo e($groupId); ?>">
                                <input type="hidden" id="group-code" value="<?php echo e($groupCode); ?>">
                                <input type="hidden" id="is-muted" value="<?php echo e(isset($isMuted) && $isMuted ? 'true' : 'false'); ?>">
                                <input type="text" class="form-control me-2" id="message-input" placeholder="<?php echo e(isset($isMuted) && $isMuted ? 'Anda sedang dimute dan tidak dapat mengirim pesan' : 'Ketik pesan Anda...'); ?>" autocomplete="off" <?php echo e(isset($isMuted) && $isMuted ? 'disabled' : ''); ?>>
                                <button type="submit" class="btn btn-primary" <?php echo e(isset($isMuted) && $isMuted ? 'disabled' : ''); ?>>
                                    <i class="fas fa-paper-plane"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="https://js.pusher.com/7.0/pusher.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const groupId = document.getElementById('group-id').value;
        const groupCode = document.getElementById('group-code').value;
        const messageInput = document.getElementById('message-input');
        const chatForm = document.getElementById('chat-form');
        const chatMessages = document.getElementById('chat-messages');
        const typingIndicator = document.getElementById('typing-indicator');
        const isMuted = document.getElementById('is-muted').value === 'true';
        
        // Variable to store current CSRF token
        let csrfToken = '<?php echo e(csrf_token()); ?>';
        
        // Refresh CSRF token every 30 minutes to prevent session timeouts
        setInterval(refreshCsrfToken, 30 * 60 * 1000);
        
        // Function to refresh CSRF token
        function refreshCsrfToken() {
            fetch('/csrf-refresh', {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                // Check for HTML response
                const contentType = response.headers.get('content-type');
                if (contentType && contentType.includes('text/html')) {
                    console.error('Received HTML instead of JSON when refreshing CSRF token. Session might have expired.');
                    throw new Error('Session expired');
                }
                return response.json();
            })
            .then(data => {
                if (data.token) {
                    csrfToken = data.token;
                    console.log('CSRF token refreshed');
                }
            })
            .catch(error => {
                console.warn('Failed to refresh CSRF token:', error);
                if (error.message === 'Session expired') {
                    showConnectionError('Sesi Anda telah berakhir. Silakan refresh halaman.');
                }
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
            pusher = new Pusher('<?php echo e(env('PUSHER_APP_KEY')); ?>', {
                cluster: '<?php echo e(env('PUSHER_APP_CLUSTER')); ?>',
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
                if (data.user_id !== <?php echo e(Auth::id()); ?>) {
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
                if (data.user_id === <?php echo e(Auth::id()); ?>) {
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
                fetch('<?php echo e(route('chat.send')); ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        message: message,
                        group_id: groupId,
                        group_code: groupCode
                    })
                })
                .then(response => {
                    if (!response.ok) {
                        // Try to parse the error response safely
                        return safeJsonParse(response).catch(e => {
                            // If JSON parsing fails, return a generic error
                            return { status: 'error', message: 'Server error: ' + response.status };
                        }).then(data => {
                            if (response.status === 403 && data.message && data.message.includes('di-mute')) {
                                showMutedAlert(data.message);
                                return { status: 'error', message: data.message };
                            }
                            throw new Error('Error: ' + (data.message || response.status));
                        });
                    }
                    return safeJsonParse(response);
                })
                .then(data => {
                    if (data.status === 'success') {
                        messageInput.value = '';
                    } else {
                        console.error('Error sending message:', data.message);
                        showConnectionError(data.message || 'Error sending message');
                    }
                })
                .catch(error => {
                    console.error('Error sending message:', error);
                    // Hanya tampilkan error jika bukan session expired yang sudah ditangani
                    if (error.message !== 'Session expired') {
                        showConnectionError('Gagal mengirim pesan. Coba refresh halaman.');
                    }
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
        
        // Send typing indicator
        let typingTimeout;
        messageInput.addEventListener('input', function() {
            clearTimeout(typingTimeout);
            fetch('<?php echo e(route('chat.typing')); ?>', {
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
            
            typingTimeout = setTimeout(() => {}, 3000);
        });
        
        // Load messages
        function loadMessages() {
            fetch(`<?php echo e(route('chat.messages')); ?>?group_id=${groupId}`)
                .then(safeJsonParse)
                .then(data => {
                    if (data.status === 'success') {
                        chatMessages.innerHTML = '';
                        data.messages.forEach(message => appendMessage(message));
                        scrollToBottom();
                    }
                })
                .catch(error => {
                    console.error('Error loading messages:', error);
                    if (error.message !== 'Session expired') {
                        showConnectionError('Gagal memuat pesan. Coba refresh halaman.');
                    }
                });
        }
        
        // Join chat room
        joinChatRoom();
        
        // Load messages
        loadMessages();
        
        // Handle window unload
        window.addEventListener('beforeunload', leaveChatRoom);
        
        // Message template function
        function appendMessage(data) {
            const isCurrentUser = data.user_id === <?php echo e(Auth::id()); ?>;
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
            fetch('<?php echo e(route('chat.join')); ?>', {
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
            fetch('<?php echo e(route('chat.logout')); ?>', {
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
    });
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make(Auth::user()->role === 'admin_grup' ? 'layouts.admin_grup' : 'layouts.user', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\MyUkm-main\resources\views/chat.blade.php ENDPATH**/ ?>