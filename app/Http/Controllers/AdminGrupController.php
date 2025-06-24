<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminGrupController extends Controller
{
    private function managedGroup(): ?Group
    {
        return Auth::user()->groups()->first();
    }

    public function dashboard()
    {
        $group = $this->managedGroup();
        $anggota = $group ? $group->users()
            ->withPivot('is_muted')
            ->where('role', '!=', 'admin_website')
            ->get() : collect();

        return view('grup.dashboard', compact('anggota'));
    }

    public function lihatAnggota()
    {
        return $this->dashboard();
    }

    public function keluarkanAnggota($id)
    {
        $group = $this->managedGroup();
        if ($group && $group->users()->where('users.id', $id)->exists()) {
            $group->users()->detach($id);
            return back()->with('success', 'Anggota dikeluarkan');
        }
        return back()->with('error', 'Anggota tidak ditemukan');
    }

    public function muteAnggota($id)
    {
        $group = $this->managedGroup();
        if ($group && $group->users()->where('users.id', $id)->exists()) {
            $pivotData = $group->users()->where('users.id', $id)->first()->pivot;
            $isMuted   = (bool) $pivotData->is_muted;
            $group->users()->updateExistingPivot($id, ['is_muted' => !$isMuted]);
            return back()->with('success', $isMuted ? 'Anggota di-unmute' : 'Anggota dimute');
        }
        return back()->with('error', 'Anggota tidak ditemukan');
    }
}
