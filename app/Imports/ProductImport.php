<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Str;

class ProductImport implements ToCollection, WithHeadingRow
{
    /**
     * Memproses data dari Excel baris per baris
     */
    public function collection(Collection $rows)
    {
        $toko = DB::table('tb_toko')->where('user_id', Auth::id())->first();

        if (!$toko) {
            throw new \Exception('Toko tidak ditemukan untuk user ini.');
        }

        foreach ($rows as $row) {
            // Pembersihan data dasar
            $namaBarang = trim($row['nama_barang'] ?? '');
            if (empty($namaBarang)) {
                continue;
            }

            // Fallback Kategori (Cari kategori pertama jika tidak ada ID)
            $kategoriId = $row['kategori_id'] ?? DB::table('tb_kategori')->value('id') ?? 1;

            DB::table('tb_barang')->insert([
                'toko_id'         => $toko->id,
                'kategori_id'     => $kategoriId,
                'nama_barang'     => $namaBarang,
                'kode_barang'     => $row['kode_barang'] ?? 'SKU-'.strtoupper(Str::random(6)),
                'harga'           => (float)($row['harga'] ?? 0),
                'stok'            => (int)($row['stok'] ?? 0),
                'berat_kg'        => (float)($row['berat_kg'] ?? 0.1),
                'satuan_unit'     => !empty($row['satuan_unit']) ? trim($row['satuan_unit']) : 'pcs',
                'deskripsi'       => !empty($row['deskripsi']) ? trim($row['deskripsi']) : 'Deskripsi material belum diisi.',
                'gambar_utama'    => 'default.jpg',
                'is_active'       => 0, // Off etalase secara default
                'status_moderasi' => 'approved', // Langsung approve untuk stok gudang/POS
                'created_at'      => now(),
                'updated_at'      => now(),
            ]);
        }
    }
}
