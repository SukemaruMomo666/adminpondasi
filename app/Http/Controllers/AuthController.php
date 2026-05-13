<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Toko;

class AuthController extends Controller
{
    // ==========================================================
    // 1. HALAMAN LOGIN (CUSTOMER)
    // ==========================================================
    public function showLogin()
    {
        $throttleKey = request()->ip();
        $sisaDetik = 0;

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $sisaDetik = RateLimiter::availableIn($throttleKey);
        }

        return view('auth.login_customer', compact('sisaDetik'));
    }

    // ==========================================================
    // 2. HALAMAN LOGIN (SELLER)
    // ==========================================================
    public function showLoginSeller()
    {
        $throttleKey = request()->ip();
        $sisaDetik = 0;

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $sisaDetik = RateLimiter::availableIn($throttleKey);
        }

        return view('auth.login_seller', compact('sisaDetik'));
    }

    // ==========================================================
    // LOGIN ADMIN
    // ==========================================================
    public function showLoginAdmin()
    {
        if (Auth::check() && Auth::user()->level === 'admin') {
            return redirect('/admin/dashboard');
        }

        return view('auth.login_admin');
    }

    public function loginAdmin(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        $loginType = filter_var($request->username, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $credentials = [
            $loginType => $request->username,
            'password' => $request->password,
            'level'    => 'admin' 
        ];

        $remember = $request->has('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();
            return redirect()->intended('/admin/dashboard');
        }

        $userExists = User::where($loginType, $request->username)->first();
        if ($userExists && $userExists->level !== 'admin') {
            return back()->with('error', 'Akses ditolak! Akun ini bukan akun Admin.')->withInput();
        }

        return back()->with('error', 'Username atau Password salah.')->withInput();
    }

    // ==========================================================
    // 3. PROSES LOGIN (UMUM: CUSTOMER)
    // ==========================================================
    public function login(Request $request)
    {
        $throttleKey = $request->ip();
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return back()->with('error', "Terlalu banyak percobaan. Coba lagi dalam $seconds detik.");
        }

        $request->validate([
            'username' => 'required', 
            'password' => 'required',
        ]);

        $inputType = filter_var($request->username, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $credentials = [
            $inputType => $request->username,
            'password' => $request->password,
        ];

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            RateLimiter::clear($throttleKey); 

            $user = Auth::user();
            
            if ($user->level === 'admin') {
                return redirect()->intended('/admin/dashboard');
            } elseif ($user->level === 'seller') {
                return redirect()->intended('/seller/dashboard');
            } else {
                return redirect()->intended('/'); 
            }
        }

        RateLimiter::hit($throttleKey); 
        return back()->with('error', 'Username atau Password salah.');
    }

    // ==========================================================
    // 3B. PROSES LOGIN KHUSUS SELLER
    // ==========================================================
    public function loginSeller(Request $request)
    {
        $throttleKey = $request->ip();
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return back()->with('error', "Terlalu banyak percobaan. Coba lagi dalam $seconds detik.");
        }

        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        $loginType = filter_var($request->username, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $credentials = [
            $loginType => $request->username,
            'password' => $request->password
        ];

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            // Pastikan yang login benar-benar seller
            if ($user->level === 'seller') {
                $request->session()->regenerate();
                RateLimiter::clear($throttleKey);
                return redirect()->route('seller.dashboard')->with('success', 'Selamat datang kembali di Pondasikita Seller Centre!');
            } else {
                Auth::logout();
                return redirect()->route('seller.login')->with('error', 'Akun Anda bukan Penjual. Silakan daftar toko terlebih dahulu.');
            }
        }

        RateLimiter::hit($throttleKey);
        return back()->with('error', 'Email/Username atau Kata Sandi salah!');
    }

    // ==========================================================
    // 4. PROSES LOGOUT
    // ==========================================================
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
    
    public function logoutSeller(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/seller/login');
    }

    // ==========================================================
    // 5. REGISTER CUSTOMER
    // ==========================================================
    public function showRegister()
    {
        if (Auth::check()) {
            return redirect()->route('home');
        }
        return view('auth.register_customer');
    }

    public function register(Request $request)
    {
        $request->validate([
            'nama'     => 'required|string|max:255',
            'email'    => 'required|email|unique:tb_user,email', 
            'username' => 'required|string|unique:tb_user,username',
            'password' => 'required|min:8|confirmed',
        ], [
            'username.unique'    => 'Nama pengguna ini sudah dipakai.',
            'email.unique'       => 'Email ini sudah terdaftar, silakan login.',
            'password.min'       => 'Kata sandi minimal harus 8 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.'
        ]);

        User::create([
            'nama'        => $request->nama,
            'email'       => $request->email,
            'username'    => $request->username,
            'password'    => Hash::make($request->password),
            'level'       => 'customer',
            'status'      => 'offline',
            'is_verified' => 1,
            'is_banned'   => 0
        ]);

        return redirect()->back()->with('success', 'Akun Anda berhasil dibuat. Silakan login untuk mulai berbelanja.');
    }

    // ==========================================================
    // 6. REGISTER SELLER (DIPERBAIKI UNTUK BITESHIP & GPS)
    // ==========================================================
    public function showRegisterSeller()
    {
        // Tidak perlu lagi manggil $provinces karena pakai fitur Live Search Biteship
        return view('auth.register_seller');
    }

    public function registerSeller(Request $request)
    {
        // Validasi diubah: Dulu minta province_id dkk, sekarang minta area_id
        $request->validate([
            'nama_pemilik' => 'required|string|max:255',
            'username'     => 'required|string|max:50|unique:tb_user,username',
            'email'        => 'required|email|unique:tb_user,email',
            'password'     => 'required|min:6',
            'telepon_toko' => 'required|numeric', 
            'nama_toko'    => 'required|string|max:100|unique:tb_toko,nama_toko',
            'alamat_toko'  => 'required|string',
            'area_id'      => 'required|string', // Kunci dari Biteship
            'latitude'     => 'nullable|string', // Kunci untuk jarak GPS toko terdekat
            'longitude'    => 'nullable|string', 
            'logo_toko'    => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ], [
            'area_id.required' => 'Wilayah kecamatan/kelurahan wajib diisi melalui pilihan otomatis.'
        ]);

        DB::beginTransaction();

        try {
            $logoPath = null;
            if ($request->hasFile('logo_toko')) {
                $file = $request->file('logo_toko');
                $filename = time() . '_' . Str::slug($request->nama_toko) . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/toko'), $filename); 
                $logoPath = $filename;
            }

            $user = User::create([
                'nama'        => $request->nama_pemilik,
                'username'    => $request->username,
                'email'       => $request->email,
                'password'    => Hash::make($request->password),
                'no_telepon'  => $request->telepon_toko, 
                'level'       => 'seller',
                'status'      => 'offline',
                'is_verified' => 1, 
                'is_banned'   => 0
            ]);

            $slug = Str::slug($request->nama_toko);
            if (Toko::where('slug', $slug)->exists()) {
                $slug .= '-' . time();
            }

            // Simpan toko dengan Area ID dan GPS
            Toko::create([
                'user_id'            => $user->id,
                'nama_toko'          => $request->nama_toko,
                'slug'               => $slug,
                'telepon_toko'       => $request->telepon_toko,
                'alamat_toko'        => $request->alamat_toko,
                'area_id'            => $request->area_id, // Masuk ke Biteship
                'latitude'           => $request->latitude, // Masuk ke GPS Maps
                'longitude'          => $request->longitude,
                'logo_toko'          => $logoPath,
                'status'             => 'active', 
                'status_operasional' => 'Buka'
            ]);

            DB::commit();

            return redirect()->route('seller.login')->with('success', 'Pendaftaran Toko Berhasil! Akun Seller Anda sudah aktif, silakan masuk ke Seller Centre.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Gagal mendaftar: ' . $e->getMessage())->withInput();
        }
    }

    // ==========================================================
    // 7. API WILAYAH (AJAX) - DIBEKUKAN / KOSONGKAN SAJA
    // (Agar jika masih ada script lama yg nyasar tidak memunculkan error 404)
    // ==========================================================
    public function getCities($provinceId)
    {
        return response()->json([]);
    }

    public function getDistricts($cityId)
    {
        return response()->json([]);
    }

    // 1. Mengarahkan user ke halaman login Google
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    // 2. Menerima balasan dari Google setelah user login
    public function handleGoogleCallback()
    {
        try {
            // Tambahan ->stateless() untuk mencegah error session di localhost
            $googleUser = Socialite::driver('google')->stateless()->user();

            // Cek apakah email ini sudah terdaftar di database
            $existingUser = \Illuminate\Support\Facades\DB::table('tb_user')
                                ->where('email', $googleUser->getEmail())
                                ->first();

            if ($existingUser) {
                // Jika sudah ada, update google_id-nya dan login
                \Illuminate\Support\Facades\DB::table('tb_user')
                    ->where('id', $existingUser->id)
                    ->update(['google_id' => $googleUser->getId()]);
                
                Auth::loginUsingId($existingUser->id);
            } else {
                // BIKIN USERNAME OTOMATIS (Ini biasanya yang bikin Crash kalau kosong)
                $baseUsername = \Illuminate\Support\Str::slug($googleUser->getName());
                $randomString = \Illuminate\Support\Str::random(4);
                $finalUsername = $baseUsername . '-' . $randomString;

                // Buatkan akun baru otomatis sbg customer
                $newUserId = \Illuminate\Support\Facades\DB::table('tb_user')->insertGetId([
                    'nama' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'username' => $finalUsername, // <-- Kolom ini wajib ada!
                    'google_id' => $googleUser->getId(),
                    'level' => 'customer',
                    'password' => Hash::make(\Illuminate\Support\Str::random(16)),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                Auth::loginUsingId($newUserId);
            }

            return redirect()->route('home')->with('success', 'Berhasil masuk menggunakan Google!');

        } catch (\Exception $e) {
            // MATIKAN REDIRECT, KITA TAMPILKAN ERROR ASLINYA DI LAYAR PUTIH!
            dd('TANGKAPAN ERROR BOS: ' . $e->getMessage());
        }
    }
}