<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schedule; // Wajib di-import untuk mengaktifkan scheduler robot

// 1. Command Bawaan Laravel
Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');


// =========================================================================
// 2. ROBOT SCHEDULER: ENGINE OTOMATISASI PEMBATALAN TRANSAKSI & RETUR STOK
// =========================================================================
Schedule::call(function () {

    // Lacak 1: Transaksi Normal (Bukan DP) yang pending lebih dari 20 menit
    $expiredOrdersNormal = DB::table('tb_transaksi')
        ->where('status_pembayaran', 'pending')
        ->where('tipe_pembayaran', '!=', 'DP') // Hindari transaksi DP B2B
        ->where('status_pesanan_global', '!=', 'dibatalkan')
        ->where('status_pesanan_global', '!=', 'batal')
        ->where('tanggal_transaksi', '<', now()->subMinutes(20))
        ->get();

    // Lacak 2: Transaksi DP B2B yang sudah lewat batas waktu (dp_expired_at)
    $expiredOrdersDp = DB::table('tb_transaksi')
        ->where('status_pembayaran', 'pending')
        ->where('tipe_pembayaran', 'DP')
        ->where('status_pesanan_global', '!=', 'dibatalkan')
        ->where('status_pesanan_global', '!=', 'batal')
        ->whereNotNull('dp_expired_at')
        ->where('dp_expired_at', '<', now())
        ->get();

    // Gabungkan Koleksi
    $expiredOrders = $expiredOrdersNormal->merge($expiredOrdersDp);

    foreach($expiredOrders as $exp) {
        DB::beginTransaction();
        try {
            $catatanBatal = $exp->tipe_pembayaran == 'DP' ? ' [OTOMATIS DIBATALKAN: Waktu DP Habis]' : ' [OTOMATIS DIBATALKAN: 20 Menit Expired]';

            // Langkah A: Ubah status transaksi global dan financial status menjadi Gagal di Master Transaksi
            DB::table('tb_transaksi')->where('id', $exp->id)->update([
                'status_pesanan_global' => 'batal',
                'status_pembayaran' => 'failed',
                'catatan' => $exp->catatan . $catatanBatal
            ]);

            // Langkah B: Lacak semua item material di dalam invoice tersebut
            $items = DB::table('tb_detail_transaksi')->where('transaksi_id', $exp->id)->get();

            foreach($items as $item) {
                // Ubah status per item barang menjadi dibatalkan
                DB::table('tb_detail_transaksi')->where('id', $item->id)->update([
                    'status_pesanan_item' => 'batal'
                ]);

                // LANGKAH KRITIKAL: Kembalikan (Retur) fisik stok barang secara akurat ke etalase gudang penjual!
                DB::table('tb_barang')->where('id', $item->barang_id)->increment('stok', $item->jumlah);

                // Keamanan Sistem: Catat Retur Stok Histori
                DB::table('tb_stok_histori')->insert([
                    'barang_id' => $item->barang_id,
                    'jumlah' => $item->jumlah,
                    'keterangan' => 'Restock Batal Otomatis (' . $exp->kode_invoice . ')',
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
        }
    }

})->everyMinute(); // Menginstruksikan server untuk membangunkan robot ini setiap 60 detik sekali