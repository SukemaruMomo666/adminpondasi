<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http; // KUNCI: Wajib di-import untuk hit API Biteship

class WebhookController extends Controller
{
    /**
     * Handler untuk menerima notifikasi otomatis dari Midtrans (Webhook)
     */
    public function midtransHandler(Request $request)
    {
        // 1. Ambil Server Key dari tabel pengaturan
        $serverKey = DB::table('tb_pengaturan')->where('setting_nama', 'midtrans_server_key')->value('setting_nilai');

        // 2. Tangkap seluruh data Payload (Notifikasi) yang dikirim Midtrans
        $payload = $request->all();
        
        $orderId = $payload['order_id'] ?? '';
        $statusCode = $payload['status_code'] ?? '';
        $grossAmount = $payload['gross_amount'] ?? '';
        $signatureKey = $payload['signature_key'] ?? '';
        $transactionStatus = $payload['transaction_status'] ?? '';
        $fraudStatus = $payload['fraud_status'] ?? 'accept';

        // 3. RUMUS KEAMANAN (Validasi Keaslian Signature Key)
        $mySignature = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);

        if ($mySignature !== $signatureKey) {
            Log::warning('MIDTRANS HACK ATTEMPT / INVALID SIGNATURE: ' . $orderId);
            return response()->json(['status' => 'error', 'message' => 'Invalid Signature'], 403);
        }

        // 4. Cari Data Transaksi di Database
        $transaksi = DB::table('tb_transaksi')->where('kode_invoice', $orderId)->first();
        
        if (!$transaksi) {
            Log::error('MIDTRANS ERROR: Pesanan tidak ditemukan - ' . $orderId);
            return response()->json(['status' => 'error', 'message' => 'Pesanan tidak ditemukan'], 404);
        }

        // Mencegah stok berkurang/bertambah 2x lipat jika Midtrans mengirim notif berulang
        if (in_array($transaksi->status_pembayaran, ['paid', 'failed'])) {
            return response()->json(['status' => 'success', 'message' => 'Notifikasi sudah diproses sebelumnya'], 200);
        }

