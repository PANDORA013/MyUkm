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

        // Tambahkan pengecekan group membership jika diperlukan
        if ($request->has('group_id')) {
            $groupId = $request->group_id;
            if (!$user->groups()->where('group_id', $groupId)->exists()) {
                abort(403, 'Anda bukan anggota grup ini');
            }
        }

        return $next($request);
    }
}
