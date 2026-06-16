@extends('layouts.seller')

@section('title', 'Dashboard Seller')

@section('content')
<div class="min-h-screen bg-slate-50 p-4 md:p-6 lg:p-8 font-sans text-slate-900 space-y-6">

    {{-- BAN ALERT (DITINGKATKAN) --}}
    @if(Auth::user()->is_banned)
        @if(Auth::user()->ban_type === 'berat')
            <div class="bg-red-900 border-2 border-red-700 rounded-3xl p-6 flex flex-col md:flex-row items-center gap-6 animate-pulse text-white shadow-2xl">
                <div class="w-16 h-16 bg-red-800 rounded-2xl flex items-center justify-center text-red-200 shadow-inner">
                    <i class="mdi mdi-shield-off text-3xl"></i>
                </div>
                <div class="flex-1 text-center md:text-left">
                    <h4 class="text-xl font-black mb-1 text-white">AKUN DITANGGUHKAN PERMANEN!</h4>
                    <p class="text-sm font-bold text-red-200">Seluruh produk dan toko Anda disembunyikan dari publik. Akses Anda terbatas hingga peninjauan selesai.</p>
                </div>
                <a href="{{ route('seller.data.health') }}" class="px-6 py-3 bg-white text-red-900 font-black rounded-xl transition-all shadow-lg no-underline hover:scale-105 active:scale-95">
                    Cek Detail Pelanggaran
                </a>
            </div>
        @else
            <div class="bg-amber-50 border-2 border-amber-200 rounded-3xl p-6 flex flex-col md:flex-row items-center gap-6 shadow-sm border-dashed">
                <div class="w-16 h-16 bg-amber-100 rounded-2xl flex items-center justify-center text-amber-600 shadow-inner">
                    <i class="mdi mdi-alert-decagram text-3xl"></i>
                </div>
                <div class="flex-1 text-center md:text-left">
                    <h4 class="text-xl font-black text-amber-800 mb-1">Peringatan Pembatasan Akun!</h4>
                    <p class="text-sm font-bold text-amber-600/80">Akun Anda dalam status penangguhan RINGAN karena: <b>{{ Auth::user()->ban_reason }}</b>. Anda masih bisa login, namun performa toko akan dibatasi.</p>
                </div>
                <a href="{{ route('seller.data.health') }}" class="px-6 py-3 bg-amber-500 hover:bg-amber-600 text-white font-black rounded-xl transition-all shadow-lg shadow-amber-200 no-underline hover:scale-105 active:scale-95">
                    Ajukan Banding
                </a>
            </div>
        @endif
    @endif

    {{-- 1. HERO SECTION (Clean White Premium) --}}
    <div class="relative bg-white border border-slate-200 rounded-3xl p-6 md:p-8 shadow-sm flex flex-col md:flex-row justify-between items-start md:items-center gap-6 overflow-hidden">
        {{-- Efek Glow Halus di Pojok Kanan Atas --}}
        <div class="absolute -right-20 -top-20 w-72 h-72 bg-blue-50 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10 flex items-center gap-5">
            <div class="w-16 h-16 bg-blue-50 border border-blue-100 rounded-2xl flex items-center justify-center text-blue-600 shadow-inner">
                <i class="mdi mdi-storefront text-3xl"></i>
            </div>
            <div>
                <h3 class="text-2xl md:text-3xl font-black text-slate-900 tracking-tight mb-1">{{ $toko->nama_toko }}</h3>
                <p class="text-sm font-medium text-slate-500">Monitor performa operasional dan analitik penjualan Anda hari ini.</p>
            </div>
        </div>

        <div class="relative z-10">
            <div class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-50 text-emerald-600 border border-emerald-100 rounded-xl font-bold text-sm tracking-widest uppercase">
                <i class="mdi mdi-shield-check text-lg"></i>
                {{ str_replace('_', ' ', strtoupper($toko->tier_toko ?? 'REGULAR')) }}
            </div>
        </div>
    </div>

    {{-- 2. STATUS OPERASIONAL (Bento Action Cards) --}}
    <div>
        <h4 class="text-sm font-black text-slate-400 uppercase tracking-widest mb-4 flex items-center gap-2">
            <i class="mdi mdi-forklift text-lg text-slate-300"></i> Status Operasional
        </h4>
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            {{-- Card 1: Pesanan Masuk --}}
            <a href="{{ route('seller.orders.index', ['status' => 'diproses']) }}" class="group bg-white border border-slate-200 rounded-2xl p-5 transition-all duration-300 hover:-translate-y-1 hover:shadow-lg hover:shadow-blue-500/10 hover:border-blue-300 relative overflow-hidden">
                <div class="absolute top-0 left-0 w-full h-1 bg-blue-500 scale-x-0 group-hover:scale-x-100 origin-left transition-transform duration-300"></div>
                <div class="text-3xl md:text-4xl font-black text-slate-800 mb-2 group-hover:text-blue-600 transition-colors">{{ $perlu_diproses }}</div>
                <div class="text-xs font-bold text-slate-500 uppercase flex items-center gap-2">
                    <div class="p-1 bg-blue-50 text-blue-500 rounded-md"><i class="mdi mdi-package-variant text-base leading-none"></i></div>
                    Pesanan Masuk
                </div>
            </a>

            {{-- Card 2: Siap Kirim --}}
            <a href="{{ route('seller.orders.index', ['status' => 'dikirim']) }}" class="group bg-white border border-slate-200 rounded-2xl p-5 transition-all duration-300 hover:-translate-y-1 hover:shadow-lg hover:shadow-emerald-500/10 hover:border-emerald-300 relative overflow-hidden">
                <div class="absolute top-0 left-0 w-full h-1 bg-emerald-500 scale-x-0 group-hover:scale-x-100 origin-left transition-transform duration-300"></div>
                <div class="text-3xl md:text-4xl font-black text-slate-800 mb-2 group-hover:text-emerald-600 transition-colors">{{ $telah_diproses }}</div>
                <div class="text-xs font-bold text-slate-500 uppercase flex items-center gap-2">
                    <div class="p-1 bg-emerald-50 text-emerald-500 rounded-md"><i class="mdi mdi-truck-fast text-base leading-none"></i></div>
                    Siap Kirim
                </div>
            </a>

            {{-- Card 3: Komplain --}}
            <a href="{{ route('seller.orders.return') }}" class="group bg-white border border-slate-200 rounded-2xl p-5 transition-all duration-300 hover:-translate-y-1 hover:shadow-lg hover:shadow-amber-500/10 hover:border-amber-300 relative overflow-hidden">
                <div class="absolute top-0 left-0 w-full h-1 bg-amber-500 scale-x-0 group-hover:scale-x-100 origin-left transition-transform duration-300"></div>
                <div class="text-3xl md:text-4xl font-black text-slate-800 mb-2 group-hover:text-amber-500 transition-colors">{{ $pengembalian }}</div>
                <div class="text-xs font-bold text-slate-500 uppercase flex items-center gap-2">
                    <div class="p-1 bg-amber-50 text-amber-500 rounded-md"><i class="mdi mdi-alert-octagon text-base leading-none"></i></div>
                    Komplain
                </div>
            </a>

            {{-- Card 4: Batal --}}
            <div class="bg-slate-50 border border-slate-200/60 rounded-2xl p-5 cursor-default opacity-80">
                <div class="text-3xl md:text-4xl font-black text-slate-400 mb-2">{{ $dibatalkan }}</div>
                <div class="text-xs font-bold text-slate-400 uppercase flex items-center gap-2">
                    <div class="p-1 bg-slate-200 text-slate-500 rounded-md"><i class="mdi mdi-cancel text-base leading-none"></i></div>
                    Dibatalkan
                </div>
            </div>
        </div>
    </div>

    {{-- 3. MAIN GRID (Kiri 2, Kanan 1) --}}
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

        {{-- BAGIAN KIRI: ANALITIK & CHART --}}
        <div class="xl:col-span-2 space-y-6">
            <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-sm">

                {{-- Header Chart --}}
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
                    <h4 class="text-lg font-black text-slate-900 flex items-center gap-2">
                        <div class="p-1.5 bg-emerald-50 text-emerald-500 rounded-lg"><i class="mdi mdi-finance text-lg leading-none"></i></div>
                        Analitik Pendapatan
                    </h4>
                    <select id="chartFilter" class="bg-slate-50 border border-slate-200 text-slate-700 text-sm font-bold rounded-xl px-4 py-2 hover:bg-slate-100 focus:ring-2 focus:ring-blue-500 focus:outline-none cursor-pointer transition-colors">
                        <option value="tahun" selected>Tahun {{ Carbon\Carbon::now()->year }}</option>
                        <option value="bulan">Bulan Ini</option>
                    </select>
                </div>

                {{-- Metric Bento Boxes --}}
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
                    <div class="bg-slate-50 rounded-2xl p-4 border border-slate-100 hover:border-blue-100 transition-colors">
                        <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Total Omzet</div>
                        <div class="text-lg md:text-xl font-black text-blue-600 truncate">Rp {{ number_format($total_penjualan ?? 0, 0, ',', '.') }}</div>
                    </div>
                    <div class="bg-slate-50 rounded-2xl p-4 border border-slate-100 hover:border-slate-200 transition-colors">
                        <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Transaksi Berhasil</div>
                        <div class="text-lg md:text-xl font-black text-slate-800">{{ number_format($total_pesanan ?? 0) }} <span class="text-xs text-slate-400 font-medium">Inv</span></div>
                    </div>
                    <div class="bg-slate-50 rounded-2xl p-4 border border-slate-100 hover:border-slate-200 transition-colors">
                        <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">SKU Aktif</div>
                        <div class="text-lg md:text-xl font-black text-slate-800">{{ number_format($total_produk_aktif ?? 0) }}</div>
                    </div>
                    <div class="bg-slate-50 rounded-2xl p-4 border border-slate-100 hover:border-emerald-100 transition-colors">
                        <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Konversi</div>
                        <div class="text-lg md:text-xl font-black text-emerald-500">{{ $konversi }}%</div>
                    </div>
                </div>

                {{-- Canvas Container --}}
                <div class="w-full h-[300px] relative">
                    <canvas id="penjualanChart"></canvas>
                </div>
            </div>
        </div>

        {{-- BAGIAN KANAN: TOP PRODUK & INFO --}}
        <div class="space-y-6">

            {{-- Material Terlaris --}}
            <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-sm flex flex-col h-full">
                <h4 class="text-sm font-black text-slate-400 uppercase tracking-widest mb-5 flex items-center gap-2 border-b border-slate-100 pb-4">
                    <i class="mdi mdi-podium-gold text-amber-500 text-lg"></i> Material Terlaris
                </h4>

                <div class="flex-1">
                    @if(count($top_produk_keys) > 0)
                        <div class="space-y-4">
                            @foreach($top_produk_keys as $index => $nama)
                                <div class="flex justify-between items-center group">
                                    <div class="flex items-center gap-3 overflow-hidden">
                                        {{-- Rank Badge --}}
                                        <div class="w-8 h-8 rounded-lg bg-slate-50 border border-slate-100 flex items-center justify-center text-slate-500 text-xs font-black group-hover:bg-blue-50 group-hover:text-blue-600 transition-colors flex-shrink-0">
                                            {{ $index + 1 }}
                                        </div>
                                        <span class="text-sm font-bold text-slate-700 truncate group-hover:text-blue-600 transition-colors">{{ $nama }}</span>
                                    </div>
                                    <div class="text-xs font-black bg-blue-50 text-blue-600 px-3 py-1 rounded-lg whitespace-nowrap ml-2">
                                        {{ $top_produk_values[$index] }} Terjual
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="h-full flex flex-col items-center justify-center py-10 opacity-60">
                            <i class="mdi mdi-package-variant-closed text-5xl text-slate-300 mb-3 block"></i>
                            <span class="text-sm font-bold text-slate-500">Belum ada penjualan</span>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Info B2B Premium Box --}}
            <div class="bg-blue-50 border border-blue-200 rounded-3xl overflow-hidden relative shadow-sm">
                <div class="absolute top-0 left-0 w-full h-1 bg-blue-500"></div>
                <div class="p-6">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center flex-shrink-0 shadow-inner">
                            <i class="mdi mdi-truck-flatbed text-2xl"></i>
                        </div>
                        <div>
                            <h5 class="text-sm font-black text-slate-900 mb-1">Armada Sendiri (B2B)</h5>
                            <p class="text-xs font-medium text-slate-600 leading-relaxed">
                                Aktifkan "Armada Toko" di Pengaturan Pengiriman untuk memudahkan pembeli memesan material curah/berat.
                            </p>
                            <a href="{{ route('seller.pengaturan.pengiriman') }}" class="inline-flex items-center gap-1 mt-3 text-xs font-bold text-blue-600 hover:text-blue-800 transition-colors bg-white px-3 py-1.5 rounded-lg border border-blue-100 shadow-sm hover:shadow">
                                Atur Sekarang <i class="mdi mdi-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    // 1. Setup Data dari Backend
    // Ganti bagian dataChart di Blade Anda dengan ini:
