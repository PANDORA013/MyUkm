<?php

/**
 * Queue Performance Test
 * Script untuk test performa dan responsivitas queue worker
 */

require_once __DIR__ . '/../vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use App\Jobs\BroadcastChatMessage;
use App\Jobs\BroadcastOnlineStatus;
use App\Models\Chat;
use App\Models\User;
use App\Models\Group;

// Bootstrap Laravel
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "╔══════════════════════════════════════════════════════════════════╗\n";
echo "║                   MyUKM Queue Performance Test                   ║\n";
echo "║                      " . date('Y-m-d H:i:s') . "                        ║\n";
echo "╚══════════════════════════════════════════════════════════════════╝\n\n";

function createTestJobs($count = 5) {
    echo "🚀 Creating $count test jobs...\n\n";
    
    try {
        // Get first user and group for testing
        $user = User::first();
        $group = Group::first();
        
        if (!$user) {
            echo "❌ No users found in database. Please create a user first.\n";
            return false;
        }
        
        if (!$group) {
            echo "❌ No groups found in database. Please create a group first.\n";
            return false;
        }
        
        $startTime = microtime(true);
        
        for ($i = 1; $i <= $count; $i++) {
            // Create test chat message
            $testChat = new Chat([
                'id' => 999990 + $i,
                'user_id' => $user->id,
                'group_id' => $group->id,
                'message' => "Test queue message #$i - " . date('H:i:s'),
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            $testChat->setRelation('user', $user);
            
            // Dispatch chat message job
            dispatch(new BroadcastChatMessage($testChat, $group->referral_code ?? 'test-group'));
            echo "  ✓ Chat message job #$i dispatched\n";
            
            // Dispatch online status job
            dispatch(new BroadcastOnlineStatus($user->id, true, $group->referral_code ?? 'test-group'));
            echo "  ✓ Online status job #$i dispatched\n";
            
            // Small delay to simulate real usage
            usleep(100000); // 0.1 second
        }
        
        $endTime = microtime(true);
        $dispatchTime = round(($endTime - $startTime) * 1000, 2);
        
        echo "\n📊 Dispatch Performance:\n";
        echo "  • Jobs created: " . ($count * 2) . "\n";
        echo "  • Dispatch time: {$dispatchTime}ms\n";
        echo "  • Average per job: " . round($dispatchTime / ($count * 2), 2) . "ms\n\n";
        
        return true;
        
    } catch (Exception $e) {
        echo "❌ Error creating test jobs: " . $e->getMessage() . "\n";
        return false;
    }
}

function monitorQueueProcessing($duration = 30) {
    echo "⏱️  Monitoring queue processing for {$duration} seconds...\n\n";
    
    $startCount = DB::table('jobs')->count();
    $startTime = time();
    
    echo "Starting queue size: $startCount jobs\n";
    echo "Monitoring";
    
    while ((time() - $startTime) < $duration) {
        echo ".";
        sleep(2);
        
        $currentCount = DB::table('jobs')->count();
        
        if ($currentCount == 0 && $startCount > 0) {
            $processingTime = time() - $startTime;
            echo "\n\n✅ All jobs processed in {$processingTime} seconds!\n";
            break;
        }
    }
    
    $finalCount = DB::table('jobs')->count();
    $processedJobs = $startCount - $finalCount;
    $actualTime = time() - $startTime;
    
    echo "\n\n📈 Processing Results:\n";
    echo "  • Initial jobs: $startCount\n";
    echo "  • Remaining jobs: $finalCount\n";
    echo "  • Processed jobs: $processedJobs\n";
    echo "  • Processing time: {$actualTime}s\n";
    
    if ($processedJobs > 0 && $actualTime > 0) {
        $jobsPerSecond = round($processedJobs / $actualTime, 2);
        echo "  • Average speed: {$jobsPerSecond} jobs/second\n";
    }
    
    echo "\n";
}

function checkQueueWorkerStatus() {
    echo "🔍 Checking queue worker status...\n";
    
    // Check if there are jobs waiting
    $pendingJobs = DB::table('jobs')->count();
    $failedJobs = DB::table('failed_jobs')->count();
    
    echo "  • Pending jobs: $pendingJobs\n";
    echo "  • Failed jobs: $failedJobs\n";
    
    if ($pendingJobs > 0) {
        echo "  ⚠️  Queue worker might not be running or is slow\n";
        echo "  💡 Start queue worker: php artisan queue:work database --verbose\n";
    } else {
        echo "  ✅ Queue is clean - worker is processing efficiently\n";
    }
    
    echo "\n";
}

function displayRecommendations() {
    echo "💡 PERFORMANCE RECOMMENDATIONS:\n";
    echo "─────────────────────────────────────────────────────────────────\n";
    echo "1. Use multiple queue workers for high load:\n";
    echo "   php artisan queue:work database --verbose --queue=high,default\n\n";
    echo "2. Set job priorities in your jobs:\n";
    echo "   \$this->queue = 'high';  // For important jobs\n\n";
    echo "3. Monitor worker memory usage:\n";
    echo "   php artisan queue:work --memory=512\n\n";
    echo "4. Use supervisor for production:\n";
    echo "   https://laravel.com/docs/queues#supervisor-configuration\n\n";
    echo "5. Monitor failed jobs regularly:\n";
    echo "   php artisan queue:failed\n\n";
}

// Main test execution
$testJobCount = 5;

echo "Starting queue performance test...\n\n";

// Step 1: Check initial status
checkQueueWorkerStatus();

// Step 2: Create test jobs
if (createTestJobs($testJobCount)) {
    // Step 3: Monitor processing
    monitorQueueProcessing(45);
    
    // Step 4: Final status check
    checkQueueWorkerStatus();
} else {
    echo "❌ Test failed to create jobs. Please check your database and models.\n";
}

// Step 5: Display recommendations
displayRecommendations();

echo "🎉 Queue performance test completed!\n";
echo "💡 Check the queue worker terminal for detailed processing logs.\n";
