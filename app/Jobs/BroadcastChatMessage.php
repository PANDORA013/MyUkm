<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use App\Models\Chat;
use App\Events\ChatMessageSent;

class BroadcastChatMessage implements ShouldQueue
{
    use Queueable, InteractsWithQueue, SerializesModels;

    public $timeout = 60;
    public $tries = 3;
    public $maxExceptions = 2;

    protected $chat;
    protected $groupCode;

    /**
     * Create a new job instance.
     */
    public function __construct(Chat $chat, string $groupCode)
    {
        $this->chat = $chat;
        $this->groupCode = $groupCode;
        
        // Set queue priority
        $this->queue = 'high';
        
        Log::info('BroadcastChatMessage job created', [
            'chat_id' => $chat->id,
            'group_code' => $groupCode,
            'user_id' => $chat->user_id
        ]);
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            Log::info('Broadcasting chat message', [
                'chat_id' => $this->chat->id,
                'group_code' => $this->groupCode,
                'attempts' => $this->attempts()
            ]);

            // Load user relationship if not loaded
            if (!$this->chat->relationLoaded('user')) {
                $this->chat->load('user');
            }

            // Prepare message data for broadcasting
            $messageData = [
                'id' => $this->chat->id,
                'message' => $this->chat->message,
                'user_id' => $this->chat->user_id,
                'name' => $this->chat->user->name ?? 'Unknown User',
                'created_at' => $this->chat->created_at->toISOString(),
                'group_id' => $this->chat->group_id,
                'formatted_time' => $this->chat->created_at->format('H:i')
            ];

            // Broadcast the event
            broadcast(new ChatMessageSent(
                $messageData,
                $this->groupCode
            ))->toOthers();

            Log::info('Chat message broadcasted successfully', [
                'chat_id' => $this->chat->id,
                'group_code' => $this->groupCode
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to broadcast chat message', [
                'chat_id' => $this->chat->id,
                'group_code' => $this->groupCode,
                'error' => $e->getMessage(),
                'attempts' => $this->attempts()
            ]);

            // Re-throw exception to trigger retry mechanism
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('BroadcastChatMessage job failed permanently', [
            'chat_id' => $this->chat->id,
            'group_code' => $this->groupCode,
            'error' => $exception->getMessage(),
            'attempts' => $this->attempts()
        ]);
    }
}
