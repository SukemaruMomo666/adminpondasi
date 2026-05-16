<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class LogisticSettingController extends Controller
{
    public function index()
    {
        $settingsData = DB::table('tb_pengaturan')->get();
        $settings = [];
        foreach ($settingsData as $row) {
            $settings[$row->setting_nama] = $row->setting_nilai;
        }

        $api_couriers = [];
        // Pastikan tidak ada spasi terselip di API Key
        $apiKey = trim($settings['biteship_api_key'] ?? ''); 
        $apiErrorMsg = null;

        $iconMap = [
            'jne'      => 'mdi-truck-fast',
            'jnt'      => 'mdi-truck-delivery',
            'sicepat'  => 'mdi-lightning-bolt',
            'pos'      => 'mdi-postbox',
            'tiki'     => 'mdi-truck-outline',
            'ninja'    => 'mdi-ninja',
            'lion'     => 'mdi-airplane-takeoff',
            'anteraja' => 'mdi-truck-check',
            'paxel'    => 'mdi-package-variant',
            'gosend'   => 'mdi-motorbike',
            'grab'     => 'mdi-motorbike',
            'lalamove' => 'mdi-truck-flatbed',
            'borzo'    => 'mdi-motorbike',
            'indah'    => 'mdi-truck-flatbed',
            'wahana'   => 'mdi-weight-kilogram',
            'sap'      => 'mdi-map-marker-path',
            'ide'      => 'mdi-truck-fast-outline',
            'sentral'  => 'mdi-package-variant-closed',
            'rex'      => 'mdi-truck-cargo-container',
            'rpx'      => 'mdi-truck-delivery',
        ];

        if (!empty($apiKey)) {
            try {
                $response = Http::withHeaders([
                    'Authorization' => $apiKey
                ])->timeout(15)->get('https://api.biteship.com/v1/couriers');

                if ($response->successful()) {
                    $couriersData = $response->json('couriers') ?? [];
                    $rawCouriers = [];

                    foreach ($couriersData as $c) {
                        // PERBAIKAN: Gunakan ?? agar kebal error jika atribut tidak ada dari Biteship
                        $company = $c['courier_code'] ?? $c['company'] ?? null;
                        if (!$company) continue;

                        if (!isset($rawCouriers[$company])) {
                            $rawCouriers[$company] = [
                                'name' => $c['courier_name'] ?? strtoupper($company),
                                'services' => []
                            ];
                        }
                        
                        $type = ucfirst($c['type'] ?? 'Standard'); 
                        if (!in_array($type, $rawCouriers[$company]['services'])) {
                            $rawCouriers[$company]['services'][] = $type;
                        }
                    }

                    foreach ($rawCouriers as $company => $data) {
                        $api_couriers[$company] = [
                            'name' => $data['name'],
                            'type' => implode(', ', $data['services']),
                            'icon' => $iconMap[$company] ?? 'mdi-truck-fast'
                        ];
                    }
                } else {
                    $apiErrorMsg = "Biteship menolak akses. Status: " . $response->status();
                }
            } catch (\Exception $e) {
                $apiErrorMsg = "Gagal memproses data API: " . $e->getMessage();
            }
        } else {
            $apiErrorMsg = "API Key Biteship belum diisi di menu Pengaturan Situs.";
        }

        // JIKA TETAP GAGAL, LEMPAR KE FALLBACK
        if (empty($api_couriers)) {
            $api_couriers = [
                'jne' => ['name' => 'JNE Express (Mode Fallback)', 'type' => $apiErrorMsg ?? 'Tidak ada data', 'icon' => 'mdi-truck-fast'],
            ];
        }

        return view('admin.logistics.index', compact('settings', 'api_couriers', 'apiErrorMsg'));
    }

    public function update(Request $request)
    {
        $data = $request->except(['_token']);

        $couriers = $request->input('api_active_couriers', []);
        $couriers = array_filter($couriers, function($value) {
            return $value !== 'NONE_SELECTED_HACK';
        });

        $data['api_active_couriers'] = implode(',', array_values($couriers));

        $toggles = [
            'enable_store_pickup',
            'enable_custom_fleet',
            'enable_emergency_delivery',
            'force_insurance'
        ];

        foreach ($toggles as $toggle) {
            if (!isset($data[$toggle])) {
                $data[$toggle] = '0';
            }
        }

        foreach ($data as $key => $value) {
            DB::table('tb_pengaturan')->updateOrInsert(
                ['setting_nama' => $key],
                ['setting_nilai' => $value]
            );
        }

        return back()->with('success', 'Regulasi dan Pengaturan Logistik berhasil diperbarui.');
    }
}