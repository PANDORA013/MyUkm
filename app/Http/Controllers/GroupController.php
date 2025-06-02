<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Group;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @method \App\Models\User user()
 */
class GroupController extends Controller
{
    public function showJoinGroupPage()
    {
        $groups = Group::all();
        return view('join-group', compact('groups'));
    }

    public function index()
    {
        /** @var User $user */
        $user = Auth::user();
        /** @var BelongsToMany $groups */
        $joinedGroups = $user->groups()->get();

        if ($joinedGroups->isEmpty()) {
            return redirect()->route('join.group');
        }

        return view('select-ukm', compact('joinedGroups'));
    }

    public function joinGroup(Request $request)
    {
        $request->validate([
            'group_id' => 'required|exists:groups,id',
        ]);

        /** @var User $user */
        $user = Auth::user();
        
        // Check if already joined
        $alreadyJoined = $user->groups()->where('group_id', $request->group_id)->exists();
        
        if ($alreadyJoined) {
            return redirect()->route('join.group')->with('info', 'Anda sudah tergabung di UKM ini!');
        }

        $user->groups()->attach($request->group_id);
        return redirect()->route('select-ukm')->with('success', 'Berhasil bergabung ke UKM!');
    }

    public function setActiveGroup(Request $request)
    {
        $request->validate([
            'group_id' => 'required|exists:groups,id'
        ]);

        /** @var User $user */
        $user = Auth::user();
        
        // Verify user is member of this group
        $isMember = $user->groups()->where('group_id', $request->group_id)->exists();
        
        if (!$isMember) {
            return redirect()->route('select-ukm')->with('error', 'Anda tidak tergabung di UKM ini!');
        }

        // Store active group in session
        session(['active_group' => $request->group_id]);
        
        return redirect()->route('chat');
    }
}
