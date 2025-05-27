<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ukm;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $request->validate(['token' => 'required|string']);

        $ukm = Ukm::where('token', $request->token)->first();

        if (!$ukm) {
            return response()->json(['message' => 'Token tidak valid'], 401);
        }

        return response()->json([
            'message' => 'Login berhasil',
            'ukm' => [
                'id' => $ukm->id,
                'name' => $ukm->name,
                'token' => $ukm->token,
            ]
        ]);
    }

    public function logout(Request $request)
    {
        // Karena token statis, backend tidak menghapus apa-apa,
        // hanya balas logout berhasil.
        return response()->json(['message' => 'Logout berhasil']);
    }
}
