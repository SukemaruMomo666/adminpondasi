<!DOCTYPE html>
<html lang="id">
<head>
    <link rel="icon" href="{{ asset('logopondasikita.png') }}" type="image/png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Transaksi #{{ $order->kode_invoice }} - Pondasikita</title>

    {{-- Tailwind CSS CDN + Config Dewa --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                    colors: {
                        brand: { 50: '#eff6ff', 100: '#dbeafe', 500: '#3b82f6', 600: '#2563eb', 700: '#1d4ed8', 950: '#020617' },
                    },
                    boxShadow: {
                        'premium': '0 20px 50px -12px rgba(0,0,0,0.05)',
                        'glow': '0 0 25px rgba(37,99,235,0.25)',
                        'glow-red': '0 0 25px rgba(239,68,68,0.3)',
                    }
                }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
    <style>
        body { background-color: #f8fafc; scroll-behavior: smooth; }

        /* Timeline Dashed Effect */
        .timeline-container::before {
            content: '';
            position: absolute;
            left: 23px;
            top: 10px;
            bottom: 10px;
            width: 2px;
            background: repeating-linear-gradient(to bottom, #e2e8f0 0, #e2e8f0 4px, transparent 4px, transparent 8px);
        }

        @keyframes pulse-ring {
            0% { transform: scale(.33); opacity: 0.8; }
            80%, 100% { opacity: 0; transform: scale(3); }
        }

        .pulse-active { position: relative; }
        .pulse-active::before {
            content: '';
            position: absolute;
            width: 100%;
            height: 100%;
            border-radius: 50%;
            background-color: #3b82f6;
            animation: pulse-ring 1.5s cubic-bezier(0.215, 0.61, 0.355, 1) infinite;
        }

        .pulse-red::before {
            background-color: #ef4444;
        }

        /* Smooth Card Transition */
        .card-hover-effect { transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1); }
        .card-hover-effect:hover { transform: translateY(-5px); box-shadow: 0 30px 60px -12px rgba(0,0,0,0.1); }
    </style>
</head>
<body class="text-zinc-900 antialiased pt-[90px] pb-20">

    @include('partials.navbar')

    @php
        // ==============================================================
        // LOGIKA WAKTU KEDALUWARSA (20 MENIT UNTUK MENGAMANKAN STOK)
        // ==============================================================
        $isPending = ($order->status_pembayaran == 'pending' && $order->status_pesanan_global != 'dibatalkan');
        
        $waktuTransaksi = \Carbon\Carbon::parse($order->tanggal_transaksi);
        $waktuKedaluwarsa = $waktuTransaksi->copy()->addMinutes(20);
        $sisaDetik = 0;

        if ($isPending) {
            $sisaDetik = now()->diffInSeconds($waktuKedaluwarsa, false);
            // Jika waktu sudah lewat (minus), kita set jadi 0
            if ($sisaDetik < 0) $sisaDetik = 0;
        }
    @endphp

    <main class="max-w-[1250px] mx-auto px-4 sm:px-6">

        {{-- TOP HEADER: B2B STATUS BAR --}}
        <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-8 mb-12 animate-fade-in">
            <div class="space-y-3">
                <a href="{{ route('pesanan.index') }}" class="group inline-flex items-center text-[10px] font-black text-zinc-400 uppercase tracking-[0.2em] hover:text-blue-600 transition-all">
                    <i class="fas fa-chevron-left mr-2 group-hover:-translate-x-1 transition-transform text-[8px]"></i> Back to Dashboard
                </a>
                <div class="flex items-center gap-4">
                    <h1 class="text-3xl lg:text-4xl font-black tracking-tight text-zinc-950">Detail Transaksi</h1>
                    <span class="px-4 py-1.5 rounded-full bg-zinc-100 border border-zinc-200 text-[10px] font-black text-zinc-500 uppercase tracking-widest">#{{ $order->kode_invoice }}</span>
                </div>
            </div>

            <div class="flex items-center gap-6 bg-white p-4 rounded-[2rem] shadow-premium border border-zinc-100">
                <div class="text-right">
                    <span class="block text-[9px] font-black text-zinc-400 uppercase tracking-widest mb-1">Financial Status</span>
                    <span class="text-sm font-black {{ $order->status_pembayaran == 'paid' ? 'text-emerald-600' : ($order->status_pembayaran == 'failed' ? 'text-red-500' : 'text-amber-500') }} uppercase flex items-center gap-2 justify-end">
                        <span class="w-2 h-2 rounded-full {{ $order->status_pembayaran == 'paid' ? 'bg-emerald-500 animate-pulse' : ($order->status_pembayaran == 'failed' ? 'bg-red-500' : 'bg-amber-500') }}"></span>
                        @if($order->status_pembayaran == 'paid')
                            Lunas Terverifikasi
                        @elseif($order->status_pembayaran == 'failed')
                            Transaksi Batal
                        @else
                            Menunggu Pembayaran
                        @endif
                    </span>
                </div>
                <div class="w-14 h-14 rounded-2xl {{ $order->status_pembayaran == 'paid' ? 'bg-emerald-600 text-white' : ($order->status_pembayaran == 'failed' ? 'bg-red-600 text-white' : 'bg-amber-500 text-white') }} flex items-center justify-center shadow-lg transform rotate-3">
                    <i class="fas {{ $order->status_pembayaran == 'paid' ? 'fa-check-double' : ($order->status_pembayaran == 'failed' ? 'fa-times-circle' : 'fa-hourglass-half') }} text-xl"></i>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-start">

            {{-- ======================================================= --}}
            {{-- LEFT COLUMN: SHIPMENT & INVENTORY (Span 8) --}}
            {{-- ======================================================= --}}
            <div class="lg:col-span-8 space-y-10">

                {{-- 1. LOGISTICS TIMELINE --}}
                <div class="bg-white rounded-[3rem] shadow-premium border border-zinc-200/50 p-8 lg:p-12 relative overflow-hidden">
                    <div class="flex items-center justify-between mb-12">
                        <h2 class="text-xl font-black text-zinc-900 flex items-center gap-4">
                            <i class="fas fa-map-location-dot text-blue-600"></i>
                            Tracking Logistik
                        </h2>
                        <div class="flex items-center gap-2 text-[10px] font-bold text-zinc-400 bg-zinc-50 px-3 py-1 rounded-full border border-zinc-100">
                            <i class="fas fa-truck-fast"></i> Update Otomatis
                        </div>
                    </div>

                    <div class="relative timeline-container space-y-12">
                        @foreach($trackingLogs as $index => $log)
                        <div class="relative pl-16 group">
                            {{-- Dot with Pulse Animation --}}
                            <div class="absolute left-0 top-1.5 w-12 h-12 -translate-x-1/2 flex items-center justify-center z-10">
                                <div class="w-5 h-5 rounded-full border-[5px] border-white shadow-md transition-all duration-500 {{ $index == 0 ? 'bg-blue-600 pulse-active scale-110' : 'bg-zinc-200 group-hover:bg-zinc-400' }}"></div>
                            </div>

                            <div class="bg-zinc-50/40 group-hover:bg-white border border-transparent group-hover:border-zinc-200 p-6 rounded-[2rem] transition-all duration-500 hover:shadow-xl">
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-3">
                                    <h4 class="text-base font-black {{ $index == 0 ? 'text-blue-600' : 'text-zinc-800' }} uppercase tracking-wide">
                                        {{ $log['status'] }}
                                    </h4>
                                    <div class="flex items-center gap-2 text-[11px] font-black text-zinc-400">
                                        <i class="far fa-clock"></i>
                                        {{ \Carbon\Carbon::parse($log['time'])->format('d M Y, H:i') }}
                                    </div>
                                </div>
                                <p class="text-sm font-medium text-zinc-500 leading-relaxed">{{ $log['desc'] }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- 2. INVENTORY LIST --}}
                <div class="bg-white rounded-[3rem] shadow-premium border border-zinc-200/50 p-8 lg:p-12">
                    <div class="flex items-center gap-4 mb-10 pb-6 border-b border-zinc-100">
                        <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl">
                            <i class="fas fa-boxes-stacked"></i>
                        </div>
                        <h2 class="text-xl font-black text-zinc-900">Manifest Barang</h2>
                    </div>

                    <div class="space-y-4">
                        @foreach($items as $item)
                        <div class="flex items-center gap-6 p-5 rounded-[2rem] hover:bg-zinc-50 border border-transparent hover:border-zinc-200 transition-all duration-300 group">

                            {{-- FOTO PRODUK DINAMIS --}}
                            <div class="w-24 h-24 rounded-3xl bg-zinc-100 overflow-hidden border border-zinc-200 flex-shrink-0 relative">
                                @php
                                    $fotoProduk = !empty($item->gambar_utama) ? $item->gambar_utama : ($item->gambar_saat_transaksi ?? 'default.jpg');
                                @endphp
                                <img src="{{ asset('assets/uploads/products/' . $fotoProduk) }}"
                                     class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700 mix-blend-multiply"
                                     onerror="this.onerror=null; this.src='{{ asset('assets/uploads/products/default.jpg') }}';">

                                <div class="absolute bottom-2 right-2 bg-black text-white text-[10px] font-black px-2 py-1 rounded-lg">x{{ $item->jumlah }}</div>
                            </div>

                            <div class="flex-1 min-w-0">
                                <span class="text-[9px] font-black text-blue-500 uppercase tracking-[0.1em] mb-1 block">SKU Terverifikasi</span>
                                <h3 class="text-base font-black text-zinc-900 truncate mb-1 group-hover:text-blue-600 transition-colors">{{ $item->nama_barang_saat_transaksi }}</h3>
                                <p class="text-xs font-bold text-zinc-400 uppercase tracking-widest">Harga Satuan: Rp{{ number_format($item->harga_saat_transaksi, 0, ',', '.') }}</p>
                            </div>
                            <div class="text-right flex flex-col items-end gap-2">
                                <div class="hidden sm:block">
                                    <span class="block text-[9px] font-black text-zinc-400 uppercase tracking-widest mb-1">Subtotal</span>
                                    <span class="text-lg font-black text-zinc-950 tracking-tighter leading-none">Rp{{ number_format($item->subtotal, 0, ',', '.') }}</span>
                                </div>

                                {{-- FITUR DEWA: Tombol Ulasan --}}
                                @if($order->status_pesanan_global == 'selesai')
                                    @php
                                        $diffDays = now()->diffInDays(\Carbon\Carbon::parse($order->updated_at));
                                        $isReviewed = in_array($item->id, $reviewed_items ?? []);
                                    @endphp

                                    @if($isReviewed)
                                        <span class="px-3 py-1.5 bg-emerald-50 text-emerald-600 text-[10px] font-black uppercase rounded-xl border border-emerald-100">
                                            <i class="fas fa-check-circle mr-1"></i> Sudah Diulas
                                        </span>
                                    @elseif($diffDays <= 14)
                                        <button type="button" onclick="openReviewModal({{ $item->id }}, '{{ addslashes($item->nama_barang_saat_transaksi) }}')" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-[10px] font-black uppercase tracking-widest rounded-xl transition-all shadow-md shadow-blue-500/20 active:scale-95">
                                            <i class="fas fa-star mr-1"></i> Tulis Ulasan
                                        </button>
                                    @else
                                        <span class="text-[9px] font-bold text-zinc-300 uppercase italic">Waktu Ulasan Habis</span>
                                    @endif
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- FITUR BARU: MODAL / KOTAK PENGAJUAN KOMPLAIN JIKA LUNAS --}}
                @if($order->status_pembayaran == 'paid' && !in_array($order->status_pesanan_global, ['selesai', 'komplain']))
                <div class="bg-white rounded-[3rem] shadow-premium border border-zinc-200/50 p-8 lg:p-12">
                    <details class="group">
                        <summary class="flex items-center justify-between font-black text-zinc-700 cursor-pointer list-none outline-none">
                            <span class="flex items-center gap-3 text-base text-zinc-900"><i class="fas fa-shield-alert text-amber-500"></i> Ajukan Masalah / Retur Material</span>
                            <span class="transition group-open:rotate-180"><i class="fas fa-chevron-down text-zinc-400"></i></span>
                        </summary>
                        <div class="mt-6 pt-6 border-t border-zinc-100 animate-fade-in">
                            <form action="{{ url('/pesanan/komplain') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                                @csrf
                                <input type="hidden" name="order_id" value="{{ $order->kode_invoice }}">
                                <div>
                                    <label class="block text-xs font-bold text-zinc-500 uppercase tracking-wider mb-2">Alasan Pengembalian / Kendala Lapangan</label>
                                    <textarea name="alasan" rows="3" class="w-full bg-zinc-50 border border-zinc-200 rounded-2xl p-4 text-sm font-semibold outline-none focus:border-blue-600" placeholder="Jelaskan detail kendala (Cth: Semen membatu, Besi karat, jumlah kurang)..." required></textarea>
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 items-center">
                                    <div>
                                        <label class="block text-xs font-bold text-zinc-500 uppercase tracking-wider mb-2">Unggah Bukti Foto Fisik</label>
                                        <input type="file" name="bukti_foto" class="w-full text-xs font-bold text-zinc-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-black file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" required>
                                    </div>
                                    <div class="text-right pt-4 sm:pt-0">
                                        <button type="submit" class="w-full sm:w-auto px-6 py-3 bg-zinc-900 hover:bg-red-600 text-white text-xs font-black uppercase tracking-wider rounded-xl transition-colors">Kirim Aduan Resmi</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </details>
                </div>
                @endif
            </div>

            {{-- ======================================================= --}}
            {{-- RIGHT COLUMN: BILLING & SUPPORT (Span 4) --}}
            {{-- ======================================================= --}}
            <div class="lg:col-span-4 space-y-8 lg:sticky lg:top-24">

                {{-- 3. PREMIUM INVOICE CARD (DARK MODE) --}}
                <div class="bg-zinc-950 rounded-[3rem] p-10 shadow-2xl relative overflow-hidden text-white border border-white/5">
                    {{-- Blue Light Flare --}}
                    <div class="absolute -top-20 -right-20 w-64 h-64 bg-blue-600/30 rounded-full blur-[80px]"></div>
                    <div class="absolute -bottom-10 -left-10 w-40 h-40 bg-indigo-600/20 rounded-full blur-[60px]"></div>

                    <div class="relative z-10">
                        <div class="flex items-center gap-3 mb-10 opacity-60">
                            <i class="fas fa-file-invoice-dollar"></i>
                            <h3 class="text-[10px] font-black uppercase tracking-[0.3em]">Billing Summary</h3>
                        </div>

                        <div class="space-y-5 mb-10">
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-zinc-500 font-medium tracking-wide">Produk Subtotal</span>
                                <span class="font-black text-zinc-300 tracking-tight text-right">Rp{{ number_format($order->total_harga_produk, 0, ',', '.') }}</span>
                            </div>
                            
                            @if($order->total_diskon > 0)
                            <div class="flex justify-between items-center text-sm text-emerald-400">
                                <span class="font-medium tracking-wide">Diskon Promo</span>
                                <span class="font-black tracking-tight text-right">- Rp{{ number_format($order->total_diskon, 0, ',', '.') }}</span>
                            </div>
                            @endif

                            <div class="flex justify-between items-center text-sm">
                                <span class="text-zinc-500 font-medium tracking-wide">Biaya Logistik</span>
                                <span class="font-black text-zinc-300 tracking-tight text-right">Rp{{ number_format($order->biaya_pengiriman, 0, ',', '.') }}</span>
                            </div>
                            <div class="pt-6 border-t border-white/10 mt-6 space-y-4">
                                @if($order->tipe_pembayaran == 'DP')
                                    {{-- Khusus Sistem DP B2B --}}
                                    <div class="bg-amber-500/10 border border-amber-500/20 rounded-2xl p-4">
                                        <div class="flex justify-between items-center text-xs mb-3">
                                            <span class="font-black text-amber-500 uppercase tracking-widest"><i class="fas fa-handshake mr-1.5"></i> Sistem B2B DP ({{ round(($order->jumlah_dp / $order->total_final) * 100) }}%)</span>
                                            <span class="px-2 py-0.5 bg-amber-500 text-white text-[9px] font-black rounded uppercase">Aktif</span>
                                        </div>
                                        <div class="space-y-2">
                                            <div class="flex justify-between items-center text-sm">
                                                <span class="text-zinc-400 font-medium text-xs uppercase tracking-wide">Uang Muka (DP)</span>
                                                <span class="font-black text-white tracking-tight">Rp{{ number_format($order->jumlah_dp, 0, ',', '.') }}</span>
                                            </div>
                                            <div class="flex justify-between items-center text-sm pt-2 border-t border-white/5">
                                                <span class="text-zinc-400 font-medium text-xs uppercase tracking-wide">Sisa Pelunasan</span>
                                                <span class="font-black text-rose-500 tracking-tight">Rp{{ number_format($order->sisa_tagihan, 0, ',', '.') }}</span>
                                            </div>
                                        </div>
                                        <div class="mt-3 text-[10px] text-zinc-500 font-bold leading-tight italic">
                                            * Sisa pelunasan dilakukan secara tunai/transfer langsung ke Penjual saat barang diterima.
                                        </div>
                                    </div>
                                    <div class="flex justify-between items-end bg-blue-600/10 border border-blue-600/20 p-4 rounded-2xl">
                                        <span class="text-xs font-black uppercase text-blue-500 tracking-widest mb-1">Total DP Ditagihkan</span>
                                        <span class="text-3xl font-black text-white tracking-tighter leading-none">Rp{{ number_format($order->jumlah_dp, 0, ',', '.') }}</span>
                                    </div>
                                @else
                                    <div class="flex justify-between items-end">
                                        <span class="text-xs font-black uppercase text-blue-500 tracking-widest mb-1">Grand Total</span>
                                        <span class="text-3xl font-black text-white tracking-tighter leading-none">Rp{{ number_format($order->total_final, 0, ',', '.') }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- AKSI DINAMIS SIKLUS TRANSAKSI ENTERPRISE --}}
                        @if($isPending)
                            
                            @if($sisaDetik > 0)
                                {{-- COUNTDOWN TIMER UI --}}
                                <div class="bg-red-500/10 border border-red-500/20 rounded-[1.5rem] p-5 mb-5 text-center relative overflow-hidden">
                                    <div class="absolute right-0 top-0 w-16 h-16 bg-red-500/20 rounded-full blur-xl"></div>
                                    <p class="text-[10px] font-black text-red-400 uppercase tracking-[0.1em] mb-1">Selesaikan Pembayaran Dalam</p>
                                    <div id="countdown-timer" class="text-3xl font-black text-red-500 tracking-tighter shadow-glow-red">
                                        {{ sprintf("%02d:%02d", floor($sisaDetik / 60), $sisaDetik % 60) }}
                                    </div>
                                </div>

                                <button id="pay-button" class="w-full bg-blue-600 hover:bg-blue-500 text-white font-black py-5 rounded-[1.5rem] transition-all duration-500 shadow-glow flex items-center justify-center gap-3 group active:scale-95 mb-4">
                                    <i class="fas fa-shield-check text-blue-200 group-hover:scale-110 transition-transform"></i>
                                    Selesaikan Pembayaran
                                </button>

                                {{-- TOMBOL PEMBATALAN PESANAN MANUAL --}}
                                <form action="{{ url('/pesanan/batalkan') }}" method="POST" id="cancel-form">
                                    @csrf
                                    <input type="hidden" name="order_id" value="{{ $order->kode_invoice }}">
                                    <button type="button" onclick="confirmCancel()" class="w-full bg-white/5 hover:bg-red-600/20 text-zinc-400 hover:text-red-400 text-xs font-black py-3.5 rounded-xl transition-all border border-white/10">
                                        Batalkan Pesanan Ini
                                    </button>
                                </form>
                            @else
                                {{-- UI KETIKA KEDALUWARSA --}}
                                <div class="bg-red-500/20 border border-red-500/30 p-5 rounded-[1.5rem] flex items-center justify-center gap-3 mb-4 text-center">
                                    <i class="fas fa-clock text-red-500 text-xl"></i>
                                    <span class="text-sm font-black text-red-400 uppercase">Waktu Habis</span>
                                </div>
                                <p class="text-xs text-zinc-400 text-center">Membatalkan pesanan & mengembalikan stok material otomatis...</p>
                                
                                {{-- FORM AUTO CANCEL --}}
                                <form action="{{ url('/pesanan/batalkan') }}" method="POST" id="auto-cancel-form" class="hidden">
                                    @csrf
                                    <input type="hidden" name="order_id" value="{{ $order->kode_invoice }}">
                                </form>
                            @endif

                            <p class="text-[10px] text-zinc-500 text-center mt-6 leading-relaxed font-medium">
                                Enkripsi keamanan 256-bit terjamin oleh <span class="text-zinc-300">Midtrans Financial</span>.
                            </p>

                        @elseif($order->status_pesanan_global == 'dibatalkan')
                            <div class="bg-red-500/10 border border-red-500/20 p-5 rounded-[2rem] flex items-center gap-5">
                                <div class="w-12 h-12 rounded-2xl bg-red-600 text-white flex items-center justify-center text-lg shadow-[0_0_20px_rgba(239,68,68,0.3)]">
                                    <i class="fas fa-ban"></i>
                                </div>
                                <div class="min-w-0">
                                    <h4 class="text-sm font-black text-red-400 uppercase tracking-wider leading-none">Pesanan Batal</h4>
                                    <p class="text-[10px] text-zinc-500 font-bold mt-2">Stok material telah dikembalikan.</p>
                                </div>
                            </div>
                        @else
                            <div class="bg-emerald-500/10 border border-emerald-500/20 p-5 rounded-[2rem] flex items-center gap-5">
                                <div class="w-12 h-12 rounded-2xl bg-emerald-500 text-white flex items-center justify-center text-lg shadow-[0_0_20px_rgba(16,185,129,0.3)]">
                                    <i class="fas fa-receipt"></i>
                                </div>
                                <div class="min-w-0">
                                    <h4 class="text-sm font-black text-emerald-400 uppercase tracking-wider leading-none">
                                        {{ $order->status_pesanan_global == 'selesai' ? 'Selesai & Lunas' : 'Pembayaran Lunas' }}
                                    </h4>
                                    <p class="text-[10px] text-zinc-500 font-bold mt-2 truncate">Ref ID: TXN-{{ strtoupper(substr($order->kode_invoice, 0, 8)) }}</p>
                                </div>
                            </div>

                            {{-- TOMBOL KONFIRMASI BARANG DITERIMA (MENERUSKAN DANA KE SELLER) --}}
                            @if(in_array($order->status_pesanan_global, ['dikirim', 'siap_kirim', 'diproses']))
                            <form action="{{ url('/pesanan/terima') }}" method="POST" id="receipt-form" class="mt-4">
                                @csrf
                                <input type="hidden" name="order_id" value="{{ $order->kode_invoice }}">
                                <button type="button" onclick="confirmReceipt()" class="w-full bg-emerald-600 hover:bg-emerald-500 text-white font-black py-4 rounded-[1.5rem] transition-all duration-300 shadow-lg text-sm flex items-center justify-center gap-2">
                                    <i class="fas fa-box-check"></i> Konfirmasi Barang Diterima
                                </button>
                            </form>
                            @endif

                            <button class="w-full mt-4 bg-white/5 text-zinc-400 hover:bg-blue-600 hover:text-white text-xs font-black py-4 rounded-[1.5rem] transition-all border border-white/10 flex items-center justify-center gap-3 group">
                                <i class="fas fa-file-pdf group-hover:scale-110 transition-transform"></i> Download E-Invoice
                            </button>
                        @endif
                    </div>
                </div>

                {{-- 4. DELIVERY INFORMATION (RIGHT ALIGNED) --}}
                <div class="bg-white rounded-[3rem] p-10 shadow-premium border border-zinc-200/60 transition-all hover:border-blue-500/30">
                    <h3 class="text-[10px] font-black text-zinc-400 uppercase tracking-[0.2em] mb-8 flex items-center gap-2">
                        <i class="fas fa-user-gear text-blue-600"></i> Informasi Pengiriman
                    </h3>

                    <div class="space-y-6">
                        <div class="flex items-center gap-5">
                            <div class="w-14 h-14 rounded-2xl bg-zinc-50 flex items-center justify-center text-zinc-400 border border-zinc-100 shrink-0">
                                <i class="fas fa-id-card-clip text-xl"></i>
                            </div>
                            <div class="min-w-0">
                                <p class="text-[10px] font-black text-zinc-400 uppercase tracking-widest mb-1">Penerima Kontrak</p>
                                <p class="text-sm font-black text-zinc-950 truncate">{{ $order->shipping_nama_penerima }}</p>
                                <p class="text-[11px] font-bold text-zinc-500 mt-0.5">{{ $order->shipping_telepon_penerima }}</p>
                            </div>
                        </div>

                        <div class="bg-zinc-50 rounded-[2rem] p-6 border border-zinc-100">
                            <div class="flex items-center gap-2 mb-3">
                                <i class="fas fa-location-dot text-blue-600 text-[10px]"></i>
                                <span class="text-[9px] font-black text-zinc-400 uppercase tracking-widest">Alamat Penurunan Material</span>
                            </div>
                            <p class="text-xs font-semibold text-zinc-700 leading-relaxed italic">
                                "{{ $order->shipping_alamat_lengkap }}"
                            </p>
                            <div class="mt-4 pt-4 border-t border-zinc-200/60 flex items-center justify-between">
                                <p class="text-xs font-black text-zinc-950 tracking-tighter">
                                    {{ $order->shipping_kota_kabupaten }}, {{ $order->shipping_provinsi }}
                                </p>
                            </div>

                            {{-- SINKRONISASI 3 METODE LOGISTIK TOKO FISIK DAN RESI --}}
                            <div class="mt-4 pt-4 border-t border-zinc-200/60">
                                <div class="flex items-center justify-between mb-3">
                                    <div>
                                        <p class="text-[9px] font-black text-zinc-400 uppercase tracking-widest mb-1">Metode Logistik</p>
                                        <p class="text-xs font-bold text-zinc-800">
                                            @if($order->tipe_pengambilan == 'ambil_di_toko')
                                                <span class="text-emerald-600"><i class="fas fa-store mr-1"></i> Ambil di Toko</span>
                                            @elseif($order->tipe_pengambilan == 'armada')
                                                <span class="text-blue-600"><i class="fas fa-truck-pickup mr-1"></i> Armada Internal Toko</span>
                                            @else
                                                <span class="text-indigo-600"><i class="fas fa-box-fast mr-1"></i> Kurir Ekspedisi Nasional</span>
                                            @endif
                                        </p>
                                    </div>
                                    @if(!empty($order->nomor_resi))
                                    <div class="text-right">
                                        <p class="text-[9px] font-black text-zinc-400 uppercase tracking-widest mb-1">No. Resi / AWB</p>
                                        <p class="text-xs font-black text-blue-600 bg-blue-50 border border-blue-100 px-2 py-1 rounded-md">{{ $order->nomor_resi }}</p>
                                    </div>
                                    @endif
                                </div>

                                @if(!empty($order->catatan))
                                <div class="mt-3 bg-white p-3 rounded-xl border border-zinc-200 shadow-sm">
                                    <p class="text-[9px] font-black text-zinc-400 uppercase tracking-widest mb-1.5 flex items-center gap-1">
                                        <i class="fas fa-comment-dots text-zinc-300"></i> Catatan Khusus & Kurir
                                    </p>
                                    <p class="text-[11px] font-semibold text-zinc-700 leading-relaxed">
                                        {{ $order->catatan }}
                                    </p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </main>


    {{-- Chat dipanggil di bawah agar tidak merusak head --}}
    @include('partials.chat')

    {{-- MIDTRANS SCRIPTS --}}
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ $clientKey }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
// ==============================================================
        // LOGIKA AUTO-CANCEL & COUNTDOWN JAVASCRIPT
        // ==============================================================
        @if($isPending)
            let timeLeft = Math.floor({{ $sisaDetik }}); // Pastikan integer
            const countdownEl = document.getElementById("countdown-timer");
            const autoCancelForm = document.getElementById("auto-cancel-form");
            
            if (timeLeft <= 0 && autoCancelForm) {
                // Jika load halaman dan ternyata waktu sudah habis di backend
                autoCancelForm.submit();
            } else if (timeLeft > 0 && countdownEl) {
                // Jalankan timer per detik
                const timer = setInterval(() => {
                    timeLeft--;
                    if(timeLeft <= 0) {
                        clearInterval(timer);
                        countdownEl.innerHTML = "00:00";
                        
                        // Disable tombol bayar agar tidak kecolongan klik
                        const pb = document.getElementById('pay-button');
                        if (pb) {
                            pb.disabled = true;
                            pb.innerHTML = "Waktu Habis...";
                        }
                        
                        // Eksekusi auto cancel (Submit Form Batal) untuk merelease stok!
                        document.getElementById("cancel-form").submit();
                    } else {
                        // FIX: Tambahkan Math.floor di detik biar gak muncul desimal panjang!
                        let m = Math.floor(timeLeft / 60);
                        let s = Math.floor(timeLeft % 60);
                        countdownEl.innerHTML = (m < 10 ? "0" + m : m) + ":" + (s < 10 ? "0" + s : s);
                    }
                }, 1000);
            }
        @endif

        const payButton = document.getElementById('pay-button');
        if(payButton) {
            payButton.onclick = function(){
                // Ubah tombol jadi loading biar keren
                payButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Membuka Midtrans...';
                payButton.disabled = true;

                snap.pay('{{ $order->snap_token }}', {
                    onSuccess: function(result){
                        // TRIK ENTERPRISE: Langsung hajar refresh halaman + bawa kode lunas!
                        // PageController kita akan otomatis menangkap ini dan ngubah DB jadi 'Diproses/Dikemas'
                        window.location.href = window.location.pathname + "?transaction_status=settlement";
                    },
                    onPending: function(result){
                        Swal.fire({
                            icon: 'info', title: 'Menunggu Pembayaran',
                            text: 'Selesaikan transaksi di portal pembayaran (Transfer/Indomaret).',
                            confirmButtonColor: '#0f172a',
                            customClass: { popup: 'rounded-[3rem]', confirmButton: 'rounded-xl px-8 py-3' }
                        }).then(() => { window.location.reload(); });
                    },
                    onError: function(result){
                        Swal.fire({
                            icon: 'error', title: 'Transaksi Gagal',
                            text: 'Pembayaran dibatalkan atau terjadi kesalahan.',
                            customClass: { popup: 'rounded-[3rem]' }
                        }).then(() => { window.location.reload(); });
                    },
                    onClose: function(){
                        // Kembalikan tombol jika user nutup popup tanpa bayar
                        payButton.innerHTML = '<i class="fas fa-shield-check text-blue-200"></i> Selesaikan Pembayaran';
                        payButton.disabled = false;
                    }
                });
            };
        }

    {{-- FITUR DEWA: MODAL ULASAN PRODUK --}}
    <div id="modalReview" class="fixed inset-0 z-[100] hidden overflow-y-auto">
        <div class="fixed inset-0 bg-zinc-950/80 backdrop-blur-sm transition-opacity" onclick="closeReviewModal()"></div>
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative w-full max-w-lg transform overflow-hidden rounded-[3rem] bg-white p-8 lg:p-12 shadow-2xl transition-all">
                
                <div class="flex items-center justify-between mb-8">
                    <div>
                        <h3 class="text-xl font-black text-zinc-900">Beri Penilaian</h3>
                        <p class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest mt-1" id="review_item_name"></p>
                    </div>
                    <button type="button" onclick="closeReviewModal()" class="w-10 h-10 flex items-center justify-center rounded-full bg-zinc-100 text-zinc-500 hover:bg-red-500 hover:text-white transition-all">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <form action="{{ route('pesanan.review') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
                    @csrf
                    <input type="hidden" name="detail_id" id="review_detail_id">
                    
                    {{-- Star Rating --}}
                    <div class="text-center">
                        <p class="text-xs font-black text-zinc-400 uppercase tracking-[0.2em] mb-4">Bagaimana Kualitas Material?</p>
                        <div class="flex justify-center gap-3" id="star-rating">
                            @for($i=1; $i<=5; $i++)
                                <button type="button" onclick="setRating({{ $i }})" class="star-btn text-4xl text-zinc-200 hover:text-yellow-400 transition-all transform hover:scale-110" data-val="{{ $i }}">
                                    <i class="fas fa-star"></i>
                                </button>
                            @endfor
                        </div>
                        <input type="hidden" name="rating" id="input_rating" required>
                    </div>

                    {{-- Text Review --}}
                    <div>
                        <label class="block text-xs font-black text-zinc-500 uppercase tracking-wider mb-3">Tulis Ulasan Anda</label>
                        <textarea name="ulasan" rows="4" class="w-full bg-zinc-50 border border-zinc-200 rounded-[2rem] p-5 text-sm font-semibold outline-none focus:border-blue-600 focus:ring-4 focus:ring-blue-600/5 transition-all" placeholder="Ceritakan pengalaman Anda menggunakan material ini..." required></textarea>
                    </div>

                    {{-- Upload Media --}}
                    <div class="p-6 bg-blue-50/50 border border-dashed border-blue-200 rounded-[2rem]">
                        <label class="block text-xs font-black text-blue-600 uppercase tracking-wider mb-3 flex items-center justify-between">
                            <span>Lampirkan Foto Produk</span>
                            <span class="text-[9px] bg-blue-600 text-white px-2 py-0.5 rounded-full">+50 Poin</span>
                        </label>
                        <input type="file" name="foto" accept="image/*" class="w-full text-xs font-bold text-zinc-500 file:mr-4 file:py-2.5 file:px-6 file:rounded-xl file:border-0 file:text-[10px] file:font-black file:bg-blue-600 file:text-white hover:file:bg-blue-700 transition-all cursor-pointer">
                        <p class="mt-3 text-[10px] font-medium text-blue-600/60 leading-relaxed italic">Ulasan dengan foto membantu Juragan lain dan memberikan Anda poin belanja tambahan!</p>
                    </div>

                    <button type="submit" class="w-full py-5 bg-zinc-950 hover:bg-blue-600 text-white font-black text-xs uppercase tracking-[0.2em] rounded-[2rem] shadow-xl transition-all active:scale-95">
                        Kirim Penilaian Resmi
                    </button>
                </form>

            </div>
        </div>
    </div>

    <script>
        function openReviewModal(detailId, itemName) {
            document.getElementById('review_detail_id').value = detailId;
            document.getElementById('review_item_name').innerText = itemName;
            document.getElementById('modalReview').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeReviewModal() {
            document.getElementById('modalReview').classList.add('hidden');
            document.body.style.overflow = '';
        }

        function setRating(val) {
            document.getElementById('input_rating').value = val;
            const stars = document.querySelectorAll('.star-btn');
            stars.forEach((s, idx) => {
                if (idx < val) {
                    s.classList.remove('text-zinc-200');
                    s.classList.add('text-yellow-400');
                } else {
                    s.classList.remove('text-yellow-400');
                    s.classList.add('text-zinc-200');
                }
            });
        }

        // Logika Batalkan Pesanan
        function confirmCancel() {
            Swal.fire({
                title: 'Batalkan Pesanan?',
                text: "Stok material bangunan Anda akan otomatis dikembalikan ke sistem gudang.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#94a3b8',
                confirmButtonText: 'Ya, Batalkan',
                cancelButtonText: 'Kembali',
                reverseButtons: true,
                customClass: { popup: 'rounded-[2.5rem]', confirmButton: 'rounded-xl px-5 py-2.5', cancelButton: 'rounded-xl px-5 py-2.5' }
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('cancel-form').submit();
                }
            });
        }

        // Logika Terima Pesanan (Konfirmasi Selesai)
        function confirmReceipt() {
            Swal.fire({
                title: 'Selesaikan Pesanan?',
                text: "Pastikan seluruh volume material sudah diturunkan dan dihitung sesuai invoice. Dana akan dicairkan langsung ke dompet penjual.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#10b981',
                cancelButtonColor: '#94a3b8',
                confirmButtonText: 'Ya, Sudah Sesuai',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                customClass: { popup: 'rounded-[2.5rem]', confirmButton: 'rounded-xl px-5 py-2.5', cancelButton: 'rounded-xl px-5 py-2.5' }
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('receipt-form').submit();
                }
            });
        }
    </script>
</body>
</html>
