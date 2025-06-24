<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;
use App\Models\UserPassword;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function show(): View
    {
        return view('profile.index', [
            'user' => Auth::user()
        ]);
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
