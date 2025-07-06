<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Group;
use App\Models\User;
use App\Services\GroupAdminService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminGrupController extends Controller
{
    public function __construct(
        private GroupAdminService $groupAdminService
    ) {}

    /**
     * Display admin grup dashboard
     */
    public function dashboard(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();
        
        $selectedGroupId = $request->input('group_id');
        $dashboardData = $this->groupAdminService->getDashboardStats($user);
        
        // If no group is selected but user has managed groups, select the first one
        if (!$selectedGroupId && $dashboardData['managed_groups']->count() > 0) {
            $selectedGroupId = $dashboardData['managed_groups']->first()->id;
        }
        
        // Get specific group details if selected
        $groupDetails = [];
        if ($selectedGroupId) {
            $result = $this->groupAdminService->getGroupDetails($user, $selectedGroupId);
            $groupDetails = [
                'selectedGroup' => $result['group'],
                'group' => $result['group'], // Add alias for backward compatibility
                'members' => $result['members'],
                'anggota' => $result['members'], // Add alias for Indonesian naming convention
                'stats' => $result['stats']
            ];
        } else {
            $groupDetails = [
                'selectedGroup' => null,
                'group' => null,
                'members' => collect(),
                'anggota' => collect(),
                'stats' => ['total_anggota' => 0, 'anggota_aktif' => 0, 'anggota_muted' => 0]
            ];
        }
        
        return view('grup.dashboard', array_merge($dashboardData, $groupDetails));
    }
    
    /**
     * Display group members
     */
    public function lihatAnggota(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();
        
        $selectedGroupId = $request->input('group_id');
        $dashboardData = $this->groupAdminService->getDashboardStats($user);
        
        // If no group is selected but user has managed groups, select the first one
        if (!$selectedGroupId && $dashboardData['managed_groups']->count() > 0) {
            $selectedGroupId = $dashboardData['managed_groups']->first()->id;
        }
        
        // Get specific group details if selected
        $groupDetails = [];
        if ($selectedGroupId) {
            $result = $this->groupAdminService->getGroupDetails($user, $selectedGroupId);
            $groupDetails = [
                'selectedGroup' => $result['group'],
                'group' => $result['group'],
                'members' => $result['members'],
                'anggota' => $result['members']
            ];
        } else {
            $groupDetails = [
                'selectedGroup' => null,
                'group' => null,
                'members' => collect(),
                'anggota' => collect()
            ];
        }
        
        return view('grup.anggota', array_merge($dashboardData, $groupDetails));
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
        
        $result = $this->groupAdminService->removeMember($user, $groupId, $id);
        
        if ($result['success']) {
            return back()->with('success', $result['message']);
        } else {
            return back()->with('error', $result['error']);
        }
    }
    
    /**
     * Mute a member in a group
     */
    public function muteAnggota(Request $request, $id)
    {
        /** @var User $user */
        $user = Auth::user();
        $groupId = $request->input('group_id');
        $duration = (int) $request->input('duration', 60);
        
        if (!$groupId) {
            return back()->with('error', 'ID grup tidak ditemukan');
        }
        
        // Validate duration is positive
        if ($duration <= 0) {
            $duration = 60; // Default to 60 minutes if invalid
        }
        
        $result = $this->groupAdminService->toggleMuteStatus($user, $groupId, $id, $duration);
        
        if ($result['success']) {
            return back()->with('success', $result['message']);
        } else {
            return back()->with('error', $result['error']);
        }
    }
    
    /**
     * Update group description
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
        
        $result = $this->groupAdminService->updateGroupDescription($user, $groupId, $description);
        
        if ($result['success']) {
            return back()->with('success', $result['message']);
        } else {
            return back()->with('error', $result['error']);
        }
    }
    
    /**
     * Manage a specific group
     */
    public function manageGroup($id)
    {
        /** @var User $user */
        $user = Auth::user();
        
        $result = $this->groupAdminService->getGroupDetails($user, $id);
        
        if (!$result['success']) {
            return back()->with('error', $result['error']);
        }
        
        return view('grup.manage', [
            'group' => $result['group'],
            'members' => $result['members'],
            'stats' => $result['stats']
        ]);
    }
    
    /**
     * Remove a member from a specific group (admin panel version)
     */
    public function removeMember($id, $userId)
    {
        /** @var User $user */
        $user = Auth::user();
        
        $result = $this->groupAdminService->removeMember($user, $id, $userId);
        
        if ($result['success']) {
            return back()->with('success', $result['message']);
        } else {
            return back()->with('error', $result['error']);
        }
    }
    
    /**
     * Mute a member in a specific group (admin panel version)
     */
    public function muteMember(Request $request, $id, $userId)
    {
        /** @var User $user */
        $user = Auth::user();
        $duration = (int) $request->input('duration', 60);
        
        // Validate duration is positive
        if ($duration <= 0) {
            $duration = 60; // Default to 60 minutes if invalid
        }
        
        $result = $this->groupAdminService->toggleMuteStatus($user, $id, $userId, $duration);
        
        if ($result['success']) {
            return back()->with('success', $result['message']);
        } else {
            return back()->with('error', $result['error']);
        }
    }

    /**
     * Display admin grup UKM index page listing all UKMs
     */
    public function index()
    {
        /** @var User $user */
        $user = Auth::user();
        
        $dashboardData = $this->groupAdminService->getDashboardStats($user);
        
        // Get other groups the user is a member of
        $memberGroups = $user->groups()
            ->whereNotIn('groups.id', $dashboardData['managed_groups']->pluck('id')->toArray())
            ->withPivot('role')
            ->get();
            
        return view('grup.ukm_index', [
            'managedGroups' => $dashboardData['managed_groups'],
            'memberGroups' => $memberGroups,
            'joinedGroups' => $memberGroups // Add alias for backward compatibility
        ]);
    }
}
