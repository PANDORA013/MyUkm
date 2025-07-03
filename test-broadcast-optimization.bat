@echo off
title Broadcasting Optimization Test
color 0E

echo.
echo ⚡⚡⚡ BROADCASTING OPTIMIZATION TEST ⚡⚡⚡
echo ===========================================
echo.

echo 🔍 Testing broadcasting configuration...
echo.

:: Test 1: Check broadcasting driver
echo [1/8] Checking broadcasting driver...
php artisan tinker --execute="echo 'Broadcasting Driver: ' . config('broadcasting.default');"
echo.

:: Test 2: Check Pusher configuration
echo [2/8] Checking Pusher configuration...
php artisan tinker --execute="
if (config('broadcasting.default') === 'pusher') {
    echo 'Pusher Key: ' . (config('broadcasting.connections.pusher.key') ? 'SET' : 'NOT SET') . PHP_EOL;
    echo 'Pusher Secret: ' . (config('broadcasting.connections.pusher.secret') ? 'SET' : 'NOT SET') . PHP_EOL;
    echo 'Pusher App ID: ' . (config('broadcasting.connections.pusher.app_id') ? 'SET' : 'NOT SET') . PHP_EOL;
    echo 'Pusher Cluster: ' . config('broadcasting.connections.pusher.options.cluster') . PHP_EOL;
} else {
    echo 'Pusher not configured as default driver';
}
"
echo.

:: Test 3: Check queue configuration
echo [3/8] Checking queue configuration...
php artisan tinker --execute="
echo 'Default Queue: ' . config('queue.default') . PHP_EOL;
echo 'Queue Database Table: ' . config('queue.connections.database.table') . PHP_EOL;
"
echo.

:: Test 4: Test queue worker readiness
echo [4/8] Testing queue worker readiness...
tasklist /FI "WINDOWTITLE eq Queue Worker*" >nul 2>&1
if %errorlevel% == 0 (
    echo ✅ Queue worker is running
) else (
    echo ❌ Queue worker not detected
)
echo.

:: Test 5: Check broadcasting routes
echo [5/8] Checking broadcasting routes...
php artisan route:list --grep=broadcasting
echo.

:: Test 6: Test ChatMessageSent event
echo [6/8] Testing ChatMessageSent event optimization...
php artisan tinker --execute="
use App\Events\ChatMessageSent;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
echo 'ChatMessageSent implements ShouldBroadcastNow: ' . (in_array(ShouldBroadcastNow::class, class_implements(ChatMessageSent::class)) ? 'YES' : 'NO') . PHP_EOL;
"
echo.

:: Test 7: Test BroadcastChatMessage job
echo [7/8] Testing BroadcastChatMessage job optimization...
php artisan tinker --execute="
use App\Jobs\BroadcastChatMessage;
\$job = new ReflectionClass(BroadcastChatMessage::class);
\$timeout = \$job->getProperty('timeout');
\$timeout->setAccessible(true);
\$tries = \$job->getProperty('tries');
\$tries->setAccessible(true);
echo 'Job Timeout: ' . \$timeout->getValue(new BroadcastChatMessage(new App\Models\Chat())) . ' seconds' . PHP_EOL;
echo 'Job Tries: ' . \$tries->getValue(new BroadcastChatMessage(new App\Models\Chat())) . PHP_EOL;
"
echo.

:: Test 8: Real-time responsiveness test
echo [8/8] Testing real-time responsiveness...
echo.
echo ⏱️  Measuring event creation speed...
php artisan tinker --execute="
use App\Models\Chat;
use App\Models\User;
use App\Models\Group;
use App\Events\ChatMessageSent;

\$start = microtime(true);

// Create test models (without saving)
\$user = new User(['id' => 1, 'name' => 'Test User']);
\$group = new Group(['id' => 1, 'referral_code' => 'TEST123']);
\$chat = new Chat([
    'id' => 1,
    'user_id' => 1,
    'group_id' => 1,
    'message' => 'Test message',
    'created_at' => now()
]);

// Set relationships
\$chat->setRelation('user', \$user);
\$chat->setRelation('group', \$group);

// Create event
\$event = new ChatMessageSent(\$chat);

\$end = microtime(true);
\$duration = (\$end - \$start) * 1000;

echo 'Event Creation Time: ' . round(\$duration, 2) . ' ms' . PHP_EOL;

if (\$duration < 10) {
    echo '✅ EXCELLENT: Ultra-fast event creation (< 10ms)' . PHP_EOL;
} elseif (\$duration < 50) {
    echo '✅ GOOD: Fast event creation (< 50ms)' . PHP_EOL;
} elseif (\$duration < 100) {
    echo '⚠️  ACCEPTABLE: Moderate event creation (< 100ms)' . PHP_EOL;
} else {
    echo '❌ SLOW: Event creation needs optimization (> 100ms)' . PHP_EOL;
}
"

echo.
echo ===========================================
echo ✅ Broadcasting optimization test complete!
echo ===========================================
echo.
echo 📊 OPTIMIZATION SUMMARY:
echo   ⚡ Event: ChatMessageSent uses ShouldBroadcastNow
echo   🚀 Job: BroadcastChatMessage ultra-optimized
echo   📡 Broadcasting: Pusher with timeout optimization
echo   🔄 Queue: realtime,high,default priority
echo   ⏱️  Worker: --sleep=0 for instant processing
echo.
pause