        // 5. Mulai Proses Perubahan Status & Manajemen Stok
        DB::beginTransaction();
        try {
            // =========================================================================
            // A. JIKA PEMBAYARAN BERHASIL (LUNAS)
            // =========================================================================
            if ($transactionStatus == 'capture' || $transactionStatus == 'settlement') {
                if ($fraudStatus == 'accept') {
                    
                    // Update Status Pesanan Jadi Lunas & Diproses
                    DB::table('tb_transaksi')->where('id', $transaksi->id)->update([
                        'status_pembayaran' => 'paid',
                        'status_pesanan_global' => 'diproses',
                        'updated_at' => now()
                    ]);

                    // --- FITUR NGURANGIN STOK BARANG ---
                    $items = DB::table('tb_detail_transaksi')->where('transaksi_id', $transaksi->id)->get();
                    
                    foreach ($items as $item) {
                        DB::table('tb_barang')
                            ->where('id', $item->barang_id)
                            ->decrement('stok', $item->jumlah);
                    }

                    // --- FITUR AUTO-ORDER KE BITESHIP TINGKAT DEWA ---
                    if ($transaksi->tipe_pengambilan === 'pengiriman') {
                        $this->panggilKurirBiteshipOtomatis($transaksi, $items);
                    }
                }
            } 
            // =========================================================================
            // B. JIKA PEMBAYARAN GAGAL / KADALUWARSA / DIBATALKAN
            // =========================================================================
            else if ($transactionStatus == 'cancel' || $transactionStatus == 'deny' || $transactionStatus == 'expire') {
                
                DB::table('tb_transaksi')->where('id', $transaksi->id)->update([
                    'status_pembayaran' => 'failed',
                    'status_pesanan_global' => 'dibatalkan',
                    'updated_at' => now()
                ]);

                DB::table('tb_detail_transaksi')->where('transaksi_id', $transaksi->id)->update([
                    'status_pesanan_item' => 'dibatalkan',
                    'updated_at' => now()
                ]);

                // FITUR RETURN BARANG (KEMBALIKAN STOK)
                /*
                $items = DB::table('tb_detail_transaksi')->where('transaksi_id', $transaksi->id)->get();
                foreach ($items as $item) {
                    DB::table('tb_barang')->where('id', $item->barang_id)->increment('stok', $item->jumlah);
                }
                */
            }

            DB::commit();
            Log::info('MIDTRANS PAYMENT SUCCESS: Invoice ' . $orderId . ' is now ' . $transactionStatus);
            
            return response()->json(['status' => 'success', 'message' => 'Webhook berhasil diproses'], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('MIDTRANS WEBHOOK DATABASE ERROR: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Sistem gagal memproses database'], 500);
        }
    }


    /**
     * PRIVATE FUNCTION: Memanggil Kurir dari Biteship Secara Otomatis
     */
    private function panggilKurirBiteshipOtomatis($transaksi, $detailItems)
    {
        try {
            $apiKey = DB::table('tb_pengaturan')->where('setting_nama', 'biteship_api_key')->value('setting_nilai');
            if (empty($apiKey)) return;

            $user = DB::table('tb_user')->where('id', $transaksi->user_id)->first();
            
            // 1. Group barang berdasarkan Toko (Untuk mendukung Multi-Toko Checkout)
            $groupedItems = collect($detailItems)->groupBy('toko_id');
            $resiList = [];

            foreach ($groupedItems as $tokoId => $storeItems) {
                $toko = DB::table('tb_toko')->where('id', $tokoId)->first();
                if (!$toko) continue;

                // 2. Ekstrak nama kurir yang dipilih user dari kolom catatan (Contoh: "TokoID-1: SICEPAT")
                $courierCompany = 'jne'; // Default
                preg_match("/TokoID-{$tokoId}:\s*([A-Za-z]+)/i", $transaksi->catatan, $matches);
                if (!empty($matches[1])) {
                    $courierCompany = strtolower($matches[1]);
                }

                // 3. Susun data items untuk Biteship
                $biteshipItems = [];
                foreach ($storeItems as $item) {
                    $biteshipItems[] = [
                        'name' => $item->nama_barang_saat_transaksi,
                        'value' => (int) $item->harga_saat_transaksi,
                        'weight' => 1000 * $item->jumlah, // Asumsi 1Kg per item
                        'quantity' => (int) $item->jumlah
                    ];
                }

                // 4. Hit Endpoint Create Order Biteship
                $response = Http::withHeaders([
                    'Authorization' => $apiKey,
                    'Content-Type'  => 'application/json'
                ])->post('https://api.biteship.com/v1/orders', [
                    'shipper_contact_name'      => $toko->nama_toko,
                    'shipper_contact_phone'     => $toko->no_telepon ?? '081234567890',
                    'shipper_contact_email'     => 'seller@pondasikita.com',
                    'shipper_organization'      => 'Pondasikita Seller',
                    'origin_area_id'            => 'IDNP3171011001', // TODO: Ambil dari DB Toko
                    
                    'destination_contact_name'  => $transaksi->shipping_nama_penerima,
                    'destination_contact_phone' => $transaksi->shipping_telepon_penerima,
                    'destination_contact_email' => $user->email ?? 'buyer@pondasikita.com',
                    'destination_address'       => $transaksi->shipping_alamat_lengkap,
                    'destination_area_id'       => 'IDNP3171011001', // TODO: Ambil dari DB Transaksi
                    'destination_postal_code'   => (int) $transaksi->shipping_kode_pos,
                    
                    'courier_company'           => $courierCompany,
                    'courier_type'              => 'standard',
                    'delivery_type'             => 'now',
                    'items'                     => $biteshipItems
                ]);

                // 5. Tangkap Nomor Resi (Waybill)
                if ($response->successful()) {
                    $resData = $response->json();
                    if (!empty($resData['courier']['waybill_id'])) {
                        $resiList[] = strtoupper($courierCompany) . '-' . $resData['courier']['waybill_id'];
                    }
                } else {
                    Log::error("BITESHIP CREATE ORDER ERROR (TOKO {$tokoId}): " . $response->body());
                }
            }

            // 6. Simpan seluruh Nomor Resi ke transaksi
            if (!empty($resiList)) {
                DB::table('tb_transaksi')->where('id', $transaksi->id)->update([
                    'nomor_resi' => implode(', ', $resiList)
                ]);
            }

        } catch (\Exception $e) {
            Log::error("BITESHIP AUTO-ORDER EXCEPTION: " . $e->getMessage());
        }
    }
}