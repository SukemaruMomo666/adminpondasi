<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB; 

class ChatAiController extends Controller
{
    public function handleChat(Request $request)
    {
        $userMessage = $request->input('message');
        $chatHistory = $request->input('history', []); 

        if (!$userMessage) {
            return response()->json(['reply' => 'Pesan tidak boleh kosong, Bos!'], 400);
        }

        $apiKey = "AIzaSyCc4Es67EjRU5u68nWMEZiOfb9a6CiXb-A";

        // ====================================================================
        // 1. CEK TOKO POPULER
        // ====================================================================
        $tokoPopuler = DB::table('tb_toko')
            ->where('status', 'active')
            ->whereIn('tier_toko', ['power_merchant', 'official_store'])
            ->limit(3)
            ->get(['nama_toko', 'tier_toko']);

        $infoToko = "INFO TOKO TERBAIK: ";
        if ($tokoPopuler->count() > 0) {
            foreach($tokoPopuler as $t) {
                $infoToko .= "Toko {$t->nama_toko} ({$t->tier_toko}), ";
            }
        } else {
            $infoToko .= "Belum ada toko berstatus Power Merchant.";
        }

        // ====================================================================
        // 2. MESIN PENCARI BARANG PINTAR
        // ====================================================================
        $keywords = explode(' ', strtolower($userMessage));
        $stopWords = ['yang', 'buat', 'apa', 'tolong', 'carikan', 'halo', 'pota', 'di', 'dari', 'ke', 'ini', 'itu', 'adalah', 'ada', 'jual', 'toko', 'paling', 'mahal', 'murah'];
        $cleanWords = array_diff($keywords, $stopWords);

        $infoPencarian = "";
        
        // Buat Query Dasar
        $query = DB::table('tb_barang')
            ->join('tb_toko', 'tb_barang.toko_id', '=', 'tb_toko.id')
            ->where('tb_barang.is_active', 1)
            // KITA TAMBAHKAN tb_barang.slug UNTUK MEMBUAT URL
            ->select('tb_barang.id', 'tb_barang.nama_barang', 'tb_barang.slug', 'tb_barang.harga', 'tb_barang.stok', 'tb_toko.nama_toko');

        // Jika ada kata kunci spesifik
        if (count($cleanWords) > 0) {
            $query->where(function($q) use ($cleanWords) {
                foreach ($cleanWords as $word) {
                    if (strlen($word) > 2) {
                        $q->orWhere('tb_barang.nama_barang', 'like', '%' . $word . '%')
                          ->orWhere('tb_barang.deskripsi', 'like', '%' . $word . '%');
                    }
                }
            });
        }
        
        // Jika user nanya "paling mahal", kita bantu urutkan dari yang termahal
        if (str_contains(strtolower($userMessage), 'mahal')) {
            $query->orderBy('tb_barang.harga', 'desc');
        } elseif (str_contains(strtolower($userMessage), 'murah')) {
            $query->orderBy('tb_barang.harga', 'asc');
        }

        // Ambil 4 produk teratas
        $hasilCari = $query->limit(4)->get();

        if ($hasilCari->count() > 0) {
            $infoPencarian = "\n\nHASIL PENCARIAN DATABASE:\n";
            foreach($hasilCari as $item) {
                $hargaRupiah = number_format($item->harga, 0, ',', '.');
                // KITA BUAT URL ASLI MENUJU PRODUK
                $urlProduk = route('produk.detail', $item->slug); 
                
                // Masukkan URL ke dalam bisikan untuk POTA
                $infoPencarian .= "- Nama: {$item->nama_barang} | Harga: Rp{$hargaRupiah} | Stok: {$item->stok} | Penjual: {$item->nama_toko} | LinkAsli: {$urlProduk}\n";
            }
        } else {
            $infoPencarian = "\n\nINFO: Maaf, barang yang dicari user saat ini sedang kosong di database.";
        }

        // ====================================================================
        // GABUNGKAN PERSONA DENGAN ATURAN HTML LINK
        // ====================================================================
        
        $systemInstruction = "Kamu adalah POTA (Pondasikita Assistant) alias 'Mandor', asisten AI pintar untuk marketplace bahan bangunan Pondasikita. Gaya bahasa ramah, asik, panggil user 'Bos' atau 'Juragan'.

ATURAN SANGAT PENTING:
1. JANGAN PERNAH MENGARANG DATA. Selalu gunakan CONTEKAN DATA di bawah.
2. JIKA kamu merekomendasikan produk dari data tersebut, KAMU WAJIB mengubah nama produknya menjadi link HTML yang bisa diklik dengan format warna biru.
Gunakan format HTML ini: <a href=\"[LinkAsli]\" class=\"text-blue-600 font-black hover:underline\" target=\"_blank\">[Nama Barang]</a>

Contoh gaya bicaramu: 'Dari data yang POTA punya, produk paling mahal saat ini adalah <a href="http://localhost:8000/produk/sapu-mahal" class="text-blue-600 font-black hover:underline" target="_blank">Sapu Mahal</a> harganya Rp120.000 dijual oleh Toko Ucok.'


CONTEKAN DATA:
" . $infoToko . $infoPencarian;

        // Format History
        $formattedContents = [];
        if (is_array($chatHistory)) {
            foreach ($chatHistory as $chat) {
                if (!empty(trim($chat['text']))) {
                    $role = ($chat['sender'] === 'bot') ? 'model' : 'user';
                    $formattedContents[] = [
                        'role' => $role,
                        'parts' => [['text' => $chat['text']]]
                    ];
                }
            }
        }

        // Pesan Baru
        $formattedContents[] = [
            'role' => 'user',
            'parts' => [['text' => $userMessage]]
        ];

        try {
            $response = Http::withoutVerifying()
                ->withHeaders([
                    'Content-Type' => 'application/json',
                ])->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$apiKey}", [
                'system_instruction' => [
                    'parts' => [['text' => $systemInstruction]]
                ],
                'contents' => $formattedContents
            ]);

            if ($response->successful()) {
                $reply = $response->json('candidates.0.content.parts.0.text');
                return response()->json(['reply' => $reply]);
            } else {
                return response()->json(['reply' => 'POTA Error dari Google: ' . $response->body()], 500);
            }

        } catch (\Exception $e) {
            return response()->json(['reply' => 'Koneksi ke otak Mandor terputus: ' . $e->getMessage()], 500);
        }
    }
}