<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // Menampilkan halaman login rahasia
    public function showLoginForm()
    {
        // Jika sudah login dan dia admin, langsung lempar ke dashboard
        if (Auth::check() && Auth::user()->level === 'admin') {
            return redirect()->route('admin.dashboard');
        }
        
        return view('admin.auth.login');
    }

    // Proses autentikasi
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        // Coba login
        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            // CEK STATUS BAN
            if ($user->is_banned) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                $appealLink = '<a href="https://wa.me/6285156677227" class="underline font-bold" target="_blank">Hubungi Super Admin</a>';
                return back()->with('error', "Akses ditolak. Akun administrator Anda telah ditangguhkan. Silakan {$appealLink} untuk informasi lebih lanjut.");
            }

            // Cek apakah yang login BENAR-BENAR admin
            if ($user->level === 'admin') {
                $request->session()->regenerate();
                return redirect()->route('admin.dashboard')->with('success', 'Selamat datang kembali, Komandan.');
            } else {
                // Jika user biasa nyasar kesini dan coba login, tendang dia keluar!
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return back()->with('error', 'Akses ditolak. Anda bukan Administrator.');
            }
        }

        // Jika email/password salah
        return back()->withErrors([
            'email' => 'Kredensial yang diberikan tidak cocok dengan data kami.',
        ])->onlyInput('email');
    }
}