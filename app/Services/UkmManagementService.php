<?php

namespace App\Services;

use App\Models\UKM;
use App\Models\Group;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Collection;

/**
 * Service for handling UKM management operations
 */
class UkmManagementService
{
    /**
     * Create a new UKM and its corresponding group
     * 
     * @param array<string, mixed> $ukmData
     * @return UKM
     */
    public function createUkm(array $ukmData): UKM
    {
        DB::beginTransaction();

        try {
            $ukm = UKM::create([
                'name' => $ukmData['name'],
                'code' => $ukmData['code'],
                'description' => $ukmData['description'] ?? null,
            ]);

            // Create corresponding group
            Group::create([
                'name' => $ukm->name,
                'referral_code' => $ukm->code,
                'description' => $ukm->description,
                'ukm_id' => $ukm->id,
                'is_active' => true,
            ]);

            DB::commit();

            Log::info('UKM created successfully', [
                'ukm_id' => $ukm->id,
                'ukm_name' => $ukm->name,
                'ukm_code' => $ukm->code
            ]);

            return $ukm;
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Failed to create UKM', [
                'error' => $e->getMessage(),
                'ukm_data' => $ukmData
            ]);
            
            throw $e;
        }
    }

    /**
     * Update UKM and sync with group
     * 
     * @param UKM $ukm
     * @param array<string, mixed> $ukmData
     * @return UKM
     */
    public function updateUkm(UKM $ukm, array $ukmData): UKM
    {
        DB::beginTransaction();

        try {
            $oldCode = $ukm->code;
            
            $ukm->update([
                'name' => $ukmData['name'],
                'code' => $ukmData['code'],
                'description' => $ukmData['description'] ?? $ukm->description,
            ]);

            // Update corresponding group
            $group = Group::where('referral_code', $oldCode)->first();
            if ($group) {
                $group->update([
                    'name' => $ukm->name,
                    'referral_code' => $ukm->code,
                    'description' => $ukm->description,
                ]);
            }

            DB::commit();

            Log::info('UKM updated successfully', [
                'ukm_id' => $ukm->id,
                'old_code' => $oldCode,
                'new_code' => $ukm->code
            ]);

            return $ukm->fresh();
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Failed to update UKM', [
                'ukm_id' => $ukm->id,
                'error' => $e->getMessage()
            ]);
            
            throw $e;
        }
    }

    /**
     * Delete UKM and its associated data
     * 
     * @param UKM $ukm
     * @return bool
     */
    public function deleteUkm(UKM $ukm): bool
    {
        DB::beginTransaction();

        try {
            // Find and delete corresponding group
            $group = Group::where('referral_code', $ukm->code)->first();
            
            if ($group) {
                // Remove all members from group
                $group->users()->detach();
                // Delete group
                $group->delete();
            }

            // Delete UKM
            $ukmName = $ukm->name;
            $ukm->delete();

            DB::commit();

            Log::info('UKM deleted successfully', [
                'ukm_name' => $ukmName,
                'had_group' => $group !== null
            ]);

            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Failed to delete UKM', [
                'ukm_id' => $ukm->id,
                'error' => $e->getMessage()
            ]);
            
            return false;
        }
    }

    /**
     * Get UKM members with pagination
     * 
     * @param UKM $ukm
     * @param string|null $search
     * @param int $perPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getUkmMembers(UKM $ukm, ?string $search = null, int $perPage = 15)
    {
        $group = Group::where('referral_code', $ukm->code)->first();
        
        if (!$group) {
            return collect()->paginate($perPage);
        }

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
     * Remove member from UKM
     * 
     * @param UKM $ukm
     * @param User $user
     * @return bool
     */
    public function removeMemberFromUkm(UKM $ukm, User $user): bool
    {
        $group = Group::where('referral_code', $ukm->code)->first();
        
        if (!$group) {
            return false;
        }

        if (!$user->groups()->where('group_id', $group->id)->exists()) {
            return false; // User is not a member
        }

        $user->groups()->detach($group->id);

        Log::info('Member removed from UKM', [
            'ukm_id' => $ukm->id,
            'ukm_name' => $ukm->name,
            'user_id' => $user->id,
            'user_name' => $user->name
        ]);

        return true;
    }

    /**
     * Search UKMs by name or code
     * 
     * @param string|null $search
     * @param int $perPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function searchUkms(?string $search = null, int $perPage = 15)
    {
        $query = UKM::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('code', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%');
            });
        }

        return $query->orderBy('name')->paginate($perPage);
    }

    /**
     * Find UKM by ID with proper error handling for soft deletes
     * 
     * @param int $id
     * @return array
     */
    public function findUkmById(int $id): array
    {
        try {
            // Check if UKM exists (including soft deleted)
            $ukm = UKM::withTrashed()->find($id);
            
            if (!$ukm) {
                return [
                    'success' => false,
                    'message' => 'UKM tidak ditemukan.',
                    'ukm' => null
                ];
            }
            
            if ($ukm->trashed()) {
                return [
                    'success' => false,
                    'message' => 'UKM "' . $ukm->name . '" sudah dihapus sebelumnya.',
                    'ukm' => $ukm,
                    'is_deleted' => true
                ];
            }
            
            return [
                'success' => true,
                'message' => 'UKM ditemukan.',
                'ukm' => $ukm,
                'is_deleted' => false
            ];
            
        } catch (\Exception $e) {
            Log::error('Error finding UKM', [
                'ukm_id' => $id,
                'error' => $e->getMessage()
            ]);
            
            return [
                'success' => false,
                'message' => 'Terjadi kesalahan saat mencari UKM.',
                'ukm' => null,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Delete UKM by ID with proper error handling
     * 
     * @param int $id
     * @return array
     */
    public function deleteUkmById(int $id): array
    {
        $findResult = $this->findUkmById($id);
        
        if (!$findResult['success']) {
            return $findResult;
        }
        
        if ($findResult['is_deleted']) {
            return $findResult;
        }
        
        $ukm = $findResult['ukm'];
        $success = $this->deleteUkm($ukm);
        
        if ($success) {
            return [
                'success' => true,
                'message' => 'UKM "' . $ukm->name . '" berhasil dihapus beserta semua anggotanya.'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Gagal menghapus UKM. Silakan coba lagi.'
            ];
        }
    }
}
