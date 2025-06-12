<?php

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Log;
use App\Models\Group;

// Authentication channel
Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Chat channel - requires both authentication and group membership
Broadcast::channel('chat.{groupId}', function ($user, $groupId) {
    try {
        Log::info('Channel authentication attempt', [
            'channel' => 'chat.'.$groupId,
            'user_id' => $user->id,
            'group_id' => $groupId
        ]);

        $isMember = $user->groups()->where('group_id', $groupId)->exists();
        
        if (!$isMember) {
            Log::warning('Unauthorized chat access attempt', [
                'user_id' => $user->id,
                'group_id' => $groupId,
                'user_groups' => $user->groups()->pluck('group_id')->toArray()
            ]);
        } else {
            Log::info('Channel authentication successful', [
                'user_id' => $user->id,
                'group_id' => $groupId
            ]);
        }
        
        return $isMember;
    } catch (\Exception $e) {
        Log::error('Error in chat channel authorization', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
            'user_id' => $user->id,
            'group_id' => $groupId
        ]);
        return false;
    }
});