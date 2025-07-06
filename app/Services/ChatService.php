<?php

namespace App\Services;

use App\Models\Chat;
use App\Models\Group;
use App\Models\User;
use App\Jobs\BroadcastChatMessage;
use App\Events\ChatMessageSent;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

/**
 * Service for handling chat operations
 */
class ChatService
{
    private const MESSAGE_HISTORY_LIMIT = 100;
    private const TYPING_TIMEOUT = 3; // seconds
    private const CACHE_TTL = 3600; // 1 hour

    /**
     * Send a chat message
     * 
     * @param User $user
     * @param Group $group
     * @param string $message
     * @return Chat
     */
    public function sendMessage(User $user, Group $group, string $message): Chat
    {
        // Validate user is member of group
        if (!$user->groups()->where('group_id', $group->id)->exists()) {
            throw new \Exception('User is not a member of this group');
        }

        // Check if user is muted
        $membership = $user->groups()
            ->where('group_id', $group->id)
            ->withPivot(['is_muted'])
            ->first();

        if ($membership && $membership->pivot->is_muted) {
            throw new \Exception('User is muted in this group');
        }

        // Filter and validate message
        $filteredMessage = $this->filterMessage($message);
        
        if (empty(trim($filteredMessage))) {
            throw new \Exception('Message cannot be empty');
        }

        // Create chat message
        $chat = Chat::create([
            'user_id' => $user->id,
            'group_id' => $group->id,
            'message' => $filteredMessage,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Load user relation for broadcasting
        $chat->load('user');

        // Broadcast message asynchronously
        $this->broadcastMessage($chat, $group->referral_code);

        Log::info('Chat message created', [
            'chat_id' => $chat->id,
            'user_id' => $user->id,
            'group_id' => $group->id,
            'group_code' => $group->referral_code
        ]);

        return $chat;
    }

    /**
     * Get chat messages for a group
     * 
     * @param Group $group
     * @param User $user
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getMessages(Group $group, User $user, ?int $limit = null): \Illuminate\Database\Eloquent\Collection
    {
        // Validate user is member of group
        if (!$user->groups()->where('group_id', $group->id)->exists()) {
            throw new \Exception('User is not a member of this group');
        }

        $limit = $limit ?? self::MESSAGE_HISTORY_LIMIT;

        return Chat::where('group_id', $group->id)
            ->with('user:id,name,nim')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->reverse()
            ->values();
    }

    /**
     * Get unread message count for a user in a group
     * 
     * @param User $user
     * @param int $groupId
     * @return array
     */
    public function getUnreadCount(User $user, int $groupId): array
    {
        try {
            $count = Chat::where('group_id', $groupId)
                ->where('user_id', '!=', $user->id)
                ->whereNull('read_at')
                ->count();

            return [
                'count' => $count,
                'success' => true
            ];
        } catch (\Exception $e) {
            Log::error('Error getting unread count', [
                'user_id' => $user->id,
                'group_id' => $groupId,
                'error' => $e->getMessage()
            ]);
            
            return [
                'count' => 0,
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Record typing indicator
     * 
     * @param User $user
     * @param Group $group
     * @return void
     */
    public function recordTyping(User $user, Group $group): void
    {
        $cacheKey = "typing_{$group->id}_{$user->id}";
        
        Cache::put($cacheKey, [
            'user_id' => $user->id,
            'user_name' => $user->name,
            'group_id' => $group->id,
            'timestamp' => now()
        ], self::TYPING_TIMEOUT);

        Log::debug('Typing indicator recorded', [
            'user_id' => $user->id,
            'group_id' => $group->id
        ]);
    }

    /**
     * Get currently typing users in group
     * 
     * @param Group $group
     * @return array
     */
    public function getTypingUsers(Group $group): array
    {
        $typingUsers = [];
        $cachePattern = "typing_{$group->id}_*";
        
        // This is a simplified implementation
        // In production, you might want to use Redis pattern matching
        
        return $typingUsers;
    }

    /**
     * Filter message content for security and formatting
     * 
     * @param string $message
     * @return string
     */
    private function filterMessage(string $message): string
    {
        // Remove HTML tags and encode special characters
        $message = htmlspecialchars(strip_tags($message), ENT_QUOTES, 'UTF-8');
        
        // Trim whitespace
        $message = trim($message);
        
        // Limit message length
        if (strlen($message) > 1000) {
            $message = substr($message, 0, 1000);
        }
        
        return $message;
    }

    /**
     * Broadcast message to group members
     * 
     * @param Chat $chat
     * @param string $groupCode
     * @return void
     */
    private function broadcastMessage(Chat $chat, string $groupCode): void
    {
        try {
            // Try async queue broadcasting first
            dispatch(new BroadcastChatMessage($chat, $groupCode))
                ->onQueue('high');
                
            Log::info('Chat broadcast job dispatched', [
                'chat_id' => $chat->id,
                'group_code' => $groupCode,
                'queue' => 'high'
            ]);
        } catch (\Exception $queueException) {
            // Fallback to synchronous broadcasting
            Log::warning('Queue dispatch failed, falling back to sync broadcast', [
                'chat_id' => $chat->id,
                'error' => $queueException->getMessage()
            ]);
            
            try {
                event(new ChatMessageSent($chat));
            } catch (\Exception $broadcastException) {
                Log::error('Both async and sync broadcasting failed', [
                    'chat_id' => $chat->id,
                    'queue_error' => $queueException->getMessage(),
                    'broadcast_error' => $broadcastException->getMessage()
                ]);
            }
        }
    }

    /**
     * Join user to group for chat
     * 
     * @param User $user
     * @param string $groupCode
     * @return Group
     */
    public function joinGroup(User $user, string $groupCode): Group
    {
        $group = Group::where('referral_code', $groupCode)->firstOrFail();
        
        // Check if user is already a member
        if ($user->groups()->where('group_id', $group->id)->exists()) {
            throw new \Exception('User is already a member of this group');
        }
        
        // Add user to group
        $user->groups()->attach($group->id, [
            'is_admin' => false,
            'is_muted' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        Log::info('User joined group via chat', [
            'user_id' => $user->id,
            'group_id' => $group->id,
            'group_code' => $groupCode
        ]);
        
        return $group;
    }

    /**
     * Remove user from group chat session
     * 
     * @param User $user
     * @param Group $group
     * @return void
     */
    public function leaveGroup(User $user, Group $group): void
    {
        // Clear typing indicators
        $cacheKey = "typing_{$group->id}_{$user->id}";
        Cache::forget($cacheKey);
        
        Log::info('User left group chat session', [
            'user_id' => $user->id,
            'group_id' => $group->id
        ]);
    }

    /**
     * Get chat data for a group and user
     * 
     * @param User $user
     * @param Group $group
     * @return array
     */
    public function getChatData(User $user, Group $group): array
    {
        try {
            // Get recent chats
            $chats = $group->chats()
                ->with('user')
                ->orderBy('created_at', 'desc')
                ->limit(self::MESSAGE_HISTORY_LIMIT)
                ->get()
                ->reverse();
            
            // Check if user is muted
            $userMembership = $group->users()->where('user_id', $user->id)->first();
            $isMuted = $userMembership && $userMembership->pivot->is_muted;
            
            return [
                'chats' => $chats,
                'groupName' => $group->name,
                'memberCount' => $group->users()->count(),
                'groupCode' => $group->referral_code,
                'groupId' => $group->id,
                'isMuted' => $isMuted,
            ];
        } catch (\Exception $e) {
            Log::error('Error getting chat data', [
                'user_id' => $user->id,
                'group_id' => $group->id,
                'error' => $e->getMessage()
            ]);
            
            return [
                'chats' => collect(),
                'groupName' => $group->name ?? 'Unknown Group',
                'memberCount' => 0,
                'groupCode' => $group->referral_code ?? '',
                'groupId' => $group->id ?? 0,
                'isMuted' => false,
            ];
        }
    }
}
