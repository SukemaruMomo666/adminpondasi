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
// 2. ROBOT SCHEDULER: ENGINE OTOMATISASI PEMBATALAN TRANSAKSI & RETUR STOK (20 MENIT)
// =========================================================================
Schedule::call(function () {
    
    // Satelit menyisir database, mencari invoice pending yang umurnya sudah lewat dari 20 menit
    $expiredOrders = DB::table('tb_transaksi')
        ->where('status_pembayaran', 'pending')
        ->where('status_pesanan_global', '!=', 'dibatalkan')
        ->where('tanggal_transaksi', '<', now()->subMinutes(20))
        ->get();

    foreach($expiredOrders as $exp) {
        DB::beginTransaction();
        try {
            // Langkah A: Ubah status transaksi global dan financial status menjadi Gagal di Master Transaksi
            DB::table('tb_transaksi')->where('id', $exp->id)->update([
                'status_pesanan_global' => 'dibatalkan',
                'status_pembayaran' => 'failed'
            ]);
            
            // Langkah B: Lacak semua item material di dalam invoice tersebut
            $items = DB::table('tb_detail_transaksi')->where('transaksi_id', $exp->id)->get();
            
            foreach($items as $item) {
                // Ubah status per item barang menjadi dibatalkan
                DB::table('tb_detail_transaksi')->where('id', $item->id)->update([
                    'status_pesanan_item' => 'dibatalkan'
                ]);
                
                // LANGKAH KRITIKAL: Kembalikan (Retur) fisik stok barang secara akurat ke etalase gudang penjual!
                DB::table('tb_barang')->where('id', $item->barang_id)->increment('stok', $item->jumlah);
            }
            
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
        }
    }

})->everyMinute(); // Menginstruksikan server untuk membangunkan robot ini setiap 60 detik sekali