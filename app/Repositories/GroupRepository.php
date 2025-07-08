<?php

namespace App\Repositories;

use App\Models\Group;
use App\Models\User;
use App\Models\UKM;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

/**
 * Repository for Group model operations
 */
class GroupRepository
{
    /**
     * Find group by ID
     */
    public function find(int $id): ?Group
    {
        return Group::find($id);
    }

    /**
     * Find group by ID or fail
     */
    public function findOrFail(int $id): Group
    {
        return Group::findOrFail($id);
    }

    /**
     * Find group by referral code
     */
    public function findByReferralCode(string $code): ?Group
    {
        return Group::where('referral_code', $code)->first();
    }

    /**
     * Find group by referral code or fail
     */
    public function findByReferralCodeOrFail(string $code): Group
    {
        return Group::where('referral_code', $code)->firstOrFail();
    }

    /**
     * Get all active groups
     */
    public function getActive(): Collection
    {
        return Group::where('is_active', true)->get();
    }

    /**
     * Get groups with their members
     */
    public function getWithMembers(): Collection
    {
        return Group::with('users')->get();
    }

    /**
     * Get groups by UKM
     */
    public function getByUkm(UKM $ukm): Collection
    {
        return Group::where('ukm_id', $ukm->id)->get();
    }

    /**
     * Get groups where user is a member
     */
    public function getUserGroups(User $user): Collection
    {
        return $user->groups()->get();
    }

    /**
     * Get groups where user is admin
     */
    public function getUserAdminGroups(User $user): Collection
    {
        return $user->adminGroups()->get();
    }

    /**
     * Get available groups for user (not a member yet)
     */
    public function getAvailableForUser(User $user): Collection
    {
        $joinedGroupIds = $user->groups()->pluck('group_id');
        
        return Group::whereNotIn('id', $joinedGroupIds)
            ->where('is_active', true)
            ->get();
    }

    /**
     * Create new group
     */
    public function create(array $data): Group
    {
        return Group::create($data);
    }

    /**
     * Update group
     */
    public function update(Group $group, array $data): bool
    {
        return $group->update($data);
    }

    /**
     * Delete group
     */
    public function delete(Group $group): bool
    {
        return $group->delete();
    }

    /**
     * Add user to group
     */
    public function addUser(Group $group, User $user, bool $isAdmin = false): void
    {
        $group->users()->attach($user->id, [
            'is_admin' => $isAdmin,
            'is_muted' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Remove user from group
     */
    public function removeUser(Group $group, User $user): void
    {
        $group->users()->detach($user->id);
    }

    /**
     * Check if user is member of group
     */
    public function isUserMember(Group $group, User $user): bool
    {
        return $group->users()->where('user_id', $user->id)->exists();
    }

    /**
     * Check if user is admin of group
     */
    public function isUserAdmin(Group $group, User $user): bool
    {
        return $group->users()->where('user_id', $user->id)
            ->wherePivot('is_admin', true)->exists();
    }

    /**
     * Promote user to admin in group
     */
    public function promoteUserToAdmin(Group $group, User $user): void
    {
        $group->users()->updateExistingPivot($user->id, [
            'is_admin' => true,
            'updated_at' => now(),
        ]);
    }

    /**
     * Demote user from admin in group
     */
    public function demoteUserFromAdmin(Group $group, User $user): void
    {
        $group->users()->updateExistingPivot($user->id, [
            'is_admin' => false,
            'updated_at' => now(),
        ]);
    }

    /**
     * Get group members with pagination
     */
    public function getMembersPaginated(Group $group, ?string $search = null, int $perPage = 15): LengthAwarePaginator
    {
        $query = $group->users()->withPivot(['is_admin', 'is_muted']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('nim', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        return $query->paginate($perPage);
    }

    /**
     * Search groups
     */
    public function search(string $query, int $limit = 10): Collection
    {
        return Group::where('name', 'like', '%' . $query . '%')
            ->orWhere('referral_code', 'like', '%' . $query . '%')
            ->orWhere('description', 'like', '%' . $query . '%')
            ->limit($limit)
            ->get();
    }

    /**
     * Check if referral code exists
     */
    public function existsByReferralCode(string $code): bool
    {
        return Group::where('referral_code', $code)->exists();
    }

    /**
     * Get total number of groups
     */
    public function count(): int
    {
        return Group::count();
    }

    /**
     * Get total unique members across all groups
     */
    public function getTotalUniqueMembers(): int
    {
        return DB::table('group_user')
            ->distinct('user_id')
            ->count('user_id');
    }
}
