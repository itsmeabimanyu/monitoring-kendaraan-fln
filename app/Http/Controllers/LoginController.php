<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    // Tampilkan form login
    public function index()
    {
        return view('login');
    }

    // Proses login
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => ['required'],
            'password' => ['required'],
        ]);

        $remember = $request->filled('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            // Cek role/jabatan pengguna
            $user = Auth::user();
            $role_GA = ['Admin GA', 'Staff GA'];
            if (in_array($user->jabatan, $role_GA)) {
                return redirect('/admin');
            } elseif ($user->jabatan == 'Security' || $user->jabatan == 'Admin GA' || $user->jabatan == 'Staff GA') {
                return redirect('/kendaraan');
            }

            return redirect('/login')->withErrors(['username' => 'Akses tidak diizinkan.']);
        }

        return back()->withErrors([
            'username' => 'Username atau password salah.',
        ])->onlyInput('username');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Tambahkan header untuk mencegah caching halaman login
        return redirect('/kendaraan')
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache')
            ->header('Expires', 'Sat, 01 Jan 2000 00:00:00 GMT');
    }
}
