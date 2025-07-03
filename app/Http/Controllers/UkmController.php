<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UkmController extends Controller
{
    /**
     * Display list of UKMs
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        /** @var User $user */
        $user = Auth::user();
        $joinedGroups = $user->groups()->with('users')->get();
        $availableGroups = Group::whereNotIn('id', $joinedGroups->pluck('id'))->get();
        
        // Berdasarkan role user, tampilkan view yang sesuai
        if ($user->role === 'admin_website' || $user->role === 'admin_grup') {
            return view('ukm.index', [
                'joinedGroups' => $joinedGroups,
                'availableGroups' => $availableGroups,
                'userUkm' => $user->ukm_id ? Group::find($user->ukm_id) : null
            ]);
        } else {
            // Untuk user biasa (anggota), gunakan view khusus user
            return view('ukm.user_index', [
                'joinedGroups' => $joinedGroups,
                'availableGroups' => $availableGroups,
                'userUkm' => $user->ukm_id ? Group::find($user->ukm_id) : null
            ]);
        }
    }

    public function join(Request $request)
    {
        $request->validate([
            'group_code' => 'required|string|size:4'
        ]);

        $code = $request->group_code;
        
        // Find the group by referral code
        $group = Group::where('referral_code', $code)->first();
        if (!$group) {
            return redirect()->route('ukm.index')
                ->with('error', 'Kode referral tidak valid');
        }

        /** @var User $user */
        $user = Auth::user();
        
        // Check if user is already a member
        if ($user->groups()->where('group_id', $group->id)->exists()) {
            return redirect()->route('ukm.index')
                ->with('info', 'Anda sudah tergabung di UKM ini');
        }

        // Join the group
        $user->groups()->attach($group->id);
        return redirect()->route('ukm.index')
            ->with('success', 'Berhasil bergabung dengan ' . $group->name);
    }

    public function leave($code)
    {
        $group = Group::where('referral_code', $code)->firstOrFail();
        /** @var User $user */
        $user = Auth::user();
        
        if (!$user->groups()->where('group_id', $group->id)->exists()) {
            return redirect()->route('ukm.index')
                ->with('error', 'Anda tidak tergabung di UKM ini');
        }

        $user->groups()->detach($group->id);
        return redirect()->route('ukm.index')
            ->with('success', 'Berhasil keluar dari ' . $group->name);
    }

    public function chat($code)
    {
        /** @var User $user */
        $user = Auth::user();
        $group = Group::where('referral_code', $code)->firstOrFail();
        
        if (!$user->groups()->where('group_id', $group->id)->exists()) {
            return redirect()->route('ukm.index')
                ->with('error', 'Anda tidak memiliki akses ke grup ini');
        }

        // Store active group in session for chat actions
        session(['active_group_id' => $group->id]);

        return view('chat', [
            'groupName' => $group->name,
            'groupCode' => $group->referral_code, // Only used for Pusher channel, not displayed
            'groupId' => $group->id
        ]);
    }
}