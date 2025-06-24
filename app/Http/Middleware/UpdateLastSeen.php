<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class UpdateLastSeen
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();
            
            // Update last_seen_at using direct DB query to avoid model events
            DB::table('users')
                ->where('id', $user->id)
                ->update([
                    'last_seen_at' => now(),
                    'updated_at' => $user->updated_at // Preserve the original updated_at
                ]);
        }

        return $next($request);
    }
}
