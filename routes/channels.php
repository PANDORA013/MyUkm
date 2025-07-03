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

// Group channel for online status updates - requires group membership
Broadcast::channel('group.{groupCode}', function ($user, $groupCode) {
    try {
        Log::info('Group channel authentication attempt', [
            'channel' => 'group.'.$groupCode,
            'user_id' => $user->id,
            'group_code' => $groupCode
        ]);

        // Cari grup berdasarkan kode
        $group = Group::where('code', $groupCode)->first();
        if (!$group) {
            Log::warning('Group not found for online status channel', [
                'user_id' => $user->id,
                'group_code' => $groupCode
            ]);
            return false;
        }

        // Cek apakah user adalah anggota grup
        $isMember = $user->groups()->where('group_id', $group->id)->exists();
        
        if (!$isMember) {
            Log::warning('Unauthorized group online status access attempt', [
                'user_id' => $user->id,
                'group_code' => $groupCode,
                'group_id' => $group->id
            ]);
        } else {
            Log::info('Group channel authentication successful', [
                'user_id' => $user->id,
                'group_code' => $groupCode,
                'group_id' => $group->id
            ]);
        }
        
        return $isMember;
    } catch (\Exception $e) {
        Log::error('Error in group channel authorization', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
            'user_id' => $user->id,
            'group_code' => $groupCode
        ]);
        return false;
    }
});