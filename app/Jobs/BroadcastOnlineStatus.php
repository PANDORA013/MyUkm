<?php

namespace App\Jobs;

use App\Events\UserStatusChanged;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Job for broadcasting user online status asynchronously.
 * 
 * This job handles online/offline status updates in the background,
 * improving performance and user experience in real-time features.
 * 
 * @package App\Jobs
 */
class BroadcastOnlineStatus implements ShouldQueue
{
    use Queueable, InteractsWithQueue, SerializesModels;

    /**
     * The number of seconds the job can run before timing out.
     */
    public int $timeout = 30;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 2;

    /**
     * The maximum number of unhandled exceptions to allow before failing.
     */
    public int $maxExceptions = 1;

    /**
     * Job configuration constants.
     */
    private const QUEUE_NAME = 'default';

    /**
     * The user ID whose status is being updated.
     */
    private int $userId;

    /**
     * Whether the user is online or offline.
     */
    private bool $isOnline;

    /**
     * The group referral code for targeting the broadcast.
     */
    private ?string $groupCode;

    /**
     * Create a new job instance.
     *
     * @param int $userId The user ID
     * @param bool $isOnline Whether the user is online
     * @param string|null $groupCode The group referral code (optional)
     */
    public function __construct(int $userId, bool $isOnline, ?string $groupCode = null)
    {
        $this->userId = $userId;
        $this->isOnline = $isOnline;
        $this->groupCode = $groupCode;
        $this->onQueue(self::QUEUE_NAME);
        
        $this->logJobCreation();
    }

    /**
     * Execute the job.
     * 
     * Broadcasts the user's online status to other users.
     * Includes retry mechanism and comprehensive error handling.
     */
    public function handle(): void
    {
        try {
            $this->logJobStart();
            
            $user = $this->findUser();
            if (!$user) {
                $this->logUserNotFound();
                return;
            }

            $statusData = $this->prepareStatusData($user);
            $this->broadcastStatus($statusData);
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
        Log::error('BroadcastOnlineStatus job failed permanently', [
            'user_id' => $this->userId,
            'is_online' => $this->isOnline,
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
        Log::info('BroadcastOnlineStatus job created', [
            'user_id' => $this->userId,
            'is_online' => $this->isOnline,
            'group_code' => $this->groupCode,
            'queue' => self::QUEUE_NAME
        ]);
    }

    /**
     * Log job start.
     */
    private function logJobStart(): void
    {
        Log::info('Broadcasting online status', [
            'user_id' => $this->userId,
            'is_online' => $this->isOnline,
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
        Log::info('Online status broadcasted successfully', [
            'user_id' => $this->userId,
            'is_online' => $this->isOnline,
            'group_code' => $this->groupCode,
            'attempts' => $this->attempts()
        ]);
    }

    /**
     * Log job error.
     */
    private function logJobError(\Exception $e): void
    {
        Log::error('Failed to broadcast online status', [
            'user_id' => $this->userId,
            'is_online' => $this->isOnline,
            'group_code' => $this->groupCode,
            'error' => $e->getMessage(),
            'attempts' => $this->attempts(),
            'trace' => $e->getTraceAsString()
        ]);
    }

    /**
     * Log when user is not found.
     */
    private function logUserNotFound(): void
    {
        Log::warning('User not found for online status broadcast', [
            'user_id' => $this->userId,
            'is_online' => $this->isOnline,
            'group_code' => $this->groupCode
        ]);
    }

    /**
     * Find the user by ID.
     * 
     * @return User|null The user or null if not found
     */
    private function findUser(): ?User
    {
        return User::find($this->userId);
    }

    /**
     * Prepare status data for broadcasting.
     * 
     * @param User $user The user object
     * @return array The formatted status data
     */
    private function prepareStatusData(User $user): array
    {
        return [
            'user_id' => $this->userId,
            'name' => $user->name,
            'is_online' => $this->isOnline,
            'last_seen' => now()->toISOString(),
            'group_code' => $this->groupCode
        ];
    }

    /**
     * Broadcast the status to other users.
     * 
     * @param array $statusData The status data to broadcast
     */
    private function broadcastStatus(array $statusData): void
    {
        broadcast(new UserStatusChanged($statusData))
            ->toOthers()
            ->via('pusher');
    }
}
