<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UKM;
use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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

    public function showMemberUkms($userId)
    {
        $user = User::with('ukm')->findOrFail($userId);
        return view('admin.member_ukms', compact('user'));
    }
}
