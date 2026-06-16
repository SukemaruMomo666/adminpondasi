@extends('layouts.admin')

@section('title', 'Command Center')

@push('styles')
<style>
    /* ========================================= */
    /* ==  PREMIUM DASHBOARD CSS (TRUE BLACK) == */
    /* ========================================= */

    /* Animasi Mengambang */
    @keyframes floatSoft {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-4px); }
    }
    .animate-float { animation: floatSoft 4s ease-in-out infinite; }

    /* Scrollbar Khusus untuk Tabel - OLED Edition */
    .table-container::-webkit-scrollbar { height: 6px; }
    .table-container::-webkit-scrollbar-track { background: transparent; }
    .table-container::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    .table-container::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

    .dark .table-container::-webkit-scrollbar-thumb { background: #475569; }
    .dark .table-container::-webkit-scrollbar-thumb:hover { background: #64748b; }

    /* Efek Glow Khusus Dark Mode */
    .dark .text-glow-blue { text-shadow: 0 0 20px rgba(59, 130, 246, 0.6); }
    .dark .text-glow-emerald { text-shadow: 0 0 20px rgba(16, 185, 129, 0.6); }
    .dark .text-glow-rose { text-shadow: 0 0 20px rgba(244, 63, 94, 0.6); }

    /* ========================================= */
    /* == POLYFILL DARK MODE (ANTI-TRANSPARAN) == */
    /* ========================================= */
    .dark .dark\:bg-slate-800 { background-color: #1e293b !important; }
    .dark .dark\:bg-slate-900 { background-color: #0f172a !important; }
    .dark .dark\:bg-slate-950 { background-color: #020617 !important; }
    .dark .dark\:border-slate-700 { border-color: #334155 !important; }
    .dark .dark\:bg-slate-700 { background-color: #334155 !important; }
    .dark .dark\:hover\:bg-slate-600:hover { background-color: #475569 !important; }
    .dark .dark\:bg-slate-900\/50 { background-color: rgba(15, 23, 42, 0.5) !important; }
    .dark .dark\:bg-slate-800\/50 { background-color: rgba(30, 41, 59, 0.5) !important; }
    .dark .dark\:hover\:bg-slate-700\/50:hover { background-color: rgba(51, 65, 85, 0.5) !important; }
</style>
@endpush

@section('content')
    {{-- HEADER DASHBOARD --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-4 mb-8">
        <div>
            <h2 class="text-2xl md:text-3xl font-black text-slate-800 dark:text-white tracking-tight flex items-center gap-3 mb-1 transition-colors duration-500">
                <div class="w-10 h-10 bg-blue-50 dark:bg-blue-900/30 border dark:border-blue-500/20 text-blue-600 dark:text-blue-400 rounded-xl flex items-center justify-center transition-colors duration-500 shadow-inner">
                    <i class="mdi mdi-radar text-2xl dark:animate-pulse"></i>
                </div>
                Sistem Pengawasan
            </h2>
            <p class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest pl-14 m-0 transition-colors duration-500">Monitoring Operasional Real-Time</p>
        </div>

        <div class="flex items-center gap-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-4 py-2.5 rounded-xl shadow-sm dark:shadow-none transition-colors duration-500">
            <i class="mdi mdi-calendar-clock text-blue-500 dark:text-blue-400 text-lg"></i>
            <span class="text-sm font-bold text-slate-700 dark:text-slate-200">{{ \Carbon\Carbon::now()->translatedFormat('d F Y • H:i') }}</span>
        </div>
    </div>

    {{-- KOTAK ANTREAN TINDAKAN (TASK GRID) --}}
    <div class="mb-3">
        <h4 class="text-[11px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-[0.2em] flex items-center gap-2 mb-4 transition-colors duration-500">
            <i class="mdi mdi-inbox-arrow-down text-lg"></i> Antrean Tindakan
        </h4>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5 mb-10">
        {{-- Task 1: Verifikasi Toko --}}
        <a href="{{ route('admin.stores.index', ['status' => 'pending']) }}" class="group bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:border-amber-300 dark:hover:border-amber-500/50 rounded-[2rem] p-6 flex items-center gap-5 transition-all duration-300 hover:-translate-y-1 hover:shadow-lg hover:shadow-amber-500/10 text-decoration-none relative overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-amber-500/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity hidden dark:block"></div>
            <div class="w-14 h-14 rounded-xl bg-amber-50 dark:bg-amber-500/10 border dark:border-amber-500/20 text-amber-500 dark:text-amber-400 flex items-center justify-center text-2xl flex-shrink-0 group-hover:scale-110 transition-transform relative z-10">
                <i class="mdi mdi-store-alert"></i>
            </div>
            <div class="relative z-10">
                <h4 class="text-2xl font-black text-slate-800 dark:text-white leading-none mb-1 transition-colors duration-500">{{ $tugas['toko_pending'] ?? 0 }}</h4>
                <p class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest m-0 transition-colors duration-500">Verifikasi Toko</p>
            </div>
        </a>

        {{-- Task 2: Moderasi Material --}}
        <a href="{{ route('admin.products.index', ['status' => 'pending']) }}" class="group bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:border-blue-300 dark:hover:border-blue-500/50 rounded-[2rem] p-6 flex items-center gap-5 transition-all duration-300 hover:-translate-y-1 hover:shadow-lg hover:shadow-blue-500/10 text-decoration-none relative overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-blue-500/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity hidden dark:block"></div>
            <div class="w-14 h-14 rounded-xl bg-blue-50 dark:bg-blue-900/40 border dark:border-blue-500/20 text-blue-500 dark:text-blue-400 flex items-center justify-center text-2xl flex-shrink-0 group-hover:scale-110 transition-transform relative z-10">
                <i class="mdi mdi-cube-send"></i>
            </div>
            <div class="relative z-10">
                <h4 class="text-2xl font-black text-slate-800 dark:text-white leading-none mb-1 transition-colors duration-500">{{ $tugas['produk_pending'] ?? 0 }}</h4>
                <p class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest m-0 transition-colors duration-500">Moderasi Material</p>
            </div>
        </a>

        {{-- Task 3: Pencairan Dana --}}
        <a href="{{ route('admin.payouts.index') }}" class="group bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:border-emerald-300 dark:hover:border-emerald-500/50 rounded-[2rem] p-6 flex items-center gap-5 transition-all duration-300 hover:-translate-y-1 hover:shadow-lg hover:shadow-emerald-500/10 text-decoration-none relative overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-emerald-500/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity hidden dark:block"></div>
            <div class="w-14 h-14 rounded-xl bg-emerald-50 dark:bg-emerald-500/10 border dark:border-emerald-500/20 text-emerald-500 dark:text-emerald-400 flex items-center justify-center text-2xl flex-shrink-0 group-hover:scale-110 transition-transform relative z-10">
                <i class="mdi mdi-cash-sync"></i>
            </div>
            <div class="relative z-10">
                <h4 class="text-2xl font-black text-slate-800 dark:text-white leading-none mb-1 transition-colors duration-500">{{ $tugas['payout_pending'] ?? 0 }}</h4>
                <p class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest m-0 transition-colors duration-500">Pencairan Dana</p>
            </div>
        </a>

        {{-- Task 4: Komplain --}}
        <a href="{{ route('admin.disputes.index', ['status' => 'aktif']) }}" class="group bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:border-rose-300 dark:hover:border-rose-500/50 rounded-[2rem] p-6 flex items-center gap-5 transition-all duration-300 hover:-translate-y-1 hover:shadow-lg hover:shadow-rose-500/10 text-decoration-none relative overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-rose-500/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity hidden dark:block"></div>
            <div class="w-14 h-14 rounded-xl bg-rose-50 dark:bg-rose-500/10 border dark:border-rose-500/20 text-rose-500 dark:text-rose-400 flex items-center justify-center text-2xl flex-shrink-0 group-hover:scale-110 transition-transform relative z-10">
                <i class="mdi mdi-alert-decagram"></i>
            </div>
            <div class="relative z-10">
                <h4 class="text-2xl font-black {{ ($tugas['komplain_aktif'] ?? 0) > 0 ? 'text-rose-500 dark:text-glow-rose' : 'text-slate-800 dark:text-white' }} leading-none mb-1 transition-colors duration-500">{{ $tugas['komplain_aktif'] ?? 0 }}</h4>
                <p class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest m-0 transition-colors duration-500">Komplain Aktif</p>
            </div>
        </a>
    </div>

    {{-- STATISTIK PLATFORM (KPI) --}}
    <div class="mb-3 mt-4">
        <h4 class="text-[11px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-[0.2em] flex items-center gap-2 mb-4 transition-colors duration-500">
            <i class="mdi mdi-chart-box-outline text-lg"></i> Matrik Platform Utama
        </h4>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-10">

        <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-[2rem] p-6 shadow-sm relative overflow-hidden group hover:border-blue-200 dark:hover:border-slate-500 transition-colors duration-500">
            <div class="absolute inset-y-0 left-0 w-1.5 bg-blue-500 dark:bg-gradient-to-b dark:from-blue-400 dark:to-indigo-500 rounded-l-2xl transition-colors duration-500"></div>
            <div class="absolute right-[-10px] bottom-[-20px] opacity-5 dark:opacity-5 group-hover:opacity-10 transition-opacity">
                <i class="mdi mdi-wallet-outline text-[100px] text-blue-600 dark:text-white"></i>
            </div>
            <div class="flex justify-between items-start mb-4 relative z-10">
                <p class="text-[10px] font-bold text-slate-400 dark:text-slate-400 uppercase tracking-widest m-0">Total GMV</p>
                <i class="mdi mdi-wallet-outline text-slate-400 dark:text-slate-500 text-lg"></i>
            </div>
            <h3 class="text-2xl font-black text-blue-600 dark:text-white mb-2 relative z-10 transition-colors duration-500 dark:text-glow-blue">Rp {{ number_format($statistik['total_penjualan'] ?? 0, 0, ',', '.') }}</h3>
            <p class="text-[10px] font-bold text-emerald-500 dark:text-emerald-400 m-0 flex items-center gap-1 relative z-10 transition-colors duration-500"><i class="mdi mdi-arrow-top-right"></i> Pertumbuhan Positif</p>
        </div>

        <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-[2rem] p-6 shadow-sm relative overflow-hidden group hover:border-slate-300 dark:hover:border-slate-500 transition-colors duration-500">
            <div class="absolute inset-y-0 left-0 w-1.5 bg-slate-300 dark:bg-slate-500 rounded-l-2xl transition-colors duration-500"></div>
            <div class="absolute right-[-10px] bottom-[-20px] opacity-5 dark:opacity-5 group-hover:opacity-10 transition-opacity">
                <i class="mdi mdi-account-group text-[100px] text-slate-600 dark:text-white"></i>
            </div>
            <div class="flex justify-between items-start mb-4 relative z-10">
                <p class="text-[10px] font-bold text-slate-400 dark:text-slate-400 uppercase tracking-widest m-0">Pengguna Aktif</p>
                <i class="mdi mdi-account-group text-slate-400 dark:text-slate-500 text-lg"></i>
            </div>
            <h3 class="text-2xl font-black text-slate-800 dark:text-white mb-2 relative z-10 transition-colors duration-500">{{ number_format($statistik['total_pengguna'] ?? 0) }}</h3>
            <p class="text-[10px] font-bold text-slate-400 dark:text-slate-400 m-0 flex items-center gap-1 relative z-10 transition-colors duration-500"><i class="mdi mdi-minus"></i> Total Keseluruhan</p>
        </div>

        <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-[2rem] p-6 shadow-sm relative overflow-hidden group hover:border-slate-300 dark:hover:border-slate-500 transition-colors duration-500">
            <div class="absolute inset-y-0 left-0 w-1.5 bg-slate-300 dark:bg-slate-500 rounded-l-2xl transition-colors duration-500"></div>
            <div class="absolute right-[-10px] bottom-[-20px] opacity-5 dark:opacity-5 group-hover:opacity-10 transition-opacity">
                <i class="mdi mdi-storefront text-[100px] text-slate-600 dark:text-white"></i>
            </div>
            <div class="flex justify-between items-start mb-4 relative z-10">
                <p class="text-[10px] font-bold text-slate-400 dark:text-slate-400 uppercase tracking-widest m-0">Toko Material</p>
                <i class="mdi mdi-storefront text-slate-400 dark:text-slate-500 text-lg"></i>
            </div>
            <h3 class="text-2xl font-black text-slate-800 dark:text-white mb-2 relative z-10 transition-colors duration-500">{{ number_format($statistik['total_toko'] ?? 0) }}</h3>
            <p class="text-[10px] font-bold text-slate-400 dark:text-slate-400 m-0 flex items-center gap-1 relative z-10 transition-colors duration-500"><i class="mdi mdi-check-circle-outline"></i> Mitra Terverifikasi</p>
        </div>

        <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-[2rem] p-6 shadow-sm relative overflow-hidden group hover:border-slate-300 dark:hover:border-slate-500 transition-colors duration-500">
            <div class="absolute inset-y-0 left-0 w-1.5 bg-slate-300 dark:bg-slate-500 rounded-l-2xl transition-colors duration-500"></div>
            <div class="absolute right-[-10px] bottom-[-20px] opacity-5 dark:opacity-5 group-hover:opacity-10 transition-opacity">
                <i class="mdi mdi-hard-hat text-[100px] text-slate-600 dark:text-white"></i>
            </div>
            <div class="flex justify-between items-start mb-4 relative z-10">
                <p class="text-[10px] font-bold text-slate-400 dark:text-slate-400 uppercase tracking-widest m-0">Total Material</p>
                <i class="mdi mdi-hard-hat text-slate-400 dark:text-slate-500 text-lg"></i>
            </div>
            <h3 class="text-2xl font-black text-slate-800 dark:text-white mb-2 relative z-10 transition-colors duration-500">{{ number_format($statistik['total_produk'] ?? 0) }}</h3>
            <p class="text-[10px] font-bold text-slate-400 dark:text-slate-400 m-0 flex items-center gap-1 relative z-10 transition-colors duration-500"><i class="mdi mdi-package-variant"></i> Katalog Aktif</p>
        </div>

    </div>

    {{-- GRAFIK & LOGISTIK ROW --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-10">

        {{-- GRAFIK --}}
        <div class="lg:col-span-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-[2rem] p-6 shadow-sm relative overflow-hidden transition-colors duration-500">
            <div class="flex justify-between items-center mb-6 relative z-10">
                <h4 class="text-sm font-black text-slate-800 dark:text-white tracking-wide transition-colors duration-500">Pertumbuhan Pengguna (7 Hari)</h4>
            </div>
            <div class="w-full h-[280px] relative z-10">
                <canvas id="mainChart"></canvas>
            </div>
        </div>

        {{-- LOGISTIK WIDGET --}}
        <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-[2rem] p-6 shadow-sm flex flex-col transition-colors duration-500">
            <h4 class="text-sm font-black text-slate-800 dark:text-white tracking-wide mb-6 transition-colors duration-500">Logistik & Armada Global</h4>

            <div class="flex-1 flex flex-col gap-4 justify-center">
                <div class="bg-slate-50 dark:bg-slate-900 border border-slate-100 dark:border-slate-700 p-4 rounded-2xl hover:border-emerald-200 dark:hover:border-emerald-500/50 transition-colors group">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-[10px] font-black text-slate-400 dark:text-slate-400 uppercase tracking-widest">Dalam Pengiriman</span>
                        <i class="mdi mdi-truck-delivery text-emerald-500 dark:text-emerald-400 text-xl group-hover:scale-110 transition-transform"></i>
                    </div>
                    <div class="text-2xl font-black text-slate-800 dark:text-white transition-colors duration-500">-- <span class="text-xs font-medium text-slate-500">Surat Jalan</span></div>
                </div>

                <div class="bg-slate-50 dark:bg-slate-900 border border-slate-100 dark:border-slate-700 p-4 rounded-2xl hover:border-amber-200 dark:hover:border-amber-500/50 transition-colors group">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-[10px] font-black text-slate-400 dark:text-slate-400 uppercase tracking-widest">Menunggu Pickup</span>
                        <i class="mdi mdi-package-variant text-amber-500 dark:text-amber-400 text-xl group-hover:scale-110 transition-transform"></i>
                    </div>
                    <div class="text-2xl font-black text-slate-800 dark:text-white transition-colors duration-500">-- <span class="text-xs font-medium text-slate-500">Order</span></div>
                </div>

                <div class="bg-slate-50 dark:bg-slate-900 border border-slate-100 dark:border-slate-700 p-4 rounded-2xl hover:border-blue-200 dark:hover:border-blue-500/50 transition-colors group">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-[10px] font-black text-slate-400 dark:text-slate-400 uppercase tracking-widest">Estimasi Muatan</span>
                        <i class="mdi mdi-weight-kilogram text-blue-500 dark:text-blue-400 text-xl group-hover:scale-110 transition-transform"></i>
                    </div>
                    <div class="text-2xl font-black text-slate-800 dark:text-white transition-colors duration-500">-- <span class="text-xs font-medium text-slate-500">Tonase</span></div>
                </div>
            </div>
        </div>

    </div>

    {{-- TOP PERFORMANCE TABLE --}}
    <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-[2rem] shadow-sm overflow-hidden mb-8 transition-colors duration-500">
        <div class="px-6 py-5 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center bg-white dark:bg-slate-800 transition-colors duration-500">
            <h3 class="text-sm font-black text-slate-800 dark:text-white transition-colors duration-500">Top Performance Toko Bangunan</h3>
            <a href="{{ route('admin.dashboard.top_stores') }}" class="text-xs font-bold text-blue-600 dark:text-white hover:text-blue-800 dark:hover:text-blue-400 transition-colors outline-none bg-blue-50 dark:bg-slate-700 dark:hover:bg-slate-600 px-3 py-1.5 rounded-lg border-0 no-underline">Lihat Semua</a>
        </div>

        <div class="overflow-x-auto table-container">
            <table class="w-full text-left border-collapse whitespace-nowrap">
                <thead>
                    <tr class="bg-slate-100 dark:bg-slate-900 border-b border-slate-200 dark:border-slate-700 transition-colors duration-500">
                        <th class="px-6 py-4 text-[10px] font-black text-slate-700 dark:text-slate-200 uppercase tracking-widest align-middle">Peringkat</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-700 dark:text-slate-200 uppercase tracking-widest align-middle">Informasi Toko</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-700 dark:text-slate-200 uppercase tracking-widest align-middle">GMV (Penjualan)</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-700 dark:text-slate-200 uppercase tracking-widest align-middle">Transaksi</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-700 dark:text-slate-200 uppercase tracking-widest align-middle">Rating</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                    @forelse($topToko ?? [] as $index => $toko)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors duration-300">
                        <td class="px-6 py-4 text-center w-20">
                            @if($index == 0)
                                <i class="mdi mdi-medal text-amber-400 text-3xl dark:drop-shadow-[0_0_10px_rgba(251,191,36,0.5)]"></i>
                            @elseif($index == 1)
                                <i class="mdi mdi-medal text-slate-400 dark:text-slate-300 text-2xl dark:drop-shadow-[0_0_10px_rgba(203,213,225,0.3)]"></i>
                            @elseif($index == 2)
                                <i class="mdi mdi-medal text-orange-500 dark:text-orange-500 text-xl dark:drop-shadow-[0_0_10px_rgba(234,88,12,0.3)]"></i>
                            @else
                                <span class="font-black text-slate-500 dark:text-slate-400">{{ $index + 1 }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="font-black text-slate-800 dark:text-white text-sm mb-1 transition-colors duration-500">{{ $toko->nama_toko }}</div>
                            <div class="text-[10px] font-bold text-slate-500 dark:text-slate-400 flex items-center gap-1">
                                <i class="mdi mdi-map-marker-outline"></i> {{ $toko->nama_kota ?? 'Lokasi Belum Diatur' }}
                            </div>
                        </td>
                        <td class="px-6 py-4 font-black text-emerald-600 dark:text-emerald-400 text-sm transition-colors duration-500 dark:text-glow-emerald">
                            Rp {{ number_format($toko->total_gmv, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4">
                            <span class="font-black text-slate-800 dark:text-white text-sm transition-colors duration-500">{{ number_format($toko->total_order) }}</span>
                            <span class="text-[10px] font-bold text-slate-500 dark:text-slate-400">Pesanan</span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-1.5 bg-yellow-50 dark:bg-amber-500/10 w-fit px-2.5 py-1 rounded-lg border border-yellow-100 dark:border-amber-500/20 transition-colors duration-500">
                                <i class="mdi mdi-star text-yellow-500 dark:text-amber-400 text-sm leading-none"></i>
                                <span class="font-black text-yellow-700 dark:text-white text-xs transition-colors duration-500">4.9</span>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-16 text-center bg-slate-50/50 dark:bg-slate-800/50">
                            <i class="mdi mdi-store-off-outline text-5xl text-slate-300 dark:text-slate-600 mb-3 block transition-colors duration-500"></i>
                            <h6 class="font-black text-slate-600 dark:text-slate-400 mb-1 transition-colors duration-500">Belum ada data penjualan.</h6>
                            <p class="text-xs font-bold text-slate-400 dark:text-slate-500 m-0">Toko dengan penjualan tertinggi akan otomatis muncul di sini.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    const canvas = document.getElementById('mainChart');
    if(!canvas) return;

    const ctx = canvas.getContext('2d');
    let chartInstance = null;

    const labels = {!! json_encode($chart_labels ?? ['Sen','Sel','Rab','Kam','Jum','Sab','Min']) !!};
    const values = {!! json_encode($chart_values ?? [12, 19, 15, 25, 22, 30, 28]) !!};

    function renderChart() {
        const isDark = document.documentElement.classList.contains('dark');

        const gridColor = isDark ? 'rgba(255, 255, 255, 0.05)' : '#f1f5f9';
        const tickColor = isDark ? '#94a3b8' : '#64748b';
        const tooltipBg = isDark ? '#1e293b' : '#ffffff'; // slate-800
        const tooltipText = isDark ? '#ffffff' : '#1e293b';
        const tooltipBorder = isDark ? 'rgba(255, 255, 255, 0.1)' : '#e2e8f0';

        let gradient = ctx.createLinearGradient(0, 0, 0, 300);
        gradient.addColorStop(0, 'rgba(59, 130, 246, 0.4)'); // Blue-500
        gradient.addColorStop(1, 'rgba(59, 130, 246, 0.0)');

        if(chartInstance) {
            chartInstance.destroy();
        }

        chartInstance = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Pengguna Baru',
                    data: values,
                    borderColor: '#3b82f6',
                    backgroundColor: gradient,
                    borderWidth: 3,
                    pointBackgroundColor: isDark ? '#1e293b' : '#ffffff',
                    pointBorderColor: '#3b82f6',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    pointHoverBackgroundColor: '#3b82f6',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: tooltipBg,
                        titleColor: tooltipText,
                        bodyColor: tooltipText,
                        titleFont: { size: 11, family: "'Plus Jakarta Sans', sans-serif", weight: 'bold' },
                        bodyFont: { size: 13, family: "'Plus Jakarta Sans', sans-serif", weight: '900' },
                        padding: 12,
                        displayColors: false,
                        cornerRadius: 12,
                        borderColor: tooltipBorder,
                        borderWidth: 1,
                        boxShadow: isDark ? 'none' : '0 4px 6px -1px rgba(0, 0, 0, 0.1)'
                    }
                },
                scales: {
                    x: {
                        grid: { display: false, drawBorder: false },
                        ticks: { color: tickColor, font: { family: "'Plus Jakarta Sans', sans-serif", weight: 'bold', size: 10 } }
                    },
                    y: {
                        beginAtZero: true,
                        grid: { color: gridColor, drawBorder: false, borderDash: [5, 5] },
                        ticks: {
                            color: tickColor,
                            stepSize: 10,
                            font: { family: "'Plus Jakarta Sans', sans-serif", weight: 'bold', size: 10 },
                            precision: 0
                        }
                    }
                }
            }
        });
    }

    renderChart();

    const observer = new MutationObserver((mutations) => {
        mutations.forEach((mutation) => {
            if (mutation.attributeName === 'class') {
                renderChart();
            }
        });
    });
    observer.observe(document.documentElement, { attributes: true });
});
</script>
@endpush
