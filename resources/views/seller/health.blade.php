@extends('layouts.seller')

@section('title', 'Kesehatan Toko')

@section('content')
<div class="min-h-screen bg-slate-50 p-4 md:p-6 lg:p-8 font-sans text-slate-900 space-y-6">

    {{-- 1. HEADER --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 mb-8">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-white border border-slate-200 rounded-2xl flex items-center justify-center text-emerald-500 shadow-sm flex-shrink-0">
                <i class="mdi mdi-shield-check-outline text-2xl"></i>
            </div>
            <div>
                <h1 class="text-2xl font-black text-slate-900 tracking-tight">Kesehatan Toko</h1>
                <div class="text-sm font-medium text-slate-500 mt-0.5 flex items-center gap-2">
                    <a href="{{ route('seller.dashboard') }}" class="hover:text-emerald-600 transition-colors">Dashboard</a>
                    <i class="mdi mdi-chevron-right text-xs"></i>
                    <span class="text-slate-700 font-bold">Analisis Kesehatan</span>
                </div>
            </div>
        </div>

        {{-- FILTER BAR --}}
        <div class="w-full md:w-auto bg-white border border-slate-200 rounded-2xl p-2 shadow-sm flex flex-wrap lg:flex-nowrap items-center gap-3">
            <div class="flex items-center gap-2 px-3 border-r border-slate-100 flex-1 md:flex-none">
                <i class="mdi mdi-filter-variant text-slate-400 text-lg"></i>
                <select class="w-full md:w-auto bg-transparent text-sm font-bold text-slate-700 focus:outline-none cursor-pointer appearance-none">
                    <option>7 Hari Terakhir</option>
                    <option>30 Hari Terakhir</option>
                    <option>Kuartal Ini (Q3)</option>
                </select>
            </div>

            <div class="flex items-center justify-end px-1 w-full md:w-auto">
                <a href="{{ route('seller.data.health.export') }}" target="_blank" class="flex items-center justify-center gap-1.5 px-5 py-2 bg-slate-900 hover:bg-black text-white rounded-xl text-xs font-black transition-colors shadow-sm shadow-slate-900/20 w-full md:w-auto">
                    <i class="mdi mdi-download text-base"></i> Unduh Laporan
                </a>
            </div>
        </div>
    </div>

    {{-- MAIN LAYOUT (KIRI 2, KANAN 1) --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

        {{-- ================================================= --}}
        {{-- SISI KIRI (BANNER & LIST METRIK)                    --}}
        {{-- ================================================= --}}
        <div class="lg:col-span-8 space-y-6">

            {{-- BANNER STATUS & CHART RADAR --}}
            <div class="bg-white border border-slate-200 rounded-3xl p-6 md:p-8 shadow-sm flex flex-col md:flex-row items-center gap-8 relative overflow-hidden">
                {{-- Efek Glow --}}
                <div class="absolute -left-20 -top-20 w-64 h-64 bg-emerald-50 rounded-full blur-3xl pointer-events-none"></div>

                <div class="flex-1 min-w-0 relative z-10">
                    <div class="inline-flex items-center gap-2 px-5 py-2 {{ Auth::user()->is_banned ? (Auth::user()->ban_type == 'berat' ? 'bg-red-600' : 'bg-amber-500') : 'bg-slate-900' }} text-white rounded-full text-sm font-black uppercase tracking-widest mb-4 shadow-sm shadow-slate-900/20">
                        <i class="mdi {{ Auth::user()->is_banned ? 'mdi-alert-octagon-outline' : 'mdi-thumb-up-outline' }} text-lg leading-none"></i> {{ Auth::user()->is_banned ? 'Status: ' . strtoupper(Auth::user()->ban_type) . ' BAN' : $status_kesehatan }}
                    </div>
                    @if(Auth::user()->is_banned)
                        <div class="bg-red-50 border border-red-100 rounded-2xl p-4 mb-4">
                            <h6 class="text-xs font-black text-red-800 uppercase mb-1">Alasan Penangguhan:</h6>
                            <p class="text-xs font-bold text-red-600 m-0">{{ Auth::user()->ban_reason }}</p>
                            @if(Auth::user()->banned_until)
                                <p class="text-[10px] font-black text-red-500 mt-2 uppercase tracking-widest">Berlaku Hingga: {{ Auth::user()->banned_until->format('d M Y H:i') }}</p>
                            @endif
                        </div>
                    @endif
                    <p class="text-sm font-medium text-slate-500 leading-relaxed mb-6 max-w-lg">
                        {{ Auth::user()->is_banned ? 'Akun Anda sedang dalam pembatasan. Harap ajukan banding dengan melampirkan bukti perbaikan atau penjelasan yang valid.' : 'Toko Anda berada pada kondisi prima. Terus pertahankan kecepatan pengiriman dan kualitas produk untuk menarik lebih banyak pembeli!' }}
                    </p>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div class="bg-slate-50 border border-slate-100 p-4 rounded-2xl hover:border-slate-300 transition-colors">
                            <span class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5">Pesanan</span>
                            <div class="text-xl font-black {{ $top_summary['pesanan_terselesaikan'] > 0 ? 'text-red-500' : 'text-emerald-500' }}">
                                {{ $top_summary['pesanan_terselesaikan'] }} <span class="text-xs font-bold text-slate-500">Isu</span>
                            </div>
                        </div>
                        <div class="bg-slate-50 border border-slate-100 p-4 rounded-2xl hover:border-slate-300 transition-colors">
                            <span class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5">Produk</span>
                            <div class="text-xl font-black {{ $top_summary['produk_dilarang'] > 0 ? 'text-red-500' : 'text-emerald-500' }}">
                                {{ $top_summary['produk_dilarang'] }} <span class="text-xs font-bold text-slate-500">Isu</span>
                            </div>
                        </div>
                        <div class="bg-slate-50 border border-slate-100 p-4 rounded-2xl hover:border-slate-300 transition-colors">
                            <span class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5">Pelayanan</span>
                            <div class="text-xl font-black {{ $top_summary['pelayanan_pembeli'] > 0 ? 'text-red-500' : 'text-emerald-500' }}">
                                {{ $top_summary['pelayanan_pembeli'] }} <span class="text-xs font-bold text-slate-500">Isu</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Chart Radar Container --}}
                <div class="w-[280px] h-[280px] flex-shrink-0 relative z-10 hidden sm:block">
                    <canvas id="healthRadarChart"></canvas>
                </div>
            </div>

            {{-- LIST METRIK DETAIL --}}
            <div class="space-y-6">
                @foreach ($metrics as $category => $items)
                    <div class="bg-white border border-slate-200 rounded-3xl overflow-hidden shadow-sm">

                        {{-- Header Kategori --}}
                        <div class="bg-slate-50/80 px-6 py-4 border-b border-slate-100 flex items-center gap-2">
                            <i class="mdi mdi-format-list-checks text-slate-400 text-lg leading-none"></i>
                            <h3 class="text-sm font-black text-slate-800 uppercase tracking-widest">{{ $category }}</h3>
                        </div>

                        {{-- Item List --}}
                        <div class="divide-y divide-slate-100">
                            @foreach ($items as $item)
                                <div class="p-6 flex flex-col md:flex-row md:items-center justify-between gap-6 hover:bg-slate-50/50 transition-colors">

                                    {{-- Kiri: Info & Target --}}
                                    <div class="flex-1">
                                        <h6 class="text-sm font-bold text-slate-900 mb-2 leading-snug">{{ $item['nama'] }}</h6>
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-white border border-slate-200 rounded-lg text-[10px] font-black text-slate-500 uppercase tracking-widest">
                                            <i class="mdi mdi-flag-triangle text-amber-500"></i> Target: <span class="text-slate-800">{{ $item['target'] }}</span>
                                        </span>
                                    </div>

                                    {{-- Tengah: Nilai Pencapaian --}}
                                    <div class="w-full md:w-48 md:text-center md:border-x border-slate-100 md:px-6">
                                        <div class="text-2xl font-black text-slate-900 mb-1 tracking-tight">{{ $item['sekarang'] }}</div>
                                        <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Bulan Lalu: {{ $item['sebelumnya'] }}</div>
                                    </div>

                                    {{-- Kanan: Aksi --}}
                                    <div class="w-full md:w-auto md:text-right">
                                        <a href="#" class="inline-flex items-center justify-center gap-1.5 px-5 py-2.5 bg-white border border-slate-300 text-slate-700 hover:bg-slate-900 hover:text-white hover:border-slate-900 text-xs font-bold rounded-xl transition-all shadow-sm w-full md:w-auto">
                                            Rincian <i class="mdi mdi-arrow-right text-base leading-none"></i>
                                        </a>
                                    </div>

                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>

        </div>

        {{-- ================================================= --}}
        {{-- SISI KANAN (WIDGETS)                              --}}
        {{-- ================================================= --}}
        <div class="lg:col-span-4 space-y-6">

            {{-- WIDGET STATUS PENJUAL --}}
            <div class="bg-gradient-to-br from-slate-900 to-slate-800 border border-slate-800 rounded-3xl p-8 text-center shadow-xl shadow-slate-900/10 relative overflow-hidden">
                <i class="mdi mdi-shield-star-outline absolute -right-6 -bottom-6 text-9xl text-white/5 pointer-events-none transform -rotate-12"></i>

                <div class="relative z-10">
                    <div class="w-20 h-20 bg-white/10 border border-white/20 rounded-full flex items-center justify-center text-white mx-auto mb-5 shadow-inner">
                        <i class="mdi mdi-medal-outline text-4xl"></i>
                    </div>
                    <span class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5">Status Saat Ini</span>
                    <h4 class="text-xl font-black text-white mb-3">Penjual Reguler</h4>
                    <p class="text-xs font-medium text-slate-400 leading-relaxed mb-6">Penuhi 3 kriteria tambahan performa toko untuk meningkatkan status menjadi <b>Penjual Star</b>.</p>

                    <a href="#" class="flex items-center justify-center gap-2 w-full px-5 py-3 bg-white text-slate-900 hover:bg-slate-100 text-sm font-bold rounded-xl transition-colors shadow-sm">
                        Lihat Kriteria <i class="mdi mdi-arrow-right"></i>
                    </a>
                </div>
            </div>

            {{-- WIDGET PENALTI SAYA --}}
            <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-sm">
                <div class="flex justify-between items-center border-b border-slate-100 pb-4 mb-5">
                    <h5 class="text-sm font-black text-slate-900 flex items-center gap-2">
                        <i class="mdi mdi-gavel text-red-500 text-lg leading-none"></i> Penalti Saya
                    </h5>
                    <a href="#" class="text-xs font-bold text-blue-600 hover:text-blue-800 transition-colors">Riwayat <i class="mdi mdi-chevron-right"></i></a>
                </div>

                <div class="bg-slate-50 border border-slate-100 rounded-2xl p-4 mb-5 text-center">
                    <span class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Poin Kuartal Ini</span>
                    <div class="text-3xl font-black text-slate-900 tracking-tight">
                        {{ $poin_penalti_kuartal_ini }} <span class="text-sm font-bold text-slate-500">Poin</span>
                    </div>
                </div>

                <div class="space-y-4 mb-6">
                    @foreach($pelanggaran_penalti as $pelanggaran => $poin)
                        <div class="flex justify-between items-center text-xs font-bold">
                            <span class="text-slate-500">{{ $pelanggaran }}</span>
                            <span class="text-slate-900 bg-slate-100 px-2 py-0.5 rounded-md">{{ $poin }} Poin</span>
                        </div>
                    @endforeach
                </div>

                <div class="bg-emerald-50 border border-emerald-200 border-dashed rounded-2xl p-4 text-center">
                    <i class="mdi mdi-check-circle-outline text-2xl text-emerald-500 mb-1"></i>
                    <p class="text-xs font-bold text-emerald-700 m-0">Hebat! Tidak ada penalti aktif yang berjalan.</p>
                </div>
            </div>

            {{-- WIDGET MASALAH PERLU DISELESAIKAN --}}
            <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-sm">
                <div class="border-b border-slate-100 pb-4 mb-5">
                    <h5 class="text-sm font-black text-slate-900 flex items-center gap-2 mb-1">
                        <i class="mdi mdi-alert-octagon-outline text-amber-500 text-lg leading-none"></i> Isu Berjalan
                    </h5>
                    <p class="text-[11px] font-bold text-slate-400">Ada {{ count(array_filter($masalah_perlu_diselesaikan)) }} masalah yang perlu ditangani.</p>
                </div>

                <div class="space-y-3">
                    <div class="flex justify-between items-center bg-amber-50 border border-amber-100 rounded-2xl p-4 hover:border-amber-300 transition-colors cursor-pointer">
                        <span class="text-xs font-bold text-amber-700 flex items-center gap-2">
                            <i class="mdi mdi-alert-circle text-amber-500 text-base"></i> Produk bermasalah
                        </span>
                        <span class="text-lg font-black text-amber-600">{{ $masalah_perlu_diselesaikan['produk_bermasalah'] }}</span>
                    </div>
                    <div class="flex justify-between items-center bg-red-50 border border-red-100 rounded-2xl p-4 hover:border-red-300 transition-colors cursor-pointer">
                        <span class="text-xs font-bold text-red-700 flex items-center gap-2">
                            <i class="mdi mdi-clock-alert text-red-500 text-base"></i> Telat Pengiriman
                        </span>
                        <span class="text-lg font-black text-red-600">{{ $masalah_perlu_diselesaikan['keterlambatan_pengiriman'] }}</span>
                    </div>
                </div>
            </div>

            {{-- SECTION BANDING AKUN (HANYA MUNCUL JIKA BANNED) --}}
            @if(Auth::user()->is_banned)
                <div class="bg-white border-2 border-red-100 rounded-3xl p-6 shadow-xl shadow-red-500/5 relative overflow-hidden">
                    <div class="absolute top-0 right-0 p-4 opacity-5">
                        <i class="mdi mdi-gavel text-8xl text-red-600"></i>
                    </div>

                    <div class="relative z-10">
                        <h5 class="text-sm font-black text-red-600 flex items-center gap-2 mb-4 uppercase tracking-widest">
                            <i class="mdi mdi-file-document-edit-outline text-lg"></i> Pengajuan Banding
                        </h5>

                        @php
                            $appeal = DB::table('tb_banding_akun')->where('user_id', Auth::id())->orderByDesc('created_at')->first();
                        @endphp

                        @if($appeal && $appeal->status === 'pending')
                            <div class="bg-amber-50 border border-amber-200 rounded-2xl p-5 text-center">
                                <i class="mdi mdi-clock-fast text-3xl text-amber-500 mb-2"></i>
                                <h6 class="text-sm font-black text-amber-800 mb-1">Banding Sedang Ditinjau</h6>
                                <p class="text-[11px] font-bold text-amber-600">Admin sedang memverifikasi klarifikasi Anda. Harap tunggu 1-3 hari kerja.</p>
                            </div>
                        @elseif($appeal && $appeal->status === 'ditolak')
                             <div class="bg-red-50 border border-red-200 rounded-2xl p-5 mb-4">
                                <h6 class="text-xs font-black text-red-800 flex items-center gap-2 mb-2 uppercase">
                                    <i class="mdi mdi-close-circle"></i> Banding Sebelumnya Ditolak
                                </h6>
                                <p class="text-[11px] font-bold text-red-600 mb-0">Catatan Admin: {{ $appeal->catatan_admin }}</p>
                            </div>
                            {{-- Form kirim ulang banding --}}
                            <form action="{{ route('account.appeal') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                                @csrf
                                <div>
                                    <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">Klarifikasi Perbaikan</label>
                                    <textarea name="alasan_banding" rows="4" class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 text-xs font-bold focus:ring-2 focus:ring-blue-500 outline-none transition-all" placeholder="Jelaskan bukti perbaikan yang telah Anda lakukan..."></textarea>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">Bukti Pendukung (Opsional)</label>
                                    <input type="file" name="bukti_pendukung" class="w-full text-xs font-bold text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-[10px] file:font-black file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                </div>
                                <button type="submit" class="w-full py-3 bg-red-600 hover:bg-red-700 text-white font-black rounded-xl text-xs uppercase tracking-widest transition-all shadow-lg shadow-red-200">
                                    Kirim Ulang Banding
                                </button>
                            </form>
                        @else
                            <form action="{{ route('account.appeal') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                                @csrf
                                <div>
                                    <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">Alasan & Klarifikasi</label>
                                    <textarea name="alasan_banding" rows="4" class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 text-xs font-bold focus:ring-2 focus:ring-blue-500 outline-none transition-all" placeholder="Jelaskan alasan mengapa penangguhan ini harus ditinjau kembali..."></textarea>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">Bukti Pendukung (Foto/Dokumen)</label>
                                    <input type="file" name="bukti_pendukung" class="w-full text-xs font-bold text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-[10px] file:font-black file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                </div>
                                <button type="submit" class="w-full py-3 bg-slate-900 hover:bg-black text-white font-black rounded-xl text-xs uppercase tracking-widest transition-all shadow-lg shadow-slate-200">
                                    Ajukan Banding
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @endif

        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {

    @if(session('success'))
        Swal.fire({ icon: 'success', title: 'Berhasil!', text: "{{ session('success') }}", confirmButtonColor: '#10b981' });
    @endif

    @if(session('error'))
        Swal.fire({ icon: 'error', title: 'Gagal!', text: "{{ session('error') }}", confirmButtonColor: '#ef4444' });
    @endif

    @if($errors->any())
        Swal.fire({
            icon: 'error',
            title: 'Validasi Gagal',
            html: '<ul class="text-left text-xs font-bold text-red-600 list-disc pl-5">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>',
            confirmButtonColor: '#ef4444'
        });
    @endif

    // Konfigurasi Radar Chart Tingkat Dewa
    const ctx = document.getElementById('healthRadarChart').getContext('2d');

    // Data dummy, disarankan diisi dari backend
    const dataSkor = [95, 100, 80, 100];

    new Chart(ctx, {
        type: 'radar',
        data: {
            labels: ['Kecepatan', 'Produk', 'Pelayanan', 'Stok'],
            datasets: [{
                label: 'Skor Kesehatan',
                data: dataSkor,
                backgroundColor: 'rgba(16, 185, 129, 0.15)', // Emerald transparent
                borderColor: '#10b981', // Emerald solid
                pointBackgroundColor: '#10b981',
                pointBorderColor: '#ffffff',
                pointHoverBackgroundColor: '#ffffff',
                pointHoverBorderColor: '#10b981',
                borderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 6,
                pointBorderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                r: {
                    angleLines: { color: '#f1f5f9' },
                    grid: { color: '#f1f5f9' },
                    pointLabels: {
                        color: '#64748b',
                        font: { family: 'Inter', size: 10, weight: '800' }
                    },
                    ticks: {
                        display: false,
                        min: 0,
                        max: 100
                    }
                }
            },
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#0f172a',
                    padding: 12,
                    cornerRadius: 8,
                    bodyFont: { family: 'Inter', size: 12, weight: '700' },
                    displayColors: false
                }
            },
            animation: {
                duration: 1500,
                easing: 'easeOutQuart'
            }
        }
    });

});
</script>
@endpush
