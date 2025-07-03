<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use App\Models\User;

class BroadcastOnlineStatus implements ShouldQueue
{
    use Queueable, InteractsWithQueue, SerializesModels;

    public $timeout = 30;
    public $tries = 2;
    public $maxExceptions = 1;

    protected $userId;
    protected $isOnline;
    protected $groupCode;

    /**
     * Create a new job instance.
     */
    public function __construct(int $userId, bool $isOnline, string $groupCode = null)
    {
        $this->userId = $userId;
        $this->isOnline = $isOnline;
        $this->groupCode = $groupCode;
        
        // Set queue priority (lower than chat messages)
        $this->queue = 'default';
        
        Log::info('BroadcastOnlineStatus job created', [
            'user_id' => $userId,
            'is_online' => $isOnline,
            'group_code' => $groupCode
        ]);
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            Log::info('Broadcasting online status', [
                'user_id' => $this->userId,
                'is_online' => $this->isOnline,
                'group_code' => $this->groupCode,
                'attempts' => $this->attempts()
            ]);

            $user = User::find($this->userId);
            if (!$user) {
                Log::warning('User not found for online status broadcast', [
                    'user_id' => $this->userId
                ]);
                return;
            }

            // Prepare status data for broadcasting
            $statusData = [
                'user_id' => $this->userId,
                'name' => $user->name,
                'is_online' => $this->isOnline,
                'last_seen' => now()->toISOString()
            ];

            // Broadcast to specific group or globally
            if ($this->groupCode) {
                broadcast(new \App\Events\UserStatusChanged($statusData))
                    ->toOthers()
                    ->via('pusher');
            } else {
                // Global status update
                broadcast(new \App\Events\UserStatusChanged($statusData))
                    ->toOthers()
                    ->via('pusher');
            }

            Log::info('Online status broadcasted successfully', [
                'user_id' => $this->userId,
                'is_online' => $this->isOnline
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to broadcast online status', [
                'user_id' => $this->userId,
                'is_online' => $this->isOnline,
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
        Log::error('BroadcastOnlineStatus job failed permanently', [
            'user_id' => $this->userId,
            'is_online' => $this->isOnline,
            'error' => $exception->getMessage(),
            'attempts' => $this->attempts()
        ]);
    }
}
