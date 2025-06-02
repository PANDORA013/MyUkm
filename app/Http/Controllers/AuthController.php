<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
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
        ], [
            'nim.unique' => 'NIM sudah terdaftar, silahkan gunakan NIM lain',
            'password.min' => 'Password minimal 6 karakter',
            'password.confirmed' => 'Password konfirmasi tidak cocok',
        ]);

        $user = User::create([
            'name' => $request->name,
            'nim' => $request->nim,
            'password' => Hash::make($request->password),
        ]);

        Auth::login($user);

        return redirect()->route('ukm.index');
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

        if (Auth::attempt(['nim' => $credentials['nim'], 'password' => $credentials['password']])) {
            $request->session()->regenerate();
            return redirect()->intended(route('ukm.index'));
        }

        return back()->withErrors(['nim' => 'NIM atau password salah'])->onlyInput('nim');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
