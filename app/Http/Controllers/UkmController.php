<?php

namespace App\Http\Controllers;

use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UkmController extends Controller
{
    private $groupDefaults = [
        '0812' => ['name' => 'SIMS', 'code' => '0812'],
        '0813' => ['name' => 'PSM', 'code' => '0813'],
        '0814' => ['name' => 'PSHT', 'code' => '0814'],
    ];

    public function index()
    {
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
        
        // Check if it's one of our default groups
        $defaultGroup = $this->groupDefaults[$code] ?? null;
        
        // Find or create the group
        if ($defaultGroup) {
            $group = Group::firstOrCreate(
                ['referral_code' => $code],
                ['name' => $defaultGroup['name']]
            );
        } else {
            $group = Group::where('referral_code', $code)->first();
            if (!$group) {
                return redirect()->route('ukm.index')
                    ->with('error', 'Kode UKM tidak valid');
            }
        }

        $user = Auth::user();
        
        if ($user->groups()->where('group_id', $group->id)->exists()) {
            return redirect()->route('ukm.index')
                ->with('info', 'Anda sudah tergabung di UKM ini');
        }

        $user->groups()->attach($group->id);

        return redirect()->route('ukm.index')
            ->with('success', 'Berhasil bergabung dengan ' . $group->name);
    }

    public function leave($code)
    {
        $group = Group::where('referral_code', $code)->firstOrFail();
        $user = Auth::user();
        
        if (!$user->groups()->where('group_id', $group->id)->exists()) {
            return redirect()->route('ukm.index')
                ->with('error', 'Anda tidak tergabung di UKM ini');
        }

        $user->groups()->detach($group->id);
        return redirect()->route('ukm.index')
            ->with('success', 'Berhasil keluar dari ' . $group->name);
    }
}