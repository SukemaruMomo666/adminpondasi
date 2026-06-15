<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProductTemplateExport implements FromArray, WithHeadings, ShouldAutoSize, WithStyles
{
    /**
     * Header kolom sesuai dengan ProductImport.php
     */
    public function headings(): array
    {
        return [
            'nama_barang',
            'kategori_id',
            'kode_barang',
            'harga',
            'stok',
            'berat_kg',
            'satuan_unit',
            'deskripsi'
        ];
    }

    /**
     * Memberikan contoh baris data agar user tidak bingung
     */
    public function array(): array
    {
        return [
            [
                'Semen Tiga Roda 50kg',
                '1', // ID Kategori (biasanya 1 adalah Bahan Bangunan)
                'SMN-001',
                '65000',
                '100',
                '50',
                'sak',
                'Semen berkualitas tinggi untuk konstruksi beton.'
            ],
            [
                'Besi Beton 10mm',
                '2',
                'BSI-10MM',
                '85000',
                '500',
                '6.2',
                'batang',
                'Besi beton standar SNI panjang 12m.'
            ]
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Bold header
            1    => ['font' => ['bold' => true]],
        ];
    }
}
