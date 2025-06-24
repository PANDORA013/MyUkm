<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class CheckGroupMembership
{
    private const CACHE_TTL = 3600; // 1 hour

    public function handle($request, Closure $next)
    {
        $user = Auth::user();
        $groupId = $request->route('groupId') ?? session('active_group_id');

        if (!$groupId || !$this->isGroupMember($user, $groupId)) {
            return redirect()->route('ukm.index')
                ->with('error', 'Anda tidak tergabung dalam UKM ini');
        }

        return $next($request);
    }

    private function isGroupMember($user, $groupId): bool
    {
        $cacheKey = "group_membership:{$user->id}:{$groupId}";
        
        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($user, $groupId) {
            return $user->groups()->where('group_id', $groupId)->exists();
        });
    }
}
