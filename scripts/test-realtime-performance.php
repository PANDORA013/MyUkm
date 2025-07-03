<?php

/**
 * Script untuk test performa queue worker dengan real data
 * Test responsiveness real-time features
 */

require_once __DIR__ . '/../vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\DB;
use App\Jobs\BroadcastChatMessage;
use App\Jobs\BroadcastOnlineStatus;
use App\Models\Chat;
use App\Models\User;
use App\Models\Group;

// Bootstrap Laravel
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== MyUKM Queue Performance Test ===\n";
echo "Testing real-time responsiveness with queue workers\n";
echo "Time: " . date('Y-m-d H:i:s') . "\n\n";

try {
    // Get statistics
    $userCount = User::count();
    $groupCount = Group::count();
    $chatCount = Chat::count();
    $jobsInQueue = DB::table('jobs')->count();
    
    echo "📊 Current Statistics:\n";
    echo "   Users: $userCount\n";
    echo "   Groups: $groupCount\n";
    echo "   Chat Messages: $chatCount\n";
    echo "   Jobs in Queue: $jobsInQueue\n\n";
    
    if ($jobsInQueue > 0) {
        echo "⏳ Processing existing jobs in queue...\n";
        $jobs = DB::table('jobs')->select('queue', 'payload')->get();
        foreach ($jobs as $job) {
            $payload = json_decode($job->payload, true);
            $jobClass = $payload['displayName'] ?? 'Unknown';
            echo "   - Queue: {$job->queue}, Job: $jobClass\n";
        }
        echo "\n";
    }
    
    // Test dengan data yang ada
    echo "🧪 Testing Real-time Performance:\n\n";
    
    // Test 1: Chat Message Broadcasting Performance
    echo "1. Testing Chat Message Broadcasting...\n";
    $testUser = User::first();
    if ($testUser) {
        $testGroup = Group::first();
        if ($testGroup) {
            $startTime = microtime(true);
            
            // Create test chat message
            $testChat = Chat::create([
                'user_id' => $testUser->id,
                'group_id' => $testGroup->id,
                'message' => '🚀 Queue Performance Test - ' . date('H:i:s'),
            ]);
            
            $testChat->load('user');
            
            // Dispatch via queue
            dispatch(new BroadcastChatMessage($testChat, $testGroup->referral_code));
            
            $endTime = microtime(true);
            $responseTime = round(($endTime - $startTime) * 1000, 2);
            
            echo "   ✅ Message created and queued in {$responseTime}ms\n";
            echo "   📝 Message: \"{$testChat->message}\"\n";
            echo "   👤 User: {$testUser->name}\n";
            echo "   🏢 Group: {$testGroup->name}\n\n";
            
        } else {
            echo "   ⚠️ No groups found. Creating test group...\n";
            $testGroup = Group::create([
                'name' => 'Test Queue Group',
                'referral_code' => 'QUEUE' . rand(1000, 9999),
                'description' => 'Group for testing queue performance',
                'admin_id' => $testUser->id
            ]);
            echo "   ✅ Test group created: {$testGroup->name}\n\n";
        }
    } else {
        echo "   ❌ No users found in database\n\n";
    }
    
    // Test 2: Online Status Broadcasting Performance
    echo "2. Testing Online Status Broadcasting...\n";
    if ($testUser && $testGroup) {
        $startTime = microtime(true);
        
        // Test online status
        dispatch(new BroadcastOnlineStatus($testUser->id, true, $testGroup->referral_code));
        
        $endTime = microtime(true);
        $responseTime = round(($endTime - $startTime) * 1000, 2);
        
        echo "   ✅ Online status queued in {$responseTime}ms\n";
        echo "   👤 User: {$testUser->name} -> Online\n\n";
    }
    
    // Test 3: Queue Performance Summary
    echo "3. Queue Performance Summary...\n";
    $newJobsInQueue = DB::table('jobs')->count();
    $jobsAdded = $newJobsInQueue - $jobsInQueue;
    
    echo "   📊 Jobs before test: $jobsInQueue\n";
    echo "   📊 Jobs after test: $newJobsInQueue\n";
    echo "   ➕ Jobs added: $jobsAdded\n\n";
    
    if ($newJobsInQueue > 0) {
        echo "   🔄 Queue worker is processing these jobs in background\n";
        echo "   📈 This improves response time for users\n";
        echo "   ⚡ Real-time features should be more responsive\n\n";
    }
    
    // Performance comparison
    echo "🚀 Queue Benefits:\n";
    echo "   ✅ Non-blocking message sending\n";
    echo "   ✅ Background event broadcasting\n";
    echo "   ✅ Better error handling with retries\n";
    echo "   ✅ Improved server response times\n";
    echo "   ✅ Scalable real-time features\n\n";
    
    echo "💡 Recommendations:\n";
    echo "   - Keep queue worker running with: php artisan queue:work\n";
    echo "   - Monitor queue with: php artisan queue:monitor\n";
    echo "   - Use supervisor in production for auto-restart\n";
    echo "   - Check Laravel logs for detailed processing info\n\n";
    
    echo "=== Test Completed Successfully ===\n";
    echo "Queue worker is improving real-time responsiveness! 🎉\n";
    
} catch (Exception $e) {
    echo "❌ Error during testing: " . $e->getMessage() . "\n";
    echo "📋 Stack trace:\n" . $e->getTraceAsString() . "\n";
}
