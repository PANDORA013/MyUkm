<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Group;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminGrupController extends Controller
{
    /**
     * Display admin grup dashboard
     */
    public function dashboard(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();
        
        // Get selected group ID from query parameter or session
        $selectedGroupId = $request->input('group_id');
        
        // Get all groups where user is admin
        $managedGroups = $user->adminGroups()->get();
        
        // If no group is selected but user has managed groups, select the first one
        if (!$selectedGroupId && $managedGroups->count() > 0) {
            $selectedGroupId = $managedGroups->first()->id;
        }
        
        // If a group is selected, get its details and members
        $selectedGroup = null;
        $members = collect();
        $stats = [
            'total_anggota' => 0,
            'anggota_aktif' => 0,
            'anggota_muted' => 0
        ];
        
        if ($selectedGroupId) {
            $selectedGroup = $managedGroups->where('id', $selectedGroupId)->first();
            
            // Make sure user is admin of this group
            if ($selectedGroup) {
                // Get group members with their roles in this group
                $members = $selectedGroup->users()
                    ->withPivot('is_admin', 'is_muted', 'muted_until', 'created_at')
                    ->orderBy('pivot_is_admin', 'desc')
                    ->get();
                
                // Calculate stats
                $stats = [
                    'total_anggota' => $members->count(),
                    'anggota_aktif' => $members->where('pivot.is_muted', false)->count(),
                    'anggota_muted' => $members->where('pivot.is_muted', true)->count()
                ];
            }
        }
        
        return view('grup.dashboard', [
            'managedGroups' => $managedGroups,
            'selectedGroup' => $selectedGroup,
            'group' => $selectedGroup, // Add alias for backward compatibility
            'members' => $members,
            'anggota' => $members, // Add alias for Indonesian naming convention
            'stats' => $stats
        ]);
    }
    
    /**
     * Display admin grup UKM index page listing all UKMs
     */
    public function index()
    {
        /** @var User $user */
        $user = Auth::user();
        
        // Get all groups where user is admin
        $managedGroups = $user->adminGroups()->get();
        
        // Get other groups the user is a member of
        $memberGroups = $user->groups()
            ->whereNotIn('groups.id', $managedGroups->pluck('id')->toArray())
            ->withPivot('role')
            ->get();
            
        return view('grup.ukm_index', [
            'managedGroups' => $managedGroups,
            'memberGroups' => $memberGroups,
            'joinedGroups' => $memberGroups // Add alias for backward compatibility
        ]);
    }
    
    /**
     * Display group members
     */
    public function lihatAnggota(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();
        
        // Get selected group ID from query parameter
        $selectedGroupId = $request->input('group_id');
        
        // Get all groups where user is admin
        $managedGroups = $user->adminGroups()->get();
        
        // If no group is selected but user has managed groups, select the first one
        if (!$selectedGroupId && $managedGroups->count() > 0) {
            $selectedGroupId = $managedGroups->first()->id;
        }
        
        // If a group is selected, get its details and members
        $selectedGroup = null;
        $members = collect();
        
        if ($selectedGroupId) {
            $selectedGroup = $managedGroups->where('id', $selectedGroupId)->first();
            
            // Make sure user is admin of this group
            if ($selectedGroup) {
                // Get group members with their roles in this group
                $members = $selectedGroup->users()
                    ->withPivot('is_admin', 'is_muted', 'muted_until', 'created_at')
                    ->orderBy('pivot_is_admin', 'desc')
                    ->get();
            }
        }
        
        return view('grup.anggota', [
            'managedGroups' => $managedGroups,
            'selectedGroup' => $selectedGroup,
            'group' => $selectedGroup, // Add alias for backward compatibility
            'members' => $members,
            'anggota' => $members // Add alias for Indonesian naming convention
        ]);
    }
    
    /**
     * Remove a member from a group
     */
    public function keluarkanAnggota(Request $request, $id)
    {
        /** @var User $user */
        $user = Auth::user();
        $groupId = $request->input('group_id');
        
        if (!$groupId) {
            return back()->with('error', 'ID grup tidak ditemukan');
        }
        
        // Make sure the user is admin of this group
        $group = $user->adminGroups()->where('groups.id', $groupId)->first();
        
        if (!$group) {
            return back()->with('error', 'Anda tidak memiliki akses ke grup ini');
        }
        
        // Don't allow removing self
        if ($id == $user->id) {
            return back()->with('error', 'Anda tidak dapat mengeluarkan diri sendiri');
        }
        
        // Get the target user
        $targetUser = User::find($id);
        
        if (!$targetUser) {
            return back()->with('error', 'User tidak ditemukan');
        }
        
        // Check if the target user is in this group
        if (!$targetUser->groups()->where('group_id', $groupId)->exists()) {
            return back()->with('error', 'User tidak tergabung dalam grup ini');
        }
        
        // Check if the target user is an admin_website (cannot be removed)
        if ($targetUser->role === 'admin_website') {
            return back()->with('error', 'Tidak dapat mengeluarkan admin website');
        }
        
        // Remove the user from group
        $targetUser->groups()->detach($groupId);
        
        // Check if user was admin_grup and if this was their last managed group
        if ($targetUser->role === 'admin_grup' && !$targetUser->adminGroups()->exists()) {
            $targetUser->role = 'member';
            $targetUser->save();
            
            // If user is currently logged in, log them out
            if (Auth::id() === $targetUser->id) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return redirect()->route('login')
                    ->with('info', 'Status admin Anda telah dicabut karena Anda tidak lagi menjadi admin di grup manapun');
            }
        }
        
        return back()->with('success', "Berhasil mengeluarkan {$targetUser->name} dari grup");
    }
    
    /**
     * Mute a member in a group
     */
    public function muteAnggota(Request $request, $id)
    {
        /** @var User $user */
        $user = Auth::user();
        $groupId = $request->input('group_id');
        $duration = (int) $request->input('duration', 60); // Convert to integer
        
        // Validate duration is positive
        if ($duration <= 0) {
            $duration = 60; // Default to 60 minutes if invalid
        }
        
        if (!$groupId) {
            return back()->with('error', 'ID grup tidak ditemukan');
        }
        
        // Make sure the user is admin of this group
        $group = $user->adminGroups()->where('groups.id', $groupId)->first();
        
        if (!$group) {
            return back()->with('error', 'Anda tidak memiliki akses ke grup ini');
        }
        
        // Don't allow muting self
        if ($id == $user->id) {
            return back()->with('error', 'Anda tidak dapat membisukan diri sendiri');
        }
        
        // Get the target user
        $targetUser = User::find($id);
        
        if (!$targetUser) {
            return back()->with('error', 'User tidak ditemukan');
        }
        
        // Check if the target user is in this group
        if (!$targetUser->groups()->where('group_id', $groupId)->exists()) {
            return back()->with('error', 'User tidak tergabung dalam grup ini');
        }
        
        // Check if the target user is an admin_website or admin_grup (cannot be muted)
        if (in_array($targetUser->role, ['admin_website', 'admin_grup'])) {
            return back()->with('error', 'Tidak dapat membisukan admin');
        }
        
        // Get current mute status
        $currentPivot = DB::table('group_user')
            ->where('user_id', $id)
            ->where('group_id', $groupId)
            ->first();
            
        if ($currentPivot && $currentPivot->is_muted) {
            // Unmute the user
            DB::table('group_user')
                ->where('user_id', $id)
                ->where('group_id', $groupId)
                ->update([
                    'is_muted' => false,
                    'muted_until' => null
                ]);
                
            return back()->with('success', "Berhasil unmute {$targetUser->name}");
        } else {
            // Mute the user
            $expiryTime = now()->addMinutes($duration);
            
            DB::table('group_user')
                ->where('user_id', $id)
                ->where('group_id', $groupId)
                ->update([
                    'is_muted' => true,
                    'muted_until' => $expiryTime
                ]);
                
            return back()->with('success', "Berhasil membisukan {$targetUser->name} untuk {$duration} menit");
        }
    }
    
    /**
     * Update the group description
     */
    public function updateDescription(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();
        $groupId = $request->input('group_id');
        $description = $request->input('description');
        
        if (!$groupId) {
            return back()->with('error', 'ID grup tidak ditemukan');
        }
        
        // Make sure the user is admin of this group
        $group = $user->adminGroups()->where('groups.id', $groupId)->first();
        
        if (!$group) {
            return back()->with('error', 'Anda tidak memiliki akses ke grup ini');
        }
        
        $group->description = $description;
        $group->save();
        
        return back()->with('success', "Deskripsi grup berhasil diperbarui");
    }
    
    /**
     * Manage a specific group
     */
    public function manageGroup($id)
    {
        /** @var User $user */
        $user = Auth::user();
        
        // Make sure the user is admin of this group
        $group = $user->adminGroups()->where('groups.id', $id)->first();
        
        if (!$group) {
            return back()->with('error', 'Anda tidak memiliki akses ke grup ini');
        }
        
        // Get group members
        $members = $group->users()
            ->withPivot('is_admin', 'is_muted', 'muted_until', 'created_at')
            ->orderBy('pivot_is_admin', 'desc')
            ->get();
            
        return view('grup.manage', [
            'group' => $group,
            'members' => $members
        ]);
    }
    
    /**
     * Remove a member from a specific group (admin panel version)
     */
    public function removeMember($id, $userId)
    {
        /** @var User $user */
        $user = Auth::user();
        
        // Make sure the user is admin of this group
        $group = $user->adminGroups()->where('groups.id', $id)->first();
        
        if (!$group) {
            return back()->with('error', 'Anda tidak memiliki akses ke grup ini');
        }
        
        // Don't allow removing self
        if ($userId == $user->id) {
            return back()->with('error', 'Anda tidak dapat mengeluarkan diri sendiri');
        }
        
        // Get the target user
        $targetUser = User::find($userId);
        
        if (!$targetUser) {
            return back()->with('error', 'User tidak ditemukan');
        }
        
        // Remove the user from group
        $targetUser->groups()->detach($id);
        
        // If target user is admin_grup and this was their last managed group, demote them
        if ($targetUser->role === 'admin_grup' && !$targetUser->adminGroups()->exists()) {
            $targetUser->role = 'member';
            $targetUser->save();
        }
        
        return back()->with('success', "{$targetUser->name} berhasil dikeluarkan dari grup");
    }
    
    /**
     * Mute a member in a specific group (admin panel version)
     */
    public function muteMember(Request $request, $id, $userId)
    {
        /** @var User $user */
        $user = Auth::user();
        $duration = (int) $request->input('duration', 60); // Convert to integer
        
        // Validate duration is positive
        if ($duration <= 0) {
            $duration = 60; // Default to 60 minutes if invalid
        }
        
        // Make sure the user is admin of this group
        $group = $user->adminGroups()->where('groups.id', $id)->first();
        
        if (!$group) {
            return back()->with('error', 'Anda tidak memiliki akses ke grup ini');
        }
        
        // Don't allow muting self
        if ($userId == $user->id) {
            return back()->with('error', 'Anda tidak dapat membisukan diri sendiri');
        }
        
        // Get the target user
        $targetUser = User::find($userId);
        
        if (!$targetUser) {
            return back()->with('error', 'User tidak ditemukan');
        }
        
        // Get current mute status
        $currentPivot = DB::table('group_user')
            ->where('user_id', $userId)
            ->where('group_id', $id)
            ->first();
            
        if ($currentPivot && $currentPivot->is_muted) {
            // Unmute the user
            DB::table('group_user')
                ->where('user_id', $userId)
                ->where('group_id', $id)
                ->update([
                    'is_muted' => false,
                    'muted_until' => null
                ]);
                
            return back()->with('success', "{$targetUser->name} berhasil di-unmute");
        } else {
            // Mute the user
            $expiryTime = now()->addMinutes($duration);
            
            DB::table('group_user')
                ->where('user_id', $userId)
                ->where('group_id', $id)
                ->update([
                    'is_muted' => true,
                    'muted_until' => $expiryTime
                ]);
                
            return back()->with('success', "{$targetUser->name} berhasil dibisukan untuk {$duration} menit");
        }
    }
}
