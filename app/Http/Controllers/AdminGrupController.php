<?php

namespace App\Http\Controllers;

use App\Helpers\BroadcastHelper;
use App\Models\Group;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminGrupController extends Controller
{
    /**
     * Get the group managed by current user
     * 
     * @return Group|null
     */
    private function managedGroup(): ?Group
    {
        /** @var User $user */
        $user = Auth::user();
        return $user->groups()->first();
    }

    /**
     * Display dashboard for group management
     * 
     * @return \Illuminate\View\View
     */
    public function dashboard()
    {
        $group = $this->managedGroup();
        
        if (!$group) {
            return view('grup.dashboard', [
                'group' => null,
                'anggota' => collect(),
                'stats' => [
                    'total_anggota' => 0,
                    'anggota_aktif' => 0,
                    'anggota_muted' => 0
                ]
            ]);
        }
        
        $anggota = $group->members()
            ->withPivot('is_muted', 'created_at')
            ->orderBy('group_user.created_at', 'desc')
            ->get();

        $stats = [
            'total_anggota' => $anggota->count(),
            'anggota_aktif' => $anggota->where('pivot.is_muted', false)->count(),
            'anggota_muted' => $anggota->where('pivot.is_muted', true)->count(),
        ];

        return view('grup.dashboard', compact('group', 'anggota', 'stats'));
    }

    /**
     * Display list of group members
     * 
     * @return \Illuminate\View\View
     */
    public function lihatAnggota()
    {
        $group = $this->managedGroup();
        
        if (!$group) {
            return view('grup.anggota', [
                'group' => null,
                'anggota' => collect()
            ]);
        }
        
        $anggota = $group->members()
            ->withPivot('is_muted', 'created_at')
            ->orderBy('group_user.created_at', 'desc')
            ->get();

        return view('grup.anggota', compact('group', 'anggota'));
    }

    /**
     * Remove a member from the group
     * 
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function keluarkanAnggota($id)
    {
        try {
            $group = $this->managedGroup();
            
            if (!$group) {
                return back()->with('error', 'Anda tidak memiliki akses untuk mengelola grup.');
            }
            
            $member = $group->users()->where('users.id', $id)->first();
            
            if (!$member) {
                return back()->with('error', 'Anggota tidak ditemukan dalam grup ini.');
            }
            
            // Tidak bisa mengeluarkan admin grup lain
            if ($member->role === 'admin_grup') {
                return back()->with('error', 'Tidak dapat mengeluarkan admin grup.');
            }
            
            $group->users()->detach($id);
            
            return back()->with('success', "Anggota {$member->name} berhasil dikeluarkan dari grup.");
            
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan saat mengeluarkan anggota.');
        }
    }

    /**
     * Mute or unmute a group member
     * 
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function muteAnggota($id)
    {
        try {
            $group = $this->managedGroup();
            
            if (!$group) {
                return back()->with('error', 'Anda tidak memiliki akses untuk mengelola grup.');
            }
            
            $member = $group->users()->where('users.id', $id)->first();
            
            if (!$member) {
                return back()->with('error', 'Anggota tidak ditemukan dalam grup ini.');
            }
            
            // Tidak bisa mute admin grup lain
            if ($member->role === 'admin_grup') {
                return back()->with('error', 'Tidak dapat mute admin grup.');
            }
            
            $isMuted = (bool) $member->pivot->is_muted;
            $newMuteStatus = !$isMuted;
            
            $group->users()->updateExistingPivot($id, ['is_muted' => $newMuteStatus]);
            
            $action = $newMuteStatus ? 'dimute' : 'di-unmute';
            
            // Try to broadcast event for real-time notification using the helper
            BroadcastHelper::safeBroadcast(new \App\Events\UserMuteStatusChanged([
                'user_id' => $id,
                'name' => $member->name,
                'group_id' => $group->id,
                'group_code' => $group->referral_code,
                'is_muted' => $newMuteStatus
            ]));
            
            return back()->with('success', "Anggota {$member->name} berhasil {$action}.");
            
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan saat mengubah status mute anggota: ' . $e->getMessage());
        }
    }

    /**
     * Manage specific group
     * 
     * @param int $groupId
     * @return \Illuminate\View\View
     */
    public function manageGroup($groupId)
    {
        /** @var User $user */
        $user = Auth::user();
        
        // Cek apakah user adalah admin dari grup ini
        $group = $user->adminGroups()->where('id', $groupId)->first();
        
        if (!$group) {
            abort(403, 'Anda tidak memiliki akses untuk mengelola grup ini.');
        }
        
        $anggota = $group->users()
            ->withPivot('is_muted', 'created_at')
            ->where('role', '!=', 'admin_website')
            ->orderBy('group_user.created_at', 'desc')
            ->get();
        
        $stats = [
            'total_anggota' => $anggota->count(),
            'anggota_aktif' => $anggota->where('pivot.is_muted', false)->count(),
            'anggota_muted' => $anggota->where('pivot.is_muted', true)->count()
        ];
        
        return view('admin_grup.manage_group', [
            'group' => $group,
            'anggota' => $anggota,
            'stats' => $stats
        ]);
    }

    /**
     * Remove member from specific group
     * 
     * @param int $groupId
     * @param int $userId
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function removeMember($groupId, $userId, Request $request)
    {
        /** @var User $user */
        $user = Auth::user();
        
        // Cek apakah user adalah admin dari grup ini
        $group = $user->adminGroups()->where('id', $groupId)->first();
        
        if (!$group) {
            abort(403, 'Anda tidak memiliki akses untuk mengelola grup ini.');
        }
        
        try {
            $member = User::findOrFail($userId);
            
            // Pastikan member adalah anggota grup
            if (!$group->users()->where('user_id', $userId)->exists()) {
                return back()->with('error', 'User bukan anggota dari grup ini.');
            }
            
            // Keluarkan anggota dari grup
            $group->users()->detach($userId);
            
            return back()->with('success', "Anggota {$member->name} berhasil dikeluarkan dari grup.");
            
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan saat mengeluarkan anggota.');
        }
    }

    /**
     * Mute/unmute member in specific group
     * 
     * @param int $groupId
     * @param int $userId
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function muteMember($groupId, $userId, Request $request)
    {
        /** @var User $user */
        $user = Auth::user();
        
        // Cek apakah user adalah admin dari grup ini
        $group = $user->adminGroups()->where('id', $groupId)->first();
        
        if (!$group) {
            abort(403, 'Anda tidak memiliki akses untuk mengelola grup ini.');
        }
        
        try {
            $member = User::findOrFail($userId);
            
            // Pastikan member adalah anggota grup
            $pivot = $group->users()->where('user_id', $userId)->first();
            if (!$pivot) {
                return back()->with('error', 'User bukan anggota dari grup ini.');
            }
            
            // Toggle status mute
            $currentMuteStatus = $pivot->pivot->is_muted;
            $newMuteStatus = !$currentMuteStatus;
            
            $group->users()->updateExistingPivot($userId, ['is_muted' => $newMuteStatus]);
            
            $action = $newMuteStatus ? 'dimute' : 'di-unmute';
            
            // Try to broadcast event for real-time notification using the helper
            BroadcastHelper::safeBroadcast(new \App\Events\UserMuteStatusChanged([
                'user_id' => $userId,
                'name' => $member->name,
                'group_id' => $group->id,
                'group_code' => $group->referral_code,
                'is_muted' => $newMuteStatus
            ]));
            
            return back()->with('success', "Anggota {$member->name} berhasil {$action}.");
            
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan saat mengubah status mute anggota: ' . $e->getMessage());
        }
    }

    /**
     * Update UKM description by admin grup
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateDescription(Request $request)
    {
        $request->validate([
            'description' => 'nullable|string|max:1000',
        ]);

        $group = $this->managedGroup();
        
        if (!$group) {
            return back()->with('error', 'Anda tidak memiliki akses untuk mengedit deskripsi UKM.');
        }
        
        // Cari UKM berdasarkan kode referral yang sama dengan kode grup
        $ukm = \App\Models\UKM::where('code', $group->referral_code)->first();
        
        if (!$ukm) {
            return back()->with('error', 'UKM tidak ditemukan.');
        }
        
        // Update deskripsi UKM
        $ukm->description = $request->description;
        $ukm->save();
        
        return redirect()->route('grup.dashboard')->with('success', 'Deskripsi UKM berhasil diperbarui.');
    }
}
