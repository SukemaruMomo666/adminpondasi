<!DOCTYPE html>
<html lang="id">
<head>
    <link rel="icon" href="{{ asset('logopondasikita.png') }}" type="image/png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editor Dekorasi Toko - Pondasikita</title>

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

        /* Custom Scrollbar Modern */
        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
        .hide-scrollbar::-webkit-scrollbar { display: none; }
        .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

        /* Efek Drag & Drop SortableJS */
        .sortable-ghost { opacity: 0.6; background: #eff6ff !important; border: 2px dashed #2563eb !important; border-radius: 12px; overflow:hidden; }
        .sortable-drag { cursor: grabbing !important; transform: scale(1.02); box-shadow: 0 20px 25px -5px rgba(0,0,0,0.15); }

        /* Kanvas Item Active State */
        .canvas-item-wrapper { position: relative; border: 2px solid transparent; transition: border-color 0.2s ease; border-radius: 12px; margin-bottom: 12px; background: white;}
        .canvas-item-wrapper:hover { border-color: #cbd5e1; cursor: pointer; }
        .canvas-item-wrapper.is-active { border-color: #2563eb; box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.15); z-index: 10; }

        /* Mockup HP Fixed Size */
        .mockup-container {
            width: 380px; height: 800px; max-height: 90vh;
            background: #ffffff; border: 12px solid #1e293b; border-radius: 44px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            display: flex; flex-direction: column; position: relative; overflow: hidden;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .mockup-notch {
            position: absolute; top: -1px; left: 50%; transform: translateX(-50%);
            width: 140px; height: 26px; background: #1e293b;
            border-bottom-left-radius: 18px; border-bottom-right-radius: 18px; z-index: 50;
        }
        .preview-mode .mockup-container { transform: scale(1.05); box-shadow: 0 35px 60px -15px rgba(0, 0, 0, 0.3); }

        /* Layout Ratios */
        .ratio-2-1 { aspect-ratio: 2 / 1; }
        .ratio-16-9 { aspect-ratio: 16 / 9; }
        .ratio-1-1 { aspect-ratio: 1 / 1; }

        /* Animasi Modal */
        .modal-enter { animation: modalFadeIn 0.3s ease-out forwards; }
        @keyframes modalFadeIn { from { opacity: 0; transform: scale(0.95) translateY(10px); } to { opacity: 1; transform: scale(1) translateY(0); } }
    </style>

    {{-- Scripts: AlpineJS, SortableJS, SweetAlert2 --}}
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/focus@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body x-data="pondasiEditor()" x-init="initEditor()" x-cloak class="h-screen w-screen flex flex-col text-slate-800" :class="isPreviewMode ? 'preview-mode' : ''">

    {{-- DATA TOKO --}}
    @php
        $tokoName = optional(Auth::user()->toko ?? null)->nama_toko ?? 'Nama Toko Saya';
    @endphp

    {{-- INPUT FILE TERSEMBUNYI UNTUK UPLOAD --}}
    <input type="file" x-ref="headerUploader" class="hidden" accept="image/*" @change="handleHeaderUpload">
    <input type="file" x-ref="componentUploader" class="hidden" accept="image/*" @change="handleComponentUpload">
    <input type="file" x-ref="videoUploader" class="hidden" accept="video/mp4,video/webm" @change="handleVideoUpload">

    {{-- ==========================================
         HEADER UTAMA EDITOR (FULL WIDTH)
         ========================================== --}}
    <header class="h-16 bg-white border-b border-slate-200 flex justify-between items-center px-6 flex-shrink-0 z-30 shadow-sm transition-all duration-300">
        <div class="flex items-center gap-4">
            <a x-show="!isPreviewMode" href="{{ route('seller.shop.decoration.template') }}" class="w-10 h-10 flex items-center justify-center rounded-full bg-slate-100 hover:bg-blue-50 text-slate-600 hover:text-blue-600 transition-colors outline-none">
                <i class="mdi mdi-arrow-left text-xl"></i>
            </a>
            <button x-show="isPreviewMode" @click="togglePreview()" class="w-10 h-10 flex items-center justify-center rounded-full bg-red-100 hover:bg-red-200 text-red-600 transition-colors outline-none">
                <i class="mdi mdi-close text-xl"></i>
            </button>
            <div>
                <h1 class="font-black text-slate-800 text-lg leading-tight flex items-center gap-2">
                    Editor Visual Toko <i class="mdi mdi-check-decagram text-blue-500 text-base" x-show="!isPreviewMode"></i>
                </h1>
                <div class="text-[11px] font-bold text-slate-500 flex items-center gap-1">
                    Mode Aktif: <span class="text-blue-600 uppercase tracking-wider" x-text="isPreviewMode ? 'PRATINJAU (PREVIEW)' : templateName"></span>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <button x-show="!isPreviewMode" @click="gantiTemplate()" class="px-4 py-2 text-sm font-bold text-slate-600 bg-white border border-slate-200 hover:border-blue-300 hover:text-blue-600 rounded-xl transition-all outline-none flex items-center gap-2">
                <i class="mdi mdi-swap-horizontal text-lg"></i> Ganti Template
            </button>
            <button @click="togglePreview()" class="px-4 py-2 text-sm font-bold rounded-xl transition-all outline-none flex items-center gap-2" :class="isPreviewMode ? 'bg-blue-50 text-blue-600 border border-blue-200' : 'bg-white border border-slate-200 text-slate-600 hover:border-blue-300 hover:text-blue-600'">
                <i class="mdi text-lg" :class="isPreviewMode ? 'mdi-eye-off-outline' : 'mdi-eye-outline'"></i> <span x-text="isPreviewMode ? 'Tutup Pratinjau' : 'Pratinjau Mobile'"></span>
            </button>
            <div class="w-px h-6 bg-slate-200 mx-1" x-show="!isPreviewMode"></div>
            <button x-show="!isPreviewMode" @click="simpanDraf()" class="px-4 py-2 text-sm font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl transition-colors outline-none">
                Simpan Draf
            </button>
            <button x-show="!isPreviewMode" @click="tampilkan()" class="px-6 py-2 text-sm font-bold text-white bg-blue-600 hover:bg-blue-700 rounded-xl shadow-lg shadow-blue-600/20 transition-all outline-none flex items-center gap-2">
                <i class="mdi mdi-rocket-launch"></i> Tayangkan Toko
            </button>
        </div>
    </header>

    {{-- ==========================================
         WORKSPACE (3 KOLOM)
         ========================================== --}}
    <main class="flex-1 flex min-h-0 relative">

        {{-- =======================================
             KOLOM 1: PALET KOMPONEN KIRI
             ======================================= --}}
        <aside x-show="!isPreviewMode" x-transition.opacity.duration.300ms class="w-[300px] bg-white border-r border-slate-200 flex flex-col flex-shrink-0 z-20 shadow-[4px_0_24px_rgba(0,0,0,0.03)]">
            <div class="p-4 border-b border-slate-100 bg-slate-50/50">
                <div class="relative">
                    <i class="mdi mdi-magnify absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-lg"></i>
                    <input type="text" placeholder="Cari Modul..." class="w-full pl-10 pr-4 py-2.5 text-sm font-medium border border-slate-200 rounded-xl bg-white focus:ring-2 focus:ring-blue-100 focus:border-blue-500 outline-none transition-all">
                </div>
            </div>

            <div class="flex-1 overflow-y-auto hide-scrollbar">
                <div class="p-4">
                    <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4 px-1">Tampilan & Teks</h3>

                    {{-- AREA SUMBER DRAG --}}
                    <div class="grid grid-cols-2 gap-3" id="palette-list">
                        <div class="border border-slate-200 rounded-2xl p-3 flex flex-col items-center justify-center text-center cursor-move hover:border-blue-500 hover:shadow-md transition-all bg-white group" data-type="banner">
                            <div class="w-10 h-10 bg-blue-50 text-blue-600 flex items-center justify-center rounded-xl mb-2 group-hover:scale-110 transition-transform"><i class="mdi mdi-image-area text-xl"></i></div>
                            <span class="text-[10px] font-black text-slate-700">Banner Toko</span>
                        </div>
                        <div class="border border-slate-200 rounded-2xl p-3 flex flex-col items-center justify-center text-center cursor-move hover:border-indigo-500 hover:shadow-md transition-all bg-white group" data-type="carousel">
                            <div class="w-10 h-10 bg-indigo-50 text-indigo-600 flex items-center justify-center rounded-xl mb-2 group-hover:scale-110 transition-transform"><i class="mdi mdi-view-grid-plus text-xl"></i></div>
                            <span class="text-[10px] font-black text-slate-700">Banyak Foto</span>
                        </div>
                        <div class="border border-slate-200 rounded-2xl p-3 flex flex-col items-center justify-center text-center cursor-move hover:border-red-500 hover:shadow-md transition-all bg-white group" data-type="video">
                            <div class="w-10 h-10 bg-red-50 text-red-600 flex items-center justify-center rounded-xl mb-2 group-hover:scale-110 transition-transform"><i class="mdi mdi-youtube text-xl"></i></div>
                            <span class="text-[10px] font-black text-slate-700">Video Promo</span>
                        </div>
                        <div class="border border-slate-200 rounded-2xl p-3 flex flex-col items-center justify-center text-center cursor-move hover:border-emerald-500 hover:shadow-md transition-all bg-white group" data-type="produk">
                            <div class="w-10 h-10 bg-emerald-50 text-emerald-600 flex items-center justify-center rounded-xl mb-2 group-hover:scale-110 transition-transform"><i class="mdi mdi-storefront text-xl"></i></div>
                            <span class="text-[10px] font-black text-slate-700">Daftar Produk</span>
                        </div>
                        <div class="col-span-2 border border-slate-200 rounded-2xl p-3 flex items-center gap-4 cursor-move hover:border-amber-500 hover:shadow-md transition-all bg-white group" data-type="kategori">
                            <div class="w-10 h-10 bg-amber-50 text-amber-600 flex items-center justify-center rounded-xl group-hover:scale-110 transition-transform"><i class="mdi mdi-shape text-xl"></i></div>
                            <div class="text-left"><div class="text-xs font-black text-slate-700">Kategori Ikon</div><div class="text-[9px] text-slate-400 font-bold mt-0.5">Navigasi halaman cepat</div></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="p-4 border-t border-slate-100 bg-white">
                <button @click="clearCanvas()" class="w-full py-3 border-2 border-red-100 text-red-500 text-xs font-black rounded-xl hover:bg-red-50 transition-colors flex items-center justify-center gap-2 outline-none">
                    <i class="mdi mdi-delete-sweep text-lg"></i> Bersihkan Kanvas
                </button>
            </div>
        </aside>

        {{-- =======================================
             KOLOM 2: MOCKUP HP (TENGAH CENTERED)
             ======================================= --}}
        <section class="flex-1 relative overflow-y-auto flex py-10 px-4 transition-all duration-300 hide-scrollbar"
                 :class="isPreviewMode ? 'justify-center items-center bg-slate-900/5 backdrop-blur-sm' : 'justify-center bg-slate-200 shadow-inner'" id="main-scroll-area">

            <div class="absolute left-1/2 -translate-x-[280px] top-10 hidden xl:block">
                <div class="bg-blue-600 text-white text-[10px] font-bold px-3 py-1.5 rounded-lg shadow-md flex items-center gap-2">
                    <i class="mdi mdi-cellphone"></i> Pratinjau Mobile
                </div>
            </div>

            {{-- BINGKAI HP --}}
            <div class="mockup-container">
                <div class="mockup-notch"></div>

                {{-- AREA DALAM HP (YANG BISA DI-SCROLL) --}}
                <div class="flex-1 w-full h-full flex flex-col bg-[#f4f6f8] overflow-y-auto hide-scrollbar relative">

                    {{-- STATUS BAR --}}
                    <div class="h-7 bg-transparent absolute top-0 inset-x-0 z-[101] text-white flex justify-between items-center px-5 text-[10px] select-none font-bold mix-blend-difference pointer-events-none">
                        <span x-text="currentTime">12:30</span>
                        <div class="flex gap-1.5"><i class="mdi mdi-signal"></i><i class="mdi mdi-wifi"></i><i class="mdi mdi-battery-80"></i></div>
                    </div>

                    {{-- HEADER TOKO --}}
                    <div class="relative h-44 flex flex-col justify-end select-none flex-shrink-0 bg-cover bg-center"
                         :class="!uploadedHeader ? templateHeaderColor : ''"
                         :style="uploadedHeader ? { backgroundImage: `url('${uploadedHeader}')` } : {}">
                        <div class="absolute inset-0 bg-black/30"></div>
                        <div class="absolute inset-0 opacity-20 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')]" x-show="!uploadedHeader"></div>

                        <div class="relative z-10 px-4 pt-6 flex items-center justify-between text-white">
                            <i class="mdi mdi-arrow-left text-2xl"></i>
                            <div class="flex-1 mx-3 bg-white/25 border border-white/20 rounded-lg h-9 flex items-center px-3 gap-2 text-sm backdrop-blur-sm">
                                <i class="mdi mdi-magnify text-white/80"></i> <span class="text-white/70">Cari di toko</span>
                            </div>
                            <i class="mdi mdi-dots-vertical text-2xl"></i>
                        </div>

                        <div class="relative z-10 px-5 pb-5 mt-auto flex items-end justify-between">
                            <div class="flex gap-3 items-center text-white">
                                <div class="w-14 h-14 bg-white rounded-full p-0.5 shadow-md border border-white/20">
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($tokoName) }}&background=random" class="w-full h-full rounded-full object-cover">
                                </div>
                                <div>
                                    <h3 class="text-base font-black truncate max-w-[150px] drop-shadow-md">{{ $tokoName }}</h3>
                                    <div class="text-[10px] font-bold text-white/90 flex items-center gap-1 mt-0.5 drop-shadow"><i class="mdi mdi-star text-yellow-300"></i> 5.0 | Penjual Terpercaya</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- TABS STATIS --}}
                    <div class="flex border-b border-slate-200 bg-white sticky top-0 z-40 select-none shadow-sm flex-shrink-0">
                        <div class="flex-1 py-3.5 text-center text-[13px] font-black border-b-[3px] border-blue-600 text-blue-600">Beranda</div>
                        <div class="flex-1 py-3.5 text-center text-[13px] font-bold text-slate-500">Semua Produk</div>
                        <div class="flex-1 py-3.5 text-center text-[13px] font-bold text-slate-500">Kategori</div>
                    </div>

                    {{-- KANVAS DROPZONE --}}
                    <div class="relative flex-1 flex flex-col w-full bg-[#f4f6f8] pb-32 pt-1 min-h-[500px]" id="canvas-dropzone">

                        {{-- State Kosong dengan Warna Kustom (Hanya Border & Text Icon, bukan Background) --}}
                        <div x-show="canvasItems.length === 0" class="absolute inset-4 flex flex-col items-center justify-center p-6 text-center select-none pointer-events-none mt-2 border-2 border-dashed rounded-3xl transition-colors duration-300 z-0" :style="`border-color: ${emptyCanvasBorderColor};`" :class="isPreviewMode ? 'hidden' : ''">
                            <div class="w-20 h-20 mx-auto bg-white rounded-full flex items-center justify-center shadow-sm mb-4 transition-colors duration-300" :style="`color: ${emptyCanvasBorderColor};`">
                                <i class="mdi mdi-tray-arrow-down text-4xl"></i>
                            </div>
                            <p class="text-sm font-black text-slate-700">Kanvas Kosong</p>
                            <p class="text-[10px] text-slate-500 mt-1 font-bold px-4 leading-relaxed">Tarik modul dari panel kiri dan lepaskan di area ini.</p>
                        </div>

                        {{-- RENDER ALPINE LOOP --}}
                        <template x-for="(item, index) in canvasItems" :key="item.uid">

                            <div class="canvas-item-wrapper group/canvas z-10"
                                 :class="{ 'is-active': activeItemId === item.uid && !isPreviewMode, 'is-editable': !isPreviewMode }"
                                 @click="!isPreviewMode ? setActive(item) : null"
                                 :data-uid="item.uid">

                                {{-- Tombol Hapus --}}
                                <button x-show="activeItemId === item.uid && !isPreviewMode" @click.stop="removeItem(index)" class="absolute -top-3 -right-3 w-7 h-7 bg-red-500 text-white rounded-full shadow-xl z-[60] flex items-center justify-center hover:bg-red-600 outline-none transform transition-transform hover:scale-110">
                                    <i class="mdi mdi-close text-sm font-bold"></i>
                                </button>

                                {{-- 1. RENDER BANNER TOKO (Slider up to 3 Images) --}}
                                <div x-show="item.type === 'banner'" style="display: none;" class="w-full flex flex-col items-center justify-center relative overflow-hidden rounded-xl bg-cover bg-center"
                                     :class="[(!item.config.images || item.config.images.length === 0) ? templateAccentColor : '', item.config.ratio === '16:9' ? 'aspect-video' : (item.config.ratio === '1:1' ? 'aspect-square' : 'aspect-[21/9]')]"
                                     :style="(item.config.images && item.config.images.length > 0) ? { backgroundImage: `url('${item.config.images[0]}')` } : {}">

                                    <div class="absolute inset-0 opacity-10 bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')]" x-show="!item.config.images || item.config.images.length === 0"></div>
                                    <div class="absolute inset-0 bg-black/20" x-show="item.config.images && item.config.images.length > 0"></div>

                                    <div class="relative z-10 p-4 w-full h-full flex flex-col items-center justify-center overflow-hidden">
                                        <h3 class="font-black text-center drop-shadow-md italic w-full truncate px-4"
                                            :class="item.config.ratio === '1:1' ? 'text-2xl' : 'text-xl'"
                                            :style="{ color: item.config.textColor }"
                                            x-text="item.config.title" x-show="item.config.title"></h3>
                                    </div>

                                    {{-- Indikator Dots jika slider > 1 --}}
                                    <div x-show="item.config.images && item.config.images.length > 1" class="absolute bottom-2 flex gap-1.5 z-10">
                                        <template x-for="(img, i) in item.config.images">
                                            <div class="w-1.5 h-1.5 rounded-full" :class="i === 0 ? 'bg-white' : 'bg-white/40'"></div>
                                        </template>
                                    </div>
                                </div>

                                {{-- 2. RENDER BANYAK FOTO (GRID MURNI) --}}
                                <div x-show="item.type === 'carousel'" style="display: none;" class="w-full flex flex-col p-3 bg-white rounded-xl shadow-sm overflow-hidden">
                                    <h4 class="text-[11px] font-black mb-3 px-1 truncate w-full"
                                        :style="{ color: item.config.textColor }"
                                        x-text="item.config.title" x-show="item.config.title"></h4>

                                    {{-- Grid Dinamis 2, 3, atau 4 --}}
                                    <div class="grid gap-2 w-full" :class="item.config.gridType === '2' ? 'grid-cols-2' : (item.config.gridType === '3' ? 'grid-cols-3' : 'grid-cols-4')">
                                        <template x-for="n in parseInt(item.config.gridType || 2)">
                                            <div class="aspect-square bg-slate-100 rounded-lg overflow-hidden border border-slate-200 flex items-center justify-center relative w-full">
                                                <template x-if="item.config.images && item.config.images[n-1]">
                                                    <img :src="item.config.images[n-1]" class="w-full h-full object-cover absolute inset-0 z-0">
                                                </template>
                                                <template x-if="!item.config.images || !item.config.images[n-1]">
                                                    <i class="mdi mdi-image-outline text-slate-300 relative z-10" :class="item.config.gridType === '4' ? 'text-xl' : 'text-3xl'"></i>
                                                </template>
                                            </div>
                                        </template>
                                    </div>
                                </div>

                                {{-- 3. RENDER VIDEO --}}
                                <div x-show="item.type === 'video'" style="display: none;" class="w-full flex flex-col bg-white rounded-xl shadow-sm p-3 overflow-hidden">
                                    <h4 class="text-[11px] font-black mb-3 px-1 truncate w-full"
                                        :style="{ color: item.config.textColor }"
                                        x-text="item.config.title" x-show="item.config.title"></h4>

                                    <div class="w-full aspect-video bg-slate-900 flex flex-col items-center justify-center relative overflow-hidden rounded-lg">
                                        <template x-if="item.config.videoSource === 'youtube' && item.config.videoUrl">
                                            <div class="absolute inset-0 bg-slate-800 flex items-center justify-center bg-cover bg-center" style="background-image: url('https://images.unsplash.com/photo-1611162617474-5b21e879e113?q=80&w=400');">
                                                <div class="absolute inset-0 bg-black/40"></div>
                                                <i class="mdi mdi-youtube text-red-600 text-6xl relative z-10 drop-shadow-xl"></i>
                                            </div>
                                        </template>

                                        <template x-if="item.config.videoSource === 'local' && item.config.videoFile">
                                            <video :src="item.config.videoFile" class="absolute inset-0 w-full h-full object-cover" autoplay muted loop></video>
                                        </template>

                                        <template x-if="(!item.config.videoUrl && item.config.videoSource === 'youtube') || (!item.config.videoFile && item.config.videoSource === 'local')">
                                            <div class="flex flex-col items-center opacity-50 relative z-10">
                                                <i class="mdi mdi-video-outline text-white text-5xl mb-2"></i>
                                                <span class="text-[10px] text-white font-bold mt-1" x-text="item.config.videoSource === 'local' ? 'Video Belum Diupload' : 'Link Youtube Belum Diisi'"></span>
                                            </div>
                                        </template>
                                    </div>
                                </div>

                                {{-- 4. RENDER KATEGORI --}}
                                <div x-show="item.type === 'kategori'" style="display: none;" class="p-3 bg-white rounded-xl shadow-sm overflow-hidden">
                                    <h4 class="text-[11px] font-black mb-3 pl-2 border-l-2 border-blue-500 truncate w-full"
                                        :style="{ color: item.config.textColor }"
                                        x-text="item.config.title" x-show="item.config.title"></h4>
                                    <div class="grid grid-cols-4 gap-2">
                                        <div class="flex flex-col items-center gap-1.5"><div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-500 flex items-center justify-center border border-blue-100"><i class="mdi mdi-tshirt-crew text-xl"></i></div><span class="text-[9px] font-bold text-slate-600">Pakaian</span></div>
                                        <div class="flex flex-col items-center gap-1.5"><div class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-500 flex items-center justify-center border border-rose-100"><i class="mdi mdi-shoe-sneaker text-xl"></i></div><span class="text-[9px] font-bold text-slate-600">Sepatu</span></div>
                                        <div class="flex flex-col items-center gap-1.5"><div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-500 flex items-center justify-center border border-emerald-100"><i class="mdi mdi-watch text-xl"></i></div><span class="text-[9px] font-bold text-slate-600">Aksesoris</span></div>
                                        <div class="flex flex-col items-center gap-1.5"><div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-500 flex items-center justify-center border border-slate-200"><i class="mdi mdi-dots-horizontal text-xl"></i></div><span class="text-[9px] font-bold text-slate-600">Lainnya</span></div>
                                    </div>
                                </div>

                                {{-- 5. RENDER PRODUK (Mendukung Horizontal Scroll & Vertical Grid) --}}
                                <div x-show="item.type === 'produk'" style="display: none;" class="py-3 px-1 bg-transparent overflow-hidden">
                                    <div class="flex justify-between items-center mb-3 px-2">
                                        <h4 class="text-[11px] font-black uppercase tracking-wide truncate flex-1 border-l-2 border-blue-500 pl-2"
                                            :style="{ color: item.config.textColor }"
                                            x-text="item.config.title"></h4>
                                        <span class="text-[9px] font-bold text-blue-600 px-2 py-1 rounded bg-blue-100/50 flex-shrink-0 ml-2">Lihat Semua</span>
                                    </div>

                                    {{-- Layout Scroll Samping (Horizontal) --}}
                                    <template x-if="item.config.layout === 'horizontal'">
                                        <div class="flex overflow-x-auto gap-3 pb-2 px-2 hide-scrollbar w-full">
                                            <template x-if="item.config.productSource === 'manual' && item.config.selectedProducts && item.config.selectedProducts.length > 0">
                                                <template x-for="prod in item.config.selectedProducts.slice(0,6)" :key="prod.id">
                                                    <div class="border border-slate-200/80 rounded-xl overflow-hidden shadow-sm bg-white min-w-[130px] w-[130px] flex-shrink-0">
                                                        <div class="aspect-square bg-slate-100 bg-cover bg-center" :style="{ backgroundImage: `url('${prod.img}')` }"></div>
                                                        <div class="p-2.5">
                                                            <div class="text-[10px] font-bold text-slate-700 truncate mb-1" x-text="prod.name"></div>
                                                            <div class="text-xs font-black text-orange-500" x-text="prod.price"></div>
                                                        </div>
                                                    </div>
                                                </template>
                                            </template>
                                            <template x-if="item.config.productSource === 'auto' || !item.config.selectedProducts || item.config.selectedProducts.length === 0">
                                                <template x-for="i in 3">
                                                    <div class="border border-slate-200/80 rounded-xl overflow-hidden shadow-sm bg-white min-w-[130px] w-[130px] flex-shrink-0">
                                                        <div class="aspect-square bg-slate-100 flex items-center justify-center"><i class="mdi mdi-image-outline text-3xl text-slate-300"></i></div>
                                                        <div class="p-2.5">
                                                            <div class="text-[10px] font-bold text-slate-700 truncate mb-1">Produk Terlaris</div>
                                                            <div class="text-xs font-black text-orange-500">Rp 199.000</div>
                                                        </div>
                                                    </div>
                                                </template>
                                            </template>
                                        </div>
                                    </template>

                                    {{-- Layout Grid Kebawah (Vertical) --}}
                                    <template x-if="item.config.layout === 'vertical'">
                                        <div class="grid grid-cols-2 gap-3 px-2 w-full">
                                            <template x-if="item.config.productSource === 'manual' && item.config.selectedProducts && item.config.selectedProducts.length > 0">
                                                <template x-for="prod in item.config.selectedProducts.slice(0,4)" :key="prod.id">
                                                    <div class="border border-slate-200/80 rounded-xl overflow-hidden shadow-sm bg-white">
                                                        <div class="aspect-square bg-slate-100 bg-cover bg-center" :style="{ backgroundImage: `url('${prod.img}')` }"></div>
                                                        <div class="p-2.5">
                                                            <div class="text-[10px] font-bold text-slate-700 truncate mb-1" x-text="prod.name"></div>
                                                            <div class="text-xs font-black text-orange-500" x-text="prod.price"></div>
                                                        </div>
                                                    </div>
                                                </template>
                                            </template>
                                            <template x-if="item.config.productSource === 'auto' || !item.config.selectedProducts || item.config.selectedProducts.length === 0">
                                                <template x-for="i in 4">
                                                    <div class="border border-slate-200/80 rounded-xl overflow-hidden shadow-sm bg-white">
                                                        <div class="aspect-square bg-slate-100 flex items-center justify-center"><i class="mdi mdi-image-outline text-3xl text-slate-300"></i></div>
                                                        <div class="p-2.5">
                                                            <div class="text-[10px] font-bold text-slate-700 truncate mb-1">Produk Terlaris</div>
                                                            <div class="text-xs font-black text-orange-500">Rp 199.000</div>
                                                        </div>
                                                    </div>
                                                </template>
                                            </template>
                                        </div>
                                    </template>

                                </div>

                            </div>

                            {{-- KONTROL MENGAMBANG (UP/DOWN/DELETE) --}}
                            <div x-show="activeItemId === item.uid && !isPreviewMode" class="absolute -right-14 top-0 flex flex-col gap-2 z-[60]">
                                <button @click.stop="moveUp(index)" class="w-10 h-10 bg-white border border-slate-200 shadow-md rounded-full flex items-center justify-center text-slate-600 hover:text-blue-600 hover:bg-blue-50 disabled:opacity-30 disabled:hover:bg-white disabled:hover:text-slate-600 transition-all outline-none" :disabled="index === 0"><i class="mdi mdi-arrow-up text-lg"></i></button>
                                <button @click.stop="moveDown(index)" class="w-10 h-10 bg-white border border-slate-200 shadow-md rounded-full flex items-center justify-center text-slate-600 hover:text-blue-600 hover:bg-blue-50 disabled:opacity-30 disabled:hover:bg-white disabled:hover:text-slate-600 transition-all outline-none" :disabled="index === canvasItems.length - 1"><i class="mdi mdi-arrow-down text-lg"></i></button>
                            </div>
                        </template>
                    </div>
                </div>

            </div>
        </section>

        {{-- =======================================
             KOLOM 3: PANEL PENGATURAN KANAN
             ======================================= --}}
        <aside x-show="!isPreviewMode" x-transition.opacity.duration.300ms class="w-[340px] bg-white border-l border-slate-200 flex flex-col flex-shrink-0 z-20 shadow-[-4px_0_24px_rgba(0,0,0,0.03)]">

            <div class="p-4 border-b border-slate-100 bg-slate-50 flex justify-between items-center flex-shrink-0">
                <h3 class="text-sm font-black text-slate-800" x-text="activeItem ? getSettingTitle(activeItem.type) : 'Pengaturan Latar Toko'"></h3>
                <button x-show="activeItem" @click="activeItemId = null" class="w-8 h-8 rounded-lg bg-white border border-slate-200 text-slate-400 hover:text-red-500 hover:bg-red-50 hover:border-red-200 flex items-center justify-center transition-colors outline-none"><i class="mdi mdi-close"></i></button>
            </div>

            <div class="p-5 overflow-y-auto flex-1 hide-scrollbar bg-white">

                {{-- JIKA KOSONG: PENGATURAN HEADER TOKO --}}
                <div x-show="!activeItem">
                    <div class="bg-blue-50 border border-blue-100 p-4 rounded-2xl mb-6 flex gap-3">
                        <i class="mdi mdi-information text-blue-500 text-xl"></i>
                        <p class="text-[11px] text-blue-800 font-medium leading-relaxed">Upload gambar kustom untuk latar belakang Header Toko agar tampilan lebih profesional.</p>
                    </div>

                    <h5 class="text-xs font-black text-slate-800 mb-2">Upload Gambar Latar</h5>

                    <div class="w-full aspect-[21/9] border-2 border-dashed border-slate-300 bg-slate-50 flex flex-col items-center justify-center relative cursor-pointer hover:border-blue-500 hover:bg-blue-50 transition-colors group rounded-2xl overflow-hidden mb-3 bg-cover bg-center"
                         @click="$refs.headerUploader.click()"
                         :style="uploadedHeader ? { backgroundImage: `url('${uploadedHeader}')` } : {}">

                        <div class="relative z-10 flex flex-col items-center w-full h-full justify-center transition-opacity" :class="uploadedHeader ? 'opacity-0 hover:opacity-100 bg-black/60 backdrop-blur-sm' : ''">
                            <div class="w-10 h-10 bg-white rounded-full flex items-center justify-center shadow-sm border border-slate-200 mb-2 group-hover:scale-110 group-hover:text-blue-600 transition-all text-slate-400">
                                <i class="mdi mdi-cloud-upload-outline text-xl"></i>
                            </div>
                            <span class="text-[10px] font-bold" :class="uploadedHeader ? 'text-white' : 'text-slate-600'" x-text="uploadedHeader ? 'Ganti Gambar' : 'Pilih Gambar'"></span>
                        </div>
                    </div>

                    <button x-show="uploadedHeader" @click="uploadedHeader = null" class="w-full py-2.5 text-[11px] font-bold text-red-500 hover:bg-red-50 rounded-xl mb-4 transition-colors outline-none border border-red-100">Hapus Header Kustom</button>

                    <ul class="text-[10px] font-medium text-slate-500 list-disc pl-4 space-y-1.5 mb-6">
                        <li>Rekomendasi ukuran: 1200 x 518 px</li>
                        <li>Maksimal file: 2 MB (JPG, PNG)</li>
                    </ul>

                    {{-- MENU WARNA BORDER KANVAS KOSONG --}}
                    <div class="mt-6 pt-6 border-t border-slate-200">
                        <h5 class="text-xs font-black text-slate-800 mb-2">Warna Garis Kanvas Kosong</h5>
                        <p class="text-[10px] text-slate-500 mb-3 leading-relaxed">Pilih warna garis putus-putus pada area kanvas kosong.</p>

                        <div class="flex items-center gap-3 bg-slate-50 p-2 border border-slate-200 rounded-xl shadow-sm">
                            <div class="relative w-8 h-8 rounded-lg overflow-hidden border border-slate-200 shadow-sm flex-shrink-0 cursor-pointer">
                                <input type="color" x-model="emptyCanvasBorderColor" class="absolute -top-2 -left-2 w-16 h-16 cursor-pointer outline-none border-none p-0 bg-transparent">
                            </div>
                            <div class="flex-1 text-xs font-bold text-slate-600 uppercase tracking-wider" x-text="emptyCanvasBorderColor"></div>
                        </div>
                    </div>
                </div>

                {{-- JIKA AKTIF: FORM DINAMIS KOMPONEN --}}
                <div x-show="activeItem !== null" style="display: none;" x-data="{ currentConfig: {} }" x-effect="if(activeItem) currentConfig = activeItem.config">

                    {{-- Input Judul & Warna Teks --}}
                    <div class="mb-6 bg-slate-50 border border-slate-200 p-4 rounded-xl">
                        <label class="block text-[11px] font-black text-slate-500 mb-2 uppercase tracking-wider">Teks Judul</label>
                        <input type="text" x-model="currentConfig.title" @input="updateItemConfig()" class="w-full px-4 py-2.5 border border-slate-300 rounded-lg bg-white text-sm font-bold text-slate-800 focus:border-blue-500 outline-none transition-all placeholder:text-slate-400 mb-3" placeholder="Kosongkan jika tanpa judul">

                        <label class="block text-[11px] font-black text-slate-500 mb-2 uppercase tracking-wider">Warna Teks</label>
                        <div class="flex gap-2">
                            <button @click="currentConfig.textColor = '#1e293b'; updateItemConfig()" class="w-8 h-8 rounded-full border-2 shadow-sm outline-none" :class="currentConfig.textColor === '#1e293b' ? 'border-blue-500 scale-110' : 'border-slate-200 hover:scale-105'" style="background: #1e293b;"></button>
                            <button @click="currentConfig.textColor = '#ffffff'; updateItemConfig()" class="w-8 h-8 rounded-full border-2 shadow-sm outline-none" :class="currentConfig.textColor === '#ffffff' ? 'border-blue-500 scale-110' : 'border-slate-200 hover:scale-105'" style="background: #ffffff;"></button>
                            <button @click="currentConfig.textColor = '#2563eb'; updateItemConfig()" class="w-8 h-8 rounded-full border-2 shadow-sm outline-none" :class="currentConfig.textColor === '#2563eb' ? 'border-blue-500 scale-110' : 'border-white hover:scale-105'" style="background: #2563eb;"></button>
                            <button @click="currentConfig.textColor = '#ef4444'; updateItemConfig()" class="w-8 h-8 rounded-full border-2 shadow-sm outline-none" :class="currentConfig.textColor === '#ef4444' ? 'border-blue-500 scale-110' : 'border-white hover:scale-105'" style="background: #ef4444;"></button>
                        </div>
                    </div>

                    {{-- Form: Banner (Maks 3 Gambar/Slider) --}}
                    <div x-show="activeItem && activeItem.type === 'banner'">

                        <div class="mb-5">
                            <h5 class="text-xs font-black text-slate-800 mb-2">Pilih Layout Rasio</h5>
                            <div class="flex gap-2 bg-slate-50 p-1.5 rounded-lg border border-slate-200">
                                <button @click="currentConfig.ratio = '2:1'; updateItemConfig()" class="flex-1 py-1.5 text-[10px] font-bold rounded shadow-sm outline-none transition-all" :class="currentConfig.ratio === '2:1' ? 'bg-white text-blue-600 border border-slate-200' : 'text-slate-500 hover:bg-slate-100'">2:1 (Default)</button>
                                <button @click="currentConfig.ratio = '16:9'; updateItemConfig()" class="flex-1 py-1.5 text-[10px] font-bold rounded outline-none transition-all" :class="currentConfig.ratio === '16:9' ? 'bg-white text-blue-600 border border-slate-200 shadow-sm' : 'text-slate-500 hover:bg-slate-100'">16:9</button>
                                <button @click="currentConfig.ratio = '1:1'; updateItemConfig()" class="flex-1 py-1.5 text-[10px] font-bold rounded outline-none transition-all" :class="currentConfig.ratio === '1:1' ? 'bg-white text-blue-600 border border-slate-200 shadow-sm' : 'text-slate-500 hover:bg-slate-100'">1:1 (Persegi)</button>
                            </div>
                        </div>

                        <div class="mb-4">
                            <div class="flex justify-between items-center mb-2">
                                <h5 class="text-xs font-black text-slate-800">Upload Foto Banner</h5>
                                <span class="text-[10px] font-bold text-slate-400" x-text="currentConfig.images ? `${currentConfig.images.length} / 3` : '0 / 3'"></span>
                            </div>
                            <ul class="text-[10px] font-medium text-slate-500 list-disc pl-4 mb-4 space-y-1">
                                <li>Maksimal file: 2 MB (JPG, PNG) per gambar</li>
                                <li>Batas maksimal: 3 foto untuk slider otomatis.</li>
                            </ul>

                            {{-- List Gambar Uploaded --}}
                            <div class="space-y-3 mb-4" x-show="currentConfig.images && currentConfig.images.length > 0">
                                <template x-for="(img, imgIdx) in currentConfig.images" :key="imgIdx">
                                    <div class="flex gap-3 bg-slate-50 p-2.5 rounded-xl border border-slate-200">
                                        <div class="w-16 h-12 rounded bg-slate-200 flex-shrink-0 bg-cover bg-center border border-slate-300" :style="{ backgroundImage: `url('${img}')` }"></div>
                                        <div class="flex-1 flex flex-col justify-center">
                                            <input type="text" placeholder="Link (Opsional)" class="w-full bg-white border border-slate-200 text-[10px] rounded px-2 py-1.5 outline-none focus:border-blue-400">
                                        </div>
                                        <button @click="removeComponentImage(imgIdx)" class="text-slate-400 hover:text-red-500 transition-colors p-1 outline-none"><i class="mdi mdi-delete"></i></button>
                                    </div>
                                </template>
                            </div>

                            {{-- Tombol Upload --}}
                            <button x-show="!currentConfig.images || currentConfig.images.length < 3"
                                    @click="$refs.componentUploader.click()"
                                    class="w-full py-4 border-2 border-dashed border-blue-300 bg-blue-50/50 text-blue-600 text-xs font-bold rounded-xl hover:bg-blue-100 hover:border-blue-400 transition-colors flex flex-col items-center justify-center gap-1 outline-none">
                                <i class="mdi mdi-plus text-xl"></i> Tambah Foto Slider
                            </button>
                        </div>
                    </div>

                    {{-- Form: Carousel (Banyak Foto Grid) --}}
                    <div x-show="activeItem && activeItem.type === 'carousel'">

                        <div class="mb-5">
                            <h5 class="text-xs font-black text-slate-800 mb-2">Pilih Layout Grid</h5>
                            <p class="text-[10px] text-slate-500 mb-3">Tentukan jumlah foto yang ingin disejajarkan.</p>
                            <div class="flex gap-2 bg-slate-50 p-1.5 rounded-lg border border-slate-200">
                                <button @click="changeGridType('2')" class="flex-1 py-2 text-[10px] font-bold rounded shadow-sm outline-none transition-all flex flex-col items-center gap-1" :class="currentConfig.gridType === '2' ? 'bg-white text-blue-600 border border-slate-200' : 'text-slate-500 hover:bg-slate-100'">
                                    <div class="flex gap-0.5"><div class="w-3 h-3 bg-current rounded-sm"></div><div class="w-3 h-3 bg-current rounded-sm opacity-50"></div></div>
                                    2 Foto
                                </button>
                                <button @click="changeGridType('3')" class="flex-1 py-2 text-[10px] font-bold rounded outline-none transition-all flex flex-col items-center gap-1" :class="currentConfig.gridType === '3' ? 'bg-white text-blue-600 border border-slate-200 shadow-sm' : 'text-slate-500 hover:bg-slate-100'">
                                    <div class="flex gap-0.5"><div class="w-2.5 h-3 bg-current rounded-sm"></div><div class="w-2.5 h-3 bg-current rounded-sm opacity-70"></div><div class="w-2.5 h-3 bg-current rounded-sm opacity-40"></div></div>
                                    3 Foto
                                </button>
                                <button @click="changeGridType('4')" class="flex-1 py-2 text-[10px] font-bold rounded outline-none transition-all flex flex-col items-center gap-1" :class="currentConfig.gridType === '4' ? 'bg-white text-blue-600 border border-slate-200 shadow-sm' : 'text-slate-500 hover:bg-slate-100'">
                                    <div class="flex gap-0.5"><div class="w-2 h-3 bg-current rounded-sm"></div><div class="w-2 h-3 bg-current rounded-sm opacity-70"></div><div class="w-2 h-3 bg-current rounded-sm opacity-50"></div><div class="w-2 h-3 bg-current rounded-sm opacity-30"></div></div>
                                    4 Foto
                                </button>
                            </div>
                        </div>

                        <div class="mb-4">
                            <div class="flex justify-between items-center mb-2">
                                <h5 class="text-xs font-black text-slate-800">Daftar Foto Grid</h5>
                                <span class="text-[10px] font-bold text-slate-400" x-text="currentConfig.images ? `${currentConfig.images.length} / ${currentConfig.maxImages}` : `0 / 2`"></span>
                            </div>

                            {{-- List Gambar --}}
                            <div class="space-y-3 mb-4" x-show="currentConfig.images && currentConfig.images.length > 0">
                                <template x-for="(img, imgIdx) in currentConfig.images" :key="imgIdx">
                                    <div class="flex gap-3 bg-slate-50 p-2.5 rounded-xl border border-slate-200">
                                        <div class="w-12 h-12 rounded bg-slate-200 flex-shrink-0 bg-cover bg-center border border-slate-300" :style="{ backgroundImage: `url('${img}')` }"></div>
                                        <div class="flex-1 flex flex-col justify-center">
                                            <input type="text" placeholder="Link Target (Opsional)" class="w-full bg-white border border-slate-200 text-[10px] rounded px-2 py-1.5 outline-none focus:border-blue-400">
                                        </div>
                                        <button @click="removeComponentImage(imgIdx)" class="text-slate-400 hover:text-red-500 transition-colors p-1 outline-none"><i class="mdi mdi-delete"></i></button>
                                    </div>
                                </template>
                            </div>

                            {{-- Tombol Upload --}}
                            <button x-show="!currentConfig.images || currentConfig.images.length < currentConfig.maxImages"
                                    @click="$refs.componentUploader.click()"
                                    class="w-full py-3 border-2 border-dashed border-blue-300 bg-blue-50/50 text-blue-600 text-xs font-bold rounded-xl hover:bg-blue-100 hover:border-blue-400 transition-colors flex items-center justify-center gap-2 outline-none">
                                <i class="mdi mdi-plus text-lg"></i> Tambah Foto Ke-<span x-text="(currentConfig.images ? currentConfig.images.length : 0) + 1"></span>
                            </button>
                        </div>
                    </div>

                    {{-- Form: Produk --}}
                    <div x-show="activeItem && activeItem.type === 'produk'">
                        <div class="mb-5">
                            <h5 class="text-xs font-black text-slate-800 mb-2">Pilih Layout Produk</h5>
                            <div class="flex gap-2 bg-slate-50 p-1.5 rounded-lg border border-slate-200">
                                <button @click="currentConfig.layout = 'horizontal'; updateItemConfig()" class="flex-1 py-2 text-[10px] font-bold rounded outline-none transition-all flex flex-col items-center gap-1" :class="currentConfig.layout === 'horizontal' ? 'bg-white text-blue-600 border border-slate-200 shadow-sm' : 'text-slate-500 hover:bg-slate-100'">
                                    <i class="mdi mdi-gesture-swipe-horizontal text-lg"></i> Scroll Samping (Max 6)
                                </button>
                                <button @click="currentConfig.layout = 'vertical'; updateItemConfig()" class="flex-1 py-2 text-[10px] font-bold rounded shadow-sm outline-none transition-all flex flex-col items-center gap-1" :class="currentConfig.layout === 'vertical' ? 'bg-white text-blue-600 border border-slate-200' : 'text-slate-500 hover:bg-slate-100'">
                                    <i class="mdi mdi-grid text-lg"></i> Grid Kebawah (Max 4)
                                </button>
                            </div>
                        </div>

                        <h5 class="text-xs font-black text-slate-800 mb-2">Sumber Data Produk</h5>
                        <p class="text-[10px] font-medium text-slate-500 mb-3">Pilih produk secara manual dari etalase toko Anda atau biarkan sistem memilih otomatis produk terlaris.</p>

                        <div class="flex flex-col gap-3 mb-5">
                            <label class="flex items-center gap-3 p-4 border-2 rounded-xl cursor-pointer transition-colors" :class="currentConfig.productSource === 'auto' ? 'border-blue-500 bg-blue-50' : 'border-slate-200 bg-white hover:border-slate-300'">
                                <input type="radio" name="prod_src" value="auto" x-model="currentConfig.productSource" @change="updateItemConfig()" class="w-4 h-4 text-blue-600 focus:ring-blue-500">
                                <div>
                                    <div class="text-sm font-black" :class="currentConfig.productSource === 'auto' ? 'text-blue-700' : 'text-slate-700'">Pilih Otomatis</div>
                                    <div class="text-[9px] font-bold mt-0.5" :class="currentConfig.productSource === 'auto' ? 'text-blue-500/80' : 'text-slate-500'">Sistem memunculkan produk terlaris otomatis</div>
                                </div>
                            </label>

                            <label class="flex items-center gap-3 p-4 border-2 rounded-xl cursor-pointer transition-colors" :class="currentConfig.productSource === 'manual' ? 'border-blue-500 bg-blue-50' : 'border-slate-200 bg-white hover:border-slate-300'">
                                <input type="radio" name="prod_src" value="manual" x-model="currentConfig.productSource" @change="updateItemConfig()" class="w-4 h-4 text-blue-600 focus:ring-blue-500">
                                <div class="flex-1">
                                    <div class="text-sm font-black" :class="currentConfig.productSource === 'manual' ? 'text-blue-700' : 'text-slate-700'">Pilih Manual</div>
                                    <div class="text-[9px] font-bold mt-0.5" :class="currentConfig.productSource === 'manual' ? 'text-blue-500/80' : 'text-slate-500'">Anda dapat memilih maksimal 8 produk.</div>
                                </div>
                            </label>
                        </div>

                        {{-- Area Produk Terpilih (Muncul Hanya Jika Manual) --}}
                        <div x-show="currentConfig.productSource === 'manual'" class="mt-2 p-3 bg-slate-50 border border-slate-200 rounded-xl">
                            <div class="flex justify-between items-center mb-3">
                                <h6 class="text-[10px] font-bold text-slate-700">Produk Terpilih (<span x-text="currentConfig.selectedProducts ? currentConfig.selectedProducts.length : 0"></span>/8)</h6>
                            </div>

                            <div class="space-y-2 mb-3" x-show="currentConfig.selectedProducts && currentConfig.selectedProducts.length > 0">
                                <template x-for="(prod, pIdx) in currentConfig.selectedProducts" :key="prod.id">
                                    <div class="flex items-center gap-2 bg-white p-2 rounded border border-slate-200">
                                        <img :src="prod.img" class="w-8 h-8 rounded object-cover border border-slate-100">
                                        <div class="flex-1 overflow-hidden">
                                            <div class="text-[9px] font-bold text-slate-700 truncate" x-text="prod.name"></div>
                                            <div class="text-[10px] font-black text-orange-500" x-text="prod.price"></div>
                                        </div>
                                        <button @click="removeSelectedProduct(pIdx)" class="text-red-400 hover:text-red-600 outline-none"><i class="mdi mdi-close-circle"></i></button>
                                    </div>
                                </template>
                            </div>

                            <button @click="openProductModal()" class="w-full py-2.5 bg-slate-800 text-white text-xs font-bold rounded-lg hover:bg-slate-900 transition-colors shadow-md outline-none flex items-center justify-center gap-2" :disabled="currentConfig.selectedProducts && currentConfig.selectedProducts.length >= 8">
                                <i class="mdi mdi-plus-box-outline text-base"></i> Tambah Produk
                            </button>
                        </div>
                    </div>

                    {{-- Form: Video (Pilih Source YT atau Lokal) --}}
                    <div x-show="activeItem && activeItem.type === 'video'">
                        <h5 class="text-xs font-black text-slate-800 mb-2">Sumber Video</h5>
                        <div class="flex gap-2 mb-4 bg-slate-50 p-1.5 rounded-lg border border-slate-200">
                            <button @click="currentConfig.videoSource = 'youtube'; updateItemConfig()" class="flex-1 py-1.5 text-[11px] font-bold rounded outline-none transition-all flex items-center justify-center gap-1" :class="currentConfig.videoSource === 'youtube' ? 'bg-white text-red-600 border border-slate-200 shadow-sm' : 'text-slate-500 hover:bg-slate-100'"><i class="mdi mdi-youtube text-lg"></i> YouTube</button>
                            <button @click="currentConfig.videoSource = 'local'; updateItemConfig()" class="flex-1 py-1.5 text-[11px] font-bold rounded outline-none transition-all flex items-center justify-center gap-1" :class="currentConfig.videoSource === 'local' ? 'bg-white text-blue-600 border border-slate-200 shadow-sm' : 'text-slate-500 hover:bg-slate-100'"><i class="mdi mdi-monitor-arrow-down text-lg"></i> File Lokal</button>
                        </div>

                        {{-- Input Youtube --}}
                        <div x-show="currentConfig.videoSource === 'youtube'">
                            <label class="block text-[10px] font-black text-slate-500 mb-1.5 uppercase">Tautan Video Youtube</label>
                            <input type="url" x-model="currentConfig.videoUrl" @input="updateItemConfig()" placeholder="https://youtube.com/watch?v=..." class="w-full px-4 py-3 border border-slate-200 rounded-xl bg-slate-50 text-sm font-bold text-slate-800 focus:bg-white focus:border-red-500 focus:ring-4 focus:ring-red-50 outline-none transition-all mb-3">
                        </div>

                        {{-- Upload File Lokal --}}
                        <div x-show="currentConfig.videoSource === 'local'">
                            <label class="block text-[10px] font-black text-slate-500 mb-1.5 uppercase">Upload Video MP4</label>
                            <div class="w-full aspect-video border-2 border-dashed border-slate-300 bg-slate-50 flex flex-col items-center justify-center hover:border-blue-500 hover:bg-blue-50 transition-colors cursor-pointer rounded-2xl group overflow-hidden mb-3 relative" @click="$refs.videoUploader.click()">
                                <template x-if="currentConfig.videoFile">
                                    <video :src="currentConfig.videoFile" class="w-full h-full object-cover absolute inset-0 z-0"></video>
                                </template>
                                <div class="relative z-10 flex flex-col items-center p-3 text-center" :class="currentConfig.videoFile ? 'opacity-0 hover:opacity-100 bg-black/60 w-full h-full justify-center backdrop-blur-sm' : ''">
                                    <div class="w-10 h-10 bg-white rounded-full flex items-center justify-center shadow-sm border border-slate-200 mb-2 group-hover:scale-110 transition-all" :class="currentConfig.videoFile ? 'text-white bg-transparent border-white/50' : 'text-blue-500'">
                                        <i class="mdi mdi-video-plus-outline text-xl"></i>
                                    </div>
                                    <span class="text-[10px] font-bold" :class="currentConfig.videoFile ? 'text-white' : 'text-slate-600'" x-text="currentConfig.videoFile ? 'Ganti Video' : 'Pilih File Video'"></span>
                                </div>
                            </div>
                            <ul class="text-[9px] font-medium text-slate-500 list-disc pl-4 space-y-1 mb-3">
                                <li>Format: MP4, Max: 10MB.</li>
                            </ul>
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
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-3xl flex flex-col overflow-hidden transform transition-all modal-enter" @click.outside="showProductModal = false">

            <div class="px-6 py-4 border-b border-slate-200 flex justify-between items-center bg-slate-50">
                <h3 class="text-base font-black text-slate-800">Pilih Produk Etalase</h3>
                <button @click="showProductModal = false" class="text-slate-400 hover:text-red-500 transition-colors outline-none"><i class="mdi mdi-close text-2xl"></i></button>
            </div>

            <div class="p-6 overflow-y-auto max-h-[60vh] bg-slate-100">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    {{-- Render Dummy Products --}}
                    <template x-for="prod in availableProducts" :key="prod.id">
                        <div class="bg-white border rounded-xl p-3 cursor-pointer transition-all flex flex-col hover:shadow-md"
                             :class="isProductTempSelected(prod) ? 'border-blue-500 ring-2 ring-blue-100' : 'border-slate-200 hover:border-blue-300'"
                             @click="toggleTempProduct(prod)">

                            <div class="aspect-square bg-slate-100 rounded-lg mb-3 overflow-hidden border border-slate-100 relative">
                                <img :src="prod.img" class="w-full h-full object-cover">
                                {{-- Ceklis Overlay --}}
                                <div x-show="isProductTempSelected(prod)" class="absolute top-2 right-2 w-6 h-6 bg-blue-600 text-white rounded-full flex items-center justify-center shadow-md">
                                    <i class="mdi mdi-check text-sm font-bold"></i>
                                </div>
                            </div>
                            <div class="text-xs font-bold text-slate-700 leading-tight mb-1 truncate" x-text="prod.name"></div>
                            <div class="text-sm font-black text-orange-600" x-text="prod.price"></div>
                        </div>
                    </template>
                </div>
            </div>

            <div class="px-6 py-4 border-t border-slate-200 bg-white flex justify-between items-center">
                <div class="text-xs font-bold text-slate-500">Terpilih: <span class="text-blue-600 font-black text-sm" x-text="tempSelectedProducts.length"></span> / 8</div>
                <div class="flex gap-3">
                    <button @click="showProductModal = false" class="px-5 py-2 rounded-lg font-bold text-sm text-slate-600 bg-slate-100 hover:bg-slate-200 transition-colors outline-none">Batal</button>
                    <button @click="saveProductSelection()" class="px-6 py-2 rounded-lg font-bold text-sm text-white bg-blue-600 hover:bg-blue-700 shadow-md shadow-blue-500/30 transition-colors flex items-center gap-2 outline-none">
                        <i class="mdi mdi-check-circle"></i> Konfirmasi Pilihan
                    </button>
                </div>
            </div>
        </div>
    </div>

