<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Direktori Mitra & Peta Geospasial - Pondasikita</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

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
                        'card': '0 4px 20px -2px rgba(0,0,0,0.05)',
                        'card-hover': '0 20px 40px -4px rgba(37,99,235,0.15)',
                        'floating': '0 15px 40px -5px rgba(0,0,0,0.2)',
                        'map-popup': '0 10px 30px -5px rgba(0,0,0,0.3)',
                    },
                    animation: {
                        'radar': 'radar 2s linear infinite',
                        'float': 'float 3s ease-in-out infinite',
                    },
                    keyframes: {
                        'radar': { '0%': { transform: 'rotate(0deg)' }, '100%': { transform: 'rotate(360deg)' } },
                        'float': { '0%, 100%': { transform: 'translateY(0)' }, '50%': { transform: 'translateY(-5px)' } }
                    }
                }
            }
        }
    </script>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    {{-- LEAFLET CSS --}}
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>

    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f4f4f5; }
        
        select.custom-select { -webkit-appearance: none; -moz-appearance: none; appearance: none; }

        /* Pagination Setup */
        .pagination-wrap nav { display: flex; justify-content: center; width: 100%; margin-top: 3rem; margin-bottom: 2rem; }
        .pagination-wrap .pagination { display: flex; gap: 0.5rem; background: white; padding: 0.5rem; border-radius: 1.5rem; box-shadow: 0 4px 20px rgba(0,0,0,0.03); border: 1px solid #f4f4f5; }
        .pagination-wrap .page-item .page-link { display: flex; align-items: center; justify-content: center; min-width: 2.5rem; height: 2.5rem; border-radius: 1rem; font-weight: 800; color: #71717a; padding: 0 0.75rem; transition: all 0.3s; }
        .pagination-wrap .page-item:not(.active) .page-link:hover { background: #f4f4f5; color: #18181b; }
        .pagination-wrap .page-item.active .page-link { background: #18181b; color: white; box-shadow: 0 4px 15px rgba(0,0,0,0.2); }

        /* BADGE TOKO */
        .badge-store { display: inline-flex; align-items: center; justify-content: center; padding: 3px 8px; border-radius: 6px; font-size: 0.65rem; font-weight: 800; letter-spacing: 0.05em; text-transform: uppercase; white-space: nowrap; flex-shrink: 0;}
        .badge-official { background-color: #f3e8ff; color: #7e22ce; border: 1px solid #e9d5ff; }
        .badge-pro { background-color: #d1fae5; color: #047857; border: 1px solid #a7f3d0; }

        /* ========================================================
           ✨ CSS LEAFLET MAP & POPUP TINGKAT DEWA ✨
           ======================================================== */
        #store-map-wrapper { height: calc(100vh - 80px); max-height: 75vh; min-height: 480px; position: relative; width: 100%; z-index: 10;}
        #store-map { position: absolute; inset: 0; z-index: 10; }
        
        .leaflet-control-zoom { border: none !important; box-shadow: 0 10px 25px rgba(0,0,0,0.15) !important; border-radius: 12px !important; overflow: hidden; margin-right: 15px !important; margin-bottom: 25px !important;}
        .leaflet-control-zoom a { background-color: rgba(255,255,255,0.95) !important; backdrop-filter: blur(10px); color: #18181b !important; border-bottom: 1px solid #e4e4e7 !important; transition: all 0.2s; width: 40px !important; height: 40px !important; line-height: 40px !important; font-weight: bold; }
        .leaflet-control-zoom a:hover { background-color: #2563eb !important; color: white !important; }
        .leaflet-control-attribution { display: none !important; }
        
        .leaflet-popup-content-wrapper { background: transparent !important; box-shadow: none !important; padding: 0 !important; border-radius: 1.5rem !important;}
        .leaflet-popup-content { margin: 0 !important; width: auto !important; }
        .leaflet-popup-tip-container { display: none !important; } 
        .leaflet-container a.leaflet-popup-close-button { color: #fff !important; text-shadow: 0 2px 4px rgba(0,0,0,0.8) !important; top: 12px !important; right: 12px !important; z-index: 50 !important; background: rgba(0,0,0,0.3); border-radius: 50%; width: 26px !important; height: 26px !important; display: flex !important; align-items: center; justify-content: center; transition: all 0.3s ease;}
        .leaflet-container a.leaflet-popup-close-button:hover { background: #ef4444 !important; }

        .pin-bounce { transform-origin: bottom center; transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
        .pin-bounce:hover { transform: scale(1.15) translateY(-5px); z-index: 1000 !important; }
    </style>
</head>
<body class="text-zinc-800 antialiased pt-[80px]">

    @include('partials.navbar')

    {{-- ========================================================
         MAP SECTION (LAYAR PENUH, SEAMLESS, GOOGLE MAPS STYLE)
         ======================================================== --}}
    <div id="store-map-wrapper" class="border-b border-zinc-200">
        
        {{-- Lapisan Peta Leaflet Base --}}
        <div id="store-map"></div>
        
        {{-- Vignette Shadow Tepi Peta agar estetik --}}
        <div class="absolute inset-0 pointer-events-none shadow-[inset_0_0_30px_rgba(0,0,0,0.05)] z-[20]"></div>

        {{-- FLOATING WIDGET PANEL (Pojok Kiri Atas - Ramping & Tidak Menghalangi Peta) --}}
        <div class="absolute top-4 left-4 right-4 md:top-6 md:left-6 md:right-auto md:w-[360px] z-[400] flex flex-col gap-2 md:gap-3 pointer-events-none">
            
            {{-- Bagian Atas: Info Hitam & Biru (Sembunyikan di Mobile agar Peta Luas) --}}
            <div class="hidden md:block pointer-events-auto bg-zinc-950/95 backdrop-blur-xl border border-zinc-800 rounded-[1.5rem] p-5 sm:p-6 shadow-2xl transition-transform hover:-translate-y-1 duration-300">
                <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-blue-500/20 border border-blue-500/30 text-blue-400 text-[9px] font-black tracking-widest uppercase mb-3 shadow-sm">
                    <span class="w-1.5 h-1.5 rounded-full bg-blue-500 animate-pulse"></span> 
                    @if($lat ?? false) Satelit Terkunci @else Radar Geospasial @endif
                </div>
                <h1 class="text-2xl sm:text-3xl font-black text-white tracking-tight mb-2 leading-tight drop-shadow-md">
                    Peta Jaringan<br><span class="text-blue-500">Pondasikita</span>
                </h1>
                <p class="text-zinc-400 text-xs font-medium leading-relaxed">
                    Lacak dan temukan ribuan distributor material terdekat dari lokasi Anda secara real-time.
                </p>
            </div>

            {{-- Bagian Bawah: Form Putih (Dibuat Kompak di Mobile) --}}
            <div class="pointer-events-auto bg-white/95 backdrop-blur-xl border border-white rounded-[1.25rem] md:rounded-[1.5rem] p-3 md:p-5 shadow-2xl flex flex-col gap-2 md:gap-3 transition-transform hover:-translate-y-1 duration-300">
                
                <form action="{{ route('toko.index') }}" method="GET" id="filterForm" class="flex flex-col gap-2 md:gap-3">
                    
                    {{-- Dropdown Lokasi (Row 1) --}}
                    @if(!($lat ?? false))
                    <div class="relative w-full">
                        <select name="lokasi" id="lokasi-select" class="custom-select w-full bg-white border border-zinc-200 hover:border-blue-300 text-zinc-800 text-xs font-bold rounded-[1rem] focus:border-blue-600 focus:ring-4 focus:ring-blue-600/10 block pl-4 pr-10 h-11 md:h-12 transition-all outline-none cursor-pointer shadow-sm">
                            <option value="semua">Nasional (Semua Area)</option>
                            @foreach($locations as $lokasi)
                                <option value="{{ $lokasi->city_id }}" {{ ($filter_lokasi ?? '') == $lokasi->city_id ? 'selected' : '' }}>
                                    {{ $lokasi->city_name }}
                                </option>
                            @endforeach
                        </select>
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <div class="w-6 h-6 rounded-full bg-zinc-100 flex items-center justify-center"><i class="fas fa-chevron-down text-zinc-500 text-[10px]"></i></div>
                        </div>
                    </div>
                    @endif

                    {{-- Tombol GPS & Cari (Row 2 - Bersebelahan di Mobile) --}}
                    <div class="flex gap-2">
                        @if(!($lat ?? false))
                            <button onclick="requestGPS()" type="button" class="w-12 h-11 md:w-full md:h-12 shrink-0 rounded-[1rem] flex items-center justify-center gap-2 text-sm font-bold transition-all duration-300 relative bg-zinc-50 hover:bg-blue-50 text-blue-600 border border-zinc-200 hover:border-blue-200" title="Gunakan Lokasi Saya">
                                <i class="fas fa-crosshairs"></i>
                                <span class="hidden md:inline">Gunakan Lokasi Saya</span>
                            </button>

                            <button type="submit" class="flex-1 bg-zinc-950 hover:bg-blue-600 text-white h-11 md:h-12 rounded-[1rem] font-black transition-colors shadow-lg text-xs uppercase tracking-wider">
                                Cari Toko
                            </button>
                        @else
                            <button onclick="requestGPS()" type="button" class="w-12 h-11 md:w-full md:h-12 shrink-0 rounded-[1rem] flex items-center justify-center gap-2 text-sm font-bold transition-all duration-300 relative bg-blue-600 text-white shadow-lg shadow-blue-600/30 border border-blue-500" title="GPS Sedang Aktif">
                                <span class="absolute inset-0 rounded-[1rem] border border-blue-400 animate-ping opacity-50"></span>
                                <i class="fas fa-crosshairs animate-spin-slow text-blue-200"></i>
                                <span class="hidden md:inline">GPS Sedang Aktif</span>
                            </button>

                            <a href="{{ route('toko.index') }}" class="flex-1 bg-red-50 text-red-600 border border-red-200 hover:bg-red-600 hover:text-white h-11 md:h-12 rounded-[1rem] font-black transition-all flex items-center justify-center gap-2 text-xs uppercase tracking-wider shadow-sm">
                                <i class="fas fa-times"></i> <span class="hidden md:inline">Matikan GPS</span><span class="md:hidden">Reset</span>
                            </a>
                        @endif
                    </div>
                </form>

            </div>
        </div>

        {{-- Loader Lokasi --}}
        <div id="gps-loader" class="absolute inset-0 bg-zinc-950/80 backdrop-blur-md flex flex-col items-center justify-center z-[500] hidden transition-opacity duration-500">
            <div class="relative w-28 h-28 flex items-center justify-center mb-6">
                <div class="absolute inset-0 border border-blue-500/30 rounded-full"></div>
                <div class="absolute inset-4 border border-blue-500/20 rounded-full"></div>
                <div class="absolute inset-8 border border-blue-500/10 rounded-full"></div>
                <div class="absolute inset-0 rounded-full border-t-2 border-r-2 border-blue-500 animate-radar"></div>
                <div class="w-4 h-4 bg-blue-500 rounded-full shadow-[0_0_15px_#3b82f6]"></div>
            </div>
            <h3 class="text-xl sm:text-2xl font-black text-white tracking-tight drop-shadow-md">Melacak Titik Koordinat...</h3>
            <p class="text-xs sm:text-sm font-medium text-blue-300 mt-2">Menyelaraskan satelit GPS. Mohon izinkan akses lokasi.</p>
        </div>
    </div>


    {{-- ========================================================
         MAIN CONTENT GRID TOKO (TIDAK DIUBAH SAMA SEKALI)
         ======================================================== --}}
    <div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8 py-10 relative z-30">

        {{-- Info Count & Header List --}}
        <div class="mb-8 flex items-end justify-between border-b border-zinc-200 pb-4">
            <div>
                <h2 class="text-[10px] sm:text-xs font-black tracking-[0.2em] text-blue-600 uppercase mb-1">Eksplorasi Katalog</h2>
                <h3 class="text-2xl sm:text-3xl font-black text-zinc-900 tracking-tight">Direktori Mitra Toko</h3>
            </div>
            <span class="text-xs sm:text-sm font-black text-zinc-600 bg-white border border-zinc-200 shadow-sm px-4 py-2 rounded-xl">
                {{ $stores->total() }} Ditemukan
            </span>
        </div>

        {{-- DAFTAR TOKO GRID --}}
        <div id="store-grid" class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3 sm:gap-8">

            @forelse($stores as $toko)
                @php
                    $words = explode(" ", $toko->nama_toko);
                    $acronym = "";
                    foreach ($words as $w) { $acronym .= mb_substr($w, 0, 1); }
                    $storeInitials = strtoupper(substr($acronym, 0, 2)) ?: "TK";
                    $colors = ['#18181b', '#27272a', '#3f3f46', '#2563eb', '#1d4ed8'];
                    $storeColor = $colors[crc32($toko->nama_toko) % count($colors)];

                    $bannerPath = 'assets/uploads/banners/' . ($toko->banner_toko ?? '');
                    $hasBanner = !empty($toko->banner_toko) && file_exists(public_path($bannerPath));
                    $bannerStyle = $hasBanner ? "background-image: url('".asset($bannerPath)."');" : "background-color: $storeColor;";

                    $logoPath = 'assets/uploads/logos/' . ($toko->logo_toko ?? '');
                    $hasLogo = !empty($toko->logo_toko) && file_exists(public_path($logoPath));

                    $tier = $toko->tier_toko ?? 'regular';
                    if ($tier == 'official_store') {
                        $badgeBg = "bg-[#7e22ce]"; 
                        $badgeText = "OFFICIAL";
                        $miniIconColor = "text-[#7e22ce]";
                        $miniIconBg = "bg-purple-100";
                        $cardBorder = "hover:border-[#7e22ce]/50 hover:shadow-[0_20px_40px_-5px_rgba(126,34,206,0.15)]";
                    } elseif ($tier == 'power_merchant' || $tier == 'pro_merchant') {
                        $badgeBg = "bg-emerald-600";
                        $badgeText = "PRO MERCHANT";
                        $miniIconColor = "text-emerald-600";
                        $miniIconBg = "bg-emerald-100";
                        $cardBorder = "hover:border-emerald-500/50 hover:shadow-[0_20px_40px_-5px_rgba(16,185,129,0.15)]";
                    } else {
                        $badgeBg = "bg-zinc-800";
                        $badgeText = "VERIFIED";
                        $miniIconColor = "text-blue-600";
                        $miniIconBg = "bg-blue-100";
                        $cardBorder = "hover:border-blue-500/50 hover:shadow-card-hover";
                    }

                    // Pembersihan Nama Kota dari IDNP
                    $cityName = $toko->city_name ?? 'Nasional';
                    if(str_starts_with($cityName, 'IDN')) {
                        $cityName = 'Lokasi Terverifikasi';
                    }
                @endphp

                <a href="{{ route('toko.detail', ['slug' => $toko->slug]) }}"
                   class="group relative bg-white rounded-2xl sm:rounded-[2rem] shadow-[0_8px_30px_rgb(0,0,0,0.04)] overflow-hidden transition-all duration-500 hover:-translate-y-2 border border-zinc-100/50 flex flex-col w-full {{ $cardBorder }}">

                    {{-- 1. Banner Area --}}
                    <div class="h-20 sm:h-36 bg-cover bg-center relative transition-transform duration-700 group-hover:scale-105" style="{{ $bannerStyle }}">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>

                        {{-- Badge Tier Kanan Atas --}}
                        <div class="absolute top-2 right-2 sm:top-4 sm:right-4 {{ $badgeBg }} text-white px-2 py-0.5 sm:px-3 sm:py-1 rounded-full text-[7px] sm:text-[9px] font-black uppercase tracking-widest shadow-md">
                            {{ $badgeText }}
                        </div>

                        {{-- Jika GPS Aktif, tampilkan badge jarak kiri atas --}}
                        @if(isset($toko->jarak_km))
                            <div class="absolute top-2 left-2 sm:top-4 sm:left-4 bg-blue-600/90 backdrop-blur text-white px-2 py-0.5 sm:px-3 sm:py-1 rounded-full text-[7px] sm:text-[9px] font-black uppercase tracking-widest shadow-md flex items-center gap-1 sm:gap-1.5 border border-blue-400">
                                <i class="fas fa-location-arrow animate-pulse text-blue-200"></i> {{ number_format($toko->jarak_km, 1) }} KM
                            </div>
                        @endif
                    </div>

                    {{-- 2. Body Area --}}
                    <div class="px-3 pb-3 sm:px-6 sm:pb-6 flex-1 flex flex-col relative bg-white">
                        
                        {{-- Avatar Overlapping Banner --}}
                        <div class="absolute -top-8 sm:-top-10 left-3 sm:left-6">
                            <div class="relative inline-block">
                                @if($hasLogo)
                                    <img src="{{ asset($logoPath) }}" alt="{{ $toko->nama_toko }}" class="w-16 h-16 sm:w-20 sm:h-20 rounded-xl sm:rounded-2xl object-cover border-[3px] sm:border-4 border-white shadow-md bg-white transition-transform duration-500 group-hover:scale-105 group-hover:-rotate-3">
                                @else
                                    <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-xl sm:rounded-2xl border-[3px] sm:border-4 border-white shadow-md flex items-center justify-center font-black text-xl sm:text-2xl text-white transition-transform duration-500 group-hover:scale-105 group-hover:-rotate-3" style="background-color: {{ $storeColor }};">
                                        {{ $storeInitials }}
                                    </div>
                                @endif

                                {{-- Ikon Toko Mungil di sudut Kanan Bawah Avatar --}}
                                <div class="absolute -bottom-1 -right-1 sm:-bottom-2 sm:-right-2 w-5 h-5 sm:w-7 sm:h-7 rounded sm:rounded-lg border-2 sm:border-[3px] border-white flex items-center justify-center text-[8px] sm:text-[10px] shadow-sm {{ $miniIconBg }} {{ $miniIconColor }}">
                                    <i class="fas fa-store"></i>
                                </div>
                            </div>
                        </div>

                        {{-- Judul dan Lokasi --}}
                        <div class="mt-10 sm:mt-14 space-y-1 sm:space-y-1.5">
                            <h4 class="font-black text-[13px] sm:text-xl text-zinc-900 group-hover:text-blue-600 transition-colors line-clamp-1 leading-tight">
                                {{ $toko->nama_toko }}
                            </h4>
                            <p class="text-zinc-500 text-[9px] sm:text-[11px] font-bold uppercase tracking-wider flex items-center gap-1 sm:gap-1.5">
                                <i class="fas fa-map-marker-alt {{ $tier == 'official_store' ? 'text-purple-500' : 'text-blue-500' }}"></i>
                                <span class="truncate">{{ $cityName }}</span>
                            </p>
                        </div>

                        <div class="flex-1"></div>

                        {{-- 3. Footer Area --}}
                        <div class="mt-3 sm:mt-6 pt-2.5 sm:pt-5 border-t border-zinc-100 flex items-end justify-between">
                            <div class="flex flex-col">
                                <span class="text-[8px] sm:text-[9px] font-black text-zinc-400 uppercase tracking-widest leading-none mb-0.5 sm:mb-1">Koleksi</span>
                                <span class="text-[11px] sm:text-sm font-black text-zinc-800">{{ number_format($toko->jumlah_produk) }} Produk</span>
                            </div>
                            <div class="w-7 h-7 sm:w-10 sm:h-10 rounded-lg sm:rounded-2xl bg-zinc-50 flex items-center justify-center text-zinc-400 transition-all duration-300 group-hover:bg-blue-600 group-hover:text-white group-hover:shadow-[0_4px_15px_rgba(37,99,235,0.4)]">
                                <i class="fas fa-arrow-right text-[10px] sm:text-base -rotate-45 group-hover:rotate-0 transition-transform"></i>
                            </div>
                        </div>

                    </div>
                </a>

            @empty
                {{-- EMPTY STATE --}}
                <div class="col-span-full flex flex-col items-center justify-center py-20 sm:py-24 bg-white rounded-[2rem] border border-dashed border-zinc-300 shadow-sm">
                    <div class="w-24 h-24 bg-zinc-50 rounded-3xl flex items-center justify-center mb-6 shadow-inner">
                        <i class="fas fa-store-slash text-4xl text-zinc-300"></i>
                    </div>
                    <h3 class="text-2xl font-black text-zinc-900 mb-2">Tidak Ada Mitra Ditemukan</h3>
                    <p class="text-zinc-500 font-medium text-center max-w-sm mb-8 text-sm">Belum ada toko yang terdaftar di lokasi tersebut atau sistem tidak menemukan data.</p>
                    @if(($filter_lokasi ?? 'semua') !== 'semua' || ($lat ?? false))
                        <a href="{{ route('toko.index') }}" class="bg-zinc-900 hover:bg-blue-600 text-white font-bold py-3.5 px-8 rounded-2xl transition-all shadow-lg flex items-center gap-2 text-sm">
                            <i class="fas fa-globe-asia"></i> Kembali ke Pencarian Nasional
                        </a>
                    @endif
                </div>
            @endforelse

        </div>

        {{-- LOAD MORE (ESTETIK) --}}
        <div class="pagination-wrap mt-8 mb-12 text-center w-full flex justify-center">
            @if(isset($stores) && $stores->hasPages())
                @if($stores->hasMorePages())
                    <button id="load-more-btn" data-next-url="{{ $stores->nextPageUrl() }}" class="group relative overflow-hidden bg-zinc-950 hover:bg-zinc-900 text-white font-black text-xs sm:text-sm uppercase tracking-widest py-4 px-8 sm:px-10 rounded-[1.5rem] shadow-[0_10px_40px_rgba(0,0,0,0.15)] transition-all duration-300 active:scale-95 flex items-center justify-center gap-3 w-full sm:w-auto">
                        <span class="flex items-center gap-2">
                            Tampilkan Lebih Banyak <i class="fas fa-chevron-down text-blue-400 group-hover:translate-y-1 transition-transform"></i>
                        </span>
                    </button>
                @endif
            @endif
        </div>

    </div>

    @include('partials.chat')
    @include('partials.footer')
    
    <script src="{{ asset('assets/js/navbar.js') }}"></script>

    {{-- SCRIPT LEAFLET & HYPERLOCAL --}}
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            
            // ==========================================
            // 1. INISIALISASI PETA LEAFLET
            // ==========================================
            const activeLat = {{ $lat ?? 'null' }};
            const activeLng = {{ $lng ?? 'null' }};
            
            // Default View
            const defaultLat = activeLat ? activeLat : -6.5593;
            const defaultLng = activeLng ? activeLng : 107.7656;
            const zoomLevel = activeLat ? 11 : 7; 

            const map = L.map('store-map', { 
                scrollWheelZoom: false,
                zoomControl: false // Dimatikan lalu ditambah custom di Kanan Bawah
            }).setView([defaultLat, defaultLng], zoomLevel);

            // Zoom Control dipindah ke pojok Kanan Bawah agar widget kiri atas bebas
            L.control.zoom({ position: 'bottomright' }).addTo(map);

            // Tile Premium CartoDB
            L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
                attribution: '', subdomains: 'abcd', maxZoom: 20
            }).addTo(map);

            // ==========================================
            // 2. MARKER USER (PULSE BIRU)
            // ==========================================
            if (activeLat && activeLng) {
                const userIcon = L.divIcon({
                    html: `
                        <div class="relative flex items-center justify-center w-6 h-6">
                            <span class="absolute inline-flex h-12 w-12 rounded-full bg-blue-500 opacity-40 animate-ping"></span>
                            <span class="relative inline-flex rounded-full h-5 w-5 bg-blue-600 border-2 border-white shadow-[0_0_15px_#2563eb]"></span>
                        </div>
                    `,
                    className: '', iconSize: [24, 24], iconAnchor: [12, 12]
                });
                
                L.marker([activeLat, activeLng], {icon: userIcon})
                 .addTo(map)
                 .bindPopup('<div class="font-black text-blue-600 bg-white/90 backdrop-blur rounded-lg shadow-sm border border-blue-100 text-center text-[10px] uppercase tracking-widest px-3 py-1.5 mt-1">Titik Proyek Anda</div>', { closeButton: false })
                 .openPopup();
            }

            // ==========================================
            // 3. MARKER SEMUA TOKO (FLOATING POPUP)
            // ==========================================
            const storesData = @json($allMapStores ?? []);
            
            storesData.forEach(store => {
                if(store.latitude && store.longitude) {
                    
                    const storeIcon = L.divIcon({
                        html: `
                            <div class="relative pin-bounce group cursor-pointer">
                                <div class="w-10 h-10 bg-white rounded-full flex items-center justify-center border-4 border-zinc-900 shadow-[0_15px_20px_rgba(0,0,0,0.3)] overflow-hidden transition-all group-hover:border-blue-600 group-hover:scale-110">
                                    <i class="fas fa-store text-zinc-900 text-sm group-hover:text-blue-600"></i>
                                </div>
                                <div class="w-3 h-3 bg-zinc-900 rotate-45 absolute -bottom-1.5 left-1/2 transform -translate-x-1/2 group-hover:bg-blue-600"></div>
                            </div>
                        `,
                        className: '', iconSize: [40, 48], iconAnchor: [20, 48], popupAnchor: [0, -50]
                    });

                    let cityStr = store.city_name || 'Nasional';
                    if(cityStr.startsWith('IDN')) cityStr = 'Lokasi Terverifikasi';

                    const slugUrl = `{{ url('pages/toko') }}?slug=${store.slug}`;
                    const imgUrl = store.banner_toko ? `{{ asset('assets/uploads/banners/') }}/${store.banner_toko}` : `https://images.unsplash.com/photo-1541888086925-920a0b3efb98?w=500&q=80`;
                    
                    const badgeHtml = store.tier_toko === 'official_store' 
                        ? `<div class="absolute top-3 right-3 bg-[#7e22ce] text-white text-[9px] font-black px-2 py-0.5 rounded-full shadow-lg border border-[#a855f7]">OFFICIAL</div>` 
                        : (store.tier_toko === 'power_merchant' 
                            ? `<div class="absolute top-3 right-3 bg-emerald-600 text-white text-[9px] font-black px-2 py-0.5 rounded-full shadow-lg border border-emerald-400">PRO MERCHANT</div>`
                            : `<div class="absolute top-3 right-3 bg-zinc-800 text-white text-[9px] font-black px-2 py-0.5 rounded-full shadow-lg border border-zinc-600">VERIFIED</div>`);

                    const popupHTML = `
                        <div class="w-[260px] flex flex-col rounded-[1.5rem] overflow-hidden bg-white/95 backdrop-blur-xl border border-white shadow-map-popup">
                            <div class="h-28 bg-cover bg-center relative" style="background-image: url('${imgUrl}')">
                                <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/20 to-transparent"></div>
                                ${badgeHtml}
                                <div class="absolute bottom-3 left-4 right-4">
                                    <h4 class="font-black text-white text-base line-clamp-1 leading-tight mb-0.5 drop-shadow-md">${store.nama_toko}</h4>
                                    <p class="text-[10px] text-zinc-300 font-bold flex items-center gap-1"><i class="fas fa-map-marker-alt text-red-500"></i> ${cityStr}</p>
                                </div>
                            </div>
                            <div class="p-3 bg-white">
                                <a href="${slugUrl}" class="flex items-center justify-center gap-2 w-full text-center bg-zinc-950 text-white text-[10px] font-black py-3 rounded-xl hover:bg-blue-600 transition-colors uppercase tracking-widest shadow-lg">Lihat Etalase <i class="fas fa-arrow-right"></i></a>
                            </div>
                        </div>
                    `;

                    L.marker([store.latitude, store.longitude], {icon: storeIcon}).addTo(map).bindPopup(popupHTML);
                }
            });

            // ==========================================
            // 4. LOGIKA TOMBOL GPS 
            // ==========================================
            window.requestGPS = function() {
                const loader = document.getElementById('gps-loader');
                loader.classList.remove('hidden');
                
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(
                        function(position) {
                            window.location.href = `{{ route('toko.index') }}?lat=${position.coords.latitude}&lng=${position.coords.longitude}`;
                        },
                        function(error) {
                            loader.classList.add('hidden');
                            let msg = "Terjadi kesalahan saat melacak lokasi.";
                            if(error.code == error.PERMISSION_DENIED) msg = "Akses lokasi ditolak browser. Izinkan lokasi untuk fitur ini.";
                            alert(msg);
                        },
                        { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
                    );
                } else {
                    loader.classList.add('hidden');
                    alert("Browser tidak mendukung GPS.");
                }
            };

            // ==========================================
            // 5. AUTO DETECT LOCATION
            // ==========================================
            const urlParams = new URLSearchParams(window.location.search);
            if (!urlParams.has('lokasi') && !urlParams.has('lat') && !sessionStorage.getItem('auto_lokasi_semuatoko_done')) {
                sessionStorage.setItem('auto_lokasi_semuatoko_done', '1');
                fetch('https://ipapi.co/json/')
                    .then(res => res.ok ? res.json() : fetch('http://ip-api.com/json').then(r => r.json()))
                    .then(data => {
                        let cleanCity = (data.city || "").toLowerCase().replace(/kabupaten|kota|kab\./g, '').trim();
                        if (cleanCity) {
                            let selectLokasi = document.getElementById('lokasi-select');
                            let matchFound = false;
                            for (let i = 0; i < selectLokasi.options.length; i++) {
                                let optionText = selectLokasi.options[i].text.toLowerCase().replace(/kabupaten|kota|kab\./g, '').trim();
                                if (optionText === cleanCity || optionText.includes(cleanCity) || cleanCity.includes(optionText)) {
                                    selectLokasi.selectedIndex = i; matchFound = true; break;
                                }
                            }
                            if (!matchFound) {
                                let formattedCity = cleanCity.charAt(0).toUpperCase() + cleanCity.slice(1);
                                selectLokasi.add(new Option(formattedCity, formattedCity, true, true));
                            }
                            document.getElementById('filterForm').submit();
                        }
                    })
                    .catch(e => console.log('Gagal IP Geolocation'));
            }
            // ==========================================
            // 6. LOAD MORE LOGIC (AJAX) - ESTETIK
            // ==========================================
            const loadMoreBtn = document.getElementById('load-more-btn');
            if(loadMoreBtn) {
                loadMoreBtn.addEventListener('click', function() {
                    const url = this.getAttribute('data-next-url');
                    if(!url) return;

                    const originalHTML = this.innerHTML;
                    this.innerHTML = '<span class="flex items-center gap-2"><i class="fas fa-circle-notch fa-spin text-blue-400"></i> Memuat...</span>';
                    this.disabled = true;

                    fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                        .then(res => res.text())
                        .then(html => {
                            const parser = new DOMParser();
                            const doc = parser.parseFromString(html, 'text/html');
                            
                            const newItems = doc.querySelectorAll('#store-grid > a');
                            const grid = document.getElementById('store-grid');
                            newItems.forEach(item => {
                                item.style.opacity = '0';
                                item.style.transform = 'translateY(10px)';
                                item.style.transition = 'all 0.5s ease';
                                grid.appendChild(item);
                                
                                setTimeout(() => {
                                    item.style.opacity = '1';
                                    item.style.transform = 'translateY(0)';
                                }, 50);
                            });

                            const newBtn = doc.getElementById('load-more-btn');
                            if(newBtn) {
                                this.setAttribute('data-next-url', newBtn.getAttribute('data-next-url'));
                                this.innerHTML = originalHTML;
                                this.disabled = false;
                            } else {
                                this.remove(); 
                            }
                        })
                        .catch(err => {
                            console.error('Failed to load more stores:', err);
                            this.innerHTML = originalHTML;
                            this.disabled = false;
                        });
                });
            }
        });
    </script>
</body>
</html>