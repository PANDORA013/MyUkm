<?php

namespace App\Events;

use App\Models\Chat;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ChatMessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $chat;
    public $user;
    public $message;
    public $time;

    public function __construct(Chat $chat)
    {
        $this->chat = $chat;
        $this->user = $chat->user;
        $this->message = $chat->message;
        $this->time = $chat->created_at->format('H:i');
    }

    public function broadcastOn()
    {
        return new Channel('chat.' . $this->chat->group_code);
    }
}
