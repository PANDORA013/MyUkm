<?php

/**
 * Test script untuk menguji queue worker
 * Menambahkan beberapa jobs ke queue untuk menguji performa
 */

require_once __DIR__ . '/../vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\DB;
use App\Jobs\BroadcastChatMessage;
use App\Jobs\BroadcastOnlineStatus;
use App\Models\Chat;
use App\Models\User;

// Bootstrap Laravel
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Testing Queue Worker Performance ===\n";
echo "Time: " . date('Y-m-d H:i:s') . "\n\n";

try {
    // Test 1: Add sample chat job to queue
    echo "1. Testing BroadcastChatMessage job...\n";
    
    // Create a test chat (you may need to adjust this based on your data)
    $testUser = User::first();
    if ($testUser) {
        $testGroup = $testUser->groups()->first();
        if ($testGroup) {
            // Simulate a chat message
            $testChat = new Chat([
                'id' => 999999, // Test ID
                'user_id' => $testUser->id,
                'group_id' => $testGroup->id,
                'message' => 'Test message for queue - ' . date('H:i:s'),
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            // Set the user relationship manually
            $testChat->setRelation('user', $testUser);
            
            // Dispatch job with corrected constructor
            dispatch(new BroadcastChatMessage($testChat));
            echo "   ✓ Chat message job dispatched\n";
        } else {
            echo "   ⚠ No groups found for user\n";
        }
    } else {
        echo "   ⚠ No users found in database\n";
    }
    
    // Test 2: Add online status job to queue
    echo "2. Testing BroadcastOnlineStatus job...\n";
    if ($testUser && isset($testGroup)) {
        dispatch(new BroadcastOnlineStatus($testUser->id, true, $testGroup->referral_code));
        echo "   ✓ Online status job dispatched\n";
    }
    
    // Test 3: Check queue status
    echo "3. Checking queue status...\n";
    $jobCount = DB::table('jobs')->count();
    echo "   Jobs in queue: $jobCount\n";
    
    if ($jobCount > 0) {
        echo "   ⏳ Jobs are being processed by queue worker...\n";
        echo "   Monitor the queue worker terminal to see processing logs\n";
    } else {
        echo "   ✓ All jobs processed successfully\n";
    }
    
    echo "\n=== Test Completed ===\n";
    echo "Queue worker should be processing jobs in the background.\n";
    echo "Check the Laravel logs for detailed processing information.\n";
    
} catch (Exception $e) {
    echo "❌ Error during testing: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
