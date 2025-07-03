<?php

/**
 * Queue Worker Performance Monitor
 * Script untuk monitor performa queue worker dan statistik jobs
 */

require_once __DIR__ . '/../vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

// Bootstrap Laravel
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

function displayHeader() {
    echo "\033[2J\033[H"; // Clear screen
    echo "╔══════════════════════════════════════════════════════════════════╗\n";
    echo "║                    MyUKM Queue Worker Monitor                    ║\n";
    echo "║                      " . date('Y-m-d H:i:s') . "                        ║\n";
    echo "╚══════════════════════════════════════════════════════════════════╝\n\n";
}

function getQueueStats() {
    try {
        $pending = DB::table('jobs')->count();
        $failed = DB::table('failed_jobs')->count();
        
        // Get recent jobs from the last hour
        $recentJobs = DB::table('jobs')
            ->where('created_at', '>=', time() - 3600)
            ->count();
            
        return [
            'pending' => $pending,
            'failed' => $failed,
            'recent' => $recentJobs,
            'processed_today' => 0 // This would need job history table
        ];
    } catch (Exception $e) {
        return [
            'pending' => 'Error',
            'failed' => 'Error',
            'recent' => 'Error',
            'processed_today' => 'Error'
        ];
    }
}

function displayStats($stats) {
    echo "📊 QUEUE STATISTICS\n";
    echo "────────────────────────────────────────────────────────────────\n";
    echo sprintf("⏳ Pending Jobs:     %s\n", $stats['pending']);
    echo sprintf("❌ Failed Jobs:      %s\n", $stats['failed']);
    echo sprintf("🕐 Recent (1h):      %s\n", $stats['recent']);
    echo sprintf("✅ Processed Today:  %s\n", $stats['processed_today']);
    echo "\n";
}

function displayRecentActivity() {
    try {
        echo "📋 RECENT QUEUE ACTIVITY\n";
        echo "────────────────────────────────────────────────────────────────\n";
        
        $recentJobs = DB::table('jobs')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get(['id', 'queue', 'payload', 'created_at']);
            
        if ($recentJobs->count() > 0) {
            foreach ($recentJobs as $job) {
                $payload = json_decode($job->payload, true);
                $jobClass = $payload['displayName'] ?? 'Unknown Job';
                $createdAt = date('H:i:s', $job->created_at);
                echo sprintf("• %s - %s (Queue: %s)\n", $createdAt, $jobClass, $job->queue);
            }
        } else {
            echo "No recent jobs in queue\n";
        }
        echo "\n";
    } catch (Exception $e) {
        echo "Error fetching recent activity: " . $e->getMessage() . "\n\n";
    }
}

function displayFailedJobs() {
    try {
        echo "💥 FAILED JOBS (Last 5)\n";
        echo "────────────────────────────────────────────────────────────────\n";
        
        $failedJobs = DB::table('failed_jobs')
            ->orderBy('failed_at', 'desc')
            ->limit(5)
            ->get(['id', 'queue', 'payload', 'exception', 'failed_at']);
            
        if ($failedJobs->count() > 0) {
            foreach ($failedJobs as $job) {
                $payload = json_decode($job->payload, true);
                $jobClass = $payload['displayName'] ?? 'Unknown Job';
                $failedAt = $job->failed_at;
                echo sprintf("• %s - %s\n", $failedAt, $jobClass);
                
                // Show first line of exception
                $exceptionLines = explode("\n", $job->exception);
                echo sprintf("  Error: %s\n", substr($exceptionLines[0], 0, 60) . '...');
            }
        } else {
            echo "No failed jobs\n";
        }
        echo "\n";
    } catch (Exception $e) {
        echo "Error fetching failed jobs: " . $e->getMessage() . "\n\n";
    }
}

function displayInstructions() {
    echo "⚡ QUEUE WORKER COMMANDS\n";
    echo "────────────────────────────────────────────────────────────────\n";
    echo "Start Worker:     php artisan queue:work database --verbose\n";
    echo "Restart Workers:  php artisan queue:restart\n";
    echo "Clear Failed:     php artisan queue:flush\n";
    echo "Retry Failed:     php artisan queue:retry all\n";
    echo "\n";
    echo "Press Ctrl+C to exit monitor\n";
}

// Main monitoring loop
echo "Starting Queue Monitor...\n";
echo "Press Ctrl+C to exit\n\n";

while (true) {
    displayHeader();
    
    $stats = getQueueStats();
    displayStats($stats);
    
    displayRecentActivity();
    displayFailedJobs();
    displayInstructions();
    
    // Wait 5 seconds before next update
    sleep(5);
}
