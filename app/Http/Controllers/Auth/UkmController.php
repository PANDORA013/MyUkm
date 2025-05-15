<?php

namespace App\Http\Controllers;

use App\Models\Ukm;
use Illuminate\Http\Request;

class UkmController extends Controller
{
    /**
     * Menampilkan halaman form join UKM.
     */
    public function showJoinForm()
    {
        return view('ukms.join'); // Pastikan file view 'ukms/join.blade.php' ada
    }

    /**
     * Memproses penggabungan user ke UKM berdasarkan kode.
     */
    public function join(Request $request)
    {
        $request->validate([
            'kode_ukm' => 'required|exists:ukms,kode',
        ]);

        $ukm = Ukm::where('kode', $request->kode_ukm)->first();

        // Cek apakah user sudah tergabung ke UKM
        if (auth()->user()->ukms->contains($ukm->id)) {
            return back()->with('error', 'Kamu sudah tergabung di UKM ini.');
        }

        // Tambahkan user ke UKM
        auth()->user()->ukms()->attach($ukm->id);

        return back()->with('success', 'Berhasil bergabung ke UKM!');
    }
}
