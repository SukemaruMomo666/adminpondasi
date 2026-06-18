<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CheckExpiredDp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dp:check-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Otomatisasi untuk membatalkan transaksi DP yang sudah lewat batas waktu (expired) dan mengembalikan stok.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Mencari transaksi DP yang expired...');

        // Cari transaksi yang pending, menggunakan metode DP, dan waktu sudah lewat expired_at
        $expiredTransactions = DB::table('tb_transaksi')
            ->where('tipe_pembayaran', 'DP')
            ->where('status_pembayaran', 'pending')
            ->whereNotNull('dp_expired_at')
            ->where('dp_expired_at', '<', now())
            ->get();

        if ($expiredTransactions->isEmpty()) {
            $this->info('Tidak ada transaksi DP yang expired.');
            return;
        }

        DB::beginTransaction();
        try {
            $count = 0;
            foreach ($expiredTransactions as $trx) {
                // 1. Ubah status transaksi menjadi batal/failed
                DB::table('tb_transaksi')->where('id', $trx->id)->update([
                    'status_pembayaran' => 'failed',
                    'status_pesanan_global' => 'batal',
                    'updated_at' => now(),
                    'catatan' => $trx->catatan . ' [OTOMATIS DIBATALKAN: Waktu DP Habis]'
                ]);

                // 2. Kembalikan stok untuk setiap item di detail transaksi
                $details = DB::table('tb_detail_transaksi')->where('transaksi_id', $trx->id)->get();
                
                foreach ($details as $detail) {
                    // Update status item
                    DB::table('tb_detail_transaksi')->where('id', $detail->id)->update([
                        'status_pesanan_item' => 'batal'
                    ]);

                    // Kembalikan Stok
                    DB::table('tb_barang')->where('id', $detail->barang_id)->increment('stok', $detail->jumlah);
                    
                    // Catat histori stok
                    DB::table('tb_stok_histori')->insert([
                        'barang_id' => $detail->barang_id,
                        'jumlah' => $detail->jumlah,
                        'keterangan' => 'Restock Batal DP (' . $trx->kode_invoice . ')',
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
                $count++;
            }
            DB::commit();
            $this->info("Berhasil membatalkan $count transaksi DP yang expired.");
            Log::info("Sistem Otomatisasi DP: Berhasil membatalkan $count transaksi yang expired.");
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('Gagal mengeksekusi pembatalan DP: ' . $e->getMessage());
            Log::error('Sistem Otomatisasi DP Error: ' . $e->getMessage());
        }
    }
}
