<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ShopController extends Controller
{
    /**
     * Helper untuk selalu mendapatkan toko yang valid
     */
    private function getToko()
    {
        $toko = DB::table('tb_toko')->where('user_id', Auth::id())->first();
        if (!$toko) {
            abort(403, 'Akses Ditolak: Anda belum memiliki data Toko.');
        }
        return $toko;
    }

    /**
     * ==========================================
     * 1. MANAJEMEN PROFIL TOKO
     * ==========================================
     */
    public function profile()
    {
        $toko = $this->getToko();
        return view('seller.shop.profile', compact('toko'));
    }

public function updateProfile(Request $request)
{
    $toko = $this->getToko();

    // 1. Validasi Komprehensif (DIPERBAIKI KHUSUS UNTUK BUG WINDOWS/LARAGON)
    $request->validate([
        'nama_toko'      => 'required|string|max:50',
        'slogan'         => 'nullable|string|max:100',
        'deskripsi_toko' => 'nullable|string|max:1000',
        'no_telepon'     => 'required|string|max:20',
        'alamat_toko'    => 'required|string|max:500', 
        'area_id'        => 'required|string|max:255', 
        'kota'           => 'nullable|string|max:100',
        'kode_pos'       => 'required|numeric|digits_between:5,6',
        'latitude'       => 'required|numeric',
        'longitude'      => 'required|numeric',
        'catatan_toko'   => 'nullable|string',
        'kebijakan_retur'=> 'nullable|string',

        // 🔥 SOLUSI SAKTI: Ganti 'image|mimes' jadi 'extensions' agar tidak di-block oleh Windows
        'logo_toko'      => 'nullable|file|extensions:jpeg,png,jpg,webp|max:5120',
        'banner_toko'    => 'nullable|file|extensions:jpeg,png,jpg,webp|max:5120',
        'dokumen_nib'    => 'nullable|file|extensions:pdf,jpg,jpeg,png|max:5120',
        'dokumen_npwp'   => 'nullable|file|extensions:pdf,jpg,jpeg,png|max:5120',
    ], [
        'area_id.required' => 'Kecamatan Biteship wajib dicari dan diklik dari pilihan dropdown.'
    ]);

    // 2. Persiapan Data Teks & Koordinat
    $dataUpdate = [
        'nama_toko'      => $request->nama_toko,
        'slogan'         => $request->slogan,
        'deskripsi_toko' => $request->deskripsi_toko,
        'telepon_toko'   => $request->no_telepon,
        'alamat_toko'    => $request->alamat_toko,     
        'area_id'        => $request->area_id,         
        'kota'           => $request->kota,            
        'kode_pos'       => $request->kode_pos,
        'latitude'       => $request->latitude,
        'longitude'      => $request->longitude,
        'catatan_toko'   => $request->catatan_toko,
        'kebijakan_retur'=> $request->kebijakan_retur,
        'updated_at'     => now()
    ];

    // 3. Kosongkan ID wilayah lama agar tidak bentrok
    if (\Illuminate\Support\Facades\Schema::hasColumn('tb_toko', 'province_id')) {
        $dataUpdate['province_id'] = null;
        $dataUpdate['city_id'] = null;
        $dataUpdate['district_id'] = null;
    }

    // 4. Handle Logo Baru
    if ($request->hasFile('logo_toko')) {
        $logo = $request->file('logo_toko');
        $logoName = 'logo_' . \Illuminate\Support\Str::random(10) . '.' . $logo->getClientOriginalExtension();

        if (!empty($toko->logo_toko)) {
            $oldPath = public_path('assets/uploads/logos/' . $toko->logo_toko);
            if (\Illuminate\Support\Facades\File::exists($oldPath)) { 
                \Illuminate\Support\Facades\File::delete($oldPath); 
            }
        }

        if(!\Illuminate\Support\Facades\File::exists(public_path('assets/uploads/logos'))) { 
            \Illuminate\Support\Facades\File::makeDirectory(public_path('assets/uploads/logos'), 0777, true); 
        }
        $logo->move(public_path('assets/uploads/logos'), $logoName);
        $dataUpdate['logo_toko'] = $logoName;
    }

    // 5. Handle Banner Baru
    if ($request->hasFile('banner_toko')) {
        $banner = $request->file('banner_toko');
        $bannerName = 'banner_' . \Illuminate\Support\Str::random(10) . '.' . $banner->getClientOriginalExtension();

        if (!empty($toko->banner_toko)) {
            $oldBannerPath = public_path('assets/uploads/banners/' . $toko->banner_toko);
            if (\Illuminate\Support\Facades\File::exists($oldBannerPath)) { 
                \Illuminate\Support\Facades\File::delete($oldBannerPath); 
            }
        }

        if(!\Illuminate\Support\Facades\File::exists(public_path('assets/uploads/banners'))) { 
            \Illuminate\Support\Facades\File::makeDirectory(public_path('assets/uploads/banners'), 0777, true); 
        }
        $banner->move(public_path('assets/uploads/banners'), $bannerName);
        $dataUpdate['banner_toko'] = $bannerName;
    }

    // 6. Handle Dokumen Legalitas
    $legalPath = public_path('assets/uploads/legalitas');
    if(!\Illuminate\Support\Facades\File::exists($legalPath)) { 
        \Illuminate\Support\Facades\File::makeDirectory($legalPath, 0777, true); 
    }

    if ($request->hasFile('dokumen_nib')) {
        $nib = $request->file('dokumen_nib');
        $nibName = 'NIB_' . $toko->id . '_' . \Illuminate\Support\Str::random(5) . '.' . $nib->getClientOriginalExtension();
        if (!empty($toko->dokumen_nib) && \Illuminate\Support\Facades\File::exists($legalPath . '/' . $toko->dokumen_nib)) { 
            \Illuminate\Support\Facades\File::delete($legalPath . '/' . $toko->dokumen_nib); 
        }
        $nib->move($legalPath, $nibName);
        $dataUpdate['dokumen_nib'] = $nibName;
    }

    if ($request->hasFile('dokumen_npwp')) {
        $npwp = $request->file('dokumen_npwp');
        $npwpName = 'NPWP_' . $toko->id . '_' . \Illuminate\Support\Str::random(5) . '.' . $npwp->getClientOriginalExtension();
        if (!empty($toko->dokumen_npwp) && \Illuminate\Support\Facades\File::exists($legalPath . '/' . $toko->dokumen_npwp)) { 
            \Illuminate\Support\Facades\File::delete($legalPath . '/' . $toko->dokumen_npwp); 
        }
        $npwp->move($legalPath, $npwpName);
        $dataUpdate['dokumen_npwp'] = $npwpName;
    }

    // 7. Eksekusi Update ke Database
    \Illuminate\Support\Facades\DB::table('tb_toko')->where('id', $toko->id)->update($dataUpdate);

    return redirect()->back()->with('success', 'Profil & Legalitas Toko berhasil diperbarui!');
}

/**
 * ==========================================
 * 1.5 KENAIKAN TIER (UPGRADE LEVEL)
 * ==========================================
 */
public function tier()
{
    $toko = $this->getToko();

    // 1. Ambil Statistik Rating
    $rating = DB::table('tb_toko_review')
        ->where('toko_id', $toko->id)
        ->avg('rating') ?: 0;

    // 2. Ambil Total Pesanan Selesai
    $totalPesanan = DB::table('tb_detail_transaksi')
        ->where('toko_id', $toko->id)
        ->whereIn('status_pesanan_item', ['sampai_tujuan', 'selesai'])
        ->distinct('transaksi_id')
        ->count('transaksi_id');

    // 3. Ambil Pengajuan Aktif (jika ada)
    $pengajuan = DB::table('tb_pengajuan_tier')
        ->where('toko_id', $toko->id)
        ->orderByDesc('created_at')
        ->first();

    // 4. Tentukan Eligibility (Syarat)
    $nextTier = null;
    $syarat = [];

    if ($toko->tier_toko == 'regular') {
        $nextTier = 'power_merchant';
        $syarat = [
            'rating' => ['min' => 4.0, 'current' => round($rating, 1)],
            'pesanan' => ['min' => 5, 'current' => $totalPesanan]
        ];
    } elseif ($toko->tier_toko == 'power_merchant') {
        $nextTier = 'official_store';
        $syarat = [
            'rating' => ['min' => 4.7, 'current' => round($rating, 1)],
            'pesanan' => ['min' => 50, 'current' => $totalPesanan],
            'legalitas' => ['required' => true, 'nib' => !empty($toko->dokumen_nib), 'npwp' => !empty($toko->dokumen_npwp)]
        ];
    }

    $isEligible = true;
    foreach ($syarat as $key => $val) {
        if ($key == 'rating' && $val['current'] < $val['min']) $isEligible = false;
        if ($key == 'pesanan' && $val['current'] < $val['min']) $isEligible = false;
        if ($key == 'legalitas' && (!$val['nib'] || !$val['npwp'])) $isEligible = false;
    }

    return view('seller.shop.tier', compact('toko', 'rating', 'totalPesanan', 'pengajuan', 'nextTier', 'syarat', 'isEligible'));
}

public function applyTierUpgrade(Request $request)
{
    $toko = $this->getToko();

    // Cek apakah sudah ada pengajuan pending
    $existing = DB::table('tb_pengajuan_tier')
        ->where('toko_id', $toko->id)
        ->whereIn('status', ['pending', 'revision'])
        ->exists();

    if ($existing) {
        return back()->with('error', 'Anda masih memiliki pengajuan yang sedang diproses.');
    }

    // Tentukan tier tujuan
    $tierTujuan = ($toko->tier_toko == 'regular') ? 'power_merchant' : 'official_store';

    // Simpan snapshot metadata saat pengajuan
    $rating = DB::table('tb_toko_review')->where('toko_id', $toko->id)->avg('rating') ?: 0;
    $totalPesanan = DB::table('tb_detail_transaksi')
        ->where('toko_id', $toko->id)
        ->whereIn('status_pesanan_item', ['sampai_tujuan', 'selesai'])
        ->distinct('transaksi_id')
        ->count('transaksi_id');

    DB::table('tb_pengajuan_tier')->insert([
        'toko_id' => $toko->id,
        'tier_saat_ini' => $toko->tier_toko,
        'tier_tujuan' => $tierTujuan,
        'status' => 'pending',
        'catatan_penjual' => $request->catatan,
        'metadata_syarat' => json_encode([
            'rating_snapshot' => round($rating, 2),
            'pesanan_snapshot' => $totalPesanan,
            'nib_snapshot' => !empty($toko->dokumen_nib),
            'npwp_snapshot' => !empty($toko->dokumen_npwp)
        ]),
        'created_at' => now(),
        'updated_at' => now()
    ]);

    return redirect()->route('seller.shop.tier')->with('success', 'Pengajuan naik level berhasil dikirim! Silakan tunggu tinjauan admin.');
}

    /**
     * ==========================================
     * 2. PENGATURAN TOKO & KEAMANAN
     * ==========================================
     */
    public function settings()
    {
        $user = Auth::user();
        $toko = $this->getToko();

        $notif = json_decode($toko->notifikasi_settings ?? '{}', true) ?: [
            'email_pesanan' => true,
            'email_promo'   => false,
            'push_chat'     => true,
        ];

        return view('seller.shop.settings', compact('user', 'toko', 'notif'));
    }

    public function updateSettings(Request $request)
    {
        $user = Auth::user();
        $toko = $this->getToko();

        // TAB PENGATURAN UMUM: Status Libur & Notifikasi
        if ($request->has('form_type') && $request->form_type == 'general') {
            $isVacation = $request->has('status_libur') ? 1 : 0;

            $notifSettings = json_encode([
                'email_pesanan' => $request->has('notif_email_pesanan'),
                'email_promo'   => $request->has('notif_email_promo'),
                'push_chat'     => $request->has('notif_push_chat'),
            ]);

            DB::table('tb_toko')->where('id', $toko->id)->update([
                'status_libur'        => $isVacation,
                'pesan_otomatis'      => $request->pesan_otomatis,
                'notifikasi_settings' => $notifSettings,
                'updated_at'          => now()
            ]);

            return redirect()->back()->with('success', 'Pengaturan toko berhasil disimpan!');
        }

        return redirect()->back()->with('error', 'Permintaan tidak valid.');
    }

    /**
     * ==========================================
     * 2.5 KEAMANAN AKUN (OTP & RESET PASSWORD)
     * ==========================================
     */
    public function securityIndex()
    {
        return view('seller.shop.security');
    }

    public function sendSecurityOtp(Request $request)
    {
        $user = Auth::user();
        
        // Generate 6 digit OTP
        $otp = rand(100000, 999999);
        
        // Simpan OTP di Cache selama 5 menit
        \Illuminate\Support\Facades\Cache::put('security_otp_' . $user->id, $otp, now()->addMinutes(5));
        
        // Kirim Email
        \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\OtpSecurityMail($otp, $user->nama));
        
        return response()->json(['status' => 'success', 'message' => 'Kode OTP telah dikirim ke email Anda.']);
    }

    public function verifySecurityOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|numeric|digits:6',
        ]);

        $user = Auth::user();
        $savedOtp = \Illuminate\Support\Facades\Cache::get('security_otp_' . $user->id);

        if ($savedOtp && $savedOtp == $request->otp) {
            // Tandai sesi verifikasi sukses (berlaku 15 menit)
            session(['security_verified_at' => now()]);
            \Illuminate\Support\Facades\Cache::forget('security_otp_' . $user->id);
            
            return response()->json(['status' => 'success']);
        }

        return response()->json(['status' => 'error', 'message' => 'Kode OTP salah atau telah kedaluwarsa.']);
    }

    public function resetPassword(Request $request)
    {
        // Pastikan sudah diverifikasi via OTP sebelumnya
        if (!session('security_verified_at') || now()->diffInMinutes(session('security_verified_at')) > 15) {
            return redirect()->route('seller.shop.security')->with('error', 'Sesi verifikasi telah habis. Silakan ulangi proses.');
        }

        $request->validate([
            'new_password' => 'required|min:8|confirmed',
        ]);

        $user = Auth::user();

        // Update Password Baru
        DB::table('tb_user')
            ->where('id', $user->id)
            ->update(['password' => Hash::make($request->new_password)]);

        // Hapus sesi verifikasi
        $request->session()->forget('security_verified_at');

        return redirect()->route('seller.shop.security')->with('success', 'Kata sandi berhasil diperbarui dengan aman!');
    }

    /**
     * ==========================================
     * 3. DEKORASI TOKO (DRAG & DROP LOGIC)
     * ==========================================
     */

    // Halaman Landing Dekorasi (Pilih Mobile/Desktop)
    public function decoration()
    {
        $toko = $this->getToko();

        $defaultLayout = [
            ['id' => 'banner_promo', 'type' => 'banner', 'title' => 'Banner Promo Utama', 'image' => null],
            ['id' => 'kategori_pilihan', 'type' => 'kategori', 'title' => 'Kategori Pilihan', 'items' => []],
            ['id' => 'produk_terlaris', 'type' => 'produk', 'title' => 'Produk Terlaris', 'items' => []]
        ];

        // Mencegah error null pointer jika layout belum ada
        $layoutData = empty($toko->layout_data) ? $defaultLayout : json_decode($toko->layout_data, true);

        return view('seller.shop.decoration', compact('toko', 'layoutData'));
    }

    // Halaman Pemilihan Template
    public function templateSelection()
    {
        $toko = $this->getToko();
        return view('seller.shop.template-selection', compact('toko'));
    }

    // Halaman Editor Drag & Drop (Mobile)
    public function editor()
    {
        $toko = $this->getToko();
        return view('seller.shop.editor', compact('toko'));
    }

    // Halaman Editor Desktop KITA
    public function editorDesktop()
    {
        $toko = $this->getToko();
        return view('seller.shop.editor-desktop', compact('toko'));
    }

    /**
     * Update susunan dekorasi via AJAX (Mobile / Versi Lama)
     */
    public function updateDecoration(Request $request)
    {
        $request->validate([
            'layout_data' => 'required|array'
        ]);

        $toko = $this->getToko();

        DB::table('tb_toko')->where('id', $toko->id)->update([
            'layout_data' => json_encode($request->layout_data),
            'updated_at'  => now()
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Dekorasi toko berhasil disimpan!'
        ]);
    }

    /**
     * FUNGSI SAKTI PENYIMPANAN DESKTOP EDITOR
     */
    public function saveDecoration(Request $request)
    {
        $toko = DB::table('tb_toko')->where('user_id', Auth::id())->first();

        if (!$toko) {
            return response()->json(['success' => false, 'message' => 'Toko tidak ditemukan.']);
        }

        // Pastikan menyimpan sebagai raw string (karena JSON Payload)
        DB::table('tb_toko')->where('id', $toko->id)->update([
            'dekorasi_desktop' => json_encode($request->all()),
            'updated_at'       => now()
        ]);

        return response()->json(['success' => true, 'message' => 'Dekorasi berhasil ditayangkan!']);
    }
}