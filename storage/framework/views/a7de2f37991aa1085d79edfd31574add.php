<?php $__env->startSection('title', 'Chat UKM - ' . $groupName); ?>

<?php $__env->startPush('styles'); ?>
<!--[if IE]>
<link rel="stylesheet" href="<?php echo e(asset('css/ie-compatibility.css')); ?>">
<![endif]-->
<style>
    .chat-container {
        height: calc(100vh - 160px);
        display: -ms-flexbox;
        display: flex;
        -ms-flex-direction: column;
        flex-direction: column;
        border-radius: 0.5rem;
        overflow: hidden;
    }
    .chat-messages {
        -ms-flex: 1;
        flex: 1;
        overflow-y: auto;
        padding: 12px;
        background-color: #f8f9fa;
        scroll-behavior: smooth;
    }
    .message-container {
        overflow: hidden;
        margin-bottom: 10px;
        clear: both;
        transition: all 0.3s ease;
    }
    .message {
        max-width: 80%;
    }
    .message-content {
        padding: 8px 12px;
        border-radius: 1rem;
        position: relative;
        display: inline-block;
        transition: background-color 0.2s ease;
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
    .message-incoming .message-content:hover {
        background-color: rgba(0, 0, 0, 0.05);
    }
    .message-outgoing .message-content:hover {
        background-color: rgba(255, 255, 255, 0.1);
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
        display: -ms-flexbox;
        display: flex;
    }
    .chat-form .form-control {
        -ms-flex: 1;
        flex: 1;
        margin-right: 8px;
    }
    .chat-header {
        background-color: #fff;
        padding: 12px 15px;
        border-bottom: 1px solid #e3e6f0;
    }
    .d-flex {
        display: -ms-flexbox !important;
        display: flex !important;
    }
    .justify-content-between {
        -ms-flex-pack: justify !important;
        justify-content: space-between !important;
    }
    .align-items-center {
        -ms-flex-align: center !important;
        align-items: center !important;
    }
    .chat-online-indicator {
        width: 8px;
        height: 8px;
        background-color: #10b981;
        border-radius: 50%;
        display: inline-block;
        margin-right: 4px;
        animation: pulse 2s infinite;
    }
    @keyframes pulse {
        0% { opacity: 1; }
        50% { opacity: 0.5; }
        100% { opacity: 1; }
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
        transition: all 0.3s ease;
    }
    .members-badge.updating {
        background-color: #f59e0b;
        animation: bounce 0.5s ease-in-out;
    }
    @keyframes bounce {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.05); }
    }
    .online-status-updating {
        opacity: 0.7;
        transition: opacity 0.3s ease;
    }
    .connection-status {
        position: fixed;
        top: 10px;
        right: 10px;
        z-index: 9999;
        padding: 0.5rem 1rem;
        border-radius: 0.25rem;
        font-size: 0.8rem;
        min-width: 120px;
        text-align: center;
    }
    .connection-online {
        background-color: #10b981;
        color: white;
    }
    .connection-updating {
        background-color: #f59e0b;
        color: white;
    }
    .connection-offline {
        background-color: #ef4444;
        color: white;
    }
    .typing-indicator {
        font-size: 0.8rem;
        color: #6c757d;
        font-style: italic;
        padding: 0 12px;
        margin: 4px 0;
        min-height: 20px;
        transition: opacity 0.3s ease;
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
    
    /* TAMBAHAN: Optimasi CSS untuk responsivitas dan smooth animations */
    .message-container {
        transition: all 0.3s ease;
    }
    
    .message-content {
        transition: background-color 0.2s ease;
    }
    
    .message-incoming .message-content:hover {
        background-color: rgba(0, 0, 0, 0.05);
    }
    
    .message-outgoing .message-content:hover {
        background-color: rgba(255, 255, 255, 0.1);
    }
    
    /* Typing indicator animations */
    .typing-indicator {
        transition: opacity 0.3s ease;
        font-style: italic;
        color: #6b7280;
        min-height: 18px;
    }
    
    /* Optimasi scrolling */
    .chat-messages {
        scroll-behavior: smooth;
    }
    
    /* Loading state untuk pesan yang sedang dikirim */
    .message-sending {
        opacity: 0.6;
        position: relative;
    }
    
    .message-sending::after {
        content: '';
        position: absolute;
        right: 5px;
        top: 50%;
        transform: translateY(-50%);
        width: 12px;
        height: 12px;
        border: 2px solid #4e73df;
        border-top: 2px solid transparent;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }
    
    @keyframes spin {
        0% { transform: translateY(-50%) rotate(0deg); }
        100% { transform: translateY(-50%) rotate(360deg); }
    }
    
    /* Notifikasi pesan baru */
    .new-message-notification {
        position: fixed;
        top: 70px;
        right: 20px;
        background: #10b981;
        color: white;
        padding: 8px 16px;
        border-radius: 20px;
        font-size: 14px;
        z-index: 1000;
        transform: translateX(100%);
        transition: transform 0.3s ease;
    }
    
    .new-message-notification.show {
        transform: translateX(0);
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
                            <div class="text-muted small mt-1" id="onlineMembersList" style="font-size: 0.7rem;">
                                <i class="fas fa-circle text-success" style="font-size: 0.5rem;"></i> <span id="onlineMembersText">Memuat...</span>
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
                                <button type="submit" class="btn btn-primary" <?php echo e(isset($isMuted) && $isMuted ? 'disabled' : ''); ?> aria-label="Kirim pesan" title="Kirim pesan">
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
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="https://js.pusher.com/7.0/pusher.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.15.0/dist/echo.iife.js"></script>
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
        let csrfToken = '<?php echo e(csrf_token()); ?>';
        
        // Variabel untuk menyimpan anggota online
        let onlineMembers = [];
        let totalMembers = 0;
        let lastOnlineUpdate = 0;
        let isVisible = true;
        let onlineStatusInterval;
        let onlineMembersInterval;
        let chatRefreshInterval; // TAMBAHAN: untuk auto-refresh chat
        let lastMessageId = 0; // TAMBAHAN: untuk tracking pesan terbaru
        
        // Refresh CSRF token and keep session alive every 10 minutes
        setInterval(refreshCsrfToken, 10 * 60 * 1000);
        
        // Dynamic polling - OPTIMIZED for maximum responsiveness
        function startResponsivePolling() {
            // Update online status lebih sering (5 detik saat aktif untuk responsif maksimal)
            onlineStatusInterval = setInterval(() => {
                if (isVisible) {
                    updateOnlineStatus();
                }
            }, 5 * 1000); // Dipercepat dari 15 detik ke 5 detik
            
            // Load anggota online dengan interval yang lebih cepat
            onlineMembersInterval = setInterval(() => {
                if (isVisible) {
                    loadOnlineMembers();
                }
            }, 8 * 1000); // Dipercepat dari 20 detik ke 8 detik
            
            // TAMBAHAN: Auto-refresh chat messages untuk memastikan sinkronisasi
            chatRefreshInterval = setInterval(() => {
                if (isVisible && !document.hidden) {
                    // Hanya refresh jika tidak ada aktivitas typing dalam 3 detik terakhir
                    const now = Date.now();
                    if (!window.lastTypingTime || (now - window.lastTypingTime) > 3000) {
                        loadLatestMessages();
                    }
                }
            }, 3 * 1000); // Refresh chat setiap 3 detik untuk responsif maksimal
        }
        
        // Page visibility handling untuk optimasi battery dan performance
        document.addEventListener('visibilitychange', function() {
            isVisible = !document.hidden;
            if (isVisible) {
                // Langsung update saat kembali aktif
                updateOnlineStatus();
                loadOnlineMembers();
                console.log('Page active - resuming frequent updates');
            } else {
                console.log('Page hidden - reducing update frequency');
            }
        });
        
        // User activity detection untuk update yang lebih responsif
        let userActivityTimeout;
        function onUserActivity() {
            clearTimeout(userActivityTimeout);
            userActivityTimeout = setTimeout(() => {
                // Update status saat ada aktivitas user
                if (Date.now() - lastOnlineUpdate > 10000) { // Min 10 detik gap
                    updateOnlineStatus();
                    lastOnlineUpdate = Date.now();
                }
            }, 1000);
        }
        
        // Monitor user activity
        ['mousedown', 'mousemove', 'keypress', 'scroll', 'touchstart', 'click'].forEach(event => {
            document.addEventListener(event, onUserActivity, { passive: true });
        });

        // Also refresh session on user interaction (typing, clicking)
        document.addEventListener('click', function() {
            // Throttle to avoid too many requests - only refresh if last refresh was more than 5 minutes ago
            if (!window.lastRefreshTime || (Date.now() - window.lastRefreshTime) > 5 * 60 * 1000) {
                refreshCsrfToken();
            }
        });

        // Debounce for typing and send typing indicator - OPTIMIZED
        let typingRefreshTimeout;
        let typingIndicatorTimeout;
        messageInput.addEventListener('input', function() {
            // TAMBAHAN: Track typing time untuk optimasi refresh
            window.lastTypingTime = Date.now();
            
            // Handle CSRF token refresh
            clearTimeout(typingRefreshTimeout);
            typingRefreshTimeout = setTimeout(() => {
                if (!window.lastRefreshTime || (Date.now() - window.lastRefreshTime) > 5 * 60 * 1000) {
                    refreshCsrfToken();
                }
            }, 5000);
            
            // Handle typing indicator - OPTIMIZED: lebih responsif
            clearTimeout(typingIndicatorTimeout);
            
            // Kirim typing indicator lebih cepat
            if (this.value.length > 0) {
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
            }
            
            typingIndicatorTimeout = setTimeout(() => {}, 2000); // Dipercepat dari 3 detik ke 2 detik
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
                            window.location.href = '<?php echo e(route('login')); ?>?redirect=<?php echo e(urlencode(request()->fullUrl())); ?>';
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
        
        // Configure Laravel Echo properly
        let pusher;
        let channel;
        
        try {
            // Initialize Pusher first - OPTIMIZED dengan connection settings yang lebih responsif
            pusher = new Pusher('<?php echo e(env('PUSHER_APP_KEY')); ?>', {
                cluster: '<?php echo e(env('PUSHER_APP_CLUSTER')); ?>',
                forceTLS: true,
                encrypted: true,
                // TAMBAHAN: Optimasi connection untuk responsivitas maksimal
                activityTimeout: 3000,           // Deteksi disconnection lebih cepat
                pongTimeout: 2000,               // Ping response timeout lebih cepat  
                unavailableTimeout: 1000,        // Mark sebagai unavailable lebih cepat
                enabledTransports: ['ws', 'wss'], // Prioritas WebSocket untuk speed
                disabledTransports: ['xhr_polling', 'xhr_streaming'] // Disable slow transports
            });
            
            // Initialize Laravel Echo with Pusher instance - OPTIMIZED
            window.Echo = new Echo({
                broadcaster: 'pusher',
                key: '<?php echo e(env('PUSHER_APP_KEY')); ?>',
                cluster: '<?php echo e(env('PUSHER_APP_CLUSTER')); ?>',
                forceTLS: true,
                encrypted: true,
                authEndpoint: '/broadcasting/auth',
                auth: {
                    headers: {
                        'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
                        'Accept': 'application/json'
                    }
                },
                pusher: pusher
            });
            
            console.log('✅ Laravel Echo initialized successfully');
            
            // Subscribe to private channel for this group
            channel = window.Echo.private('group.' + groupCode);
            console.log('✅ Subscribed to private channel:', 'group.' + groupCode);
            
            // Handle connection state changes
            pusher.connection.bind('state_change', function(states) {
                console.log('Pusher connection state changed:', states.current);
                if (states.current === 'connected') {
                    console.log('✅ Pusher connected successfully');
                    // Hide any existing warnings
                    const warnings = document.querySelectorAll('.pusher-warning');
                    warnings.forEach(w => w.remove());
                } else if (states.current === 'disconnected' || states.current === 'failed') {
                    console.warn('❌ Pusher connection failed:', states.current);
                    showConnectionWarning();
                }
            });
            
            // Handle connection errors
            pusher.connection.bind('error', function(err) {
                console.error('Pusher connection error:', err);
                showConnectionWarning();
            });
            
        } catch (error) {
            console.error('Echo initialization error:', error);
            showConnectionWarning();
            
            // Fallback: Use basic Pusher without Echo
            try {
                pusher = new Pusher('<?php echo e(env('PUSHER_APP_KEY')); ?>', {
                    cluster: '<?php echo e(env('PUSHER_APP_CLUSTER')); ?>',
                    forceTLS: true,
                    encrypted: true,
                    authEndpoint: '/broadcasting/auth',
                    auth: {
                        headers: {
                            'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
                            'Accept': 'application/json'
                        }
                    }
                });
                
                channel = pusher.subscribe('private-group.' + groupCode);
                console.log('✅ Fallback: Using basic Pusher, subscribed to:', 'private-group.' + groupCode);
                
            } catch (fallbackError) {
                console.error('Fallback Pusher error:', fallbackError);
            }
        }
        
        // Function to show connection warning
        function showConnectionWarning() {
            // Remove existing warnings first
            const existingWarnings = document.querySelectorAll('.pusher-warning');
            existingWarnings.forEach(w => w.remove());
            
            const warningEl = document.createElement('div');
            warningEl.className = 'alert alert-warning small m-1 pusher-warning';
            warningEl.innerHTML = '<i class="fas fa-exclamation-triangle me-1"></i> Notifikasi real-time tidak tersedia. Refresh halaman untuk melihat pesan terbaru.';
            
            // Insert before the chat form
            const chatForm = document.querySelector('.chat-form');
            if (chatForm) {
                chatForm.parentNode.insertBefore(warningEl, chatForm);
            }
            
            // Auto-hide after 15 seconds
            setTimeout(() => {
                if (warningEl.parentNode) {
                    warningEl.style.opacity = '0';
                    warningEl.style.transition = 'opacity 1s';
                    setTimeout(() => {
                        if (warningEl.parentNode) {
                            warningEl.remove();
                        }
                    }, 1000);
                }
            }, 15000);
        }
        
        // Only set up event handlers if channel exists (real-time available) - OPTIMIZED
        if (channel) {
            console.log('✅ Setting up event handlers for group:', groupCode);
            
            // Handle incoming messages - OPTIMIZED: Instant display dengan animation
            channel.listen('ChatMessageSent', function(data) {
                console.log('📨 Received chat message:', data);
                
                // TAMBAHAN: Instant response - langsung tampilkan tanpa delay
                appendMessage(data);
                
                // TAMBAHAN: Smooth scroll dengan animation
                setTimeout(() => {
                    scrollToBottom();
                }, 100);
                
                // TAMBAHAN: Visual feedback untuk pesan baru
                if (!document.hasFocus()) {
                    // Update document title untuk notifikasi
                    const originalTitle = document.title;
                    document.title = '💬 Pesan Baru - ' + originalTitle;
                    
                    // Reset title saat window focus kembali
                    const resetTitle = () => {
                        document.title = originalTitle;
                        window.removeEventListener('focus', resetTitle);
                    };
                    window.addEventListener('focus', resetTitle);
                }
            });
            
            // Handle typing indicators - OPTIMIZED: Response lebih cepat
            channel.listen('typing', function(data) {
                if (data.user_id !== <?php echo e(Auth::id()); ?>) {
                    typingIndicator.textContent = data.name + ' sedang mengetik...';
                    typingIndicator.style.opacity = '1';
                    
                    // Clear indicator lebih cepat untuk responsivitas
                    setTimeout(() => {
                        typingIndicator.style.opacity = '0';
                        setTimeout(() => {
                            typingIndicator.textContent = '';
                        }, 200);
                    }, 2000); // Dipercepat dari 3 detik ke 2 detik
                }
            });
            
            // Handle online status updates
            channel.listen('user-online', function(data) {
                document.getElementById('onlineCount').textContent = data.online_count;
                document.getElementById('totalMembers').textContent = data.total_members;
            });
            
            // Handle user online status changed
            channel.listen('user-online-status-changed', function(data) {
                console.log('User online status changed:', data);
                updateOnlineMembersDisplay(data.online_members, data.total_members);
            });
            
            // Handle user mute status changes
            channel.listen('user-mute-status', function(data) {
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
                // Disable input while sending
                messageInput.disabled = true;
                sendButton.disabled = true;
                
                retryWithTokenRefresh(async () => {
                    // Use the new asynchronous endpoint for better performance
                    const response = await fetch(`/ukm/${groupCode}/messages`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({
                            message: message
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
                    const response = await fetch(`<?php echo e(route('chat.messages')); ?>?group_id=${groupId}`, {
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
        
        // TAMBAHAN: Function untuk load pesan terbaru tanpa refresh seluruh chat
        async function loadLatestMessages() {
            try {
                await retryWithTokenRefresh(async () => {
                    const response = await fetch(`<?php echo e(route('chat.messages')); ?>?group_id=${groupId}&after=${lastMessageId}&limit=10`, {
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    
                    const data = await safeJsonParse(response);
                    
                    if (data.status === 'success' && data.messages && data.messages.length > 0) {
                        // Hanya append pesan baru yang belum ada
                        data.messages.forEach(message => {
                            if (message.id > lastMessageId) {
                                appendMessage(message);
                                lastMessageId = message.id;
                            }
                        });
                        
                        // Auto scroll jika user berada di bagian bawah chat
                        const isAtBottom = chatMessages.scrollTop + chatMessages.clientHeight >= chatMessages.scrollHeight - 50;
                        if (isAtBottom) {
                            scrollToBottom();
                        }
                    }
                });
            } catch (error) {
                console.error('Error loading latest messages:', error);
                // Tidak perlu show error untuk background refresh
            }
        }

        // Join chat room
        joinChatRoom();
        
        // Load messages
        loadMessages();
        
        // Load anggota online dan update status
        loadOnlineMembers();
        updateOnlineStatus();
        
        // Start responsive polling system
        startResponsivePolling();
        
        // Immediate heartbeat on window focus
        window.addEventListener('focus', function() {
            updateOnlineStatus();
            loadOnlineMembers();
        });
        
        // Handle window unload
        window.addEventListener('beforeunload', leaveChatRoom);
        
        // Message template function - OPTIMIZED untuk tracking message ID
        function appendMessage(data) {
            // TAMBAHAN: Update lastMessageId untuk tracking pesan terbaru
            if (data.id && data.id > lastMessageId) {
                lastMessageId = data.id;
            }
            
            // Cek apakah pesan sudah ada untuk menghindari duplikasi
            const existingMessage = document.querySelector(`[data-message-id="${data.id}"]`);
            if (existingMessage) {
                return; // Skip jika pesan sudah ada
            }
            
            const isCurrentUser = data.user_id === <?php echo e(Auth::id()); ?>;
            const messageContainer = document.createElement('div');
            messageContainer.className = 'message-container';
            messageContainer.setAttribute('data-message-id', data.id); // TAMBAHAN: ID tracking
            
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
            
            // TAMBAHAN: Animation untuk pesan baru
            messageContainer.style.opacity = '0';
            messageContainer.style.transform = 'translateY(10px)';
            
            setTimeout(() => {
                messageContainer.style.transition = 'all 0.3s ease';
                messageContainer.style.opacity = '1';
                messageContainer.style.transform = 'translateY(0)';
            }, 10);
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
        
        // Fungsi untuk load anggota online dengan caching
        async function loadOnlineMembers() {
            updateConnectionStatus('updating');
            
            try {
                await retryWithTokenRefresh(async () => {
                    const response = await fetch(`<?php echo e(route('chat.online-members')); ?>?group_id=${groupId}`, {
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    
                    if (!response.ok) {
                        throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                    }
                    
                    const data = await safeJsonParse(response);
                    
                    if (data.status === 'success') {
                        // Update hanya jika ada perubahan
                        const newOnlineMembers = data.online_members || [];
                        const newTotalMembers = data.total_members || 0;
                        
                        if (JSON.stringify(onlineMembers) !== JSON.stringify(newOnlineMembers) || 
                            totalMembers !== newTotalMembers) {
                            
                            onlineMembers = newOnlineMembers;
                            totalMembers = newTotalMembers;
                            
                            updateOnlineMembersDisplay(onlineMembers, totalMembers);
                            console.log('Online members updated:', onlineMembers.length, 'of', totalMembers);
                        } else {
                            updateConnectionStatus('online');
                        }
                    } else {
                        throw new Error(data.message || 'Failed to load online members');
                    }
                    
                    return data;
                });
            } catch (error) {
                console.error('Error loading online members:', error);
                updateConnectionStatus('offline');
                
                if (error.message !== 'Session expired') {
                    // Fallback: tetap tampilkan data terakhir yang valid
                    if (onlineMembers.length > 0) {
                        console.warn('Using cached online members data');
                        updateOnlineMembersDisplay(onlineMembers, totalMembers);
                    }
                }
            }
        }
        
        // Fungsi untuk update status online user dengan retry logic
        async function updateOnlineStatus() {
            updateConnectionStatus('updating');
            
            try {
                await retryWithTokenRefresh(async () => {
                    const response = await fetch('<?php echo e(route('chat.update-online-status')); ?>', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({
                            group_id: groupId
                        })
                    });
                    
                    if (!response.ok) {
                        throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                    }
                    
                    const data = await safeJsonParse(response);
                    
                    if (data.status === 'success') {
                        lastOnlineUpdate = Date.now();
                        updateConnectionStatus('online');
                        console.log('Online status updated successfully', {
                            broadcast_sent: data.broadcast_sent,
                            online_count: data.online_count
                        });
                    } else {
                        throw new Error(data.message || 'Failed to update online status');
                    }
                    
                    return data;
                });
            } catch (error) {
                console.error('Error updating online status:', error);
                updateConnectionStatus('offline');
                
                // Tidak menampilkan error ke user kecuali critical
                if (error.message.includes('Session expired')) {
                    showConnectionError('Sesi berakhir. Silakan refresh halaman.');
                }
            }
        }
        
        // Fungsi untuk update tampilan anggota online dengan visual feedback
        function updateOnlineMembersDisplay(onlineList, totalCount) {
            const onlineCountEl = document.getElementById('onlineCount');
            const totalMembersEl = document.getElementById('totalMembers');
            const onlineMembersTextEl = document.getElementById('onlineMembersText');
            const membersBadge = document.querySelector('.members-badge');
            
            // Tambahkan visual feedback saat updating
            if (membersBadge) {
                membersBadge.classList.add('updating');
                setTimeout(() => {
                    membersBadge.classList.remove('updating');
                }, 500);
            }
            
            if (onlineCountEl) {
                // Smooth number update
                const currentCount = parseInt(onlineCountEl.textContent) || 0;
                const newCount = onlineList.length;
                
                if (currentCount !== newCount) {
                    onlineCountEl.style.transform = 'scale(1.2)';
                    setTimeout(() => {
                        onlineCountEl.textContent = newCount;
                        onlineCountEl.style.transform = 'scale(1)';
                    }, 150);
                }
            }
            
            if (totalMembersEl) {
                totalMembersEl.textContent = totalCount;
            }
            
            // Update daftar nama anggota online
            if (onlineMembersTextEl) {
                if (onlineList.length === 0) {
                    onlineMembersTextEl.textContent = 'Tidak ada anggota online';
                    onlineMembersTextEl.previousElementSibling.className = 'fas fa-circle text-muted';
                } else {
                    const names = onlineList.map(m => m.name).join(', ');
                    if (names.length > 50) {
                        onlineMembersTextEl.textContent = `${onlineList.length} anggota online: ${names.substring(0, 47)}...`;
                    } else {
                        onlineMembersTextEl.textContent = `Online: ${names}`;
                    }
                    onlineMembersTextEl.previousElementSibling.className = 'fas fa-circle text-success';
                }
                
                // Highlight animation for new members
                onlineMembersTextEl.style.opacity = '0.7';
                setTimeout(() => {
                    onlineMembersTextEl.style.opacity = '1';
                }, 200);
            }
            
            // Update indikator online dengan animasi
            const onlineIndicator = document.querySelector('.chat-online-indicator');
            if (onlineIndicator) {
                if (onlineList.length > 0) {
                    onlineIndicator.style.backgroundColor = '#10b981'; // hijau untuk online
                    onlineIndicator.style.animation = 'pulse 2s infinite';
                } else {
                    onlineIndicator.style.backgroundColor = '#9ca3af'; // abu-abu untuk offline
                    onlineIndicator.style.animation = 'none';
                }
            }
            
            // Update connection status indicator
            updateConnectionStatus('online');
            
            console.log(`Anggota online di grup: ${onlineList.length}/${totalCount}`);
            console.log('Daftar anggota online:', onlineList.map(m => m.name).join(', '));
        }
        
        // Function untuk show connection status
        function updateConnectionStatus(status) {
            let statusEl = document.getElementById('connectionStatus');
            if (!statusEl) {
                statusEl = document.createElement('div');
                statusEl.id = 'connectionStatus';
                statusEl.className = 'connection-status';
                document.body.appendChild(statusEl);
            }
            
            statusEl.className = 'connection-status';
            
            switch(status) {
                case 'online':
                    statusEl.classList.add('connection-online');
                    statusEl.innerHTML = '<i class="fas fa-wifi me-1"></i>Online';
                    // Auto hide after 2 seconds
                    setTimeout(() => {
                        statusEl.style.opacity = '0';
                    }, 2000);
                    break;
                case 'updating':
                    statusEl.classList.add('connection-updating');
                    statusEl.innerHTML = '<i class="fas fa-sync fa-spin me-1"></i>Updating...';
                    statusEl.style.opacity = '1';
                    break;
                case 'offline':
                    statusEl.classList.add('connection-offline');
                    statusEl.innerHTML = '<i class="fas fa-exclamation-triangle me-1"></i>Offline';
                    statusEl.style.opacity = '1';
                    break;
            }
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
<?php $__env->stopPush(); ?>

<?php echo $__env->make(Auth::user()->role === 'admin_grup' ? 'layouts.admin_grup' : 'layouts.user', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\MyUkm-main\resources\views\chat.blade.php ENDPATH**/ ?>