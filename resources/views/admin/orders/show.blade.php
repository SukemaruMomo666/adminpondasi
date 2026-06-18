@extends('layouts.admin')

@section('title', 'Detail Transaksi #' . $order->kode_invoice)

@push('styles')
<style>
    /* ========================================= */
    /* ==  PREMIUM ORDER DETAIL CSS           == */
    /* ========================================= */

    /* Animasi Hover Lift yang elegan */
    .hover-lift { transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.3s ease; }
    .hover-lift:hover { transform: translateY(-4px); }

    /* Custom Scrollbar */
    .scroll-hide::-webkit-scrollbar { display: none; }
    .scroll-hide { -ms-overflow-style: none; scrollbar-width: none; }

    /* Progress Tracker Lanjutan */
    .tracker-container { position: relative; display: flex; justify-content: space-between; margin-top: 1rem; margin-bottom: 2rem; }
    .tracker-line { position: absolute; top: 24px; left: 10%; right: 10%; height: 4px; background: #e2e8f0; z-index: 1; border-radius: 4px; }
    .dark .tracker-line { background: #334155; }

    .tracker-step { z-index: 2; display: flex; flex-direction: column; align-items: center; width: 20%; position: relative; }
    .tracker-icon { width: 50px; height: 50px; border-radius: 50%; background: white; border: 4px solid #e2e8f0; display: flex; align-items: center; justify-content: center; margin: 0 auto 12px; transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); font-size: 1.25rem; color: #94a3b8; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); }
    .dark .tracker-icon { background: #1e293b; border-color: #334155; color: #64748b; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.3); }

    /* Active State Tracker */
    .tracker-step.active .tracker-icon { border-color: #3b82f6; color: #3b82f6; transform: scale(1.15); box-shadow: 0 0 15px rgba(59, 130, 246, 0.3); background: #eff6ff; }
    .dark .tracker-step.active .tracker-icon { border-color: #3b82f6; color: #60a5fa; box-shadow: 0 0 20px rgba(59, 130, 246, 0.4); background: rgba(59, 130, 246, 0.1); }

    /* Completed State Tracker */
    .tracker-step.completed .tracker-icon { background: #3b82f6; border-color: #3b82f6; color: white; }
    .dark .tracker-step.completed .tracker-icon { background: #3b82f6; border-color: #3b82f6; color: white; box-shadow: 0 0 15px rgba(59, 130, 246, 0.3); }

    .tracker-label { font-size: 10px; font-weight: 900; text-transform: uppercase; letter-spacing: 0.05em; color: #94a3b8; text-align: center; }
    .tracker-step.active .tracker-label { color: #1e293b; }
    .dark .tracker-step.active .tracker-label { color: #f8fafc; }
    .tracker-step.completed .tracker-label { color: #3b82f6; }
    .dark .tracker-step.completed .tracker-label { color: #60a5fa; }

    /* ========================================= */
    /* == POLYFILL DARK MODE (ANTI-PUTIH)     == */
    /* ========================================= */

    /* Layout & Containers */
    .dark .dark\:bg-slate-950 { background-color: #020617 !important; }
    .dark .dark\:bg-slate-900 { background-color: #0f172a !important; }
    .dark .dark\:bg-slate-800 { background-color: #1e293b !important; }
    .dark .dark\:bg-slate-800\/80 { background-color: rgba(30, 41, 59, 0.8) !important; }
    .dark .dark\:bg-slate-800\/50 { background-color: rgba(30, 41, 59, 0.5) !important; }
    .dark .dark\:bg-slate-800\/30 { background-color: rgba(30, 41, 59, 0.3) !important; }
    .dark .dark\:bg-slate-700 { background-color: #334155 !important; }
    .dark .dark\:bg-transparent { background-color: transparent !important; }

    /* Fix Gradient Background di Dark Mode */
    .dark .dark\:from-slate-800\/80.dark\:to-slate-900 { background-image: linear-gradient(to bottom right, rgba(30, 41, 59, 0.8), #0f172a) !important; }

    /* Borders */
    .dark .dark\:border-slate-800 { border-color: #1e293b !important; }
    .dark .dark\:border-slate-700 { border-color: #334155 !important; }
    .dark .dark\:border-slate-700\/50 { border-color: rgba(51, 65, 85, 0.5) !important; }

    /* Typography Umum */
    .dark .dark\:text-white { color: #ffffff !important; }
    .dark .dark\:text-slate-100 { color: #f1f5f9 !important; }
    .dark .dark\:text-slate-200 { color: #e2e8f0 !important; }
    .dark .dark\:text-slate-300 { color: #cbd5e1 !important; }
    .dark .dark\:text-slate-400 { color: #94a3b8 !important; }
    .dark .dark\:text-slate-500 { color: #64748b !important; }

    /* Badges & Accents */
    .dark .dark\:bg-blue-500\/10 { background-color: rgba(59, 130, 246, 0.1) !important; }
    .dark .dark\:bg-blue-500\/20 { background-color: rgba(59, 130, 246, 0.2) !important; }
    .dark .dark\:border-blue-500\/20 { border-color: rgba(59, 130, 246, 0.2) !important; }
    .dark .dark\:border-blue-500\/30 { border-color: rgba(59, 130, 246, 0.3) !important; }
    .dark .dark\:border-blue-500\/50 { border-color: rgba(59, 130, 246, 0.5) !important; }
    .dark .dark\:text-blue-400 { color: #60a5fa !important; }

    .dark .dark\:bg-emerald-500\/10 { background-color: rgba(16, 185, 129, 0.1) !important; }
    .dark .dark\:bg-emerald-500\/20 { background-color: rgba(16, 185, 129, 0.2) !important; }
    .dark .dark\:bg-emerald-500\/30 { background-color: rgba(16, 185, 129, 0.3) !important; }
    .dark .dark\:border-emerald-500\/20 { border-color: rgba(16, 185, 129, 0.2) !important; }
    .dark .dark\:border-emerald-500\/30 { border-color: rgba(16, 185, 129, 0.3) !important; }
    .dark .dark\:text-emerald-400 { color: #34d399 !important; }

    .dark .dark\:bg-amber-500\/10 { background-color: rgba(245, 158, 11, 0.1) !important; }
    .dark .dark\:bg-amber-500\/20 { background-color: rgba(245, 158, 11, 0.2) !important; }
    .dark .dark\:border-amber-500\/20 { border-color: rgba(245, 158, 11, 0.2) !important; }
    .dark .dark\:border-amber-500\/30 { border-color: rgba(245, 158, 11, 0.3) !important; }
    .dark .dark\:text-amber-400 { color: #fbbf24 !important; }

    .dark .dark\:bg-rose-500\/10 { background-color: rgba(244, 63, 94, 0.1) !important; }
    .dark .dark\:bg-rose-500\/20 { background-color: rgba(244, 63, 94, 0.2) !important; }
    .dark .dark\:border-rose-500\/20 { border-color: rgba(244, 63, 94, 0.2) !important; }
    .dark .dark\:border-rose-500\/30 { border-color: rgba(244, 63, 94, 0.3) !important; }
    .dark .dark\:border-rose-800 { border-color: #9f1239 !important; }
    .dark .dark\:text-rose-400 { color: #fb7185 !important; }
    .dark .dark\:text-rose-400\/80 { color: rgba(251, 113, 133, 0.8) !important; }

    .dark .dark\:bg-indigo-500\/20 { background-color: rgba(99, 102, 241, 0.2) !important; }
    .dark .dark\:text-indigo-400 { color: #818cf8 !important; }

    /* Hover Interactivity Overrides */
    .dark .dark\:hover\:bg-slate-800:hover { background-color: #1e293b !important; }
    .dark .dark\:hover\:bg-slate-600:hover { background-color: #475569 !important; }
    .dark .dark\:hover\:border-slate-600:hover { border-color: #475569 !important; }
    .dark .dark\:hover\:text-blue-400:hover { color: #60a5fa !important; }
</style>
@endpush

@section('content')

{{-- HEADER HALAMAN & TOMBOL KEMBALI --}}
<div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-4 mb-6">
    <div>
        <h2 class="text-2xl md:text-3xl font-black text-slate-800 dark:text-white tracking-tight mb-1 transition-colors duration-300 flex items-center gap-3">
            <a href="{{ route('admin.orders.index') }}" class="w-10 h-10 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-500 hover:text-blue-600 dark:text-slate-400 dark:hover:text-blue-400 rounded-xl flex items-center justify-center transition-all duration-300 shadow-sm hover:shadow-md outline-none text-decoration-none">
                <i class="mdi mdi-arrow-left text-xl"></i>
            </a>
            Detail Transaksi
        </h2>
        <div class="flex items-center gap-2 text-xs font-bold text-slate-500 dark:text-slate-400 mt-2 pl-14 transition-colors duration-300">
            <span class="text-blue-600 dark:text-blue-400 font-mono tracking-wider">{{ $order->kode_invoice }}</span>
            <span class="mx-1 opacity-50">•</span>
            <i class="mdi mdi-calendar-clock text-sm"></i> {{ \Carbon\Carbon::parse($order->tanggal_transaksi)->translatedFormat('d F Y, H:i') }}
        </div>
    </div>

    {{-- Status Badge Kanan Atas --}}
    <div class="flex items-center gap-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-5 py-3 rounded-2xl shadow-sm transition-colors duration-300">
        <div class="flex flex-col text-right">
            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-0.5">Metode Transaksi</span>
            @if(($order->sumber_transaksi ?? 'ONLINE') == 'OFFLINE' || str_starts_with($order->kode_invoice, 'POS-'))
                <span class="text-sm font-black text-amber-500 dark:text-amber-400 flex items-center justify-end gap-1.5">
                    <i class="mdi mdi-cash-register"></i> POINT OF SALE
                </span>
            @else
                <span class="text-sm font-black text-blue-600 dark:text-blue-400 flex items-center justify-end gap-1.5">
                    <i class="mdi mdi-web"></i> ONLINE WEB
                </span>
            @endif
        </div>
        <div class="w-px h-8 bg-slate-200 dark:bg-slate-700 mx-1"></div>
        <div class="flex flex-col text-right">
            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-0.5">Status Pembayaran</span>
            @if($order->status_pembayaran == 'paid' || $order->status_pembayaran == 'dp_paid')
                <span class="text-sm font-black text-emerald-600 dark:text-emerald-400 flex items-center justify-end gap-1.5">
                    <i class="mdi mdi-check-decagram"></i> {{ $order->status_pembayaran == 'dp_paid' ? 'DP LUNAS' : 'LUNAS FULL' }}
                </span>
            @elseif($order->status_pembayaran == 'pending')
                <span class="text-sm font-black text-amber-500 dark:text-amber-400 flex items-center justify-end gap-1.5">
                    <i class="mdi mdi-clock-outline"></i> MENUNGGU
                </span>
            @else
                <span class="text-sm font-black text-rose-500 dark:text-rose-400 flex items-center justify-end gap-1.5">
                    <i class="mdi mdi-close-circle"></i> GAGAL/BATAL
                </span>
            @endif
        </div>
    </div>
</div>

{{-- 1. TRACKER STATUS PESANAN (ENTERPRISE STYLE) --}}
<div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-[2rem] p-6 lg:p-8 shadow-sm transition-colors duration-300 mb-8 overflow-hidden">
    <div class="border-b border-slate-100 dark:border-slate-800 pb-5 mb-6">
        <h3 class="text-base font-black text-slate-800 dark:text-white m-0 flex items-center gap-2"><i class="mdi mdi-map-marker-path text-blue-500 text-xl"></i> Lacak Status Pesanan</h3>
    </div>

    <div class="tracker-container">
        <div class="tracker-line"></div>
        @php
            $s = strtolower($order->status_pesanan_item);
            $steps = [
                ['id' => 'menunggu_pembayaran', 'icon' => 'mdi-wallet-outline', 'label' => 'Bayar'],
                ['id' => 'diproses', 'icon' => 'mdi-store-clock-outline', 'label' => 'Diproses'],
                ['id' => 'dikirim', 'icon' => 'mdi-truck-fast-outline', 'label' => 'Dikirim'],
                ['id' => 'sampai_tujuan', 'icon' => 'mdi-package-variant-closed', 'label' => 'Sampai'],
                ['id' => 'selesai', 'icon' => 'mdi-check-decagram-outline', 'label' => 'Selesai']
            ];

            // Logika pencarian index status (karena ada status khusus spt 'dibatalkan', dll)
            $currentStepIndex = -1;
            if (in_array($s, ['dibatalkan', 'pengajuan_pengembalian', 'pengembalian_disetujui', 'pengembalian_ditolak'])) {
                // Jika statusnya batal/komplain, jangan jalankan tracker normal
                $currentStepIndex = 99;
            } else {
                $currentStepIndex = array_search($s, array_column($steps, 'id'));
                if ($currentStepIndex === false && $s == 'siap_kirim') $currentStepIndex = 1; // gabung ke diproses
            }
        @endphp

        @foreach($steps as $index => $step)
            <div class="tracker-step {{ $index < $currentStepIndex ? 'completed' : ($index == $currentStepIndex ? 'active' : '') }}">
                <div class="tracker-icon"><i class="mdi {{ $step['icon'] }}"></i></div>
                <div class="tracker-label">{{ $step['label'] }}</div>
            </div>
        @endforeach
    </div>

    {{-- Alert Khusus jika Pesanan Bermasalah --}}
    @if(in_array($s, ['dibatalkan', 'pengajuan_pengembalian', 'pengembalian_disetujui', 'pengembalian_ditolak']))
    <div class="mt-6 p-4 rounded-xl bg-rose-50 border border-rose-200 dark:bg-rose-500/10 dark:border-rose-500/20 flex items-center gap-3">
        <div class="w-10 h-10 rounded-lg bg-white dark:bg-slate-900 border border-rose-100 dark:border-rose-800 flex items-center justify-center text-rose-500 text-xl flex-shrink-0">
            <i class="mdi mdi-alert-circle"></i>
        </div>
        <div>
            <strong class="block text-sm font-black text-rose-600 dark:text-rose-400 mb-0.5 uppercase tracking-wide">Pesanan Bermasalah</strong>
            <span class="text-xs font-bold text-rose-500/80 dark:text-rose-400/80">Status saat ini: {{ strtoupper(str_replace('_', ' ', $s)) }}</span>
        </div>
    </div>
    @endif
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8 pb-10">

    {{-- KOLOM KIRI (LEBAR): RINCIAN BARANG & PENGIRIMAN --}}
    <div class="lg:col-span-2 space-y-8">

        {{-- Card: Daftar Barang --}}
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-[2rem] shadow-sm transition-colors duration-300 overflow-hidden">
            <div class="p-6 border-b border-slate-100 dark:border-slate-800 bg-blue-50/30 dark:bg-transparent flex justify-between items-center">
                <div>
                    <h3 class="text-base font-black text-slate-800 dark:text-white m-0 flex items-center gap-2"><i class="mdi mdi-store-outline text-blue-500 text-xl"></i> Mitra: {{ $order->nama_toko }}</h3>
                    <p class="text-[10px] font-bold text-slate-500 mt-1 mb-0 uppercase tracking-widest"><i class="mdi mdi-phone"></i> Hubungi Toko: <span class="text-blue-600 dark:text-blue-400">{{ $order->telp_toko ?? '-' }}</span></p>
                </div>
            </div>

            <div class="p-6 space-y-5">
                @foreach($items as $item)
                <div class="flex items-center gap-4 p-4 rounded-2xl border border-slate-100 dark:border-slate-700/50 bg-slate-50 dark:bg-slate-800/30 group hover:border-blue-200 dark:hover:border-slate-600 transition-colors">
                    {{-- Foto Barang --}}
                    <div class="w-20 h-20 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 overflow-hidden shrink-0">
                        <img src="{{ asset('storage/' . $item->foto_barang) }}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110" onerror="this.src='https://placehold.co/150x150?text=Produk'">
                    </div>

                    {{-- Detail --}}
                    <div class="flex-grow">
                        <h6 class="text-sm font-black text-slate-800 dark:text-slate-100 mb-1 leading-tight line-clamp-2">{{ $item->nama_barang }}</h6>
                        <div class="text-[11px] font-bold text-slate-500 dark:text-slate-400">
                            {{ $item->jumlah_item }} unit <span class="mx-1">x</span> <span class="text-blue-600 dark:text-blue-400 font-mono tracking-tight">Rp {{ number_format($item->harga_saat_ini, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    {{-- Subtotal --}}
                    <div class="text-right shrink-0">
                        <span class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Subtotal</span>
                        <span class="text-base font-black text-slate-800 dark:text-white font-mono">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Catatan (Jika ada) --}}
            @if(!empty($order->catatan_pembeli))
            <div class="px-6 pb-6">
                <div class="bg-amber-50 dark:bg-amber-500/10 border border-amber-100 dark:border-amber-500/20 rounded-xl p-4 flex gap-3 items-start">
                    <i class="mdi mdi-message-text-outline text-amber-500 text-lg mt-0.5"></i>
                    <div>
                        <strong class="block text-[10px] font-black text-amber-600 dark:text-amber-400 uppercase tracking-widest mb-1">Catatan Pembeli:</strong>
                        <p class="text-xs font-bold text-amber-800 dark:text-amber-200/80 m-0 leading-relaxed">{{ $order->catatan_pembeli }}</p>
                    </div>
                </div>
            </div>
            @endif
        </div>

        {{-- Card: Pengiriman & Logistik --}}
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-[2rem] shadow-sm transition-colors duration-300 overflow-hidden">
            <div class="p-6 border-b border-slate-100 dark:border-slate-800 flex items-center gap-2">
                <i class="mdi mdi-truck-fast-outline text-emerald-500 text-xl"></i>
                <h3 class="text-base font-black text-slate-800 dark:text-white m-0">Informasi Pengiriman</h3>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 divide-y md:divide-y-0 md:divide-x divide-slate-100 dark:divide-slate-800">
                <div class="p-6">
                    <span class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Metode & Kurir</span>
                    <div class="flex items-center gap-3 mb-3">
                        @if(($order->sumber_transaksi ?? 'ONLINE') == 'OFFLINE' || str_starts_with($order->kode_invoice, 'POS-'))
                            <div class="w-10 h-10 rounded-lg bg-amber-50 dark:bg-amber-500/10 border border-amber-100 dark:border-amber-500/20 text-amber-500 flex items-center justify-center text-xl">
                                <i class="mdi mdi-cash-register"></i>
                            </div>
                            <div>
                                <div class="text-sm font-black text-slate-800 dark:text-white uppercase tracking-wide">Point of Sale (OFFLINE)</div>
                                <div class="text-[11px] font-bold text-amber-600 dark:text-amber-400 mt-0.5">{{ $order->metode_pengiriman == 'AMBIL_DI_TOKO' ? 'Ambil di Toko / Langsung' : 'Dikirim' }}</div>
                            </div>
                        @else
                            <div class="w-10 h-10 rounded-lg bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-100 dark:border-emerald-500/20 text-emerald-500 flex items-center justify-center text-xl">
                                <i class="mdi mdi-package-variant"></i>
                            </div>
                            <div>
                                <div class="text-sm font-black text-slate-800 dark:text-white uppercase tracking-wide">{{ $order->kurir_terpilih ?? 'Belum Ditentukan' }}</div>
                                <div class="text-[11px] font-bold text-emerald-600 dark:text-emerald-400 mt-0.5">{{ $order->tipe_pengambilan == 'ambil_di_toko' ? 'Ambil di Toko' : 'Dikirim ke Alamat' }}</div>
                            </div>
                        @endif
                    </div>
                    <div class="bg-slate-50 dark:bg-slate-800/50 rounded-xl p-3 border border-slate-100 dark:border-slate-700">
                        @if(($order->sumber_transaksi ?? 'ONLINE') == 'OFFLINE' || str_starts_with($order->kode_invoice, 'POS-'))
                            <span class="block text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Informasi Kasir</span>
                            <div class="text-sm font-black text-amber-600 dark:text-amber-400 font-mono tracking-wider">{{ $order->catatan_global ?? 'Transaksi POS' }}</div>
                        @else
                            <span class="block text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Nomor Resi Pintu</span>
                            <div class="text-sm font-black text-blue-600 dark:text-blue-400 font-mono tracking-wider">{{ $order->resi_pengiriman ?? 'BELUM TERBIT' }}</div>
                        @endif
                    </div>
                </div>

                <div class="p-6">
                    <span class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Alamat Tujuan Pengiriman</span>
                    <div class="bg-slate-50 dark:bg-slate-800/50 rounded-xl p-4 border border-slate-100 dark:border-slate-700 h-full">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="text-xs font-black text-slate-800 dark:text-white">{{ $order->shipping_nama_penerima ?? 'Tidak Ada Data' }}</span>
                            <span class="px-2 py-0.5 bg-slate-200 dark:bg-slate-700 text-slate-600 dark:text-slate-300 rounded text-[9px] font-black uppercase">{{ $order->shipping_label_alamat ?? 'Alamat' }}</span>
                        </div>
                        <p class="text-[11px] font-bold text-slate-500 dark:text-slate-400 m-0 mb-2">{{ $order->shipping_telepon_penerima ?? '-' }}</p>
                        <p class="text-[11px] font-bold text-slate-600 dark:text-slate-300 m-0 leading-relaxed">
                            {{ $order->shipping_alamat_lengkap ?? '-' }}<br>
                            Kec. {{ $order->shipping_kecamatan ?? '-' }}, {{ $order->shipping_kota_kabupaten ?? '-' }}<br>
                            {{ $order->shipping_provinsi ?? '-' }} - {{ $order->shipping_kode_pos ?? '-' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- KOLOM KANAN (SEMPIT): RINGKASAN BIAYA & PEMBELI --}}
    <div class="space-y-8">

        {{-- Card: Pembeli --}}
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-[2rem] shadow-sm p-6 transition-colors duration-300 relative overflow-hidden group hover:border-blue-300 dark:hover:border-blue-500/50">
            <div class="absolute right-0 top-0 w-24 h-24 bg-blue-50 dark:bg-blue-500/10 rounded-bl-full flex items-start justify-end p-4 transition-colors">
                <i class="mdi mdi-account-circle-outline text-3xl text-blue-500 opacity-50 group-hover:opacity-100 group-hover:scale-110 transition-all"></i>
            </div>

            <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-4 relative z-10">Informasi Pembeli</h3>

            <div class="relative z-10">
                <h4 class="text-base font-black text-slate-800 dark:text-white mb-2">{{ $order->nama_pembeli }}</h4>
                <div class="flex flex-col gap-2">
                    <span class="text-xs font-bold text-slate-500 dark:text-slate-400 flex items-center gap-2"><i class="mdi mdi-email-outline text-slate-400"></i> {{ $order->email_pembeli }}</span>
                    <span class="text-[10px] font-black text-blue-600 dark:text-blue-400 uppercase tracking-wider bg-blue-50 dark:bg-blue-500/10 border border-blue-100 dark:border-blue-500/20 px-2.5 py-1 rounded w-fit mt-1">Status Web: {{ $order->sumber_transaksi }}</span>
                </div>
            </div>
        </div>

        {{-- Card: Rincian Pembayaran --}}
        <div class="bg-gradient-to-br from-slate-50 to-white dark:from-slate-800/80 dark:to-slate-900 border border-slate-200 dark:border-slate-700 rounded-[2rem] shadow-sm p-6 lg:p-8 transition-colors duration-300 relative overflow-hidden group">

            <h3 class="text-base font-black text-slate-800 dark:text-white flex items-center gap-2 mb-6">
                <i class="mdi mdi-cash-register text-emerald-500 text-xl"></i> Ringkasan Pembayaran
            </h3>

            <div class="space-y-3 border-b border-slate-200 dark:border-slate-700 pb-5 mb-5">
                <div class="flex justify-between items-center text-xs font-bold text-slate-500 dark:text-slate-400">
                    <span>Subtotal Barang ({{ count($items) }} jenis)</span>
                    <span class="font-black text-slate-700 dark:text-slate-300 font-mono tracking-tight">Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between items-center text-xs font-bold text-slate-500 dark:text-slate-400">
                    <span>Total Ongkos Kirim</span>
                    <span class="font-black text-slate-700 dark:text-slate-300 font-mono tracking-tight">Rp {{ number_format($order->biaya_pengiriman_item, 0, ',', '.') }}</span>
                </div>

                @if(($order->customer_service_fee + $order->customer_handling_fee) > 0)
                <div class="flex justify-between items-center text-xs font-bold text-slate-500 dark:text-slate-400">
                    <span>Biaya Layanan & Penanganan</span>
                    <span class="font-black text-slate-700 dark:text-slate-300 font-mono tracking-tight">Rp {{ number_format($order->customer_service_fee + $order->customer_handling_fee, 0, ',', '.') }}</span>
                </div>
                @endif
            </div>

            {{-- Highlight Total --}}
            <div class="bg-blue-600 dark:bg-blue-500/20 border border-blue-600 dark:border-blue-500/50 rounded-2xl p-5 relative overflow-hidden shadow-lg shadow-blue-600/20">
                <div class="absolute -right-4 -top-4 w-16 h-16 bg-white/10 rounded-full blur-xl pointer-events-none"></div>

                <div class="flex flex-col gap-1 mb-4 border-b border-white/20 dark:border-blue-500/30 pb-4">
                    <div class="flex justify-between items-center">
                        <span class="text-[10px] font-black text-blue-100 dark:text-blue-300 uppercase tracking-widest">Total Nilai Pesanan</span>
                        @if($order->tipe_pembayaran == 'DP')
                            <span class="px-2 py-0.5 bg-amber-500 text-white text-[9px] font-black uppercase rounded shadow-sm">B2B DP SYSTEM</span>
                        @endif
                    </div>
                    <span class="text-2xl font-black text-white font-mono tracking-tight">Rp {{ number_format($order->subtotal + $order->biaya_pengiriman_item + ($order->customer_service_fee ?? 0) + ($order->customer_handling_fee ?? 0), 0, ',', '.') }}</span>
                </div>

                @if($order->tipe_pembayaran == 'DP')
                    <div class="space-y-3">
                        <div class="p-3 bg-white/10 dark:bg-emerald-500/10 border border-white/20 dark:border-emerald-500/20 rounded-xl">
                            <div class="flex justify-between items-center mb-1">
                                <span class="text-[10px] font-black text-blue-100 dark:text-emerald-400 uppercase tracking-widest">
                                    @if($order->status_pembayaran == 'dp_paid' || $order->status_pembayaran == 'paid')
                                        <i class="mdi mdi-check-circle mr-1"></i> DP Telah Dibayar
                                    @else
                                        <i class="mdi mdi-clock-outline mr-1"></i> Tagihan DP (Web)
                                    @endif
                                </span>
                            </div>
                            <div class="text-xl font-black text-white dark:text-emerald-400 font-mono">
                                Rp {{ number_format($order->jumlah_dp, 0, ',', '.') }}
                            </div>
                        </div>

                        <div class="flex justify-between items-center px-1">
                            <span class="text-[10px] font-black text-blue-100 dark:text-rose-300 uppercase tracking-widest">Sisa Pelunasan (Cash/COD)</span>
                            <span class="font-black text-white dark:text-rose-400 font-mono text-sm">Rp {{ number_format($order->sisa_tagihan, 0, ',', '.') }}</span>
                        </div>
                    </div>
                @elseif(($order->sumber_transaksi ?? 'ONLINE') == 'OFFLINE' || str_starts_with($order->kode_invoice, 'POS-'))
                    <div class="space-y-2">
                        <div class="flex justify-between items-center text-xs">
                            <span class="font-bold text-blue-100 dark:text-blue-300">Dibayar Tunai:</span>
                            <span class="font-black text-white font-mono">Rp {{ number_format($order->bayar ?? 0, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between items-center text-xs pt-2 border-t border-white/20">
                            <span class="font-bold text-blue-100 dark:text-blue-300">Kembalian:</span>
                            <span class="font-black text-white font-mono">Rp {{ number_format($order->kembali ?? 0, 0, ',', '.') }}</span>
                        </div>
                    </div>
                @else
                    <div class="w-full py-2 bg-emerald-500/20 dark:bg-emerald-500/30 border border-emerald-400/50 rounded-xl flex items-center justify-center gap-2 text-emerald-100 dark:text-emerald-400 text-[11px] font-black uppercase tracking-widest mt-2 shadow-inner">
                        <i class="mdi mdi-check-decagram text-sm"></i> Dibayar Lunas
                    </div>
                @endif
            </div>

            {{-- Metode Pembayaran Badge --}}
            <div class="mt-5 flex items-center justify-between">
                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Metode Bayar Web</span>
                <span class="px-3 py-1.5 bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 text-[10px] font-black uppercase tracking-wider rounded-lg">
                    {{ str_replace('_', ' ', $order->metode_pembayaran ?? 'BELUM BAYAR') }}
                </span>
            </div>
        </div>

    </div>
</div>

@endsection
