<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserPassword;
use App\Models\UserDeletionHistory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

/**
 * Service class for profile management operations
 * 
 * Handles user profile updates, password changes, photo uploads, and account deletion.
 */
class ProfileService
{
    /**
     * Get user membership data
     *
     * @param User $user
     * @return \Illuminate\Support\Collection
     */
    public function getUserMemberships(User $user): \Illuminate\Support\Collection
    {
        if ($user->role === 'admin_website') {
            return collect([]);
        }

        return DB::table('group_user')
            ->join('groups', 'groups.id', '=', 'group_user.group_id')
            ->leftJoin('ukms', 'ukms.code', '=', 'groups.referral_code')
            ->where('group_user.user_id', $user->id)
            ->select([
                'ukms.name as ukm_name',
                'group_user.created_at as joined_at',
                'users.last_seen_at'
            ])
            ->leftJoin('users', 'users.id', '=', 'group_user.user_id')
            ->get()
            ->map(function($item) {
                $isOnline = $item->last_seen_at && 
                    Carbon::parse($item->last_seen_at)->diffInMinutes(now()) < 5;
                
                return (object)[
                    'ukm_name' => $item->ukm_name ?? 'UKM Tidak Ditemukan',
                    'joined_at' => $item->joined_at,
                    'is_online' => $isOnline,
                    'last_seen' => $item->last_seen_at
                ];
            });
    }

    /**
     * Update user password
     *
     * @param User $user
     * @param string $newPassword
     * @return array
     */
    public function updatePassword(User $user, string $newPassword): array
    {
        try {
            DB::beginTransaction();

            // Update user password
            $user->update([
                'password' => Hash::make($newPassword)
            ]);

            // Save encrypted password for admin reference
            UserPassword::updateOrCreate(
                ['user_id' => $user->id],
                ['password_enc' => Crypt::encryptString($newPassword)]
            );

            DB::commit();

            Log::info('Password updated successfully', [
                'user_id' => $user->id,
                'name' => $user->name
            ]);

            return [
                'success' => true,
                'message' => 'Password berhasil diperbarui'
            ];

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error updating password', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Gagal memperbarui password. Silakan coba lagi.'
            ];
        }
    }

    /**
     * Update user profile photo
     *
     * @param User $user
     * @param \Illuminate\Http\UploadedFile $photo
     * @return array
     */
    public function updatePhoto(User $user, $photo): array
    {
        try {
            DB::beginTransaction();

            // Delete old photo if exists
            if ($user->photo && Storage::disk('public')->exists($user->photo)) {
                try {
                    Storage::disk('public')->delete($user->photo);
                } catch (\Exception $e) {
                    Log::warning('Failed to delete old profile photo', [
                        'user_id' => $user->id,
                        'photo_path' => $user->photo,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            // Store new photo with sanitized filename
            $fileName = time() . '_' . preg_replace('/[^a-zA-Z0-9.]/', '', $photo->getClientOriginalName());
            $path = $photo->storeAs('profile-photos', $fileName, 'public');
            
            if (!$path) {
                throw new \Exception('Failed to store photo');
            }

            $user->update(['photo' => $path]);

            DB::commit();

            Log::info('Profile photo updated successfully', [
                'user_id' => $user->id,
                'photo_path' => $path
            ]);

            return [
                'success' => true,
                'message' => 'Foto profil berhasil diperbarui',
                'photo_url' => Storage::url($path)
            ];

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error updating profile photo', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Gagal memperbarui foto profil. Silakan coba lagi.'
            ];
        }
    }

    /**
     * Remove user profile photo
     *
     * @param User $user
     * @return array
     */
    public function removePhoto(User $user): array
    {
        try {
            DB::beginTransaction();

            // Delete photo file if exists
            if ($user->photo && Storage::disk('public')->exists($user->photo)) {
                try {
                    Storage::disk('public')->delete($user->photo);
                    Log::info('Profile photo file deleted', [
                        'user_id' => $user->id,
                        'photo_path' => $user->photo
                    ]);
                } catch (\Exception $e) {
                    Log::warning('Failed to delete profile photo file', [
                        'user_id' => $user->id,
                        'photo_path' => $user->photo,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            // Remove photo reference from database
            $user->update(['photo' => null]);

            DB::commit();

            Log::info('Profile photo removed successfully', [
                'user_id' => $user->id
            ]);

            return [
                'success' => true,
                'message' => 'Foto profil berhasil dihapus'
            ];

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error removing profile photo', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Gagal menghapus foto profil. Silakan coba lagi.'
            ];
        }
    }

    /**
     * Delete user account and all related data
     *
     * @param User $user
     * @return array
     */
    public function deleteAccount(User $user): array
    {
        try {
            DB::beginTransaction();

            // Create deletion history record
            UserDeletionHistory::create([
                'user_id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'nim' => $user->nim,
                'role' => $user->role,
                'deletion_reason' => 'Permintaan penghapusan akun oleh pengguna',
                'deleted_by' => $user->id, // User deletes own account
            ]);

            // Delete related data
            $this->deleteUserRelatedData($user);

            // Force delete the user
            $user->forceDelete();

            DB::commit();

            Log::info('User account deleted successfully', [
                'user_id' => $user->id,
                'name' => $user->name,
                'nim' => $user->nim
            ]);

            return [
                'success' => true,
                'message' => 'Akun Anda dan semua data terkait telah berhasil dihapus. Selamat tinggal!'
            ];

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error deleting account', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus akun. Silakan coba lagi nanti.'
            ];
        }
    }

    /**
     * Delete all user related data
     *
     * @param User $user
     * @return void
     */
    private function deleteUserRelatedData(User $user): void
    {
        try {
            // Delete API tokens
            if (method_exists($user, 'tokens')) {
                $user->tokens()->delete();
            }

            // Delete chat messages
            if (method_exists($user, 'chats')) {
                $user->chats()->delete();
            }

            // Delete created chats
            if (method_exists($user, 'createdChats')) {
                $user->createdChats()->delete();
            }

            // Detach from groups
            $user->groups()->detach();

            // Delete encrypted password
            UserPassword::where('user_id', $user->id)->delete();

            // Delete profile photo
            if ($user->photo && Storage::disk('public')->exists($user->photo)) {
                Storage::disk('public')->delete($user->photo);
            }

            // Delete any additional relations
            if (method_exists($user, 'registrations')) {
                $user->registrations()->delete();
            }

        } catch (\Exception $e) {
            Log::warning('Error deleting some user related data', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            // Don't throw here, we'll continue with the main deletion
        }
    }

    /**
     * Get profile view data for user
     *
     * @param User $user
     * @return array
     */
    public function getProfileViewData(User $user): array
    {
        $memberships = $this->getUserMemberships($user);

        return [
            'user' => $user,
            'memberships' => $memberships,
            'view_name' => $this->getViewNameByRole($user->role),
            'layout_data' => $this->getLayoutDataByRole($user->role)
        ];
    }

    /**
     * Get appropriate view name based on user role
     *
     * @param string $role
     * @return string
     */
    private function getViewNameByRole(string $role): string
    {
        switch ($role) {
            case 'admin_website':
                return 'profile.index';
            case 'admin_grup':
                return 'grup.profile';
            default:
                return 'profile.user';
        }
    }

    /**
     * Get layout-specific data based on user role
     *
     * @param string $role
     * @return array
     */
    private function getLayoutDataByRole(string $role): array
    {
        switch ($role) {
            case 'admin_website':
                return ['isAdminWebsite' => true];
            case 'admin_grup':
                return [];
            default:
                return [];
        }
    }
}
