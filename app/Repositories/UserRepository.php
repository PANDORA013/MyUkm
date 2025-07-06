<?php

namespace App\Repositories;

use App\Models\User;
use App\Models\Group;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Repository for User model operations
 */
class UserRepository
{
    /**
     * Find user by ID
     */
    public function find(int $id): ?User
    {
        return User::find($id);
    }

    /**
     * Find user by ID or fail
     */
    public function findOrFail(int $id): User
    {
        return User::findOrFail($id);
    }

    /**
     * Find user by NIM
     */
    public function findByNim(string $nim): ?User
    {
        return User::where('nim', $nim)->first();
    }

    /**
     * Find user by email
     */
    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    /**
     * Get users by role
     */
    public function getByRole(string $role): Collection
    {
        return User::where('role', $role)->get();
    }

    /**
     * Get users by role with pagination
     */
    public function getByRolePaginated(string $role, int $perPage = 15): LengthAwarePaginator
    {
        return User::where('role', $role)->paginate($perPage);
    }

    /**
     * Search users
     */
    public function search(string $query, int $limit = 10): Collection
    {
        return User::where('name', 'like', '%' . $query . '%')
            ->orWhere('nim', 'like', '%' . $query . '%')
            ->orWhere('email', 'like', '%' . $query . '%')
            ->limit($limit)
            ->get();
    }

    /**
     * Get users with pagination and search
     */
    public function getAllPaginated(?string $search = null, int $perPage = 15): LengthAwarePaginator
    {
        $query = User::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('nim', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        return $query->orderBy('name')->paginate($perPage);
    }

    /**
     * Create new user
     */
    public function create(array $data): User
    {
        return User::create($data);
    }

    /**
     * Update user
     */
    public function update(User $user, array $data): bool
    {
        return $user->update($data);
    }

    /**
     * Delete user
     */
    public function delete(User $user): bool
    {
        return $user->delete();
    }

    /**
     * Count users by role
     */
    public function countByRole(string $role): int
    {
        return User::where('role', $role)->count();
    }

    /**
     * Count new users this month
     */
    public function countNewUsersThisMonth(): int
    {
        return User::where('created_at', '>=', now()->startOfMonth())->count();
    }

    /**
     * Get users who are admin in specific group
     */
    public function getAdminsInGroup(Group $group): Collection
    {
        return $group->users()->wherePivot('is_admin', true)->get();
    }

    /**
     * Check if user exists by NIM
     */
    public function existsByNim(string $nim): bool
    {
        return User::where('nim', $nim)->exists();
    }

    /**
     * Check if user exists by email
     */
    public function existsByEmail(string $email): bool
    {
        return User::where('email', $email)->exists();
    }
}
