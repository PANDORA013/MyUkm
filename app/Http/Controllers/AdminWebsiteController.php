<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UKM;
use App\Models\Group;
use App\Models\UserDeletion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Auth as AuthFacade;

class AdminWebsiteController extends Controller
{
    public function dashboard()
    {
        // Hitung total anggota unik di seluruh group
        $totalMembers = DB::table('group_user')->distinct('user_id')->count('user_id');
        
        // Hitung total UKM
        $totalUkms = UKM::count();
        
        // Hitung total admin grup
        $totalAdmins = User::where('role', 'admin_grup')->count();
        
        // Hitung total pengguna aktif bulan ini
        $activeUsersThisMonth = DB::table('sessions')
            ->where('last_activity', '>=', now()->subMonth())
            ->distinct('user_id')
            ->count('user_id');
            
        // Hitung total pengguna baru bulan ini
        $newUsersThisMonth = User::where('created_at', '>=', now()->startOfMonth())->count();
        
        // Hitung total akun yang sudah dihapus
        $totalDeletedAccounts = UserDeletion::count();
        
        return view('admin.dashboard', compact(
            'totalMembers', 
            'totalUkms', 
            'totalAdmins',
            'activeUsersThisMonth',
            'newUsersThisMonth',
            'totalDeletedAccounts'
        ));
    }

    /**
     * Mengubah role user menjadi admin_grup
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function jadikanAdminGrup($id)
    {
        try {
            $user = User::findOrFail($id);
            
            // Cek apakah user sudah menjadi admin_grup
            if ($user->role === 'admin_grup') {
                return back()->with('info', 'User sudah menjadi admin grup');
            }
            
            // Cek apakah user adalah admin_website
            if ($user->role === 'admin_website') {
                return back()->with('error', 'Tidak dapat mengubah role admin website');
            }
            
            $user->role = 'admin_grup';
            $user->save();
            
            return back()->with('success', 'User berhasil dijadikan admin grup');
            
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Menghapus role admin_grup dari user
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function hapusAdminGrup($id)
    {
        try {
            $user = User::findOrFail($id);
            
            // Cek apakah user adalah admin_website
            if ($user->role === 'admin_website') {
                return back()->with('error', 'Tidak dapat mengubah role admin website');
            }
            
            // Cek apakah user bukan admin_grup
            if ($user->role !== 'admin_grup') {
                return back()->with('info', 'User bukan admin grup');
            }
            
            $user->role = 'anggota';
            $user->save();
            
            return back()->with('success', 'Admin grup berhasil dihapus');
            
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Hapus akun pengguna
     */
    public function hapusAkun($id)
    {
        try {
            // Cek apakah user yang akan dihapus ada
            $user = User::findOrFail($id);
            
            // Cek role user yang login
            $currentUser = Auth::user();
            
            // Cek apakah yang menghapus adalah admin website
            if ($currentUser->role !== 'admin_website') {
                return back()->with('error', 'Anda tidak memiliki izin untuk menghapus akun');
            }
            
            // Cek apakah user yang dihapus adalah admin website lain
            if ($user->role === 'admin_website') {
                return back()->with('error', 'Tidak bisa menghapus akun admin website lain');
            }
            
            // Simpan nama untuk pesan sukses
            $userName = $user->name;
            
            // Simpan riwayat penghapusan sebelum menghapus user
            UserDeletion::create([
                'deleted_user_id' => $user->id,
                'deleted_user_name' => $user->name,
                'deleted_user_nim' => $user->nim,
                'deleted_user_email' => $user->email,
                'deleted_user_role' => $user->role,
                'deletion_reason' => 'Dihapus oleh admin website',
                'deleted_by' => $currentUser->id,
            ]);
            
            // Hapus relasi di group_user terlebih dahulu
            $user->groups()->detach();
            
            // Hapus user secara permanen (hard delete)
            $user->forceDelete();
            
            return back()->with('success', 'Anggota "' . $userName . '" berhasil dihapus permanen dari sistem.');
            
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan saat menghapus anggota: ' . $e->getMessage());
        }
    }

