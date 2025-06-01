<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\Group;

Broadcast::channel('chat.{groupCode}', function ($user, $groupCode) {
    $group = Group::where('referral_code', $groupCode)->first();
    return $group && $user->groups()->where('group_id', $group->id)->exists();
});