<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UKM;
use App\Models\Group;
use App\Models\UserDeletionHistory;
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
        // Ambil semua UKM lalu hitung jumlah anggota berdasarkan relasi Group (pivot group_user)
        $ukms = UKM::all()->map(function ($ukm) {
            $group = Group::where('referral_code', $ukm->kode)->first();
            $ukm->members_count = $group ? $group->users()->count() : 0;
            return $ukm;
        });

        // Hitung total anggota unik di seluruh group
        $totalMembers = DB::table('group_user')->distinct('user_id')->count('user_id');
        
        // Hitung total UKM
        $totalUkms = $ukms->count();
        
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
        $totalDeletedAccounts = UserDeletionHistory::count();
        
        return view('admin.dashboard', compact(
            'ukms', 
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
        
        // Hapus relasi di group_user terlebih dahulu
        $user->groups()->detach();
        
        // Hapus user
        $user->delete();
        
        return back()->with('success', 'Akun berhasil dihapus');
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
            'nama' => 'required|string|max:255',
            'kode' => 'required|string|max:4|unique:ukms,kode'
        ]);
        $ukm = UKM::create($request->only(['nama', 'kode']));
        // Buat entri group agar anggota bisa join menggunakan referral_code
        Group::create([
            'name' => $ukm->nama,
            'referral_code' => $ukm->kode,
        ]);
        return back()->with('success', 'UKM berhasil ditambahkan');
    }

    public function keluarkanAnggota($ukmId, $userId)
    {
        $ukm = UKM::findOrFail($ukmId);
        $group = Group::where('referral_code', $ukm->kode)->first();
        if ($group && $group->users()->where('users.id', $userId)->exists()) {
            // Detach user from group
            $group->users()->detach($userId);
            // If that user was an admin_grup, downgrade role to anggota
            $user = User::find($userId);
            if ($user && $user->role === 'admin_grup') {
                $user->role = 'anggota';
                $user->save();
            }
            return back()->with('success', 'Anggota dikeluarkan dari grup');
        }
        return back()->with('error', 'Anggota tidak ditemukan dalam grup');
    }

    public function hapusUKM($id)
    {
        $ukm = UKM::findOrFail($id);
        // Reset anggota UKM
        User::where('ukm_id', $ukm->id)->update(['ukm_id' => null, 'role' => 'anggota']);
        // Hapus group terkait
        Group::where('referral_code', $ukm->kode)->delete();
        $ukm->delete();
        return back()->with('success', 'UKM berhasil dihapus');
    }

    public function lihatAnggota($id)
    {
        $ukm = UKM::findOrFail($id);
        // Ambil anggota melalui relasi Group agar sesuai dengan tabel pivot group_user
        $group = Group::where('referral_code', $ukm->kode)->first();
        $anggota = $group ? $group->users()->get() : collect();

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
        $oldCode = $ukm->kode;
        $request->validate([
            'nama' => 'required|string|max:255',
            'kode' => 'required|string|max:4|unique:ukms,kode,' . $ukm->id
        ]);
        $ukm->update($request->only(['nama', 'kode']));

        // Jika kode berubah, hapus group lama
        if ($oldCode !== $ukm->kode) {
            Group::where('referral_code', $oldCode)->delete();
        }

        // Sinkronkan ke table groups (buat atau perbarui)
        Group::updateOrCreate(
            ['referral_code' => $ukm->kode],
            ['name' => $ukm->nama]
        );
        return redirect('/admin/dashboard')->with('success', 'UKM diperbarui');
    }

    public function searchMember(Request $request)
    {
        $query = $request->input('q');
        
        $users = User::where(function($q) use ($query) {
                $q->where('name', 'LIKE', "%$query%")
                  ->orWhere('nim', 'LIKE', "%$query%");
            })
            ->where('role', '!=', 'admin_website') // Exclude admin website users
            ->orderBy('name')
            ->paginate(10);
        
        return view('admin.member_search', [
            'users' => $users,
            'query' => $query
        ]);
    }

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
            $query->leftJoin('ukms', 'ukms.kode', '=', 'groups.referral_code')
                ->select(
                    'groups.*',
                    'group_user.created_at as pivot_created_at',
                    'group_user.is_muted',
                    'ukms.nama as ukm_nama',
                    'ukms.kode as ukm_kode',
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
}
