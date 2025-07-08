<?php

namespace App\Events;

use App\Models\Chat;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow; // OPTIMIZED: Immediate broadcasting
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

// OPTIMIZED: ShouldBroadcastNow untuk instant broadcasting tanpa queue
class ChatMessageSent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    private $chatId;
    private $userId;
    private $userName;
    private $message;
    private $groupId;
    private $time;
    public Chat $chat;
    public User $user;

    public function __construct(Chat $chat)
    {
        $this->chat = $chat;
        $this->user = $chat->user;
        
        // Ensure group relation is loaded
        if (!$chat->relationLoaded('group')) {
            $chat->load('group');
        }
        
        $this->chatId = $chat->id;
        $this->userId = $chat->user_id;
        $this->userName = $chat->user->name;
        $this->message = $chat->message;
        $this->groupId = $chat->group_id;
        $this->time = $chat->created_at->format('H:i');
    }

    public function broadcastOn()
    {
        Log::info('Broadcasting on channel', [
            'channel' => 'group.' . $this->chat->group->referral_code,
            'chat_id' => $this->chatId
        ]);
        return new PrivateChannel('group.' . $this->chat->group->referral_code);
    }

    public function broadcastAs()
    {
        return 'chat.message';
    }

    public function broadcastWith()
    {
        return [
            'message' => $this->message,
            'chat' => [
                'id' => $this->chat->id,
                'user_id' => $this->chat->user_id,
                'group_id' => $this->chat->group_id,
                'message' => $this->chat->message,
                'created_at' => $this->chat->created_at,
            ],
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
            ],
            'time' => $this->time
        ];
    }
}
