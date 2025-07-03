<?php

namespace App\Jobs;

use App\Events\ChatMessageSent;
use App\Models\Chat;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Job for broadcasting chat messages asynchronously with maximum real-time responsiveness.
 * 
 * ULTRA-OPTIMIZED: This job handles the broadcasting of chat messages in the background,
 * improving response times and user experience in real-time chat features.
 * 
 * @package App\Jobs
 */
class BroadcastChatMessage implements ShouldQueue
{
    use Queueable, InteractsWithQueue, SerializesModels;

    /**
     * The number of seconds the job can run before timing out.
     * ULTRA-OPTIMIZED: Aggressive timeout untuk broadcasting instan
     */
    public int $timeout = 5;

    /**
     * The number of times the job may be attempted.
     * ULTRA-OPTIMIZED: Minimal attempts untuk fail-fast
     */
    public int $tries = 1;

    /**
     * The maximum number of unhandled exceptions to allow before failing.
     */
    public int $maxExceptions = 1;

    /**
     * Delay between retries in seconds.
     * ULTRA-OPTIMIZED: No delay untuk instant retry
     */
    public int $retryAfter = 0;

    /**
     * Delete the job if its models no longer exist.
     * ULTRA-OPTIMIZED: Auto-cleanup untuk memory efficiency
     */
    public bool $deleteWhenMissingModels = true;

    /**
     * Job configuration constants.
     * ULTRA-OPTIMIZED: Realtime queue dengan prioritas tertinggi
     */
    private const QUEUE_NAME = 'realtime';

    /**
     * The chat message to broadcast.
     */
    private Chat $chat;

    /**
     * Group code for debugging and logging.
     */
    private string $groupCode;

    /**
     * Create a new job instance.
     *
     * @param Chat $chat The chat message to broadcast
     */
    public function __construct(Chat $chat)
    {
        $this->chat = $chat;
        
        // Ensure group relation is loaded for performance
        if (!$chat->relationLoaded('group')) {
            $chat->load('group');
        }
        
        $this->groupCode = $chat->group->referral_code ?? 'unknown';
        
        // ULTRA-OPTIMIZED: Set queue dengan prioritas tertinggi
        $this->onQueue(self::QUEUE_NAME);
        
        Log::info('BroadcastChatMessage job created', [
            'chat_id' => $chat->id,
            'group_code' => $this->groupCode,
            'queue' => self::QUEUE_NAME
        ]);
    }

    /**
     * Execute the job.
     * ULTRA-OPTIMIZED: Maximum speed broadcasting dengan minimal overhead
     */
    public function handle(): void
    {
        $startTime = microtime(true);
        
        try {
            Log::info('Starting broadcast job execution', [
                'chat_id' => $this->chat->id,
                'group_code' => $this->groupCode,
                'queue' => self::QUEUE_NAME
            ]);

            $this->broadcastMessage();

            $executionTime = round((microtime(true) - $startTime) * 1000, 2);
            
            Log::info('Broadcast job completed successfully', [
                'chat_id' => $this->chat->id,
                'group_code' => $this->groupCode,
                'execution_time_ms' => $executionTime,
                'queue' => self::QUEUE_NAME
            ]);

        } catch (\Exception $e) {
            $executionTime = round((microtime(true) - $startTime) * 1000, 2);
            
            Log::error('Broadcast job failed', [
                'chat_id' => $this->chat->id,
                'group_code' => $this->groupCode,
                'error' => $e->getMessage(),
                'execution_time_ms' => $executionTime,
                'queue' => self::QUEUE_NAME,
                'trace' => $e->getTraceAsString()
            ]);

            throw $e;
        }
    }

    /**
     * Broadcast the message using optimized settings.
     * ULTRA-OPTIMIZED: Ultra-fast broadcasting dengan minimal overhead
     */
    private function broadcastMessage(): void
    {
        try {
            // ULTRA-OPTIMIZED: Direct broadcasting tanpa delay
            $event = new ChatMessageSent($this->chat);
            
            // ULTRA-OPTIMIZED: Use immediate broadcasting untuk real-time
            broadcast($event)
                ->toOthers()
                ->via(['pusher']); // Only use Pusher untuk speed maksimal
                
        } catch (\Exception $e) {
            // ULTRA-OPTIMIZED: Log specific broadcasting errors
            Log::error('Broadcasting failed in BroadcastChatMessage', [
                'chat_id' => $this->chat->id,
                'group_code' => $this->groupCode,
                'error' => $e->getMessage(),
                'pusher_available' => config('broadcasting.default') === 'pusher'
            ]);
            throw $e;
        }
    }

    /**
     * Get the middleware the job should pass through.
     * ULTRA-OPTIMIZED: Minimal middleware untuk speed maksimal
     */
    public function middleware(): array
    {
        return []; // No middleware untuk performance maksimal
    }

    /**
     * Determine if the job should be retried.
     * ULTRA-OPTIMIZED: Smart retry logic
     */
    public function retryUntil(): \DateTime
    {
        return now()->addSeconds(30); // Max 30 second retry window untuk fail-fast
    }

    /**
     * Handle a job failure.
     * ULTRA-OPTIMIZED: Fast failure handling
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('BroadcastChatMessage job failed permanently', [
            'chat_id' => $this->chat->id,
            'group_code' => $this->groupCode,
            'error' => $exception->getMessage(),
            'queue' => self::QUEUE_NAME
        ]);
    }

    /**
     * Get the tags that should be assigned to the job.
     * ULTRA-OPTIMIZED: Better job tracking dan monitoring
     */
    public function tags(): array
    {
        return [
            'broadcast',
            'chat:' . $this->chat->id,
            'group:' . $this->groupCode,
            'realtime'
        ];
    }
}
