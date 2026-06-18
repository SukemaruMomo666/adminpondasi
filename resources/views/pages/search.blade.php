<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Hasil Pencarian: {{ request('query') }} - Pondasikita B2B</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    {{-- Tailwind CSS CDN --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'ui-sans-serif', 'system-ui', '-apple-system', 'sans-serif'] },
                    colors: {
                        brand: { 50: '#eff6ff', 100: '#dbeafe', 500: '#3b82f6', 600: '#2563eb', 700: '#1d4ed8' }
                    },
                    boxShadow: {
                        'soft': '0 4px 40px -4px rgba(0,0,0,0.03)',
                        'hover': '0 10px 30px -5px rgba(37,99,235,0.1)',
                        'mobile-drawer': '4px 0 24px rgba(0,0,0,0.15)',
                    }
                }
            }
        }
    </script>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body { font-family: 'Inter', sans-serif; background-color: #fafafa; }

        /* Scrollbar Minimalis */
        .custom-scrollbar::-webkit-scrollbar { width: 5px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
        .custom-scrollbar:hover::-webkit-scrollbar-thumb { background: #cbd5e1; }

        /* Sembunyikan Panah di Input Number */
        input[type=number]::-webkit-inner-spin-button, input[type=number]::-webkit-outer-spin-button { -webkit-appearance: none; margin: 0; }
        input[type=number] { -moz-appearance: textfield; }

        /* Custom Checkbox UI Dewa */
        .custom-checkbox { appearance: none; background-color: #fff; margin: 0; width: 1.25rem; height: 1.25rem; border: 2px solid #e2e8f0; border-radius: 0.375rem; display: grid; place-content: center; cursor: pointer; transition: all 0.2s ease-in-out; flex-shrink: 0; }
        .custom-checkbox::before { content: ""; width: 0.65rem; height: 0.65rem; transform: scale(0); transition: 120ms transform ease-in-out; background-color: white; transform-origin: center; clip-path: polygon(14% 44%, 0 65%, 50% 100%, 100% 16%, 80% 0%, 43% 62%); }
        .custom-checkbox:checked { background-color: #2563eb; border-color: #2563eb; }
        .custom-checkbox:checked::before { transform: scale(1); }

        /* Animasi Accordion Kategori */
        .accordion-content { max-height: 0; overflow: hidden; transition: max-height 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
        .accordion-content.open { max-height: 500px; } 

        /* BADGE TOKO */
        .badge-store { display: inline-flex; align-items: center; justify-content: center; padding: 2px 5px; border-radius: 4px; font-size: 0.65rem; margin-left: 4px;}
        .badge-official { background-color: #f3e8ff; color: #7e22ce; }
        .badge-pro { background-color: #d1fae5; color: #047857; }
        
        /* Pagination Override Laravel */
        .pagination-wrap nav { display: flex; justify-content: center; width: 100%; margin-top: 3rem; margin-bottom: 2rem; }
        .pagination-wrap .pagination { display: flex; gap: 0.5rem; background: white; padding: 0.5rem; border-radius: 1rem; box-shadow: 0 4px 20px rgba(0,0,0,0.03); border: 1px solid #e4e4e7; }
        .pagination-wrap .page-item .page-link { display: flex; align-items: center; justify-content: center; min-width: 2.5rem; height: 2.5rem; border-radius: 0.5rem; font-weight: 700; color: #52525b; padding: 0 0.75rem; transition: all 0.3s; }
        .pagination-wrap .page-item:not(.active) .page-link:hover { background: #f4f4f5; color: #000; }
        .pagination-wrap .page-item.active .page-link { background: #2563eb; color: white; box-shadow: 0 4px 15px rgba(37,99,235,0.4); }
    </style>
</head>
<body class="text-zinc-900 antialiased pt-[80px]">

    {{-- Navbar --}}
    @include('partials.navbar')

    {{-- BREADCRUMB --}}
    <div class="bg-white border-b border-zinc-100 hidden md:block">
        <div class="max-w-[1400px] mx-auto px-4 lg:px-8 py-4">
            <nav class="flex text-[10px] font-black uppercase tracking-[0.2em] text-zinc-400 items-center gap-3">
                <a href="{{ url('/') }}" class="hover:text-blue-600 transition-colors">Beranda</a>
                <i class="fas fa-chevron-right text-[8px] opacity-30"></i>
                <a href="{{ route('produk.index') }}" class="hover:text-blue-600 transition-colors">Katalog Material</a>
                <i class="fas fa-chevron-right text-[8px] opacity-30"></i>
                <span class="text-blue-600">Pencarian: "{{ request('query') }}"</span>
            </nav>
        </div>
    </div>

    {{-- KONTEN UTAMA --}}
    <div class="max-w-[1400px] mx-auto px-4 lg:px-8 py-8 flex flex-col lg:flex-row items-start gap-8">

        {{-- MOBILE OVERLAY --}}
        <div id="filter-overlay" class="fixed inset-0 bg-zinc-950/60 z-[60] hidden lg:hidden backdrop-blur-sm transition-opacity opacity-0"></div>

        {{-- SIDEBAR FILTER (KIRI) --}}
        <aside id="sidebar-filters" class="fixed inset-y-0 left-0 z-[70] w-[280px] bg-white transform -translate-x-full transition-transform duration-300 ease-in-out flex flex-col shadow-mobile-drawer lg:relative lg:translate-x-0 lg:w-[280px] lg:shadow-none lg:bg-transparent lg:z-0 lg:shrink-0 lg:sticky lg:top-28">

            {{-- Header Mobile Filter --}}
            <div class="flex items-center justify-between p-5 border-b border-zinc-100 lg:hidden bg-white shrink-0">
                <h3 class="text-sm font-black text-zinc-900 uppercase tracking-widest">Filter Pencarian</h3>
                <button type="button" id="close-filter-btn" class="w-8 h-8 flex items-center justify-center text-zinc-400 hover:text-red-500 bg-zinc-50 hover:bg-red-50 rounded-full transition-colors">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            {{-- Form Wrapper --}}
            <div class="w-full h-full lg:h-auto flex flex-col bg-white lg:rounded-[2rem] lg:border lg:border-zinc-100 lg:shadow-soft overflow-hidden">

                {{-- Header Desktop Filter --}}
                <div class="hidden lg:flex items-center justify-between px-6 py-5 border-b border-zinc-100 bg-zinc-50/50">
                    <h3 class="text-xs font-black text-zinc-900 uppercase tracking-widest flex items-center gap-2">
                        <i class="fas fa-sliders-h text-blue-600"></i> Filter
                    </h3>
                    @if(request()->except('page', 'sort'))
                        <a href="{{ route('search', ['query' => request('query')]) }}" class="text-[10px] font-bold text-red-500 hover:text-red-600 uppercase tracking-widest transition-colors">Reset</a>
                    @endif
                </div>

                {{-- Form Element --}}
                <form action="{{ route('search') }}" method="GET" id="filterForm" class="flex-1 overflow-y-auto custom-scrollbar p-6 flex flex-col gap-8 relative">
                    @if(request()->has('query'))
                        <input type="hidden" name="query" value="{{ request('query') }}">
                    @endif
                    @if(request()->has('sort'))
                        <input type="hidden" name="sort" value="{{ request('sort') }}">
                    @endif

                    {{-- FILTER: KATEGORI (ACCORDION) --}}
                    <div class="space-y-3" id="category-accordion">
                            {{-- 1. BAHAN BANGUNAN DASAR --}}
                            <div class="accordion-item border border-zinc-100 rounded-2xl bg-zinc-50/50 overflow-hidden">
                                <button type="button" class="accordion-header w-full px-4 py-3 flex items-center justify-between text-sm font-bold text-zinc-700 hover:text-blue-600 transition-colors">
                                    <span class="flex items-center gap-2"><i class="fas fa-warehouse text-zinc-300 text-xs w-4"></i> Bahan Bangunan Dasar</span>
                                    <i class="fas fa-chevron-down text-[10px] text-zinc-400 transition-transform duration-300 icon-arrow"></i>
                                </button>
                                <div class="accordion-content bg-white">
                                    <div class="px-4 pb-4 pt-1 space-y-3 border-t border-zinc-100 mt-1">
                                        @foreach(['Semen', 'Pasir', 'Batu'] as $sub)
                                            <label class="flex items-start gap-3 cursor-pointer group">
                                                <input type="checkbox" name="kategori_text[]" value="{{ $sub }}" class="custom-checkbox mt-0.5" {{ in_array($sub, request('kategori_text', [])) ? 'checked' : '' }} onchange="showApplyButton()">
                                                <span class="text-xs font-semibold text-zinc-600 group-hover:text-zinc-900 select-none">{{ $sub }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            {{-- 2. BESI & BAJA --}}
                            <div class="accordion-item border border-zinc-100 rounded-2xl bg-zinc-50/50 overflow-hidden">
                                <button type="button" class="accordion-header w-full px-4 py-3 flex items-center justify-between text-sm font-bold text-zinc-700 hover:text-blue-600 transition-colors">
                                    <span class="flex items-center gap-2"><i class="fas fa-bars text-zinc-300 text-xs w-4"></i> Besi & Baja</span>
                                    <i class="fas fa-chevron-down text-[10px] text-zinc-400 transition-transform duration-300 icon-arrow"></i>
                                </button>
                                <div class="accordion-content bg-white">
                                    <div class="px-4 pb-4 pt-1 space-y-3 border-t border-zinc-100 mt-1">
                                        @foreach(['Besi Beton', 'Baja Ringan'] as $sub)
                                            <label class="flex items-start gap-3 cursor-pointer group">
                                                <input type="checkbox" name="kategori_text[]" value="{{ $sub }}" class="custom-checkbox mt-0.5" {{ in_array($sub, request('kategori_text', [])) ? 'checked' : '' }} onchange="showApplyButton()">
                                                <span class="text-xs font-semibold text-zinc-600 group-hover:text-zinc-900 select-none">{{ $sub }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            {{-- 3. CAT & PELAPIS --}}
                            <div class="accordion-item border border-zinc-100 rounded-2xl bg-zinc-50/50 overflow-hidden">
                                <button type="button" class="accordion-header w-full px-4 py-3 flex items-center justify-between text-sm font-bold text-zinc-700 hover:text-blue-600 transition-colors">
                                    <span class="flex items-center gap-2"><i class="fas fa-paint-roller text-zinc-300 text-xs w-4"></i> Cat & Pelapis</span>
                                    <i class="fas fa-chevron-down text-[10px] text-zinc-400 transition-transform duration-300 icon-arrow"></i>
                                </button>
                                <div class="accordion-content bg-white">
                                    <div class="px-4 pb-4 pt-1 space-y-3 border-t border-zinc-100 mt-1">
                                        @foreach(['Cat Tembok', 'Cat Kayu & Besi'] as $sub)
                                            <label class="flex items-start gap-3 cursor-pointer group">
                                                <input type="checkbox" name="kategori_text[]" value="{{ $sub }}" class="custom-checkbox mt-0.5" {{ in_array($sub, request('kategori_text', [])) ? 'checked' : '' }} onchange="showApplyButton()">
                                                <span class="text-xs font-semibold text-zinc-600 group-hover:text-zinc-900 select-none">{{ $sub }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            {{-- 4. KERAMIK & GRANIT --}}
                            <div class="accordion-item border border-zinc-100 rounded-2xl bg-zinc-50/50 overflow-hidden">
                                <button type="button" class="accordion-header w-full px-4 py-3 flex items-center justify-between text-sm font-bold text-zinc-700 hover:text-blue-600 transition-colors">
                                    <span class="flex items-center gap-2"><i class="fas fa-th-large text-zinc-300 text-xs w-4"></i> Keramik & Granit</span>
                                    <i class="fas fa-chevron-down text-[10px] text-zinc-400 transition-transform duration-300 icon-arrow"></i>
                                </button>
                                <div class="accordion-content bg-white">
                                    <div class="px-4 pb-4 pt-1 space-y-3 border-t border-zinc-100 mt-1">
                                        @foreach(['Keramik Lantai', 'Keramik Dinding', 'Granit'] as $sub)
                                            <label class="flex items-start gap-3 cursor-pointer group">
                                                <input type="checkbox" name="kategori_text[]" value="{{ $sub }}" class="custom-checkbox mt-0.5" {{ in_array($sub, request('kategori_text', [])) ? 'checked' : '' }} onchange="showApplyButton()">
                                                <span class="text-xs font-semibold text-zinc-600 group-hover:text-zinc-900 select-none">{{ $sub }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            {{-- 5. PIPA & PERLENGKAPAN AIR --}}
                            <div class="accordion-item border border-zinc-100 rounded-2xl bg-zinc-50/50 overflow-hidden">
                                <button type="button" class="accordion-header w-full px-4 py-3 flex items-center justify-between text-sm font-bold text-zinc-700 hover:text-blue-600 transition-colors">
                                    <span class="flex items-center gap-2"><i class="fas fa-faucet text-zinc-300 text-xs w-4"></i> Pipa & Air</span>
                                    <i class="fas fa-chevron-down text-[10px] text-zinc-400 transition-transform duration-300 icon-arrow"></i>
                                </button>
                                <div class="accordion-content bg-white">
                                    <div class="px-4 pb-4 pt-1 space-y-3 border-t border-zinc-100 mt-1">
                                        @foreach(['Pipa PVC', 'Pipa Besi', 'Perlengkapan Sanitasi'] as $sub)
                                            <label class="flex items-start gap-3 cursor-pointer group">
                                                <input type="checkbox" name="kategori_text[]" value="{{ $sub }}" class="custom-checkbox mt-0.5" {{ in_array($sub, request('kategori_text', [])) ? 'checked' : '' }} onchange="showApplyButton()">
                                                <span class="text-xs font-semibold text-zinc-600 group-hover:text-zinc-900 select-none">{{ $sub }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            {{-- 6. KELISTRIKAN --}}
                            <div class="accordion-item border border-zinc-100 rounded-2xl bg-zinc-50/50 overflow-hidden">
                                <button type="button" class="accordion-header w-full px-4 py-3 flex items-center justify-between text-sm font-bold text-zinc-700 hover:text-blue-600 transition-colors">
                                    <span class="flex items-center gap-2"><i class="fas fa-bolt text-zinc-300 text-xs w-4"></i> Kelistrikan</span>
                                    <i class="fas fa-chevron-down text-[10px] text-zinc-400 transition-transform duration-300 icon-arrow"></i>
                                </button>
                                <div class="accordion-content bg-white">
                                    <div class="px-4 pb-4 pt-1 space-y-3 border-t border-zinc-100 mt-1">
                                        @foreach(['Kabel Listrik', 'Saklar & Stop Kontak', 'Lampu & Penerangan', 'Panel & Box MCB'] as $sub)
                                            <label class="flex items-start gap-3 cursor-pointer group">
                                                <input type="checkbox" name="kategori_text[]" value="{{ $sub }}" class="custom-checkbox mt-0.5" {{ in_array($sub, request('kategori_text', [])) ? 'checked' : '' }} onchange="showApplyButton()">
                                                <span class="text-xs font-semibold text-zinc-600 group-hover:text-zinc-900 select-none">{{ $sub }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                    </div>

                    {{-- FILTER: JENIS TOKO --}}
                    <div>
                        <h4 class="text-[10px] font-black text-zinc-400 uppercase tracking-[0.2em] mb-4">Jenis Mitra</h4>
                        <div class="space-y-3">
                            <label class="flex items-center gap-3 cursor-pointer group">
                                <input type="checkbox" name="tier_toko[]" value="official_store" class="custom-checkbox" {{ in_array('official_store', request('tier_toko', [])) ? 'checked' : '' }} onchange="showApplyButton()">
                                <span class="text-sm font-semibold text-zinc-600 group-hover:text-zinc-900 select-none flex items-center gap-2">
                                    <i class="fas fa-crown text-purple-500 w-4"></i> Official Store
                                </span>
                            </label>
                            <label class="flex items-center gap-3 cursor-pointer group">
                                <input type="checkbox" name="tier_toko[]" value="power_merchant" class="custom-checkbox" {{ in_array('power_merchant', request('tier_toko', [])) ? 'checked' : '' }} onchange="showApplyButton()">
                                <span class="text-sm font-semibold text-zinc-600 group-hover:text-zinc-900 select-none flex items-center gap-2">
                                    <i class="fas fa-check-circle text-emerald-500 w-4"></i> Power Merchant
                                </span>
                            </label>
                        </div>
                    </div>

                    {{-- FILTER: LOKASI --}}
                    <div>
                        <h4 class="text-[10px] font-black text-zinc-400 uppercase tracking-[0.2em] mb-4">Lokasi Pengiriman</h4>
                        <div class="relative">
                            <select name="lokasi" id="lokasi-select" onchange="document.getElementById('filterForm').submit()" class="w-full bg-zinc-50 border border-zinc-200 text-zinc-700 text-sm font-bold rounded-xl px-4 py-3 appearance-none outline-none focus:border-blue-600 transition-colors cursor-pointer">
                                <option value="">Seluruh Indonesia</option>
                                @if(isset($locations) && count($locations) > 0)
                                    @foreach($locations as $l)
                                        <option value="{{ $l->nama_kota ?? $l->city_name }}" {{ request('lokasi') == ($l->nama_kota ?? $l->city_name) ? 'selected' : '' }}>
                                            {{ $l->nama_kota ?? $l->city_name }}
                                        </option>
                                    @endforeach
                                @else
                                    {{-- Fallback jika search controller belum mempassing $locations --}}
                                    <option value="Subang" {{ request('lokasi') == 'Subang' ? 'selected' : '' }}>Subang</option>
                                    <option value="Bandung" {{ request('lokasi') == 'Bandung' ? 'selected' : '' }}>Bandung</option>
                                    <option value="Jakarta" {{ request('lokasi') == 'Jakarta' ? 'selected' : '' }}>Jakarta</option>
                                    <option value="Surabaya" {{ request('lokasi') == 'Surabaya' ? 'selected' : '' }}>Surabaya</option>
                                @endif
                            </select>
                            <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                                <i class="fas fa-chevron-down text-[10px] text-zinc-400"></i>
                            </div>
                        </div>
                    </div>

                    {{-- FILTER: HARGA --}}
                    <div>
                        <h4 class="text-[10px] font-black text-zinc-400 uppercase tracking-[0.2em] mb-4">Rentang Harga</h4>
                        <div class="flex items-center gap-2">
                            <div class="flex items-center bg-white border border-zinc-200 rounded-xl px-3 py-2.5 focus-within:border-blue-500 focus-within:ring-2 focus-within:ring-blue-500/10 flex-1 transition-all">
                                <span class="text-zinc-400 text-[10px] font-black mr-1">Rp</span>
                                <input type="number" name="harga_min" placeholder="Min" value="{{ request('harga_min') }}" class="w-full border-none outline-none text-xs font-bold text-zinc-900 p-0 bg-transparent" oninput="showApplyButton()">
                            </div>
                            <span class="text-zinc-300 font-black">-</span>
                            <div class="flex items-center bg-white border border-zinc-200 rounded-xl px-3 py-2.5 focus-within:border-blue-500 focus-within:ring-2 focus-within:ring-blue-500/10 flex-1 transition-all">
                                <span class="text-zinc-400 text-[10px] font-black mr-1">Rp</span>
                                <input type="number" name="harga_max" placeholder="Max" value="{{ request('harga_max') }}" class="w-full border-none outline-none text-xs font-bold text-zinc-900 p-0 bg-transparent" oninput="showApplyButton()">
                            </div>
                        </div>
                    </div>

                    {{-- Button Apply (Muncul dinamis) --}}
                    <div id="btn-apply-wrapper" class="hidden lg:sticky bottom-0 left-0 w-full bg-white pt-4 pb-2 z-10 mt-auto">
                        <button type="submit" class="w-full bg-zinc-950 hover:bg-blue-600 text-white font-black text-xs uppercase tracking-widest py-4 rounded-xl shadow-md transition-all hover:-translate-y-0.5">
                            Terapkan Filter
                        </button>
                    </div>

                    {{-- Button Apply Mobile --}}
                    <div class="lg:hidden mt-auto pt-6 pb-2">
                        <button type="submit" class="w-full bg-blue-600 text-white font-black py-4 rounded-xl shadow-lg">
                            Tampilkan Hasil
                        </button>
                    </div>
                </form>
            </div>
        </aside>

        {{-- ========================================================= --}}
        {{-- KANAN: KONTEN UTAMA (HASIL PRODUK) --}}
        {{-- ========================================================= --}}
        <main class="flex-1 w-full min-w-0">

            {{-- HEADER HASIL PENCARIAN (Sorting & Result Text) --}}
            <div class="bg-white rounded-[1.5rem] shadow-sm border border-zinc-100 p-4 mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4 relative z-10">
                <div class="text-sm font-medium text-zinc-500 px-2">
                    @if(request('query'))
                        Hasil pencarian: <span class="font-black text-blue-600">"{{ request('query') }}"</span> 
                        ({{ $products->total() ?? 0 }} produk)
                    @else
                        Menampilkan <span class="font-black text-zinc-900">{{ $products->count() ?? 0 }}</span> produk dari total <span class="font-black text-zinc-900">{{ $products->total() ?? 0 }}</span>
                    @endif
                </div>

                <div class="flex items-center gap-3 w-full sm:w-auto">
                    {{-- Button Filter HP --}}
                    <button id="mobile-filter-btn" class="lg:hidden flex-1 flex items-center justify-center gap-2 bg-zinc-50 border border-zinc-200 text-zinc-800 font-bold text-xs uppercase tracking-widest py-3 px-4 rounded-xl hover:bg-zinc-100 shadow-sm transition-colors">
                        <i class="fas fa-filter"></i> Filter
                    </button>

                    {{-- Dropdown Sort Standar Ikon --}}
                    <div class="flex-1 sm:flex-none relative">
                        <form action="{{ route('search') }}" method="GET" id="sort-form">
                            {{-- Bawa filter yang sudah ada --}}
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
                            <select name="sort" onchange="document.getElementById('sort-form').submit()" class="w-full bg-white border border-zinc-200 text-zinc-700 py-3 pl-11 pr-10 rounded-xl text-sm font-bold focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 cursor-pointer shadow-sm">
                                <option value="terbaru" {{ request('sort') == 'terbaru' ? 'selected' : '' }}>Paling Baru</option>
                                <option value="termurah" {{ request('sort') == 'termurah' ? 'selected' : '' }}>Harga Terendah</option>
                                <option value="termahal" {{ request('sort') == 'termahal' ? 'selected' : '' }}>Harga Tertinggi</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                                <i class="fas fa-chevron-down text-[10px] text-zinc-400"></i>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- GRID PRODUK (1:1 Sempurna) --}}
            @if($products->count() > 0)
            <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-4 sm:gap-6 relative z-0 mb-10">
                @foreach($products as $b)
                    @php
                        $img = !empty($b->gambar_utama) ? 'assets/uploads/products/'.$b->gambar_utama : 'assets/uploads/products/default.jpg';
                        
                        // Menentukan warna icon lokasi berdasarkan tier toko
                        $tier = $b->tier_toko ?? 'regular';
                        $locIconColor = $tier == 'official_store' ? 'text-purple-500' : ($tier == 'power_merchant' ? 'text-emerald-500' : 'text-blue-500');
                        
                        // PEMBERSIH BUG IDNP KOTA
                        $cityName = $b->kota_toko ?? 'Nasional';
                        if(str_starts_with($cityName, 'IDN')) {
                            $cityName = 'Lokasi Terverifikasi';
                        }
                    @endphp
                    <a href="{{ route('produk.detail', $b->slug) }}" class="bg-white rounded-[1.5rem] shadow-sm hover:shadow-hover transition-all duration-300 overflow-hidden flex flex-col group border border-zinc-100 hover:border-blue-200 hover:-translate-y-1 relative">

                        {{-- Gambar (Rasio 1:1) --}}
                        <div class="w-full pt-[100%] relative bg-zinc-50 border-b border-zinc-50 overflow-hidden">
                            <img src="{{ asset($img) }}" onerror="this.src='{{ asset('assets/uploads/products/default.jpg') }}'" class="absolute inset-0 w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-500 ease-out mix-blend-multiply p-2" alt="{{ $b->nama_barang }}">
                        </div>

                        {{-- Detail Informasi --}}
                        <div class="p-4 sm:p-5 flex flex-col flex-1">
                            <h3 class="text-xs sm:text-sm font-bold text-zinc-800 line-clamp-2 leading-snug mb-2 group-hover:text-blue-600 transition-colors">{{ $b->nama_barang }}</h3>

                            <div class="mt-auto">
                                <div class="text-base sm:text-lg font-black text-zinc-900 tracking-tight mb-2.5">Rp{{ number_format($b->harga, 0, ',', '.') }}</div>

                                <div class="pt-3 border-t border-zinc-100/80 space-y-2">
                                    {{-- INFO TOKO DAN BADGE --}}
                                    <div class="flex items-center gap-1.5 text-[10px] sm:text-xs font-semibold text-zinc-500">
                                        <i class="fas fa-store text-zinc-400 w-3.5"></i>
                                        <span class="truncate max-w-[120px]">{{ $b->nama_toko }}</span>

                                        {{-- LOGIKA BADGE TOKO --}}
                                        @if($tier == 'official_store')
                                            <span class="badge-store badge-official" title="Official Store"><i class="fas fa-crown"></i></span>
                                        @elseif($tier == 'power_merchant' || $tier == 'pro_merchant')
                                            <span class="badge-store badge-pro" title="Pro Merchant"><i class="fas fa-check-circle"></i></span>
                                        @endif
                                    </div>

                                    {{-- LOKASI DENGAN WARNA DINAMIS TIER TOKO --}}
                                    <div class="flex items-center gap-1.5 text-[10px] sm:text-xs font-semibold text-zinc-400">
                                        <i class="fas fa-map-marker-alt {{ $locIconColor }} w-3.5"></i>
                                        <span class="truncate">{{ $cityName }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
            
            {{-- PAGINASI --}}
            <div class="pagination-wrap">
                @if(isset($products) && $products->hasPages())
                    {{ $products->appends(request()->query())->links('pagination::tailwind') }}
                @endif
            </div>

            @else
            {{-- EMPTY STATE JIKA PENCARIAN KOSONG --}}
            <div class="col-span-full flex flex-col items-center justify-center py-24 bg-white rounded-[2rem] border border-zinc-100 shadow-sm mt-2">
                <div class="w-24 h-24 bg-zinc-50 rounded-[2rem] flex items-center justify-center text-4xl text-zinc-300 mb-6">
                    <i class="fas fa-search-minus"></i>
                </div>
                <h3 class="text-xl sm:text-2xl font-black text-zinc-900 mb-2">Oops, barang tidak ditemukan</h3>
                <p class="text-zinc-500 text-sm font-medium text-center max-w-sm mb-8 px-4">Coba gunakan kata kunci yang lebih umum, ejaan yang berbeda, atau hilangkan filter yang ada.</p>
                <a href="{{ route('produk.index') }}" class="bg-zinc-950 hover:bg-blue-600 text-white font-black py-3.5 px-8 rounded-xl transition-all shadow-md">
                    Lihat Semua Material
                </a>
            </div>
            @endif

        </main>
    </div>

    {{-- Partial Includes --}}
    @include('partials.chat')
    @include('partials.footer')
    
    <script src="{{ asset('assets/js/navbar.js') }}"></script>

    {{-- LOGIKA INTERAKSI & AUTO DETECT HYPERLOCAL --}}
    <script>
        // 1. Munculkan Tombol "Terapkan" saat ada interaksi filter
        function showApplyButton() {
            const btnWrapper = document.getElementById('btn-apply-wrapper');
            if(btnWrapper) {
                btnWrapper.classList.remove('hidden');
                btnWrapper.classList.add('block');
            }
        }

        document.addEventListener('DOMContentLoaded', function() {

            // ==========================================
            // A. MOBILE SIDEBAR LOGIC
            // ==========================================
            const mobileFilterBtn = document.getElementById('mobile-filter-btn');
            const sidebarFilters = document.getElementById('sidebar-filters');
            const closeFilterBtn = document.getElementById('close-filter-btn');
            const filterOverlay = document.getElementById('filter-overlay');

            function openFilter() {
                sidebarFilters.classList.remove('-translate-x-full');
                filterOverlay.classList.remove('hidden');
                setTimeout(() => filterOverlay.classList.remove('opacity-0'), 10);
                document.body.style.overflow = 'hidden';
            }

            function closeFilter() {
                sidebarFilters.classList.add('-translate-x-full');
                filterOverlay.classList.add('opacity-0');
                setTimeout(() => filterOverlay.classList.add('hidden'), 300);
                document.body.style.overflow = '';
            }

            if (mobileFilterBtn) mobileFilterBtn.addEventListener('click', openFilter);
            if (closeFilterBtn) closeFilterBtn.addEventListener('click', closeFilter);
            if (filterOverlay) filterOverlay.addEventListener('click', closeFilter);


            // ==========================================
            // B. KATEGORI ACCORDION LOGIC
            // ==========================================
            const accordionHeaders = document.querySelectorAll('.accordion-header');
            
            // Buka otomatis jika ada checkbox yang tercentang di dalamnya
            document.querySelectorAll('.accordion-item').forEach(item => {
                if (item.querySelector('input[type="checkbox"]:checked')) {
                    item.querySelector('.accordion-content').classList.add('open');
                    item.querySelector('.icon-arrow').classList.add('rotate-180');
                }
            });

            // Toggle klik
            accordionHeaders.forEach(header => {
                header.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    const currentContent = this.nextElementSibling;
                    const currentIcon = this.querySelector('.icon-arrow');
                    const isOpen = currentContent.classList.contains('open');

                    if (!isOpen) {
                        currentContent.classList.add('open');
                        currentIcon.classList.add('rotate-180');
                    } else {
                        currentContent.classList.remove('open');
                        currentIcon.classList.remove('rotate-180');
                    }
                });
            });

            // ==========================================
            // C. AUTO DETECT LOCATION (HYPERLOCAL)
            // ==========================================
            const urlParams = new URLSearchParams(window.location.search);

            // Cek apakah parameter '?lokasi' ada di URL. 
            if (!urlParams.has('lokasi') && !sessionStorage.getItem('auto_lokasi_search_done')) {
                
                sessionStorage.setItem('auto_lokasi_search_done', '1');

                // Gunakan IP API untuk melacak kota user saat ini secara diam-diam
                fetch('https://ipapi.co/json/')
                    .then(res => res.ok ? res.json() : fetch('http://ip-api.com/json').then(r => r.json()))
                    .then(data => {
                        let detectedCity = (data.city || "").toLowerCase();
                        
                        let cleanCity = detectedCity.replace(/kabupaten|kota|kab\./g, '').trim();

                        if (cleanCity) {
                            let selectLokasi = document.getElementById('lokasi-select');
                            let options = selectLokasi.options;
                            let matchFound = false;

                            for (let i = 0; i < options.length; i++) {
                                let optionText = options[i].text.toLowerCase().replace(/kabupaten|kota|kab\./g, '').trim();

                                if (optionText === cleanCity || optionText.includes(cleanCity) || cleanCity.includes(optionText)) {
                                    selectLokasi.selectedIndex = i;
                                    matchFound = true;
                                    break;
                                }
                            }

                            if (!matchFound) {
                                let formattedCity = cleanCity.charAt(0).toUpperCase() + cleanCity.slice(1);
                                let newOption = new Option(formattedCity, formattedCity, true, true);
                                selectLokasi.add(newOption);
                            }

                            // Submit otomatis ke kota tersebut!
                            document.getElementById('filterForm').submit();
                        }
                    })
                    .catch(error => console.log('Sistem gagal melacak lokasi otomatis.', error));
            }

        });

        // Logika untuk menampilkan dan menyembunyikan sisa kategori
        function toggleCategories() {
            const hiddenDiv = document.getElementById('hidden-categories');
            const btnToggle = document.getElementById('btn-toggle-cat');

            if (hiddenDiv.style.display === 'none') {
                hiddenDiv.style.display = 'block';
                btnToggle.innerHTML = '<span>Sembunyikan</span> <i class="fas fa-chevron-up"></i>';
            } else {
                hiddenDiv.style.display = 'none';
                btnToggle.innerHTML = '<span>Lihat Selengkapnya</span> <i class="fas fa-chevron-down"></i>';
            }
        }
    </script>
</body>
</html>