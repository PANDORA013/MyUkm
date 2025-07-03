<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;
use App\Models\UserPassword;
use App\Models\UserDeletionHistory;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function show(): View
    {
        $user = Auth::user();
        $memberships = collect([]);
        
        // Only fetch UKM memberships if the user is not an admin_website
        if ($user->role !== 'admin_website') {
            // Ambil data keanggotaan UKM dengan query langsung
            $memberships = DB::table('group_user')
                ->join('groups', 'groups.id', '=', 'group_user.group_id')
                ->leftJoin('ukms', 'ukms.code', '=', 'groups.referral_code')
                ->where('group_user.user_id', $user->id)
                ->select([
                    'ukms.name as ukm_name',
                    'group_user.created_at as joined_at',
                    'users.last_seen_at'
                ])
                ->leftJoin('users', 'users.id', '=', 'group_user.user_id')
                ->get()
                ->map(function($item) {
                    $isOnline = $item->last_seen_at && \Carbon\Carbon::parse($item->last_seen_at)->diffInMinutes(now()) < 5;
                    
                    return (object)[
                        'ukm_name' => $item->ukm_name ?? 'UKM Tidak Ditemukan',
                        'joined_at' => $item->joined_at,
                        'is_online' => $isOnline,
                        'last_seen' => $item->last_seen_at
                    ];
                });
        }

        // Gunakan layout yang berbeda berdasarkan role user
        if ($user->role === 'admin_website' || $user->role === 'admin_grup') {
            // Admin menggunakan layout admin
            return view('profile.index', [
                'user' => $user,
                'memberships' => $memberships
            ]);
        } else {
            // User biasa menggunakan view profile dengan layout user
            return view('profile.user', [
                'user' => $user,
                'memberships' => $memberships
            ]);
        }
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        try {
            $validated = $request->validate([
                'current_password' => ['required', 'current_password'],
                'password' => ['required', 'confirmed', 'different:current_password', Password::defaults()],
            ]);

            /** @var User $user */
            $user = Auth::user();
            $user->update([
                'password' => Hash::make($validated['password'])
            ]);

            // Simpan password asli terenkripsi untuk admin
            UserPassword::updateOrCreate(
                ['user_id' => $user->id],
                ['password_enc' => Crypt::encryptString($validated['password'])]
            );

            return back()->with('success', 'Password berhasil diperbarui');
        } catch (\Exception $e) {
            Log::error('Error updating password', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);
            return back()->withErrors(['error' => 'Gagal memperbarui password. Silakan coba lagi.']);
        }
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();
        
        // Mulai transaksi database
        DB::beginTransaction();
        
        try {
            // Catat riwayat penghapusan
            UserDeletionHistory::create([
                'user_id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'nim' => $user->nim,
                'role' => $user->role,
                'deletion_reason' => 'Permintaan penghapusan akun oleh pengguna',
                'deleted_by' => $user->id, // User menghapus akun sendiri
            ]);
            
            // Hapus relasi dan data terkait
            $user->tokens()->delete();
            $user->chats()->delete();
            $user->createdChats()->delete();
            $user->groups()->detach();
            
            // Hapus data registrasi terkait jika ada
            if (method_exists($user, 'registrations')) {
                $user->registrations()->delete();
            }
            
            // Hapus data password terenkripsi jika ada
            if (method_exists($user, 'passwordEncrypted') && $user->passwordEncrypted) {
                $user->passwordEncrypted()->delete();
            }
            
            // Hapus data aktivitas terakhir jika ada
            if (method_exists($user, 'lastSeen') && $user->lastSeen) {
                $user->lastSeen()->delete();
            }
            
            // Hapus user secara permanen
            $user->forceDelete();
            
            // Commit transaksi
            DB::commit();
            
            // Logout user
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            
            return redirect()->route('login')
                ->with('status', 'Akun Anda dan semua data terkait telah berhasil dihapus. Selamat tinggal!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Error deleting account', [
                'user_id' => $request->user()?->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()->withErrors([
                'error' => 'Terjadi kesalahan saat menghapus akun. Silakan coba lagi nanti.'
            ]);
        }
    }
    
    public function updatePhoto(Request $request): RedirectResponse
    {
        try {
            $validated = $request->validate([
                'photo' => ['required', 'image', 'max:2048', 'mimes:jpeg,png,jpg']
            ]);

            /** @var User $user */
            $user = Auth::user();
            
            // Delete old photo if exists
            if ($user->photo && Storage::disk('public')->exists($user->photo)) {
                try {
                    Storage::disk('public')->delete($user->photo);
                } catch (\Exception $e) {
                    Log::warning('Failed to delete old profile photo', [
                        'user_id' => $user->id,
                        'photo_path' => $user->photo,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            // Store new photo with sanitized filename
            $fileName = time() . '_' . preg_replace('/[^a-zA-Z0-9.]/', '', $request->file('photo')->getClientOriginalName());
            $path = $request->file('photo')->storeAs('profile-photos', $fileName, 'public');
            
            if (!$path) {
                throw new \Exception('Failed to store photo');
            }

            $user->update([
                'photo' => $path
            ]);

            return back()->with('success', 'Foto profil berhasil diperbarui');
        } catch (\Exception $e) {
            Log::error('Error updating profile photo', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);
            return back()->withErrors(['photo' => 'Gagal memperbarui foto profil. Silakan coba lagi.']);
        }
    }
}
