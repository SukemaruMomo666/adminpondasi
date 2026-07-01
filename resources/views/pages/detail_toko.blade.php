<!DOCTYPE html>
<html lang="id">
<head>
    <link rel="icon" href="{{ asset('logopondasikita.png') }}" type="image/png">
    <meta charset="UTF-8">
    <title>{{ $toko->nama_toko }} - Official Store | Pondasikita</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'ui-sans-serif', 'system-ui', '-apple-system', 'sans-serif'] },
                    colors: { 
                        brand: { 50: '#eff6ff', 100: '#dbeafe', 500: '#3b82f6', 600: '#2563eb', 700: '#1d4ed8' },
                        toko: { 50: '#fff1f2', 100: '#ffe4e6', 500: '#f43f5e', 600: '#e11d48', 700: '#be123c' } 
                    },
                    boxShadow: { 
                        'card': '0 1px 6px 0 rgba(49,53,59,0.12)', 
                        'card-hover': '0 4px 12px 0 rgba(49,53,59,0.2)',
                    }
                }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f4f6f8; scroll-behavior: smooth; }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; scroll-behavior: smooth; }
        
        /* Pagination E-commerce */
        .pagination-wrap nav { display: flex; justify-content: center; width: 100%; margin: 2rem 0; }
        .pagination-wrap .pagination { display: flex; gap: 0.25rem; background: white; padding: 0.5rem; border-radius: 0.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .pagination-wrap .page-item .page-link { display: flex; align-items: center; justify-content: center; min-width: 2.5rem; height: 2.5rem; border-radius: 0.25rem; font-weight: 600; color: #4b5563; padding: 0 0.5rem; transition: all 0.2s; }
        .pagination-wrap .page-item:not(.active) .page-link:hover { background: #f3f4f6; color: #111827; }
        .pagination-wrap .page-item.active .page-link { background: #e11d48; color: white; border-color: #e11d48; }

        /* Style Tiket Voucher */
        .voucher-ticket {
            background: #fffafa;
            border: 1px solid #fecdd3;
            border-radius: 8px;
            position: relative;
            border-style: dashed;
        }
        .voucher-ticket::before, .voucher-ticket::after {
            content: '';
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            width: 14px;
            height: 14px;
            background-color: #f4f6f8;
            border-radius: 50%;
            border: 1px dashed #fecdd3;
        }
        .voucher-ticket::before { left: -8px; border-right-color: transparent; border-top-color: transparent; transform: translateY(-50%) rotate(45deg); }
        .voucher-ticket::after { right: -8px; border-left-color: transparent; border-bottom-color: transparent; transform: translateY(-50%) rotate(45deg); }
    </style>
</head>
<body class="text-gray-800 antialiased pt-[70px] lg:pt-[80px]">

    @include('partials.navbar')

    @php
        // =====================================================================
        // ENGINE DATA REALTIME (Data Murni Database)
        // =====================================================================

        // 1. Data Banner & Logo Anti-Error Hosting
        $bgBanner = !empty($toko->banner_toko) ? asset('assets/uploads/banners/' . $toko->banner_toko) : 'https://images.unsplash.com/photo-1504307651254-35680f356dfd?q=80&w=2000&auto=format&fit=crop';
        $logoPath = !empty($toko->logo_toko) ? asset('assets/uploads/logos/' . $toko->logo_toko) : '';
        $colors = ['#18181b', '#27272a', '#3f3f46', '#09090b', '#1e3a8a'];
        $storeColor = $colors[crc32($toko->nama_toko) % count($colors)];
        $acronym = ""; foreach (explode(" ", $toko->nama_toko) as $w) { $acronym .= mb_substr($w, 0, 1); }
        $storeInitials = strtoupper(substr($acronym, 0, 2)) ?: "TK";

        // 2. Decode Dekorasi JSON
        $dekorasi = !empty($toko->dekorasi_desktop) ? json_decode($toko->dekorasi_desktop) : null;
        $headerColorClass = ($dekorasi && isset($dekorasi->header) && str_starts_with($dekorasi->header, 'bg-')) ? $dekorasi->header : '';
        $hasBannerImage = !empty($toko->banner_toko);

        // 3. Statistik Toko Realtime
        $totalProduk = DB::table('tb_barang')->where('toko_id', $toko->id)->where('is_active', 1)->where('status_moderasi', 'approved')->count();
        $mengikuti = DB::table('tb_toko_follower')->where('user_id', $toko->user_id)->count(); 
        $avgRating = DB::table('tb_toko_review')->where('toko_id', $toko->id)->avg('rating') ?? 0;
        $totalReview = DB::table('tb_toko_review')->where('toko_id', $toko->id)->count();
        
        \Carbon\Carbon::setLocale('id');
        $bergabung = \Carbon\Carbon::parse($toko->created_at)->diffForHumans(null, true) . ' Lalu';
        
        $jmlFollower = isset($totalFollowers) ? $totalFollowers : DB::table('tb_toko_follower')->where('toko_id', $toko->id)->count();
        $sudahFollow = isset($isFollowing) ? $isFollowing : (Auth::check() ? DB::table('tb_toko_follower')->where('toko_id', $toko->id)->where('user_id', Auth::id())->exists() : false);

        function formatAngkaK($angka) {
            if ($angka >= 1000000) return number_format($angka / 1000000, 1, ',', '') . 'JT';
            if ($angka >= 1000) return number_format($angka / 1000, 1, ',', '') . 'RB';
            return $angka;
        }

        // 4. Voucher Toko
        $vouchers = DB::table('vouchers')->where('toko_id', $toko->id)->where('status', 'AKTIF')->where('tanggal_berakhir', '>', now())->get();

        // 5. Kategori Toko & Parameter Sorting
        $kategoriToko = DB::table('tb_kategori')
            ->join('tb_barang', 'tb_kategori.id', '=', 'tb_barang.kategori_id')
            ->where('tb_barang.toko_id', $toko->id)
            ->where('tb_barang.is_active', 1)
            ->select('tb_kategori.id', 'tb_kategori.nama_kategori', DB::raw('COUNT(tb_barang.id) as total'))
            ->groupBy('tb_kategori.id', 'tb_kategori.nama_kategori')
            ->orderByDesc('total')
            ->get();

        $currentSort = request()->query('sort', 'terbaru');
        $currentCat = request()->query('kategori', '');
    @endphp

    <main class="max-w-[1200px] mx-auto px-0 sm:px-4 lg:px-8 py-0 sm:py-6">

        {{-- ======================================================= --}}
        {{-- HEADER TOKO & STATISTIK OFFICIAL STORE --}}
        {{-- ======================================================= --}}
        <div class="bg-white sm:rounded-2xl shadow-card overflow-hidden border-b sm:border border-gray-200 relative z-10">
            
            {{-- Banner --}}
            <div class="w-full h-44 sm:h-56 lg:h-[320px] relative bg-zinc-900 {{ $headerColorClass }}">
                @if($hasBannerImage)
                    <img src="{{ $bgBanner }}" alt="Banner Toko" class="absolute inset-0 w-full h-full object-cover object-center opacity-95">
                @else
                    <div class="absolute inset-0 opacity-20 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')]"></div>
                @endif
                <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent"></div>
            </div>

            <div class="px-5 sm:px-8 pb-6 relative bg-white">
                <div class="flex flex-col md:flex-row md:items-start gap-5 md:gap-8">
                    
                    {{-- Logo Toko --}}
                    <div class="-mt-16 md:-mt-20 relative z-10 shrink-0 mx-auto md:mx-0">
                        <div class="w-32 h-32 sm:w-36 sm:h-36 rounded-full border-[4px] border-white shadow-md bg-white overflow-hidden relative">
                            @if($logoPath)
                                <img src="{{ $logoPath }}" alt="Logo" class="w-full h-full object-cover" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                <div class="w-full h-full hidden items-center justify-center text-4xl font-black text-white" style="background-color: {{ $storeColor }};">{{ $storeInitials }}</div>
                            @else
                                <div class="w-full h-full flex items-center justify-center text-5xl font-black text-white" style="background-color: {{ $storeColor }};">{{ $storeInitials }}</div>
                            @endif
                        </div>
                    </div>
                    
                    {{-- Nama & Statistik Toko --}}
                    <div class="text-center md:text-left pt-0 md:pt-4 flex-1">
                        
                        <div class="flex items-center justify-center md:justify-start gap-2 mb-2">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="text-purple-600">
                                <path d="M10.5 2.25L12 0L13.5 2.25H16.5V3.75L18.75 5.25L17.25 7.5L18.75 9.75L16.5 11.25V12.75H13.5L12 15L10.5 12.75H7.5V11.25L5.25 9.75L6.75 7.5L5.25 5.25L7.5 3.75V2.25H10.5Z" fill="#9333ea"/>
                                <path d="M10 10.5L7.5 8L8.5 7L10 8.5L14.5 4L15.5 5L10 10.5Z" fill="white"/>
                            </svg>
                            <h1 class="text-2xl sm:text-3xl font-black text-gray-900 tracking-tight leading-none">{{ $toko->nama_toko }}</h1>
                        </div>
                        
                        <div class="flex items-center justify-center md:justify-start gap-2 text-[13px] font-medium text-gray-500 mb-5">
                            <i class="fas fa-map-marker-alt text-brand-600"></i> {{ $toko->kota ?? 'Lokasi Nasional' }}
                            <span class="mx-1 text-gray-300">•</span>
                            <span class="text-emerald-600 font-bold flex items-center gap-1"><i class="fas fa-clock"></i> Buka</span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-4 text-[13px] text-gray-700 max-w-2xl mx-auto md:mx-0 border-t border-gray-100 pt-4">
                            <div class="flex items-center gap-2"><i class="fas fa-store text-gray-400 w-5 text-center"></i> <span class="text-gray-500">Produk:</span> <span class="font-bold text-toko-600">{{ formatAngkaK($totalProduk) }}</span></div>
                            <div class="flex items-center gap-2"><i class="fas fa-user-group text-gray-400 w-5 text-center"></i> <span class="text-gray-500">Pengikut:</span> <span class="font-bold text-toko-600" id="follower-text">{{ formatAngkaK($jmlFollower) }}</span></div>
                            
                            <div class="flex items-center gap-2"><i class="fas fa-user-plus text-gray-400 w-5 text-center"></i> <span class="text-gray-500">Mengikuti:</span> <span class="font-bold text-toko-600">{{ formatAngkaK($mengikuti) }}</span></div>
                            <div class="flex items-center gap-2"><i class="far fa-star text-gray-400 w-5 text-center"></i> <span class="text-gray-500">Penilaian:</span> <span class="font-bold text-toko-600">{{ number_format($avgRating, 1) }} ({{ formatAngkaK($totalReview) }} Penilaian)</span></div>
                            
                            <div class="flex items-center gap-2"><i class="far fa-comment-dots text-gray-400 w-5 text-center"></i> <span class="text-gray-500">Performa Chat:</span> <span class="font-bold text-toko-600">98% (Hitungan Menit)</span></div>
                            <div class="flex items-center gap-2"><i class="far fa-user text-gray-400 w-5 text-center"></i> <span class="text-gray-500">Bergabung:</span> <span class="font-bold text-toko-600">{{ $bergabung }}</span></div>
                        </div>
                    </div>

                    {{-- Tombol Aksi Chat & Follow --}}
                    <div class="flex flex-col items-center md:items-end pt-0 md:pt-4 w-full md:w-auto mt-4 md:mt-0 gap-3 border-t md:border-0 border-gray-100 pt-4">
                        <div class="flex items-center justify-center md:justify-end gap-3 w-full sm:w-auto">
                            @auth
                                <button type="button" onclick="triggerOpenChat({{ $toko->id }}, '{{ addslashes($toko->nama_toko) }}', '{{ $storeInitials ?? 'TK' }}')" class="flex-1 sm:flex-none bg-white border border-toko-600 text-toko-600 font-bold px-8 py-2.5 rounded-[4px] hover:bg-toko-50 transition-colors">
                                    <i class="fas fa-comment-dots"></i> Chat
                                </button>
                            @else
                                <button type="button" onclick="requireChatLogin()" class="flex-1 sm:flex-none bg-white border border-toko-600 text-toko-600 font-bold px-8 py-2.5 rounded-[4px] hover:bg-toko-50 transition-colors">
                                    <i class="fas fa-comment-dots"></i> Chat
                                </button>
                            @endauth
                            
                            <button id="btn-follow" onclick="toggleFollowToko()" class="flex-1 sm:flex-none font-bold px-8 py-2.5 rounded-[4px] shadow-sm transition-colors {{ $sudahFollow ? 'bg-white border border-gray-300 text-gray-600 hover:bg-gray-50' : 'bg-toko-600 text-white hover:bg-toko-700' }}">
                                <i class="fas {{ $sudahFollow ? 'fa-check' : 'fa-plus' }}" id="icon-follow"></i> 
                                <span id="text-follow">{{ $sudahFollow ? 'Mengikuti' : 'Ikuti' }}</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- STICKY SCROLL-SPY TABS --}}
            <div class="sticky top-[70px] lg:top-[80px] bg-white border-t border-gray-200 z-30 px-5 sm:px-8 shadow-sm">
                <div class="flex overflow-x-auto no-scrollbar gap-8">
                    <a href="#" id="tab-utama" class="whitespace-nowrap py-4 border-b-[3px] border-toko-600 text-toko-600 font-bold text-[15px] transition-all">Halaman Utama</a>
                    <a href="#area-produk" id="tab-produk" class="whitespace-nowrap py-4 border-b-[3px] border-transparent text-gray-500 hover:text-toko-600 font-bold text-[15px] transition-all">Produk</a>
                </div>
            </div>
        </div>

        {{-- ======================================================= --}}
        {{-- VOUCHER TOKO --}}
        {{-- ======================================================= --}}
        @if($vouchers->count() > 0)
        <div class="mt-8 px-4 sm:px-0">
            <h3 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2"><i class="fas fa-ticket text-toko-600"></i> Voucher Toko</h3>
            <div class="flex overflow-x-auto gap-4 pb-4 no-scrollbar snap-x">
                @foreach($vouchers as $v)
                    <div class="voucher-ticket snap-start min-w-[280px] w-[280px] flex items-center justify-between p-4 flex-shrink-0 shadow-sm">
                        <div class="border-r border-dashed border-red-200 pr-3 w-full">
                            <h4 class="text-toko-600 font-bold text-[15px] leading-tight">
                                Diskon {{ $v->tipe_diskon == 'PERSEN' ? round($v->nilai_diskon).'%' : 'Rp'.number_format($v->nilai_diskon/1000, 0, ',', '').'RB' }}
                            </h4>
                            <p class="text-[11px] text-toko-600 mt-0.5">Min. Blj Rp{{ number_format($v->min_pembelian/1000, 0, ',', '') }}RB</p>
                            <p class="text-[10px] text-gray-400 mt-2">Hingga: {{ \Carbon\Carbon::parse($v->tanggal_berakhir)->format('d.m.Y') }}</p>
                        </div>
                        <div class="pl-3">
                            <button class="bg-toko-600 hover:bg-toko-700 transition-colors text-white text-xs font-bold px-4 py-1.5 rounded-[4px]" onclick="alert('Voucher {{ $v->kode_voucher }} berhasil diklaim!')">Klaim</button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- ======================================================= --}}
        {{-- DYNAMIC DEKORASI (HALAMAN UTAMA) --}}
        {{-- ======================================================= --}}
        @if($dekorasi && isset($dekorasi->layout) && count($dekorasi->layout) > 0)
            <div class="w-full flex flex-col gap-6 mt-6 mb-8 px-4 sm:px-0" id="area-dekorasi">
                @foreach($dekorasi->layout as $item)
                    @php $config = $item->config; @endphp

                    {{-- Banner --}}
                    @if($item->type === 'banner')
                        @php 
                            $aspectClass = 'aspect-[4/1]';
                            if(isset($config->ratio)) {
                                if($config->ratio == '16:9') $aspectClass = 'aspect-video';
                                elseif($config->ratio == '3:1') $aspectClass = 'aspect-[3/1]';
                            }
                            $bannerId = 'banner-' . $item->uid;
                        @endphp
                        <div class="w-full rounded-2xl overflow-hidden relative shadow-sm {{ $aspectClass }} bg-slate-900 flex items-center justify-center group">
                            <div class="flex h-full w-full transition-transform duration-700 ease-in-out" id="{{ $bannerId }}">
                                @if(!empty($config->images) && count($config->images) > 0)
                                    @foreach($config->images as $img)
                                        <img src="{{ $img }}" class="w-full h-full object-cover shrink-0">
                                    @endforeach
                                @else
                                    <div class="w-full h-full bg-slate-800 flex items-center justify-center shrink-0">
                                        <i class="fas fa-image text-slate-700 text-6xl"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="absolute inset-0 bg-black/20 pointer-events-none"></div>
                            @if(!empty($config->title))
                                <h3 class="absolute z-10 font-black text-center px-10 italic drop-shadow-2xl text-white pointer-events-none" 
                                    style="color: {{ $config->textColor ?? '#ffffff' }}; font-size: clamp(1.2rem, 4vw, 2.5rem);">
                                    {{ $config->title }}
                                </h3>
                            @endif

                            {{-- Navigation --}}
                            @if(!empty($config->images) && count($config->images) > 1)
                                <div class="absolute bottom-4 left-1/2 -translate-x-1/2 flex gap-2 z-20">
                                    @foreach($config->images as $index => $img)
                                        <button class="w-2 h-2 rounded-full bg-white/40 border border-white/20 transition-all hover:bg-white" onclick="moveSlider('{{ $bannerId }}', {{ $index }})"></button>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                    {{-- Grid Foto (Frameless Aesthetic) --}}
                    @elseif($item->type === 'carousel')
                        @php
                            $actualImages = array_filter($config->images ?? []);
                            $imgCount = count($actualImages);
                        @endphp
                        
                        @if($imgCount > 0)
                            <div class="w-full overflow-hidden rounded-2xl shadow-sm border border-gray-100 bg-white">
                                @php
                                    // Logika Grid Pintar: Jika user set 5 grid tapi gambar cuma 3, pakai 3.
                                    $requestedCols = isset($config->gridType) ? (int)$config->gridType : 3;
                                    $finalCols = min($imgCount, $requestedCols);
                                    
                                    $gridClass = 'grid-cols-3';
                                    if($finalCols == 1) $gridClass = 'grid-cols-1';
                                    elseif($finalCols == 2) $gridClass = 'grid-cols-2';
                                    elseif($finalCols == 4) $gridClass = 'grid-cols-4';
                                    elseif($finalCols >= 5) $gridClass = 'grid-cols-5';
                                @endphp

                                <div class="grid {{ $gridClass }} gap-0.5 w-full bg-gray-200">
                                    @foreach($actualImages as $img)
                                        <div class="aspect-square bg-slate-100 overflow-hidden group/img relative">
                                            <img src="{{ $img }}" class="w-full h-full object-cover transition-transform duration-700 group-hover/img:scale-110">
                                            {{-- Overlay halus saat hover --}}
                                            <div class="absolute inset-0 bg-black/0 group-hover/img:bg-black/10 transition-colors duration-300"></div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                    {{-- Video --}}
                    @elseif($item->type === 'video')
                        <div class="w-full bg-black rounded-2xl overflow-hidden shadow-lg aspect-video">
                            @if($config->videoSource === 'youtube' && !empty($config->videoUrl))
                                @php
                                    $videoId = "";
                                    $regExp = '/^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=)([^#\&\?]*).*/';
                                    if(preg_match($regExp, $config->videoUrl, $match) && strlen($match[2]) == 11) {
                                        $videoId = $match[2];
                                    }
                                @endphp
                                @if($videoId)
                                    <iframe class="w-full h-full" src="https://www.youtube.com/embed/{{ $videoId }}?autoplay=0&mute=0&controls=1" frameborder="0" allowfullscreen></iframe>
                                @endif
                            @elseif($config->videoSource === 'local' && !empty($config->videoFile))
                                <video class="w-full h-full object-contain" controls>
                                    <source src="{{ $config->videoFile }}" type="video/mp4">
                                </video>
                            @endif
                        </div>

                    {{-- Kategori Icon --}}
                    @elseif($item->type === 'kategori')
                        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                            @if(!empty($config->title))
                                <h4 class="text-lg font-black mb-6 border-l-4 border-toko-500 pl-3 uppercase tracking-tight" style="color: {{ $config->textColor ?? '#1e293b' }}">{{ $config->title }}</h4>
                            @endif
                            <div class="grid grid-cols-4 md:grid-cols-6 lg:grid-cols-8 gap-4 sm:gap-6">
                                @foreach($kategoriToko as $kt)
                                    <a href="{{ request()->fullUrlWithQuery(['kategori' => $kt->id]) }}#area-produk" class="flex flex-col items-center gap-3 cursor-pointer group">
                                        <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-2xl bg-slate-50 text-slate-400 flex items-center justify-center group-hover:bg-toko-500 group-hover:text-white group-hover:shadow-lg group-hover:shadow-toko-500/20 transition-all duration-300 border border-slate-100 group-hover:border-toko-500">
                                            <i class="fas fa-layer-group text-xl sm:text-2xl"></i>
                                        </div>
                                        <span class="text-[10px] sm:text-xs font-black text-slate-600 group-hover:text-toko-600 text-center leading-tight transition-colors">{{ $kt->nama_kategori }}</span>
                                    </a>
                                @endforeach
                            </div>
                        </div>

                    {{-- Produk Showcase Horizontal --}}
                    @elseif($item->type === 'produk')
                        <div class="bg-white py-6 px-5 rounded-2xl shadow-sm border border-gray-100">
                            <div class="flex justify-between items-center mb-6">
                                <h4 class="text-lg font-black border-l-4 border-toko-500 pl-3 uppercase tracking-tight" style="color: {{ $config->textColor ?? '#1e293b' }}">{{ $config->title ?? 'Etalase' }}</h4>
                                <a href="#area-produk" class="text-xs font-black text-toko-600 hover:text-toko-800 uppercase tracking-widest bg-toko-50 px-4 py-2 rounded-lg">Lihat Semua</a>
                            </div>

                            @php
                                $renderProducts = array_slice($products->items(), 0, 10);
                            @endphp

                            <div class="flex overflow-x-auto gap-4 pb-4 no-scrollbar snap-x">
                                @foreach($renderProducts as $prod)
                                    @php
                                        $pSlug = is_object($prod) ? $prod->slug : ($prod->slug ?? '#');
                                        $pName = is_object($prod) && isset($prod->nama_barang) ? $prod->nama_barang : ($prod->name ?? 'Produk');
                                        $pPrice = is_object($prod) && isset($prod->harga) ? 'Rp'.number_format($prod->harga,0,',','.') : ($prod->price ?? '0');
                                        $pImg = (is_object($prod) && !empty($prod->gambar_utama)) ? asset('assets/uploads/products/'.$prod->gambar_utama) : 'https://images.unsplash.com/photo-1589939705384-5185137a7f0f?w=400';
                                    @endphp

                                    <a href="{{ route('produk.detail', $pSlug) }}" class="snap-start min-w-[150px] w-[150px] flex-shrink-0 bg-white rounded-xl shadow-card hover:shadow-card-hover transition-shadow duration-200 overflow-hidden flex flex-col group border border-gray-100 hover:border-toko-500">
                                        <div class="w-full pt-[100%] relative bg-gray-100 overflow-hidden">
                                            <img src="{{ $pImg }}" class="absolute inset-0 w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-500">
                                        </div>
                                        <div class="p-3 flex flex-col flex-1">
                                            <h3 class="text-xs font-medium text-gray-800 line-clamp-2 leading-[1.3] mb-1.5 group-hover:text-toko-600">{{ $pName }}</h3>
                                            <div class="mt-auto pt-1">
                                                <div class="text-sm font-bold text-gray-900 mb-1.5">{{ $pPrice }}</div>
                                            </div>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        @endif

        {{-- ======================================================= --}}
        {{-- SECTION SEMUA PRODUK (SELALU DI BAWAH) --}}
        {{-- ======================================================= --}}
        <div id="area-produk" class="mt-12 px-4 sm:px-0 grid grid-cols-1 lg:grid-cols-5 gap-6 items-start scroll-mt-24">
            
            {{-- SIDEBAR KATEGORI (ANTI-BUG URL) --}}
            <div class="lg:col-span-1 hidden lg:block bg-transparent sticky top-[150px]">
                <h3 class="font-bold text-lg text-gray-800 mb-4 flex items-center gap-2"><i class="fas fa-list text-gray-800"></i> Kategori</h3>
                <ul class="space-y-3">
                    <li>
                        <a href="{{ request()->fullUrlWithQuery(['kategori' => null]) }}#area-produk" class="text-sm block {{ empty($currentCat) ? 'text-toko-600 font-bold border-l-2 border-toko-600 pl-2' : 'text-gray-600 hover:text-toko-600' }}">
                            Semua Produk
                        </a>
                    </li>
                    @foreach($kategoriToko as $kt)
                        <li>
                            <a href="{{ request()->fullUrlWithQuery(['kategori' => $kt->id]) }}#area-produk" class="text-sm block {{ $currentCat == $kt->id ? 'text-toko-600 font-bold border-l-2 border-toko-600 pl-2' : 'text-gray-600 hover:text-toko-600' }}">
                                {{ $kt->nama_kategori }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>

            {{-- SORTING & GRID PRODUK --}}
            <div class="lg:col-span-4 w-full">
                
                {{-- Toolbar Sorting (ANTI-BUG URL) --}}
                <div class="bg-gray-100 rounded-md p-2 flex flex-wrap items-center gap-2 mb-6">
                    <span class="text-sm text-gray-600 mr-2 ml-2">Urutkan</span>
                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'terlaris']) }}#area-produk" class="px-4 py-2 text-sm rounded-sm {{ $currentSort == 'terlaris' ? 'bg-toko-600 text-white font-bold' : 'bg-white border border-gray-200 text-gray-700 hover:text-toko-600' }} transition-colors">Populer</a>
                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'terbaru']) }}#area-produk" class="px-4 py-2 text-sm rounded-sm {{ $currentSort == 'terbaru' ? 'bg-toko-600 text-white font-bold' : 'bg-white border border-gray-200 text-gray-700 hover:text-toko-600' }} transition-colors">Terbaru</a>
                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'terlaris']) }}#area-produk" class="px-4 py-2 text-sm rounded-sm bg-white border border-gray-200 text-gray-700 hover:text-toko-600 transition-colors">Terlaris</a>
                    
                    {{-- Dropdown Harga --}}
                    <div class="relative group ml-1">
                        <button class="px-4 py-2 text-sm rounded-sm bg-white border border-gray-200 text-gray-700 flex items-center gap-4 min-w-[180px] justify-between">
                            Harga <i class="fas fa-chevron-down text-[10px]"></i>
                        </button>
                        <div class="absolute top-full left-0 w-full bg-white border border-gray-200 shadow-lg rounded mt-1 hidden group-hover:block z-20">
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'termurah']) }}#area-produk" class="block px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 hover:text-toko-600">Harga: Rendah ke Tinggi</a>
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'termahal']) }}#area-produk" class="block px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 hover:text-toko-600">Harga: Tinggi ke Rendah</a>
                        </div>
                    </div>
                </div>

                {{-- Filter Kategori Mobile (ANTI-BUG URL) --}}
                <div class="block lg:hidden mb-5">
                    <select onchange="window.location.href=this.value" class="w-full bg-white border border-gray-200 p-3 rounded-lg text-sm font-bold outline-none">
                        <option value="{{ request()->fullUrlWithQuery(['kategori' => null]) }}#area-produk">Semua Kategori</option>
                        @foreach($kategoriToko as $kt)
                            <option value="{{ request()->fullUrlWithQuery(['kategori' => $kt->id]) }}#area-produk" {{ $currentCat == $kt->id ? 'selected' : '' }}>{{ $kt->nama_kategori }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- GRID PRODUK UTAMA --}}
                @if($products->count() > 0)
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-4">
                        @foreach($products as $p)
                            @php $img = !empty($p->gambar_utama) ? 'assets/uploads/products/'.$p->gambar_utama : 'assets/uploads/products/default.jpg'; @endphp
                            <a href="{{ route('produk.detail', $p->slug) }}" class="bg-white rounded-lg shadow-card hover:shadow-card-hover transition-all duration-300 overflow-hidden flex flex-col group border border-transparent hover:border-toko-500 relative hover:-translate-y-1">
                                <div class="w-full pt-[100%] relative bg-white border-b border-gray-100 overflow-hidden">
                                    <img src="{{ asset($img) }}" onerror="this.src='https://images.unsplash.com/photo-1589939705384-5185137a7f0f?w=400'" class="absolute inset-0 w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-500">
                                </div>
                                <div class="p-3.5 flex flex-col flex-1">
                                    <h3 class="text-[13px] sm:text-sm font-medium text-gray-800 line-clamp-2 leading-[1.4] mb-2 group-hover:text-toko-600">{{ $p->nama_barang }}</h3>
                                    <div class="mt-auto pt-1">
                                        <div class="text-[16px] sm:text-[18px] font-black text-gray-900 leading-none mb-2">Rp{{ number_format($p->harga, 0, ',', '.') }}</div>
                                        <div class="flex items-center text-[11px] text-emerald-600 mt-1 font-bold bg-emerald-50 px-2 py-1 rounded-md w-max">
                                            <i class="fas fa-truck-fast mr-1.5"></i> Bisa Dikirim
                                        </div>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                    
                    {{-- Pagination --}}
                    <div class="pagination-wrap">{{ $products->appends(request()->query())->links() }}</div>
                @else
                    <div class="flex flex-col items-center justify-center py-20 bg-white rounded-xl border border-gray-100 shadow-sm">
                        <img src="https://assets.tokopedia.net/assets-tokopedia-lite/v2/zeus/kratos/60454a86.png" class="w-32 sm:w-40 mb-4 opacity-60 filter grayscale">
                        <h3 class="text-lg font-bold text-gray-800 mb-2">Produk Tidak Ditemukan</h3>
                        <p class="text-gray-500 text-sm">Penjual belum menambahkan produk di kategori ini.</p>
                    </div>
                @endif

            </div>
        </div>

    </main>

    @include('partials.footer')
    
    {{-- Widget Chat Global dari Sistem --}}
    @include('partials.chat')

    <script src="{{ asset('assets/js/navbar.js') }}"></script>

    <script>
        // FUNGSI SLIDER DEKORASI
        function moveSlider(id, index) {
            const slider = document.getElementById(id);
            if(slider) {
                slider.style.transform = `translateX(-${index * 100}%)`;
            }
        }
        
        // 1. SCROLL SPY TABS (Ganti Tab Otomatis saat di-scroll)
        document.addEventListener("DOMContentLoaded", function() {
            const tabUtama = document.getElementById('tab-utama');
            const tabProduk = document.getElementById('tab-produk');
            const areaProduk = document.getElementById('area-produk');

            tabUtama.addEventListener('click', (e) => { e.preventDefault(); window.scrollTo({ top: 0, behavior: 'smooth' }); });
            tabProduk.addEventListener('click', (e) => { e.preventDefault(); areaProduk.scrollIntoView({ behavior: 'smooth', block: 'start' }); });

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        tabProduk.classList.add('border-toko-600', 'text-toko-600');
                        tabProduk.classList.remove('border-transparent', 'text-gray-500');
                        tabUtama.classList.remove('border-toko-600', 'text-toko-600');
                        tabUtama.classList.add('border-transparent', 'text-gray-500');
                    } else {
                        tabUtama.classList.add('border-toko-600', 'text-toko-600');
                        tabUtama.classList.remove('border-transparent', 'text-gray-500');
                        tabProduk.classList.remove('border-toko-600', 'text-toko-600');
                        tabProduk.classList.add('border-transparent', 'text-gray-500');
                    }
                });
            }, { rootMargin: '-100px 0px 0px 0px' }); 

            if(areaProduk) observer.observe(areaProduk);
        });

        // 2. FUNGSI PERINGATAN CHAT (Bagi yang belum login)
        function requireChatLogin() {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'lock',
                    title: 'Akses Ditolak',
                    text: 'Silakan login terlebih dahulu untuk memulai obrolan dengan penjual.',
                    confirmButtonText: 'Login Sekarang',
                    confirmButtonColor: '#e11d48',
                    showCancelButton: true,
                    cancelButtonText: 'Batal',
                    customClass: { popup: 'rounded-3xl' }
                }).then((result) => {
                    if (result.isConfirmed) { window.location.href = "{{ route('login') }}"; }
                });
            } else {
                alert('Silakan login terlebih dahulu untuk memulai obrolan.');
                window.location.href = "{{ route('login') }}";
            }
        }

        // 3. FUNGSI BUKA CHAT GLOBAL
        function triggerOpenChat(tokoId, namaToko, inisial) {
            const chatWin = document.getElementById('live-chat-window');
            if(chatWin) {
                chatWin.classList.remove('hidden');
                setTimeout(() => {
                    chatWin.classList.remove('opacity-0', 'translate-y-10', 'scale-95', 'pointer-events-none');
                    chatWin.classList.add('flex', 'opacity-100', 'translate-y-0', 'scale-100');
                }, 10);
                sessionStorage.setItem('pota_chat_open', 'true');
                if(typeof window.switchChatTab === 'function') window.switchChatTab('seller', true);
                setTimeout(() => {
                    if(typeof window.openStoreChat === 'function') window.openStoreChat(tokoId, namaToko, inisial, false);
                }, 100);
            } else {
                console.error("Elemen chat window tidak ditemukan. Pastikan partials.chat termuat di halaman ini.");
            }
        }

        // 4. LOGIKA FOLLOW TOKO REAL-TIME
        let isFollowing = {{ $sudahFollow ? 'true' : 'false' }};
        let followerCount = {{ $jmlFollower }};

        function formatK(num) {
            if (num >= 1000000) return (num / 1000000).toFixed(1).replace('.0', '') + 'JT';
            if (num >= 1000) return (num / 1000).toFixed(1).replace('.0', '') + 'RB';
            return num;
        }

        function toggleFollowToko() {
            fetch('{{ route("api.toko.follow") }}', {
                method: 'POST',
                headers: { 
                    'X-CSRF-TOKEN': '{{ csrf_token() }}', 
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ toko_id: '{{ $toko->id }}' })
            })
            .then(response => {
                if (response.status === 401) {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'lock',
                            title: 'Akses Ditolak',
                            text: 'Silakan login terlebih dahulu untuk mengikuti toko ini.',
                            confirmButtonText: 'Login Sekarang',
                            confirmButtonColor: '#e11d48'
                        }).then(() => { window.location.href = '/login'; });
                    } else {
                        alert('Silakan login terlebih dahulu untuk mengikuti toko.');
                        window.location.href = '/login';
                    }
                    throw new Error('Not logged in');
                }
                return response.json();
            })
            .then(data => {
                if(data.status === 'success') {
                    isFollowing = (data.action === 'followed');
                    followerCount = data.total_followers;
                    
                    const btn = document.getElementById('btn-follow');
                    const icon = document.getElementById('icon-follow');
                    const text = document.getElementById('text-follow');
                    const counter = document.getElementById('follower-text');

                    if(isFollowing) {
                        btn.className = 'flex-1 sm:flex-none font-bold px-8 py-2.5 rounded-[4px] shadow-sm transition-colors bg-white border border-gray-300 text-gray-600 hover:bg-gray-50';
                        icon.className = 'fas fa-check text-emerald-500';
                        text.innerText = 'Mengikuti';
                    } else {
                        btn.className = 'flex-1 sm:flex-none font-bold px-8 py-2.5 rounded-[4px] shadow-sm transition-colors bg-toko-600 text-white hover:bg-toko-700';
                        icon.className = 'fas fa-plus';
                        text.innerText = 'Ikuti';
                    }
                    
                    counter.innerText = formatK(followerCount);
                }
            })
            .catch(error => console.error('Error:', error));
        }
    </script>
</body>
</html>
