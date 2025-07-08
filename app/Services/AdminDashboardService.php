<?php

namespace App\Services;

use App\Models\User;
use App\Models\UKM;
use App\Models\UserDeletion;
use Illuminate\Support\Facades\DB;

/**
 * Service for handling admin dashboard statistics and data
 */
class AdminDashboardService
{
    /**
     * Get dashboard statistics for admin panel
     * 
     * @return array<string, int>
     */
    public function getDashboardStats(): array
    {
        return [
            'total_members' => $this->getTotalMembers(),
            'total_ukms' => $this->getTotalUkms(),
            'total_admins' => $this->getTotalAdmins(),
            'active_users_this_month' => $this->getActiveUsersThisMonth(),
            'new_users_this_month' => $this->getNewUsersThisMonth(),
            'total_deleted_accounts' => $this->getTotalDeletedAccounts(),
        ];
    }

    /**
     * Get total unique members across all groups
     */
    private function getTotalMembers(): int
    {
        return DB::table('group_user')
            ->distinct('user_id')
            ->count('user_id');
    }

    /**
     * Get total number of UKMs
     */
    private function getTotalUkms(): int
    {
        return UKM::count();
    }

    /**
     * Get total number of admin grup users
     */
    private function getTotalAdmins(): int
    {
        return User::where('role', 'admin_grup')->count();
    }

    /**
     * Get count of active users this month based on session activity
     */
    private function getActiveUsersThisMonth(): int
    {
        return DB::table('sessions')
            ->where('last_activity', '>=', now()->subMonth())
            ->distinct('user_id')
            ->count('user_id');
    }

    /**
     * Get count of new users registered this month
     */
    private function getNewUsersThisMonth(): int
    {
        return User::where('created_at', '>=', now()->startOfMonth())->count();
    }

    /**
     * Get total count of deleted user accounts
     */
    private function getTotalDeletedAccounts(): int
    {
        return UserDeletion::count();
    }
}
