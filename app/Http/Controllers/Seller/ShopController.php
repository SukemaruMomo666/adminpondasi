<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
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

        // 1. Validasi Komprehensif (Diperbaiki: alamat_toko & area_id wajib ada)
        $request->validate([
            'nama_toko'      => 'required|string|max:50',
            'slogan'         => 'nullable|string|max:100',
            'deskripsi_toko' => 'nullable|string|max:1000',
            'no_telepon'     => 'required|string|max:20',
            'alamat_toko'    => 'required|string|max:500', // <-- FIX: Harus alamat_toko
            'area_id'        => 'required|string|max:255', // <-- FIX: Wajib untuk Biteship
            'kode_pos'       => 'required|numeric|digits_between:5,6',
            'latitude'       => 'required|numeric',
            'longitude'      => 'required|numeric',
            'catatan_toko'   => 'nullable|string',
            'kebijakan_retur'=> 'nullable|string',
            'logo_toko'      => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'banner_toko'    => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'dokumen_nib'    => 'nullable|mimes:pdf,jpg,jpeg,png|max:5120',
            'dokumen_npwp'   => 'nullable|mimes:pdf,jpg,jpeg,png|max:5120',
        ], [
            'area_id.required' => 'Kecamatan Biteship wajib dicari dan diklik dari pilihan dropdown.'
        ]);

        // 2. Persiapan Data Teks & Koordinat (Diperbaiki agar area_id tersimpan ke database)
        $dataUpdate = [
            'nama_toko'      => $request->nama_toko,
            'slogan'         => $request->slogan,
            'deskripsi_toko' => $request->deskripsi_toko,
            'telepon_toko'   => $request->no_telepon,
            'alamat_toko'    => $request->alamat_toko,     // <-- FIX: Sinkron dengan database & Blade
            'area_id'        => $request->area_id,         // <-- FIX: INI YANG BIKIN BITESHIP TERSIMPAN!
            'kode_pos'       => $request->kode_pos,
            'latitude'       => $request->latitude,
            'longitude'      => $request->longitude,
            'catatan_toko'   => $request->catatan_toko,
            'kebijakan_retur'=> $request->kebijakan_retur,
            'updated_at'     => now()
        ];

        // 3. Sistem Upload File Dinamis (Skala Enterprise)
        $fileFields = [
            'logo_toko'    => 'logos',
            'banner_toko'  => 'banners',
            'dokumen_nib'  => 'legalitas', // Penyesuaian nama folder agar lebih aman
            'dokumen_npwp' => 'legalitas'
        ];

        foreach ($fileFields as $field => $folder) {
            if ($request->hasFile($field)) {
                $file = $request->file($field);
                $fileName = $field . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
                $targetPath = public_path("assets/uploads/{$folder}");

                // Buat direktori jika belum ada
                if (!File::exists($targetPath)) {
                    File::makeDirectory($targetPath, 0755, true);
                }

                // Hapus file lama jika ada
                if (!empty($toko->$field)) {
                    $oldPath = $targetPath . '/' . $toko->$field;
                    if (File::exists($oldPath)) {
                        File::delete($oldPath);
                    }
                }

                $file->move($targetPath, $fileName);
                $dataUpdate[$field] = $fileName;
            }
        }

        // 4. Eksekusi Update
        DB::table('tb_toko')->where('id', $toko->id)->update($dataUpdate);

        return redirect()->back()->with('success', 'Profil & Legalitas Toko berhasil diperbarui!');
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

        // TAB KEAMANAN: Ganti Password
        if ($request->has('form_type') && $request->form_type == 'security') {
            $request->validate([
                'current_password' => 'required',
                'new_password'     => 'required|min:8|confirmed',
            ]);

            // Cek Password Lama
            if (!Hash::check($request->current_password, $user->password)) {
                return redirect()->back()->with('error', 'Password saat ini salah!');
            }

            // Update Password Baru
            DB::table('tb_user')
                ->where('id', $user->id)
                ->update(['password' => Hash::make($request->new_password)]);

            return redirect()->back()->with('success', 'Password berhasil diperbarui!');
        }

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