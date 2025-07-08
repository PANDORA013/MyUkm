<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class UkmController extends Controller
{
    /**
     * Display list of UKMs
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        /** @var User $user */
        $user = Auth::user();
        $joinedGroups = $user->groups()->with('members')->get();
        $availableGroups = Group::with('members')->whereNotIn('id', $joinedGroups->pluck('id'))->get();
        
        // Tambahkan informasi role per grup untuk setiap grup yang diikuti
        foreach ($joinedGroups as $group) {
            $group->userRoleInGroup = $user->getRoleInGroup($group);
            $group->isUserAdminInGroup = $user->isAdminInGroup($group);
        }
        
        // Berdasarkan role user, tampilkan view yang sesuai
        if ($user->role === 'admin_website') {
            return view('admin.ukms.index', [
                'joinedGroups' => $joinedGroups,
                'availableGroups' => $availableGroups,
                'userUkm' => $user->ukm_id ? Group::find($user->ukm_id) : null,
                'isAdminWebsite' => true
            ]);
        } else if ($user->role === 'admin_grup') {
            // Admin grup menggunakan view khusus dengan layout admin_grup
            return view('grup.ukm_index', [
                'joinedGroups' => $joinedGroups,
                'availableGroups' => $availableGroups,
                'userUkm' => $user->ukm_id ? Group::find($user->ukm_id) : null,
                'isAdminGrup' => true,
                'managedGroups' => $user->adminGroups()->get()
            ]);
        } else {
            // Untuk user biasa (anggota), gunakan view khusus user
            return view('ukm.user_index', [
                'joinedGroups' => $joinedGroups,
                'availableGroups' => $availableGroups,
                'userUkm' => $user->ukm_id ? Group::find($user->ukm_id) : null
            ]);
        }
    }

    public function join(Request $request)
    {
        $request->validate([
            'group_code' => 'required|numeric|digits:4' // 4 digit angka
        ]);

        $code = $request->group_code;
        
        // Find the group by referral code
        $group = Group::where('referral_code', $code)->first();
        if (!$group) {
            return redirect()->route('ukm.index')
                ->with('error', 'Kode referral tidak valid');
        }

        /** @var User $user */
        $user = Auth::user();
        

        // Cek apakah sudah ada baris di group_user (baik aktif maupun soft deleted)
        $pivot = DB::table('group_user')
            ->where('group_id', $group->id)
            ->where('user_id', $user->id)
            ->first();

        if ($pivot) {
            // Jika sudah ada baris (apapun statusnya), lakukan restore (update deleted_at = null)
            DB::table('group_user')
                ->where('group_id', $group->id)
                ->where('user_id', $user->id)
                ->update(['deleted_at' => null, 'updated_at' => now()]);
        } else {
            // Jika belum ada baris sama sekali, insert baru
            $user->groups()->attach($group->id);
        }

        return redirect()->route('ukm.index')
            ->with('success', 'Berhasil bergabung dengan ' . $group->name);
    }

    public function leave($code)
    {
        $group = Group::where('referral_code', $code)->firstOrFail();
        /** @var User $user */
        $user = Auth::user();
        
        if (!$user->groups()->where('group_id', $group->id)->exists()) {
            return redirect()->route('ukm.index')
                ->with('error', 'Anda tidak tergabung di UKM ini');
        }
        
        // Check if user is admin in this group
        $isAdminInGroup = $user->isAdminInGroup($group);
        
        // Remove user from group
        $user->groups()->detach($group->id);
        
        // Store group name for message
        $groupName = $group->name;
        
        // If user is admin_grup and was admin in this group
        if ($user->role === 'admin_grup' && $isAdminInGroup) {
            // Check if user is still admin in any other group
            $stillAdminSomewhere = $user->adminGroups()->exists();
            
            // If not admin in any other group, demote user to regular member
            if (!$stillAdminSomewhere) {
                $user->role = 'member';
                $user->save();
                
                // Also logout to force re-login with new role
                Auth::logout();
                session()->flash('success', 'Anda telah keluar dari grup ' . $groupName . ' dan tidak lagi menjadi admin. Silakan login kembali.');
                return redirect()->route('login');
            }
        }
        
        // Invalidate any sessions or caches related to this group for this user
        session()->forget('group_' . $group->id);
        
        return redirect()->route('ukm.index')
            ->with('success', 'Berhasil keluar dari ' . $groupName);
    }

    public function chat($code)
    {
        /** @var User $user */
        $user = Auth::user();
        $group = Group::where('referral_code', $code)->firstOrFail();
        
        if (!$user->groups()->where('group_id', $group->id)->exists()) {
            return redirect()->route('ukm.index')
                ->with('error', 'Anda tidak memiliki akses ke grup ini');
        }

        // Store active group in session for chat actions
        session(['active_group_id' => $group->id]);

        // Pastikan view chat.blade.php diakses dari resources/views/
        return view('chat', [
            'groupName' => $group->name,
            'groupCode' => $group->referral_code, // Only used for Pusher channel, not displayed
            'groupId' => $group->id
        ]);
    }

    /**
     * Display specific UKM details
     *
     * @param string $code
     * @return \Illuminate\View\View
     */
    public function show($code)
    {
        /** @var User $user */
        $user = Auth::user();
        $group = Group::where('referral_code', $code)->firstOrFail();
        
        $isMember = $user->groups()->where('group_id', $group->id)->exists();
        $isGroupAdmin = $isMember && $user->isAdminInGroup($group);
        $userRoleInGroup = $user->getRoleInGroup($group);
        
        // Ambil members dengan pivot data untuk menampilkan role per grup
        $members = $group->users()->withPivot(['is_admin', 'is_muted'])->get();
        
        return view('ukm.show', [
            'group' => $group,
            'isMember' => $isMember,
            'isGroupAdmin' => $isGroupAdmin,
            'userRoleInGroup' => $userRoleInGroup,
            'members' => $members
        ]);
    }
}