const dataChart = {
    bulan: {
        labels: ['Minggu 1', 'Minggu 2', 'Minggu 3', 'Minggu 4'],
        data: {!! json_encode($penjualan_mingguan) !!} // Sekarang Riil!
    },
    tahun: {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'],
        data: {!! json_encode($penjualan_tahunan) !!} // Riil!
    }
};

    const ctx = document.getElementById('penjualanChart').getContext('2d');

    // 2. Logic Gradient (Sangat Premium di Light Mode)
    let gradient = ctx.createLinearGradient(0, 0, 0, 300);
    gradient.addColorStop(0, 'rgba(59, 130, 246, 0.25)'); // Light Blue Transparan
    gradient.addColorStop(1, 'rgba(255, 255, 255, 0.0)'); // Menghilang ke putih

    // 3. Konfigurasi Chart Tingkat Dewa
    let myChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: dataChart.tahun.labels,
            datasets: [{
                label: 'Omzet',
                data: dataChart.tahun.data,
                backgroundColor: gradient,
                borderColor: '#3b82f6', // Biru Solid
                borderWidth: 3,
                tension: 0.4, // Kurva membulat mulus
                fill: true,
                pointBackgroundColor: '#ffffff',
                pointBorderColor: '#3b82f6',
                pointBorderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 7,
                pointHoverBackgroundColor: '#3b82f6',
                pointHoverBorderColor: '#ffffff',
                pointHoverBorderWidth: 2,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                intersect: false,
                mode: 'index',
            },
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#0f172a', // Tooltip gelap agar kontras
                    titleColor: '#94a3b8',
                    bodyColor: '#ffffff',
                    padding: 12,
                    cornerRadius: 8,
                    displayColors: false,
                    callbacks: {
                        label: function(context) {
                            return 'Rp ' + context.parsed.y.toLocaleString('id-ID');
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: '#f1f5f9', // Garis grid sangat tipis
                        drawBorder: false,
                        borderDash: [5, 5]
                    },
                    ticks: {
                        color: '#64748b',
                        font: { size: 11, family: 'Inter, sans-serif', weight: '600' },
                        padding: 10,
                        // Logic god-tier untuk format Y-Axis (K, Jt, M)
                        callback: function(value) {
                            if(value >= 1000000000) return 'Rp ' + (value/1000000000).toFixed(1) + 'M';
                            if(value >= 1000000) return 'Rp ' + (value/1000000).toFixed(1) + 'Jt';
                            if(value >= 1000) return 'Rp ' + (value/1000) + 'k';
                            return 'Rp ' + value;
                        }
                    }
                },
                x: {
                    grid: { display: false, drawBorder: false },
                    ticks: {
                        color: '#64748b',
                        font: { size: 11, family: 'Inter, sans-serif', weight: '700' },
                        padding: 10
                    }
                }
            },
            animation: {
                duration: 1000,
                easing: 'easeOutQuart'
            }
        }
    });

    // 4. Update Data Instan Mulus Tanpa Reload
    document.getElementById('chartFilter').addEventListener('change', function(e) {
        const period = e.target.value;
        myChart.data.labels = dataChart[period].labels;
        myChart.data.datasets[0].data = dataChart[period].data;
        myChart.update();
    });
});
</script>
@endpush
