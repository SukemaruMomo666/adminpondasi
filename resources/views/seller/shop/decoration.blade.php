@extends('layouts.seller')

@section('title', 'Dekorasi Toko')

@section('content')
{{-- 1. CSS & SCRIPT WAJIB --}}
<style>
    [x-cloak] { display: none !important; }
    .hide-scrollbar::-webkit-scrollbar { display: none; }
    .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

    @keyframes floatSoft {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-6px); }
    }
    .animate-float { animation: floatSoft 3s ease-in-out infinite; }
    .animate-float-delay { animation: floatSoft 3s ease-in-out 1.5s infinite; }

    @keyframes shimmerBtn {
        0% { transform: translateX(-100%); }
        100% { transform: translateX(100%); }
    }
    .animate-shimmer { animation: shimmerBtn 2s infinite; }
</style>

<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

{{-- 2. WRAPPER UTAMA ALPINE JS --}}
<div class="min-h-screen bg-slate-50 p-4 md:p-6 lg:p-8 font-sans text-slate-900"
     x-data="decorLanding()"
     x-cloak>

    {{-- HEADER HALAMAN --}}
    <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4 relative z-20">
        <div>
            <h1 class="text-2xl lg:text-3xl font-black text-slate-900 tracking-tight flex items-center gap-3">
                <div class="w-10 h-10 bg-blue-600 text-white rounded-xl flex items-center justify-center shadow-lg shadow-blue-600/20">
                    <i class="mdi mdi-palette-outline text-2xl leading-none"></i>
                </div>
                Dekorasi Toko
            </h1>
            <p class="text-sm font-medium text-slate-500 mt-2 pl-14">Rancang tampilan toko yang profesional untuk meningkatkan kepercayaan dan rasio konversi pembeli.</p>
        </div>
        <div class="hidden md:flex gap-3">
            <button type="button" @click="isGuideModalOpen = true" class="flex items-center gap-2 px-4 py-2.5 bg-slate-900 hover:bg-black text-white text-sm font-bold rounded-xl transition-all shadow-sm shadow-slate-900/20 outline-none">
                <i class="mdi mdi-book-open-page-variant"></i> Panduan
            </button>
        </div>
    </div>

    {{-- KOTAK PUTIH UTAMA --}}
    <div class="bg-white border border-slate-200 rounded-[2rem] shadow-sm overflow-hidden flex flex-col min-h-[700px] relative z-10">

        {{-- TABS NAVIGASI ATAS --}}
        <div class="bg-white border-b border-slate-200 px-4 sm:px-8 flex justify-between items-end z-40 sticky top-0">
            <div class="flex gap-6 sm:gap-10 overflow-x-auto hide-scrollbar pt-6 w-full">
                <button type="button" @click.prevent="activeTab = 'halaman_toko'"
                        class="pb-4 px-1 text-sm sm:text-base font-black transition-all whitespace-nowrap border-b-4 outline-none"
                        :class="activeTab === 'halaman_toko' ? 'border-blue-600 text-blue-600' : 'border-transparent text-slate-500 hover:text-slate-800'">
                    Dekorasi Halaman Toko
                </button>
                <button type="button" @click.prevent="activeTab = 'kategori'"
                        class="pb-4 px-1 text-sm sm:text-base font-black transition-all whitespace-nowrap border-b-4 outline-none"
                        :class="activeTab === 'kategori' ? 'border-blue-600 text-blue-600' : 'border-transparent text-slate-500 hover:text-slate-800'">
                    Halaman Kategori
                </button>
                <button type="button" @click.prevent="activeTab = 'produk_pilihan'"
                        class="pb-4 px-1 text-sm sm:text-base font-black transition-all whitespace-nowrap flex items-center gap-1.5 border-b-4 outline-none"
                        :class="activeTab === 'produk_pilihan' ? 'border-blue-600 text-blue-600' : 'border-transparent text-slate-500 hover:text-slate-800'">
                    Produk Pilihan Toko
                    <i @click.stop="showInfoProdukPilihan()" class="mdi mdi-information text-slate-300 hover:text-blue-500 text-base font-normal transition-colors cursor-pointer" title="Klik untuk info lebih lanjut"></i>
                </button>
            </div>
        </div>

        {{-- ============================================================== --}}
        {{-- KONTEN TAB 1: DEKORASI HALAMAN TOKO                            --}}
        {{-- ============================================================== --}}
        <div x-show="activeTab === 'halaman_toko'" x-transition.opacity.duration.400ms class="flex-1 p-6 md:p-12 relative overflow-hidden">

            <div class="absolute top-0 right-0 w-full h-full overflow-hidden pointer-events-none z-0">
                <div class="absolute -right-40 -top-40 w-[600px] h-[600px] bg-blue-50 rounded-full blur-3xl opacity-80"></div>
                <div class="absolute right-40 bottom-10 w-[300px] h-[300px] bg-emerald-50 rounded-full blur-3xl opacity-80"></div>
            </div>

            {{-- Sub Tabs Platform (Aplikasi / Desktop) --}}
            <div class="relative z-20 flex gap-2 p-1.5 bg-slate-100 rounded-xl inline-flex mb-12 border border-slate-200/60 shadow-inner">
                <button type="button" @click="subTab = 'aplikasi'" :class="subTab === 'aplikasi' ? 'bg-white text-blue-600 shadow-sm border-slate-200' : 'text-slate-500 hover:text-slate-700 border-transparent'" class="px-6 py-2.5 text-sm font-bold rounded-lg border transition-all flex items-center gap-2 outline-none">
                    <i class="mdi mdi-cellphone text-lg leading-none"></i> Aplikasi Mobile
                </button>
                <button type="button" @click="subTab = 'situs'" :class="subTab === 'situs' ? 'bg-white text-blue-600 shadow-sm border-slate-200' : 'text-slate-500 hover:text-slate-700 border-transparent'" class="px-6 py-2.5 text-sm font-bold rounded-lg border transition-all flex items-center gap-2 outline-none">
                    <i class="mdi mdi-monitor text-lg leading-none"></i> Situs Desktop
                </button>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center relative z-20">

                {{-- KIRI: Teks & Tombol Action --}}
                <div class="lg:col-span-5 order-2 lg:order-1">
                    <div class="inline-flex items-center gap-1.5 px-3 py-1 bg-emerald-50 border border-emerald-200 text-emerald-600 rounded-lg text-[10px] font-black uppercase tracking-widest mb-6">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span> Fitur Premium
                    </div>

                    <h2 class="text-4xl lg:text-5xl font-black text-slate-900 leading-[1.15] mb-6 tracking-tight">
                        Dekorasi tokomu dan dapatkan <span class="text-blue-600">20%+</span> jumlah pengunjung!
                    </h2>

                    <ul class="space-y-4 mb-10">
                        <li class="flex items-start gap-3">
                            <i class="mdi mdi-check-circle text-blue-500 text-xl leading-none"></i>
                            <span class="text-slate-600 font-medium text-sm">Buat dekorasi unik untuk tokomu dengan sistem Drag & Drop.</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <i class="mdi mdi-check-circle text-emerald-500 text-xl leading-none"></i>
                            <span class="text-slate-600 font-medium text-sm">Tingkatkan penjualan dengan identitas visual yang profesional.</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <i class="mdi mdi-check-circle text-amber-500 text-xl leading-none"></i>
                            <span class="text-slate-600 font-medium text-sm">Satu desain, responsif sempurna di layar HP maupun komputer.</span>
                        </li>
                    </ul>

                    {{-- TOMBOL UTAMA DINAMIS BERDASARKAN SUBTAB --}}
                    <button type="button" @click.prevent="goToDecoration()" class="group relative inline-flex items-center justify-center px-8 py-4 bg-blue-600 text-white font-black rounded-2xl overflow-hidden shadow-xl shadow-blue-600/30 transition-all hover:scale-105 active:scale-95 w-full sm:w-auto outline-none z-30 disabled:opacity-70" :disabled="loadingBtn === 'goToDeco'">
                        <div class="absolute inset-0 w-full h-full bg-gradient-to-r from-transparent via-white/20 to-transparent -translate-x-full animate-shimmer opacity-0 group-hover:opacity-100 pointer-events-none"></div>
                        <span class="relative flex items-center gap-2">
                            <i class="mdi" :class="loadingBtn === 'goToDeco' ? 'mdi-loading mdi-spin text-xl' : 'mdi-magic-staff text-xl'"></i>
                            {{-- Teks berubah otomatis --}}
                            <span x-text="subTab === 'aplikasi' ? 'Pilih Template Mobile' : 'Mulai Dekorasi Desktop'"></span>
                        </span>
                    </button>

                </div>

                {{-- KANAN: MOCKUP PREVIEW --}}
                <div class="lg:col-span-7 order-1 lg:order-2 flex justify-center lg:justify-end pointer-events-none relative h-[600px] w-full">

                    {{-- Preview Aplikasi --}}
                    <div x-show="subTab === 'aplikasi'" 
                         x-transition:enter="transition ease-out duration-500" 
                         x-transition:enter-start="opacity-0 scale-95 -translate-x-4" 
                         x-transition:enter-end="opacity-100 scale-100 translate-x-0" 
                         x-transition:leave="transition ease-in duration-300 absolute top-0" 
                         x-transition:leave-start="opacity-100 scale-100 translate-x-0" 
                         x-transition:leave-end="opacity-0 scale-95 -translate-x-4" 
                         class="w-full max-w-[280px]">
                        <div class="w-full h-[580px] bg-slate-900 rounded-[3rem] p-2.5 shadow-[0_20px_50px_-10px_rgba(0,0,0,0.3)] border-[6px] border-slate-800 relative z-10">
                            <div class="w-full h-full bg-slate-50 rounded-[2rem] overflow-hidden flex flex-col relative border border-slate-200">
                                <div class="absolute top-0 left-1/2 -translate-x-1/2 w-24 h-5 bg-slate-900 rounded-b-xl z-50"></div>
                                <div class="h-8 w-full bg-blue-700 flex justify-end items-center px-4 pt-1 z-40 text-[9px] text-white">
                                    <div class="flex gap-1 items-center"><i class="mdi mdi-wifi"></i><i class="mdi mdi-battery"></i></div>
                                </div>
                                <div class="bg-blue-700 text-white pb-4 px-4 shadow-sm relative z-30">
                                    <div class="flex items-center gap-3 mt-2">
                                        <div class="w-10 h-10 rounded-full bg-white/20 border border-white/30 flex items-center justify-center font-black text-lg">T</div>
                                        <div>
                                            <h4 class="text-sm font-black">{{ $toko->nama_toko ?? 'Nama Toko' }}</h4>
                                            <div class="text-[9px] text-blue-200"><i class="mdi mdi-star text-amber-300"></i> 4.9/5.0</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex-1 overflow-hidden pt-4 pb-6">
                                    <div class="px-4 mb-4"><div class="w-full h-28 rounded-xl bg-slate-200 flex items-center justify-center border border-slate-300"><i class="mdi mdi-image-outline text-3xl text-slate-400"></i></div></div>
                                    <div class="px-4 grid grid-cols-4 gap-2 mb-4">
                                        <div class="aspect-square bg-slate-200 rounded-lg"></div><div class="aspect-square bg-slate-200 rounded-lg"></div><div class="aspect-square bg-slate-200 rounded-lg"></div><div class="aspect-square bg-slate-200 rounded-lg"></div>
                                    </div>
                                    <div class="px-4">
                                        <div class="w-1/2 h-3 bg-slate-200 rounded-full mb-3"></div>
                                        <div class="grid grid-cols-2 gap-3">
                                            <div class="bg-white rounded-xl border border-slate-200 h-32"></div>
                                            <div class="bg-white rounded-xl border border-slate-200 h-32"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="absolute top-24 -left-16 bg-white/90 backdrop-blur px-4 py-2 rounded-2xl shadow-lg border border-slate-100 text-[10px] font-black text-blue-600 uppercase animate-float z-20">Tampilkan Produk Terbaikmu</div>
                        <div class="absolute bottom-32 -right-12 bg-white/90 backdrop-blur px-4 py-2 rounded-2xl shadow-lg border border-slate-100 text-[10px] font-black text-amber-500 uppercase animate-float-delay z-20">Atur Tata Letak</div>
                    </div>

                    {{-- Preview Situs --}}
                    <div x-show="subTab === 'situs'" 
                         x-transition:enter="transition ease-out duration-500" 
                         x-transition:enter-start="opacity-0 scale-95 translate-x-4" 
                         x-transition:enter-end="opacity-100 scale-100 translate-x-0" 
                         x-transition:leave="transition ease-in duration-300 absolute top-0" 
                         x-transition:leave-start="opacity-100 scale-100 translate-x-0" 
                         x-transition:leave-end="opacity-0 scale-95 translate-x-4" 
                         class="w-full max-w-[600px] mt-10">
                        <div class="w-full aspect-[16/10] bg-white rounded-2xl shadow-2xl border border-slate-200 flex flex-col overflow-hidden relative z-10">
                            <div class="h-8 bg-slate-100 border-b border-slate-200 flex items-center px-3 gap-1.5 flex-shrink-0">
                                <div class="w-2.5 h-2.5 rounded-full bg-red-400"></div><div class="w-2.5 h-2.5 rounded-full bg-amber-400"></div><div class="w-2.5 h-2.5 rounded-full bg-emerald-400"></div>
                                <div class="mx-4 flex-1 h-5 bg-white rounded border border-slate-200"></div>
                            </div>
                            <div class="flex-1 bg-slate-50 flex flex-col">
                                <div class="h-10 bg-slate-800 w-full flex items-center px-4 gap-3"><div class="w-6 h-6 rounded-full bg-slate-600"></div><div class="w-20 h-2 rounded-full bg-slate-600"></div></div>
                                <div class="w-full h-24 bg-blue-100 border-b border-slate-200"></div>
                                <div class="flex-1 p-5 flex gap-5">
                                    <div class="w-1/4 h-full bg-white rounded-lg border border-slate-200 p-3">
                                        <div class="w-full h-1.5 bg-slate-200 rounded-full mb-2"></div>
                                        <div class="w-3/4 h-1.5 bg-slate-200 rounded-full mb-2"></div>
                                    </div>
                                    <div class="w-3/4 h-full grid grid-cols-3 gap-3">
                                        <div class="bg-white rounded-lg border border-slate-200"></div>
                                        <div class="bg-white rounded-lg border border-slate-200"></div>
                                        <div class="bg-white rounded-lg border border-slate-200"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="absolute -top-4 -right-4 bg-white/90 backdrop-blur px-4 py-2 rounded-xl shadow-lg border border-slate-100 text-[10px] font-black text-indigo-600 uppercase animate-float z-20">Tampilan Ultra Wide</div>
                    </div>

                </div>
            </div>
        </div>

        {{-- ============================================================== --}}
        {{-- KONTEN TAB 2: HALAMAN KATEGORI                                 --}}
        {{-- ============================================================== --}}
        <div x-show="activeTab === 'kategori'" x-transition.opacity.duration.400ms class="flex-1 p-6 md:p-8 bg-slate-50/50">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 h-full relative z-20">
                <div class="lg:col-span-8 bg-white border border-slate-200 rounded-[2rem] shadow-sm p-6 md:p-8 flex flex-col">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
                        <div>
                            <h3 class="text-xl font-black text-slate-900">Kategori Toko Pintar</h3>
                            <p class="text-xs font-bold text-slate-500 mt-1">Pengelompokan produk secara otomatis.</p>
                        </div>
                        <div class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-50 border border-blue-200 text-blue-600 rounded-lg text-[10px] font-black uppercase tracking-widest">
                            <i class="mdi mdi-auto-fix"></i> Sistem Otomatis Aktif
                        </div>
                    </div>

                    <div class="flex-1 flex flex-col items-center justify-center text-center border-2 border-dashed border-blue-200 rounded-3xl p-10 bg-blue-50/50">
                        <div class="w-24 h-24 bg-white rounded-full flex items-center justify-center shadow-sm mb-6 border border-blue-100">
                            <i class="mdi mdi-robot-outline text-5xl text-blue-500"></i>
                        </div>
                        <h5 class="text-lg font-black text-slate-900 mb-2">Kategori Dikelola Oleh AI</h5>
                        <p class="text-sm font-medium text-slate-500 max-w-md mb-8 leading-relaxed">
                            Sistem Pondasikita secara pintar memindai dan mengelompokkan produk jualan Anda berdasarkan <b>Kategori Master</b> (Semen, Besi, Cat, dll). 
                            <br><br>Pembeli dapat langsung memfilter produk di halaman profil toko Anda tanpa perlu pengaturan manual.
                        </p>
                        <button type="button" class="flex items-center gap-2 px-6 py-3 bg-blue-600 text-white text-sm font-bold rounded-xl shadow-sm outline-none cursor-default">
                            <i class="mdi mdi-check-circle"></i> Sinkronisasi Kategori Berjalan
                        </button>
                    </div>
                </div>

                <div class="lg:col-span-4 flex flex-col items-center pointer-events-none">
                    <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4">Preview Tampilan</h3>
                    <div class="w-[260px] h-[520px] bg-slate-900 rounded-[35px] p-2 shadow-xl border-[4px] border-slate-800 relative">
                        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-20 h-4 bg-slate-900 rounded-b-xl z-50"></div>
                        <div class="w-full h-full bg-slate-50 rounded-[28px] overflow-hidden flex flex-col border border-slate-200">
                            <div class="bg-blue-600 text-white pt-8 pb-3 px-4 shadow-sm flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center font-black">T</div>
                                <div class="w-20 h-2 bg-white/40 rounded-full"></div>
                            </div>
                            <div class="flex bg-white border-b border-slate-200 shadow-sm">
                                <div class="flex-1 py-2.5 text-center text-[10px] font-bold text-slate-400">Toko</div>
                                <div class="flex-1 py-2.5 text-center text-[10px] font-bold text-slate-400">Produk</div>
                                <div class="flex-1 py-2.5 text-center text-[10px] font-black text-blue-600 border-b-2 border-blue-600">Kategori</div>
                            </div>
                            <div class="p-4 space-y-3 flex-1 overflow-hidden">
                                <div class="bg-white p-3 rounded-xl border border-slate-100 flex justify-between items-center shadow-sm">
                                    <div class="flex items-center gap-3"><div class="w-6 h-6 bg-slate-100 rounded"></div><span class="text-[11px] font-black text-slate-700">Semua Produk</span></div><i class="mdi mdi-chevron-right text-slate-300"></i>
                                </div>
                                <div class="bg-white p-3 rounded-xl border border-slate-100 flex justify-between items-center shadow-sm">
                                    <div class="flex items-center gap-3"><div class="w-6 h-6 bg-slate-100 rounded"></div><span class="text-[11px] font-black text-slate-700">Promo Spesial</span></div><i class="mdi mdi-chevron-right text-slate-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ============================================================== --}}
        {{-- KONTEN TAB 3: PRODUK PILIHAN TOKO                              --}}
        {{-- ============================================================== --}}
        <div x-show="activeTab === 'produk_pilihan'" x-transition.opacity.duration.400ms class="flex-1 p-6 md:p-12 flex flex-col justify-center bg-slate-50/30">
            <div class="flex flex-col lg:flex-row items-center justify-center gap-12 lg:gap-24 max-w-5xl mx-auto relative z-20">
                <div class="order-2 lg:order-1 relative pointer-events-none">
                    <div class="absolute inset-0 bg-amber-400/20 rounded-full blur-3xl"></div>
                    <div class="w-[280px] h-[560px] bg-slate-900 rounded-[40px] p-2.5 shadow-2xl border-[4px] border-slate-800 relative z-10">
                        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-24 h-5 bg-slate-900 rounded-b-xl z-50"></div>
                        <div class="w-full h-full bg-slate-50 rounded-[30px] overflow-hidden flex flex-col border border-slate-200">
                            <div class="h-40 bg-slate-200"></div>
                            <div class="p-5 flex-1 bg-white">
                                <div class="w-full h-3 bg-slate-100 rounded-full mb-2"></div>
                                <div class="w-2/3 h-3 bg-slate-100 rounded-full mb-8"></div>
                                <div class="flex items-center justify-between mb-3">
                                    <h6 class="text-xs font-black text-slate-900 uppercase tracking-widest">Top Picks</h6>
                                    <span class="text-[9px] font-bold text-blue-600 bg-blue-50 px-2 py-0.5 rounded">See All</span>
                                </div>
                                <div class="grid grid-cols-2 gap-3">
                                    <div class="bg-white rounded-xl border border-amber-200 shadow-sm overflow-hidden ring-2 ring-amber-400/20">
                                        <div class="h-20 bg-amber-50 flex items-center justify-center relative">
                                            <span class="absolute top-1 left-1 bg-amber-500 text-white text-[8px] font-black px-1.5 py-0.5 rounded">#1</span>
                                            <i class="mdi mdi-cube text-3xl text-amber-200"></i>
                                        </div>
                                        <div class="p-2"><div class="text-[9px] font-black text-slate-800 truncate mb-1">Produk Unggulan</div><div class="text-[10px] font-black text-amber-600">Rp 150K</div></div>
                                    </div>
                                    <div class="bg-white rounded-xl border border-slate-100 shadow-sm overflow-hidden opacity-70">
                                        <div class="h-20 bg-slate-100 flex items-center justify-center relative">
                                            <span class="absolute top-1 left-1 bg-slate-400 text-white text-[8px] font-black px-1.5 py-0.5 rounded">#2</span>
                                            <i class="mdi mdi-cube text-3xl text-slate-200"></i>
                                        </div>
                                        <div class="p-2"><div class="text-[9px] font-black text-slate-600 truncate mb-1">Produk Laris</div><div class="text-[10px] font-black text-slate-500">Rp 80K</div></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="order-1 lg:order-2 max-w-md text-center lg:text-left">
                    <div class="inline-flex items-center gap-1.5 px-3 py-1 bg-amber-50 border border-amber-200 text-amber-600 rounded-lg text-[10px] font-black uppercase tracking-widest mb-4">
                        <i class="mdi mdi-star"></i> Terlaris Otomatis
                    </div>
                    <h2 class="text-3xl lg:text-4xl font-black text-slate-900 leading-tight mb-4">
                        Sorot produk jagoan di etalase utama!
                    </h2>
                    <p class="text-sm font-medium text-slate-600 mb-8 leading-relaxed">
                        Anda tidak perlu memilih satu per satu. Sistem kami secara <i>real-time</i> melacak data transaksi Anda dan otomatis menyematkan produk paling laris di posisi paling atas halaman toko Anda sebagai <b>Top Picks</b>.
                    </p>
                    <button type="button" class="group relative flex items-center justify-center lg:justify-start gap-3 w-full lg:w-auto px-8 py-4 bg-slate-100 text-slate-500 font-black rounded-2xl border border-slate-200 cursor-default outline-none">
                        <i class="mdi mdi-check-decagram text-xl text-emerald-500"></i>
                        <span>Auto-Kurasi Telah Aktif</span>
                    </button>
                </div>
            </div>
        </div>

    </div>

    {{-- ============================================================== --}}
    {{-- MODAL PANDUAN DEKORASI                                         --}}
    {{-- ============================================================== --}}

    {{-- Modal Panduan Dekorasi --}}
    <div x-show="isGuideModalOpen" class="fixed inset-0 z-[100] flex items-center justify-center p-4 sm:p-6" x-cloak>
        <div x-show="isGuideModalOpen" x-transition.opacity class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" @click="isGuideModalOpen = false"></div>
        <div x-show="isGuideModalOpen"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-90 translate-y-4"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             class="relative bg-white rounded-[2rem] shadow-2xl w-full max-w-2xl max-h-[90vh] flex flex-col overflow-hidden z-10">

            <div class="px-6 py-5 border-b border-slate-100 flex justify-between items-center bg-slate-900 text-white">
                <div class="flex items-center gap-3">
                    <i class="mdi mdi-book-open-page-variant text-2xl text-blue-400"></i>
                    <h2 class="text-lg font-black">Panduan Dekorasi</h2>
                </div>
                <button type="button" @click="isGuideModalOpen = false" class="text-slate-400 hover:text-white transition-colors outline-none">
                    <i class="mdi mdi-close text-2xl"></i>
                </button>
            </div>

            <div class="flex-1 overflow-y-auto p-6 md:p-8 space-y-8">
                <div class="flex gap-4">
                    <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center font-black text-xl flex-shrink-0">1</div>
                    <div>
                        <h4 class="text-lg font-black text-slate-900 mb-1">Masuk ke Mode Editor</h4>
                        <p class="text-sm text-slate-600 leading-relaxed">Klik tombol <b>"Mulai Dekorasi Instan"</b> pada tab Dekorasi Halaman Toko. Anda akan diarahkan ke kanvas visual.</p>
                    </div>
                </div>
                <div class="flex gap-4">
                    <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center font-black text-xl flex-shrink-0">2</div>
                    <div>
                        <h4 class="text-lg font-black text-slate-900 mb-1">Pilih Komponen (Drag & Drop)</h4>
                        <p class="text-sm text-slate-600 leading-relaxed">Tersedia berbagai blok seperti Banner Promo, Video Youtube, dan Grid Produk. Tarik blok tersebut ke susunan halaman Anda.</p>
                    </div>
                </div>
                <div class="flex gap-4">
                    <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center font-black text-xl flex-shrink-0">3</div>
                    <div>
                        <h4 class="text-lg font-black text-slate-900 mb-1">Simpan & Tampilkan</h4>
                        <p class="text-sm text-slate-600 leading-relaxed">Periksa tampilan pada preview Mobile di sebelah kanan. Jika sudah puas, klik <b>Simpan Dekorasi</b> agar pembeli bisa langsung melihatnya.</p>
                    </div>
                </div>
                <div class="bg-emerald-50 border border-emerald-200 p-4 rounded-2xl flex gap-3 mt-4">
                    <i class="mdi mdi-lightbulb-on-outline text-2xl text-emerald-500"></i>
                    <p class="text-xs font-bold text-emerald-700 m-0">Tips: Gunakan "Ruang Media" untuk menyimpan gambar Banner Anda agar dapat digunakan berkali-kali tanpa perlu upload ulang.</p>
                </div>
            </div>

            <div class="p-6 border-t border-slate-100 bg-slate-50 text-right">
                <button type="button" @click="isGuideModalOpen = false" class="px-6 py-2.5 bg-slate-900 text-white font-bold rounded-xl hover:bg-black transition-colors outline-none">Tutup Panduan</button>
            </div>
        </div>
    </div>

