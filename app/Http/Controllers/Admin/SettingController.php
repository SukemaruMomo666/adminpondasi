<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SettingController extends Controller
{
    /**
     * Menampilkan halaman Pengaturan
     */
    public function index()
    {
        // Ambil semua pengaturan dan jadikan key-value pair
        $settingsData = DB::table('tb_pengaturan')->get();
        $settings = [];
        foreach ($settingsData as $row) {
            $settings[$row->setting_nama] = $row->setting_nilai;
        }

        // Daftar Kurir Bawaan untuk Integrasi Logistik (Biteship Standard)
        $couriers = [
            'jne' => 'JNE Express', 
            'pos' => 'POS Indonesia', 
            'tiki' => 'TIKI',
            'sicepat' => 'SiCepat', 
            'jnt' => 'J&T Express', 
            'ninja' => 'Ninja Xpress',
            'anteraja' => 'AnterAja', 
            'gojek' => 'GoSend', 
            'grab' => 'GrabExpress',
            'lalamove' => 'Lalamove'
        ];

        return view('admin.settings.index', compact('settings', 'couriers'));
    }

    /**
     * Menyimpan semua pembaruan pengaturan (Upload Gambar & Data)
     */
    public function update(Request $request)
    {
        // 1. Ambil semua inputan kecuali token dan method
        $settings = $request->except(['_token', '_method']);

        // 2. LOGIKA UPLOAD GAMBAR (Banner & Popup)
        $imageFields = ['hero_image_1', 'hero_image_2', 'hero_image_3', 'hero_image_4', 'popup_image'];

        foreach ($imageFields as $field) {
            // Jika ada gambar baru yang diunggah
            if ($request->hasFile($field)) {
                $file = $request->file($field);
                $filename = time() . '_' . $field . '.' . $file->getClientOriginalExtension();
                
                // Simpan gambar ke folder storage/app/public/banners
                $path = $file->storeAs('banners', $filename, 'public');
                
                // Masukkan path ke array settings agar ikut tersimpan ke database
                $settings[$field] = $path;
            }
        }

        // 3. Keamanan Checkbox: Jika toggle dimatikan, paksa nilainya jadi '0'
        $toggles = [
            'maintenance_mode',
            'enable_welcome_popup',
            'show_top_stores',
            'show_best_selling',
            'midtrans_is_production', 
            'auto_approve_products', 
            'auto_approve_stores', 
            'enable_dp_system',
            'force_insurance',
            'enable_store_pickup',
            'enable_custom_fleet',
            'enable_emergency_delivery'
        ];
        
        foreach ($toggles as $toggle) {
            if (!isset($settings[$toggle])) {
                $settings[$toggle] = '0';
            }
        }

        // 4. Keamanan Array: Ubah pilihan kurir menjadi format JSON agar bisa disimpan di 1 kolom
        if (isset($settings['couriers'])) {
            $settings['api_active_couriers'] = json_encode($settings['couriers']);
            unset($settings['couriers']); // Hapus array asli agar tidak error saat di-looping
        } else {
            // Jika tidak ada kurir yang dicentang sama sekali
            $settings['api_active_couriers'] = json_encode([]);
        }

        // 5. Looping Simpan ke Database (Disesuaikan dengan tabel tb_pengaturan)
        foreach ($settings as $key => $value) {
            // Pastikan data yang disimpan bukan array atau object (kecuali yang sudah diubah ke JSON)
            if (is_array($value)) {
                $value = json_encode($value);
            }

            if (!is_object($value)) {
                DB::table('tb_pengaturan')
                    ->updateOrInsert(
                        ['setting_nama' => $key],
                        ['setting_nilai' => $value]
                    );
            }
        }

        return redirect()->back()->with('success', 'Pengaturan sistem & tampilan website berhasil diperbarui!');
    }

    /**
     * Fitur Sinkronisasi Data Wilayah
     * CATATAN: Untuk sistem Biteship, fitur ini sudah tidak diperlukan lagi.
     */
    public function syncKomerce()
    {
        // Fitur ini dibiarkan ada untuk mencegah error 404 pada tombol/route lama.
        // Biteship menggunakan pencarian API langsung (Auto-complete) dari frontend.
        // Tidak butuh lagi mendownload ribuan data kota/kecamatan ke database lokal.
        
        return back()->with('success', 'Sistem Biteship beroperasi secara Real-Time (On-Demand). Anda tidak perlu lagi melakukan sinkronisasi database wilayah secara manual!');
    }
}