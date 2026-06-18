<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // 1. TAMPILKAN HALAMAN UTAMA & STATISTIK
    public function index(Request $request)
    {
        $limit = 10;
        $level_filter = $request->get('level', 'semua');
        $search = $request->get('search');

        $stats = [
            'total'    => User::where('level', '!=', 'bot')->count(),
            'admin'    => User::where('level', 'admin')->count(),
            'seller'   => User::where('level', 'seller')->count(),
            'customer' => User::where('level', 'customer')->count(),
            'banned'   => User::where('is_banned', true)->count(),
        ];

        $query = User::query()->where('level', '!=', 'bot');

        if ($level_filter !== 'semua') {
            $query->where('level', $level_filter);
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nama', 'LIKE', "%$search%")
                  ->orWhere('username', 'LIKE', "%$search%")
                  ->orWhere('email', 'LIKE', "%$search%");
            });
        }

        $users = $query->latest()->paginate($limit)->withQueryString();

        return view('admin.users.index', compact('users', 'level_filter', 'search', 'stats'));
    }

    // 2. TAMBAH ADMIN BARU (KHUSUS SUPER ADMIN)
    public function store(Request $request)
    {
        // Proteksi Lapis 2: Tolak jika yang memaksa bukan Super Admin
        if (Auth::user()->admin_role !== 'super') {
            return back()->with('error', 'Akses Ditolak! Hanya Super Admin yang diizinkan menambah sistem administrator.');
        }

        // Validasi input
        $request->validate([
            'nama' => 'required|string|max:255',
            'username' => 'required|string|max:50|unique:tb_user,username',
            'email' => 'required|email|unique:tb_user,email',
            'password' => 'required|min:6',
            'admin_role' => 'required|in:super,finance,cs'
        ], [
            'username.unique' => 'Username ini sudah dipakai, silakan cari yang lain.',
            'email.unique' => 'Email ini sudah terdaftar di sistem.'
        ]);

        // Simpan Admin Baru ke Database
        User::create([
            'nama' => $request->nama,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password), // Password di enkripsi
            'level' => 'admin',
            'admin_role' => $request->admin_role,
            'is_verified' => 1, // Langsung verified karena dibuat oleh pusat
            'status' => 'offline',
            'status_online' => 'offline'
        ]);

        return back()->with('success', 'Berhasil! Akses Administrator Baru (' . strtoupper($request->admin_role) . ') telah ditambahkan ke sistem.');
    }

    // 3. EDIT PENGGUNA TERINTEGRASI
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // Aturan Validasi Dasar
        $rules = [
            'nama' => 'required|string|max:255',
            'email' => 'required|email|unique:tb_user,email,' . $id,
            'no_telepon' => 'nullable|string|max:20',
        ];

        // Otoritas Ganda: Jika yang diedit adalah admin, dan yang mengedit adalah Super Admin
        if ($user->level === 'admin' && Auth::user()->admin_role === 'super') {
            $rules['admin_role'] = 'required|in:super,finance,cs';
        }

        // Cek jika kolom password diisi (artinya mau ganti password)
        if ($request->filled('password')) {
            $rules['password'] = 'min:6';
        }

        $request->validate($rules);

        // Eksekusi Pembaruan Data
        $user->nama = $request->nama;
        $user->email = $request->email;
        $user->no_telepon = $request->no_telepon;

        // Update role admin (Hanya berlaku jika syarat otoritas terpenuhi)
        if ($user->level === 'admin' && Auth::user()->admin_role === 'super' && $request->has('admin_role')) {
            $user->admin_role = $request->admin_role;
        }

        // Update password jika diisi
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return back()->with('success', "Sempurna! Data profil pengguna {$user->nama} berhasil diperbarui.");
    }

    // 4. BLOKIR ATAU AKTIFKAN AKUN PENGGUNA (DITINGKATKAN)
    public function toggleBan(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        if ($user->id === Auth::id()) {
            return back()->with('error', 'Keamanan Sistem: Anda tidak diizinkan memblokir akun Anda sendiri!');
        }

        // Jika user saat ini sudah banned, maka aksinya adalah UNBAN (Aktifkan Kembali)
        if ($user->is_banned) {
            $user->is_banned = false;
            $user->ban_type = 'none';
            $user->ban_reason = null;
            $user->banned_until = null;
            $user->save();

            if ($user->level === 'seller') {
                \Illuminate\Support\Facades\DB::table('tb_toko')
                    ->where('user_id', $user->id)
                    ->update(['status' => 'active']);
            }

            $user->notify(new \App\Notifications\UserStatusNotification([
                'title'   => "Akun Anda DIAKTIFKAN KEMBALI",
                'message' => "Selamat! Akun Anda telah diaktifkan kembali. Anda dapat melanjutkan aktivitas Anda di Pondasikita.",
                'url'     => $user->level === 'seller' ? route('seller.dashboard') : route('profil.index'),
                'icon'    => 'mdi-account-check',
                'color'   => 'emerald'
            ]));

            return back()->with('success', "Status Diperbarui: Pengguna {$user->nama} telah diaktifkan kembali.");
        }

        // Jika user belum banned, maka proses BAN (Blokir)
        $request->validate([
            'ban_type' => 'required|in:ringan,berat',
            'ban_reason' => 'required|string|max:500',
            'banned_until' => 'nullable|date|after:now',
        ]);

        $user->is_banned = true;
        $user->ban_type = $request->ban_type;
        $user->ban_reason = $request->ban_reason;
        $user->banned_until = $request->banned_until;
        $user->save();

        // JIKA USER ADALAH SELLER, UPDATE JUGA STATUS TOKONYA
        if ($user->level === 'seller') {
            $tokoStatus = 'suspended'; // Default suspended for both types of ban for now
            \Illuminate\Support\Facades\DB::table('tb_toko')
                ->where('user_id', $user->id)
                ->update(['status' => $tokoStatus]);
        }

        // KIRIM NOTIFIKASI KE USER
        $banTypeText = $request->ban_type === 'berat' ? 'PERMANEN/BERAT' : 'RINGAN (PELANGGARAN)';
        $durationText = $request->banned_until 
            ? "sampai tanggal " . \Carbon\Carbon::parse($request->banned_until)->format('d M Y H:i')
            : "selamanya (permanen)";

        $statusMessage = "Akun Anda telah ditangguhkan ({$banTypeText}) {$durationText} dengan alasan: {$request->ban_reason}. Harap hubungi Customer Service jika merasa ini adalah kesalahan.";
        
        $user->notify(new \App\Notifications\UserStatusNotification([
            'title'   => "Akun Anda DIBLOKIR ({$banTypeText})",
            'message' => $statusMessage,
            'url'     => $user->level === 'seller' ? route('seller.data.health') : route('profil.index'),
            'icon'    => 'mdi-account-off',
            'color'   => 'red'
        ]));

        return back()->with('success', "Status Diperbarui: Pengguna {$user->nama} telah diblokir ({$request->ban_type}).");
    }

    // 4B. LIHAT DAFTAR BANDING AKUN
    public function appeals(Request $request)
    {
        $appeals = \Illuminate\Support\Facades\DB::table('tb_banding_akun as b')
            ->join('tb_user as u', 'b.user_id', '=', 'u.id')
            ->select('b.*', 'u.nama', 'u.email', 'u.level')
            ->orderByRaw("FIELD(b.status, 'pending', 'disetujui', 'ditolak')")
            ->orderByDesc('b.created_at')
            ->paginate(15);

        return view('admin.users.appeals', compact('appeals'));
    }

    // 4C. PROSES BANDING AKUN
    public function processAppeal(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:disetujui,ditolak',
            'catatan_admin' => 'required|string|max:1000'
        ]);

        try {
            return \Illuminate\Support\Facades\DB::transaction(function () use ($request, $id) {
                $appeal = \Illuminate\Support\Facades\DB::table('tb_banding_akun')->where('id', $id)->first();
                
                if (!$appeal) {
                    return back()->with('error', 'Kesalahan: Data banding tidak ditemukan di sistem.');
                }

                $user = User::find($appeal->user_id);
                if (!$user) {
                    return back()->with('error', 'Kesalahan: Data pengguna terkait tidak ditemukan. Akun mungkin telah dihapus.');
                }

                if ($request->status === 'disetujui') {
                    // Pulihkan Akun
                    $user->is_banned = false;
                    $user->ban_type = 'none';
                    $user->ban_reason = null;
                    $user->banned_until = null;
                    $user->save();

                    // Jika Seller, aktifkan kembali tokonya
                    if ($user->level === 'seller') {
                        \Illuminate\Support\Facades\DB::table('tb_toko')
                            ->where('user_id', $user->id)
                            ->update(['status' => 'active']);
                    }

                    $user->notify(new \App\Notifications\UserStatusNotification([
                        'title'   => 'Banding Akun Disetujui',
                        'message' => "Selamat! Permohonan banding Anda disetujui. " . $request->catatan_admin,
                        'url'     => $user->level === 'seller' ? route('seller.dashboard') : route('profil.index'),
                        'icon'    => 'mdi-check-decagram',
                        'color'   => 'emerald'
                    ]));
                } else {
                    // Jika Ditolak, tetap banned tapi kirim notifikasi
                    $user->notify(new \App\Notifications\UserStatusNotification([
                        'title'   => 'Banding Akun Ditolak',
                        'message' => "Mohon maaf, permohonan banding Anda ditolak. " . $request->catatan_admin,
                        'url'     => $user->level === 'seller' ? route('seller.data.health') : route('profil.index'),
                        'icon'    => 'mdi-alert-circle',
                        'color'   => 'red'
                    ]));
                }

                // Update Status Banding
                \Illuminate\Support\Facades\DB::table('tb_banding_akun')
                    ->where('id', $id)
                    ->update([
                        'status' => $request->status,
                        'catatan_admin' => $request->catatan_admin,
                        'updated_at' => now()
                    ]);

                return back()->with('success', "Keputusan berhasil disimpan! Permohonan banding untuk {$user->nama} telah " . strtoupper($request->status) . ".");
            });
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Gagal proses banding: " . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan sistem saat memproses banding. Silakan coba lagi.');
        }
    }

    // 5. EXPORT DATA KE CSV
    public function exportCsv(Request $request)
    {
        $level_filter = $request->get('level', 'semua');
        $query = User::query()->where('level', '!=', 'bot');

        if ($level_filter !== 'semua') {
            $query->where('level', $level_filter);
        }

        $users = $query->latest()->get();

        $filename = "Data_Pengguna_Pondasikita_" . date('Ymd_His') . ".csv";
        $handle = fopen('php://memory', 'w');
        
        fputcsv($handle, ['ID', 'Username', 'Nama', 'Email', 'No Telepon', 'Level', 'Role Admin', 'Status Banned', 'Tanggal Daftar']);

        foreach ($users as $user) {
            fputcsv($handle, [
                $user->id,
                $user->username,
                $user->nama,
                $user->email,
                $user->no_telepon ?? '-',
                strtoupper($user->level),
                strtoupper($user->admin_role ?? '-'),
                $user->is_banned ? 'BANNED' : 'AKTIF',
                $user->created_at->format('Y-m-d H:i:s')
            ]);
        }

        fseek($handle, 0);
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        return response()->stream(function() use ($handle) {
            fpassthru($handle);
            fclose($handle);
        }, 200, $headers);
    }
}