    /* -------------------- UKM Management -------------------- */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'nim' => 'required|string|max:32|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|string',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'nim' => $validated['nim'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
        ]);
        return back()->with('success', 'UKM berhasil ditambahkan');
    }

    public function tambahUKM(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:4|unique:ukms,code',
            'description' => 'nullable|string|max:1000'
        ]);
        $ukm = UKM::create($request->only(['name', 'code', 'description']));
        // Buat entri group agar anggota bisa join menggunakan referral_code
        Group::create([
            'name' => $ukm->name,
            'referral_code' => $ukm->code,
        ]);
        return back()->with('success', 'UKM berhasil ditambahkan');
    }

    public function keluarkanAnggota($ukmId, $userId)
    {
        try {
            $ukm = UKM::findOrFail($ukmId);
            $group = Group::where('referral_code', $ukm->code)->first();
            $user = User::find($userId);
            
            if (!$group) {
                return back()->with('error', 'Grup tidak ditemukan');
            }
            
            if (!$user) {
                return back()->with('error', 'User tidak ditemukan');
            }
            
            if (!$group->users()->where('users.id', $userId)->exists()) {
                return back()->with('error', 'Anggota tidak ditemukan dalam grup');
            }
            
            // Admin website memiliki kontrol absolut - bisa mengeluarkan siapa saja
            // Detach user from group
            $group->users()->detach($userId);
            
            // If that user was an admin_grup and no longer admin in any group, downgrade role to member
            if ($user->role === 'admin_grup' && !$user->adminGroups()->exists()) {
                $user->role = 'member';
                $user->save();
                
                // If user is currently logged in, log them out
                if (Auth::id() === $user->id) {
                    Auth::logout();
                    session()->flash('info', 'Status admin Anda telah dicabut karena Anda tidak lagi menjadi admin di grup manapun');
                    return redirect()->route('login');
                }
            }
            
            return back()->with('success', 'Anggota "' . $user->name . '" berhasil dikeluarkan dari grup');
            
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function hapusUKM($id)
    {
        try {
            $ukm = UKM::findOrFail($id);
            
            // Cari group terkait berdasarkan referral_code
            $group = Group::where('referral_code', $ukm->code)->first();
            
            if ($group) {
                // Hapus semua anggota dari group
                $group->users()->detach();
                // Hapus group
                $group->delete();
            }
            
            // Hapus UKM
            $ukm->delete();
            
            return back()->with('success', 'UKM "' . $ukm->name . '" berhasil dihapus beserta semua anggotanya.');
            
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan saat menghapus UKM: ' . $e->getMessage());
        }
    }

    public function lihatAnggota(Request $request, $id)
    {
        $ukm = UKM::findOrFail($id);
        
        // Ambil anggota melalui relasi Group agar sesuai dengan tabel pivot group_user
        $group = Group::where('referral_code', $ukm->code)->first();
        
        if (!$group) {
            $anggota = collect();
        } else {
            $query = $group->users()->withPivot(['is_admin', 'is_muted', 'created_at']);
            
            // Add search functionality
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('nim', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            }
            
            $anggota = $query->orderBy('name')->get();
        }

        return view('admin.ukm_anggota', [
            'ukm' => $ukm,
            'anggota' => $anggota
        ]);
    }

    public function editUKM($id)
    {
        $ukm = UKM::findOrFail($id);
        return view('admin.ukm_edit', compact('ukm'));
    }

    public function updateUKM(Request $request, $id)
    {
        $ukm = UKM::findOrFail($id);
        $oldCode = $ukm->code;
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:4|unique:ukms,code,' . $ukm->id,
            'description' => 'nullable|string|max:1000'
        ]);
        $ukm->update($request->only(['name', 'code', 'description']));

        // Jika kode berubah, hapus group lama
        if ($oldCode !== $ukm->code) {
            Group::where('referral_code', $oldCode)->delete();
        }

        // Sinkronkan ke table groups (buat atau perbarui)
        Group::updateOrCreate(
            ['referral_code' => $ukm->code],
            ['name' => $ukm->name]
        );
        return redirect('/admin/dashboard')->with('success', 'UKM diperbarui');
    }

    // Removed searchMember method as it's redundant with the Filter Anggota feature

    /**
     * Show the UKM memberships for a specific user.
     *
     * @param  int  $userId
     * @return \Illuminate\View\View
     */
    public function showMemberUkms($userId)
    {
        // Eager load the user with their groups and the related UKM for each group
        $user = User::with(['groups' => function($query) {
            $query->leftJoin('ukms', 'ukms.code', '=', 'groups.referral_code')
                ->select(
                    'groups.*',
                    'group_user.created_at as pivot_created_at',
                    'group_user.is_muted',
                    'ukms.name as ukm_nama',
                    'ukms.code as ukm_kode',
                    'ukms.id as ukm_id'
                )
                ->withPivot('created_at', 'is_muted');
        }])->findOrFail($userId);
        
        // For admin website, show the actual password (not recommended for production)
        $authUser = AuthFacade::user();
        if ($authUser && $authUser->role === 'admin_website') {
            try {
                // Cek dulu kolom yang ada di tabel users
                $columns = Schema::getColumnListing('users');
                $selectColumns = in_array('password_plain', $columns) 
                    ? ['password_plain', 'password'] 
                    : ['password'];
                
                $userData = DB::table('users')->where('id', $user->id)->first($selectColumns);
                
                if (isset($userData->password_plain)) {
                    $user->password_visible = $userData->password_plain;
                } elseif (isset($userData->password)) {
                    $user->password_visible = 'Password terenkripsi: ' . substr($userData->password, 0, 15) . '...';
                } else {
                    $user->password_visible = 'Tidak ada password tersimpan';
                }
                
                $user->is_admin = true;
            } catch (\Exception $e) {
                $user->password_visible = 'Tidak dapat mengambil data password';
            }
        }
        
        // Prepare the data for the view and remove duplicates by UKM code
        $ukms = $user->groups
            ->unique('ukm_kode') // Ensure unique UKMs by code
            ->map(function($group) {
                return (object)[
                    'id' => $group->id,
                    'nama' => $group->ukm_nama ?? 'UKM Tidak Ditemukan',
                    'kode' => $group->ukm_kode ?? 'N/A',
                    'pivot' => (object)[
                        'role' => $group->pivot->is_muted ? 'Muted' : ($group->pivot->is_admin ? 'Admin' : 'Anggota'),
                        'created_at' => $group->pivot->created_at,
                        'updated_at' => $group->pivot->updated_at ?? $group->pivot->created_at
                    ]
                ];
            });
        
        return view('admin.member_ukms', [
            'user' => $user,
            'ukms' => $ukms
        ]);
    }

    /**
     * Display all members
     */
    public function members(Request $request)
    {
        $query = User::with(['groups' => function($q) {
            $q->select('groups.id', 'groups.name', 'groups.referral_code');
        }]);

        // Filter berdasarkan pencarian
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('nim', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        // Filter berdasarkan role
        if ($request->has('role') && !empty($request->role)) {
            $query->where('role', $request->role);
        }

        // Filter berdasarkan UKM
        if ($request->has('ukm') && !empty($request->ukm)) {
            $query->whereHas('groups', function($q) use ($request) {
                $q->where('groups.referral_code', $request->ukm);
            });
        }

        $members = $query->paginate(20);

        // Get available UKMs for filter
        $ukms = Group::select('referral_code', 'name')->orderBy('name')->get();

        return view('admin.members.index', compact('members', 'ukms'));
    }

    /**
     * Show member details
     */
    public function showMember($id)
    {
        $member = User::with(['groups' => function($q) {
            $q->select('groups.id', 'groups.name', 'groups.referral_code');
        }])->findOrFail($id);

        return view('admin.members.show', compact('member'));
    }

    /**
     * Edit member
     */
    public function editMember($id)
    {
        $member = User::with(['groups' => function($q) {
            $q->select('groups.id', 'groups.name', 'groups.referral_code');
        }])->findOrFail($id);

        $availableGroups = Group::select('id', 'name', 'referral_code')->orderBy('name')->get();

        return view('admin.members.edit', compact('member', 'availableGroups'));
    }

    /**
     * Update member
     */
    public function updateMember(Request $request, $id)
    {
        $member = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'nim' => 'nullable|string|max:50',
            'role' => 'required|in:admin_website,admin_grup,member',
        ]);

        $member->update([
            'name' => $request->name,
            'email' => $request->email,
            'nim' => $request->nim,
            'role' => $request->role,
        ]);

        return redirect()->route('admin.member.show', $id)
            ->with('success', 'Data anggota berhasil diperbarui.');
    }

    /**
     * Display list of UKMs for admin
     */
    public function ukms(Request $request)
    {
        $query = UKM::query();

        // Add search functionality
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('code', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%');
            });
        }

        $ukms = $query->paginate(15)->withQueryString();

        // Add member count to each UKM
        $ukms->getCollection()->transform(function ($ukm) {
            $group = Group::where('referral_code', $ukm->code)->first();
            $ukm->members_count = $group ? $group->users()->count() : 0;
            return $ukm;
        });

        return view('admin.ukms.index', compact('ukms'));
    }

    /**
     * Promosikan user menjadi admin di grup tertentu
     *
     * @param int $userId
     * @param Request $request  
     * @return \Illuminate\Http\JsonResponse
     */
    public function promoteToAdminInGroup(Request $request, $userId)
    {
        try {
            $user = User::findOrFail($userId);
            $ukmId = $request->input('ukm_id');
            $ukm = UKM::findOrFail($ukmId);
            $group = Group::where('referral_code', $ukm->code)->firstOrFail();
            
            // Cek apakah user adalah anggota grup
            if (!$user->groups()->where('group_id', $group->id)->exists()) {
                return response()->json(['error' => 'User bukan anggota grup ini'], 400);
            }
            
            // Cek apakah user sudah admin di grup ini
            if ($user->isAdminInGroup($group)) {
                return response()->json(['error' => 'User sudah menjadi admin di grup ini'], 400);
            }
            
            // Don't promote admin_website to group admin
            if ($user->role === 'admin_website') {
                return response()->json(['error' => 'Admin website tidak perlu dipromosikan ke admin grup'], 400);
            }
            
            // Promosikan ke admin di grup ini
            $user->promoteToAdminInGroup($group);
            
            // If user is not admin_grup yet, promote them to admin_grup role
            if ($user->role !== 'admin_grup') {
                $user->role = 'admin_grup';
                $user->save();
            }
            
            return response()->json([
                'success' => true, 
                'message' => $user->name . ' berhasil dipromosikan menjadi admin di grup ' . $group->name
            ]);
            
        } catch (\Exception $e) {
            return response()->json(['error' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }
    
    /**
     * Turunkan user dari admin menjadi anggota biasa di grup tertentu
     *
     * @param int $userId
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function demoteFromAdminInGroup(Request $request, $userId)
    {
        try {
            $user = User::findOrFail($userId);
            $ukmId = $request->input('ukm_id');
            $ukm = UKM::findOrFail($ukmId);
            $group = Group::where('referral_code', $ukm->code)->firstOrFail();
            
            // Cek apakah user adalah admin di grup ini
            if (!$user->isAdminInGroup($group)) {
                return response()->json(['error' => 'User bukan admin di grup ini'], 400);
            }
            
            // Admin website memiliki kontrol absolut - bisa menurunkan admin terakhir
            $currentUser = Auth::user();
            if ($currentUser->role !== 'admin_website') {
                // Untuk non-admin website, cek jangan sampai admin terakhir
                $adminCount = $group->users()->wherePivot('is_admin', true)->count();
                if ($adminCount <= 1) {
                    return response()->json(['error' => 'Tidak dapat menurunkan admin terakhir'], 400);
                }
            }
            
            // Turunkan dari admin di grup ini
            $user->demoteFromAdminInGroup($group);
            
            // Jika user yang diturunkan adalah admin_grup dan tidak lagi admin di grup manapun
            if ($user->role === 'admin_grup' && !$user->adminGroups()->exists()) {
                $user->role = 'member';
                $user->save();
                
                // Jika user sedang login, logout paksa
                if (Auth::id() === $user->id) {
                    Auth::logout();
                    session()->flash('info', 'Status admin Anda telah dicabut karena Anda tidak lagi menjadi admin di grup manapun');
                }
            }
            
            return response()->json([
                'success' => true,
                'message' => $user->name . ' berhasil diturunkan dari admin di grup ' . $group->name
            ]);
            
        } catch (\Exception $e) {
            return response()->json(['error' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }
    
    /**
     * Get UKM members for a specific UKM
     */
    public function ukmMembers($ukm)
    {
        $ukm = UKM::findOrFail($ukm);
        
        // Find the group associated with this UKM
        $group = Group::where('referral_code', $ukm->code)->first();
        
        if (!$group) {
            $members = collect();
        } else {
            $members = $group->users()
                ->withPivot(['is_admin', 'is_muted', 'created_at'])
                ->orderBy('name')
                ->get();
        }
        
        return view('admin.ukm_members', [
            'ukm' => $ukm,
            'members' => $members
        ]);
    }
    
    /**
     * Search for members (for AJAX requests)
     */
    public function searchMember(Request $request)
    {
        $query = $request->get('query');
        
        if (!$query) {
            return response()->json(['users' => []]);
        }
        
        $users = User::where('name', 'like', '%' . $query . '%')
            ->orWhere('nim', 'like', '%' . $query . '%')
            ->orWhere('email', 'like', '%' . $query . '%')
            ->limit(10)
            ->get(['id', 'name', 'nim', 'email']);
            
        return response()->json(['users' => $users]);
    }
    
    /**
     * Make a user admin globally (admin_grup role)
     */
    public function makeGlobalAdmin($userId)
    {
        try {
            $user = User::findOrFail($userId);
            
            // Check if user is already admin_grup
            if ($user->role === 'admin_grup') {
                return response()->json(['error' => 'User sudah menjadi admin grup'], 400);
            }
            
            // Don't allow changing admin_website role
            if ($user->role === 'admin_website') {
                return response()->json(['error' => 'Tidak dapat mengubah role admin website'], 400);
            }
            
            $user->role = 'admin_grup';
            $user->save();
            
            return response()->json([
                'success' => true,
                'message' => $user->name . ' berhasil dijadikan admin grup'
            ]);
            
        } catch (\Exception $e) {
            return response()->json(['error' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }
    
    /**
     * Remove global admin status (change admin_grup to member)
     */
    public function removeGlobalAdmin($userId)
    {
        try {
            $user = User::findOrFail($userId);
            
            // Check if user is admin_website
            if ($user->role === 'admin_website') {
                return response()->json(['error' => 'Tidak dapat mengubah role admin website'], 400);
            }
            
            // Check if user is not admin_grup
            if ($user->role !== 'admin_grup') {
                return response()->json(['error' => 'User bukan admin grup'], 400);
            }
            
            $user->role = 'member';
            $user->save();
            
            return response()->json([
                'success' => true,
                'message' => $user->name . ' berhasil diturunkan dari admin grup'
            ]);
            
        } catch (\Exception $e) {
            return response()->json(['error' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }
    
    /**
     * Force delete a user account (for admin website absolute control)
     */
    public function forceDeleteUser($userId)
    {
        try {
            $user = User::findOrFail($userId);
            $currentUser = Auth::user();
            
            // Only admin_website can force delete
            if ($currentUser->role !== 'admin_website') {
                return response()->json(['error' => 'Tidak memiliki izin untuk menghapus akun'], 403);
            }
            
            // Cannot delete other admin_website
            if ($user->role === 'admin_website') {
                return response()->json(['error' => 'Tidak dapat menghapus admin website lain'], 400);
            }
            
            $userName = $user->name;
            
            // Save deletion history
            UserDeletion::create([
                'deleted_user_id' => $user->id,
                'deleted_user_name' => $user->name,
                'deleted_user_nim' => $user->nim,
                'deleted_user_email' => $user->email,
                'deleted_user_role' => $user->role,
                'deletion_reason' => 'Force delete by admin website',
                'deleted_by' => $currentUser->id,
            ]);
            
            // Remove all group memberships
            $user->groups()->detach();
            
            // Force delete the user
            $user->forceDelete();
            
            return response()->json([
                'success' => true,
                'message' => 'Akun ' . $userName . ' berhasil dihapus permanen'
            ]);
            
        } catch (\Exception $e) {
            return response()->json(['error' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }
    
    /**
     * Tampilkan riwayat penghapusan user
     */
    public function riwayatPenghapusan()
    {
        try {
            // Ambil semua riwayat penghapusan dengan informasi admin yang menghapus
            $deletions = UserDeletion::with('deletedBy')
                ->orderBy('created_at', 'desc')
                ->paginate(20);
            
            return view('admin.riwayat-penghapusan', compact('deletions'));
            
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    
    /**
     * Get statistics for dashboard
     */
    public function getStatistics()
    {
        $stats = [
            'total_members' => User::count(),
            'total_ukms' => UKM::count(),
            'total_admin_grup' => User::where('role', 'admin_grup')->count(),
            'active_users_today' => User::where('last_seen_at', '>=', now()->subDay())->count(),
            'new_users_this_month' => User::where('created_at', '>=', now()->startOfMonth())->count(),
            'total_deleted_accounts' => UserDeletion::count(),
        ];
        
        return response()->json($stats);
    }
}
