<!DOCTYPE html>
<html lang="id">
<head>
    <link rel="icon" href="{{ asset('logopondasikita.png') }}" type="image/png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pilih Template Dekorasi</title>

    {{-- Tailwind CSS Standalone --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@mdi/font@7.2.96/css/materialdesignicons.min.css">

    <style>
        [x-cloak] { display: none !important; }
        .hide-scrollbar::-webkit-scrollbar { display: none; }
        .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

        /* EFEK KARTU - NAIK HALUS SAAT HOVER */
        .template-card { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
        .template-card:hover {
            box-shadow: 0 20px 40px -5px rgba(59, 130, 246, 0.2);
            border-color: #3b82f6; transform: translateY(-6px); z-index: 10;
        }

        /* ==========================================================
           MOCKUP HP TINGKAT DEWA (iPHONE 15 PRO MAX TITANIUM)
           ========================================================== */
        .iphone-frame {
            width: 350px; height: 730px; flex-shrink: 0;
            /* Metallic titanium finish */
            background: linear-gradient(135deg, #475569 0%, #1e293b 25%, #000000 50%, #1e293b 75%, #475569 100%);
            border-radius: 56px; padding: 12px;
            position: relative; margin: 0 auto;
            /* Ultimate 3D Shadow rendering */
            box-shadow:
                inset 0 0 0 2px #64748b,
                inset 0 0 0 7px #0f172a,
                0 45px 80px -15px rgba(0, 0, 0, 0.7),
                0 20px 40px -10px rgba(0, 0, 0, 0.5);
        }

        /* Tombol Kiri (Action Button & Volumes) */
        .iphone-frame::before {
            content: ''; position: absolute; top: 120px; left: -3px;
            width: 3px; height: 26px;
            background: linear-gradient(to bottom, #64748b, #94a3b8, #64748b);
            border-radius: 4px 0 0 4px;
            box-shadow: 0 50px 0 0 #64748b, 0 110px 0 0 #64748b;
        }

        /* Tombol Kanan (Power) */
        .iphone-power {
            position: absolute; top: 170px; right: -3px;
            width: 3px; height: 80px;
            background: linear-gradient(to bottom, #64748b, #94a3b8, #64748b);
            border-radius: 0 4px 4px 0;
        }

        /* Layar Utama */
        .iphone-screen {
            width: 100%; height: 100%; background: #f8fafc;
            border-radius: 44px; overflow: hidden; position: relative;
            display: flex; flex-direction: column;
            box-shadow: inset 0 0 0 2px #000; /* Screen black border gap */
        }

        /* Silau Kaca / Glass Glare */
        .iphone-glare {
            position: absolute; inset: 0; pointer-events: none; z-index: 998;
            background: linear-gradient(110deg, rgba(255,255,255,0.12) 0%, rgba(255,255,255,0) 35%, rgba(255,255,255,0) 100%);
            border-radius: 44px;
        }

        /* Dynamic Island */
        .iphone-island {
            position: absolute; top: 10px; left: 50%; transform: translateX(-50%);
            width: 110px; height: 32px; background: #000000; border-radius: 20px; z-index: 100;
            display: flex; align-items: center; justify-content: flex-end; padding: 0 8px;
            box-shadow: inset 0 0 2px rgba(255,255,255,0.15);
        }

        /* Lensa Kamera Dalam Island */
        .island-camera {
            width: 14px; height: 14px; border-radius: 50%; background: #111;
            box-shadow: inset -2px -2px 4px rgba(255,255,255,0.15); border: 1px solid #222;
        }
    </style>

    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/focus@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-[#f8fafc] overflow-y-auto">

{{-- DATA 20 TEMPLATE PREMIUM --}}
@php
$templates = [
    ['id'=>1, 'name'=>'Oceanic Premium', 'desc'=>'Profesional & Elegan', 'hc'=>'bg-gradient-to-r from-blue-600 to-indigo-700', 'ac'=>'bg-gradient-to-br from-blue-400 to-cyan-400', 'layout'=>['banner', 'kategori', 'produk1']],
    ['id'=>2, 'name'=>'Eco Harvest', 'desc'=>'Segar & Natural', 'hc'=>'bg-gradient-to-r from-emerald-500 to-green-600', 'ac'=>'bg-gradient-to-br from-emerald-400 to-lime-400', 'layout'=>['kategori', 'carousel', 'produk1']],
    ['id'=>3, 'name'=>'Sunset Flash', 'desc'=>'Fokus Diskon Kilat', 'hc'=>'bg-gradient-to-r from-orange-500 to-red-500', 'ac'=>'bg-gradient-to-br from-orange-400 to-red-400', 'layout'=>['video', 'produk1', 'banner']],
    ['id'=>4, 'name'=>'Midnight Luxury', 'desc'=>'Eksklusif & Gelap', 'hc'=>'bg-gradient-to-b from-slate-800 to-black', 'ac'=>'bg-gradient-to-br from-slate-700 to-slate-900', 'layout'=>['carousel', 'kategori', 'produk1']],
    ['id'=>5, 'name'=>'Pink Blossom', 'desc'=>'Lembut & Feminim', 'hc'=>'bg-gradient-to-r from-pink-400 to-rose-500', 'ac'=>'bg-gradient-to-br from-pink-300 to-rose-400', 'layout'=>['banner', 'produk1', 'kategori']],
    ['id'=>6, 'name'=>'Neon Cyber', 'desc'=>'Modern & High Tech', 'hc'=>'bg-gradient-to-r from-purple-600 to-cyan-500', 'ac'=>'bg-gradient-to-br from-purple-500 to-fuchsia-500', 'layout'=>['video', 'carousel', 'produk1']],
    ['id'=>7, 'name'=>'Minimalist Clean', 'desc'=>'Putih & Rapi', 'hc'=>'bg-white border-b border-slate-200 text-slate-800', 'ac'=>'bg-slate-100 text-slate-800 border border-slate-200', 'layout'=>['kategori', 'produk1', 'produk2']],
    ['id'=>8, 'name'=>'Pastel Dream', 'desc'=>'Warna Warni Lembut', 'hc'=>'bg-gradient-to-r from-violet-400 to-fuchsia-400', 'ac'=>'bg-gradient-to-br from-fuchsia-300 to-pink-300', 'layout'=>['carousel', 'banner', 'produk1']],
    ['id'=>9, 'name'=>'Earthy Warm', 'desc'=>'Coklat & Nyaman', 'hc'=>'bg-gradient-to-r from-amber-700 to-orange-800', 'ac'=>'bg-gradient-to-br from-amber-500 to-orange-400', 'layout'=>['banner', 'kategori', 'produk1']],
    ['id'=>10, 'name'=>'Royal Gold', 'desc'=>'Mewah & Klasik', 'hc'=>'bg-gradient-to-r from-yellow-600 to-amber-600', 'ac'=>'bg-gradient-to-br from-yellow-400 to-amber-500', 'layout'=>['carousel', 'produk1', 'kategori']],
    ['id'=>11, 'name'=>'Ruby Red', 'desc'=>'Berani & Meriah', 'hc'=>'bg-gradient-to-r from-red-600 to-rose-700', 'ac'=>'bg-gradient-to-br from-red-500 to-rose-500', 'layout'=>['video', 'kategori', 'produk1']],
    ['id'=>12, 'name'=>'Sky Blue', 'desc'=>'Cerah & Terang', 'hc'=>'bg-gradient-to-r from-sky-400 to-blue-500', 'ac'=>'bg-gradient-to-br from-sky-300 to-blue-400', 'layout'=>['banner', 'carousel', 'produk1']],
    ['id'=>13, 'name'=>'Vintage Retro', 'desc'=>'Klasik Nostalgia', 'hc'=>'bg-gradient-to-r from-stone-600 to-orange-800', 'ac'=>'bg-gradient-to-br from-stone-400 to-orange-300', 'layout'=>['kategori', 'produk1', 'banner']],
    ['id'=>14, 'name'=>'Sporty Active', 'desc'=>'Kuning & Hitam', 'hc'=>'bg-gradient-to-r from-yellow-400 to-yellow-500 text-slate-900', 'ac'=>'bg-gradient-to-br from-slate-800 to-black', 'layout'=>['video', 'produk1', 'produk2']],
    ['id'=>15, 'name'=>'Lavender Magic', 'desc'=>'Ungu Elegan', 'hc'=>'bg-gradient-to-r from-indigo-500 to-purple-600', 'ac'=>'bg-gradient-to-br from-indigo-400 to-purple-400', 'layout'=>['banner', 'kategori', 'produk1']],
    ['id'=>16, 'name'=>'Mint Fresh', 'desc'=>'Hijau Muda', 'hc'=>'bg-gradient-to-r from-teal-400 to-emerald-400', 'ac'=>'bg-gradient-to-br from-teal-300 to-emerald-300', 'layout'=>['carousel', 'kategori', 'produk1']],
    ['id'=>17, 'name'=>'Dark Maroon', 'desc'=>'Merah Gelap', 'hc'=>'bg-gradient-to-r from-rose-900 to-red-950', 'ac'=>'bg-gradient-to-br from-rose-800 to-red-800', 'layout'=>['video', 'banner', 'produk1']],
    ['id'=>18, 'name'=>'Silver Steel', 'desc'=>'Abu-Abu Tekno', 'hc'=>'bg-gradient-to-r from-slate-400 to-slate-600', 'ac'=>'bg-gradient-to-br from-slate-300 to-slate-400 text-slate-800', 'layout'=>['kategori', 'carousel', 'produk1']],
    ['id'=>19, 'name'=>'Peach Perfect', 'desc'=>'Manis & Hangat', 'hc'=>'bg-gradient-to-r from-rose-400 to-orange-400', 'ac'=>'bg-gradient-to-br from-rose-300 to-orange-300', 'layout'=>['banner', 'produk1', 'kategori']],
    ['id'=>20, 'name'=>'Galaxy Night', 'desc'=>'Neon Biru Pink', 'hc'=>'bg-gradient-to-r from-indigo-900 to-fuchsia-900', 'ac'=>'bg-gradient-to-br from-indigo-500 to-fuchsia-500', 'layout'=>['carousel', 'video', 'produk1']],
];
@endphp

<script>
    const TEMPLATES_DATA = @json($templates);
</script>

{{-- WRAPPER UTAMA ALPINE JS --}}
<div x-data="templateManager()" x-init="initPage()" class="font-sans text-slate-800 w-full" x-cloak>

    {{-- =================================================================
         KONTEN HALAMAN (BERDIRI SENDIRI, TANPA SIDEBAR BAWAAN)
         ================================================================= --}}
    <div>

        {{-- 1. HEADER PONDASIKITA --}}
        <div class="bg-[#1e293b] border-b border-slate-700 h-16 px-4 md:px-8 flex justify-between items-center sticky top-0 z-40 shadow-sm">
            <div class="flex items-center gap-4">
                <div class="w-8 h-8 bg-blue-600 text-white rounded flex items-center justify-center font-black text-xl shadow-lg shadow-blue-500/30">
                    <i class="mdi mdi-store"></i>
                </div>
                <div class="flex items-center text-sm md:text-base font-bold text-slate-400 gap-2">
                    <a href="{{ route('seller.dashboard') }}" class="hover:text-blue-400 transition-colors">Beranda</a>
                    <i class="mdi mdi-chevron-right text-slate-600"></i>
                    <a href="{{ route('seller.shop.decoration') }}" class="hover:text-blue-400 transition-colors">Dekorasi Toko</a>
                    <i class="mdi mdi-chevron-right text-slate-600"></i>
                    <span class="text-white">Dekorasi Instan</span>
                </div>
            </div>
            <div class="flex items-center gap-4">
                <div class="flex items-center gap-2 cursor-pointer hover:bg-slate-800 p-1.5 rounded-lg transition-colors text-white">
                    <div class="w-8 h-8 rounded-full bg-blue-600 text-white flex items-center justify-center font-black border border-blue-500">
                        {{ strtoupper(substr(optional(Auth::user())->name ?? 'U', 0, 1)) }}
                    </div>
                    <span class="text-sm font-bold hidden md:block">{{ optional(Auth::user())->name ?? 'Seller' }}</span>
                </div>
            </div>
        </div>

        {{-- 2. KONTEN HALAMAN UTAMA --}}
        <div class="px-6 lg:px-10 py-8 max-w-[1400px] mx-auto relative z-10">

            {{-- OPSI HALAMAN KOSONG --}}
            <div class="mb-10">
                <div x-on:click.prevent="blankCanvas()" class="bg-white rounded-2xl border border-slate-200 p-5 flex items-center gap-5 shadow-sm cursor-pointer hover:shadow-md hover:border-blue-400 transition-all group max-w-md">
                    <div class="w-12 h-12 bg-blue-50 text-blue-600 group-hover:bg-blue-600 group-hover:text-white transition-colors rounded-full flex items-center justify-center">
                        <i class="mdi mdi-plus text-2xl"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-black text-slate-900">Buat dari Halaman Kosong</h3>
                        <p class="text-xs text-slate-500 font-medium">Rancang kanvas bebas sesuai kreativitas.</p>
                    </div>
                </div>
            </div>

            {{-- FILTER BAR --}}
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 mb-8">
                <h2 class="text-xl font-black text-slate-900 mb-2">Pilih Dari 20 Template Premium</h2>
                <p class="text-sm font-medium text-slate-500 mb-6">Setiap template memiliki struktur komponen yang unik untuk memaksimalkan penjualan.</p>

                <div class="flex flex-wrap items-center gap-4 bg-slate-50 p-4 rounded-xl border border-slate-100">
                    <div class="flex items-center gap-2 flex-1 min-w-[150px]">
                        <span class="text-xs font-bold text-slate-500 uppercase">Kategori</span>
                        <select class="w-full bg-white border border-slate-300 text-sm font-bold rounded px-3 py-2 outline-none"><option>Semua Kategori</option></select>
                    </div>
                    <div class="flex items-center gap-2 flex-1 min-w-[150px]">
                        <span class="text-xs font-bold text-slate-500 uppercase">Tema</span>
                        <select class="w-full bg-white border border-slate-300 text-sm font-bold rounded px-3 py-2 outline-none"><option>Semua Warna</option></select>
                    </div>
                    <div class="flex items-center gap-2 flex-1 min-w-[200px]">
                        <span class="text-xs font-bold text-slate-500 uppercase">Urutkan</span>
                        <select class="w-full bg-white border border-slate-300 text-sm font-bold rounded px-3 py-2 outline-none"><option>Paling Populer</option></select>
                    </div>
                </div>
            </div>

            {{-- 3. GRID 20 TEMPLATES (MENGGUNAKAN BLADE FOREACH MURNI) --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 pb-20">
                @foreach($templates as $tpl)
                    <div class="template-card bg-white rounded-[2rem] border border-slate-200 p-2.5 flex flex-col relative group">

                        {{-- Visual Mockup Card --}}
                        <div class="h-[420px] w-full bg-slate-100 relative rounded-3xl overflow-hidden border border-slate-200 flex flex-col pointer-events-none">

                            <div class="h-28 w-full p-4 flex items-end relative {{ $tpl['hc'] }}">
                                <div class="flex gap-2 items-center relative z-10">
                                    <div class="w-8 h-8 rounded-full bg-white/20 border border-white/30"></div>
                                    <div class="w-20 h-2.5 bg-white/40 rounded-full"></div>
                                </div>
                            </div>

                            <div class="flex-1 p-2 space-y-2 bg-slate-50 overflow-hidden relative flex flex-col pointer-events-none">
                                @foreach($tpl['layout'] as $comp)
                                    @if($comp === 'banner')
                                        <div class="w-full h-24 rounded-lg flex items-center justify-center text-white/80 text-[10px] font-black shadow-sm {{ $tpl['ac'] }}">BANNER</div>
                                    @elseif($comp === 'carousel')
                                        <div class="w-full h-24 rounded-lg flex items-center justify-center text-white/80 text-[10px] font-black shadow-sm border border-slate-300 {{ $tpl['ac'] }}">CAROUSEL</div>
                                    @elseif($comp === 'video')
                                        <div class="w-full h-24 bg-slate-800 rounded-lg flex items-center justify-center text-red-500 shadow-sm border border-slate-300"><i class="mdi mdi-play-circle text-3xl"></i></div>
                                    @elseif($comp === 'kategori')
                                        <div class="grid grid-cols-4 gap-1.5">
                                            <div class="h-10 bg-white rounded-lg border border-slate-200 shadow-sm"></div><div class="h-10 bg-white rounded-lg border border-slate-200 shadow-sm"></div>
                                            <div class="h-10 bg-white rounded-lg border border-slate-200 shadow-sm"></div><div class="h-10 bg-white rounded-lg border border-slate-200 shadow-sm"></div>
                                        </div>
                                    @elseif($comp === 'produk1' || $comp === 'produk2')
                                        <div class="grid grid-cols-2 gap-2">
                                            <div class="h-20 bg-white rounded-lg border border-slate-100 p-1.5 shadow-sm"><div class="h-10 bg-slate-100 rounded mb-1"></div><div class="w-full h-1.5 bg-slate-200 rounded"></div></div>
                                            <div class="h-20 bg-white rounded-lg border border-slate-100 p-1.5 shadow-sm"><div class="h-10 bg-slate-100 rounded mb-1"></div><div class="w-full h-1.5 bg-slate-200 rounded"></div></div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>

                        {{-- OVERLAY HOVER MURNI TAILWIND --}}
                        <div class="absolute inset-0 bg-slate-900/80 backdrop-blur-[2px] opacity-0 pointer-events-none group-hover:opacity-100 group-hover:pointer-events-auto flex flex-col items-center justify-center gap-3 px-6 z-30 transition-all duration-300 rounded-[1.5rem]">
                            <button type="button" x-on:click.stop="applyTemplate({{ $tpl['id'] }})" class="transform translate-y-4 group-hover:translate-y-0 transition-all duration-300 delay-75 w-full py-3 bg-blue-600 hover:bg-blue-500 text-white text-sm font-bold rounded-xl shadow-xl outline-none">Gunakan Template</button>
                            <button type="button" x-on:click.stop="openPreview({{ $tpl['id'] }})" class="transform translate-y-4 group-hover:translate-y-0 transition-all duration-300 delay-150 w-full py-3 bg-white hover:bg-slate-100 text-slate-800 text-sm font-bold rounded-xl shadow-xl outline-none">Lihat Preview</button>
                        </div>

                        {{-- Teks Info Template --}}
                        <div class="mt-3 mb-2 px-2 text-center pointer-events-none">
                            <h5 class="font-black text-slate-800 text-sm">{{ $tpl['name'] }}</h5>
                            <p class="text-[10px] text-slate-400 font-bold">{{ $tpl['desc'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

    </div>

    {{-- =================================================================
         MODAL LIVE PREVIEW
         ================================================================= --}}
    <div x-show="isPreviewOpen" style="display: none;" class="fixed inset-0 z-[100000] overflow-y-auto hide-scrollbar" x-cloak>

        {{-- Backdrop Gelap Ekstra --}}
        <div x-show="isPreviewOpen" x-transition.opacity.duration.300ms class="fixed inset-0 bg-slate-900/90 backdrop-blur-md" x-on:click="closePreview()"></div>

        {{-- TOMBOL TUTUP FLOAT DI KANAN ATAS --}}
        <button x-on:click="closePreview()" class="fixed top-6 right-6 md:top-10 md:right-10 w-12 h-12 rounded-full bg-slate-800 hover:bg-red-500 text-white border border-white/20 flex items-center justify-center transition-colors shadow-2xl outline-none z-[100002]">
            <i class="mdi mdi-close text-2xl"></i>
        </button>

        {{-- Wrapper Flex Center --}}
        <div x-show="isPreviewOpen"
             x-transition:enter="transition ease-out duration-400 transform"
             x-transition:enter-start="opacity-0 translate-y-12 scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             x-transition:leave="transition ease-in duration-300"
             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
             x-transition:leave-end="opacity-0 translate-y-12 scale-95"
             class="relative z-[100001] w-full min-h-screen flex flex-col items-center justify-center py-10 px-4">

            {{-- BENTUK FISIK HP IPHONE DEWA --}}
            <div class="iphone-frame">
                <div class="iphone-power"></div>
                <div class="iphone-screen bg-white">

                    <div class="iphone-glare"></div>

                    {{-- DYNAMIC ISLAND --}}
                    <div class="iphone-island">
                        <div class="island-camera"></div>
                    </div>

                    {{-- Status Bar Text --}}
                    <div class="h-8 w-full bg-transparent flex justify-between items-center px-7 pt-1 text-[10px] font-black z-[101] text-white absolute top-0 pointer-events-none">
                        <span x-text="currentTime"></span>
                        <div class="flex gap-1.5"><i class="mdi mdi-signal"></i><i class="mdi mdi-wifi"></i><i class="mdi mdi-battery"></i></div>
                    </div>

                    {{-- KONTEN LAYAR DALAM HP --}}
                    <div class="flex-1 overflow-y-auto hide-scrollbar bg-slate-50 flex flex-col pb-10 relative z-10 pt-2">

                        {{-- HEADER TOKO --}}
                        <div class="h-48 p-4 text-white flex flex-col justify-end relative shadow-lg pt-12" :class="activeTemplate ? activeTemplate.hc : 'bg-slate-800'">
                            <div class="absolute inset-0 opacity-20 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')]"></div>

                            <div class="absolute top-10 left-4 right-4 flex justify-between items-center z-10">
                                <i class="mdi mdi-arrow-left text-lg"></i>
                                <div class="bg-black/20 rounded-full px-3 py-1.5 flex items-center gap-2 text-[10px] w-3/5 backdrop-blur-sm border border-white/20"><i class="mdi mdi-magnify text-white/70"></i> Cari di toko</div>
                                <i class="mdi mdi-dots-vertical text-lg"></i>
                            </div>

                            <div class="relative z-10 flex items-center gap-3">
                                <div class="w-14 h-14 rounded-full border-2 border-white/50 bg-white/20 backdrop-blur-sm flex items-center justify-center text-2xl font-black shadow-lg">
                                    {{ strtoupper(substr(optional($toko ?? null)->nama_toko ?? 'T', 0, 1)) }}
                                </div>
                                <div class="flex-1">
                                    <div class="text-sm font-black truncate drop-shadow-md">{{ optional($toko ?? null)->nama_toko ?? 'NAMA TOKO' }}</div>
                                    <div class="text-[9px] font-bold text-white/90 flex items-center gap-1 mt-0.5 drop-shadow"><i class="mdi mdi-star text-amber-400"></i> 5.0 | 1.9K Pengikut</div>
                                </div>
                            </div>
                        </div>

                        {{-- TABS --}}
                        <div class="flex bg-white shadow-sm border-b border-slate-100 z-20 sticky top-0">
                            <div class="flex-1 py-3 text-center text-[12px] font-black border-b-2 border-blue-600 text-blue-600">Beranda</div>
                            <div class="flex-1 py-3 text-center text-[12px] font-bold text-slate-500">Produk</div>
                        </div>

                        {{-- RENDER KOMPONEN DINAMIS MENGGUNAKAN FLEX ORDER --}}
                        <div class="p-3 flex flex-col gap-3">

                            {{-- Komponen Banner --}}
                            <div x-show="hasComp('banner')" :style="`order: ${getOrder('banner')}`" class="w-full h-40 rounded-2xl shadow-md flex flex-col items-center justify-center p-4 border border-white/20" :class="activeTemplate ? activeTemplate.ac : 'bg-slate-200'">
                                <h3 class="text-white text-2xl font-black text-center leading-tight drop-shadow-md italic">PROMO SPESIAL</h3>
                                <button class="px-5 py-2 mt-2 bg-white text-slate-900 text-[10px] font-black rounded-full shadow-lg">Klaim Sekarang</button>
                            </div>

                            {{-- Komponen Carousel --}}
                            <div x-show="hasComp('carousel')" :style="`order: ${getOrder('carousel')}`" class="w-full h-44 rounded-2xl shadow-md flex flex-col items-center justify-center relative overflow-hidden" :class="activeTemplate ? activeTemplate.ac : 'bg-slate-200'">
                                <div class="absolute inset-0 bg-black opacity-30"></div>
                                <i class="mdi mdi-view-carousel text-5xl text-white relative z-10 drop-shadow"></i>
                                <div class="flex gap-1.5 absolute bottom-3 z-10"><div class="w-2 h-2 bg-white rounded-full"></div><div class="w-2 h-2 bg-white/40 rounded-full"></div><div class="w-2 h-2 bg-white/40 rounded-full"></div></div>
                            </div>

                            {{-- Komponen Video --}}
                            <div x-show="hasComp('video')" :style="`order: ${getOrder('video')}`" class="w-full h-48 bg-slate-900 rounded-2xl shadow-md flex items-center justify-center relative overflow-hidden border border-slate-700">
                                <img src="https://images.unsplash.com/photo-1441986300917-64674bd600d8?auto=format&fit=crop&w=400&q=80" class="absolute inset-0 w-full h-full object-cover opacity-60">
                                <i class="mdi mdi-play-circle text-red-500 text-6xl drop-shadow-lg relative z-10"></i>
                            </div>

                            {{-- Komponen Kategori --}}
                            <div x-show="hasComp('kategori')" :style="`order: ${getOrder('kategori')}`" class="bg-white rounded-2xl p-4 shadow-sm border border-slate-100">
                                <h4 class="text-[12px] font-black text-slate-800 mb-3"><i class="mdi mdi-shape text-blue-500"></i> Kategori Pilihan</h4>
                                <div class="grid grid-cols-4 gap-2">
                                    <div class="flex flex-col items-center gap-1.5"><div class="w-14 h-14 rounded-2xl flex items-center justify-center bg-slate-50 border border-slate-100 shadow-inner"><i class="mdi mdi-shoe-sneaker text-slate-400 text-2xl"></i></div><span class="text-[9px] font-bold text-slate-600">Sepatu</span></div>
                                    <div class="flex flex-col items-center gap-1.5"><div class="w-14 h-14 rounded-2xl flex items-center justify-center bg-slate-50 border border-slate-100 shadow-inner"><i class="mdi mdi-tshirt-crew text-slate-400 text-2xl"></i></div><span class="text-[9px] font-bold text-slate-600">Pakaian</span></div>
                                    <div class="flex flex-col items-center gap-1.5"><div class="w-14 h-14 rounded-2xl flex items-center justify-center bg-slate-50 border border-slate-100 shadow-inner"><i class="mdi mdi-watch text-slate-400 text-2xl"></i></div><span class="text-[9px] font-bold text-slate-600">Aksesoris</span></div>
                                    <div class="flex flex-col items-center gap-1.5"><div class="w-14 h-14 rounded-2xl flex items-center justify-center bg-slate-50 border border-slate-100 shadow-inner"><i class="mdi mdi-dots-horizontal text-slate-400 text-2xl"></i></div><span class="text-[9px] font-bold text-slate-600">Lainnya</span></div>
                                </div>
                            </div>

                            {{-- Komponen Produk 1 --}}
                            <div x-show="hasComp('produk1')" :style="`order: ${getOrder('produk1')}`" class="bg-white rounded-2xl p-4 shadow-sm border border-slate-100 mt-2">
                                <div class="flex justify-between items-center mb-3">
                                    <h4 class="text-[12px] font-black text-slate-800"><i class="mdi mdi-fire text-red-500"></i> Rekomendasi Utama</h4>
                                    <span class="text-[9px] font-bold text-slate-400">LIHAT SEMUA <i class="mdi mdi-chevron-right"></i></span>
                                </div>
                                <div class="grid grid-cols-2 gap-3">
                                    <div class="border border-slate-100 rounded-xl p-2 shadow-sm bg-slate-50">
                                        <div class="h-32 bg-white rounded-lg mb-2 flex items-center justify-center border border-slate-100"><i class="mdi mdi-image-outline text-3xl text-slate-300"></i></div>
                                        <div class="text-[10px] font-bold text-slate-700 truncate">Produk Unggulan A</div><div class="text-[12px] font-black text-blue-600 mt-1">Rp 150.000</div>
                                    </div>
                                    <div class="border border-slate-100 rounded-xl p-2 shadow-sm bg-slate-50">
                                        <div class="h-32 bg-white rounded-lg mb-2 flex items-center justify-center border border-slate-100"><i class="mdi mdi-image-outline text-3xl text-slate-300"></i></div>
                                        <div class="text-[10px] font-bold text-slate-700 truncate">Produk Unggulan B</div><div class="text-[12px] font-black text-blue-600 mt-1">Rp 250.000</div>
                                    </div>
                                </div>
                            </div>

                            {{-- Komponen Produk 2 --}}
                            <div x-show="hasComp('produk2')" :style="`order: ${getOrder('produk2')}`" class="bg-white rounded-2xl p-4 shadow-sm border border-slate-100">
                                <div class="flex justify-between items-center mb-3">
                                    <h4 class="text-[12px] font-black text-slate-800"><i class="mdi mdi-tag text-blue-500"></i> Pilihan Lainnya</h4>
                                </div>
                                <div class="grid grid-cols-2 gap-3">
                                    <div class="border border-slate-100 rounded-xl p-2 shadow-sm bg-slate-50">
                                        <div class="h-32 bg-white rounded-lg mb-2 flex items-center justify-center border border-slate-100"><i class="mdi mdi-image-outline text-3xl text-slate-300"></i></div>
                                        <div class="text-[10px] font-bold text-slate-700 truncate">Produk Regular C</div><div class="text-[12px] font-black text-blue-600 mt-1">Rp 99.000</div>
                                    </div>
                                    <div class="border border-slate-100 rounded-xl p-2 shadow-sm bg-slate-50">
                                        <div class="h-32 bg-white rounded-lg mb-2 flex items-center justify-center border border-slate-100"><i class="mdi mdi-image-outline text-3xl text-slate-300"></i></div>
                                        <div class="text-[10px] font-bold text-slate-700 truncate">Produk Regular D</div><div class="text-[12px] font-black text-blue-600 mt-1">Rp 120.000</div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<script>
    function templateManager() {
        return {
            templates: TEMPLATES_DATA,
            isPreviewOpen: false,
            activeTemplate: null,
            currentTime: '12:30',

            initPage() {
                setInterval(() => {
                    const now = new Date();
                    this.currentTime = now.getHours().toString().padStart(2, '0') + ':' + now.getMinutes().toString().padStart(2, '0');
                }, 1000);
            },

            hasComp(compName) {
                if(!this.activeTemplate) return false;
                return this.activeTemplate.layout.includes(compName);
            },
            getOrder(compName) {
                if(!this.activeTemplate) return 99;
                return this.activeTemplate.layout.indexOf(compName);
            },

            blankCanvas() {
                Swal.fire({
                    title: 'Membuat Kanvas Kosong',
                    text: 'Menyiapkan ruang kerja tanpa template...',
                    icon: 'success',
                    timer: 1500,
                    showConfirmButton: false,
                    customClass: { popup: 'rounded-2xl' }
                }).then(() => {
                    // MENGGUNAKAN ROUTE EDITOR DRAG & DROP
                    window.location.href = "{{ route('seller.shop.decoration.editor') }}?tpl=blank";
                });
            },

            openPreview(id) {
                this.activeTemplate = this.templates.find(t => t.id === id);
                this.isPreviewOpen = true;
                // Nonaktifkan scroll pada body aslinya agar scroll hanya terjadi di dalam modal
                document.body.style.overflow = 'hidden';
            },

            closePreview() {
                this.isPreviewOpen = false;
                document.body.style.overflow = 'auto';
                setTimeout(() => { this.activeTemplate = null; }, 300);
            },

            applyTemplate(id) {
                const tpl = this.templates.find(t => t.id === id);
                Swal.fire({
                    title: 'Terapkan Template?',
                    text: `Toko Anda akan menggunakan tema "${tpl.name}".`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#2563eb',
                    confirmButtonText: 'Gunakan Tema Ini',
                    cancelButtonText: 'Batal',
                    customClass: { popup: 'rounded-2xl' }
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({ title: 'Menyiapkan Kanvas...', html: 'Menerapkan struktur layout, mohon tunggu.', allowOutsideClick: false, didOpen: () => { Swal.showLoading(); } });
                        // MENGGUNAKAN ROUTE EDITOR DRAG & DROP
                        setTimeout(() => { window.location.href = "{{ route('seller.shop.decoration.editor') }}?tpl=" + id; }, 1500);
                    }
                });
            }
        }
    }
</script>
</body>
</html>

