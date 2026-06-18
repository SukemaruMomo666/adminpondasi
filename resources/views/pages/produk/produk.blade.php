@extends('layouts.app')

@section('title', 'Katalog Material - Pondasikita B2B')

@section('content')

{{-- Konfigurasi Tailwind (Jika belum ada di app.blade.php) --}}
<script src="https://cdn.tailwindcss.com"></script>
<script>
    tailwind.config = {
        theme: {
            extend: {
                fontFamily: { sans: ['Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'] },
                colors: { brand: { 50: '#eff6ff', 500: '#3b82f6', 600: '#2563eb', 900: '#1e3a8a' } },
            }
        }
    }
</script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    /* Styling Scrollbar Khusus Area Filter */
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
    .custom-scrollbar:hover::-webkit-scrollbar-thumb { background: #cbd5e1; }
    
    /* Smooth Transition for Accordion */
    .accordion-content { transition: max-height 0.3s ease-in-out, opacity 0.3s ease-in-out; max-height: 0; opacity: 0; overflow: hidden; }
    .accordion-content.open { max-height: 500px; opacity: 1; }
</style>

<div class="bg-[#fafafa] min-h-screen pt-[80px] pb-24 font-sans text-zinc-900">
    
    {{-- Breadcrumb --}}
    <div class="bg-white border-b border-zinc-100 hidden md:block">
        <div class="max-w-[1400px] mx-auto px-4 lg:px-8 py-4">
            <nav class="flex text-[10px] font-black uppercase tracking-[0.2em] text-zinc-400 items-center gap-3">
                <a href="{{ url('/') }}" class="hover:text-blue-600 transition-colors">Beranda</a>
                <i class="fas fa-chevron-right text-[8px] opacity-30"></i>
                <span class="text-zinc-900">Katalog Material</span>
            </nav>
        </div>
    </div>

    <div class="max-w-[1400px] mx-auto px-4 lg:px-8 py-8 lg:py-12 flex flex-col lg:flex-row gap-8 items-start">
        
        {{-- ========================================== --}}
        {{-- SIDEBAR FILTER (KIRI) --}}
        {{-- ========================================== --}}
        
        {{-- Overlay Mobile --}}
        <div id="filter-overlay" class="fixed inset-0 bg-zinc-950/60 backdrop-blur-sm z-[60] opacity-0 invisible transition-all duration-300 lg:hidden"></div>

        <aside id="sidebar-filters" class="fixed inset-y-0 left-0 z-[70] w-[85%] max-w-[320px] bg-white lg:bg-transparent lg:static lg:w-1/4 lg:max-w-none transform -translate-x-full lg:translate-x-0 transition-transform duration-500 ease-out flex flex-col h-full lg:h-auto lg:block lg:sticky lg:top-28">
            
            <form action="{{ route('produk.index') }}" method="GET" class="flex flex-col h-full lg:bg-white lg:rounded-[2rem] lg:border lg:border-zinc-100 lg:shadow-sm overflow-hidden">
                
                @if(request('query'))
                    <input type="hidden" name="query" value="{{ request('query') }}">
                @endif

                {{-- Header Filter --}}
                <div class="px-6 py-5 border-b border-zinc-100 flex items-center justify-between bg-zinc-50/50">
                    <h3 class="text-sm font-black text-zinc-900 uppercase tracking-widest flex items-center gap-2">
                        <i class="fas fa-sliders-h text-blue-600"></i> Filter Pencarian
                    </h3>
                    <button type="button" id="close-filter-btn" class="lg:hidden w-8 h-8 rounded-full bg-zinc-100 flex items-center justify-center text-zinc-500 hover:bg-red-500 hover:text-white transition-colors">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                {{-- Body Filter --}}
                <div class="p-6 flex-1 overflow-y-auto custom-scrollbar space-y-8">
                    
                    {{-- 1. Kategori (Accordion Model) --}}
                    <div>
                        <h4 class="text-[10px] font-black text-zinc-400 uppercase tracking-[0.2em] mb-4">Kategori Utama</h4>
                        <div class="space-y-2" id="category-accordion">
                            
                            @foreach($categories as $k)
                                <div class="accordion-item border border-zinc-100 rounded-2xl bg-zinc-50/50 overflow-hidden">
                                    {{-- Header Kategori Utama --}}
                                    <button type="button" class="accordion-header w-full px-4 py-3 flex items-center justify-between text-sm font-bold text-zinc-700 hover:text-blue-600 transition-colors">
                                        <span class="flex items-center gap-2">
                                            <i class="fas fa-folder text-zinc-300 text-xs"></i> {{ $k->nama_kategori }}
                                        </span>
                                        <i class="fas fa-chevron-down text-[10px] text-zinc-400 transition-transform duration-300 icon-arrow"></i>
                                    </button>
                                    
                                    {{-- Sub Kategori Dropdown --}}
                                    {{-- PASTIKAN NAMA RELASI '$k->subKategori' SESUAI DENGAN MODEL BOS --}}
                                    <div class="accordion-content bg-white">
                                        <div class="p-4 pt-1 space-y-3">
                                            {{-- Jika tidak punya relasi subkategori di DB, gunakan looping manual atau biarkan checkbox ada disini --}}
                                            @if(isset($k->subKategori) && count($k->subKategori) > 0)
                                                @foreach($k->subKategori as $sub)
                                                    <label class="flex items-start gap-3 cursor-pointer group">
                                                        <div class="relative flex items-center justify-center shrink-0 mt-0.5">
                                                            <input type="checkbox" name="kategori[]" value="{{ $sub->id }}" class="peer sr-only" {{ in_array($sub->id, request('kategori', [])) ? 'checked' : '' }}>
                                                            <div class="w-4 h-4 rounded border-2 border-zinc-200 peer-checked:bg-blue-600 peer-checked:border-blue-600 transition-all flex items-center justify-center">
                                                                <i class="fas fa-check text-white text-[8px] opacity-0 peer-checked:opacity-100 transition-opacity"></i>
                                                            </div>
                                                        </div>
                                                        <span class="text-xs font-semibold text-zinc-600 group-hover:text-zinc-900 transition-colors">{{ $sub->nama_kategori }}</span>
                                                    </label>
                                                @endforeach
                                            @else
                                                {{-- Fallback jika hanya ada 1 level kategori --}}
                                                <label class="flex items-start gap-3 cursor-pointer group">
                                                    <div class="relative flex items-center justify-center shrink-0 mt-0.5">
                                                        <input type="checkbox" name="kategori[]" value="{{ $k->id }}" class="peer sr-only" {{ in_array($k->id, request('kategori', [])) ? 'checked' : '' }}>
                                                        <div class="w-4 h-4 rounded border-2 border-zinc-200 peer-checked:bg-blue-600 peer-checked:border-blue-600 transition-all flex items-center justify-center">
                                                            <i class="fas fa-check text-white text-[8px] opacity-0 peer-checked:opacity-100 transition-opacity"></i>
                                                        </div>
                                                    </div>
                                                    <span class="text-xs font-semibold text-zinc-600 group-hover:text-zinc-900 transition-colors">Semua {{ $k->nama_kategori }}</span>
                                                </label>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach

                        </div>
                    </div>

                    {{-- 2. Lokasi --}}
                    <div>
                        <h4 class="text-[10px] font-black text-zinc-400 uppercase tracking-[0.2em] mb-4">Lokasi Pengiriman</h4>
                        <div class="relative">
                            <select name="lokasi" class="w-full bg-zinc-50 border border-zinc-200 text-zinc-700 text-sm font-bold rounded-xl px-4 py-3 appearance-none outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/10 transition-all cursor-pointer">
                                <option value="">Semua Wilayah</option>
                                @foreach($locations as $l)
                                    <option value="{{ $l->city_id }}" {{ request('lokasi') == $l->city_id ? 'selected' : '' }}>
                                        {{ $l->nama_kota }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                                <i class="fas fa-map-marker-alt text-zinc-400"></i>
                            </div>
                        </div>
                    </div>

                    {{-- 3. Rentang Harga --}}
                    <div>
                        <h4 class="text-[10px] font-black text-zinc-400 uppercase tracking-[0.2em] mb-4">Rentang Harga</h4>
                        <div class="flex items-center gap-3">
                            <div class="relative w-full">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-xs font-bold text-zinc-400 pointer-events-none">Rp</span>
                                <input type="number" name="harga_min" value="{{ request('harga_min') }}" placeholder="Min" class="w-full bg-zinc-50 border border-zinc-200 text-zinc-900 text-sm font-bold rounded-xl pl-9 pr-3 py-3 outline-none focus:border-blue-600 transition-all">
                            </div>
                            <span class="text-zinc-300 font-bold">-</span>
                            <div class="relative w-full">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-xs font-bold text-zinc-400 pointer-events-none">Rp</span>
                                <input type="number" name="harga_max" value="{{ request('harga_max') }}" placeholder="Max" class="w-full bg-zinc-50 border border-zinc-200 text-zinc-900 text-sm font-bold rounded-xl pl-9 pr-3 py-3 outline-none focus:border-blue-600 transition-all">
                            </div>
                        </div>
                    </div>

                </div>

                {{-- Footer Filter (Action Buttons) --}}
                <div class="p-6 border-t border-zinc-100 bg-white">
                    <button type="submit" class="w-full bg-zinc-950 hover:bg-blue-600 text-white font-black text-xs uppercase tracking-widest py-4 rounded-xl transition-all shadow-md hover:shadow-lg hover:-translate-y-0.5">
                        Terapkan Filter
                    </button>
                    
                    @if(request()->hasAny(['kategori', 'lokasi', 'harga_min', 'harga_max', 'query', 'sort']))
                        <a href="{{ route('produk.index') }}" class="block text-center mt-4 text-xs font-bold text-red-500 hover:text-red-600 transition-colors">
                            <i class="fas fa-sync-alt mr-1"></i> Reset Semua
                        </a>
                    @endif
                </div>

            </form>
        </aside>

        {{-- ========================================== --}}
        {{-- KONTEN PRODUK (KANAN) --}}
        {{-- ========================================== --}}
        <main class="w-full lg:w-3/4 flex flex-col min-w-0">
            
            {{-- Top Bar (Sort & Mobile Button) --}}
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8 bg-white p-4 rounded-2xl border border-zinc-100 shadow-sm">
                
                <div class="text-sm font-medium text-zinc-500">
                    Menampilkan <span class="font-black text-zinc-900">{{ $products->total() }}</span> material ditemukan.
                </div>
                
                <div class="flex items-center gap-3 w-full sm:w-auto">
                    {{-- Mobile Filter Button --}}
                    <button type="button" id="mobile-filter-btn" class="lg:hidden flex-1 sm:flex-none flex items-center justify-center gap-2 bg-zinc-100 hover:bg-zinc-200 text-zinc-800 font-bold text-xs uppercase tracking-widest px-5 py-3 rounded-xl transition-colors">
                        <i class="fas fa-filter"></i> Filter
                    </button>

                    {{-- Sort Dropdown Form (Icon Standar) --}}
                    <form action="{{ route('produk.index') }}" method="GET" class="flex-1 sm:flex-none relative" id="sort-form">
                        {{-- Pertahankan filter yang ada --}}
                        @foreach(request()->except('sort', 'page') as $key => $value)
                            @if(is_array($value))
                                @foreach($value as $v) <input type="hidden" name="{{ $key }}[]" value="{{ $v }}"> @endforeach
                            @else
                                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                            @endif
                        @endforeach

                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="fas fa-sort-amount-down text-zinc-400"></i>
                        </div>
                        <select name="sort" onchange="document.getElementById('sort-form').submit()" class="w-full bg-zinc-50 border border-zinc-200 text-zinc-700 text-sm font-bold rounded-xl pl-10 pr-10 py-3 appearance-none outline-none focus:border-blue-600 transition-all cursor-pointer">
                            <option value="terbaru" {{ request('sort') == 'terbaru' ? 'selected' : '' }}>Paling Baru</option>
                            <option value="termurah" {{ request('sort') == 'termurah' ? 'selected' : '' }}>Harga Terendah</option>
                            <option value="termahal" {{ request('sort') == 'termahal' ? 'selected' : '' }}>Harga Tertinggi</option>
                            <option value="abjad" {{ request('sort') == 'abjad' ? 'selected' : '' }}>A - Z</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                            <i class="fas fa-chevron-down text-[10px] text-zinc-400"></i>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Grid Produk --}}
            <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-4 sm:gap-6 mb-10">
                @forelse($products as $b)
                    <a href="{{ route('produk.detail', $b->slug) }}" class="group bg-white rounded-[1.5rem] border border-zinc-100 overflow-hidden shadow-sm hover:shadow-xl hover:border-blue-100 hover:-translate-y-1 transition-all duration-300 flex flex-col">
                        
                        {{-- Image --}}
                        <div class="w-full aspect-square bg-zinc-50 overflow-hidden relative p-4">
                            @php $img = !empty($b->gambar_utama) ? 'assets/uploads/products/'.$b->gambar_utama : 'assets/uploads/products/default.jpg'; @endphp
                            <img src="{{ asset($img) }}" alt="{{ $b->nama_barang }}" class="w-full h-full object-cover rounded-xl mix-blend-multiply group-hover:scale-110 transition-transform duration-700" onerror="this.onerror=null; this.src='{{ asset('assets/uploads/products/default.jpg') }}';">
                        </div>
                        
                        {{-- Detail --}}
                        <div class="p-4 sm:p-5 flex flex-col flex-1">
                            <h3 class="text-xs sm:text-sm font-bold text-zinc-800 line-clamp-2 mb-2 leading-snug group-hover:text-blue-600 transition-colors">{{ $b->nama_barang }}</h3>
                            
                            @php
                                $now = \Carbon\Carbon::now();
                                $isPromo = !empty($b->nilai_diskon) && $b->nilai_diskon > 0;
                                if ($isPromo && $b->diskon_mulai && $b->diskon_berakhir) {
                                    $start = \Carbon\Carbon::parse($b->diskon_mulai);
                                    $end = \Carbon\Carbon::parse($b->diskon_berakhir);
                                    if (!$now->between($start, $end)) { $isPromo = false; }
                                }

                                $hargaTampil = $b->harga;
                                if($isPromo) {
                                    if($b->tipe_diskon == 'PERSEN') {
                                        $hargaTampil = $b->harga - ($b->harga * ($b->nilai_diskon / 100));
                                    } else {
                                        $hargaTampil = $b->harga - $b->nilai_diskon;
                                    }
                                }
                            @endphp

                            <div class="mt-auto mb-3">
                                @if($isPromo)
                                    <div class="flex items-center gap-2 mb-0.5">
                                        <span class="text-[9px] font-black bg-red-100 text-red-600 px-1 py-0.5 rounded">{{ $b->tipe_diskon == 'PERSEN' ? $b->nilai_diskon.'%' : 'PROMO' }}</span>
                                        <span class="text-[10px] text-zinc-400 line-through font-bold">Rp{{ number_format($b->harga, 0, ',', '.') }}</span>
                                    </div>
                                @endif
                                <div class="text-base sm:text-lg font-black text-zinc-900 tracking-tight">Rp{{ number_format($hargaTampil, 0, ',', '.') }}</div>
                            </div>
                            
                            <div class="pt-3 border-t border-zinc-50 space-y-1.5">
                                <div class="text-[10px] sm:text-xs font-semibold text-zinc-500 truncate flex items-center gap-1.5">
                                    <i class="fas fa-store text-blue-500 w-3 text-center"></i> {{ $b->nama_toko }}
                                </div>
                                <div class="text-[10px] sm:text-xs font-semibold text-zinc-400 truncate flex items-center gap-1.5">
                                    <i class="fas fa-map-marker-alt text-red-400 w-3 text-center"></i> {{ $b->nama_kota ?? 'Lokasi Nasional' }}
                                </div>
                            </div>
                        </div>
                    </a>
                @empty
                    {{-- Empty State --}}
                    <div class="col-span-full bg-white rounded-[2rem] border border-zinc-100 py-20 flex flex-col items-center justify-center text-center px-4">
                        <div class="w-24 h-24 bg-zinc-50 rounded-full flex items-center justify-center text-4xl text-zinc-300 mb-6">
                            <i class="fas fa-box-open"></i>
                        </div>
                        <h3 class="text-2xl font-black text-zinc-900 mb-2">Material Tidak Ditemukan</h3>
                        <p class="text-sm font-medium text-zinc-500 max-w-md mb-6">Maaf, kami tidak dapat menemukan material dengan filter yang Anda pilih. Silakan sesuaikan kriteria pencarian Anda.</p>
                        <a href="{{ route('produk.index') }}" class="bg-black hover:bg-blue-600 text-white font-bold py-3 px-8 rounded-xl transition-all">
                            Lihat Semua Material
                        </a>
                    </div>
                @endforelse
            </div>

            {{-- Pagination --}}
            <div class="flex justify-center mt-auto">
                {{ $products->appends(request()->query())->links('pagination::tailwind') }}
            </div>

        </main>
    </div>
