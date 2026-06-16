<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Kesehatan Toko - {{ $toko->nama_toko }}</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; color: #333; margin: 0; padding: 20px; font-size: 14px; }
        .header { text-align: center; border-bottom: 2px solid #10b981; padding-bottom: 20px; margin-bottom: 30px; }
        .header h1 { margin: 0; color: #047857; font-size: 24px; text-transform: uppercase; letter-spacing: 1px; }
        .header p { margin: 5px 0 0; color: #64748b; font-size: 12px; }
        .section-title { font-size: 16px; font-weight: bold; border-bottom: 1px solid #e2e8f0; padding-bottom: 8px; margin-bottom: 15px; color: #1e293b; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        th, td { padding: 12px 15px; text-align: left; border-bottom: 1px solid #e2e8f0; }
        th { background-color: #f8fafc; color: #475569; font-weight: bold; text-transform: uppercase; font-size: 12px; }
        td { font-size: 14px; color: #334155; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .metric-value { font-weight: bold; }
        .text-emerald { color: #10b981; }
        .text-red { color: #ef4444; }
        .footer { text-align: center; margin-top: 50px; font-size: 10px; color: #94a3b8; border-top: 1px solid #e2e8f0; padding-top: 15px; }
        .status-box { background-color: #f8fafc; border: 1px solid #e2e8f0; padding: 15px; border-radius: 8px; text-align: center; margin-bottom: 30px; }
        .status-box h2 { margin: 0 0 5px; font-size: 20px; }
        .status-box p { margin: 0; font-size: 12px; color: #64748b; }
    </style>
</head>
<body>

    <div class="header">
        <h1>Laporan Kesehatan Toko</h1>
        <p>{{ $toko->nama_toko }} | Tanggal Cetak: {{ \Carbon\Carbon::now()->format('d M Y, H:i') }} WIB</p>
    </div>

    <div class="status-box">
        <h2 class="{{ $status_kesehatan == 'Sangat baik' ? 'text-emerald' : 'text-red' }}">
            Status: {{ strtoupper($status_kesehatan) }}
        </h2>
        <p>Evaluasi menyeluruh terhadap performa layanan dan kepatuhan toko terhadap kebijakan.</p>
    </div>

    <div class="section-title">Ringkasan Isu & Pelanggaran</div>
    <table>
        <thead>
            <tr>
                <th>Indikator Kesehatan</th>
                <th class="text-center">Jumlah Kasus</th>
                <th class="text-right">Persentase Risiko</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Pesanan Gagal / Dibatalkan (Tidak Terpenuhi)</td>
                <td class="text-center metric-value {{ $pesananGagal > 0 ? 'text-red' : 'text-emerald' }}">{{ $pesananGagal }} Kasus</td>
                <td class="text-right metric-value {{ $persentaseGagal > 10 ? 'text-red' : 'text-emerald' }}">{{ $persentaseGagal }}%</td>
            </tr>
            <tr>
                <td>Produk Dilarang (Ditolak Moderasi)</td>
                <td class="text-center metric-value {{ $produkDilarang > 0 ? 'text-red' : 'text-emerald' }}">{{ $produkDilarang }} Kasus</td>
                <td class="text-right metric-value">-</td>
            </tr>
            <tr>
                <td>Poin Penalti Terakumulasi (Kuartal Ini)</td>
                <td class="text-center metric-value {{ $poin_penalti_kuartal_ini > 0 ? 'text-red' : 'text-emerald' }}">{{ $poin_penalti_kuartal_ini }} Poin</td>
                <td class="text-right metric-value">-</td>
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
                <td>Peringatan</td>
                <td>
                    @if($status_kesehatan == 'Sangat baik')
                        <span class="text-emerald">Toko Anda dalam kondisi prima. Terus pertahankan!</span>
                    @else
                        <span class="text-red">Terdapat metrik yang melampaui batas aman. Segera perbaiki untuk menghindari pembatasan fitur.</span>
                    @endif
                </td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        Dokumen ini dibuat secara otomatis oleh Sistem B2B Pondasikita pada {{ \Carbon\Carbon::now()->format('d F Y') }}.<br>
        Data yang ditampilkan adalah rekapitulasi real-time dari sistem moderasi dan pelacakan transaksi.
    </div>

</body>
</html>
