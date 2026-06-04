<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // WAJIB ADA untuk query DB::table
use Midtrans\Config; // WAJIB ADA untuk Midtrans
use Midtrans\Snap;   // WAJIB ADA untuk Midtrans

class TransactionController extends Controller
{
    // --- 1. FUNGSI MENAMPILKAN RIWAYAT PESANAN (KODEMU) ---
    public function userOrders(Request $request)
    {
        $userId = $request->user()->id;

        $orders = DB::table('tb_detail_transaksi as dt')
            ->join('tb_transaksi as t', 'dt.transaksi_id', '=', 't.id')
            ->join('tb_barang as b', 'dt.barang_id', '=', 'b.id')
            ->join('tb_toko as tk', 'b.toko_id', '=', 'tk.id')
            ->select(
                't.id',
                't.invoice_no', 
                'tk.nama_toko',
                't.status_pesanan', 
                'b.nama_barang',
                'b.harga',
                'dt.jumlah as qty',
                't.total_harga',
                'b.gambar_utama'
            )
            ->where('t.user_id', $userId)
            ->orderBy('t.created_at', 'DESC')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $orders
        ], 200);
    }   

    // --- 2. FUNGSI BARU UNTUK PROSES CHECKOUT & MIDTRANS ---
    public function checkout(Request $request)
    {
        $user = $request->user();
        
        // Kita gunakan try-catch agar kalau error tidak blank 500, tapi ketahuan alasannya
        DB::beginTransaction();
        try {
            // 1. Buat Invoice Unik (Biar tidak bentrok di Midtrans)
            $invoiceNo = 'INV-' . time() . '-' . $user->id;
            $totalPembayaran = $request->total_pembayaran;

            // 2. Simpan ke tb_transaksi
            $transaksiId = DB::table('tb_transaksi')->insertGetId([
                'user_id' => $user->id,
                'invoice_no' => $invoiceNo,
                'status_pesanan' => 'pending', // Status awal sebelum dibayar
                'total_harga' => $totalPembayaran,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // 3. Simpan barang-barang ke tb_detail_transaksi
            $items = $request->items; // Mengambil array barang dari React Native
            foreach ($items as $item) {
                DB::table('tb_detail_transaksi')->insert([
                    'transaksi_id' => $transaksiId,
                    'barang_id' => $item['id'], 
                    'jumlah' => $item['qty'],
                    // 'harga' => $item['harga'], // Buka komen ini jika ada kolom harga di detail
                ]);
            }

            DB::commit(); // Simpan permanen ke database

            // 4. Konfigurasi Midtrans
            // (Pastikan MIDTRANS_SERVER_KEY sudah diisi di file .env server Hostinger)
            Config::$serverKey = env('MIDTRANS_SERVER_KEY', 'SB-Mid-server-xxxxxxxxx');
            Config::$isProduction = env('MIDTRANS_IS_PRODUCTION', false);
            Config::$isSanitized = true;
            Config::$is3ds = true;

            // 5. Racik Parameter untuk dikirim ke Midtrans
            $params = [
                'transaction_details' => [
                    'order_id' => $invoiceNo,
                    'gross_amount' => (int) $totalPembayaran, // Wajib angka bulat
                ],
                'customer_details' => [
                    'first_name' => $user->name ?? $user->nama_lengkap ?? 'Customer',
                    'email' => $user->email ?? 'no-email@pondasikita.com',
                    'phone' => $user->telepon ?? $user->no_hp ?? '081234567890',
                ],
                'callbacks' => [
                    // Ini link yang akan dicegat oleh React Native kita
                    'finish' => 'https://www.pondasikita.com/pesanan' 
                ]
            ];

            // 6. Minta URL Snap dari Midtrans
            $snapToken = Snap::getSnapToken($params);
            
            // Tentukan URL berdasarkan environment (Sandbox / Production)
            $isProd = env('MIDTRANS_IS_PRODUCTION', false);
            $baseUrl = $isProd ? 'https://app.midtrans.com/snap/v2/vtweb/' : 'https://app.sandbox.midtrans.com/snap/v2/vtweb/';

            return response()->json([
                'status' => 'success',
                'redirect_url' => $baseUrl . $snapToken
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack(); // Batalkan simpan DB jika ada error
            
            // Lempar error spesifik ke layar HP agar mudah di-debug
            return response()->json([
                'status' => 'error',
                'message' => 'Error Laravel: ' . $e->getMessage() . ' di baris ' . $e->getLine()
            ], 500);
        }
    }
}