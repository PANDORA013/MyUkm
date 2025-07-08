<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class UserOnlineStatusChanged implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $userId;
    public $userName;
    public $isOnline;
    public $groupCode;
    public $onlineCount;
    public $timestamp;

    /**
     * Create a new event instance.
     */
    public function __construct($userId, $userName, $isOnline, $groupCode, $onlineCount)
    {
        $this->userId = $userId;
        $this->userName = $userName;
        $this->isOnline = $isOnline;
        $this->groupCode = $groupCode;
        $this->onlineCount = $onlineCount;
        $this->timestamp = now();

        Log::info('UserOnlineStatusChanged event created', [
            'user_id' => $userId,
            'user_name' => $userName,
            'is_online' => $isOnline,
            'group_code' => $groupCode,
            'online_count' => $onlineCount
        ]);
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('group.' . $this->groupCode),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'user.online.status.changed';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'user_id' => $this->userId,
            'user_name' => $this->userName,
            'is_online' => $this->isOnline,
            'online_count' => $this->onlineCount,
            'timestamp' => $this->timestamp->toISOString()
        ];
    }
}
