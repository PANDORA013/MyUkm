<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserDeletion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;
use App\Models\UserPassword;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'nim' => 'required|string|unique:users,nim',
            'password' => 'required|string|min:6|confirmed',
            'ukm_code' => 'nullable|string|exists:ukms,code',
        ], [
            'nim.unique' => 'NIM sudah terdaftar, silahkan gunakan NIM lain',
            'password.min' => 'Password minimal 6 karakter',
            'password.confirmed' => 'Password konfirmasi tidak cocok',
            'ukm_code.exists' => 'Kode UKM tidak valid',
        ]);

        // Find UKM if ukm_code is provided
        $ukmId = null;
        if ($request->ukm_code) {
            $ukm = \App\Models\UKM::where('code', $request->ukm_code)->first();
            $ukmId = $ukm ? $ukm->id : null;
        }

        $user = User::create([
            'name' => $request->name,
            'nim' => $request->nim,
            'password' => Hash::make($request->password),
            'ukm_id' => $ukmId,
            'role' => 'anggota', // Pastikan role konsisten sebagai 'anggota'
        ]);

        // Simpan password asli terenkripsi untuk admin
        UserPassword::updateOrCreate(
            ['user_id' => $user->id],
            ['password_enc' => Crypt::encryptString($request->password)]
        );

        Auth::login($user);

        return redirect()->route('home');
    }

    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'nim' => 'required|string',
            'password' => 'required|string',
        ]);

        // Cek apakah user dengan NIM ini pernah dihapus
        $isUserDeleted = UserDeletion::where('deleted_user_nim', $credentials['nim'])->exists();
        
        if ($isUserDeleted) {
            return back()->withErrors(['nim' => 'Akun ini telah dihapus oleh admin dan tidak dapat digunakan lagi.'])->onlyInput('nim');
        }

        if (Auth::attempt(['nim' => $credentials['nim'], 'password' => $credentials['password']])) {
            $request->session()->regenerate();
            $user = Auth::user();
            if ($user->role === 'admin_website') {
                return redirect('/admin/dashboard');
            }
            if ($user->role === 'admin_grup') {
                return redirect('/grup/dashboard');
            }
            return redirect()->route('home');
        }

        return back()->withErrors(['nim' => 'NIM atau password salah'])->onlyInput('nim');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
