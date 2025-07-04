<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class EnsureAdminGrupAccess
{
    /**
     * Handle an incoming request.
     * Ensure admin_grup users can only access groups they are admin of
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        /** @var User $user */
        $user = Auth::user();

        // Only apply to admin_grup users
        if ($user->role !== 'admin_grup') {
            return $next($request);
        }
        
        // Check if the admin_grup has any groups to manage
        if (!$user->groups()->wherePivot('is_admin', true)->exists()) {
            return redirect()->route('ukm.index')
                ->with('error', 'Anda tidak memiliki grup yang dapat dikelola');
        }
        
        // Get group ID from route parameters
        $groupId = $request->route('id');
        
        // If there's a specific group ID, check if admin manages it
        if ($groupId) {
            if (!$user->groups()->wherePivot('is_admin', true)->where('groups.id', $groupId)->exists()) {
                return redirect()->route('grup.dashboard')
                    ->with('error', 'Anda tidak memiliki akses untuk mengelola grup ini');
            }
        }

        return $next($request);
    }
}
