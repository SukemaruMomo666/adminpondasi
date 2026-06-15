@extends('layouts.seller')

@section('title', 'Performa Toko')

@push('styles')
<style>
    /* Mengamankan ukuran canvas Donut agar tidak melar di Flexbox Tailwind */
    .chart-donut-wrapper { width: 180px; height: 180px; flex-shrink: 0; }

    /* Animasi Tab Transisi */
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    .animate-fade-in { animation: fadeIn 0.4s ease-out forwards; }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-slate-50 p-4 md:p-6 lg:p-8 font-sans text-slate-900 space-y-6">

    {{-- HEADER PAGE --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-white border border-slate-200 rounded-2xl flex items-center justify-center text-blue-600 shadow-sm flex-shrink-0">
                <i class="mdi mdi-chart-box-outline text-2xl"></i>
            </div>
            <div>
                <h1 class="text-2xl font-black text-slate-900 tracking-tight">Performa Toko</h1>
                <div class="text-sm font-medium text-slate-500 mt-0.5 flex items-center gap-2">
                    <a href="{{ route('seller.dashboard') }}" class="hover:text-blue-600 transition-colors">Dashboard</a>
                    <i class="mdi mdi-chevron-right text-xs"></i>
                    <span class="text-slate-700 font-bold">Analitik Data</span>
                </div>
            </div>
        </div>

        {{-- KOTAK FILTER & AKSI --}}
        <div class="w-full md:w-auto bg-white border border-slate-200 rounded-2xl p-2 shadow-sm flex flex-wrap lg:flex-nowrap items-center gap-3">

            <div class="flex items-center gap-2 px-3 border-r border-slate-100 flex-1 md:flex-none">
                <i class="mdi mdi-calendar-month text-slate-400 text-lg"></i>
                <input type="date" class="w-full md:w-auto bg-transparent text-sm font-bold text-slate-700 focus:outline-none cursor-pointer" value="{{ date('Y-m-d') }}">
            </div>

            <div class="flex-1 md:flex-none px-3 border-r border-slate-100">
                <select class="w-full md:w-auto bg-transparent text-sm font-bold text-slate-700 focus:outline-none cursor-pointer appearance-none">
                    <option>Semua Pesanan</option>
                    <option>Selesai Saja</option>
                </select>
            </div>

            <div class="flex items-center gap-2 px-1 w-full md:w-auto justify-end">
                <button class="flex items-center gap-1.5 px-4 py-2 bg-blue-50 text-blue-700 hover:bg-blue-100 hover:text-blue-800 rounded-xl text-xs font-black transition-colors">
                    <i class="mdi mdi-flash text-base"></i> Real-Time
                </button>
                <a href="{{ route('seller.data.performance.export') }}" target="_blank" class="flex items-center gap-1.5 px-4 py-2 bg-slate-900 hover:bg-black text-white rounded-xl text-xs font-black transition-colors shadow-sm shadow-slate-900/20">
                    <i class="mdi mdi-download text-base"></i> Laporan
                </a>
            </div>

        </div>
    </div>

    {{-- NAVIGASI TAB UTAMA --}}
    <div class="flex gap-6 border-b border-slate-200 overflow-x-auto hide-scrollbar">
        <button onclick="appLogic.switchTab('tinjauan')" id="tab-tinjauan" class="pb-3 text-sm font-black border-b-2 border-blue-600 text-blue-600 transition-colors whitespace-nowrap">Ringkasan Utama</button>
        <button onclick="appLogic.switchTab('produk')" id="tab-produk" class="pb-3 text-sm font-black border-b-2 border-transparent text-slate-500 hover:text-slate-800 hover:border-slate-300 transition-colors whitespace-nowrap">Analitik Produk</button>
    </div>

    {{-- KONTEN TAB --}}
    <div id="tab-content" class="relative">

        {{-- ============================================================== --}}
        {{-- TAB 1: TINJAUAN UTAMA                                          --}}
        {{-- ============================================================== --}}
        <div id="tinjauan-content" class="block space-y-6 animate-fade-in">

            {{-- KPI CARDS (KEY PERFORMANCE INDICATORS) --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6">
                @php
                    $kpis = [
                        ['title' => 'Total Penjualan', 'val' => 'Rp ' . number_format($kriteria['penjualan']['nilai'], 0, ',', '.'), 'vs' => $kriteria['penjualan']['perbandingan'], 'icon' => 'mdi-cash-multiple', 'color' => 'blue'],
                        ['title' => 'Jumlah Pesanan', 'val' => number_format($kriteria['pesanan']['nilai'], 0, ',', '.'), 'vs' => $kriteria['pesanan']['perbandingan'], 'icon' => 'mdi-receipt', 'color' => 'indigo'],
                        ['title' => 'Tingkat Konversi', 'val' => $kriteria['tingkat_konversi']['nilai'] . '%', 'vs' => $kriteria['tingkat_konversi']['perbandingan'], 'icon' => 'mdi-percent-circle-outline', 'color' => 'emerald'],
                        ['title' => 'Total Pengunjung', 'val' => number_format($kriteria['pengunjung']['nilai'], 0, ',', '.'), 'vs' => $kriteria['pengunjung']['perbandingan'], 'icon' => 'mdi-account-group-outline', 'color' => 'amber']
                    ];
                @endphp

                @foreach($kpis as $kpi)
                    <div class="bg-white p-5 md:p-6 rounded-3xl border border-slate-200 shadow-sm hover:shadow-md hover:border-slate-300 transition-all duration-300 group">
                        <div class="flex justify-between items-start mb-4">
                            <div class="w-12 h-12 rounded-2xl bg-{{ $kpi['color'] }}-50 text-{{ $kpi['color'] }}-600 flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                                <i class="mdi {{ $kpi['icon'] }} text-2xl"></i>
                            </div>
                            @php $isPositive = $kpi['vs'] >= 0; @endphp
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[10px] font-black uppercase tracking-widest {{ $isPositive ? 'bg-emerald-50 text-emerald-600 border border-emerald-200' : 'bg-red-50 text-red-600 border border-red-200' }}">
                                <i class="mdi {{ $isPositive ? 'mdi-trending-up' : 'mdi-trending-down' }} text-xs"></i> {{ abs($kpi['vs']) }}%
                            </span>
                        </div>
                        <h3 class="text-[11px] font-black text-slate-400 uppercase tracking-widest mb-1">{{ $kpi['title'] }}</h3>
                        <div class="text-2xl md:text-3xl font-black text-slate-900 tracking-tight">{{ $kpi['val'] }}</div>
                    </div>
                @endforeach
            </div>

            {{-- CHART TREN PERFORMA UTAMA --}}
            <div class="bg-white p-6 md:p-8 rounded-3xl border border-slate-200 shadow-sm">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-6">
                    <h2 class="text-lg font-black text-slate-900 flex items-center gap-2">
                        <i class="mdi mdi-chart-line text-blue-600"></i> Grafik Tren Performa
                    </h2>

                    {{-- Checkboxes untuk memunculkan dataset --}}
                    <div class="flex flex-wrap items-center gap-4 bg-slate-50 p-2 rounded-xl border border-slate-100" id="chartToggles">
                        @foreach(['penjualan' => 'Penjualan', 'pesanan' => 'Pesanan', 'pengunjung' => 'Pengunjung'] as $val => $label)
                            <label class="flex items-center gap-2 px-3 py-1 cursor-pointer group">
                                <input type="checkbox" value="{{ $val }}" class="w-4 h-4 text-blue-600 rounded border-slate-300 focus:ring-blue-500 cursor-pointer" {{ $val != 'pesanan' ? 'checked' : '' }} onchange="appLogic.updateMainChart()">
                                <span class="text-sm font-bold text-slate-600 group-hover:text-slate-900 transition-colors">{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                {{-- Container Canvas Chart --}}
                <div class="w-full h-[350px] relative">
                    <canvas id="mainPerformanceChart"></canvas>
                </div>
            </div>

            {{-- GRID BAWAH: SALURAN & STATISTIK PEMBELI --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                {{-- Kolom Kiri: Sumber Pendapatan --}}
                <div class="bg-white p-6 md:p-8 rounded-3xl border border-slate-200 shadow-sm flex flex-col h-full">
                    <h2 class="text-base font-black text-slate-900 mb-6 pb-4 border-b border-slate-100 flex items-center gap-2">
                        <i class="mdi mdi-source-branch text-indigo-500"></i> Sumber Pendapatan
                    </h2>

                    <div class="flex-1 space-y-2">
                        @php
                            $channels = [
                                ['label' => 'Halaman Produk', 'icon' => 'mdi-cube-outline text-blue-500', 'bg' => 'bg-blue-50', 'val' => $saluran['halaman_produk']['nilai'], 'vs' => $saluran['halaman_produk']['perbandingan']],
                                ['label' => 'Live Streaming', 'icon' => 'mdi-video-wireless-outline text-red-500', 'bg' => 'bg-red-50', 'val' => $saluran['live']['nilai'], 'vs' => $saluran['live']['perbandingan']],
                                ['label' => 'Video Promosi', 'icon' => 'mdi-play-circle-outline text-amber-500', 'bg' => 'bg-amber-50', 'val' => $saluran['video']['nilai'], 'vs' => $saluran['video']['perbandingan']],
                            ]
                        @endphp

                        @foreach($channels as $ch)
                            <div class="flex items-center justify-between p-4 hover:bg-slate-50 rounded-2xl border border-transparent hover:border-slate-100 transition-all duration-300">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 rounded-xl {{ $ch['bg'] }} flex items-center justify-center flex-shrink-0">
                                        <i class="mdi {{ $ch['icon'] }} text-2xl"></i>
                                    </div>
                                    <span class="font-bold text-slate-800">{{ $ch['label'] }}</span>
                                </div>
                                <div class="text-right">
                                    <div class="text-base font-black text-slate-900 mb-1">Rp {{ number_format($ch['val'], 0, ',', '.') }}</div>
                                    @php $isUp = $ch['vs'] >= 0; @endphp
                                    <div class="text-[10px] font-black uppercase tracking-widest {{ $isUp ? 'text-emerald-500' : 'text-red-500' }}">
                                        {{ $isUp ? '+' : '' }}{{ $ch['vs'] }}% <span class="text-slate-400 font-medium">vs Kemarin</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Kolom Kanan: Retensi & Akuisisi Pembeli --}}
                <div class="bg-white p-6 md:p-8 rounded-3xl border border-slate-200 shadow-sm flex flex-col h-full">
                    <h2 class="text-base font-black text-slate-900 mb-6 pb-4 border-b border-slate-100 flex items-center gap-2">
                        <i class="mdi mdi-account-group text-emerald-500"></i> Retensi & Akuisisi Pembeli
                    </h2>

                    <div class="flex-1 flex flex-col xl:flex-row items-center justify-center gap-8">
                        {{-- Donut Chart --}}
                        <div class="relative chart-donut-wrapper">
                            <canvas id="buyerDonutChart"></canvas>
                            <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                                <span class="text-3xl font-black text-slate-900 tracking-tighter">{{ $pembeli['pembeli_saat_ini_persen'] }}%</span>
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">Pembeli Setia</span>
                            </div>
                        </div>

                        {{-- Metric Grid Kotak-Kotak --}}
                        <div class="grid grid-cols-2 gap-3 w-full">
                            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100">
                                <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Total Pembeli</div>
                                <div class="text-xl font-black text-slate-900">{{ number_format($pembeli['total_pembeli']) }}</div>
                            </div>
                            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100">
                                <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Pembeli Baru</div>
                                <div class="text-xl font-black text-slate-900">{{ number_format($pembeli['pembeli_baru']) }}</div>
                            </div>
                            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100">
                                <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Potensi Kembali</div>
                                <div class="text-xl font-black text-slate-900">{{ number_format($pembeli['potensi_pembeli']) }}</div>
                            </div>
                            <div class="bg-blue-50 p-4 rounded-2xl border border-blue-100">
                                <div class="text-[10px] font-black text-blue-500 uppercase tracking-widest mb-1">Retention Rate</div>
                                <div class="text-xl font-black text-blue-700">{{ $pembeli['tingkat_pembeli_berulang'] }}%</div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        {{-- ============================================================== --}}
        {{-- TAB 2: ANALITIK PRODUK (EMPTY STATE)                           --}}
        {{-- ============================================================== --}}
        <div id="produk-content" class="hidden animate-fade-in">
            <div class="bg-white border border-slate-200 rounded-3xl shadow-sm py-24 px-6 text-center flex flex-col items-center justify-center">
                <div class="w-24 h-24 bg-slate-50 border border-slate-100 rounded-full flex items-center justify-center mb-6 shadow-inner">
                    <i class="mdi mdi-cube-scan text-5xl text-slate-300"></i>
                </div>
                <h2 class="text-2xl font-black text-slate-900 mb-2">Data Analitik Produk Kosong</h2>
                <p class="text-sm font-medium text-slate-500 max-w-md mx-auto">Kumpulkan lebih banyak metrik transaksi untuk mengaktifkan Heat-Map dan analisis performa per-SKU.</p>
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const appLogic = {
    charts: {},
    data: {
        labels: @json($chart_labels),
        metrics: {
            penjualan: @json($chart_data['penjualan']),
            pesanan: @json($chart_data['pesanan']),
            pengunjung: @json($chart_data['pengunjung'])
        },
        configs: {
            penjualan: { label: 'Penjualan (Rp)', borderColor: '#2563eb', backgroundColor: 'rgba(37, 99, 235, 0.1)' },
            pesanan: { label: 'Pesanan', borderColor: '#6366f1', backgroundColor: 'rgba(99, 102, 241, 0.1)' },
            pengunjung: { label: 'Pengunjung', borderColor: '#1e293b', backgroundColor: 'rgba(30, 41, 59, 0.1)' }
        }
    },

    init() {
        this.initMainChart();
        this.initDonutChart();
    },

    switchTab(tab) {
        ['tinjauan', 'produk'].forEach(t => {
            const content = document.getElementById(`${t}-content`);
            const btn = document.getElementById(`tab-${t}`);

            if(t === tab) {
                content.classList.remove('hidden');
                content.classList.add('block');
                btn.className = 'pb-3 text-sm font-black border-b-2 border-blue-600 text-blue-600 transition-colors whitespace-nowrap';
            } else {
                content.classList.add('hidden');
                content.classList.remove('block');
                btn.className = 'pb-3 text-sm font-black border-b-2 border-transparent text-slate-500 hover:text-slate-800 hover:border-slate-300 transition-colors whitespace-nowrap';
            }
        });
    },

    initMainChart() {
        const ctx = document.getElementById('mainPerformanceChart').getContext('2d');
        this.charts.main = new Chart(ctx, {
            type: 'line',
            data: { labels: this.data.labels, datasets: [] },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: {
                        position: 'top', align: 'end',
                        labels: { usePointStyle: true, boxWidth: 8, font: { family: 'Inter, sans-serif', weight: '700', size: 12 }, color: '#64748b' }
                    },
                    tooltip: {
                        backgroundColor: '#0f172a', padding: 12, cornerRadius: 12,
                        titleFont: { family: 'Inter', size: 12, weight: '700' }, titleColor: '#94a3b8',
                        bodyFont: { family: 'Inter', size: 14, weight: '800' }, bodyColor: '#ffffff',
                        displayColors: false,
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if(label.includes('Rp')) {
                                    return label.replace('(Rp)', '') + ': Rp ' + context.parsed.y.toLocaleString('id-ID');
                                }
                                return label + ': ' + context.parsed.y.toLocaleString('id-ID');
                            }
                        }
                    }
                },
                scales: {
                    x: { grid: { display: false, drawBorder: false }, ticks: { font: {family: 'Inter', weight: '700', size: 11}, color: '#94a3b8', padding: 10 } },
                    y: {
                        border: { display: false }, grid: { color: '#f1f5f9', drawBorder: false, borderDash: [5,5] }, beginAtZero: true,
                        ticks: {
                            font: {family: 'Inter', weight: '700', size: 11}, color: '#94a3b8', padding: 10,
                            callback: function(value) {
                                if(value >= 1000000) return 'Rp' + (value/1000000) + 'Jt';
                                if(value >= 1000) return value/1000 + 'k';
                                return value;
                            }
                        }
                    }
                },
                elements: {
                    line: { tension: 0.4, borderWidth: 3 },
                    point: { radius: 0, hoverRadius: 6, backgroundColor: '#fff', borderWidth: 2 }
                }
            }
        });
        this.updateMainChart();
    },

    updateMainChart() {
        const active = Array.from(document.querySelectorAll('#chartToggles input:checked')).map(cb => cb.value);
        this.charts.main.data.datasets = active.map(key => ({
            ...this.data.configs[key],
            data: this.data.metrics[key],
            fill: true,
            pointBorderColor: this.data.configs[key].borderColor,
            pointHoverBackgroundColor: this.data.configs[key].borderColor,
            pointHoverBorderColor: '#ffffff'
        }));
        this.charts.main.update();
    },

    initDonutChart() {
        const ctx = document.getElementById('buyerDonutChart').getContext('2d');
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Pembeli Baru', 'Pembeli Setia'],
                datasets: [{
                    data: [{{ $pembeli_donut_chart['baru'] }}, {{ $pembeli_donut_chart['berulang'] }}],
                    backgroundColor: ['#e2e8f0', '#2563eb'],
                    borderWidth: 0, cutout: '75%', borderRadius: 4, hoverOffset: 4
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#0f172a', padding: 12, cornerRadius: 8,
                        bodyFont: { family: 'Inter', size: 13, weight: '800' },
                        displayColors: true, usePointStyle: true, boxWidth: 8
                    }
                }
            }
        });
    }
};

document.addEventListener("DOMContentLoaded", () => appLogic.init());
</script>
@endpush
