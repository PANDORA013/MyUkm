<?php

namespace App\Events;

use App\Models\Chat;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;

class MessageRead implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $chat;

    public function __construct(Chat $chat)
    {
        $this->chat = $chat;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('chat.' . $this->chat->group_id);
    }

    public function broadcastAs()
    {
        return 'message.read';
    }

    public function broadcastWith()
    {
        return [            'message_id' => $this->chat->id,
            'read_at' => $this->chat->read_at->format('Y-m-d H:i:s'),
            'reader_id' => Auth::id()
        ];
    }
}
