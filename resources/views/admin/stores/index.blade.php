@extends('layouts.admin')

@section('title', 'Manajemen Mitra Toko')

@push('styles')
<style>
    /* ========================================= */
    /* ==  PREMIUM STORE MANAGEMENT CSS       == */
    /* ========================================= */
    .hover-lift { transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.3s ease; }
    .hover-lift:hover { transform: translateY(-4px); }

    .table-wrapper::-webkit-scrollbar, .filter-wrapper::-webkit-scrollbar { height: 6px; }
    .table-wrapper::-webkit-scrollbar-track, .filter-wrapper::-webkit-scrollbar-track { background: transparent; }
    .table-wrapper::-webkit-scrollbar-thumb, .filter-wrapper::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    .table-wrapper::-webkit-scrollbar-thumb:hover, .filter-wrapper::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

    .dark .table-wrapper::-webkit-scrollbar-thumb, .dark .filter-wrapper::-webkit-scrollbar-thumb { background: #475569; }
    .dark .table-wrapper::-webkit-scrollbar-thumb:hover, .dark .filter-wrapper::-webkit-scrollbar-thumb:hover { background: #64748b; }

    .dark .modal-content { background-color: #0f172a !important; border: 1px solid #1e293b !important; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5) !important; color: #f8fafc !important;}
    .dark .modal-header, .dark .border-bottom { border-bottom-color: #1e293b !important; background-color: rgba(30, 41, 59, 0.5) !important; }
    .dark .modal-footer, .dark .border-top { border-top-color: #1e293b !important; }
    .dark .btn-close { filter: invert(1) grayscale(100%) brightness(200%); opacity: 0.5; }
    .dark .btn-close:hover { opacity: 1; }
    .dark .text-dark { color: #f8fafc !important; }
    .dark .text-muted { color: #94a3b8 !important; }
    .dark .bg-light { background-color: #1e293b !important; }

    .dark .pagination .page-link { background-color: #1e293b; border-color: #334155; color: #cbd5e1; }
    .dark .pagination .page-item.active .page-link { background-color: #3b82f6; border-color: #3b82f6; color: white; }
    .dark .pagination .page-item.disabled .page-link { background-color: #0f172a; color: #475569; border-color: #1e293b; }

    .tier-radio:checked + label { border-color: var(--checked-border) !important; background-color: var(--checked-bg) !important; }
    .tier-radio:checked + label .check-icon { display: block !important; color: var(--checked-text) !important; }
    .tier-radio:not(:checked) + label .check-icon { display: none !important; }

    /* ========================================= */
    /* == POLYFILL DARK MODE (ANTI-PUTIH)     == */
    /* ========================================= */
    .dark .dark\:bg-slate-900 { background-color: #0f172a !important; }
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

    /* Custom Colors */
    .dark .dark\:bg-blue-500\/10 { background-color: rgba(59, 130, 246, 0.1) !important; }
    .dark .dark\:border-blue-500\/20 { border-color: rgba(59, 130, 246, 0.2) !important; }

    .dark .dark\:bg-purple-500\/10 { background-color: rgba(168, 85, 247, 0.1) !important; }
    .dark .dark\:bg-purple-500\/20 { background-color: rgba(168, 85, 247, 0.2) !important; }
    .dark .dark\:border-purple-500\/20 { border-color: rgba(168, 85, 247, 0.2) !important; }
    .dark .dark\:border-purple-500\/30 { border-color: rgba(168, 85, 247, 0.3) !important; }

    .dark .dark\:bg-emerald-500\/10 { background-color: rgba(16, 185, 129, 0.1) !important; }
    .dark .dark\:bg-emerald-500\/20 { background-color: rgba(16, 185, 129, 0.2) !important; }
    .dark .dark\:border-emerald-500\/20 { border-color: rgba(16, 185, 129, 0.2) !important; }
    .dark .dark\:border-emerald-500\/30 { border-color: rgba(16, 185, 129, 0.3) !important; }
</style>
@endpush

@section('content')

{{-- HEADER HALAMAN --}}
<div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-4 mb-8">
    <div>
        <h2 class="text-2xl md:text-3xl font-black text-slate-800 dark:text-white tracking-tight mb-1 transition-colors duration-300">
            Manajemen Mitra Toko
        </h2>
        <div class="flex items-center gap-2 text-xs font-bold text-slate-500 dark:text-slate-400 transition-colors duration-300">
            <a href="{{ route('admin.dashboard') }}" class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors text-decoration-none">Dashboard</a>
            <i class="mdi mdi-chevron-right text-sm"></i>
            <span class="text-blue-600 dark:text-blue-400">Kelola Toko & Kasta (Tier)</span>
        </div>
    </div>
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.stores.tierApplications') }}" class="bg-amber-500 hover:bg-amber-600 text-white px-5 py-2.5 rounded-xl flex items-center gap-2 transition-all shadow-md text-decoration-none">
            <i class="mdi mdi-rocket-launch text-xl"></i>
            <span class="text-xs font-black uppercase">Pengajuan Naik Level</span>
        </a>
    </div>
</div>

{{-- 1. STATS CARDS (KPI) --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-5 mb-8">

    {{-- Card 1: Total Mitra --}}
    <div class="bg-white dark:bg-slate-800/40 border border-slate-200 dark:border-slate-700/50 p-5 rounded-2xl flex items-center gap-4 shadow-sm hover-lift transition-colors duration-300 group">
        <div class="w-14 h-14 rounded-xl bg-slate-800 dark:bg-slate-700 text-white flex items-center justify-center text-3xl flex-shrink-0 group-hover:scale-110 transition-transform">
            <i class="mdi mdi-storefront-outline"></i>
        </div>
        <div>
            <div class="text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-1 transition-colors duration-300">Total Mitra</div>
            <div class="text-3xl font-black text-slate-800 dark:text-white leading-none transition-colors duration-300">{{ number_format($stats['total']) }}</div>
        </div>
    </div>

    {{-- Card 2: Official Store --}}
    <div class="bg-white dark:bg-slate-800/40 border border-purple-200 dark:border-purple-500/30 p-5 rounded-2xl flex items-center gap-4 shadow-sm hover-lift transition-colors duration-300 group">
        <div class="w-14 h-14 rounded-xl bg-purple-100 dark:bg-purple-500/20 text-purple-600 dark:text-purple-400 flex items-center justify-center text-3xl flex-shrink-0 group-hover:scale-110 transition-transform border border-purple-200 dark:border-purple-500/30">
            <i class="mdi mdi-check-decagram"></i>
        </div>
        <div>
            <div class="text-[10px] font-black text-purple-600 dark:text-purple-400 uppercase tracking-widest mb-1 transition-colors duration-300">Official Store</div>
            <div class="text-3xl font-black text-purple-700 dark:text-purple-300 leading-none transition-colors duration-300">{{ number_format($stats['official']) }}</div>
        </div>
    </div>

    {{-- Card 3: Power Merchant --}}
    <div class="bg-white dark:bg-slate-800/40 border border-emerald-200 dark:border-emerald-500/30 p-5 rounded-2xl flex items-center gap-4 shadow-sm hover-lift transition-colors duration-300 group">
        <div class="w-14 h-14 rounded-xl bg-emerald-100 dark:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-3xl flex-shrink-0 group-hover:scale-110 transition-transform border border-emerald-200 dark:border-emerald-500/30">
            <i class="mdi mdi-lightning-bolt"></i>
        </div>
        <div>
            <div class="text-[10px] font-black text-emerald-600 dark:text-emerald-400 uppercase tracking-widest mb-1 transition-colors duration-300">Power Merchant</div>
            <div class="text-3xl font-black text-emerald-700 dark:text-emerald-300 leading-none transition-colors duration-300">{{ number_format($stats['power']) }}</div>
        </div>
    </div>

</div>

{{-- 2. TABEL MITRA TOKO --}}
<div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-[1.5rem] shadow-sm overflow-hidden transition-colors duration-300">

    {{-- Filter & Search Header --}}
    <div class="p-5 border-b border-slate-100 dark:border-slate-800/80 bg-white dark:bg-slate-900 flex flex-col xl:flex-row justify-between items-start xl:items-center gap-4 transition-colors duration-300">

        {{-- Tabs Filter Tier --}}
        <div class="flex p-1 bg-slate-50 dark:bg-slate-800/50 rounded-xl border border-slate-200 dark:border-slate-700/50 w-full xl:w-auto overflow-x-auto filter-wrapper shadow-inner dark:shadow-none">
            <a href="{{ route('admin.stores.index', ['tier' => 'semua', 'search' => $search]) }}"
               class="px-4 py-2 text-xs font-black capitalize rounded-lg transition-all text-decoration-none outline-none whitespace-nowrap {{ $tier_filter == 'semua' ? 'bg-white dark:bg-slate-700 text-slate-800 dark:text-white shadow-sm border border-slate-200 dark:border-slate-600' : 'text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200' }}">
                Semua
            </a>
            <a href="{{ route('admin.stores.index', ['tier' => 'official_store', 'search' => $search]) }}"
               class="flex items-center gap-1.5 px-4 py-2 text-xs font-black capitalize rounded-lg transition-all text-decoration-none outline-none whitespace-nowrap {{ $tier_filter == 'official_store' ? 'bg-white dark:bg-slate-700 text-purple-600 dark:text-purple-400 shadow-sm border border-slate-200 dark:border-slate-600' : 'text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200' }}">
                <i class="mdi mdi-check-decagram text-sm"></i> Official
            </a>
            <a href="{{ route('admin.stores.index', ['tier' => 'power_merchant', 'search' => $search]) }}"
               class="flex items-center gap-1.5 px-4 py-2 text-xs font-black capitalize rounded-lg transition-all text-decoration-none outline-none whitespace-nowrap {{ $tier_filter == 'power_merchant' ? 'bg-white dark:bg-slate-700 text-emerald-600 dark:text-emerald-400 shadow-sm border border-slate-200 dark:border-slate-600' : 'text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200' }}">
                <i class="mdi mdi-lightning-bolt text-sm"></i> Power
            </a>
            <a href="{{ route('admin.stores.index', ['tier' => 'regular', 'search' => $search]) }}"
               class="px-4 py-2 text-xs font-black capitalize rounded-lg transition-all text-decoration-none outline-none whitespace-nowrap {{ $tier_filter == 'regular' ? 'bg-white dark:bg-slate-700 text-slate-800 dark:text-white shadow-sm border border-slate-200 dark:border-slate-600' : 'text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200' }}">
                Reguler
            </a>
        </div>

        {{-- Search Input --}}
        <form action="{{ route('admin.stores.index') }}" method="GET" class="relative w-full xl:w-80">
            <input type="hidden" name="tier" value="{{ $tier_filter }}">
            <i class="mdi mdi-magnify absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 dark:text-slate-500 text-lg"></i>
            <input type="text" name="search" value="{{ $search }}"
                   class="w-full pl-11 pr-4 py-2.5 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-bold text-slate-800 dark:text-white placeholder:text-slate-400 dark:placeholder:text-slate-500 focus:bg-white dark:focus:bg-slate-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition-all shadow-inner dark:shadow-none"
                   placeholder="Cari nama toko / pemilik...">
        </form>
    </div>

    {{-- Tabel Data --}}
    <div class="overflow-x-auto table-wrapper">
        <table class="w-full text-left border-collapse whitespace-nowrap">
            <thead>
                <tr class="bg-slate-50 dark:bg-slate-800/30 border-b border-slate-200 dark:border-slate-800 transition-colors duration-300">
                    <th class="px-6 py-4 text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest">Informasi Toko</th>
                    <th class="px-6 py-4 text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest">Pemilik & Kontak</th>
                    <th class="px-6 py-4 text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest">Kasta / Tier Saat Ini</th>
                    <th class="px-6 py-4 text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800/50">
                @forelse($stores as $s)
                <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors duration-200">

                    {{-- Kolom 1: Informasi Toko --}}
                    <td class="px-6 py-5 align-middle">
                        <div class="flex items-center gap-4">
                            @if($s->logo_toko)
                                <img src="{{ asset('storage/'.$s->logo_toko) }}" class="w-12 h-12 rounded-xl object-cover border border-slate-200 dark:border-slate-700 shadow-sm" alt="Logo">
                            @else
                                <div class="w-12 h-12 rounded-xl bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 flex items-center justify-center text-xl font-black text-slate-400 dark:text-slate-500 shadow-sm">
                                    {{ strtoupper(substr($s->nama_toko, 0, 1)) }}
                                </div>
                            @endif
                            <div>
                                <strong class="block text-sm font-black text-slate-800 dark:text-white mb-1">{{ $s->nama_toko }}</strong>
                                <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400 flex items-center gap-1.5"><i class="mdi mdi-map-marker-outline"></i> {{ $s->kota_kabupaten ?? 'Kota belum diatur' }}</span>
                            </div>
                        </div>
                    </td>

                    {{-- Kolom 2: Pemilik & Kontak --}}
                    <td class="px-6 py-5 align-middle">
                        <strong class="block text-sm font-bold text-slate-800 dark:text-slate-200 mb-1">{{ $s->nama_pemilik }}</strong>
                        <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400 flex items-center gap-1.5"><i class="mdi mdi-phone-outline"></i> {{ $s->telepon_pemilik ?? '-' }}</span>
                    </td>

                    {{-- Kolom 3: Kasta / Tier --}}
                    <td class="px-6 py-5 align-middle">
                        @if($s->tier_toko == 'official_store')
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-purple-50 dark:bg-purple-500/10 text-purple-600 dark:text-purple-400 border border-purple-200 dark:border-purple-500/20 rounded-lg text-[10px] font-black uppercase tracking-widest shadow-sm dark:shadow-none">
                                <i class="mdi mdi-check-decagram text-base"></i> Official Store
                            </span>
                        @elseif($s->tier_toko == 'power_merchant')
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-500/20 rounded-lg text-[10px] font-black uppercase tracking-widest shadow-sm dark:shadow-none">
                                <i class="mdi mdi-lightning-bolt text-base"></i> Power Merchant
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-700 rounded-lg text-[10px] font-black uppercase tracking-widest shadow-sm dark:shadow-none">
                                <i class="mdi mdi-storefront-outline text-base"></i> Reguler
                            </span>
                        @endif
                    </td>

                    {{-- Kolom 4: Aksi --}}
                    <td class="px-6 py-5 align-middle text-center">
                        <button class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-xl bg-slate-800 dark:bg-slate-700 text-white text-xs font-bold hover:bg-slate-900 dark:hover:bg-slate-600 transition-colors shadow-md outline-none btn-upgrade"
                            data-bs-toggle="modal" data-bs-target="#modalTier"
                            data-id="{{ $s->id }}"
                            data-nama="{{ $s->nama_toko }}"
                            data-tier="{{ $s->tier_toko }}">
                            <i class="mdi mdi-star-circle-outline text-lg"></i> Ubah Kasta
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center py-20 px-6 bg-slate-50/50 dark:bg-transparent">
                        <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-slate-100 dark:bg-slate-800/50 mb-4">
                            <i class="mdi mdi-store-off-outline text-4xl text-slate-400 dark:text-slate-600"></i>
                        </div>
                        <h5 class="text-base font-black text-slate-700 dark:text-slate-300 mb-1">Tidak ada data toko ditemukan</h5>
                        <p class="text-xs font-bold text-slate-500 m-0">Coba gunakan kata kunci pencarian atau tab filter yang berbeda.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Footer Tabel & Pagination --}}
    <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-800/80 bg-slate-50/50 dark:bg-slate-900/50 flex flex-col md:flex-row justify-between items-center gap-4 transition-colors duration-300">
        <div class="text-xs font-bold text-slate-500 dark:text-slate-400">
            Menampilkan <span class="text-blue-600 dark:text-blue-400 font-black">{{ $stores->firstItem() ?? 0 }}</span> - <span class="text-blue-600 dark:text-blue-400 font-black">{{ $stores->lastItem() ?? 0 }}</span> dari <span class="text-slate-800 dark:text-white font-black">{{ $stores->total() }}</span> toko
        </div>
        <div class="pagination-wrapper">
            {{ $stores->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>

{{-- ============================================================================== --}}
{{-- MODAL UBAH TIER TOKO (MENDUKUNG DARK MODE)                                     --}}
{{-- ============================================================================== --}}
<div class="modal fade" id="modalTier" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="formTier" method="POST" action="">
                @csrf
                <div class="modal-header">
                    <h5 class="font-black text-slate-800 flex items-center gap-2 m-0 text-base">
                        <i class="mdi mdi-star-circle text-amber-500 text-xl"></i> Pengaturan Kasta Mitra
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-6">
                    <p class="text-[11px] font-bold text-slate-500 dark:text-slate-400 mb-5 leading-relaxed">
                        Pilih tingkatan baru untuk toko <strong id="mdl-nama-toko" class="text-slate-800 dark:text-white font-black px-1.5 py-0.5 bg-slate-100 dark:bg-slate-800 rounded"></strong>. Perubahan ini akan memengaruhi badge toko di sisi pembeli.
                    </p>

                    <div class="space-y-3">
                        {{-- Opsi: Official Store --}}
                        <div class="relative">
                            <input type="radio" name="tier_toko" value="official_store" id="t_official" class="tier-radio hidden">
                            <label for="t_official" class="flex items-start gap-4 p-4 border-2 border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800/50 rounded-xl cursor-pointer transition-colors w-full" style="--checked-border: #8b5cf6; --checked-bg: #ede9fe; --checked-text: #7c3aed;">
                                <i class="mdi mdi-check-decagram text-3xl text-purple-500"></i>
                                <div class="flex-1">
                                    <strong class="block text-sm font-black text-slate-800 dark:text-white mb-1">Official Store</strong>
                                    <span class="text-[10px] font-bold text-slate-500 dark:text-slate-400">Distributor resmi atau principal merk berbadan hukum (PT/CV).</span>
                                </div>
                                <i class="mdi mdi-check-circle check-icon text-xl" style="display: none;"></i>
                            </label>
                            {{-- CSS Helper for Dark Mode override --}}
                            <style>.dark .tier-radio:checked#t_official + label { background-color: rgba(139, 92, 246, 0.1) !important; }</style>
                        </div>

                        {{-- Opsi: Power Merchant --}}
                        <div class="relative">
                            <input type="radio" name="tier_toko" value="power_merchant" id="t_power" class="tier-radio hidden">
                            <label for="t_power" class="flex items-start gap-4 p-4 border-2 border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800/50 rounded-xl cursor-pointer transition-colors w-full" style="--checked-border: #10b981; --checked-bg: #ecfdf5; --checked-text: #059669;">
                                <i class="mdi mdi-lightning-bolt text-3xl text-emerald-500"></i>
                                <div class="flex-1">
                                    <strong class="block text-sm font-black text-slate-800 dark:text-white mb-1">Power Merchant</strong>
                                    <span class="text-[10px] font-bold text-slate-500 dark:text-slate-400">Toko dengan reputasi sangat baik, penjualan tinggi, dan pelayanan responsif.</span>
                                </div>
                                <i class="mdi mdi-check-circle check-icon text-xl" style="display: none;"></i>
                            </label>
                            <style>.dark .tier-radio:checked#t_power + label { background-color: rgba(16, 185, 129, 0.1) !important; }</style>
                        </div>

                        {{-- Opsi: Regular --}}
                        <div class="relative">
                            <input type="radio" name="tier_toko" value="regular" id="t_regular" class="tier-radio hidden">
                            <label for="t_regular" class="flex items-start gap-4 p-4 border-2 border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800/50 rounded-xl cursor-pointer transition-colors w-full" style="--checked-border: #64748b; --checked-bg: #f8fafc; --checked-text: #475569;">
                                <i class="mdi mdi-storefront-outline text-3xl text-slate-500 dark:text-slate-400"></i>
                                <div class="flex-1">
                                    <strong class="block text-sm font-black text-slate-800 dark:text-white mb-1">Toko Reguler</strong>
                                    <span class="text-[10px] font-bold text-slate-500 dark:text-slate-400">Tingkat standar untuk semua penjual baru yang mendaftar di platform.</span>
                                </div>
                                <i class="mdi mdi-check-circle check-icon text-xl" style="display: none;"></i>
                            </label>
                            <style>.dark .tier-radio:checked#t_regular + label { background-color: rgba(100, 116, 139, 0.1) !important; }</style>
                        </div>
                    </div>
                </div>

                <div class="flex gap-3 justify-end mt-2 border-t border-slate-100 dark:border-slate-800 p-5">
                    <button type="button" class="px-5 py-2.5 rounded-xl font-bold text-sm text-slate-600 dark:text-slate-300 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 transition-colors outline-none" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="px-6 py-2.5 rounded-xl font-bold text-sm text-white bg-slate-800 hover:bg-slate-900 dark:bg-blue-600 dark:hover:bg-blue-700 shadow-md transition-all outline-none">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {

        // PERBAIKAN BUG: Pindahkan Modal ke Body SETELAH DOM selesai dimuat
        document.querySelectorAll('.modal').forEach(modal => {
            document.body.appendChild(modal);
        });

        // Logika Mengisi Data Modal Edit Tier Toko
        document.querySelectorAll('.btn-upgrade').forEach(button => {
            button.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const nama = this.getAttribute('data-nama');
                const currentTier = this.getAttribute('data-tier');

                // Set nama toko di text keterangan
                document.getElementById('mdl-nama-toko').innerText = nama;

                // Set action URL form (Pastikan rute ini benar di web.php Anda)
                document.getElementById('formTier').action = `/portal-rahasia-pks/stores/${id}/tier`;

                // Reset semua checked status terlebih dahulu
                document.getElementById('t_official').checked = false;
                document.getElementById('t_power').checked = false;
                document.getElementById('t_regular').checked = false;

                // Centang radio button sesuai tier saat ini
                if(currentTier === 'official_store') document.getElementById('t_official').checked = true;
                else if(currentTier === 'power_merchant') document.getElementById('t_power').checked = true;
                else document.getElementById('t_regular').checked = true;
            });
        });

    });
</script>
@endpush