</div>

<script>
// HARDCODE DATA JAVASCRIPT
    const TEMPLATES_DATA = [
        {id:1, name:'Oceanic Premium', hc:'bg-gradient-to-r from-blue-600 to-indigo-800', ac:'bg-gradient-to-br from-blue-500 to-indigo-600', layout:['banner', 'kategori', 'produk']},
        {id:2, name:'Eco Harvest', hc:'bg-gradient-to-r from-emerald-600 to-teal-800', ac:'bg-gradient-to-br from-emerald-500 to-green-600', layout:['kategori', 'carousel', 'produk']},
        {id:3, name:'Sunset Flash', hc:'bg-gradient-to-r from-orange-500 to-red-600', ac:'bg-gradient-to-br from-orange-500 to-red-500', layout:['video', 'produk', 'banner']},
        {id:4, name:'Midnight Luxury', hc:'bg-slate-900', ac:'bg-gradient-to-br from-slate-700 to-slate-900', layout:['carousel', 'kategori', 'produk']},
        {id:5, name:'Pink Blossom', hc:'bg-gradient-to-r from-pink-500 to-rose-600', ac:'bg-gradient-to-br from-pink-400 to-rose-500', layout:['banner', 'produk', 'kategori']},
        {id:6, name:'Neon Cyber', hc:'bg-gradient-to-r from-purple-700 to-indigo-800', ac:'bg-gradient-to-br from-purple-600 to-cyan-500', layout:['video', 'carousel', 'produk']},
        {id:7, name:'Minimalist Clean', hc:'bg-slate-800', ac:'bg-slate-800', layout:['kategori', 'produk', 'produk']},
        {id:8, name:'Pastel Dream', hc:'bg-gradient-to-r from-fuchsia-500 to-purple-600', ac:'bg-gradient-to-br from-violet-400 to-fuchsia-400', layout:['carousel', 'banner', 'produk']},
        {id:9, name:'Earthy Warm', hc:'bg-gradient-to-r from-amber-700 to-orange-800', ac:'bg-gradient-to-br from-amber-600 to-orange-700', layout:['banner', 'kategori', 'produk']},
        {id:10, name:'Royal Gold', hc:'bg-gradient-to-r from-yellow-600 to-amber-700', ac:'bg-gradient-to-br from-yellow-500 to-amber-500', layout:['carousel', 'produk', 'kategori']},
        {id:11, name:'Ruby Red', hc:'bg-gradient-to-r from-red-600 to-rose-700', ac:'bg-gradient-to-br from-red-500 to-rose-500', layout:['video', 'kategori', 'produk']},
        {id:12, name:'Sky Blue', hc:'bg-gradient-to-r from-sky-400 to-blue-500', ac:'bg-gradient-to-br from-sky-300 to-blue-400', layout:['banner', 'carousel', 'produk']},
        {id:13, name:'Vintage Retro', hc:'bg-gradient-to-r from-stone-600 to-orange-800', ac:'bg-gradient-to-br from-stone-400 to-orange-300', layout:['kategori', 'produk', 'banner']},
        {id:14, name:'Sporty Active', hc:'bg-gradient-to-r from-yellow-400 to-yellow-500 text-slate-900', ac:'bg-gradient-to-br from-slate-800 to-black', layout:['video', 'produk', 'produk']},
        {id:15, name:'Lavender Magic', hc:'bg-gradient-to-r from-indigo-500 to-purple-600', ac:'bg-gradient-to-br from-indigo-400 to-purple-400', layout:['banner', 'kategori', 'produk']},
        {id:16, name:'Mint Fresh', hc:'bg-gradient-to-r from-teal-400 to-emerald-400', ac:'bg-gradient-to-br from-teal-300 to-emerald-300', layout:['carousel', 'kategori', 'produk']},
        {id:17, name:'Dark Maroon', hc:'bg-gradient-to-r from-rose-900 to-red-950', ac:'bg-gradient-to-br from-rose-800 to-red-800', layout:['video', 'banner', 'produk']},
        {id:18, name:'Silver Steel', hc:'bg-gradient-to-r from-slate-400 to-slate-600', ac:'bg-gradient-to-br from-slate-300 to-slate-400 text-slate-800', layout:['kategori', 'carousel', 'produk']},
        {id:19, name:'Peach Perfect', hc:'bg-gradient-to-r from-rose-400 to-orange-400', ac:'bg-gradient-to-br from-rose-300 to-orange-300', layout:['banner', 'produk', 'kategori']},
        {id:20, name:'Galaxy Night', hc:'bg-gradient-to-r from-indigo-900 to-fuchsia-900', ac:'bg-gradient-to-br from-indigo-500 to-fuchsia-500', layout:['carousel', 'video', 'produk']}
    ];

    // Simulasi Database Produk
    const DUMMY_PRODUCTS = [
        { id: 101, name: 'Sepatu Sneakers Pria Original', price: 'Rp 250.000', img: 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=200&q=80' },
        { id: 102, name: 'Kaos Polos Cotton Combed 30s', price: 'Rp 45.000', img: 'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?w=200&q=80' },
        { id: 103, name: 'Jam Tangan Kulit Analog', price: 'Rp 175.000', img: 'https://images.unsplash.com/photo-1524592094714-0f0654e20314?w=200&q=80' },
        { id: 104, name: 'Topi Baseball Bordir Custom', price: 'Rp 35.000', img: 'https://images.unsplash.com/photo-1556306535-0f09a5f6f0d5?w=200&q=80' },
        { id: 105, name: 'Tas Ransel Backpack Canvas', price: 'Rp 120.000', img: 'https://images.unsplash.com/photo-1553062407-98eeb64c6a62?w=200&q=80' },
        { id: 106, name: 'Kacamata Hitam Polarized', price: 'Rp 85.000', img: 'https://images.unsplash.com/photo-1511499767150-a48a237f0083?w=200&q=80' },
        { id: 107, name: 'Kemeja Flanel Kasual', price: 'Rp 115.000', img: 'https://images.unsplash.com/photo-1598033129183-c4f50c736f10?w=200&q=80' },
        { id: 108, name: 'Celana Chino Pria', price: 'Rp 135.000', img: 'https://images.unsplash.com/photo-1624378439575-d8705ad7ae80?w=200&q=80' },
    ];

    const generateUid = () => Date.now().toString(36) + Math.random().toString(36).substr(2);

    function pondasiEditor() {
        return {
            templates: TEMPLATES_DATA,
            templateName: 'Kanvas Kosong',
            canvasItems: [],
            activeItemId: null,
            currentTime: '12:00',
            templateHeaderColor: 'bg-slate-800',
            templateAccentColor: 'bg-slate-800',
            isPreviewMode: false,
            uploadedHeader: null,
            emptyCanvasBorderColor: '#cbd5e1', // Default slate-300

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

                // INIT SORTABLE: Solusi Akurat DOM Node Indexing
                this.$nextTick(() => {
                    const palette = document.getElementById('palette-list');
                    const canvas = document.getElementById('canvas-dropzone');

                    if(palette) {
                        new Sortable(palette, {
                            group: { name: 'shared', pull: 'clone', put: false },
                            sort: false, animation: 150
                        });
                    }

                    if(canvas) {
                        new Sortable(canvas, {
                            group: 'shared', animation: 150,
                            draggable: '.group\\/canvas', ghostClass: 'sortable-ghost',
                            onAdd: (evt) => {
                                const type = evt.item.dataset.type;

                                // CARA PALING AKURAT MENGHITUNG INDEX DI DOM
                                let realIndex = 0;
                                let current = evt.item.previousElementSibling;
                                while(current) {
                                    if (current.classList.contains('group/canvas')) realIndex++;
                                    current = current.previousElementSibling;
                                }

                                evt.item.remove();
                                this.addComponent(type, realIndex);
                            },
                            onUpdate: (evt) => {
                                // Hitung target index baru
                                let newRealIndex = 0;
                                let current = evt.item.previousElementSibling;
                                while(current) {
                                    if (current.classList.contains('group/canvas') && current !== evt.item) newRealIndex++;
                                    current = current.previousElementSibling;
                                }

                                // Cari Index Lama di Array Alpine
                                const oldIndex = this.canvasItems.findIndex(i => i.uid === evt.item.dataset.uid);

                                // Revert perubahan DOM Sortable agar Alpine tidak error
                                if(evt.oldIndex < evt.from.childNodes.length) {
                                    evt.from.insertBefore(evt.item, evt.from.childNodes[evt.oldIndex]);
                                } else {
                                    evt.from.appendChild(evt.item);
                                }

                                // Jalankan Modifikasi Array (Reactivity)
                                if(oldIndex !== -1) {
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
                    this.templateName = tpl.name;
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
                if(type === 'banner') return { title: 'Promo Spesial', textColor: '#ffffff', images: [], maxImages: 3, ratio: '2:1' };
                if(type === 'carousel') return { title: 'Banyak Foto Grid', textColor: '#1e293b', images: [], maxImages: 2, gridType: '2' };
                if(type === 'video') return { title: 'Video Promo', textColor: '#ffffff', videoSource: 'youtube', videoUrl: '', videoFile: null };
                if(type === 'kategori') return { title: 'Kategori Pilihan', textColor: '#1e293b' };
                if(type === 'produk') return { title: 'Produk Etalase', textColor: '#1e293b', productSource: 'auto', layout: 'horizontal', selectedProducts: [] };
            },

            getSettingTitle(type) {
                if(type === 'banner') return 'Pengaturan Banner Toko';
                if(type === 'carousel') return 'Pengaturan Banyak Foto (Grid)';
                if(type === 'video') return 'Pengaturan Video';
                if(type === 'kategori') return 'Pengaturan Kategori';
                return 'Pengaturan Daftar Produk';
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
                    this.activeItem.config.maxImages = parseInt(type);
                    if(this.activeItem.config.images.length > parseInt(type)) {
                        this.activeItem.config.images = this.activeItem.config.images.slice(0, parseInt(type));
                    }
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
                    if(this.tempSelectedProducts.length >= 8) {
                        Swal.fire({toast: true, position: 'top-end', icon: 'warning', title: 'Maksimal 8 produk', showConfirmButton: false, timer: 1500});
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
                    title: 'Kosongkan Kanvas?', text: 'Semua komponen akan dihapus.', icon: 'warning',
                    showCancelButton: true, confirmButtonColor: '#ef4444', cancelButtonText: 'Batal', confirmButtonText: 'Ya, Bersihkan',
                    customClass: { popup: 'rounded-2xl' }
                }).then(res => {
                    if(res.isConfirmed) {
                        this.canvasItems = []; this.activeItemId = null;
                        this.templateName = 'Kanvas Kosong'; this.uploadedHeader = null;
                        this.templateHeaderColor = 'bg-slate-800';
                    }
                });
            },

            gantiTemplate() { window.location.href = "{{ route('seller.shop.decoration.template') }}"; },
            togglePreview() { this.isPreviewMode = !this.isPreviewMode; this.activeItemId = null; },

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
                    Swal.fire('Batas Maksimal', `Anda hanya bisa memasukkan maksimal ${this.activeItem.config.maxImages} foto.`, 'warning');
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
                Swal.fire({
                    title: 'Tayangkan Dekorasi?', text: 'Desain ini akan langsung terlihat oleh pembeli.', icon: 'question',
                    showCancelButton: true, confirmButtonColor: '#2563eb', cancelButtonText: 'Batal', confirmButtonText: 'Ya, Tayangkan',
                    customClass: { popup: 'rounded-2xl' }
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({ title: 'Menerapkan...', timer: 1500, didOpen: () => Swal.showLoading(), customClass: { popup: 'rounded-2xl' } }).then(() => {
                            Swal.fire({icon: 'success', title: 'Berhasil!', text: 'Dekorasi toko berhasil ditayangkan.', timer:2500, showConfirmButton:false, customClass: { popup: 'rounded-2xl' }});
                        });
                    }
                });
            }
        }
    }
</script>
</body>
</html>

