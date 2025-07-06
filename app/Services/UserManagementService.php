<?php

namespace App\Services;

use App\Models\User;
use App\Models\Group;
use App\Models\UKM;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Collection;

/**
 * Service for handling user management operations
 */
class UserManagementService
{
    /**
     * Create a new user with validation
     * 
     * @param array<string, mixed> $userData
     * @return User
     * @throws \Exception
     */
    public function createUser(array $userData): User
    {
        try {
            $user = User::create([
                'name' => $userData['name'],
                'nim' => $userData['nim'],
                'email' => $userData['email'] ?? null,
                'password' => Hash::make($userData['password']),
                'role' => $userData['role'] ?? 'member',
                'ukm_id' => $userData['ukm_id'] ?? null,
            ]);

            Log::info('User created successfully', [
                'user_id' => $user->id,
                'name' => $user->name,
                'role' => $user->role
            ]);

            return $user;
        } catch (\Exception $e) {
            Log::error('Failed to create user', [
                'error' => $e->getMessage(),
                'user_data' => $userData
            ]);
            throw $e;
        }
    }

    /**
     * Update user information
     * 
     * @param User $user
     * @param array<string, mixed> $userData
     * @return User
     */
    public function updateUser(User $user, array $userData): User
    {
        $user->update([
            'name' => $userData['name'],
            'email' => $userData['email'] ?? $user->email,
            'nim' => $userData['nim'] ?? $user->nim,
            'role' => $userData['role'] ?? $user->role,
        ]);

        Log::info('User updated successfully', [
            'user_id' => $user->id,
            'updated_fields' => array_keys($userData)
        ]);

        return $user->fresh();
    }

    /**
     * Promote user to admin grup globally
     * 
     * @param User $user
     * @return bool
     */
    public function promoteToGlobalAdmin(User $user): bool
    {
        if ($user->role === 'admin_grup') {
            return false; // Already admin
        }

        if ($user->role === 'admin_website') {
            return false; // Cannot change admin website role
        }

        $user->update(['role' => 'admin_grup']);

        Log::info('User promoted to global admin', [
            'user_id' => $user->id,
            'user_name' => $user->name
        ]);

        return true;
    }

    /**
     * Remove global admin privileges from user
     * 
     * @param User $user
     * @return bool
     */
    public function removeGlobalAdmin(User $user): bool
    {
        if ($user->role !== 'admin_grup') {
            return false; // Not an admin grup
        }

        // Check if user is still admin in any group
        $stillAdminSomewhere = $user->adminGroups()->exists();
        
        if (!$stillAdminSomewhere) {
            $user->update(['role' => 'member']);
            
            Log::info('User demoted from global admin', [
                'user_id' => $user->id,
                'user_name' => $user->name
            ]);
            
            return true;
        }

        return false; // Still admin in some groups
    }

    /**
     * Promote user to admin in specific group
     * 
     * @param User $user
     * @param Group $group
     * @return bool
     */
    public function promoteToAdminInGroup(User $user, Group $group): bool
    {
        if (!$user->groups()->where('group_id', $group->id)->exists()) {
            return false; // Not a member
        }

        if ($user->isAdminInGroup($group)) {
            return false; // Already admin
        }

        if ($user->role === 'admin_website') {
            return false; // Admin website doesn't need group promotion
        }

        $user->promoteToAdminInGroup($group);

        // Promote to admin_grup role if not already
        if ($user->role !== 'admin_grup') {
            $user->update(['role' => 'admin_grup']);
        }

        Log::info('User promoted to admin in group', [
            'user_id' => $user->id,
            'group_id' => $group->id,
            'group_name' => $group->name
        ]);

        return true;
    }

    /**
     * Demote user from admin in specific group
     * 
     * @param User $user
     * @param Group $group
     * @return bool
     */
    public function demoteFromAdminInGroup(User $user, Group $group): bool
    {
        if (!$user->isAdminInGroup($group)) {
            return false; // Not admin in this group
        }

        $user->demoteFromAdminInGroup($group);

        // Check if user is still admin in any other group
        $stillAdminSomewhere = $user->adminGroups()->exists();
        
        if (!$stillAdminSomewhere && $user->role === 'admin_grup') {
            $user->update(['role' => 'member']);
        }

        Log::info('User demoted from admin in group', [
            'user_id' => $user->id,
            'group_id' => $group->id,
            'group_name' => $group->name,
            'still_admin_elsewhere' => $stillAdminSomewhere
        ]);

        return true;
    }

    /**
     * Search users by name, NIM, or email
     * 
     * @param string $query
     * @param int $limit
     * @return Collection<int, User>
     */
    public function searchUsers(string $query, int $limit = 10): Collection
    {
        return User::where('name', 'like', '%' . $query . '%')
            ->orWhere('nim', 'like', '%' . $query . '%')
            ->orWhere('email', 'like', '%' . $query . '%')
            ->limit($limit)
            ->get(['id', 'name', 'nim', 'email']);
    }
}
