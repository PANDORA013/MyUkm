<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UKM;
use App\Models\Group;
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
        return view('admin.dashboard', compact('ukms', 'totalMembers'));
    }

    public function jadikanAdminGrup(Request $request)
    {
        $user = User::findOrFail($request->user_id);
        $user->role = 'admin_grup';
        $user->save();
        return back()->with('success', 'User dijadikan admin grup');
    }

    public function hapusAdminGrup(Request $request)
    {
        $user = User::findOrFail($request->user_id);
        $user->role = 'anggota';
        $user->save();
        return back()->with('success', 'Admin grup dihapus');
    }

    /* -------------------- UKM Management -------------------- */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|string',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'password_plain' => $validated['password'], // Simpan password plain
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
            $query->with(['ukm' => function($q) {
                $q->select('id', 'nama', 'kode');
            }])
            ->select('groups.*', 'group_user.created_at as pivot_created_at', 'group_user.is_muted')
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
        
        // Prepare the data for the view, filtering out groups without a UKM
        $ukms = $user->groups->filter(function($group) {
            return $group->ukm !== null;
        })->map(function($group) {
            return (object)[
                'id' => $group->id,
                'nama' => $group->ukm->nama,
                'kode' => $group->ukm->kode,
                'pivot' => (object)[
                    'role' => $group->pivot->is_muted ? 'Muted' : 'Anggota',
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
