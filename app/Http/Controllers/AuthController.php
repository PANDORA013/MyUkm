<?php

namespace App\Http\Controllers;

use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

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

        $result = $this->authService->register($request->only([
            'name', 'nim', 'password', 'ukm_code'
        ]));

        if ($result['success']) {
            return redirect()->route('home');
        }

        return back()->withErrors(['general' => $result['message']])->withInput();
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

        $result = $this->authService->authenticateUser($credentials);

        if ($result['success']) {
            $request->session()->regenerate();
            $user = Auth::user();
            
            // Redirect based on user role
            $redirectRoute = $this->authService->getRedirectRoute($user);
            return redirect($redirectRoute);
        }

        // Handle specific error messages
        $errorField = isset($result['is_deleted']) ? 'nim' : 'nim';
        return back()->withErrors([$errorField => $result['message']])->onlyInput('nim');
    }

    public function logout(Request $request)
    {
        $result = $this->authService->logout();
        
        if ($result['success']) {
            return redirect('/');
        }
        
        // If logout failed for some reason, still redirect to home
        return redirect('/')->with('error', $result['message']);
    }
}
