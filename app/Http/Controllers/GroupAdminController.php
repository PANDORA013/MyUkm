<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Routing\Controller as BaseController;

class GroupAdminController extends BaseController
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show group admin dashboard
     */
    public function dashboard($code)
    {
        /** @var User $user */
        $user = Auth::user();
        $group = Group::where('referral_code', $code)->firstOrFail();
        
        if (!$user->isAdminInGroup($group)) {
            return redirect()->route('ukm.index')
                ->with('error', 'Anda tidak memiliki akses admin di grup ini');
        }
        
        $members = $group->users()->withPivot(['is_admin', 'is_muted'])->get();
        $adminCount = $members->where('pivot.is_admin', true)->count();
        $memberCount = $members->where('pivot.is_admin', false)->count();
        
        return view('group.admin.dashboard', [
            'group' => $group,
            'members' => $members,
            'adminCount' => $adminCount,
            'memberCount' => $memberCount,
            'isGroupAdmin' => true
        ]);
    }
    
    /**
     * Show members management page
     */
    public function members($code)
    {
        /** @var User $user */
        $user = Auth::user();
        $group = Group::where('referral_code', $code)->firstOrFail();
        
        if (!$user->isAdminInGroup($group)) {
            return redirect()->route('ukm.index')
                ->with('error', 'Anda tidak memiliki akses admin di grup ini');
        }
        
        $members = $group->users()
            ->withPivot(['is_admin', 'is_muted', 'created_at'])
            ->orderBy('pivot_is_admin', 'desc')
            ->orderBy('pivot_created_at', 'asc')
            ->get();
        
        return view('group.admin.members', [
            'group' => $group,
            'members' => $members,
            'isGroupAdmin' => true
        ]);
    }
    
    /**
     * Promote member to admin
     */
    public function promoteToAdmin($code, Request $request)
    {
        /** @var User $user */
        $user = Auth::user();
        $group = Group::where('referral_code', $code)->firstOrFail();
        
        if (!$user->isAdminInGroup($group)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);
        
        $targetUser = User::findOrFail($request->user_id);
        
        // Check if target user is member of the group
        if (!$targetUser->groups()->where('group_id', $group->id)->exists()) {
            return response()->json(['error' => 'User bukan anggota grup ini'], 400);
        }
        
        // Check if user is already admin
        if ($targetUser->isAdminInGroup($group)) {
            return response()->json(['error' => 'User sudah menjadi admin di grup ini'], 400);
        }
        
        DB::transaction(function() use ($targetUser, $group) {
            $targetUser->promoteToAdminInGroup($group);
        });
        
        return response()->json([
            'success' => true,
            'message' => $targetUser->name . ' berhasil dipromosikan menjadi admin grup'
        ]);
    }
    
    /**
     * Demote admin to member
     */
    public function demoteToMember($code, Request $request)
    {
        /** @var User $user */
        $user = Auth::user();
        $group = Group::where('referral_code', $code)->firstOrFail();
        
        if (!$user->isAdminInGroup($group)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);
        
        $targetUser = User::findOrFail($request->user_id);
        
        // Prevent self-demotion if this is the only admin
        $adminCount = $group->users()->wherePivot('is_admin', true)->count();
        if ($targetUser->id === $user->id && $adminCount <= 1) {
            return response()->json(['error' => 'Tidak dapat menurunkan admin terakhir'], 400);
        }
        
        // Check if target user is admin in the group
        if (!$targetUser->isAdminInGroup($group)) {
            return response()->json(['error' => 'User bukan admin di grup ini'], 400);
        }
        
        DB::transaction(function() use ($targetUser, $group) {
            $targetUser->demoteFromAdminInGroup($group);
        });
        
        return response()->json([
            'success' => true,
            'message' => $targetUser->name . ' berhasil diturunkan menjadi anggota biasa'
        ]);
    }
    
    /**
     * Remove member from group
     */
    public function removeMember($code, Request $request)
    {
        /** @var User $user */
        $user = Auth::user();
        $group = Group::where('referral_code', $code)->firstOrFail();
        
        if (!$user->isAdminInGroup($group)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);
        
        $targetUser = User::findOrFail($request->user_id);
        
        // Prevent self-removal if this is the only admin
        if ($targetUser->id === $user->id) {
            $adminCount = $group->users()->wherePivot('is_admin', true)->count();
            if ($adminCount <= 1) {
                return response()->json(['error' => 'Admin terakhir tidak dapat mengeluarkan diri sendiri'], 400);
            }
        }
        
        // Check if target user is member of the group
        if (!$targetUser->groups()->where('group_id', $group->id)->exists()) {
            return response()->json(['error' => 'User bukan anggota grup ini'], 400);
        }
        
        DB::transaction(function() use ($targetUser, $group) {
            $targetUser->groups()->detach($group->id);
        });
        
        return response()->json([
            'success' => true,
            'message' => $targetUser->name . ' berhasil dikeluarkan dari grup'
        ]);
    }
    
    /**
     * Update group settings
     */
    public function updateSettings($code, Request $request)
    {
        /** @var User $user */
        $user = Auth::user();
        $group = Group::where('referral_code', $code)->firstOrFail();
        
        if (!$user->isAdminInGroup($group)) {
            return redirect()->route('ukm.index')
                ->with('error', 'Anda tidak memiliki akses admin di grup ini');
        }
        
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000'
        ]);
        
        DB::transaction(function() use ($group, $request) {
            $group->update([
                'name' => $request->name,
                'description' => $request->description
            ]);
        });
        
        return redirect()->route('group.admin.dashboard', $code)
            ->with('success', 'Pengaturan grup berhasil diperbarui');
    }
}
