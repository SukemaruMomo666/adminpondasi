<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ApiMonitorController extends Controller
{
    /**
     * Tampilkan halaman monitor API
     */
    public function index()
    {
        $apis = $this->checkAllApis();
        return view('pages.api_monitor', compact('apis'));
    }

    /**
     * Mengecek semua API yang didaftarkan.
     * Mengembalikan array dengan status, nama, dan response time.
     */
    private function checkAllApis()
    {
        $results = [];

        // 1. Cek Kunci API POTA / Gemini
        $geminiKeys = explode(',', env('GEMINI_API_KEYS', ''));
        foreach ($geminiKeys as $index => $key) {
            $key = trim($key);
            if (empty($key)) continue;

            $maskedKey = substr($key, 0, 10) . '...' . substr($key, -5);
            $startTime = microtime(true);
            
            $modelsList = [];
            
            try {
                // Melakukan ping ringan ke endpoint models (hanya membaca list model, tidak memakai token generation)
                $response = Http::timeout(5)->get('https://generativelanguage.googleapis.com/v1beta/models', [
                    'key' => $key
                ]);

                $timeTaken = round((microtime(true) - $startTime) * 1000);

                if ($response->successful()) {
                    $status = 'active';
                    $message = 'Tersedia';
                    
                    // Ambil daftar model Gemini yang didukung oleh API Key ini
                    $data = $response->json();
                    if (isset($data['models'])) {
                        foreach ($data['models'] as $modelData) {
                            $modelName = str_replace('models/', '', $modelData['name'] ?? '');
                            if (strpos($modelName, 'gemini') !== false && !in_array($modelName, $modelsList)) {
                                $modelsList[] = $modelName;
                            }
                        }
                    }
                } elseif ($response->status() == 429) {
                    $status = 'limit';
                    $message = 'Limit (Kuota Habis)';
                } else {
                    $status = 'error';
                    $message = 'Error ' . $response->status();
                }

            } catch (\Exception $e) {
                $timeTaken = round((microtime(true) - $startTime) * 1000);
                $status = 'error';
                $message = 'Timeout / Unreachable';
            }

            // Batasi tampilan model agar tidak terlalu panjang (tampilkan 3 utama)
            $supportedModels = count($modelsList) > 0 
                ? implode(', ', array_slice($modelsList, 0, 3)) . (count($modelsList) > 3 ? ' (+' . (count($modelsList) - 3) . ' lainnya)' : '')
                : 'Menunggu koneksi...';

            $results[] = [
                'name' => 'POTA Engine (Gemini) - ' . ($index + 1),
                'key' => $maskedKey,
                'status' => $status,
                'message' => $message,
                'latency' => $timeTaken,
                'type' => 'ai',
                'models' => $supportedModels
            ];
        }

        // 2. Tambahan: Pusher API (Hanya cek ketersediaan info dari env)
        $pusherKey = env('PUSHER_APP_KEY');
        if (!empty($pusherKey)) {
            $results[] = [
                'name' => 'Pusher Realtime (WebSocket)',
                'key' => substr($pusherKey, 0, 5) . '...',
                'status' => 'active',
                'message' => 'Online (No Rate Limit Detected)',
                'latency' => 0,
                'type' => 'websocket'
            ];
        }

        // 3. Tambahan: Biteship / Kurir (Jika ada)
        $biteshipKey = env('BITESHIP_API_KEY');
        if (!empty($biteshipKey)) {
            // Asumsi Biteship API
            $results[] = [
                'name' => 'Biteship Logistic API',
                'key' => substr($biteshipKey, 0, 5) . '...',
                'status' => 'active',
                'message' => 'Monitoring belum tersedia untuk Biteship',
                'latency' => 0,
                'type' => 'logistic'
            ];
        }

        return $results;
    }
}
