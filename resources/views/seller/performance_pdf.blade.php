<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Performa - {{ $toko->nama_toko }}</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; color: #333; margin: 0; padding: 20px; font-size: 14px; }
        .header { text-align: center; border-bottom: 2px solid #2563eb; padding-bottom: 20px; margin-bottom: 30px; }
        .header h1 { margin: 0; color: #1e3a8a; font-size: 24px; text-transform: uppercase; letter-spacing: 1px; }
        .header p { margin: 5px 0 0; color: #64748b; font-size: 12px; }
        .section-title { font-size: 16px; font-weight: bold; border-bottom: 1px solid #e2e8f0; padding-bottom: 8px; margin-bottom: 15px; color: #1e293b; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        th, td { padding: 12px 15px; text-align: left; border-bottom: 1px solid #e2e8f0; }
        th { background-color: #f8fafc; color: #475569; font-weight: bold; text-transform: uppercase; font-size: 12px; }
        td { font-size: 14px; color: #334155; }
        .text-right { text-align: right; }
        .metric-value { font-weight: bold; color: #2563eb; }
        .footer { text-align: center; margin-top: 50px; font-size: 10px; color: #94a3b8; border-top: 1px solid #e2e8f0; padding-top: 15px; }
    </style>
</head>
<body>

    <div class="header">
        <h1>Laporan Performa Toko</h1>
        <p>{{ $toko->nama_toko }} | Tanggal Cetak: {{ \Carbon\Carbon::now()->format('d M Y, H:i') }} WIB</p>
    </div>

    <div class="section-title">Ringkasan Kinerja Keseluruhan</div>
    <table>
        <thead>
            <tr>
                <th>Indikator Kinerja Utama (KPI)</th>
                <th class="text-right">Nilai Saat Ini</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Total Pendapatan (Penjualan Bersih)</td>
                <td class="text-right metric-value">Rp {{ number_format($kriteria['penjualan'], 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Jumlah Pesanan Berhasil</td>
                <td class="text-right metric-value">{{ number_format($kriteria['pesanan'], 0, ',', '.') }} Pesanan</td>
            </tr>
            <tr>
                <td>Total Pengunjung / Pembeli Unik</td>
                <td class="text-right metric-value">{{ number_format($kriteria['pengunjung'], 0, ',', '.') }} Akun</td>
            </tr>
            <tr>
                <td>Tingkat Konversi Pembelian</td>
                <td class="text-right metric-value">{{ $kriteria['tingkat_konversi'] }}%</td>
            </tr>
        </tbody>
    </table>

    <div class="section-title">Informasi Tambahan</div>
    <table>
        <tbody>
            <tr>
                <td style="width: 30%;">Status Operasional Toko</td>
                <td><strong>{{ $toko->status_operasional ?? 'Buka' }}</strong></td>
            </tr>
            <tr>
                <td>Tier Kemitraan</td>
                <td style="text-transform: capitalize;"><strong>{{ str_replace('_', ' ', $toko->tier_toko ?? 'Regular') }}</strong></td>
            </tr>
            <tr>
                <td>Lokasi / Kota</td>
                <td><strong>{{ $toko->kota ?? 'Nasional' }}</strong></td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        Dokumen ini dibuat secara otomatis oleh Sistem B2B Pondasikita pada {{ \Carbon\Carbon::now()->format('d F Y') }}.<br>
        Data yang ditampilkan adalah rekapitulasi real-time berdasarkan transaksi yang berstatus "Selesai".
    </div>

</body>
</html>
