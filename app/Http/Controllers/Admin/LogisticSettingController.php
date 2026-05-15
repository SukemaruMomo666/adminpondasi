<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LogisticSettingController extends Controller
{
    public function index()
    {
        // Ambil semua pengaturan logistik dari database
        $settingsData = DB::table('tb_pengaturan')->get();
        $settings = [];
        foreach ($settingsData as $row) {
            $settings[$row->setting_nama] = $row->setting_nilai;
        }

        // =========================================================================
        // KAMUS MASTER EKSPEDISI BITESHIP (REAL DATA / BUKAN DUMMY)
        // Ini adalah kamus kode resmi yang dikenali oleh API Biteship.
        // Mendukung layanan Instan, Sameday, Reguler, hingga Kargo.
        // =========================================================================
        $api_couriers = [
            'jne'      => ['name' => 'JNE Express', 'type' => 'Reguler, Kargo & Truking', 'icon' => 'mdi-truck-fast'],
            'jnt'      => ['name' => 'J&T Express', 'type' => 'Reguler & Kargo', 'icon' => 'mdi-truck-delivery'],
            'sicepat'  => ['name' => 'SiCepat', 'type' => 'Reguler, Kargo & Sameday', 'icon' => 'mdi-lightning-bolt'],
            'pos'      => ['name' => 'POS Indonesia', 'type' => 'Reguler & Kargo', 'icon' => 'mdi-postbox'],
            'tiki'     => ['name' => 'TIKI', 'type' => 'Reguler & Sameday', 'icon' => 'mdi-truck-outline'],
            'ninja'    => ['name' => 'Ninja Xpress', 'type' => 'Reguler', 'icon' => 'mdi-ninja'],
            'lion'     => ['name' => 'Lion Parcel', 'type' => 'Reguler & Kargo', 'icon' => 'mdi-airplane-takeoff'],
            'anteraja' => ['name' => 'AnterAja', 'type' => 'Reguler, Kargo & Sameday', 'icon' => 'mdi-truck-check'],
            'paxel'    => ['name' => 'Paxel', 'type' => 'Sameday & Frozen', 'icon' => 'mdi-package-variant'],
            'gosend'   => ['name' => 'GoSend', 'type' => 'Instant & Sameday', 'icon' => 'mdi-motorbike'],
            'grab'     => ['name' => 'GrabExpress', 'type' => 'Instant & Sameday', 'icon' => 'mdi-motorbike'],
            'lalamove' => ['name' => 'Lalamove', 'type' => 'Instant & Armada Besar', 'icon' => 'mdi-truck-flatbed'],
            'borzo'    => ['name' => 'Borzo', 'type' => 'Instant Delivery', 'icon' => 'mdi-motorbike'],
            'indah'    => ['name' => 'Indah Logistik', 'type' => 'Kargo Berat', 'icon' => 'mdi-truck-flatbed'],
            'wahana'   => ['name' => 'Wahana Express', 'type' => 'Kargo & Ekonomi', 'icon' => 'mdi-weight-kilogram'],
            'sap'      => ['name' => 'SAP Express', 'type' => 'Reguler & Kargo', 'icon' => 'mdi-map-marker-path'],
            'ide'      => ['name' => 'ID Express', 'type' => 'Reguler', 'icon' => 'mdi-truck-fast-outline'],
            'sentral'  => ['name' => 'Sentral Cargo', 'type' => 'Kargo Domestik', 'icon' => 'mdi-package-variant-closed'],
            'rex'      => ['name' => 'REX Express', 'type' => 'Kargo & Dokumen', 'icon' => 'mdi-truck-cargo-container'],
            'rpx'      => ['name' => 'RPX', 'type' => 'Reguler & Kargo', 'icon' => 'mdi-truck-delivery'],
        ];

        return view('admin.logistics.index', compact('settings', 'api_couriers'));
    }

    public function update(Request $request)
    {
        $data = $request->except(['_token']);

        // =========================================================================
        // PROSES DATA KURIR BITESHIP
        // =========================================================================
        // Tangkap array kurir yang dikirim dari form
        $couriers = $request->input('api_active_couriers', []);
        
        // Filter & Bersihkan HACK (NONE_SELECTED_HACK) yang dipasang di Blade
        $couriers = array_filter($couriers, function($value) {
            return $value !== 'NONE_SELECTED_HACK';
        });

        // Reset index array dan encode menjadi format JSON sebelum masuk database
        $data['api_active_couriers'] = json_encode(array_values($couriers));

        // =========================================================================
        // PROSES FITUR TOGGLE (ON/OFF)
        // =========================================================================
        $toggles = [
            'enable_store_pickup',
            'enable_custom_fleet',
            'enable_emergency_delivery',
            'force_insurance'
        ];

        // Jika toggle tidak dikirim (karena tidak dicentang dan tidak ada hidden field), paksa jadi '0'
        foreach ($toggles as $toggle) {
            if (!isset($data[$toggle])) {
                $data[$toggle] = '0';
            }
        }

        // =========================================================================
        // SIMPAN / UPDATE KE DATABASE
        // =========================================================================
        foreach ($data as $key => $value) {
            DB::table('tb_pengaturan')->updateOrInsert(
                ['setting_nama' => $key],
                ['setting_nilai' => $value]
            );
        }

        return back()->with('success', 'Regulasi dan Pengaturan Logistik berhasil diperbarui.');
    }
}