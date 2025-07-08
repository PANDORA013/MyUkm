<?php

namespace App\Services;

use App\Models\Group;
use App\Models\User;
use App\Repositories\GroupRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GroupAdminService
{
    public function __construct(
        private GroupRepository $groupRepository,
        private UserRepository $userRepository
    ) {}

    /**
     * Update UKM description (for admin grup)
     *
     * @param User $admin
     * @param int $ukmId
     * @param string $description
     * @return array
     */
    public function updateUkmDescription(User $admin, int $ukmId, string $description): array
    {
        try {
            // Cek apakah admin_grup memang admin di salah satu grup UKM ini
            $isAdminInUkm = $admin->adminGroups()->where('ukm_id', $ukmId)->exists();
            if (!$isAdminInUkm) {
                throw new \Exception('Anda tidak memiliki akses admin ke UKM ini');
            }
            $ukm = \App\Models\UKM::find($ukmId);
            if (!$ukm) {
                throw new \Exception('UKM tidak ditemukan');
            }
            $ukm->description = $description;
            $ukm->save();
            Log::info('UKM description updated', [
                'admin_id' => $admin->id,
                'ukm_id' => $ukmId
            ]);
            return [
                'success' => true,
                'message' => 'Deskripsi UKM berhasil diperbarui'
            ];
        } catch (\Exception $e) {
            Log::error('Error updating UKM description', [
                'admin_id' => $admin->id,
                'ukm_id' => $ukmId,
                'error' => $e->getMessage()
            ]);
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Get dashboard statistics for admin groups
     *
     * @param User $user
     * @return array
     */
    public function getDashboardStats(User $user): array
    {
        try {
            $managedGroups = $user->adminGroups()->get();
            
            return [
                'managed_groups' => $managedGroups,
                'total_managed_groups' => $managedGroups->count(),
                'total_members_managed' => $this->getTotalMembersManaged($managedGroups),
                'recent_activity' => $this->getRecentActivity($managedGroups)
            ];
        } catch (\Exception $e) {
            Log::error('Error getting admin group dashboard stats', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            
            return [
                'managed_groups' => collect(),
                'total_managed_groups' => 0,
                'total_members_managed' => 0,
                'recent_activity' => collect()
            ];
        }
    }

    /**
     * Get group details with members and statistics
     *
     * @param User $admin
     * @param int $groupId
     * @return array
     */
    public function getGroupDetails(User $admin, int $groupId): array
    {
        try {
            $group = $admin->adminGroups()->where('groups.id', $groupId)->first();
            if (!$group) {
                throw new \Exception('Admin does not have access to this group');
            }

            // Anggota aktif (deleted_at NULL)
            $members = $group->users()
                ->withPivot('is_admin', 'is_muted', 'muted_until', 'created_at', 'deleted_at')
                ->orderBy('pivot_is_admin', 'desc')
                ->wherePivotNull('deleted_at')
                ->get();

            // Ex-anggota (deleted_at NOT NULL)
            $exMembers = $group->users()
                ->withPivot('is_admin', 'is_muted', 'muted_until', 'created_at', 'deleted_at')
                ->orderBy('pivot_is_admin', 'desc')
                ->wherePivotNotNull('deleted_at')
                ->get();

            $stats = $this->calculateGroupStats($members);

            return [
                'group' => $group,
                'members' => $members,
                'ex_members' => $exMembers,
                'stats' => $stats,
                'success' => true
            ];
        } catch (\Exception $e) {
            Log::error('Error getting group details', [
                'admin_id' => $admin->id,
                'group_id' => $groupId,
                'error' => $e->getMessage()
            ]);

            return [
                'group' => null,
                'members' => collect(),
                'ex_members' => collect(),
                'stats' => $this->getDefaultStats(),
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Remove member from group
     *
     * @param User $admin
     * @param int $groupId
     * @param int $memberId
     * @return array
     */
    public function removeMember(User $admin, int $groupId, int $memberId): array
    {
        try {
            DB::beginTransaction();

            // Validate admin access
            $group = $admin->adminGroups()->where('groups.id', $groupId)->first();
            if (!$group) {
                throw new \Exception('Admin does not have access to this group');
            }

            // Validate member
            $member = User::find($memberId);
            if (!$member) {
                throw new \Exception('Member not found');
            }

            // Prevent self-removal
            if ($memberId == $admin->id) {
                throw new \Exception('Cannot remove yourself from the group');
            }

            // Prevent removal of admin_website
            if ($member->role === 'admin_website') {
                throw new \Exception('Cannot remove admin website');
            }

            // Check if member is in the group
            if (!$member->groups()->where('group_id', $groupId)->exists()) {
                throw new \Exception('Member is not in this group');
            }

            // Remove member
            $member->groups()->detach($groupId);

            // Handle role demotion if necessary
            $this->handleRoleDemotionAfterRemoval($member);

            DB::commit();

            Log::info('Member removed from group', [
                'admin_id' => $admin->id,
                'group_id' => $groupId,
                'member_id' => $memberId,
                'member_name' => $member->name
            ]);

            return [
                'success' => true,
                'message' => "Successfully removed {$member->name} from group"
            ];

        } catch (\Exception $e) {
            DB::rollback();
            
            Log::error('Error removing member from group', [
                'admin_id' => $admin->id,
                'group_id' => $groupId,
                'member_id' => $memberId,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Toggle mute status for a member
     *
     * @param User $admin
     * @param int $groupId
     * @param int $memberId
     * @param int $duration Duration in minutes for mute
     * @return array
     */
    public function toggleMuteStatus(User $admin, int $groupId, int $memberId, int $duration = 60): array
    {
        try {
            DB::beginTransaction();

            // Validate admin access
            $group = $admin->adminGroups()->where('groups.id', $groupId)->first();
            if (!$group) {
                throw new \Exception('Admin does not have access to this group');
            }

            // Validate member
            $member = User::find($memberId);
            if (!$member) {
                throw new \Exception('Member not found');
            }

            // Prevent self-muting
            if ($memberId == $admin->id) {
                throw new \Exception('Cannot mute yourself');
            }

            // Prevent muting other admins
            if (in_array($member->role, ['admin_website', 'admin_grup'])) {
                throw new \Exception('Cannot mute administrators');
            }

            // Get current pivot data
            $currentPivot = DB::table('group_user')
                ->where('user_id', $memberId)
                ->where('group_id', $groupId)
                ->first();

            if (!$currentPivot) {
                throw new \Exception('Member is not in this group');
            }

            $result = $this->updateMuteStatus($currentPivot, $memberId, $groupId, $duration);

            DB::commit();

            Log::info('Member mute status toggled', [
                'admin_id' => $admin->id,
                'group_id' => $groupId,
                'member_id' => $memberId,
                'action' => $result['action']
            ]);

            return [
                'success' => true,
                'message' => "{$member->name} " . $result['message'],
                'action' => $result['action']
            ];

        } catch (\Exception $e) {
            DB::rollback();
            
            Log::error('Error toggling mute status', [
                'admin_id' => $admin->id,
                'group_id' => $groupId,
                'member_id' => $memberId,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Update group description
     *
     * @param User $admin
     * @param int $groupId
     * @param string $description
     * @return array
     */
    public function updateGroupDescription(User $admin, int $groupId, string $description): array
    {
        try {
            $group = $admin->adminGroups()->where('groups.id', $groupId)->first();
            
            if (!$group) {
                throw new \Exception('Admin does not have access to this group');
            }

            $group->description = $description;
            $group->save();

            Log::info('Group description updated', [
                'admin_id' => $admin->id,
                'group_id' => $groupId
            ]);

            return [
                'success' => true,
                'message' => 'Group description updated successfully'
            ];

        } catch (\Exception $e) {
            Log::error('Error updating group description', [
                'admin_id' => $admin->id,
                'group_id' => $groupId,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Calculate group statistics from members
     *
     * @param \Illuminate\Support\Collection $members
     * @return array
     */
    private function calculateGroupStats($members): array
    {
        return [
            'total_anggota' => $members->count(),
            'anggota_aktif' => $members->where('pivot.is_muted', false)->count(),
            'anggota_muted' => $members->where('pivot.is_muted', true)->count(),
            'admin_count' => $members->where('pivot.is_admin', true)->count()
        ];
    }

    /**
     * Get default statistics structure
     *
     * @return array
     */
    private function getDefaultStats(): array
    {
        return [
            'total_anggota' => 0,
            'anggota_aktif' => 0,
            'anggota_muted' => 0,
            'admin_count' => 0
        ];
    }

    /**
     * Get total members managed across all groups
     *
     * @param \Illuminate\Support\Collection $managedGroups
     * @return int
     */
    private function getTotalMembersManaged($managedGroups): int
    {
        return $managedGroups->sum(function ($group) {
            return $group->users()->count();
        });
    }

    /**
     * Get recent activity for managed groups
     *
     * @param \Illuminate\Support\Collection $managedGroups
     * @return \Illuminate\Support\Collection
     */
    private function getRecentActivity($managedGroups)
    {
        $groupIds = $managedGroups->pluck('id');
        
        return DB::table('group_user')
            ->whereIn('group_id', $groupIds)
            ->join('users', 'users.id', '=', 'group_user.user_id')
            ->join('groups', 'groups.id', '=', 'group_user.group_id')
            ->select('users.name', 'groups.name as group_name', 'group_user.created_at')
            ->orderBy('group_user.created_at', 'desc')
            ->limit(10)
            ->get();
    }

    /**
     * Handle role demotion after member removal
     *
     * @param User $member
     * @return void
     */
    private function handleRoleDemotionAfterRemoval(User $member): void
    {
        if ($member->role === 'admin_grup' && !$member->adminGroups()->exists()) {
            $member->role = 'member';
            $member->save();

            // Force logout if user is currently logged in
            if (Auth::id() === $member->id) {
                Auth::logout();
                session()->flash('info', 'Your admin status has been revoked. Please log in again.');
            }
        }
    }

    /**
     * Update mute status in database
     *
     * @param object $currentPivot
     * @param int $memberId
     * @param int $groupId
     * @param int $duration
     * @return array
     */
    private function updateMuteStatus($currentPivot, int $memberId, int $groupId, int $duration): array
    {
        if ($currentPivot->is_muted) {
            // Unmute the user
            DB::table('group_user')
                ->where('user_id', $memberId)
                ->where('group_id', $groupId)
                ->update([
                    'is_muted' => false,
                    'muted_until' => null
                ]);

            return [
                'action' => 'unmuted',
                'message' => 'has been unmuted successfully'
            ];
        } else {
            // Mute the user
            $expiryTime = now()->addMinutes($duration);
            
            DB::table('group_user')
                ->where('user_id', $memberId)
                ->where('group_id', $groupId)
                ->update([
                    'is_muted' => true,
                    'muted_until' => $expiryTime
                ]);

            return [
                'action' => 'muted',
                'message' => "has been muted for {$duration} minutes"
            ];
        }
    }
}
