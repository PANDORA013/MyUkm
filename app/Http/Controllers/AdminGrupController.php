<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        $anggota = $group ? $group->users()
            ->withPivot('is_muted')
            ->where('role', '!=', 'admin_website')
            ->get() : collect();

        return view('grup.dashboard', compact('anggota'));
    }

    /**
     * Display list of group members
     * 
     * @return \Illuminate\View\View
     */
    public function lihatAnggota()
    {
        return $this->dashboard();
    }

    /**
     * Remove a member from the group
     * 
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function keluarkanAnggota($id)
    {
        $group = $this->managedGroup();
        if ($group && $group->users()->where('users.id', $id)->exists()) {
            $group->users()->detach($id);
            return back()->with('success', 'Anggota dikeluarkan');
        }
        return back()->with('error', 'Anggota tidak ditemukan');
    }

    /**
     * Mute or unmute a group member
     * 
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
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
