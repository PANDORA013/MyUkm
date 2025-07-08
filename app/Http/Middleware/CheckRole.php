<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  mixed  ...$roles
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        /** @var User|null $user */
        $user = Auth::user();

        if (!$user || !in_array($user->role, $roles, true)) {
            abort(403);
        }

        // Check group membership if group_id is specified in the request
        if ($request->has('group_id')) {
            $groupId = $request->group_id;
            if (!$user->groups()->where('group_id', $groupId)->exists()) {
                if ($request->ajax()) {
                    return response()->json(['error' => 'Anda bukan anggota grup ini'], 403);
                }
                return redirect()->route('ukm.index')
                    ->with('error', 'Anda bukan anggota grup ini');
            }
        }
        
        // For group-specific routes with code parameter
        if ($request->route('code')) {
            $code = $request->route('code');
            $group = \App\Models\Group::where('referral_code', $code)->first();
            if ($group && !$user->groups()->where('group_id', $group->id)->exists()) {
                if ($request->ajax()) {
                    return response()->json(['error' => 'Anda bukan anggota grup ini'], 403);
                }
                return redirect()->route('ukm.index')
                    ->with('error', 'Anda bukan anggota grup ini');
            }
        }

        return $next($request);
    }
}
