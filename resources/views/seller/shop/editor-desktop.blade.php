<!DOCTYPE html>
<html lang="id">
<head>
    <link rel="icon" href="{{ asset('logopondasikita.png') }}" type="image/png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editor Situs Web Toko - Pondasikita</title>
    
    {{-- WAJIB ADA UNTUK AJAX --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Font & Icons --}}
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@mdi/font@7.2.96/css/materialdesignicons.min.css">

    {{-- Tailwind CSS Standalone --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: { extend: { fontFamily: { sans: ['Inter', 'sans-serif'] } } }
        }
    </script>

    <style>
        [x-cloak] { display: none !important; }
        body { margin: 0; overflow: hidden; background-color: #f1f5f9; }

        /* Scrollbar Modern */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
        .hide-scrollbar::-webkit-scrollbar { display: none; }
        .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

        /* Sortable Drag & Drop Effect */
        .sortable-ghost { opacity: 0.5; background: #eff6ff !important; border: 2px dashed #2563eb !important; border-radius: 12px; overflow: hidden; min-height: 100px; }
        .sortable-drag { cursor: grabbing !important; transform: scale(1.02); box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25); z-index: 9999; }

        /* Canvas Item Behavior */
        .canvas-item { position: relative; border: 2px solid transparent; transition: all 0.2s ease; border-radius: 12px; background: white; margin-bottom: 16px; }
        .canvas-item:hover { border-color: #cbd5e1; cursor: pointer; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.05); }
        .canvas-item.is-active { border-color: #2563eb; box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.15); z-index: 10; }

        /* Modal Animation */
        .modal-enter { animation: modalFadeIn 0.3s cubic-bezier(0.4, 0, 0.2, 1) forwards; }
        @keyframes modalFadeIn { from { opacity: 0; transform: scale(0.95) translateY(10px); } to { opacity: 1; transform: scale(1) translateY(0); } }
    </style>

    {{-- Scripts: AlpineJS, SortableJS, SweetAlert2 --}}
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/focus@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body x-data="desktopEditor()" x-init="initEditor()" x-cloak class="h-screen w-screen flex flex-col text-slate-800" :class="isPreviewMode ? 'bg-slate-900' : ''">

    {{-- DATA TOKO --}}
    @php
        $toko = optional(Auth::user()->toko ?? null);
        $tokoName = $toko->nama_toko ?? 'Nama Toko Saya';
        $savedDecoration = !empty($toko->dekorasi_desktop) ? $toko->dekorasi_desktop : 'null';
    @endphp

    {{-- INPUT UPLOAD TERSEMBUNYI --}}
    <input type="file" x-ref="headerUploader" class="hidden" accept="image/png, image/jpeg, image/jpg" @change="handleHeaderUpload">
    <input type="file" x-ref="componentUploader" class="hidden" accept="image/png, image/jpeg, image/jpg" @change="handleComponentUpload">
    <input type="file" x-ref="videoUploader" class="hidden" accept="video/mp4, video/webm" @change="handleVideoUpload">

    {{-- ==========================================
         TOPBAR NAVIGATION
         ========================================== --}}
    <header class="h-16 bg-white border-b border-slate-200 flex justify-between items-center px-6 flex-shrink-0 z-30 shadow-sm transition-all duration-300">
        <div class="flex items-center gap-4">
            <a x-show="!isPreviewMode" href="{{ route('seller.shop.decoration') }}" class="w-10 h-10 flex items-center justify-center rounded-full bg-slate-100 hover:bg-slate-200 text-slate-600 transition-colors outline-none">
                <i class="mdi mdi-arrow-left text-xl"></i>
            </a>
            <button x-show="isPreviewMode" @click="togglePreview()" class="w-10 h-10 flex items-center justify-center rounded-full bg-red-100 hover:bg-red-200 text-red-600 transition-colors outline-none">
                <i class="mdi mdi-close text-xl"></i>
            </button>
            <div>
                <h1 class="font-black text-slate-800 text-lg leading-tight flex items-center gap-2">
                    Editor Situs Web Desktop <i class="mdi mdi-check-decagram text-blue-500 text-base" x-show="!isPreviewMode"></i>
                </h1>
                <div class="text-[11px] font-bold text-slate-500 flex items-center gap-1">
                    Status: <span class="text-emerald-600 uppercase tracking-wider" x-text="isPreviewMode ? 'MODE PRATINJAU LIVE' : 'SEDANG MENYUNTING'"></span>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <button @click="togglePreview()" class="px-5 py-2.5 text-sm font-bold rounded-xl transition-all outline-none flex items-center gap-2" :class="isPreviewMode ? 'bg-blue-50 text-blue-600 border border-blue-200' : 'bg-white border border-slate-200 text-slate-700 hover:border-blue-300 hover:text-blue-600'">
                <i class="mdi text-lg leading-none" :class="isPreviewMode ? 'mdi-eye-off' : 'mdi-eye'"></i>
                <span x-text="isPreviewMode ? 'Kembali ke Editor' : 'Pratinjau Situs'"></span>
            </button>
            <div class="w-px h-6 bg-slate-200 mx-2" x-show="!isPreviewMode"></div>
            <button x-show="!isPreviewMode" @click="tampilkan()" class="px-6 py-2.5 text-sm font-bold text-white bg-blue-600 hover:bg-blue-700 rounded-xl shadow-lg shadow-blue-600/30 transition-all outline-none flex items-center gap-2">
                <i class="mdi mdi-rocket-launch text-lg leading-none"></i> Tayangkan Toko
            </button>
        </div>
    </header>

    {{-- ==========================================
         AREA KERJA UTAMA (3 KOLOM)
         ========================================== --}}
    <main class="flex-1 flex min-h-0 relative">

        {{-- KOLOM 1: PALET KOMPONEN --}}
        <aside x-show="!isPreviewMode" x-transition.opacity.duration.300ms class="w-[320px] bg-white border-r border-slate-200 flex flex-col flex-shrink-0 z-20 shadow-[4px_0_24px_rgba(0,0,0,0.02)]">
            <div class="p-5 border-b border-slate-100 bg-slate-50/80">
                <div class="relative">
                    <i class="mdi mdi-magnify absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-lg"></i>
                    <input type="text" placeholder="Cari modul visual..." class="w-full pl-12 pr-4 py-3 text-sm font-bold border border-slate-200 rounded-xl bg-white focus:ring-2 focus:ring-blue-100 focus:border-blue-500 outline-none transition-all placeholder:font-medium">
                </div>
            </div>

            <div class="flex-1 overflow-y-auto hide-scrollbar p-5">
                <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4">Pilih & Tarik Komponen</h3>

                {{-- AREA DRAG SOURCE --}}
                <div class="grid grid-cols-2 gap-4" x-ref="paletteList">

                    {{-- Item: Banner --}}
                    <div @click="addComponent('banner')" class="border border-slate-200 rounded-2xl p-4 flex flex-col items-center justify-center text-center cursor-grab hover:border-blue-400 hover:shadow-md transition-all bg-white group" data-type="banner">
                        <div class="w-12 h-12 bg-blue-50 text-blue-600 flex items-center justify-center rounded-xl mb-3 group-hover:scale-110 transition-transform"><i class="mdi mdi-image-area text-2xl"></i></div>
                        <span class="text-[11px] font-black text-slate-700">Banner Lebar</span>
                    </div>

                    {{-- Item: Carousel Grid --}}
                    <div @click="addComponent('carousel')" class="border border-slate-200 rounded-2xl p-4 flex flex-col items-center justify-center text-center cursor-grab hover:border-indigo-400 hover:shadow-md transition-all bg-white group" data-type="carousel">
                        <div class="w-12 h-12 bg-indigo-50 text-indigo-600 flex items-center justify-center rounded-xl mb-3 group-hover:scale-110 transition-transform"><i class="mdi mdi-view-grid-plus text-2xl"></i></div>
                        <span class="text-[11px] font-black text-slate-700">Grid Foto</span>
                    </div>

                    {{-- Item: Video --}}
                    <div @click="addComponent('video')" class="border border-slate-200 rounded-2xl p-4 flex flex-col items-center justify-center text-center cursor-grab hover:border-red-400 hover:shadow-md transition-all bg-white group" data-type="video">
                        <div class="w-12 h-12 bg-red-50 text-red-600 flex items-center justify-center rounded-xl mb-3 group-hover:scale-110 transition-transform"><i class="mdi mdi-youtube text-2xl"></i></div>
                        <span class="text-[11px] font-black text-slate-700">Video Promo</span>
                    </div>

                    {{-- Item: Produk --}}
                    <div @click="addComponent('produk')" class="border border-slate-200 rounded-2xl p-4 flex flex-col items-center justify-center text-center cursor-grab hover:border-emerald-400 hover:shadow-md transition-all bg-white group" data-type="produk">
                        <div class="w-12 h-12 bg-emerald-50 text-emerald-600 flex items-center justify-center rounded-xl mb-3 group-hover:scale-110 transition-transform"><i class="mdi mdi-storefront text-2xl"></i></div>
                        <span class="text-[11px] font-black text-slate-700">Daftar Produk</span>
                    </div>

                    {{-- Item: Kategori --}}
                    <div @click="addComponent('kategori')" class="col-span-2 border border-slate-200 rounded-2xl p-4 flex items-center gap-5 cursor-grab hover:border-amber-400 hover:shadow-md transition-all bg-white group" data-type="kategori">
                        <div class="w-12 h-12 bg-amber-50 text-amber-600 flex items-center justify-center rounded-xl group-hover:scale-110 transition-transform"><i class="mdi mdi-shape text-2xl"></i></div>
                        <div class="text-left flex-1">
                            <div class="text-xs font-black text-slate-800">Menu Kategori</div>
                            <div class="text-[10px] text-slate-400 font-bold mt-1">Navigasi halaman cepat</div>
                        </div>
                    </div>

                </div>
            </div>

            <div class="p-5 border-t border-slate-100 bg-white">
                <button @click="clearCanvas()" class="w-full py-3.5 border-2 border-red-100 text-red-500 text-sm font-black rounded-xl hover:bg-red-50 transition-colors flex items-center justify-center gap-2 outline-none">
                    <i class="mdi mdi-delete-sweep text-xl leading-none"></i> Bersihkan Kanvas
                </button>
            </div>
        </aside>

        {{-- KOLOM 2: MOCKUP DESKTOP (AREA TENGAH) --}}
        <section class="flex-1 relative overflow-y-auto flex py-10 px-8 transition-colors duration-500 hide-scrollbar"
                 :class="isPreviewMode ? 'bg-slate-900 justify-center' : 'bg-slate-200 shadow-inner justify-center'" id="main-scroll-area">

            {{-- Container Browser --}}
            <div class="w-full max-w-[1200px] flex-shrink-0 bg-[#f4f6f8] rounded-xl shadow-[0_30px_60px_-15px_rgba(0,0,0,0.3)] border border-slate-300 flex flex-col relative overflow-hidden transition-all duration-300"
                 :class="isPreviewMode ? 'ring-4 ring-slate-700' : ''">

                {{-- Mockup Tab Browser --}}
                <div class="h-12 bg-slate-200 border-b border-slate-300 flex items-center px-4 gap-3 select-none sticky top-0 z-[100]">
                    <div class="flex gap-2">
                        <div class="w-3.5 h-3.5 rounded-full bg-red-400 shadow-inner"></div>
                        <div class="w-3.5 h-3.5 rounded-full bg-amber-400 shadow-inner"></div>
                        <div class="w-3.5 h-3.5 rounded-full bg-emerald-400 shadow-inner"></div>
                    </div>
                    <div class="ml-4 flex-1 max-w-2xl h-8 bg-white/70 rounded-lg flex items-center px-4 text-xs font-bold text-slate-500 shadow-sm border border-white">
                        <i class="mdi mdi-lock text-slate-400 mr-2 text-sm"></i> https://pondasikita.com/{{ Str::slug($tokoName) }}
                    </div>
                </div>

                {{-- Bodi Web Toko (Bisa di-scroll mandiri) --}}
                <div class="flex-1 w-full overflow-y-auto hide-scrollbar flex flex-col relative" id="canvas-scroll-container">

                    {{-- 1. HEADER TOKO DESKTOP --}}
                    <div class="relative h-[250px] lg:h-[300px] flex flex-col justify-end select-none flex-shrink-0 bg-cover bg-center border-b border-slate-200"
                         :class="!uploadedHeader ? templateHeaderColor : ''"
                         :style="uploadedHeader ? `background-image: url('${uploadedHeader}')` : ''">

                        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent"></div>
                        <div class="absolute inset-0 opacity-10 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')]" x-show="!uploadedHeader"></div>

                        <div class="relative z-10 w-full max-w-6xl mx-auto px-8 pb-8 flex items-end justify-between">
                            <div class="flex gap-6 items-center text-white">
                                <div class="w-28 h-28 bg-white rounded-full p-1.5 shadow-2xl border border-white/20">
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($tokoName) }}&background=random" class="w-full h-full rounded-full object-cover">
                                </div>
                                <div class="mb-2">
                                    <h3 class="text-3xl font-black drop-shadow-lg mb-2">{{ $tokoName }}</h3>
                                    <div class="text-sm font-bold text-white/90 flex items-center gap-4 drop-shadow">
                                        <span class="flex items-center gap-1"><i class="mdi mdi-star text-yellow-400 text-lg leading-none"></i> 5.0 Penilaian</span>
                                        <span class="opacity-50">|</span>
                                        <span class="flex items-center gap-1"><i class="mdi mdi-account-group text-lg leading-none"></i> 1.9K Pengikut</span>
                                        <span class="opacity-50">|</span>
                                        <span class="flex items-center gap-1"><i class="mdi mdi-chat-processing text-lg leading-none"></i> 98% Performa Chat</span>
                                    </div>
                                </div>
                            </div>
                            <div class="flex gap-3 mb-2">
                                <button class="px-6 py-2.5 bg-transparent border-2 border-white text-white text-sm font-bold rounded-lg hover:bg-white hover:text-slate-900 transition-colors"><i class="mdi mdi-chat-processing-outline mr-1"></i> Chat</button>
                                <button class="px-8 py-2.5 bg-blue-600 border-2 border-blue-600 text-white text-sm font-bold rounded-lg shadow-lg hover:bg-blue-700 transition-colors"><i class="mdi mdi-plus mr-1"></i> Ikuti Toko</button>
                            </div>
                        </div>
                    </div>

                    {{-- 2. TABS MENU DESKTOP --}}
                    <div class="bg-white border-b border-slate-200 sticky top-0 z-40 shadow-sm flex-shrink-0">
                        <div class="max-w-6xl mx-auto flex px-4">
                            <div class="px-8 py-4 text-center text-[15px] font-black border-b-[3px] border-blue-600 text-blue-600 cursor-pointer">Halaman Depan</div>
                            <div class="px-8 py-4 text-center text-[15px] font-bold text-slate-500 hover:text-slate-800 cursor-pointer transition-colors">Semua Produk</div>
                            <div class="px-8 py-4 text-center text-[15px] font-bold text-slate-500 hover:text-slate-800 cursor-pointer transition-colors">Kategori</div>
                        </div>
                    </div>

                    {{-- 3. AREA KANVAS DROPZONE --}}
                    <div class="relative flex-1 flex flex-col w-full bg-[#f4f6f8] items-center pt-8 pb-32">

                        {{-- Tampilan Saat Kosong --}}
                        <div x-show="canvasItems.length === 0" class="absolute inset-0 flex flex-col items-center justify-center p-10 text-center opacity-70 select-none z-0" :class="isPreviewMode ? 'hidden' : ''">
                            <div class="w-24 h-24 mx-auto bg-white rounded-full flex items-center justify-center border border-slate-200 shadow-sm mb-6 text-blue-400">
                                <i class="mdi mdi-tray-arrow-down text-5xl"></i>
                            </div>
                            <p class="text-xl font-black text-slate-700 mb-2">Kanvas Masih Kosong</p>
                            <p class="text-sm text-slate-500 font-medium px-4 max-w-sm">Tarik modul dari panel kiri dan lepaskan di area ini untuk mulai mendesain tampilan website tokomu.</p>
                        </div>

                        {{-- RENDER ALPINE LOOP --}}
                        <div x-ref="canvasDropzone" class="relative z-10 w-full max-w-6xl flex flex-col gap-6 px-4 md:px-8 min-h-[400px]">

                            <template x-for="(item, index) in canvasItems" :key="item.uid">
                                <div class="canvas-item group/canvas w-full shadow-sm hover:shadow-md"
                                     :class="{ 'is-active': activeItemId === item.uid && !isPreviewMode }"
                                     @click="!isPreviewMode ? setActive(item) : null"
                                     :data-uid="item.uid">

                                    {{-- Tombol Hapus Cepat --}}
                                    <button x-show="activeItemId === item.uid && !isPreviewMode" @click.stop="removeItem(index)" class="absolute -top-4 -right-4 w-9 h-9 bg-red-500 text-white rounded-full shadow-xl z-[60] flex items-center justify-center hover:bg-red-600 outline-none transform transition-transform hover:scale-110 border-2 border-white">
                                        <i class="mdi mdi-close text-lg font-bold"></i>
                                    </button>

                                    {{-- 1. RENDER BANNER TOKO (Slider Up to 3 Images) --}}
                                    <div x-show="item.type === 'banner'" class="w-full flex flex-col items-center justify-center relative overflow-hidden rounded-xl bg-cover bg-center"
                                         :class="[item.config.images.length === 0 ? templateAccentColor : '', item.config.ratio === '16:9' ? 'aspect-[16/9]' : (item.config.ratio === '3:1' ? 'aspect-[3/1]' : 'aspect-[4/1]')]"
                                         :style="item.config.images.length > 0 ? `background-image: url('${item.config.images[0]}')` : ''">

                                        <div class="absolute inset-0 opacity-10 bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')]" x-show="item.config.images.length === 0"></div>
                                        <div class="absolute inset-0 bg-black/20" x-show="item.config.images.length > 0"></div>

                                        <div class="relative z-10 p-8 w-full h-full flex flex-col items-center justify-center">
                                            <h3 class="font-black text-center drop-shadow-lg italic w-full truncate px-8"
                                                :class="item.config.ratio === '16:9' ? 'text-5xl' : 'text-4xl'"
                                                :style="`color: ${item.config.textColor}`"
                                                x-text="item.config.title" x-show="item.config.title"></h3>
                                        </div>

                                        {{-- Indikator Dots --}}
                                        <div x-show="item.config.images.length > 1" class="absolute bottom-5 flex gap-2.5 z-10">
                                            <template x-for="(img, i) in item.config.images">
                                                <div class="w-3 h-3 rounded-full shadow-md border border-black/10" :class="i === 0 ? 'bg-white' : 'bg-white/50'"></div>
                                            </template>
                                        </div>
                                    </div>

                                    {{-- 2. RENDER BANYAK FOTO (GRID MURNI DESKTOP) --}}
                                    <div x-show="item.type === 'carousel'" class="w-full flex flex-col overflow-hidden rounded-xl border border-slate-200 bg-white">
                                        @php
                                            // Di editor kita pakai simulasi Alpine untuk membuang slot kosong
                                        @endphp
                                        <div class="grid gap-0.5 w-full bg-slate-200" 
                                             :class="Math.min(item.config.images.length || 1, parseInt(item.config.gridType || 3)) === 2 ? 'grid-cols-2' : 
                                                     (Math.min(item.config.images.length || 1, parseInt(item.config.gridType || 3)) === 1 ? 'grid-cols-1' :
                                                     (Math.min(item.config.images.length || 1, parseInt(item.config.gridType || 3)) === 4 ? 'grid-cols-4' : 
                                                     (Math.min(item.config.images.length || 1, parseInt(item.config.gridType || 3)) >= 5 ? 'grid-cols-5' : 'grid-cols-3')))">
                                            
                                            <template x-for="(img, idx) in item.config.images" :key="idx">
                                                <div class="aspect-square bg-slate-50 overflow-hidden relative group/img">
                                                    <img :src="img" class="absolute inset-0 w-full h-full object-cover transition-transform duration-500 group-hover/img:scale-110">
                                                </div>
                                            </template>

                                            <template x-if="item.config.images.length === 0">
                                                <div class="aspect-[21/9] bg-slate-50 flex flex-col items-center justify-center text-slate-300">
                                                    <i class="mdi mdi-view-grid-plus text-5xl mb-2"></i>
                                                    <span class="text-xs font-bold">Grid Foto Kosong - Tambahkan Foto di Sidebar</span>
                                                </div>
                                            </template>
                                        </div>
                                    </div>

                                    {{-- 3. RENDER VIDEO DESKTOP --}}
                                    <div x-show="item.type === 'video'" class="w-full flex flex-col bg-white rounded-xl p-6">
                                        <h4 class="text-lg font-black mb-5 px-2 truncate w-full border-l-4 border-blue-500 pl-3"
                                            :style="`color: ${item.config.textColor}`"
                                            x-text="item.config.title" x-show="item.config.title"></h4>

                                        <div class="w-full aspect-[21/9] bg-slate-900 flex flex-col items-center justify-center relative overflow-hidden rounded-xl">
                                            
                                            <template x-if="item.config.videoSource === 'youtube' && item.config.videoUrl">
                                                <div class="absolute inset-0 w-full h-full bg-slate-900">
                                                    <template x-if="getYoutubeEmbedUrl(item.config.videoUrl)">
                                                        <iframe 
                                                            :src="getYoutubeEmbedUrl(item.config.videoUrl)" 
                                                            class="w-full h-full border-0 pointer-events-none" 
                                                            allow="autoplay; encrypted-media" 
                                                            allowfullscreen>
                                                        </iframe>
                                                    </template>
                                                    <template x-if="!getYoutubeEmbedUrl(item.config.videoUrl)">
                                                        <div class="absolute inset-0 flex flex-col items-center justify-center bg-slate-800/80 text-white">
                                                            <i class="mdi mdi-link-variant-remove text-[60px] mb-2 text-slate-400"></i>
                                                            <span class="text-sm font-bold text-slate-300">Tautan YouTube Tidak Valid</span>
                                                        </div>
                                                    </template>
                                                </div>
                                            </template>

                                            <template x-if="item.config.videoSource === 'local' && item.config.videoFile">
                                                <video :src="item.config.videoFile" class="absolute inset-0 w-full h-full object-cover" autoplay muted loop></video>
                                            </template>

                                            <template x-if="(!item.config.videoUrl && item.config.videoSource === 'youtube') || (!item.config.videoFile && item.config.videoSource === 'local')">
                                                <div class="flex flex-col items-center opacity-50">
                                                    <i class="mdi mdi-video-outline text-white text-[80px] mb-3"></i>
                                                    <span class="text-base text-white font-bold" x-text="item.config.videoSource === 'local' ? 'Video Belum Diunggah' : 'Tautan Youtube Kosong'"></span>
                                                </div>
                                            </template>
                                        </div>
                                    </div>

                                    {{-- 4. RENDER KATEGORI DESKTOP --}}
                                    <div x-show="item.type === 'kategori'" style="display: none;" class="p-6 bg-white rounded-xl">
                                        <h4 class="text-lg font-black mb-6 border-l-4 border-blue-500 pl-3 truncate w-full"
                                            :style="`color: ${item.config.textColor}`"
                                            x-text="item.config.title" x-show="item.config.title"></h4>

                                        <div class="grid grid-cols-6 md:grid-cols-8 gap-4">
                                            <div class="flex flex-col items-center gap-3 cursor-pointer group/cat"><div class="w-20 h-20 rounded-full bg-blue-50 text-blue-500 flex items-center justify-center border border-blue-100 group-hover/cat:bg-blue-500 group-hover/cat:text-white transition-all group-hover/cat:shadow-md"><i class="mdi mdi-tshirt-crew text-4xl"></i></div><span class="text-sm font-bold text-slate-600 group-hover/cat:text-blue-600">Pakaian</span></div>
                                            <div class="flex flex-col items-center gap-3 cursor-pointer group/cat"><div class="w-20 h-20 rounded-full bg-rose-50 text-rose-500 flex items-center justify-center border border-rose-100 group-hover/cat:bg-rose-500 group-hover/cat:text-white transition-all group-hover/cat:shadow-md"><i class="mdi mdi-shoe-sneaker text-4xl"></i></div><span class="text-sm font-bold text-slate-600 group-hover/cat:text-rose-600">Sepatu</span></div>
                                            <div class="flex flex-col items-center gap-3 cursor-pointer group/cat"><div class="w-20 h-20 rounded-full bg-emerald-50 text-emerald-500 flex items-center justify-center border border-emerald-100 group-hover/cat:bg-emerald-500 group-hover/cat:text-white transition-all group-hover/cat:shadow-md"><i class="mdi mdi-watch text-4xl"></i></div><span class="text-sm font-bold text-slate-600 group-hover/cat:text-emerald-600">Aksesoris</span></div>
                                            <div class="flex flex-col items-center gap-3 cursor-pointer group/cat"><div class="w-20 h-20 rounded-full bg-amber-50 text-amber-500 flex items-center justify-center border border-amber-100 group-hover/cat:bg-amber-500 group-hover/cat:text-white transition-all group-hover/cat:shadow-md"><i class="mdi mdi-bag-personal text-4xl"></i></div><span class="text-sm font-bold text-slate-600 group-hover/cat:text-amber-600">Tas</span></div>
                                            <div class="flex flex-col items-center gap-3 cursor-pointer group/cat"><div class="w-20 h-20 rounded-full bg-purple-50 text-purple-500 flex items-center justify-center border border-purple-100 group-hover/cat:bg-purple-500 group-hover/cat:text-white transition-all group-hover/cat:shadow-md"><i class="mdi mdi-glasses text-4xl"></i></div><span class="text-sm font-bold text-slate-600 group-hover/cat:text-purple-600">Kacamata</span></div>
                                            <div class="flex flex-col items-center gap-3 cursor-pointer group/cat"><div class="w-20 h-20 rounded-full bg-slate-100 text-slate-500 flex items-center justify-center border border-slate-200 group-hover/cat:bg-slate-500 group-hover/cat:text-white transition-all group-hover/cat:shadow-md"><i class="mdi mdi-dots-horizontal text-4xl"></i></div><span class="text-sm font-bold text-slate-600 group-hover/cat:text-slate-800">Lainnya</span></div>
                                        </div>
                                    </div>

                                    {{-- 5. RENDER PRODUK DESKTOP (Grid 5/6 Kolom) --}}
                                    <div x-show="item.type === 'produk'" style="display: none;" class="py-6 px-4 bg-white rounded-xl">
                                        <div class="flex justify-between items-center mb-6">
                                            <h4 class="text-xl font-black uppercase tracking-wide truncate flex-1 border-l-4 border-blue-500 pl-3"
                                                :style="`color: ${item.config.textColor}`"
                                                x-text="item.config.title"></h4>
                                            <span class="text-sm font-bold text-blue-600 hover:text-blue-800 cursor-pointer transition-colors flex items-center">Lihat Semua Kategori Ini <i class="mdi mdi-chevron-right text-lg"></i></span>
                                        </div>

                                        {{-- Layout Scroll Samping (Horizontal) --}}
                                        <template x-if="item.config.layout === 'horizontal'">
                                            <div class="flex overflow-x-auto gap-4 pb-4 hide-scrollbar w-full">
                                                <template x-if="item.config.productSource === 'manual' && item.config.selectedProducts.length > 0">
                                                    <template x-for="prod in item.config.selectedProducts" :key="prod.id">
                                                        <div class="border border-slate-200 rounded-xl overflow-hidden shadow-sm bg-white hover:shadow-lg transition-all cursor-pointer group/prod min-w-[200px] w-[200px] flex-shrink-0">
                                                            <div class="aspect-square bg-slate-100 bg-cover bg-center" :style="`background-image: url('${prod.img}')`"></div>
                                                            <div class="p-4">
                                                                <div class="text-sm font-bold text-slate-700 truncate mb-1.5 group-hover/prod:text-blue-600 transition-colors" x-text="prod.name"></div>
                                                                <div class="text-base font-black text-orange-500 mb-2" x-text="prod.price"></div>
                                                                <div class="text-xs font-medium text-slate-400 flex items-center gap-1"><i class="mdi mdi-star text-yellow-400"></i> 4.9 | Terjual 100+</div>
                                                            </div>
                                                        </div>
                                                    </template>
                                                </template>
                                                <template x-if="item.config.productSource === 'auto' || item.config.selectedProducts.length === 0">
                                                    <template x-for="i in 6">
                                                        <div class="border border-slate-200 rounded-xl overflow-hidden shadow-sm bg-white min-w-[200px] w-[200px] flex-shrink-0">
                                                            <div class="aspect-square bg-slate-100 flex items-center justify-center"><i class="mdi mdi-image-outline text-5xl text-slate-300"></i></div>
                                                            <div class="p-4">
                                                                <div class="text-sm font-bold text-slate-700 truncate mb-1.5">Produk Terlaris Sistem</div>
                                                                <div class="text-base font-black text-orange-500 mb-2">Rp 199.000</div>
                                                                <div class="text-xs font-medium text-slate-400 flex items-center gap-1"><i class="mdi mdi-star text-slate-300"></i> - | 0 Terjual</div>
                                                            </div>
                                                        </div>
                                                    </template>
                                                </template>
                                            </div>
                                        </template>

                                        {{-- Layout Grid Kebawah (Vertical 5 Kolom) --}}
                                        <template x-if="item.config.layout === 'vertical'">
                                            <div class="grid grid-cols-5 gap-4 w-full">
                                                <template x-if="item.config.productSource === 'manual' && item.config.selectedProducts.length > 0">
                                                    <template x-for="prod in item.config.selectedProducts" :key="prod.id">
                                                        <div class="border border-slate-200 rounded-xl overflow-hidden shadow-sm bg-white hover:shadow-lg transition-all cursor-pointer group/prod">
                                                            <div class="aspect-square bg-slate-100 bg-cover bg-center" :style="`background-image: url('${prod.img}')`"></div>
                                                            <div class="p-4">
                                                                <div class="text-sm font-bold text-slate-700 truncate mb-1.5 group-hover/prod:text-blue-600 transition-colors" x-text="prod.name"></div>
                                                                <div class="text-base font-black text-orange-500 mb-2" x-text="prod.price"></div>
                                                                <div class="text-xs font-medium text-slate-400 flex items-center gap-1"><i class="mdi mdi-star text-yellow-400"></i> 4.9 | Terjual 100+</div>
                                                            </div>
                                                        </div>
                                                    </template>
                                                </template>

                                                <template x-if="item.config.productSource === 'auto' || item.config.selectedProducts.length === 0">
                                                    <template x-for="i in 10">
                                                        <div class="border border-slate-200 rounded-xl overflow-hidden shadow-sm bg-white hover:shadow-md transition-shadow">
                                                            <div class="aspect-square bg-slate-100 flex items-center justify-center"><i class="mdi mdi-image-outline text-5xl text-slate-300"></i></div>
                                                            <div class="p-4">
                                                                <div class="text-sm font-bold text-slate-700 truncate mb-1.5">Produk Terlaris Sistem</div>
                                                                <div class="text-base font-black text-orange-500 mb-2">Rp 199.000</div>
                                                                <div class="text-xs font-medium text-slate-400 flex items-center gap-1"><i class="mdi mdi-star text-slate-300"></i> - | 0 Terjual</div>
                                                            </div>
                                                        </div>
                                                    </template>
                                                </template>
                                            </div>
                                        </template>
                                    </div>

                                </div>

                                {{-- KONTROL MENGAMBANG (UP/DOWN) - Di luar container utama --}}
                                <div x-show="activeItemId === item.uid && !isPreviewMode" class="absolute -left-16 top-0 flex flex-col gap-2 z-[60]">
                                    <button @click.stop="moveUp(index)" class="w-12 h-12 bg-white border border-slate-200 shadow-md rounded-xl flex items-center justify-center text-slate-600 hover:text-blue-600 hover:bg-blue-50 disabled:opacity-30 disabled:hover:bg-white disabled:hover:text-slate-600 transition-all outline-none" :disabled="index === 0"><i class="mdi mdi-arrow-up text-2xl"></i></button>
                                    <button @click.stop="moveDown(index)" class="w-12 h-12 bg-white border border-slate-200 shadow-md rounded-xl flex items-center justify-center text-slate-600 hover:text-blue-600 hover:bg-blue-50 disabled:opacity-30 disabled:hover:bg-white disabled:hover:text-slate-600 transition-all outline-none" :disabled="index === canvasItems.length - 1"><i class="mdi mdi-arrow-down text-2xl"></i></button>
                                </div>
                            </template>
                        </div>
                    </div>

                </div>
            </div>
        </section>

        {{-- =======================================
             KOLOM 3: PANEL PENGATURAN KANAN
             ======================================= --}}
        <aside x-show="!isPreviewMode" x-transition.opacity.duration.300ms class="w-[360px] bg-white border-l border-slate-200 flex flex-col flex-shrink-0 z-20 shadow-[-4px_0_24px_rgba(0,0,0,0.03)]">

            <div class="p-5 border-b border-slate-100 bg-slate-50 flex justify-between items-center flex-shrink-0">
                <h3 class="text-sm font-black text-slate-800" x-text="activeItem ? getSettingTitle(activeItem.type) : 'Pengaturan Latar Toko'"></h3>
                <button x-show="activeItem" @click="activeItemId = null" class="w-8 h-8 rounded-lg bg-white border border-slate-200 text-slate-400 hover:text-red-500 hover:bg-red-50 flex items-center justify-center transition-colors outline-none"><i class="mdi mdi-close"></i></button>
            </div>

            <div class="p-6 overflow-y-auto flex-1 hide-scrollbar bg-white">

                {{-- JIKA KOSONG: PENGATURAN HEADER TOKO --}}
                <div x-show="!activeItem">
                    <div class="bg-blue-50 border border-blue-100 p-4 rounded-xl mb-6 flex gap-3">
                        <i class="mdi mdi-information text-blue-500 text-xl"></i>
                        <p class="text-[11px] text-blue-800 font-medium leading-relaxed">Upload gambar kustom untuk latar belakang Header Desktop Toko agar tampilan lebih profesional.</p>
                    </div>

                    <h5 class="text-sm font-black text-slate-800 mb-3">Gambar Header Latar Belakang</h5>

                    <div class="w-full aspect-[21/9] border-2 border-dashed border-slate-300 bg-slate-50 flex flex-col items-center justify-center relative cursor-pointer hover:border-blue-500 hover:bg-blue-50 transition-colors group rounded-2xl overflow-hidden mb-4 bg-cover bg-center"
                         @click="$refs.headerUploader.click()"
                         :style="uploadedHeader ? `background-image: url('${uploadedHeader}')` : ''">

                        <div class="relative z-10 flex flex-col items-center w-full h-full justify-center transition-opacity" :class="uploadedHeader ? 'opacity-0 hover:opacity-100 bg-black/60 backdrop-blur-sm' : ''">
                            <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center shadow-sm border border-slate-200 mb-2 group-hover:scale-110 group-hover:text-blue-600 transition-all text-slate-400">
                                <i class="mdi mdi-cloud-upload-outline text-2xl"></i>
                            </div>
                            <span class="text-xs font-bold" :class="uploadedHeader ? 'text-white' : 'text-slate-600'" x-text="uploadedHeader ? 'Ganti Gambar' : 'Pilih Gambar'"></span>
                        </div>
                    </div>

                    <button x-show="uploadedHeader" @click="uploadedHeader = null" class="w-full py-3 text-xs font-bold text-red-500 hover:bg-red-50 rounded-xl mb-5 transition-colors outline-none border border-red-100">Hapus Header Kustom</button>

                    <ul class="text-[11px] font-medium text-slate-500 list-disc pl-4 space-y-2">
                        <li>Rekomendasi ukuran: 1920 x 400 px</li>
                        <li>Maksimal file: 3 MB (JPG, PNG)</li>
                    </ul>
                </div>

                {{-- JIKA AKTIF: FORM DINAMIS KOMPONEN --}}
                <div x-show="activeItem !== null" style="display: none;" x-data="{ currentConfig: {} }" x-effect="if(activeItem) currentConfig = activeItem.config">

                    {{-- Input Judul & Warna Teks --}}
                    <div class="mb-6 bg-slate-50 border border-slate-200 p-5 rounded-xl">
                        <label class="block text-[11px] font-black text-slate-500 mb-2 uppercase tracking-wider">Teks Judul Seksi</label>
                        <input type="text" x-model="currentConfig.title" @input="updateItemConfig()" class="w-full px-4 py-3 border border-slate-300 rounded-lg bg-white text-sm font-bold text-slate-800 focus:border-blue-500 outline-none transition-all placeholder:text-slate-400 mb-4" placeholder="Kosongkan jika tanpa judul">

                        <label class="block text-[11px] font-black text-slate-500 mb-2 uppercase tracking-wider">Warna Teks Judul</label>
                        <div class="flex gap-3">
                            <button @click="currentConfig.textColor = '#1e293b'; updateItemConfig()" class="w-8 h-8 rounded-full border-2 shadow-sm outline-none" :class="currentConfig.textColor === '#1e293b' ? 'border-blue-500 scale-110' : 'border-slate-200 hover:scale-105'" style="background: #1e293b;"></button>
                            <button @click="currentConfig.textColor = '#ffffff'; updateItemConfig()" class="w-8 h-8 rounded-full border-2 shadow-sm outline-none" :class="currentConfig.textColor === '#ffffff' ? 'border-blue-500 scale-110' : 'border-slate-200 hover:scale-105'" style="background: #ffffff;"></button>
                            <button @click="currentConfig.textColor = '#2563eb'; updateItemConfig()" class="w-8 h-8 rounded-full border-2 shadow-sm outline-none" :class="currentConfig.textColor === '#2563eb' ? 'border-blue-500 scale-110' : 'border-white hover:scale-105'" style="background: #2563eb;"></button>
                            <button @click="currentConfig.textColor = '#ef4444'; updateItemConfig()" class="w-8 h-8 rounded-full border-2 shadow-sm outline-none" :class="currentConfig.textColor === '#ef4444' ? 'border-blue-500 scale-110' : 'border-white hover:scale-105'" style="background: #ef4444;"></button>
                        </div>
                    </div>

                    {{-- Form: Banner (Maks 3 Gambar/Slider) --}}
                    <div x-show="activeItem && activeItem.type === 'banner'">

                        <div class="mb-6">
                            <h5 class="text-sm font-black text-slate-800 mb-2">Pilih Layout Rasio</h5>
                            <div class="flex gap-2 bg-slate-50 p-1.5 rounded-lg border border-slate-200">
                                <button @click="currentConfig.ratio = '4:1'; updateItemConfig()" class="flex-1 py-2 text-xs font-bold rounded shadow-sm outline-none transition-all" :class="currentConfig.ratio === '4:1' ? 'bg-white text-blue-600 border border-slate-200' : 'text-slate-500 hover:bg-slate-100'">Lebar (4:1)</button>
                                <button @click="currentConfig.ratio = '3:1'; updateItemConfig()" class="flex-1 py-2 text-xs font-bold rounded outline-none transition-all" :class="currentConfig.ratio === '3:1' ? 'bg-white text-blue-600 border border-slate-200 shadow-sm' : 'text-slate-500 hover:bg-slate-100'">Medium (3:1)</button>
                                <button @click="currentConfig.ratio = '16:9'; updateItemConfig()" class="flex-1 py-2 text-xs font-bold rounded outline-none transition-all" :class="currentConfig.ratio === '16:9' ? 'bg-white text-blue-600 border border-slate-200 shadow-sm' : 'text-slate-500 hover:bg-slate-100'">Klasik (16:9)</button>
                            </div>
                        </div>

                        <div class="mb-5">
                            <div class="flex justify-between items-center mb-3">
                                <h5 class="text-sm font-black text-slate-800">Upload Foto Banner</h5>
                                <span class="text-[11px] font-bold px-2 py-1 bg-slate-100 rounded text-slate-600" x-text="currentConfig.images ? `${currentConfig.images.length} / 3` : '0 / 3'"></span>
                            </div>
                            <ul class="text-[11px] font-medium text-slate-500 list-disc pl-4 mb-5 space-y-1">
                                <li>Maksimal file: 2 MB (JPG, PNG) per gambar</li>
                                <li>Batas maksimal: 3 foto untuk efek slider.</li>
                            </ul>

                            {{-- List Gambar Uploaded --}}
                            <div class="space-y-3 mb-5" x-show="currentConfig.images && currentConfig.images.length > 0">
                                <template x-for="(img, imgIdx) in currentConfig.images" :key="imgIdx">
                                    <div class="flex gap-3 bg-slate-50 p-3 rounded-xl border border-slate-200 shadow-sm">
                                        <div class="w-20 h-12 rounded bg-slate-200 flex-shrink-0 bg-cover bg-center border border-slate-300" :style="`background-image: url('${img}')`"></div>
                                        <div class="flex-1 flex flex-col justify-center">
                                            <input type="text" placeholder="Tautan Tujuan (Opsional)" class="w-full bg-white border border-slate-200 text-[11px] font-bold rounded-lg px-3 py-2 outline-none focus:border-blue-400">
                                        </div>
                                        <button @click="removeComponentImage(imgIdx)" class="text-slate-400 hover:text-red-500 transition-colors px-2 outline-none"><i class="mdi mdi-delete text-lg"></i></button>
                                    </div>
                                </template>
                            </div>

                            {{-- Tombol Upload --}}
                            <button x-show="!currentConfig.images || currentConfig.images.length < 3"
                                    @click="$refs.componentUploader.click()"
                                    class="w-full py-4 border-2 border-dashed border-blue-300 bg-blue-50/50 text-blue-600 text-sm font-bold rounded-xl hover:bg-blue-100 hover:border-blue-400 transition-colors flex flex-col items-center justify-center gap-1 outline-none">
                                <i class="mdi mdi-plus-circle-outline text-2xl"></i> Tambah Foto Baru
                            </button>
                        </div>
                    </div>

                    {{-- Form: Carousel (Banyak Foto Grid) --}}
                    <div x-show="activeItem && activeItem.type === 'carousel'">

                        <div class="mb-6">
                            <h5 class="text-sm font-black text-slate-800 mb-2">Pilih Layout Kolom Grid</h5>
                            <p class="text-[11px] text-slate-500 mb-3">Tentukan jumlah foto yang ingin disejajarkan.</p>
                            <div class="flex gap-2 bg-slate-50 p-1.5 rounded-lg border border-slate-200">
                                <button @click="changeGridType('3')" class="flex-1 py-3 text-[11px] font-bold rounded shadow-sm outline-none transition-all flex flex-col items-center gap-1.5" :class="currentConfig.gridType === '3' ? 'bg-white text-blue-600 border border-slate-200' : 'text-slate-500 hover:bg-slate-100'">
                                    <div class="flex gap-0.5"><div class="w-3 h-3.5 bg-current rounded-[2px]"></div><div class="w-3 h-3.5 bg-current rounded-[2px] opacity-70"></div><div class="w-3 h-3.5 bg-current rounded-[2px] opacity-40"></div></div>
                                    3 Grid
                                </button>
                                <button @click="changeGridType('4')" class="flex-1 py-3 text-[11px] font-bold rounded outline-none transition-all flex flex-col items-center gap-1.5" :class="currentConfig.gridType === '4' ? 'bg-white text-blue-600 border border-slate-200 shadow-sm' : 'text-slate-500 hover:bg-slate-100'">
                                    <div class="flex gap-[2px]"><div class="w-2.5 h-3.5 bg-current rounded-[2px]"></div><div class="w-2.5 h-3.5 bg-current rounded-[2px] opacity-70"></div><div class="w-2.5 h-3.5 bg-current rounded-[2px] opacity-50"></div><div class="w-2.5 h-3.5 bg-current rounded-[2px] opacity-30"></div></div>
                                    4 Grid
                                </button>
                                <button @click="changeGridType('5')" class="flex-1 py-3 text-[11px] font-bold rounded outline-none transition-all flex flex-col items-center gap-1.5" :class="currentConfig.gridType === '5' ? 'bg-white text-blue-600 border border-slate-200 shadow-sm' : 'text-slate-500 hover:bg-slate-100'">
                                    <div class="flex gap-[2px]"><div class="w-1.5 h-3.5 bg-current rounded-[2px]"></div><div class="w-1.5 h-3.5 bg-current rounded-[2px] opacity-80"></div><div class="w-1.5 h-3.5 bg-current rounded-[2px] opacity-60"></div><div class="w-1.5 h-3.5 bg-current rounded-[2px] opacity-40"></div><div class="w-1.5 h-3.5 bg-current rounded-[2px] opacity-20"></div></div>
                                    5 Grid
                                </button>
                            </div>
                        </div>

                        <div class="mb-5">
                            <div class="flex justify-between items-center mb-3">
                                <h5 class="text-sm font-black text-slate-800">Daftar Foto</h5>
                                <span class="text-[11px] font-bold px-2 py-1 bg-slate-100 rounded text-slate-600" x-text="currentConfig.images ? `${currentConfig.images.length} / ${currentConfig.maxImages}` : `0 / 3`"></span>
                            </div>

                            {{-- List Gambar --}}
                            <div class="space-y-3 mb-4" x-show="currentConfig.images && currentConfig.images.length > 0">
                                <template x-for="(img, imgIdx) in currentConfig.images" :key="imgIdx">
                                    <div class="flex gap-3 bg-slate-50 p-2.5 rounded-xl border border-slate-200 shadow-sm">
                                        <div class="w-14 h-14 rounded bg-slate-200 flex-shrink-0 bg-cover bg-center border border-slate-300" :style="`background-image: url('${img}')`"></div>
                                        <div class="flex-1 flex flex-col justify-center">
                                            <input type="text" placeholder="Tautan (Opsional)" class="w-full bg-white border border-slate-200 text-[11px] font-bold rounded-lg px-3 py-2 outline-none focus:border-blue-400">
                                        </div>
                                        <button @click="removeComponentImage(imgIdx)" class="text-slate-400 hover:text-red-500 transition-colors px-2 outline-none"><i class="mdi mdi-delete text-lg"></i></button>
                                    </div>
                                </template>
                            </div>

                            {{-- Tombol Upload --}}
                            <button x-show="!currentConfig.images || currentConfig.images.length < currentConfig.maxImages"
                                    @click="$refs.componentUploader.click()"
                                    class="w-full py-4 border-2 border-dashed border-blue-300 bg-blue-50/50 text-blue-600 text-sm font-bold rounded-xl hover:bg-blue-100 hover:border-blue-400 transition-colors flex items-center justify-center gap-2 outline-none">
                                <i class="mdi mdi-plus-circle-outline text-xl"></i> Tambah Foto Ke-<span x-text="(currentConfig.images ? currentConfig.images.length : 0) + 1"></span>
                            </button>
                        </div>
                    </div>

                    {{-- Form: Produk --}}
                    <div x-show="activeItem && activeItem.type === 'produk'">
                        <div class="mb-6">
                            <h5 class="text-sm font-black text-slate-800 mb-2">Pilih Layout Tampilan Produk</h5>
                            <div class="flex gap-2 bg-slate-50 p-1.5 rounded-lg border border-slate-200">
                                <button @click="currentConfig.layout = 'horizontal'; updateItemConfig()" class="flex-1 py-3 text-xs font-bold rounded outline-none transition-all flex flex-col items-center gap-1.5" :class="currentConfig.layout === 'horizontal' ? 'bg-white text-blue-600 border border-slate-200 shadow-sm' : 'text-slate-500 hover:bg-slate-100'">
                                    <i class="mdi mdi-gesture-swipe-horizontal text-xl"></i> Scroll Samping (Max 10)
                                </button>
                                <button @click="currentConfig.layout = 'vertical'; updateItemConfig()" class="flex-1 py-3 text-xs font-bold rounded shadow-sm outline-none transition-all flex flex-col items-center gap-1.5" :class="currentConfig.layout === 'vertical' ? 'bg-white text-blue-600 border border-slate-200' : 'text-slate-500 hover:bg-slate-100'">
                                    <i class="mdi mdi-grid text-xl"></i> Grid Kebawah (Max 10)
                                </button>
                            </div>
                        </div>

                        <h5 class="text-sm font-black text-slate-800 mb-2">Sumber Data Produk</h5>
                        <p class="text-[11px] font-medium text-slate-500 mb-4">Pilih produk manual atau biarkan sistem memilih otomatis produk terlaris.</p>

                        <div class="flex flex-col gap-3 mb-6">
                            <label class="flex items-center gap-4 p-4 border-2 rounded-xl cursor-pointer transition-colors" :class="currentConfig.productSource === 'auto' ? 'border-blue-500 bg-blue-50' : 'border-slate-200 bg-white hover:border-slate-300'">
                                <input type="radio" name="prod_src" value="auto" x-model="currentConfig.productSource" @change="updateItemConfig()" class="w-5 h-5 text-blue-600 focus:ring-blue-500">
                                <div>
                                    <div class="text-sm font-black" :class="currentConfig.productSource === 'auto' ? 'text-blue-700' : 'text-slate-700'">Pilih Otomatis</div>
                                    <div class="text-[10px] font-bold mt-0.5" :class="currentConfig.productSource === 'auto' ? 'text-blue-500/80' : 'text-slate-500'">Sistem memunculkan produk terlaris otomatis</div>
                                </div>
                            </label>

                            <label class="flex items-center gap-4 p-4 border-2 rounded-xl cursor-pointer transition-colors" :class="currentConfig.productSource === 'manual' ? 'border-blue-500 bg-blue-50' : 'border-slate-200 bg-white hover:border-slate-300'">
                                <input type="radio" name="prod_src" value="manual" x-model="currentConfig.productSource" @change="updateItemConfig()" class="w-5 h-5 text-blue-600 focus:ring-blue-500">
                                <div class="flex-1">
                                    <div class="text-sm font-black" :class="currentConfig.productSource === 'manual' ? 'text-blue-700' : 'text-slate-700'">Pilih Manual</div>
                                    <div class="text-[10px] font-bold mt-0.5" :class="currentConfig.productSource === 'manual' ? 'text-blue-500/80' : 'text-slate-500'">Anda dapat memilih maksimal 10 produk.</div>
                                </div>
                            </label>
                        </div>

                        {{-- Area Produk Terpilih (Muncul Hanya Jika Manual) --}}
                        <div x-show="currentConfig.productSource === 'manual'" class="mt-2 p-4 bg-slate-50 border border-slate-200 rounded-xl">
                            <div class="flex justify-between items-center mb-4">
                                <h6 class="text-xs font-black text-slate-800">Daftar Produk Terpilih</h6>
                                <span class="text-[11px] font-bold px-2 py-1 bg-white border border-slate-200 rounded text-slate-600"><span x-text="currentConfig.selectedProducts ? currentConfig.selectedProducts.length : 0"></span>/10</span>
                            </div>

                            <div class="space-y-2.5 mb-4" x-show="currentConfig.selectedProducts && currentConfig.selectedProducts.length > 0">
                                <template x-for="(prod, pIdx) in currentConfig.selectedProducts" :key="prod.id">
                                    <div class="flex items-center gap-3 bg-white p-2.5 rounded-xl border border-slate-200 shadow-sm">
                                        <img :src="prod.img" class="w-10 h-10 rounded-lg object-cover border border-slate-100">
                                        <div class="flex-1 overflow-hidden">
                                            <div class="text-[11px] font-bold text-slate-700 truncate mb-1" x-text="prod.name"></div>
                                            <div class="text-[11px] font-black text-orange-500" x-text="prod.price"></div>
                                        </div>
                                        <button @click="removeSelectedProduct(pIdx)" class="text-slate-400 hover:text-red-500 outline-none px-1"><i class="mdi mdi-delete text-lg"></i></button>
                                    </div>
                                </template>
                            </div>

                            <button @click="openProductModal()" class="w-full py-3 bg-slate-800 text-white text-xs font-bold rounded-xl hover:bg-slate-900 transition-colors shadow-md outline-none flex items-center justify-center gap-2" :disabled="currentConfig.selectedProducts && currentConfig.selectedProducts.length >= 10">
                                <i class="mdi mdi-plus-box-outline text-lg"></i> Pilih Produk Etalase
                            </button>
                        </div>
                    </div>

                    {{-- Form: Video (Pilih Source YT atau Lokal) --}}
                    <div x-show="activeItem && activeItem.type === 'video'">
                        <h5 class="text-sm font-black text-slate-800 mb-2">Sumber Video</h5>
                        <div class="flex gap-2 mb-5 bg-slate-50 p-1.5 rounded-lg border border-slate-200">
                            <button @click="currentConfig.videoSource = 'youtube'; updateItemConfig()" class="flex-1 py-2 text-xs font-bold rounded outline-none transition-all flex items-center justify-center gap-1.5" :class="currentConfig.videoSource === 'youtube' ? 'bg-white text-red-600 border border-slate-200 shadow-sm' : 'text-slate-500 hover:bg-slate-100'"><i class="mdi mdi-youtube text-lg"></i> YouTube</button>
                            <button @click="currentConfig.videoSource = 'local'; updateItemConfig()" class="flex-1 py-2 text-xs font-bold rounded outline-none transition-all flex items-center justify-center gap-1.5" :class="currentConfig.videoSource === 'local' ? 'bg-white text-blue-600 border border-slate-200 shadow-sm' : 'text-slate-500 hover:bg-slate-100'"><i class="mdi mdi-monitor-arrow-down text-lg"></i> File Lokal</button>
                        </div>

                        {{-- Input Youtube --}}
                        <div x-show="currentConfig.videoSource === 'youtube'">
                            <label class="block text-[11px] font-black text-slate-500 mb-2 uppercase">Tautan Video Youtube</label>
                            <input type="url" x-model="currentConfig.videoUrl" @input="updateItemConfig()" placeholder="https://youtube.com/watch?v=..." class="w-full px-4 py-3 border border-slate-200 rounded-xl bg-slate-50 text-sm font-bold text-slate-800 focus:bg-white focus:border-red-500 focus:ring-2 focus:ring-red-100 outline-none transition-all mb-3">
                        </div>

                        {{-- Upload File Lokal --}}
                        <div x-show="currentConfig.videoSource === 'local'">
                            <label class="block text-[11px] font-black text-slate-500 mb-2 uppercase">Upload Video MP4</label>
                            <div class="w-full aspect-video border-2 border-dashed border-slate-300 bg-slate-50 flex flex-col items-center justify-center hover:border-blue-500 hover:bg-blue-50 transition-colors cursor-pointer rounded-2xl group overflow-hidden mb-4 relative" @click="$refs.videoUploader.click()">
                                <template x-if="currentConfig.videoFile">
                                    <video :src="currentConfig.videoFile" class="w-full h-full object-cover absolute inset-0 z-0"></video>
                                </template>
                                <div class="relative z-10 flex flex-col items-center p-3 text-center" :class="currentConfig.videoFile ? 'opacity-0 hover:opacity-100 bg-black/60 w-full h-full justify-center backdrop-blur-sm' : ''">
                                    <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center shadow-sm border border-slate-200 mb-2 group-hover:scale-110 transition-all" :class="currentConfig.videoFile ? 'text-white bg-transparent border-white/50' : 'text-blue-500'">
                                        <i class="mdi mdi-video-plus-outline text-2xl"></i>
                                    </div>
                                    <span class="text-[11px] font-bold" :class="currentConfig.videoFile ? 'text-white' : 'text-slate-600'" x-text="currentConfig.videoFile ? 'Ganti Video' : 'Pilih File Video'"></span>
                                </div>
                            </div>
                            <ul class="text-[10px] font-medium text-slate-500 list-disc pl-4 space-y-1 mb-4">
                                <li>Format file: MP4.</li>
                                <li>Ukuran maksimal: 15MB.</li>
                            </ul>
                        </div>

                        <div class="bg-blue-50 border border-blue-100 p-4 rounded-xl flex gap-3">
                            <i class="mdi mdi-information text-blue-500 text-lg"></i>
                            <p class="text-[11px] font-medium text-blue-800 leading-relaxed">Video akan diputar otomatis tanpa suara saat pembeli mengunjungi halaman toko Anda.</p>
                        </div>
                    </div>

                </div>

            </div>
        </aside>

    </main>

    {{-- =================================================================
         MODAL PILIH PRODUK MANUAL (Z-INDEX SUPER TINGGI)
         ================================================================= --}}
    <div x-show="showProductModal" class="fixed inset-0 z-[100005] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm" style="display: none;" x-cloak>
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-4xl flex flex-col overflow-hidden transform transition-all modal-enter" @click.outside="showProductModal = false">

            <div class="px-8 py-5 border-b border-slate-200 flex justify-between items-center bg-slate-50">
                <div>
                    <h3 class="text-lg font-black text-slate-800">Pilih Produk Etalase</h3>
                    <p class="text-xs font-bold text-slate-500 mt-1">Pilih produk andalan untuk ditampilkan ke halaman toko.</p>
                </div>
                <button @click="showProductModal = false" class="text-slate-400 hover:text-red-500 transition-colors outline-none bg-white w-10 h-10 rounded-full flex items-center justify-center border border-slate-200 shadow-sm"><i class="mdi mdi-close text-xl"></i></button>
            </div>

            <div class="p-8 overflow-y-auto max-h-[60vh] bg-slate-100/50">
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-4">
                    {{-- Render Dummy Products --}}
                    <template x-for="prod in availableProducts" :key="prod.id">
                        <div class="bg-white border-2 rounded-xl p-3 cursor-pointer transition-all flex flex-col hover:shadow-md"
                             :class="isProductTempSelected(prod) ? 'border-blue-500 shadow-blue-500/20' : 'border-slate-200 hover:border-blue-300'"
                             @click="toggleTempProduct(prod)">

                            <div class="aspect-square bg-slate-100 rounded-lg mb-3 overflow-hidden border border-slate-100 relative">
                                <img :src="prod.img" class="w-full h-full object-cover">
                                {{-- Ceklis Overlay --}}
                                <div x-show="isProductTempSelected(prod)" class="absolute top-2 right-2 w-7 h-7 bg-blue-600 text-white rounded-full flex items-center justify-center shadow-lg border-2 border-white">
                                    <i class="mdi mdi-check text-base font-black"></i>
                                </div>
                            </div>
                            <div class="text-xs font-bold text-slate-700 leading-tight mb-1 truncate" x-text="prod.name"></div>
                            <div class="text-sm font-black text-orange-600" x-text="prod.price"></div>
                        </div>
                    </template>
                </div>
            </div>

            <div class="px-8 py-5 border-t border-slate-200 bg-white flex justify-between items-center">
                <div class="text-sm font-bold text-slate-500">Total Terpilih: <span class="text-blue-600 font-black text-lg ml-1" x-text="tempSelectedProducts.length"></span> <span class="text-slate-400 font-medium">/ 10 Produk</span></div>
                <div class="flex gap-3">
                    <button @click="showProductModal = false" class="px-6 py-2.5 rounded-xl font-bold text-sm text-slate-600 bg-slate-100 hover:bg-slate-200 transition-colors outline-none">Batal</button>
                    <button @click="saveProductSelection()" class="px-8 py-2.5 rounded-xl font-bold text-sm text-white bg-blue-600 hover:bg-blue-700 shadow-lg shadow-blue-500/30 transition-colors flex items-center gap-2 outline-none">
                        <i class="mdi mdi-check-all text-lg"></i> Terapkan
                    </button>
                </div>
            </div>
        </div>
    </div>

<script>
    // DATA TEMPLATE (HARDCODE AMAN)
    const TEMPLATES_DATA = [
        {id:1, name:'Oceanic Premium', hc:'bg-gradient-to-r from-blue-600 to-indigo-800', ac:'bg-gradient-to-br from-blue-500 to-indigo-600', layout:['banner', 'kategori', 'produk']},
        {id:2, name:'Eco Harvest', hc:'bg-gradient-to-r from-emerald-600 to-teal-800', ac:'bg-gradient-to-br from-emerald-500 to-green-600', layout:['kategori', 'carousel', 'produk']},
        {id:3, name:'Sunset Flash', hc:'bg-gradient-to-r from-orange-500 to-red-600', ac:'bg-gradient-to-br from-orange-500 to-red-500', layout:['video', 'produk', 'banner']},
        {id:4, name:'Midnight Luxury', hc:'bg-slate-900', ac:'bg-gradient-to-br from-slate-700 to-slate-900', layout:['carousel', 'kategori', 'produk']},
        {id:5, name:'Pink Blossom', hc:'bg-gradient-to-r from-pink-500 to-rose-600', ac:'bg-gradient-to-br from-pink-400 to-rose-500', layout:['banner', 'produk', 'kategori']}
    ];

    // Simulasi Database Produk Penjual
    const DUMMY_PRODUCTS = [
        { id: 101, name: 'Sepatu Sneakers Pria Original', price: 'Rp 250.000', img: 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=400&q=80' },
        { id: 102, name: 'Kaos Polos Cotton Combed 30s', price: 'Rp 45.000', img: 'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?w=400&q=80' },
        { id: 103, name: 'Jam Tangan Kulit Analog', price: 'Rp 175.000', img: 'https://images.unsplash.com/photo-1524592094714-0f0654e20314?w=400&q=80' },
        { id: 104, name: 'Topi Baseball Bordir Custom', price: 'Rp 35.000', img: 'https://images.unsplash.com/photo-1556306535-0f09a5f6f0d5?w=400&q=80' },
        { id: 105, name: 'Tas Ransel Backpack Canvas', price: 'Rp 120.000', img: 'https://images.unsplash.com/photo-1553062407-98eeb64c6a62?w=400&q=80' },
        { id: 106, name: 'Kacamata Hitam Polarized', price: 'Rp 85.000', img: 'https://images.unsplash.com/photo-1511499767150-a48a237f0083?w=400&q=80' },
        { id: 107, name: 'Kemeja Flanel Kasual', price: 'Rp 115.000', img: 'https://images.unsplash.com/photo-1598033129183-c4f50c736f10?w=400&q=80' },
        { id: 108, name: 'Celana Chino Pria', price: 'Rp 135.000', img: 'https://images.unsplash.com/photo-1624378439575-d8705ad7ae80?w=400&q=80' },
        { id: 109, name: 'Jaket Denim Lengan Panjang', price: 'Rp 160.000', img: 'https://images.unsplash.com/photo-1576871337622-98d48d1cf531?w=400&q=80' },
        { id: 110, name: 'Sweater Hoodie Polos', price: 'Rp 99.000', img: 'https://images.unsplash.com/photo-1556821840-3a63f95609a7?w=400&q=80' }
    ];

    const generateUid = () => Date.now().toString(36) + Math.random().toString(36).substr(2);

    function desktopEditor() {
        // BACA DATA DARI DATABASE (INJEKSI PHP KE JS)
        const savedData = {!! $savedDecoration !!};
        
        let initialCanvas = [];
        let initialHeaderColor = 'bg-slate-800';
        let initialTemplateName = 'Kanvas Desktop Kosong';

        // Jika sudah pernah disimpan, masukkan ke variabel awal
        if(savedData && savedData.layout) {
            initialCanvas = savedData.layout;
            initialHeaderColor = savedData.header || 'bg-slate-800';
            initialTemplateName = savedData.template || 'Template Kustom';
        }

        return {
            templates: TEMPLATES_DATA,
            templateName: initialTemplateName,
            canvasItems: initialCanvas,
            activeItemId: null,
            currentTime: '12:00',
            templateHeaderColor: initialHeaderColor,
            templateAccentColor: 'bg-slate-800',
            isPreviewMode: false,
            uploadedHeader: null,

            // State Modal Produk
            showProductModal: false,
            availableProducts: DUMMY_PRODUCTS,
            tempSelectedProducts: [],

            get activeItem() {
                if(!this.activeItemId) return null;
                return this.canvasItems.find(item => item.uid === this.activeItemId) || null;
            },

            initEditor() {
                setInterval(() => {
                    const d = new Date();
                    this.currentTime = d.getHours().toString().padStart(2,'0') + ':' + d.getMinutes().toString().padStart(2,'0');
                }, 1000);

                const params = new URLSearchParams(window.location.search);
                const tplId = params.get('tpl');
                if (tplId && tplId !== 'blank') {
                    this.loadTemplate(tplId);
                }

                this.$nextTick(() => {
                    const palette = this.$refs.paletteList;
                    const canvas = this.$refs.canvasDropzone;

                    if(palette) {
                        new Sortable(palette, {
                            group: { name: 'shared', pull: 'clone', put: false },
                            sort: false, animation: 150
                        });
                    }

                    if(canvas) {
                        new Sortable(canvas, {
                            group: 'shared',
                            animation: 150,
                            draggable: '.group\\/canvas',
                            ghostClass: 'sortable-ghost',
                            onAdd: (evt) => {
                                const type = evt.item.dataset.type;

                                // Lacak posisi akurat berdasarkan elemen di atasnya
                                let dropIndex = 0;
                                let sibling = evt.item.previousElementSibling;
                                
                                while (sibling) {
                                    // Hitung hanya elemen kanvas asli (abaikan tag <template> bawaan Alpine)
                                    if (sibling.classList.contains('group/canvas')) {
                                        dropIndex++;
                                    }
                                    sibling = sibling.previousElementSibling;
                                }

                                // Cabut elemen DOM cloningan Sortable dari Palette
                                evt.item.remove(); 

                                // Lempar komponen ke posisi dropIndex yang super presisi
                                this.addComponent(type, dropIndex);
                            },
                            onUpdate: (evt) => {
                                // Revert perubahan DOM Sortable agar Alpine tidak error mutasi DOM
                                evt.from.insertBefore(evt.item, evt.from.children[evt.oldIndex]);

                                // Dapatkan index baru dari DOM
                                const itemNodes = Array.from(evt.from.querySelectorAll('.group\\/canvas'));
                                const newRealIndex = itemNodes.indexOf(evt.item);

                                // Cari Index Lama di Array Alpine
                                const oldIndex = this.canvasItems.findIndex(i => i.uid === evt.item.dataset.uid);

                                // Modifikasi Array (Reactivity Alpine)
                                if(oldIndex !== -1 && newRealIndex !== -1) {
                                    const movedItem = this.canvasItems.splice(oldIndex, 1)[0];
                                    this.canvasItems.splice(newRealIndex, 0, movedItem);
                                }
                            }
                        });
                    }
                });
            },

            loadTemplate(id) {
                const tpl = this.templates.find(t => t.id == id);
                if (tpl) {
                    this.templateName = 'Template: ' + tpl.name;
                    this.templateHeaderColor = tpl.hc;
                    this.templateAccentColor = tpl.ac;

                    if (tpl.layout) {
                        this.canvasItems = tpl.layout.map(typeRaw => {
                            let type = typeRaw.replace(/[0-9]/g, '');
                            return { uid: generateUid(), type: type, config: this.getInitialConfig(type) };
                        });
                    }
                }
            },

            getInitialConfig(type) {
                if(type === 'banner') return { title: 'Promo Spesial', textColor: '#ffffff', images: [], maxImages: 3, ratio: '4:1' };
                if(type === 'carousel') return { title: '', textColor: '#1e293b', images: [], maxImages: 10, gridType: '3' };
                if(type === 'video') return { title: 'Video Edukasi', textColor: '#1e293b', videoSource: 'youtube', videoUrl: '', videoFile: null };
                if(type === 'kategori') return { title: 'Kategori Belanja', textColor: '#1e293b' };
                if(type === 'produk') return { title: 'Produk Rekomendasi', textColor: '#1e293b', productSource: 'auto', layout: 'horizontal', selectedProducts: [] };
            },

            getSettingTitle(type) {
                if(type === 'banner') return 'Pengaturan Banner';
                if(type === 'carousel') return 'Pengaturan Grid Foto Estetik';
                if(type === 'video') return 'Pengaturan Video';
                if(type === 'kategori') return 'Pengaturan Kategori';
                return 'Pengaturan Produk';
            },

            addComponent(type, index) {
                const idx = index !== undefined ? index : this.canvasItems.length;
                const newItem = { uid: generateUid(), type: type, config: this.getInitialConfig(type) };

                this.canvasItems.splice(idx, 0, newItem);
                this.setActive(newItem);

                setTimeout(() => {
                    const scrollArea = document.getElementById('main-scroll-area');
                    if(scrollArea) scrollArea.scrollTop = scrollArea.scrollHeight;
                }, 100);
            },

            setActive(item) { this.activeItemId = item.uid; },
            updateItemConfig() { this.canvasItems = [...this.canvasItems]; },
            removeItem(idx) { this.canvasItems.splice(idx, 1); this.activeItemId = null; },

            removeComponentImage(imgIdx) {
                if(this.activeItem && this.activeItem.config.images) {
                    this.activeItem.config.images.splice(imgIdx, 1);
                    this.updateItemConfig();
                }
            },

            changeGridType(type) {
                if(this.activeItem && this.activeItem.type === 'carousel') {
                    this.activeItem.config.gridType = type;
                    this.updateItemConfig();
                }
            },

            // --- Product Modal Logic ---
            openProductModal() {
                if(this.activeItem && this.activeItem.type === 'produk') {
                    this.tempSelectedProducts = [...this.activeItem.config.selectedProducts];
                    this.showProductModal = true;
                }
            },
            isProductTempSelected(prod) {
                return this.tempSelectedProducts.find(p => p.id === prod.id) !== undefined;
            },
            toggleTempProduct(prod) {
                const existIdx = this.tempSelectedProducts.findIndex(p => p.id === prod.id);
                if(existIdx > -1) {
                    this.tempSelectedProducts.splice(existIdx, 1);
                } else {
                    if(this.tempSelectedProducts.length >= 10) {
                        Swal.fire({toast: true, position: 'top-end', icon: 'warning', title: 'Maksimal 10 produk', showConfirmButton: false, timer: 1500});
                        return;
                    }
                    this.tempSelectedProducts.push(prod);
                }
            },
            saveProductSelection() {
                if(this.activeItem) {
                    this.activeItem.config.selectedProducts = [...this.tempSelectedProducts];
                    this.updateItemConfig();
                    this.showProductModal = false;
                }
            },
            removeSelectedProduct(idx) {
                if(this.activeItem && this.activeItem.config.selectedProducts) {
                    this.activeItem.config.selectedProducts.splice(idx, 1);
                    this.updateItemConfig();
                }
            },

            // Reorder
            moveUp(idx) { if (idx > 0) { const i = this.canvasItems.splice(idx, 1)[0]; this.canvasItems.splice(idx - 1, 0, i); } },
            moveDown(idx) { if (idx < this.canvasItems.length - 1) { const i = this.canvasItems.splice(idx, 1)[0]; this.canvasItems.splice(idx + 1, 0, i); } },

            clearCanvas() {
                if(this.canvasItems.length === 0) return;
                Swal.fire({
                    title: 'Kosongkan Kanvas?', text: 'Semua komponen akan dihapus permanen.', icon: 'warning',
                    showCancelButton: true, confirmButtonColor: '#ef4444', cancelButtonText: 'Batal', confirmButtonText: 'Ya, Bersihkan',
                    customClass: { popup: 'rounded-2xl' }
                }).then(res => {
                    if(res.isConfirmed) {
                        this.canvasItems = []; this.activeItemId = null;
                        this.templateName = 'Kanvas Desktop Kosong'; this.uploadedHeader = null;
                        this.templateHeaderColor = 'bg-slate-800';
                    }
                });
            },

            gantiTemplate() { window.location.href = "{{ route('seller.shop.decoration.template') ?? '#' }}"; },
            togglePreview() { this.isPreviewMode = !this.isPreviewMode; this.activeItemId = null; },

            // Fungsi sakti untuk ubah URL YouTube jadi URL Embed Autoplay
            getYoutubeEmbedUrl(url) {
                if (!url) return '';
                let videoId = '';
                const regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=)([^#\&\?]*).*/;
                const match = url.match(regExp);

                if (match && match[2].length === 11) {
                    videoId = match[2];
                    return `https://www.youtube.com/embed/${videoId}?autoplay=1&mute=1&loop=1&playlist=${videoId}&controls=0&showinfo=0&rel=0`;
                }
                return '';
            },

            handleHeaderUpload(event) {
                const file = event.target.files[0];
                if (!file) return;
                const reader = new FileReader();
                reader.onload = (e) => { this.uploadedHeader = e.target.result; };
                reader.readAsDataURL(file);
                event.target.value = '';
            },

            handleComponentUpload(event) {
                const file = event.target.files[0];
                if (!file || !this.activeItem) return;
                if(!this.activeItem.config.images) this.activeItem.config.images = [];
                if(this.activeItem.config.images.length >= this.activeItem.config.maxImages) {
                    Swal.fire('Batas Maksimal', `Maksimal foto adalah ${this.activeItem.config.maxImages}.`, 'warning');
                    return;
                }
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.activeItem.config.images.push(e.target.result);
                    this.updateItemConfig();
                };
                reader.readAsDataURL(file);
                event.target.value = '';
            },

            handleVideoUpload(event) {
                const file = event.target.files[0];
                if(!file || !this.activeItem) return;
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.activeItem.config.videoFile = e.target.result;
                    this.updateItemConfig();
                };
                reader.readAsDataURL(file);
                event.target.value = '';
            },

            simpanDraf() {
                if(this.canvasItems.length === 0) {
                    Swal.fire({icon: 'error', title: 'Kanvas Kosong', text: 'Tidak ada yang bisa disimpan.', customClass: { popup: 'rounded-2xl' }});
                    return;
                }
                Swal.fire({ title: 'Menyimpan Draf...', timer: 1000, didOpen: () => Swal.showLoading() }).then(() => {
                    Swal.fire({icon: 'success', title:'Tersimpan', text:'Draf berhasil disimpan lokal.', timer:1500, showConfirmButton:false, customClass: { popup: 'rounded-2xl' }});
                });
            },

            tampilkan() {
                if(this.canvasItems.length === 0) {
                    Swal.fire({icon: 'error', title: 'Gagal Menyimpan', text: 'Kanvas masih kosong.', customClass: { popup: 'rounded-2xl' }});
                    return;
                }
                
                const payload = {
                    template: this.templateName,
                    header: this.uploadedHeader ? 'Custom Image' : this.templateHeaderColor,
                    layout: this.canvasItems
                };
                
                Swal.fire({
                    title: 'Tayangkan ke Desktop?', 
                    text: 'Desain ini akan langsung terlihat oleh pengunjung yang menggunakan komputer.', 
                    icon: 'question',
                    showCancelButton: true, confirmButtonColor: '#2563eb', cancelButtonText: 'Batal', confirmButtonText: 'Ya, Tayangkan',
                    customClass: { popup: 'rounded-2xl' }
                }).then((result) => {
                    if (result.isConfirmed) {
                        
                        Swal.fire({ title: 'Menyimpan ke Server...', allowOutsideClick: false, didOpen: () => Swal.showLoading(), customClass: { popup: 'rounded-2xl' } });

                        // KIRIM DATA KE LARAVEL MENGGUNAKAN FETCH API (AJAX)
                        fetch("{{ route('seller.shop.decoration.save') }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify(payload)
                        })
                        .then(response => response.json())
                        .then(data => {
                            if(data.success) {
                                Swal.fire({icon: 'success', title: 'Berhasil!', text: 'Dekorasi toko desktop berhasil ditayangkan.', timer:2500, showConfirmButton:false, customClass: { popup: 'rounded-2xl' }});
                            } else {
                                Swal.fire({icon: 'error', title: 'Gagal', text: data.message || 'Terjadi kesalahan sistem.'});
                            }
                        })
                        .catch(error => {
                            console.error("Error:", error);
                            Swal.fire({icon: 'error', title: 'Terjadi Kesalahan Server', text: 'Silakan pastikan route "seller.shop.decoration.save" sudah dibuat.'});
                        });
                    }
                });
            }
        }
    }
</script>
</body>
</html>
