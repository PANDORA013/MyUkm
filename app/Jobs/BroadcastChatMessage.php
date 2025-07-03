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
 * Job for broadcasting chat messages asynchronously.
 * 
 * This job handles the broadcasting of chat messages in the background,
 * improving response times and user experience in real-time chat features.
 * 
 * @package App\Jobs
 */
class BroadcastChatMessage implements ShouldQueue
{
    use Queueable, InteractsWithQueue, SerializesModels;

    /**
     * The number of seconds the job can run before timing out.
     */
    public int $timeout = 60;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 3;

    /**
     * The maximum number of unhandled exceptions to allow before failing.
     */
    public int $maxExceptions = 2;

    /**
     * Job configuration constants.
     */
    private const QUEUE_NAME = 'high';

    /**
     * The chat message to be broadcasted.
     */
    private Chat $chat;

    /**
     * The group referral code for targeting the broadcast.
     */
    private string $groupCode;

    /**
     * Create a new job instance.
     *
     * @param Chat $chat The chat message to broadcast
     * @param string $groupCode The group referral code
     */
    public function __construct(Chat $chat, string $groupCode)
    {
        $this->chat = $chat;
        $this->groupCode = $groupCode;
        $this->onQueue(self::QUEUE_NAME);
        
        $this->logJobCreation();
    }

    /**
     * Execute the job.
     * 
     * Broadcasts the chat message to other users in the group.
     * Includes retry mechanism and comprehensive error handling.
     */
    public function handle(): void
    {
        try {
            $this->logJobStart();
            $this->ensureUserRelationLoaded();
            $this->broadcastMessage();
            $this->logJobSuccess();
            
        } catch (\Exception $e) {
            $this->logJobError($e);
            throw $e; // Re-throw to trigger retry mechanism
        }
    }

    /**
     * Handle a job failure.
     * 
     * Called when the job has failed after all retry attempts.
     *
     * @param \Throwable $exception The exception that caused the failure
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('BroadcastChatMessage job failed permanently', [
            'chat_id' => $this->chat->id,
            'group_code' => $this->groupCode,
            'error' => $exception->getMessage(),
            'attempts' => $this->attempts(),
            'max_tries' => $this->tries
        ]);
    }

    /**
     * Log job creation.
     */
    private function logJobCreation(): void
    {
        Log::info('BroadcastChatMessage job created', [
            'chat_id' => $this->chat->id,
            'group_code' => $this->groupCode,
            'user_id' => $this->chat->user_id,
            'queue' => self::QUEUE_NAME
        ]);
    }

    /**
     * Log job start.
     */
    private function logJobStart(): void
    {
        Log::info('Broadcasting chat message', [
            'chat_id' => $this->chat->id,
            'group_code' => $this->groupCode,
            'attempts' => $this->attempts(),
            'max_tries' => $this->tries
        ]);
    }

    /**
     * Log job success.
     */
    private function logJobSuccess(): void
    {
        Log::info('Chat message broadcasted successfully', [
            'chat_id' => $this->chat->id,
            'group_code' => $this->groupCode,
            'attempts' => $this->attempts()
        ]);
    }

    /**
     * Log job error.
     */
    private function logJobError(\Exception $e): void
    {
        Log::error('Failed to broadcast chat message', [
            'chat_id' => $this->chat->id,
            'group_code' => $this->groupCode,
            'error' => $e->getMessage(),
            'attempts' => $this->attempts(),
            'trace' => $e->getTraceAsString()
        ]);
    }

    /**
     * Ensure user relationship is loaded.
     */
    private function ensureUserRelationLoaded(): void
    {
        if (!$this->chat->relationLoaded('user')) {
            $this->chat->load('user');
        }
    }

    /**
     * Broadcast the message to other users.
     */
    private function broadcastMessage(): void
    {
        broadcast(new ChatMessageSent($this->chat))->toOthers();
    }
}
