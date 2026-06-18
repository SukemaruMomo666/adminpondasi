@extends('layouts.admin')

@section('title', 'Global Order Monitor')

@push('styles')
<style>
    /* ========================================= */
    /* ==  PREMIUM ORDER MONITOR CSS          == */
    /* ========================================= */

    /* Animasi Mengambang Halus */
    .hover-lift { transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.3s ease; }
    .hover-lift:hover { transform: translateY(-4px); }

    /* Custom Scrollbar untuk Tabel & Filter */
    .table-wrapper::-webkit-scrollbar, .filter-wrapper::-webkit-scrollbar { height: 6px; }
    .table-wrapper::-webkit-scrollbar-track, .filter-wrapper::-webkit-scrollbar-track { background: transparent; }
    .table-wrapper::-webkit-scrollbar-thumb, .filter-wrapper::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    .table-wrapper::-webkit-scrollbar-thumb:hover, .filter-wrapper::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

    /* Scrollbar Dark Mode */
    .dark .table-wrapper::-webkit-scrollbar-thumb, .dark .filter-wrapper::-webkit-scrollbar-thumb { background: #475569; }
    .dark .table-wrapper::-webkit-scrollbar-thumb:hover, .dark .filter-wrapper::-webkit-scrollbar-thumb:hover { background: #64748b; }

    /* Override Bootstrap Pagination for Dark Mode */
    .dark .pagination .page-link { background-color: #1e293b; border-color: #334155; color: #cbd5e1; }
    .dark .pagination .page-item.active .page-link { background-color: #3b82f6; border-color: #3b82f6; color: white; }
    .dark .pagination .page-item.disabled .page-link { background-color: #0f172a; color: #475569; border-color: #1e293b; }

    /* ========================================= */
    /* == POLYFILL DARK MODE (ANTI-PUTIH)     == */
    /* ========================================= */

    /* Card, Tabel, & Filter (Slate Series) */
    .dark .dark\:bg-slate-900 { background-color: #0f172a !important; }
    .dark .dark\:bg-slate-900\/50 { background-color: rgba(15, 23, 42, 0.5) !important; }
    .dark .dark\:bg-slate-800 { background-color: #1e293b !important; }
    .dark .dark\:bg-slate-800\/50 { background-color: rgba(30, 41, 59, 0.5) !important; }
    .dark .dark\:bg-slate-800\/40 { background-color: rgba(30, 41, 59, 0.4) !important; }
    .dark .dark\:bg-slate-800\/30 { background-color: rgba(30, 41, 59, 0.3) !important; }
    .dark .dark\:hover\:bg-slate-800\/30:hover { background-color: rgba(30, 41, 59, 0.3) !important; }
    .dark .dark\:bg-slate-700 { background-color: #334155 !important; }

    .dark .dark\:border-slate-800 { border-color: #1e293b !important; }
    .dark .dark\:border-slate-800\/80 { border-color: rgba(30, 41, 59, 0.8) !important; }
    .dark .dark\:border-slate-700 { border-color: #334155 !important; }
    .dark .dark\:border-slate-700\/50 { border-color: rgba(51, 65, 85, 0.5) !important; }
    .dark .dark\:border-slate-600 { border-color: #475569 !important; }

    /* Status Badges, Header & Aksi (Efek Neon Tipis) */
    .dark .dark\:bg-blue-500\/10 { background-color: rgba(59, 130, 246, 0.1) !important; }
    .dark .dark\:border-blue-500\/20 { border-color: rgba(59, 130, 246, 0.2) !important; }
    .dark .dark\:hover\:bg-blue-500\/20:hover { background-color: rgba(59, 130, 246, 0.2) !important; }
    .dark .dark\:hover\:bg-blue-500\/10:hover { background-color: rgba(59, 130, 246, 0.1) !important; }
    .dark .dark\:hover\:border-blue-500\/30:hover { border-color: rgba(59, 130, 246, 0.3) !important; }

    .dark .dark\:bg-amber-500\/10 { background-color: rgba(245, 158, 11, 0.1) !important; }
    .dark .dark\:bg-amber-500\/5 { background-color: rgba(245, 158, 11, 0.05) !important; }
    .dark .dark\:border-amber-500\/20 { border-color: rgba(245, 158, 11, 0.2) !important; }
    .dark .dark\:border-amber-500\/30 { border-color: rgba(245, 158, 11, 0.3) !important; }

    .dark .dark\:bg-emerald-500\/10 { background-color: rgba(16, 185, 129, 0.1) !important; }
    .dark .dark\:border-emerald-500\/20 { border-color: rgba(16, 185, 129, 0.2) !important; }

    .dark .dark\:bg-rose-500\/10 { background-color: rgba(244, 63, 94, 0.1) !important; }
    .dark .dark\:border-rose-500\/20 { border-color: rgba(244, 63, 94, 0.2) !important; }

    .dark .dark\:bg-indigo-500\/10 { background-color: rgba(99, 102, 241, 0.1) !important; }
    .dark .dark\:border-indigo-500\/20 { border-color: rgba(99, 102, 241, 0.2) !important; }
</style>
@endpush

@section('content')

{{-- HEADER HALAMAN --}}
<div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-4 mb-8">
    <div>
        <h2 class="text-2xl md:text-3xl font-black text-slate-800 dark:text-white tracking-tight mb-1 transition-colors duration-300">
            Live Order Monitor
        </h2>
        <div class="flex items-center gap-2 text-xs font-bold text-slate-500 dark:text-slate-400 transition-colors duration-300">
            <a href="{{ route('admin.dashboard') }}" class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors text-decoration-none">Dashboard</a>
            <i class="mdi mdi-chevron-right text-sm"></i>
            <span class="text-blue-600 dark:text-blue-400">Pantau Pesanan Global</span>
        </div>
    </div>
    <div class="bg-blue-50 dark:bg-blue-500/10 border border-blue-100 dark:border-blue-500/20 px-4 py-2.5 rounded-xl flex items-center gap-3 transition-colors duration-300">
        <i class="mdi mdi-radar text-blue-500 text-xl animate-pulse"></i>
        <span class="text-xs font-bold text-blue-700 dark:text-blue-400">Sistem memantau seluruh pergerakan transaksi, logistik, dan aliran dana secara real-time.</span>
    </div>
</div>

{{-- 1. LIVE MONITORING CARDS (KPI) --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">

    {{-- Card 1: Total Transaksi --}}
    <div class="bg-white dark:bg-slate-800/40 border border-slate-200 dark:border-slate-700/50 p-5 rounded-2xl flex items-center justify-between shadow-sm hover-lift transition-colors duration-300 relative overflow-hidden group">
        <div class="absolute left-0 top-0 bottom-0 w-1 bg-blue-500"></div>
        <div class="relative z-10">
            <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1 transition-colors duration-300">Total Transaksi</div>
            <div class="text-2xl font-black text-slate-800 dark:text-white leading-none transition-colors duration-300">{{ number_format($stats['total']) }}</div>
        </div>
        <div class="w-12 h-12 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-slate-700 text-blue-500 flex items-center justify-center text-2xl flex-shrink-0 group-hover:scale-110 transition-all duration-300 relative z-10">
            <i class="mdi mdi-shopping-outline"></i>
        </div>
    </div>

    {{-- Card 2: Toko Perlu Kirim --}}
    <div class="bg-white dark:bg-slate-800/40 border border-slate-200 dark:border-slate-700/50 p-5 rounded-2xl flex items-center justify-between shadow-sm hover-lift transition-colors duration-300 relative overflow-hidden group">
        <div class="absolute left-0 top-0 bottom-0 w-1 bg-amber-500"></div>
        <div class="relative z-10">
            <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1 transition-colors duration-300">Toko Perlu Kirim</div>
            <div class="text-2xl font-black text-slate-800 dark:text-white leading-none transition-colors duration-300">{{ number_format($stats['perlu_dikirim']) }}</div>
        </div>
        <div class="w-12 h-12 rounded-xl bg-amber-50 dark:bg-amber-500/10 border border-amber-100 dark:border-amber-500/20 text-amber-500 flex items-center justify-center text-2xl flex-shrink-0 group-hover:scale-110 transition-all duration-300 relative z-10">
            <i class="mdi mdi-package-variant"></i>
        </div>
    </div>

    {{-- Card 3: Sedang di Jalan --}}
    <div class="bg-white dark:bg-slate-800/40 border border-slate-200 dark:border-slate-700/50 p-5 rounded-2xl flex items-center justify-between shadow-sm hover-lift transition-colors duration-300 relative overflow-hidden group">
        <div class="absolute left-0 top-0 bottom-0 w-1 bg-emerald-500"></div>
        <div class="relative z-10">
            <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1 transition-colors duration-300">Sedang di Jalan</div>
            <div class="text-2xl font-black text-slate-800 dark:text-white leading-none transition-colors duration-300">{{ number_format($stats['sedang_dikirim']) }}</div>
        </div>
        <div class="w-12 h-12 rounded-xl bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-100 dark:border-emerald-500/20 text-emerald-500 flex items-center justify-center text-2xl flex-shrink-0 group-hover:scale-110 transition-all duration-300 relative z-10">
            <i class="mdi mdi-truck-fast-outline"></i>
        </div>
    </div>

    {{-- Card 4: Dispute / Komplain --}}
    <div class="bg-white dark:bg-slate-800/40 border border-slate-200 dark:border-slate-700/50 p-5 rounded-2xl flex items-center justify-between shadow-sm hover-lift transition-colors duration-300 relative overflow-hidden group">
        <div class="absolute left-0 top-0 bottom-0 w-1 bg-rose-500"></div>
        <div class="relative z-10">
            <div class="text-[10px] font-black text-rose-500 dark:text-rose-400 uppercase tracking-widest mb-1 transition-colors duration-300">Dispute / Komplain</div>
            <div class="text-2xl font-black text-rose-600 dark:text-rose-400 leading-none transition-colors duration-300">{{ number_format($stats['komplain']) }}</div>
        </div>
        <div class="w-12 h-12 rounded-xl bg-rose-50 dark:bg-rose-500/10 border border-rose-100 dark:border-rose-500/20 text-rose-500 flex items-center justify-center text-2xl flex-shrink-0 group-hover:scale-110 transition-all duration-300 relative z-10">
            <i class="mdi mdi-alert-octagon-outline"></i>
        </div>
    </div>

</div>

{{-- 2. ORDER BOARD (TABEL DATA) --}}
<div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-[1.5rem] shadow-sm overflow-hidden transition-colors duration-300">

    {{-- Filter & Search Header --}}
    <div class="p-5 border-b border-slate-100 dark:border-slate-800/80 bg-white dark:bg-slate-900 flex flex-col xl:flex-row justify-between items-start xl:items-center gap-4 transition-colors duration-300">

        <div class="flex flex-col gap-3 w-full xl:w-auto">
            {{-- Tabs Source Filter (Online/POS) --}}
            <div class="flex p-1 bg-blue-50/50 dark:bg-blue-500/5 rounded-xl border border-blue-100 dark:border-blue-500/20 w-fit overflow-x-auto filter-wrapper">
                @php
                    $sources = [
                        'semua' => 'Global Monitor',
                        'online' => 'Online Only',
                        'offline' => 'POS / Offline'
                    ];
                @endphp
                @foreach($sources as $val => $label)
                    <a href="{{ route('admin.orders.index', ['sumber' => $val, 'status' => $status, 'search' => $search]) }}"
                       class="px-4 py-2 text-[10px] font-black uppercase tracking-wider rounded-lg transition-all text-decoration-none outline-none whitespace-nowrap {{ $sumber == $val ? 'bg-blue-600 text-white shadow-md' : 'text-blue-600 dark:text-blue-400 hover:bg-blue-100 dark:hover:bg-blue-500/10' }}">
                        <i class="mdi {{ $val == 'semua' ? 'mdi-earth' : ($val == 'online' ? 'mdi-web' : 'mdi-cash-register') }} mr-1.5"></i>
                        {{ $label }}
                    </a>
                @endforeach
            </div>

            {{-- Tabs Status Filter --}}
            <div class="flex p-1 bg-slate-50 dark:bg-slate-800/50 rounded-xl border border-slate-200 dark:border-slate-700/50 w-full xl:w-auto overflow-x-auto filter-wrapper">
                @php
                    $tabs = [
                        'semua' => 'Semua Status', 'pending' => 'Belum Bayar',
                        'diproses' => 'Diproses', 'dikirim' => 'Dikirim',
                        'selesai' => 'Selesai', 'komplain' => 'Komplain'
                    ];
                @endphp
                @foreach($tabs as $val => $label)
                    <a href="{{ route('admin.orders.index', ['status' => $val, 'sumber' => $sumber, 'search' => $search]) }}"
                       class="px-4 py-2 text-xs font-black capitalize rounded-lg transition-all text-decoration-none outline-none whitespace-nowrap {{ $status == $val ? 'bg-white dark:bg-slate-700 text-blue-600 dark:text-white shadow-sm border border-slate-200 dark:border-slate-600' : 'text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>
        </div>

        {{-- Search Input --}}
        <form action="{{ route('admin.orders.index') }}" method="GET" class="relative w-full xl:w-80">
            <input type="hidden" name="status" value="{{ $status }}">
            <input type="hidden" name="sumber" value="{{ $sumber }}">
            <i class="mdi mdi-magnify absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 dark:text-slate-500 text-lg"></i>
            <input type="text" name="search" value="{{ $search }}"
                   class="w-full pl-11 pr-4 py-2.5 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-bold text-slate-800 dark:text-white placeholder:text-slate-400 dark:placeholder:text-slate-500 focus:bg-white dark:focus:bg-slate-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition-all shadow-inner dark:shadow-none"
                   placeholder="Cari No. Invoice / Nama Toko...">
        </form>
    </div>

    {{-- Tabel Data --}}
    <div class="overflow-x-auto table-wrapper">
        <table class="w-full text-left border-collapse whitespace-nowrap">
            <thead>
                <tr class="bg-slate-50 dark:bg-slate-800/30 border-b border-slate-200 dark:border-slate-800 transition-colors duration-300">
                    <th class="px-6 py-4 text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest">Detail Transaksi</th>
                    <th class="px-6 py-4 text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest">Pembeli & Vendor</th>
                    <th class="px-6 py-4 text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest">Total & Pembayaran</th>
                    <th class="px-6 py-4 text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest">Informasi Logistik</th>
                    <th class="px-6 py-4 text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800/50">
                @forelse($orders as $order)
                <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors duration-200">

                    {{-- Kolom 1: Detail & Invoice --}}
                    <td class="px-6 py-5 align-top">
                        <div class="flex items-center gap-2 mb-3">
                            <a href="{{ route('admin.orders.show', $order->id) }}" class="inline-flex items-center gap-2 px-3 py-1.5 bg-blue-50 dark:bg-blue-500/10 text-blue-700 dark:text-blue-400 border border-blue-100 dark:border-blue-500/20 rounded-lg text-xs font-black font-mono hover:bg-blue-100 dark:hover:bg-blue-500/20 transition-colors text-decoration-none shadow-sm dark:shadow-none">
                                <i class="mdi mdi-receipt-text-outline text-base"></i> {{ $order->kode_invoice }}
                            </a>
                            @if(($order->sumber_transaksi ?? 'ONLINE') == 'OFFLINE' || str_starts_with($order->kode_invoice, 'POS-'))
                                <span class="px-2 py-1 bg-amber-500 text-white text-[9px] font-black uppercase rounded shadow-sm" title="Point of Sale / Offline Transaction">
                                    <i class="mdi mdi-cash-register mr-0.5"></i> POS
                                </span>
                            @else
                                <span class="px-2 py-1 bg-blue-500 text-white text-[9px] font-black uppercase rounded shadow-sm" title="Online Transaction">
                                    <i class="mdi mdi-web mr-0.5"></i> ONLINE
                                </span>
                            @endif
                        </div>

                        <div class="mb-2">
                            @php
                                $st = strtolower($order->status_pesanan);
                                $stClass = '';
                                if($st == 'pending' || $st == 'belum bayar') $stClass = 'bg-amber-50 text-amber-600 border-amber-200 dark:bg-amber-500/10 dark:text-amber-400 dark:border-amber-500/20';
                                elseif($st == 'diproses') $stClass = 'bg-blue-50 text-blue-600 border-blue-200 dark:bg-blue-500/10 dark:text-blue-400 dark:border-blue-500/20';
                                elseif($st == 'dikirim') $stClass = 'bg-indigo-50 text-indigo-600 border-indigo-200 dark:bg-indigo-500/10 dark:text-indigo-400 dark:border-indigo-500/20';
                                elseif($st == 'selesai') $stClass = 'bg-emerald-50 text-emerald-600 border-emerald-200 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-500/20';
                                elseif($st == 'batal') $stClass = 'bg-slate-100 text-slate-500 border-slate-200 dark:bg-slate-800 dark:text-slate-400 dark:border-slate-700';
                                elseif($st == 'komplain') $stClass = 'bg-rose-50 text-rose-600 border-rose-200 dark:bg-rose-500/10 dark:text-rose-400 dark:border-rose-500/20';
                                else $stClass = 'bg-slate-100 text-slate-600 border-slate-200 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700';
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[10px] font-black uppercase tracking-wider border {{ $stClass }}">
                                {{ $order->status_pesanan }}
                            </span>
                        </div>
                        <div class="text-[11px] font-bold text-slate-500 dark:text-slate-400 flex items-center gap-1.5">
                            <i class="mdi mdi-clock-outline"></i> {{ \Carbon\Carbon::parse($order->tanggal_transaksi)->format('d M Y, H:i') }} WIB
                        </div>
                    </td>

                    {{-- Kolom 2: Pembeli & Toko --}}
                    <td class="px-6 py-5 align-top">
                        <div class="mb-3">
                            <span class="block text-[9px] font-black text-slate-400 uppercase tracking-widest mb-0.5">Pembeli</span>
                            <span class="text-sm font-bold text-slate-800 dark:text-slate-200">{{ $order->nama_pembeli }}</span>
                        </div>
                        <div>
                            <span class="block text-[9px] font-black text-slate-400 uppercase tracking-widest mb-0.5">Penjual (Vendor)</span>
                            <span class="text-xs font-black text-blue-600 dark:text-blue-400 flex items-center gap-1.5">
                                <i class="mdi mdi-storefront-outline text-base"></i> {{ $order->nama_toko }}
                            </span>
                        </div>
                    </td>

                    {{-- Kolom 3: Harga & Pembayaran B2B --}}
                    <td class="px-6 py-5 align-top">
                        <div class="text-base font-black text-slate-800 dark:text-white mb-2">Rp {{ number_format($order->total_final, 0, ',', '.') }}</div>

                        {{-- Logika Tampilan LUNAS vs DP --}}
                        @if(($order->tipe_pembayaran ?? 'LUNAS') == 'DP')
                            <div class="p-3 bg-amber-50/50 dark:bg-amber-500/5 border border-dashed border-amber-300 dark:border-amber-500/30 rounded-xl w-64">
                                <strong class="text-[10px] font-black text-amber-600 dark:text-amber-400 uppercase tracking-widest block mb-2 flex items-center gap-1.5">
                                    <i class="mdi mdi-star-circle text-sm"></i> Transaksi B2B (DP)
                                </strong>
                                <div class="flex justify-between items-center mb-1.5 text-xs">
                                    <span class="font-bold text-slate-500 dark:text-slate-400">
                                        @if($order->status_pembayaran == 'dp_paid' || $order->status_pembayaran == 'paid')
                                            DP Lunas (Web):
                                        @else
                                            Tagihan DP:
                                        @endif
                                    </span>
                                    <span class="font-black {{ $order->status_pembayaran == 'pending' ? 'text-amber-600' : 'text-emerald-600' }} dark:text-emerald-400">Rp {{ number_format($order->jumlah_dp ?? 0, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between items-center pt-1.5 border-t border-amber-200 dark:border-amber-500/20 text-xs">
                                    <span class="font-bold text-slate-500 dark:text-slate-400">Tagihan Cash:</span>
                                    <span class="font-black text-rose-600 dark:text-rose-400">Rp {{ number_format($order->sisa_tagihan ?? 0, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        @else
                            <div>
                                @if($order->status_pembayaran == 'paid')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-500/20 rounded-lg text-[10px] font-black uppercase tracking-wider shadow-sm dark:shadow-none">
                                        <i class="mdi mdi-check-circle text-sm"></i> LUNAS (Gateway)
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-amber-50 dark:bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-200 dark:border-amber-500/20 rounded-lg text-[10px] font-black uppercase tracking-wider shadow-sm dark:shadow-none">
                                        <i class="mdi mdi-clock-outline text-sm"></i> Menunggu Bayar
                                    </span>
                                @endif
                            </div>
                        @endif
                    </td>

                    {{-- Kolom 4: Logistik --}}
                    <td class="px-6 py-5 align-top">
                        <div class="bg-slate-50 dark:bg-slate-800/50 border border-dashed border-slate-200 dark:border-slate-700 p-3 rounded-xl w-60">
                            @if(($order->sumber_transaksi ?? 'ONLINE') == 'OFFLINE')
                                <strong class="block text-xs font-black text-amber-600 dark:text-amber-400 mb-1 flex items-center gap-1.5">
                                    <i class="mdi mdi-shopping text-amber-500"></i> Transaksi Langsung (POS)
                                </strong>
                                <div class="text-[10px] font-bold text-slate-500 dark:text-slate-400">
                                    Metode: {{ str_replace('_', ' ', $order->metode_pengiriman ?? 'Ambil di Tempat') }}
                                </div>
                            @else
                                <strong class="block text-xs font-black text-slate-800 dark:text-slate-200 mb-1 flex items-center gap-1.5">
                                    <i class="mdi mdi-truck-delivery text-slate-400"></i> {{ $order->kurir_pengiriman ?? ($order->metode_pengiriman ? str_replace('_', ' ', $order->metode_pengiriman) : 'Belum dipilih') }}
                                </strong>
                                <div class="text-[11px] font-medium text-slate-500 dark:text-slate-400 flex flex-col gap-1">
                                    <span>Resi: <span class="font-bold text-slate-700 dark:text-slate-300">{{ $order->nomor_resi ?? 'Belum ada resi' }}</span></span>
                                </div>
                            @endif
                        </div>
                        @if($order->kurir_pengiriman == 'Armada Toko')
                            <div class="mt-2 inline-flex items-center gap-1 px-2.5 py-1 bg-slate-800 text-white dark:bg-slate-700 dark:text-slate-200 rounded text-[9px] font-black tracking-widest uppercase">
                                <i class="mdi mdi-truck-flatbed"></i> Custom Fleet
                            </div>
                        @endif
                    </td>

                    {{-- Kolom 5: Aksi --}}
                    <td class="px-6 py-5 align-top text-center">
                        <a href="{{ route('admin.orders.show', $order->id) }}" class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-500 dark:text-slate-400 hover:text-blue-600 dark:hover:text-blue-400 hover:border-blue-200 dark:hover:border-blue-500/30 hover:bg-blue-50 dark:hover:bg-blue-500/10 transition-all shadow-sm dark:shadow-none outline-none" title="Pantau Detail Order">
                            <i class="mdi mdi-eye text-lg"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-20 px-6 bg-slate-50/50 dark:bg-transparent">
                        <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-slate-100 dark:bg-slate-800/50 mb-4">
                            <i class="mdi mdi-clipboard-text-off-outline text-4xl text-slate-300 dark:text-slate-600"></i>
                        </div>
                        <h5 class="text-base font-black text-slate-700 dark:text-slate-300 mb-1">Tidak ada transaksi ditemukan</h5>
                        <p class="text-xs font-bold text-slate-500 m-0">Coba gunakan kata kunci pencarian atau tab status yang berbeda.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Footer Tabel & Pagination --}}
    <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-800/80 bg-slate-50/50 dark:bg-slate-900/50 flex flex-col md:flex-row justify-between items-center gap-4 transition-colors duration-300">
        <div class="text-xs font-bold text-slate-500 dark:text-slate-400">
            Menampilkan <span class="text-blue-600 dark:text-blue-400 font-black">{{ $orders->firstItem() ?? 0 }}</span> - <span class="text-blue-600 dark:text-blue-400 font-black">{{ $orders->lastItem() ?? 0 }}</span> dari <span class="text-slate-800 dark:text-white font-black">{{ $orders->total() }}</span> transaksi
        </div>
        <div class="pagination-wrapper">
            {{ $orders->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
@endsection
