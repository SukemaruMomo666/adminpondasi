<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class BiteshipController extends Controller
{
    public function searchArea(Request $request)
    {
        $input = $request->query('input');
        
        // 1. Ambil API KEY dari Database (Pastikan sudah diisi di tb_pengaturan)
        $apiKey = DB::table('tb_pengaturan')->where('setting_nama', 'biteship_api_key')->value('setting_nilai');
        
        if (empty($apiKey)) {
            return response()->json(['success' => false, 'message' => 'API Key belum disetting'], 500);
        }

        // 2. Tembak langsung ke server Biteship
        try {
            $response = Http::withHeaders([
                'Authorization' => $apiKey,
                'Content-Type'  => 'application/json'
            ])->get('https://api.biteship.com/v1/maps/areas', [
                'countries' => 'ID',
                'input' => $input,
                'type' => 'single' // Bisa diubah tergantung kebutuhan
            ]);

            return response()->json($response->json(), $response->status());
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}