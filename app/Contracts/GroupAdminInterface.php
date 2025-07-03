<?php

namespace App\Contracts;

use App\Models\Group;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

interface GroupAdminInterface
{
    /**
     * Check if user is admin in a specific group
     *
     * @param Group|int $group
     * @return bool
     */
    public function isAdminInGroup($group): bool;

    /**
     * Check if user is member (not admin) in a specific group
     *
     * @param Group|int $group
     * @return bool
     */
    public function isMemberInGroup($group): bool;

    /**
     * Get user's role in a specific group (admin or member)
     *
     * @param Group|int $group
     * @return string|null 'admin', 'member', or null if not in group
     */
    public function getRoleInGroup($group): ?string;

    /**
     * Promote user to admin in a specific group
     *
     * @param Group|int $group
     * @return bool
     */
    public function promoteToAdminInGroup($group): bool;

    /**
     * Demote user from admin to member in a specific group
     *
     * @param Group|int $group
     * @return bool
     */
    public function demoteFromAdminInGroup($group): bool;

    /**
     * Get groups where the user is an admin
     *
     * @return BelongsToMany
     */
    public function adminGroups(): BelongsToMany;
}
