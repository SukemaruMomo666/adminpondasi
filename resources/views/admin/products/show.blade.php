@extends('layouts.admin')

@section('title', 'Moderasi Material: ' . $produk->nama_barang)

@push('styles')
<style>
    /* ========================================= */
    /* ==  PREMIUM DETAIL MODERATION CSS      == */
    /* ========================================= */

    /* Animasi Hover Halus */
    .hover-lift { transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.3s ease; }
    .hover-lift:hover { transform: translateY(-4px); }

    /* Scrollbar Khusus Area Deskripsi/Galeri */
    .custom-scroll::-webkit-scrollbar { width: 6px; height: 6px; }
    .custom-scroll::-webkit-scrollbar-track { background: transparent; }
    .custom-scroll::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    .custom-scroll::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    .dark .custom-scroll::-webkit-scrollbar-thumb { background: #475569; }
    .dark .custom-scroll::-webkit-scrollbar-thumb:hover { background: #64748b; }

    /* ========================================= */
    /* == POLYFILL DARK MODE (ANTI-PUTIH)     == */
    /* ========================================= */

    /* Container Utama & Card */
    .dark .dark\:bg-slate-950 { background-color: #020617 !important; }
    .dark .dark\:bg-slate-900 { background-color: #0f172a !important; }
    .dark .dark\:bg-slate-900\/50 { background-color: rgba(15, 23, 42, 0.5) !important; }
    .dark .dark\:bg-slate-800 { background-color: #1e293b !important; }
    .dark .dark\:bg-slate-800\/60 { background-color: rgba(30, 41, 59, 0.6) !important; }
    .dark .dark\:bg-slate-800\/50 { background-color: rgba(30, 41, 59, 0.5) !important; }
    .dark .dark\:bg-slate-700 { background-color: #334155 !important; }

    /* Border & Divider */
    .dark .dark\:border-slate-800 { border-color: #1e293b !important; }
    .dark .dark\:border-slate-700 { border-color: #334155 !important; }
    .dark .dark\:border-slate-700\/50 { border-color: rgba(51, 65, 85, 0.5) !important; }
    .dark .divide-slate-800\/80 > * + * { border-color: rgba(30, 41, 59, 0.8) !important; }

    /* Typography */
    .dark .dark\:text-white { color: #ffffff !important; }
    .dark .dark\:text-slate-100 { color: #f1f5f9 !important; }
    .dark .dark\:text-slate-200 { color: #e2e8f0 !important; }
    .dark .dark\:text-slate-300 { color: #cbd5e1 !important; }
    .dark .dark\:text-slate-400 { color: #94a3b8 !important; }

    /* Modals & Forms Overrides */
    .dark .modal-content { background-color: #0f172a !important; border: 1px solid #1e293b !important; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5) !important; color: #f8fafc !important; }
    .dark .modal-header, .dark .modal-footer { border-color: #1e293b !important; background-color: #0f172a !important; }
    .dark .btn-close { filter: invert(1) grayscale(100%) brightness(200%); }
    .dark .form-control { background-color: #1e293b !important; border-color: #334155 !important; color: #f8fafc !important; }
    .dark .form-control:focus { border-color: #f43f5e !important; box-shadow: 0 0 0 4px rgba(244, 63, 94, 0.15) !important; }

    /* Action Panels & Status Badges */
    .dark .dark\:bg-blue-500\/10 { background-color: rgba(59, 130, 246, 0.1) !important; }
    .dark .dark\:bg-blue-500\/20 { background-color: rgba(59, 130, 246, 0.2) !important; }
    .dark .dark\:bg-blue-500\/50 { background-color: rgba(59, 130, 246, 0.5) !important; }
    .dark .dark\:border-blue-500\/50 { border-color: rgba(59, 130, 246, 0.5) !important; }

    .dark .dark\:bg-amber-500\/20 { background-color: rgba(245, 158, 11, 0.2) !important; }
    .dark .dark\:border-amber-500\/30 { border-color: rgba(245, 158, 11, 0.3) !important; }

    .dark .dark\:bg-emerald-500\/20 { background-color: rgba(16, 185, 129, 0.2) !important; }
    .dark .dark\:border-emerald-500\/30 { border-color: rgba(16, 185, 129, 0.3) !important; }

    .dark .dark\:bg-rose-500\/10 { background-color: rgba(244, 63, 94, 0.1) !important; }
    .dark .dark\:bg-rose-500\/20 { background-color: rgba(244, 63, 94, 0.2) !important; }
    .dark .dark\:border-rose-500\/20 { border-color: rgba(244, 63, 94, 0.2) !important; }
    .dark .dark\:border-rose-500\/30 { border-color: rgba(244, 63, 94, 0.3) !important; }

    .dark .dark\:bg-indigo-500\/20 { background-color: rgba(99, 102, 241, 0.2) !important; }

    /* Interactive Elements */
    .dark .dark\:hover\:bg-slate-800:hover { background-color: #1e293b !important; }
    .dark .dark\:hover\:bg-rose-500\/10:hover { background-color: rgba(244, 63, 94, 0.1) !important; }
</style>
@endpush

@section('content')

{{-- HEADER HALAMAN --}}
<div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-4 mb-8">
    <div>
        <a href="{{ route('admin.products.index') }}" class="inline-flex items-center gap-2 px-3 py-1.5 bg-white dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 text-slate-500 dark:text-slate-400 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-slate-50 dark:hover:bg-slate-800 rounded-lg text-xs font-bold transition-all text-decoration-none outline-none mb-4 shadow-sm dark:shadow-none">
            <i class="mdi mdi-arrow-left"></i> Kembali ke Daftar
        </a>
        <h2 class="text-2xl md:text-3xl font-black text-slate-800 dark:text-white tracking-tight mb-1 transition-colors duration-300">
            Detail Material
        </h2>
        <div class="flex items-center gap-2 text-xs font-bold text-slate-500 dark:text-slate-400 transition-colors duration-300">
            Pusat Moderasi <i class="mdi mdi-chevron-right text-sm"></i> <span class="text-blue-600 dark:text-blue-400 truncate max-w-[200px]">{{ $produk->nama_barang }}</span>
        </div>
    </div>

    {{-- Badge ID Produk --}}
    <div class="bg-white dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700/50 px-4 py-2.5 rounded-xl shadow-sm transition-colors duration-300 flex items-center gap-2">
        <i class="mdi mdi-barcode-scan text-slate-400 dark:text-slate-500 text-lg"></i>
        <div class="flex flex-col">
            <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest leading-none">Sistem ID</span>
            <span class="text-sm font-black text-slate-800 dark:text-white font-mono mt-0.5">#{{ str_pad($produk->id, 6, '0', STR_PAD_LEFT) }}</span>
        </div>
    </div>
</div>

{{-- HERO SECTION (INFO UTAMA) --}}
<div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-[2rem] p-6 mb-8 shadow-sm transition-colors duration-300 flex flex-col md:flex-row gap-6 lg:gap-8 items-start">

    {{-- Gambar Utama --}}
    <div class="w-full md:w-64 lg:w-72 aspect-square rounded-[1.5rem] bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 overflow-hidden flex-shrink-0 relative shadow-inner dark:shadow-none">
        <img src="{{ asset('assets/uploads/products/' . ($produk->gambar_utama ?? 'default.jpg')) }}" alt="{{ $produk->nama_barang }}" class="w-full h-full object-cover">

        {{-- Status Badge Mengambang di Gambar --}}
        @php
            $st = strtolower($produk->status_moderasi);
            $badgeBg = $st == 'pending' ? 'bg-amber-100 text-amber-700 border-amber-200 dark:bg-amber-500/20 dark:text-amber-400 dark:border-amber-500/30' :
                      ($st == 'approved' ? 'bg-emerald-100 text-emerald-700 border-emerald-200 dark:bg-emerald-500/20 dark:text-emerald-400 dark:border-emerald-500/30' :
                      'bg-rose-100 text-rose-700 border-rose-200 dark:bg-rose-500/20 dark:text-rose-400 dark:border-rose-500/30');
            $icon = $st == 'pending' ? 'mdi-clock-outline animate-pulse' : ($st == 'approved' ? 'mdi-check-decagram' : 'mdi-cancel');
        @endphp
        <div class="absolute top-4 left-4 inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-[10px] font-black uppercase tracking-wider border backdrop-blur-md {{ $badgeBg }}">
            <i class="mdi {{ $icon }} text-sm"></i> {{ $produk->status_moderasi }}
        </div>
    </div>

    {{-- Detail Teks Hero --}}
    <div class="flex-1 w-full pt-2">
        <h1 class="text-2xl lg:text-3xl font-black text-slate-800 dark:text-white leading-tight mb-4 transition-colors duration-300">
            {{ $produk->nama_barang }}
        </h1>

        <div class="flex flex-wrap gap-3 mt-4">
            <div class="inline-flex items-center gap-2 px-3 py-2 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700/50 rounded-xl">
                <div class="w-6 h-6 rounded bg-blue-100 dark:bg-blue-500/20 text-blue-600 dark:text-blue-400 flex items-center justify-center text-xs"><i class="mdi mdi-storefront-outline"></i></div>
                <span class="text-xs font-bold text-slate-700 dark:text-slate-300">{{ $produk->nama_toko }}</span>
            </div>

            <div class="inline-flex items-center gap-2 px-3 py-2 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700/50 rounded-xl">
                <div class="w-6 h-6 rounded bg-indigo-100 dark:bg-indigo-500/20 text-indigo-600 dark:text-indigo-400 flex items-center justify-center text-xs"><i class="mdi mdi-tag-outline"></i></div>
                <span class="text-xs font-bold text-slate-700 dark:text-slate-300">{{ $produk->nama_kategori ?? 'Tanpa Kategori' }}</span>
            </div>

            <div class="inline-flex items-center gap-2 px-3 py-2 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700/50 rounded-xl">
                <div class="w-6 h-6 rounded bg-slate-200 dark:bg-slate-700 text-slate-600 dark:text-slate-400 flex items-center justify-center text-xs"><i class="mdi mdi-barcode"></i></div>
                <span class="text-xs font-bold text-slate-700 dark:text-slate-300">SKU: {{ $produk->kode_barang ?? '-' }}</span>
            </div>
        </div>
    </div>
</div>

{{-- GRID KONTEN UTAMA --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-8 items-start mb-10">

    {{-- KOLOM KIRI (DESKRIPSI & GALERI) --}}
    <div class="lg:col-span-2 space-y-6 lg:space-y-8">

        {{-- Card: Deskripsi --}}
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-[2rem] p-6 lg:p-8 shadow-sm transition-colors duration-300">
            <h4 class="text-[11px] font-black text-slate-400 uppercase tracking-[0.2em] flex items-center gap-2 mb-6">
                <i class="mdi mdi-text-box-outline text-lg"></i> Deskripsi Material
            </h4>
            <div class="text-sm font-medium text-slate-600 dark:text-slate-300 leading-relaxed whitespace-pre-line custom-scroll max-h-[400px] overflow-y-auto">
                {!! nl2br(e($produk->deskripsi)) !!}
            </div>
        </div>

        {{-- Card: Galeri --}}
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-[2rem] p-6 lg:p-8 shadow-sm transition-colors duration-300">
            <h4 class="text-[11px] font-black text-slate-400 uppercase tracking-[0.2em] flex items-center gap-2 mb-6">
                <i class="mdi mdi-image-multiple-outline text-lg"></i> Galeri Foto Lainnya
            </h4>

            <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 gap-4">
                {{-- Foto Utama masuk galeri juga --}}
                <div class="aspect-square rounded-xl overflow-hidden border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 cursor-pointer group">
                    <img src="{{ asset('assets/uploads/products/' . ($produk->gambar_utama ?? 'default.jpg')) }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300">
                </div>

                {{-- Loop Galeri Tambahan --}}
                @forelse($gallery as $img)
                    <div class="aspect-square rounded-xl overflow-hidden border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 cursor-pointer group">
                        <img src="{{ asset('assets/uploads/products/' . $img->nama_file) }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300">
                    </div>
                @empty
                    @if(empty($produk->gambar_utama))
                    <div class="col-span-full py-6 text-center bg-slate-50 dark:bg-slate-800/50 rounded-xl border border-dashed border-slate-200 dark:border-slate-700">
                        <p class="text-xs font-bold text-slate-400 m-0 italic">Tidak ada foto tambahan yang diunggah.</p>
                    </div>
                    @endif
                @endforelse
            </div>
        </div>
    </div>

    {{-- KOLOM KANAN (INFO HARGA & AKSI STICKY) --}}
    <div class="lg:col-span-1 space-y-6 lg:space-y-8 sticky top-[100px]">

        {{-- Card: Info Komersial --}}
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-[2rem] p-6 shadow-sm transition-colors duration-300">
            <h4 class="text-[11px] font-black text-slate-400 uppercase tracking-[0.2em] flex items-center gap-2 mb-6">
                <i class="mdi mdi-cash-tag text-lg"></i> Informasi Komersial
            </h4>

            <ul class="m-0 p-0 list-none divide-y divide-slate-100 dark:divide-slate-800/80">
                <li class="py-3 flex justify-between items-center">
                    <span class="text-xs font-bold text-slate-500 dark:text-slate-400">Harga Satuan</span>
                    <span class="text-lg font-black text-blue-600 dark:text-blue-400">Rp {{ number_format($produk->harga, 0, ',', '.') }}</span>
                </li>
                <li class="py-3 flex justify-between items-center">
                    <span class="text-xs font-bold text-slate-500 dark:text-slate-400">Stok Ready</span>
                    <span class="text-sm font-black text-slate-800 dark:text-white">{{ $produk->stok }} <span class="text-xs text-slate-400">{{ $produk->satuan_unit }}</span></span>
                </li>
                <li class="py-3 flex justify-between items-center">
                    <span class="text-xs font-bold text-slate-500 dark:text-slate-400">Berat Logistik</span>
                    <span class="text-sm font-black text-rose-600 dark:text-rose-400">{{ $produk->berat_kg }} Kg</span>
                </li>
                <li class="py-3 flex justify-between items-center">
                    <span class="text-xs font-bold text-slate-500 dark:text-slate-400">Diskon Aktif</span>
                    <span class="text-sm font-black text-emerald-600 dark:text-emerald-400">
                        {{ $produk->nilai_diskon ? ($produk->tipe_diskon == 'PERSEN' ? $produk->nilai_diskon.'%' : 'Rp '.number_format($produk->nilai_diskon, 0, ',', '.')) : '-' }}
                    </span>
                </li>
            </ul>
        </div>

        {{-- PANEL AKSI MODERASI --}}
        @if($produk->status_moderasi == 'pending')
            <div class="bg-white dark:bg-slate-900 border-2 border-blue-500/30 dark:border-blue-500/50 rounded-[2rem] p-6 shadow-lg shadow-blue-500/10 dark:shadow-[0_0_30px_rgba(59,130,246,0.1)] relative overflow-hidden transition-colors duration-300">
                <div class="absolute top-0 inset-x-0 h-1 bg-gradient-to-r from-blue-400 via-indigo-500 to-purple-500"></div>

                <h4 class="text-[11px] font-black text-blue-600 dark:text-blue-400 uppercase tracking-[0.2em] mb-2 text-center">
                    Keputusan Eksekusi
                </h4>
                <p class="text-xs font-bold text-slate-500 dark:text-slate-400 text-center mb-6">
                    Pastikan gambar jelas, tidak mengandung unsur terlarang, dan deskripsi sesuai.
                </p>

                <div class="space-y-3">
                    {{-- Form Approve --}}
                    <form id="form-approve" action="{{ route('admin.products.process', $produk->id) }}" method="POST" class="m-0">
                        @csrf
                        <input type="hidden" name="action" value="approve">
                        <button type="button" onclick="confirmApprove()" class="w-full flex items-center justify-center gap-2 px-4 py-3.5 bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-black rounded-xl shadow-md shadow-emerald-500/30 hover:shadow-lg hover:-translate-y-0.5 transition-all outline-none">
                            <i class="mdi mdi-check-decagram text-lg"></i> SETUJUI MATERIAL
                        </button>
                    </form>

                    {{-- Form Reject (Hidden input for SweetAlert) --}}
                    <form id="form-reject" action="{{ route('admin.products.process', $produk->id) }}" method="POST" class="m-0">
                        @csrf
                        <input type="hidden" name="action" value="reject">
                        <input type="hidden" name="alasan_penolakan" id="reject-alasan">
                        <button type="button" onclick="confirmReject()" class="w-full flex items-center justify-center gap-2 px-4 py-3.5 bg-white dark:bg-slate-800 border-2 border-rose-100 dark:border-rose-500/30 text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-500/10 text-sm font-black rounded-xl transition-all outline-none">
                            <i class="mdi mdi-close-octagon-outline text-lg"></i> TOLAK PENGAJUAN
                        </button>
                    </form>
                </div>
            </div>

        @elseif($produk->status_moderasi == 'rejected')
            {{-- Panel Alasan Ditolak --}}
            <div class="bg-rose-50 dark:bg-rose-500/10 border border-rose-200 dark:border-rose-500/20 rounded-[2rem] p-6 shadow-sm transition-colors duration-300">
                <h4 class="text-[11px] font-black text-rose-600 dark:text-rose-400 uppercase tracking-[0.2em] flex items-center gap-2 mb-3">
                    <i class="mdi mdi-alert-circle text-lg"></i> Alasan Penolakan
                </h4>
                <p class="text-sm font-bold text-slate-800 dark:text-slate-200 leading-relaxed italic m-0">
                    "{{ $produk->alasan_penolakan }}"
                </p>
            </div>
        @endif

    </div>
</div>

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
<style>
    /* Styling khusus agar SweetAlert menyatu dengan Tailwind Dark Mode */
    .dark .swal2-popup { background-color: #0f172a; border: 1px solid #1e293b; color: #f8fafc; }
    .dark .swal2-title, .dark .swal2-html-container { color: #f8fafc; }
    .dark .swal2-textarea { background-color: #1e293b; color: #f8fafc; border-color: #334155; }
    .dark .swal2-textarea:focus { border-color: #f43f5e; box-shadow: 0 0 0 3px rgba(244, 63, 94, 0.2); }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    const isDarkMode = document.documentElement.classList.contains('dark');
    const swalBg = isDarkMode ? '#0f172a' : '#ffffff';
    const swalColor = isDarkMode ? '#f8fafc' : '#1e293b';

    function confirmApprove() {
        Swal.fire({
            title: 'Setujui Material?',
            text: "Material ini akan langsung tampil di katalog dan bisa dibeli oleh customer.",
            icon: 'success',
            showCancelButton: true,
            confirmButtonColor: '#10b981',
            cancelButtonColor: '#64748b',
            confirmButtonText: '<i class="mdi mdi-check-decagram mr-1"></i> Ya, Setujui',
            cancelButtonText: 'Batal',
            background: swalBg,
            color: swalColor,
            customClass: {
                popup: 'rounded-3xl',
                confirmButton: 'rounded-xl font-bold shadow-lg shadow-emerald-500/30',
                cancelButton: 'rounded-xl font-bold'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('form-approve').submit();
            }
        });
    }

    function confirmReject() {
        Swal.fire({
            title: 'Tolak Material Ini?',
            text: "Berikan alasan yang jelas agar penjual dapat memperbaiki kesalahannya.",
            icon: 'warning',
            input: 'textarea',
            inputPlaceholder: 'Contoh: Foto produk sangat buram, mengandung kata terlarang...',
            inputAttributes: {
                'aria-label': 'Alasan penolakan'
            },
            showCancelButton: true,
            confirmButtonColor: '#f43f5e',
            cancelButtonColor: '#64748b',
            confirmButtonText: '<i class="mdi mdi-send mr-1"></i> Kirim Penolakan',
            cancelButtonText: 'Batal',
            background: swalBg,
            color: swalColor,
            customClass: {
                popup: 'rounded-3xl',
                confirmButton: 'rounded-xl font-bold shadow-lg shadow-rose-500/30',
                cancelButton: 'rounded-xl font-bold',
                input: 'rounded-xl border-slate-200 text-sm font-bold text-slate-800'
            },
            inputValidator: (value) => {
                if (!value || value.trim().length < 10) {
                    return 'Alasan penolakan wajib diisi (minimal 10 karakter)!';
                }
            }
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('reject-alasan').value = result.value;
                document.getElementById('form-reject').submit();
            }
        });
    }
</script>
@endpush

@endsection
