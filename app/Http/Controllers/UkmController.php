<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UkmController extends Controller
{
    private $groupDefaults = [
        '0812' => ['name' => 'SIMS'],
        '0813' => ['name' => 'PSM'],
        '0814' => ['name' => 'PSHT'],
    ];

    public function index()
    {
        /** @var User $user */
        $user = Auth::user();
        $joinedGroups = $user->groups()->with('users')->get();
        $availableGroups = Group::whereNotIn('id', $joinedGroups->pluck('id'))->get();
        
        return view('ukm.index', [
            'groupDefaults' => $this->groupDefaults,
            'joinedGroups' => $joinedGroups,
            'availableGroups' => $availableGroups
        ]);
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

        return view('chat', [
            'groupName' => $group->name,
            'groupCode' => $group->referral_code // Only used for Pusher channel, not displayed
        ]);
    }
}