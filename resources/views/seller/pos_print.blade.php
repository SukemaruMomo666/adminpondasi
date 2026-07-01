<!DOCTYPE html>
<html lang="id">
<head>
    <link rel="icon" href="{{ asset('logopondasikita.png') }}" type="image/png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk POS - {{ $transaksi->kode_invoice }}</title>
    <style>
        @page { margin: 0; }
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 11px;
            color: #000;
            margin: 0;
            padding: 10px;
            background: #fff;
            width: 80mm;
            margin-left: auto;
            margin-right: auto;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .font-bold { font-weight: bold; }
        .divider { border-top: 1px dashed #000; margin: 8px 0; }
        .table-items { width: 100%; border-collapse: collapse; margin: 5px 0; }
        .table-items td { padding: 2px 0; vertical-align: top; }
        .store-name { font-size: 16px; font-weight: bold; text-transform: uppercase; margin-bottom: 2px; }
        .store-info { font-size: 10px; margin-bottom: 5px; line-height: 1.2; }
        .invoice-info { font-size: 10px; margin-bottom: 5px; }
        
        @media print {
            body { width: 100%; padding: 5px; }
            .no-print { display: none; }
        }
    </style>
</head>
<body onload="window.print()">
    
    <div class="text-center">
        <div class="store-name">{{ $toko->nama_toko }}</div>
        <div class="store-info">
            {{ $toko->alamat_toko }}<br>
            Telp: {{ $toko->telepon_toko }}
        </div>
    </div>
    
    <div class="divider"></div>
    
    <div class="invoice-info">
        <table style="width: 100%;">
            <tr>
                <td width="30%">No. Struk</td>
                <td width="5%">:</td>
                <td>{{ $transaksi->kode_invoice }}</td>
            </tr>
            <tr>
                <td>Tanggal</td>
                <td>:</td>
                <td>{{ \Carbon\Carbon::parse($transaksi->tanggal_transaksi)->format('d/m/Y H:i') }}</td>
            </tr>
            <tr>
                <td>Kasir</td>
                <td>:</td>
                <td>{{ str_replace(['Pembelian POS (' . $toko->nama_toko . ') | Dilayani Kasir: ', 'Pembelian POS | Dilayani Kasir: '], '', $transaksi->catatan) }}</td>
            </tr>
        </table>
    </div>
    
    <div class="divider"></div>
    
    <table class="table-items">
        @foreach($details as $item)
        <tr>
            <td colspan="2" class="font-bold">{{ $item->nama_barang_saat_transaksi }}</td>
        </tr>
        <tr>
            <td width="60%">{{ $item->jumlah }} x {{ number_format($item->harga_saat_transaksi, 0, ',', '.') }}</td>
            <td width="40%" class="text-right">{{ number_format($item->subtotal, 0, ',', '.') }}</td>
        </tr>
        @endforeach
    </table>
    
    <div class="divider"></div>
    
    <table class="table-items">
        <tr>
            <td class="font-bold">TOTAL TAGIHAN</td>
            <td class="text-right font-bold">Rp {{ number_format($transaksi->total_final, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>TUNAI</td>
            <td class="text-right">Rp {{ number_format($transaksi->bayar, 0, ',', '.') }}</td>
        </tr>
        <tr style="font-size: 12px;">
            <td class="font-bold">KEMBALIAN</td>
            <td class="text-right font-bold">Rp {{ number_format($transaksi->kembali, 0, ',', '.') }}</td>
        </tr>
    </table>

    <div class="divider"></div>
    
    <div class="text-center" style="margin-top: 15px; font-size: 9px; line-height: 1.4;">
        *** TERIMA KASIH ***<br>
        Barang yang sudah dibeli tidak dapat<br>
        ditukar atau dikembalikan.<br>
        <br>
        Powered by Pondasikita
    </div>

</body>
</html>