</div>

{{-- SCRIPT INTERAKSI --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        
        // 1. MOBILE SIDEBAR LOGIC
        const mobileFilterBtn = document.getElementById('mobile-filter-btn');
        const sidebarFilters = document.getElementById('sidebar-filters');
        const closeFilterBtn = document.getElementById('close-filter-btn');
        const filterOverlay = document.getElementById('filter-overlay');
        
        function openSidebar() {
            sidebarFilters.classList.remove('-translate-x-full');
            filterOverlay.classList.remove('opacity-0', 'invisible');
            document.body.style.overflow = 'hidden'; // Prevent background scrolling
        }

        function closeSidebar() {
            sidebarFilters.classList.add('-translate-x-full');
            filterOverlay.classList.add('opacity-0', 'invisible');
            document.body.style.overflow = '';
        }

        if (mobileFilterBtn) mobileFilterBtn.addEventListener('click', openSidebar);
        if (closeFilterBtn) closeFilterBtn.addEventListener('click', closeSidebar);
        if (filterOverlay) filterOverlay.addEventListener('click', closeSidebar);

        // 2. ACCORDION KATEGORI (Hanya boleh 1 yang terbuka)
        const accordionHeaders = document.querySelectorAll('.accordion-header');
        
        // Buka otomatis jika ada checkbox yang tercentang di dalamnya
        document.querySelectorAll('.accordion-item').forEach(item => {
            const hasChecked = item.querySelector('input[type="checkbox"]:checked');
            if (hasChecked) {
                const content = item.querySelector('.accordion-content');
                const icon = item.querySelector('.icon-arrow');
                content.classList.add('open');
                icon.classList.add('rotate-180');
            }
        });

        accordionHeaders.forEach(header => {
            header.addEventListener('click', function() {
                const currentItem = this.parentElement;
                const currentContent = currentItem.querySelector('.accordion-content');
                const currentIcon = this.querySelector('.icon-arrow');
                const isOpen = currentContent.classList.contains('open');

                // Tutup semua konten yang lain dulu
                document.querySelectorAll('.accordion-content').forEach(content => {
                    content.classList.remove('open');
                });
                document.querySelectorAll('.icon-arrow').forEach(icon => {
                    icon.classList.remove('rotate-180');
                });

                // Jika tadinya tertutup, maka buka yang ini
                if (!isOpen) {
                    currentContent.classList.add('open');
                    currentIcon.classList.add('rotate-180');
                }
            });
        });

    });
</script>
@endsection