</div>

<script>
    function decorLanding() {
        return {
            activeTab: 'halaman_toko',
            subTab: 'aplikasi',
            loadingBtn: null,
            isGuideModalOpen: false,

            // KUNCI LOGIKA: Mengarahkan flow Mobile ke Template, Desktop ke Kosong
            goToDecoration() {
                this.loadingBtn = 'mulaiDekorasi';

                if (this.subTab === 'aplikasi') {
                    // Redirect ke halaman pemilihan Template Mobile
                    setTimeout(() => {
                        window.location.href = "{{ route('seller.shop.decoration.template') }}";
                    }, 500);
                } else {
                    // Tampilkan SweetAlert khusus Desktop lalu redirect
                    Swal.fire({
                        title: 'Membuka Editor Desktop...',
                        text: 'Menyiapkan kanvas kosong untuk layar lebar.',
                        icon: 'success',
                        showConfirmButton: false,
                        timer: 1500,
                        customClass: { popup: 'rounded-3xl' }
                    }).then(() => {
                        // MENGARAHKAN LANGSUNG KE PAGE EDIT DESKTOP
                        window.location.href = "{{ route('seller.shop.decoration.editor.desktop') }}";
                        this.loadingBtn = null;
                    });
                }
            },

            showInfoProdukPilihan() {
                Swal.fire({
                    title: 'Produk Pilihan Toko',
                    html: '<p class=\'text-sm text-slate-600\'>Fitur ini memungkinkan Anda menyematkan 4-8 produk andalan di bagian paling atas halaman toko Anda.<br><br><b>Sangat cocok untuk:</b><br>⭐ Peluncuran Produk Baru<br>⭐ Promo Cuci Gudang<br>⭐ Produk dengan Margin Tinggi</p>',
                    icon: 'info',
                    confirmButtonText: 'Mengerti',
                    confirmButtonColor: '#2563eb',
                    customClass: { popup: 'rounded-3xl' }
                });
            }
        }
    }
</script>
@endsection
