<?php

namespace App\Jobs;

use App\Models\Chat;
use App\Events\ChatMessageSent;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class ProcessChatMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $chat;
    private $message;

    public function __construct(Chat $chat, string $message)
    {
        $this->chat = $chat;
        $this->message = $message;
    }

    public function handle()
    {
        try {
            // Process and filter message
            $filteredMessage = $this->filterMessage($this->message);

            // Update the chat message
            $this->chat->update(['message' => $filteredMessage]);

            // Clear group chats cache
            Cache::forget("group_chats:{$this->chat->group_id}");

            // Broadcast the message
            broadcast(new ChatMessageSent($this->chat))->toOthers();

            Log::info('Chat message processed successfully', [
                'chat_id' => $this->chat->id,
                'user_id' => $this->chat->user_id,
                'group_id' => $this->chat->group_id
            ]);
        } catch (\Exception $e) {
            Log::error('Error processing chat message', [
                'chat_id' => $this->chat->id,
                'error' => $e->getMessage()
            ]);
            $this->fail($e);
        }
    }

    private function filterMessage(string $message): string
    {
        // Basic XSS prevention
        $message = htmlspecialchars($message, ENT_QUOTES, "UTF-8");
        
        // Convert URLs to clickable links
        $message = preg_replace(
            "/(https?:\/\/[^\s<]+)/i",
            "<a href=\"$1\" target=\"_blank\" rel=\"noopener noreferrer\" class=\"text-blue-500 hover:underline\">$1</a>",
            $message
        );

        // Basic emoji support
        $emojis = [
            ":)" => "😊",
            ":(" => "😢",
            ":D" => "😀",
            ";)" => "😉",
            "<3" => "❤️",
            ":p" => "😛",
            ":P" => "😛",
            ":o" => "😮",
            ":O" => "😮",
        ];

        return str_replace(array_keys($emojis), array_values($emojis), $message);
    }
}