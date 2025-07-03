<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Group;

class EnsureGroupAdmin
{
    /**
     * Handle an incoming request.
     * Checks if user is admin in a specific group based on route parameter
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect('login');
        }

        $user = Auth::user();
        
        // Get group from route parameter (could be 'code' or 'id')
        $groupCode = $request->route('code');
        $groupId = $request->route('group_id') ?? $request->route('id');
        
        $group = null;
        
        if ($groupCode) {
            $group = Group::where('referral_code', $groupCode)->first();
        } elseif ($groupId) {
            $group = Group::find($groupId);
        }
        
        if (!$group) {
            return redirect()->route('ukm.index')
                ->with('error', 'Grup tidak ditemukan');
        }
        
        // Check if user is admin in this specific group
        if (!$user->isAdminInGroup($group)) {
            return redirect()->route('ukm.index')
                ->with('error', 'Anda tidak memiliki akses admin di grup ini');
        }
        
        // Store group info in request for controller use
        $request->attributes->set('group', $group);
        
        return $next($request);
    }
}
