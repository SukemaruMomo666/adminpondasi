<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Checkout Aman - Pondasikita</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Tailwind CSS CDN + Config Dewa --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'] },
                    colors: {
                        brand: { 50: '#eff6ff', 100: '#dbeafe', 500: '#3b82f6', 600: '#2563eb', 700: '#1d4ed8' },
                        surface: '#fcfcfd',
                    },
                    boxShadow: {
                        'soft': '0 4px 40px -4px rgba(0,0,0,0.03)',
                        'float': '0 10px 30px -5px rgba(0,0,0,0.08)',
                        'glow': '0 0 20px rgba(37,99,235,0.3)',
                        'sticky': '0 -10px 40px rgba(0,0,0,0.08)',
                    },
                    animation: {
                        'fade-in': 'fadeIn 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards',
                        'shimmer': 'shimmer 2.5s infinite',
                    },
                    keyframes: {
                        fadeIn: { '0%': { opacity: 0, transform: 'translateY(15px)' }, '100%': { opacity: 1, transform: 'translateY(0)' } },
                        shimmer: { '100%': { transform: 'translateX(100%)' } }
                    }
                }
            }
        }
    </script>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
    {{-- LEAFLET CSS --}}
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>

    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f4f4f5; }

        input[type=number]::-webkit-inner-spin-button,
        input[type=number]::-webkit-outer-spin-button { -webkit-appearance: none; margin: 0; }
        input[type=number] { -moz-appearance: textfield; }

        .custom-scrollbar::-webkit-scrollbar { width: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }

        /* Smooth Hide/Show Form & Map */
        .manual-form-wrapper { display: grid; grid-template-rows: 0fr; transition: grid-template-rows 0.4s ease-out; }
        .manual-form-wrapper.active { grid-template-rows: 1fr; }
        .manual-form-inner { overflow: hidden; }

        /* Address Card Active */
        .address-card.selected { border-color: #2563eb; background-color: #eff6ff; }
        .address-card.selected .check-icon { opacity: 1; transform: scale(1); }
        .address-card .check-icon { opacity: 0; transform: scale(0.5); transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275); }
        
        .price-transition { transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1); }

        /* Map Styling */
        #checkout-map { height: 250px; width: 100%; border-radius: 0.75rem; z-index: 10; border: 1px solid #e2e8f0; }
        .leaflet-control-attribution { display: none !important; }

        /* Custom Select Kurir yang lebih rapi */
        .custom-dropdown-container button:focus {
            outline: none;
        }
    </style>
</head>
<body class="text-zinc-800 antialiased pt-[80px] pb-32 lg:pb-12">

    @include('partials.navbar')

    {{-- BREADCRUMB --}}
    <div class="bg-white border-b border-zinc-200 hidden md:block relative z-10 shadow-sm">
        <div class="max-w-[1200px] mx-auto px-4 sm:px-6 py-3">
            <nav class="flex text-xs font-semibold text-zinc-500 items-center gap-3">
                <a href="{{ route('keranjang.index') }}" class="hover:text-black transition-colors flex items-center gap-2">
                    <i class="fas fa-arrow-left"></i> Kembali ke Keranjang
                </a>
                <span class="w-1 h-1 rounded-full bg-zinc-300"></span>
                <span class="text-zinc-900 font-bold"><i class="fas fa-lock text-emerald-500 mr-1"></i> Checkout Aman</span>
            </nav>
        </div>
    </div>

    {{-- MAIN FORM --}}
    <form id="checkout-form">
        @csrf
        {{-- Hidden Data Core --}}
        <input type="hidden" name="user_email" value="{{ $userEmail }}">
        <input type="hidden" name="total_produk_subtotal" value="{{ $totalProduk }}">
        <input type="hidden" name="grand_total" id="input_grand_total" value="{{ $totalProduk }}">
        
        {{-- Hidden Data Voucher & Diskon --}}
        <input type="hidden" name="voucher_code" id="input-voucher-code" value="">
        <input type="hidden" name="total_diskon" id="input_total_diskon" value="0">

        {{-- Hidden Data Alamat Pengiriman --}}
        <input type="hidden" name="shipping_label_alamat" id="final_label">
        <input type="hidden" name="shipping_nama_penerima" id="final_nama">
        <input type="hidden" name="shipping_telepon_penerima" id="final_telepon">
        <input type="hidden" name="shipping_alamat_lengkap" id="final_alamat">
        
        {{-- Area ID Biteship dan Lat Lng --}}
        <input type="hidden" name="shipping_area_id" id="final_area_id">
        <input type="hidden" name="shipping_lat" id="final_lat">
        <input type="hidden" name="shipping_lng" id="final_lng">

        <input type="hidden" name="shipping_kecamatan" id="final_kecamatan">
        <input type="hidden" name="shipping_kota_kabupaten" id="final_kota">
        <input type="hidden" name="shipping_provinsi" id="final_provinsi">
        <input type="hidden" name="shipping_kode_pos" id="final_kodepos">

        @if($isDirectPurchase)
            <input type="hidden" name="direct_purchase" value="1">
            <input type="hidden" name="product_id" value="{{ request('product_id') }}">
            <input type="hidden" name="jumlah" value="{{ request('jumlah') }}">
        @else
            @php
                $rawItems = request('selected_items', '');
                $itemArray = is_string($rawItems) && $rawItems !== '' ? explode(',', $rawItems) : (is_array($rawItems) ? $rawItems : []);
            @endphp
            @foreach($itemArray as $itemId)
                <input type="hidden" name="selected_items[]" value="{{ trim($itemId) }}">
            @endforeach
        @endif

        <main class="max-w-[1200px] mx-auto px-4 sm:px-6 py-6 lg:py-10">

            <div class="mb-8">
                <h1 class="text-3xl font-black text-black tracking-tight flex items-center gap-3">
                    Konfirmasi Pesanan
                </h1>
                <p class="text-sm font-medium text-zinc-500 mt-1">Sistem kami terhubung dengan Biteship & Armada Toko untuk kalkulasi ongkos kirim otomatis.</p>
            </div>

            <div class="flex flex-col lg:grid lg:grid-cols-12 gap-8 xl:gap-10 items-start">

                {{-- KOLOM KIRI --}}
                <div class="w-full lg:col-span-8 flex flex-col gap-8 animate-fade-in">

                    {{-- 1. KARTU ALAMAT --}}
                    <div class="bg-white rounded-[2rem] shadow-soft border border-zinc-200 p-6 sm:p-8 relative overflow-hidden">
                        <div class="absolute top-0 left-0 w-2 h-full bg-blue-600"></div>
                        <h2 class="text-xl font-black text-black mb-6">1. Alamat Tujuan</h2>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            
                            {{-- ALAMAT TERSIMPAN (PROFIL) --}}
                            <label id="card-saved" class="address-card selected relative flex flex-col p-5 border-2 border-zinc-200 rounded-2xl cursor-pointer transition-all duration-300 group">
                                <input type="radio" name="address_type" value="saved" checked class="peer sr-only">
                                
                                <div class="flex items-start justify-between mb-3">
                                    <div class="flex items-center gap-2 text-zinc-900 font-bold">
                                        <i class="fas fa-home text-blue-500 bg-blue-50 p-2 rounded-lg"></i> Alamat Profil
                                    </div>
                                    <i class="fas fa-check-circle text-blue-600 text-xl check-icon"></i>
                                </div>

                                @if($alamatUser && !$isAlamatIncomplete)
                                    <div class="text-sm text-zinc-600 space-y-1">
                                        <p class="font-bold text-black">{{ $alamatUser->nama_penerima }} <span class="text-zinc-400 font-medium">({{ $alamatUser->telepon_penerima }})</span></p>
                                        <p class="line-clamp-2">{{ $alamatUser->alamat_lengkap }}</p>
                                        <p class="font-medium text-xs mt-1 text-blue-600 bg-blue-50 px-2 py-0.5 rounded w-max border border-blue-100">Kode Pos: {{ $alamatUser->kode_pos ?? '-' }}</p>
                                        
                                        {{-- Simpan Data Backend ke atribut data untuk diambil JS --}}
                                        <div id="saved_data_carrier" class="hidden"
                                            data-nama="{{ $alamatUser->nama_penerima }}" data-tlp="{{ $alamatUser->telepon_penerima }}"
                                            data-alamat="{{ $alamatUser->alamat_lengkap }}" data-area="{{ $alamatUser->area_id }}"
                                            data-lat="{{ $alamatUser->latitude }}" data-lng="{{ $alamatUser->longitude }}"
                                            data-pos="{{ $alamatUser->kode_pos }}">
                                        </div>
                                    </div>
                                @else
                                    <div class="text-sm text-red-600 bg-red-50 border border-red-100 p-4 rounded-xl mt-2 flex flex-col gap-3">
                                        <div class="flex items-start gap-2">
                                            <i class="fas fa-exclamation-triangle mt-0.5"></i>
                                            <span class="font-medium">Data alamat profil belum lengkap.</span>
                                        </div>
                                    </div>
                                @endif
                            </label>

                            {{-- ALAMAT MANUAL (MAPS + BITESHIP) --}}
                            <label id="card-manual" class="address-card relative flex flex-col p-5 border-2 border-zinc-200 rounded-2xl cursor-pointer transition-all duration-300 group hover:border-blue-300 hover:bg-zinc-50">
                                <input type="radio" name="address_type" value="manual" class="peer sr-only">
                                <div class="flex items-start justify-between mb-2">
                                    <div class="flex items-center gap-2 text-zinc-900 font-bold">
                                        <i class="fas fa-map-marker-alt text-zinc-500 bg-zinc-100 p-2 rounded-lg group-hover:text-blue-500 group-hover:bg-blue-50 transition-colors"></i> Kirim ke Alamat Lain
                                    </div>
                                    <i class="fas fa-check-circle text-blue-600 text-xl check-icon"></i>
                                </div>
                                <p class="text-xs text-zinc-500 font-medium mt-1">Cari wilayah & tentukan titik peta.</p>
                            </label>
                        </div>

                        {{-- WRAPPER FORM MANUAL --}}
                        <div id="manual-address-form" class="manual-form-wrapper mt-4">
                            <div class="manual-form-inner bg-zinc-50 border border-zinc-200 rounded-2xl p-5 sm:p-6">
                                <h4 class="text-xs font-black text-zinc-500 uppercase tracking-widest mb-4 flex items-center gap-2">
                                    <i class="fas fa-pencil-alt text-blue-500"></i> Detail Penerima
                                </h4>
                                
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                                    <input type="text" class="manual-input w-full bg-white border border-zinc-300 text-sm font-semibold rounded-xl focus:border-blue-600 focus:ring-4 focus:ring-blue-600/10 px-4 py-3 outline-none" id="manual_nama" placeholder="Nama Penerima">
                                    <input type="number" class="manual-input w-full bg-white border border-zinc-300 text-sm font-semibold rounded-xl focus:border-blue-600 focus:ring-4 focus:ring-blue-600/10 px-4 py-3 outline-none" id="manual_telepon" placeholder="081234567890">
                                </div>

                                <h4 class="text-xs font-black text-zinc-500 uppercase tracking-widest mb-3 mt-6 flex items-center gap-2">
                                    <i class="fas fa-search-location text-blue-500"></i> Cari Area (Kecamatan/Kota)
                                </h4>
                                
                                {{-- AUTOCOMPLETE BITESHIP --}}
                                <div class="relative w-full mb-4">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <i class="fas fa-search text-zinc-400"></i>
                                    </div>
                                    <input type="text" id="biteship-search" class="w-full bg-white border border-zinc-300 text-sm font-bold rounded-xl focus:border-blue-600 focus:ring-4 focus:ring-blue-600/10 pl-10 pr-4 py-3.5 outline-none placeholder:font-medium placeholder:text-zinc-400" placeholder="Ketik minimal 3 huruf (Cth: Cicendo, Bandung)..." autocomplete="off">
                                    
                                    {{-- Dropdown Hasil --}}
                                    <div id="biteship-results" class="absolute z-50 w-full bg-white border border-zinc-200 rounded-xl shadow-xl mt-1 max-h-60 overflow-y-auto hidden"></div>
                                </div>

                                {{-- Hidden Inputs for Manual Address --}}
                                <input type="hidden" id="manual_area_id">
                                <input type="hidden" id="manual_provinsi">
                                <input type="hidden" id="manual_kota">
                                <input type="hidden" id="manual_kecamatan">
                                <input type="hidden" id="manual_kodepos">
                                <input type="hidden" id="manual_lat">
                                <input type="hidden" id="manual_lng">

                                <div class="mb-4">
                                    <textarea class="manual-input custom-scrollbar w-full bg-white border border-zinc-300 text-sm font-semibold rounded-xl focus:border-blue-600 focus:ring-4 focus:ring-blue-600/10 px-4 py-3 outline-none resize-none" id="manual_alamat" rows="2" placeholder="Detail jalan, gang, RT/RW, nomor rumah..."></textarea>
                                </div>

                                {{-- LEAFLET MAP --}}
                                <h4 class="text-xs font-black text-zinc-500 uppercase tracking-widest mb-2 mt-4 flex items-center gap-2">
                                    <i class="fas fa-map-pin text-red-500"></i> Geser Pin ke Titik Akurat
                                </h4>
                                <div id="checkout-map"></div>
                                <p class="text-[10px] font-bold text-zinc-400 mt-2"><i class="fas fa-info-circle"></i> Peta akan otomatis berpindah saat Anda memilih area dari kolom pencarian di atas.</p>
                            </div>
                        </div>
                    </div>

                    {{-- 2. KARTU DAFTAR PRODUK --}}
                    <div class="bg-white rounded-[2rem] shadow-soft border border-zinc-200 p-6 sm:p-8">
                        <div class="flex items-center justify-between mb-6 pb-4 border-b border-zinc-100">
                            <h2 class="text-xl font-black text-black">2. Rincian Pesanan</h2>
                            <span class="bg-zinc-100 text-zinc-600 px-3 py-1 rounded-full text-xs font-bold">{{ count($itemsPerToko) }} Toko</span>
                        </div>

                        <div class="mb-8">
                            <label class="block text-[10px] font-black text-zinc-400 uppercase tracking-widest mb-2 ml-1">Metode Pengiriman Global</label>
                            <div class="relative max-w-lg">
                                <select name="tipe_pengambilan" id="tipe_pengambilan" class="w-full bg-blue-50 border border-blue-200 text-blue-800 text-sm font-bold rounded-xl focus:border-blue-600 px-4 py-3.5 outline-none cursor-pointer appearance-none">
                                    <option value="kurir">🚀 Pengiriman Ekspedisi Nasional (JNE, Sicepat, dll)</option>
                                    <option value="armada">🚚 Pengiriman Armada Toko (Khusus Material Berat)</option>
                                    <option value="ambil_di_toko">🏪 Ambil Sendiri di Toko Fisik (Gratis Ongkir)</option>
                                </select>
                                <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none"><i class="fas fa-chevron-down text-blue-500"></i></div>
                            </div>
                        </div>

                        <div class="space-y-8">
                            @foreach($itemsPerToko as $tokoId => $toko)
                                <div class="bg-zinc-50 border border-zinc-200 rounded-2xl store-container" 
                                     data-toko-id="{{ $tokoId }}"
                                     data-origin="{{ $toko['origin_area_id'] }}" 
                                     data-couriers="{{ $toko['active_couriers'] }}">
                                    
                                    <div class="bg-zinc-100 border-b border-zinc-200 rounded-t-2xl px-5 py-3 flex items-center justify-between">
                                        <div class="flex items-center gap-2">
                                            <i class="fas fa-store text-emerald-600 bg-white p-1.5 rounded-md shadow-sm text-xs"></i>
                                            <h4 class="font-black text-sm text-zinc-900">{{ $toko['nama_toko'] }}</h4>
                                        </div>
                                    </div>

                                    @php $totalBeratToko = 0; @endphp
                                    <div class="p-5 flex flex-col gap-4">
                                        @foreach($toko['items'] as $item)
                                            @php 
                                                $subtotal = $item->harga * $item->jumlah; 
                                                $totalBeratToko += (1000 * $item->jumlah);
                                            @endphp
                                            <div class="flex gap-4">
                                                <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-xl bg-white border border-zinc-200 overflow-hidden shrink-0">
                                                    <img src="{{ asset('assets/uploads/products/' . ($item->gambar_utama ?? 'default.jpg')) }}" class="w-full h-full object-cover mix-blend-multiply" onerror="this.onerror=null; this.src='{{ asset('assets/uploads/products/default.jpg') }}';">
                                                </div>
                                                <div class="flex-1 min-w-0 flex flex-col justify-center">
                                                    <h5 class="text-sm font-bold text-zinc-800 line-clamp-1 mb-1">{{ $item->nama_barang }}</h5>
                                                    <p class="text-xs font-semibold text-zinc-500 mb-2">{{ $item->jumlah }} x Rp{{ number_format($item->harga, 0, ',', '.') }}</p>
                                                    <div class="text-sm font-black text-black">Rp{{ number_format($subtotal, 0, ',', '.') }}</div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>

                                    <input type="hidden" id="weight-toko-{{ $tokoId }}" value="{{ $totalBeratToko }}">

                                    {{-- KOTAK KURIR CUSTOM MEWAH --}}
                                    <div class="px-5 pb-5 shipping-box-wrapper">
                                        <div class="bg-white border border-blue-100 rounded-xl p-4">
                                            <label class="block text-[10px] font-black text-blue-600 uppercase tracking-widest mb-2">Pilih Layanan Pengiriman</label>
                                            
                                            <div class="relative custom-dropdown-container">
                                                {{-- Hidden input ini yang akan dikirim ke Backend --}}
                                                <input type="hidden" name="shipping[{{ $tokoId }}]" id="shipping-input-{{ $tokoId }}" class="shipping-input" value="">
                                                
                                                {{-- Tombol yang diklik untuk membuka dropdown --}}
                                                <button type="button" onclick="toggleDropdown('{{ $tokoId }}')" id="dropdown-button-{{ $tokoId }}" class="w-full bg-transparent border-b-2 border-zinc-300 text-zinc-900 text-sm font-medium pb-2 pt-1 flex justify-between items-center outline-none focus:border-blue-600 transition-all text-left disabled:opacity-50">
                                                    <span id="dropdown-text-{{ $tokoId }}">Menunggu alamat tujuan...</span>
                                                    <i class="fas fa-chevron-down text-zinc-400 text-sm transition-transform duration-200" id="dropdown-icon-{{ $tokoId }}"></i>
                                                </button>
                                                
                                                {{-- List CSS Mewah yang muncul ke bawah --}}
                                                <div id="dropdown-menu-{{ $tokoId }}" class="absolute z-50 w-full mt-1 bg-white border border-zinc-200 rounded-xl shadow-[0_10px_40px_-10px_rgba(0,0,0,0.15)] hidden overflow-hidden">
                                                    <ul id="shipping-list-{{ $tokoId }}" class="max-h-60 overflow-y-auto custom-scrollbar py-2">
                                                        <li class="px-4 py-3 text-sm text-zinc-500 text-center">Menunggu alamat...</li>
                                                    </ul>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        {{-- CATATAN --}}
                        <div class="mt-8 border-t border-zinc-100 pt-6">
                            <label class="block text-[11px] font-black text-zinc-400 uppercase tracking-widest mb-2 ml-1"><i class="fas fa-comment-alt mr-1"></i> Catatan Pesanan</label>
                            <input type="text" name="catatan" class="w-full bg-zinc-50 border border-zinc-200 text-black text-sm font-medium rounded-xl focus:border-blue-600 px-4 py-3 outline-none" placeholder="Tinggalkan instruksi khusus pengiriman...">
                        </div>
                    </div>
                </div>

                {{-- KOLOM KANAN (RINGKASAN & VOUCHER) --}}
                <div class="w-full lg:col-span-4 lg:sticky lg:top-28 z-20 animate-fade-in" style="animation-delay: 0.1s;">
                    <div class="bg-white rounded-[2rem] shadow-soft border border-zinc-200 overflow-hidden">
                        <div class="bg-emerald-50 border-b border-emerald-100 px-6 py-3 flex items-center justify-center gap-2">
                            <i class="fas fa-shield-alt text-emerald-600 text-sm"></i>
                            <span class="text-xs font-bold text-emerald-700 tracking-wide">Checkout Aman Terenkripsi</span>
                        </div>

                        <div class="p-6 sm:p-8">
                            <h3 class="text-lg font-black text-black mb-6">Ringkasan Pembayaran</h3>
                            
                            {{-- Rincian Tagihan --}}
                            <div class="space-y-4 text-sm border-b border-dashed border-zinc-200 pb-6 mb-6">
                                <div class="flex justify-between items-center text-zinc-500 font-medium">
                                    <span>Total Harga Barang</span>
                                    <span class="font-bold text-black">Rp{{ number_format($totalProduk, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between items-center text-zinc-500 font-medium">
                                    <span>Total Ongkos Kirim</span>
                                    <span id="shipping-total-display" class="font-bold text-black">Rp0</span>
                                </div>
                                
                                {{-- Baris Diskon Voucher --}}
                                <div id="discount-row" class="hidden justify-between items-center text-emerald-600 font-medium bg-emerald-50 px-3 py-2 rounded-lg border border-emerald-100">
                                    <span>Diskon Promo</span>
                                    <span id="discount-total-display" class="font-black">- Rp0</span>
                                </div>
                            </div>

                            {{-- FITUR VOUCHER GLOBAL --}}
                            <div class="mb-6">
                                <span class="text-[10px] font-black text-zinc-900 uppercase tracking-widest mb-3 flex items-center gap-1.5">
                                    <i class="fas fa-ticket-alt text-blue-500"></i> Makin Hemat Pakai Promo
                                </span>
                                
                                <div id="voucher-input-box" class="flex items-center gap-2">
                                    <div class="relative flex-1">
                                        <input type="text" id="voucher-input" placeholder="Masukkan kode promo" class="w-full bg-zinc-50 border border-zinc-200 text-zinc-800 text-xs font-bold rounded-xl focus:bg-white focus:border-blue-500 block px-4 py-3.5 outline-none uppercase placeholder:normal-case placeholder:font-medium placeholder:text-zinc-400">
                                    </div>
                                    <button type="button" onclick="applyVoucher()" id="btn-apply-voucher" class="bg-zinc-900 hover:bg-blue-600 text-white px-5 py-3.5 rounded-xl font-black transition-colors shadow-md text-[10px] uppercase tracking-widest shrink-0 w-[100px] flex justify-center items-center">
                                        Terapkan
                                    </button>
                                </div>

                                <div id="voucher-message" class="mt-2 hidden"></div>

                                <div id="applied-voucher-tag" class="hidden mt-3 items-center justify-between bg-emerald-50 border border-emerald-200 px-3 py-2 rounded-lg">
                                    <div class="flex items-center gap-2">
                                        <div class="w-6 h-6 rounded-full bg-emerald-600 flex items-center justify-center text-white text-[10px]"><i class="fas fa-check"></i></div>
                                        <span class="text-xs font-black text-emerald-700 uppercase tracking-wider" id="applied-voucher-code">PROMO10</span>
                                    </div>
                                    <button type="button" onclick="removeVoucher()" class="text-red-500 hover:text-red-700 p-1"><i class="fas fa-times"></i></button>
                                </div>
                            </div>

                            {{-- Grand Total --}}
                            <div class="flex justify-between items-end mb-8 pt-4 border-t border-zinc-100">
                                <span class="text-[11px] font-black text-zinc-400 uppercase tracking-widest">Total Tagihan</span>
                                <span id="grand-total-display" class="text-3xl font-black text-black tracking-tight leading-none text-right price-transition">
                                    Rp{{ number_format($totalProduk, 0, ',', '.') }}
                                </span>
                            </div>

                            <button type="submit" id="btn-submit-desktop" disabled class="hidden lg:flex group w-full bg-black hover:bg-blue-600 text-white font-black py-4 rounded-2xl transition-all duration-300 shadow-[0_4px_20px_rgba(0,0,0,0.15)] items-center justify-center gap-2 relative overflow-hidden disabled:opacity-50 disabled:cursor-not-allowed">
                                <div class="absolute inset-0 w-full h-full bg-gradient-to-r from-transparent via-white/10 to-transparent -translate-x-full group-hover:animate-shimmer"></div>
                                <i class="fas fa-file-invoice text-sm relative z-10"></i> 
                                <span class="relative z-10">Menghitung Ongkir...</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        {{-- MOBILE STICKY --}}
        <div class="fixed bottom-0 left-0 w-full bg-white/90 backdrop-blur-xl border-t border-zinc-200 p-4 pb-safe shadow-sticky z-50 lg:hidden flex items-center justify-between gap-4">
            <div class="flex flex-col flex-1 min-w-0">
                <span class="text-[10px] font-black text-zinc-400 uppercase tracking-widest">Total Pembayaran</span>
                <span id="mobile-grand-total" class="text-xl font-black text-black truncate price-transition">Rp0</span>
            </div>
            <button type="submit" id="btn-submit-mobile" disabled class="w-auto px-6 bg-black text-white font-black py-3.5 rounded-xl active:scale-95 flex items-center justify-center gap-2 text-xs shadow-lg disabled:opacity-50">
                <i class="fas fa-spinner fa-spin"></i> Tunggu Ongkir
            </button>
        </div>
    </form>

    @include('partials.footer')

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>

    <script>
        // ==========================================
        // VARIABEL GLOBAL
        // ==========================================
        const totalProdukAsli = {{ $totalProduk }};
        let currentDiscountPercent = 0; 
        let currentVoucherCode = null;
        let isFetchingRates = false;

        const radioAddress = document.querySelectorAll('input[name="address_type"]');
        const cardSaved = document.getElementById('card-saved');
        const cardManual = document.getElementById('card-manual');
        const manualFormDiv = document.getElementById('manual-address-form');
        
        const btnSubmitDesktop = document.getElementById('btn-submit-desktop');
        const btnSubmitMobile = document.getElementById('btn-submit-mobile');
        const voucherInput = document.getElementById('voucher-input');

        const final = {
            label: document.getElementById('final_label'), nama: document.getElementById('final_nama'),
            telepon: document.getElementById('final_telepon'), alamat: document.getElementById('final_alamat'),
            area_id: document.getElementById('final_area_id'), lat: document.getElementById('final_lat'), lng: document.getElementById('final_lng'),
            kecamatan: document.getElementById('final_kecamatan'), kota: document.getElementById('final_kota'),
            provinsi: document.getElementById('final_provinsi'), kodepos: document.getElementById('final_kodepos')
        };

        const tipePengambilan = document.getElementById('tipe_pengambilan');

        // ==========================================
        // LEAFLET MAP (MANUAL FORM)
        // ==========================================
        let checkoutMap = null;
        let checkoutMarker = null;

        function initMap() {
            if (checkoutMap) return; 
            
            // Set Default (Contoh: Bundaran HI)
            const defaultLat = -6.1931; 
            const defaultLng = 106.8231;

            checkoutMap = L.map('checkout-map').setView([defaultLat, defaultLng], 14);
            L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png').addTo(checkoutMap);

            checkoutMarker = L.marker([defaultLat, defaultLng], {draggable: true}).addTo(checkoutMap);

checkoutMarker.on('dragend', async function(e) {
                const position = e.target.getLatLng();
                const lat = position.lat;
                const lng = position.lng;
                
                // 1. Simpan kordinat baru
                document.getElementById('manual_lat').value = lat;
                document.getElementById('manual_lng').value = lng;
                syncManualToHidden();

                // 2. Beri efek loading di kotak pencarian
                const searchInput = document.getElementById('biteship-search');
                const searchIcon = searchInput.previousElementSibling.querySelector('i');
                searchIcon.className = 'fas fa-spinner fa-spin text-blue-500';
                searchInput.value = "Menerjemahkan titik peta...";

                try {
                    // 3. REVERSE GEOCODING (Minta nama daerah ke OpenStreetMap)
                    const geoRes = await fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`);
                    const geoData = await geoRes.json();
                    
                    if (geoData && geoData.address) {
                        // Tampilkan alamat lengkap dari peta ke kolom input
                        searchInput.value = geoData.display_name;

                        // Ambil variabel kecamatan & kota untuk dicari ID Biteship-nya
                        const kecamatan = geoData.address.suburb || geoData.address.village || geoData.address.town || '';
                        const kota = geoData.address.city || geoData.address.county || geoData.address.state || '';
                        const query = `${kecamatan} ${kota}`.trim();

                        // 4. AUTO-SEARCH BITESHIP AREA ID (Agar ongkir JNE/Sicepat ikut berubah)
                        if (query) {
                            const biteRes = await fetch(`/api/biteship/search?q=${encodeURIComponent(query)}`);
                            const biteData = await biteRes.json();

                            if (biteData.areas && biteData.areas.length > 0) {
                                const area = biteData.areas[0]; // Ambil hasil paling cocok
                                document.getElementById('manual_area_id').value = area.id;
                                document.getElementById('manual_kecamatan').value = area.name;
                                document.getElementById('manual_kota').value = area.administrative_division_level_2_name;
                                document.getElementById('manual_provinsi').value = area.administrative_division_level_1_name;
                                document.getElementById('manual_kodepos').value = area.postal_code;
                                syncManualToHidden();
                            }
                        }
                    }
                } catch (error) {
                    console.error("Gagal reverse geocode:", error);
                    searchInput.value = "Gagal membaca area peta. Ketik manual saja.";
                } finally {
                    searchIcon.className = 'fas fa-search text-zinc-400';
                    // 5. KALKULASI ULANG SEMUA ONGKIR!
                    triggerShippingFetch(); 
                }
            });
        }

        // ==========================================
        // AUTOCOMPLETE BITESHIP + GEOCODING (FLY TO MAP)
        // ==========================================
        const areaSearchInput = document.getElementById('biteship-search');
        const areaResultsDiv = document.getElementById('biteship-results');
        let searchTimeout = null;

        areaSearchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            const query = this.value.trim();

            if (query.length < 3) {
                areaResultsDiv.classList.add('hidden');
                return;
            }

            const searchIcon = this.previousElementSibling.querySelector('i');
            searchIcon.className = 'fas fa-spinner fa-spin text-blue-500';

            searchTimeout = setTimeout(async () => {
                try {
                    const response = await fetch(`/api/biteship/search?q=${query}`);
                    const data = await response.json();

                    areaResultsDiv.innerHTML = '';
                    if (data.areas && data.areas.length > 0) {
                        data.areas.forEach(area => {
                            const div = document.createElement('div');
                            div.className = 'px-4 py-3 hover:bg-blue-50 cursor-pointer border-b border-zinc-100 last:border-0 text-sm text-zinc-700';
                            div.innerHTML = `<span class="font-bold text-black">${area.name}</span>, ${area.administrative_division_level_2_name}, ${area.administrative_division_level_1_name}`;
                            
                            div.addEventListener('click', () => {
                                // 1. Isi hidden inputs
                                document.getElementById('manual_area_id').value = area.id;
                                document.getElementById('manual_kecamatan').value = area.name;
                                document.getElementById('manual_kota').value = area.administrative_division_level_2_name;
                                document.getElementById('manual_provinsi').value = area.administrative_division_level_1_name;
                                document.getElementById('manual_kodepos').value = area.postal_code;
                                
                                areaSearchInput.value = `${area.name}, ${area.administrative_division_level_2_name}`;
                                areaResultsDiv.classList.add('hidden');

                                // 2. GEOCODING OTOMATIS: Terbang ke Titik Kota/Kecamatan
                                const geocodeQuery = `${area.name}, ${area.administrative_division_level_2_name}, Indonesia`;
                                fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(geocodeQuery)}`)
                                    .then(res => res.json())
                                    .then(geoData => {
                                        if (geoData && geoData.length > 0) {
                                            const newLat = parseFloat(geoData[0].lat);
                                            const newLng = parseFloat(geoData[0].lon);
                                            
                                            // Efek terbang (Fly To)
                                            if(checkoutMap && checkoutMarker) {
                                                checkoutMap.flyTo([newLat, newLng], 15, { duration: 1.5 });
                                                checkoutMarker.setLatLng([newLat, newLng]);
                                            }
                                            
                                            document.getElementById('manual_lat').value = newLat;
                                            document.getElementById('manual_lng').value = newLng;
                                            syncManualToHidden();
                                            triggerShippingFetch(); 
                                        } else {
                                            // Fallback jika geocoding gagal
                                            syncManualToHidden();
                                            triggerShippingFetch();
                                        }
                                    }).catch(err => {
                                        console.log('Geocoding error:', err);
                                        syncManualToHidden();
                                        triggerShippingFetch();
                                    });
                            });
                            areaResultsDiv.appendChild(div);
                        });
                        areaResultsDiv.classList.remove('hidden');
                    } else {
                        areaResultsDiv.innerHTML = '<div class="p-4 text-xs text-center text-zinc-500">Area tidak ditemukan</div>';
                        areaResultsDiv.classList.remove('hidden');
                    }
                } catch (err) {
                    console.error('Error fetching Biteship', err);
                } finally {
                    searchIcon.className = 'fas fa-search text-zinc-400';
                }
            }, 600);
        });

        document.addEventListener('click', function(e) {
            if (!areaSearchInput.contains(e.target) && !areaResultsDiv.contains(e.target)) {
                areaResultsDiv.classList.add('hidden');
            }
        });

        // ==========================================
        // LOGIKA ALAMAT (SAVED VS MANUAL)
        // ==========================================
        function updateAddressUI() {
            const selected = document.querySelector('input[name="address_type"]:checked').value;
            if (selected === 'saved') {
                cardSaved.classList.add('selected'); cardManual.classList.remove('selected');
                manualFormDiv.classList.remove('active');
                
                const savedData = document.getElementById('saved_data_carrier');
                if (savedData) {
                    final.label.value = "Alamat Profil"; 
                    final.nama.value = savedData.dataset.nama;
                    final.telepon.value = savedData.dataset.tlp; 
                    final.alamat.value = savedData.dataset.alamat;
                    final.area_id.value = savedData.dataset.area;
                    final.lat.value = savedData.dataset.lat;
                    final.lng.value = savedData.dataset.lng;
                    final.kodepos.value = savedData.dataset.pos;
                    
                    triggerShippingFetch(); 
                } else {
                    setCheckoutButtonsState(false, "Lengkapi Alamat Profil");
                }
            } else {
                cardSaved.classList.remove('selected'); cardManual.classList.add('selected');
                manualFormDiv.classList.add('active');
                setTimeout(() => { initMap(); checkoutMap.invalidateSize(); }, 300);

                final.label.value = "Alamat Manual";
                syncManualToHidden();
                
                if(final.area_id.value) {
                    triggerShippingFetch();
                } else {
                    setCheckoutButtonsState(false, "Pilih Area Tujuan");
                    
                    // Reset dropdown kalau manual belum diisi
                    document.querySelectorAll('.shipping-input').forEach(input => {
                        const tokoId = input.id.replace('shipping-input-', '');
                        const btnTextEl = document.getElementById(`dropdown-text-${tokoId}`);
                        input.value = '';
                        if(btnTextEl) btnTextEl.innerHTML = "Pilih area tujuan di form";
                    });
                }
            }
        }

        function syncManualToHidden() {
            if (document.querySelector('input[name="address_type"]:checked').value !== 'manual') return;
            final.nama.value = document.getElementById('manual_nama').value;
            final.telepon.value = document.getElementById('manual_telepon').value;
            final.alamat.value = document.getElementById('manual_alamat').value;
            final.kecamatan.value = document.getElementById('manual_kecamatan').value;
            final.kota.value = document.getElementById('manual_kota').value;
            final.provinsi.value = document.getElementById('manual_provinsi').value;
            final.kodepos.value = document.getElementById('manual_kodepos').value;
            final.area_id.value = document.getElementById('manual_area_id').value;
            final.lat.value = document.getElementById('manual_lat').value;
            final.lng.value = document.getElementById('manual_lng').value;
        }

        // ==========================================
        // FETCH ONGKIR KE BACKEND (BITESHIP + ARMADA TOKO + PICKUP)
        // ==========================================
        async function triggerShippingFetch() {
            const tipe = tipePengambilan.value; // 'kurir', 'armada', 'ambil_di_toko'
            
            if (tipe === 'ambil_di_toko') {
                document.querySelectorAll('.shipping-box-wrapper').forEach(el => el.style.display = 'none');
                document.querySelectorAll('.shipping-input').forEach(el => el.value = '');
                calculateTotal(); 
                setCheckoutButtonsState(true);
                return;
            }

            const destinationAreaId = final.area_id.value;
            if(!destinationAreaId && tipe === 'kurir') {
                setCheckoutButtonsState(false, "Pilih Area Tujuan Dulu");
                return;
            }

            const destLat = final.lat.value || 0;
            const destLng = final.lng.value || 0;
            if((!destLat || !destLng) && tipe === 'armada') {
                setCheckoutButtonsState(false, "Pin Peta Belum Ditemukan");
                return;
            }

            setCheckoutButtonsState(false, "Menghitung Ongkir...");
            document.querySelectorAll('.shipping-box-wrapper').forEach(el => el.style.display = 'block');
            
            const stores = document.querySelectorAll('.store-container');
            let fetchPromises = [];

            stores.forEach(store => {
                const tokoId = store.getAttribute('data-toko-id');
                const weight = document.getElementById(`weight-toko-${tokoId}`).value || 1000;
                
                const originAreaId = store.getAttribute('data-origin');
                const sellerCouriers = store.getAttribute('data-couriers') || 'jne'; 

                // Ambil elemen custom dropdown kita
                const listEl = document.getElementById(`shipping-list-${tokoId}`);
                const btnTextEl = document.getElementById(`dropdown-text-${tokoId}`);
                const inputEl = document.getElementById(`shipping-input-${tokoId}`);
                const btnEl = document.getElementById(`dropdown-button-${tokoId}`);

                let loadingText = tipe === 'kurir' ? 'Mencari Kurir Nasional...' : 'Kalkulasi Jarak Armada...';
                
                btnTextEl.innerHTML = `<i class="fas fa-spinner fa-spin text-blue-500 mr-2"></i> ${loadingText}`;
                btnEl.disabled = true;

                if(!originAreaId && tipe === 'kurir') {
                    btnTextEl.innerHTML = 'Toko belum mengatur Area ID';
                    inputEl.value = '';
                    listEl.innerHTML = '<li class="px-4 py-3 text-sm text-red-500 text-center">Toko belum mengatur lokasi pengiriman</li>';
                    return; 
                }

                // HIT KE BACKEND LARAVEL (API CEK ONGKIR + KALKULASI JARAK ARMADA TOKO)
                const url = `/api/cek-ongkir?tipe=${tipe}&toko_id=${tokoId}&origin=${originAreaId}&destination=${destinationAreaId}&weight=${weight}&couriers=${sellerCouriers}&dest_lat=${destLat}&dest_lng=${destLng}`;

                const p = fetch(url)
                    .then(res => res.json())
                    .then(data => {
                        let html = '';
                        if (data.success !== false && data.pricing && data.pricing.length > 0) {
                            data.pricing.forEach((rate, index) => {
                                // Desain List Pilihan (Bisa di-hover & diklik)
                                let isSelectedClass = index === 0 ? 'bg-blue-50 border-blue-500' : 'border-transparent hover:bg-zinc-50';
                                
                                // Pilih opsi teratas secara otomatis
                                if(index === 0) {
                                    inputEl.value = `${rate.company}_${rate.price}`;
                                    btnTextEl.innerHTML = `${rate.courier_name} ${rate.courier_service_name} &mdash; <b>Rp ${rate.price.toLocaleString('id-ID')}</b>`;
                                }

                                html += `<li class="px-4 py-3 text-sm text-zinc-700 border-l-4 cursor-pointer transition-colors ${isSelectedClass}" 
                                             data-value="${rate.company}_${rate.price}" 
                                             data-label="${rate.courier_name} ${rate.courier_service_name} &mdash; <b>Rp ${rate.price.toLocaleString('id-ID')}</b>"
                                             onclick="selectShippingOption('${tokoId}', this)">
                                            <div class="font-black text-zinc-900">${rate.courier_name} <span class="font-semibold text-zinc-500">${rate.courier_service_name}</span></div>
                                            <div class="text-blue-600 font-black mt-1">Rp ${rate.price.toLocaleString('id-ID')}</div>
                                         </li>`;
                            });
                            btnEl.disabled = false;
                        } else {
                            let errMsg = data.message || data.error || "Layanan tidak tersedia untuk area ini";
                            html = `<li class="px-4 py-3 text-sm text-red-500 font-bold text-center"><i class="fas fa-exclamation-triangle"></i> ${errMsg}</li>`;
                            inputEl.value = "";
                            btnTextEl.innerHTML = "Gagal memuat layanan";
                            btnEl.disabled = true;
                        }
                        listEl.innerHTML = html;
                    })
                    .catch(err => {
                        inputEl.value = "";
                        btnTextEl.innerHTML = "Gagal memuat layanan";
                        listEl.innerHTML = '<li class="px-4 py-3 text-center text-red-500">Koneksi terputus.</li>';
                    });
                    
                fetchPromises.push(p);
            });

            await Promise.all(fetchPromises);
            calculateTotal();
            setCheckoutButtonsState(true);
        }

        // ==========================================
        // LOGIKA CUSTOM DROPDOWN CSS MEWAH
        // ==========================================
        window.toggleDropdown = function(tokoId) {
            const menu = document.getElementById(`dropdown-menu-${tokoId}`);
            const icon = document.getElementById(`dropdown-icon-${tokoId}`);
            
            // Tutup menu dropdown lain jika ada
            document.querySelectorAll('[id^="dropdown-menu-"]').forEach(el => {
                if(el.id !== `dropdown-menu-${tokoId}`) el.classList.add('hidden');
            });
            document.querySelectorAll('[id^="dropdown-icon-"]').forEach(el => {
                if(el.id !== `dropdown-icon-${tokoId}`) el.style.transform = 'rotate(0deg)';
            });

            if (menu.classList.contains('hidden')) {
                menu.classList.remove('hidden');
                icon.style.transform = 'rotate(180deg)';
            } else {
                menu.classList.add('hidden');
                icon.style.transform = 'rotate(0deg)';
            }
        };

        window.selectShippingOption = function(tokoId, element) {
            const inputEl = document.getElementById(`shipping-input-${tokoId}`);
            const btnTextEl = document.getElementById(`dropdown-text-${tokoId}`);
            const menuEl = document.getElementById(`dropdown-menu-${tokoId}`);
            const iconEl = document.getElementById(`dropdown-icon-${tokoId}`);
            
            // Pindahkan warna biru (Highlight) ke opsi yang diklik
            element.parentElement.querySelectorAll('li').forEach(el => {
                el.classList.remove('bg-blue-50', 'border-blue-500');
                el.classList.add('border-transparent');
            });
            element.classList.add('bg-blue-50', 'border-blue-500');
            element.classList.remove('border-transparent');
            
            // Update nilai hidden input dan tulisan di tombol
            inputEl.value = element.getAttribute('data-value');
            btnTextEl.innerHTML = element.getAttribute('data-label');
            
            // Tutup menu dan putar balik ikon panah
            menuEl.classList.add('hidden');
            iconEl.style.transform = 'rotate(0deg)';
            
            // Panggil ulang kalkulator harga
            calculateTotal();
        };

        // Tutup dropdown kalau ngeklik sembarang tempat di layar
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.custom-dropdown-container')) {
                document.querySelectorAll('[id^="dropdown-menu-"]').forEach(el => el.classList.add('hidden'));
                document.querySelectorAll('[id^="dropdown-icon-"]').forEach(el => el.style.transform = 'rotate(0deg)');
            }
        });

        // ==========================================
        // KALKULASI TOTAL
        // ==========================================
        function calculateTotal() {
            let shippingCost = 0;
            const tipe = tipePengambilan.value;
            
            if (tipe === 'kurir' || tipe === 'armada') {
                document.querySelectorAll('.shipping-box-wrapper').forEach(el => el.style.display = 'block');
                
                // BACA DARI HIDDEN INPUT (shipping-input) BUKAN SELECT LAGI
                document.querySelectorAll('.shipping-input').forEach(input => {
                    if (input.value) {
                        let valParts = input.value.split('_');
                        if (valParts.length > 1) shippingCost += parseInt(valParts[valParts.length - 1]);
                    }
                });
            } else {
                document.querySelectorAll('.shipping-box-wrapper').forEach(el => el.style.display = 'none');
                shippingCost = 0;
            }

            let totalDiskon = totalProdukAsli * currentDiscountPercent;
            let grandTotal = totalProdukAsli - totalDiskon + shippingCost;

            const formatRp = (angka) => 'Rp' + Math.round(angka).toLocaleString('id-ID');

            document.getElementById('shipping-total-display').innerText = formatRp(shippingCost);
            document.getElementById('discount-total-display').innerText = '- ' + formatRp(totalDiskon);
            
            const rowDiscount = document.getElementById('discount-row');
            if(totalDiskon > 0) {
                rowDiscount.classList.remove('hidden'); rowDiscount.classList.add('flex');
            } else {
                rowDiscount.classList.add('hidden'); rowDiscount.classList.remove('flex');
            }

            const totalDisplays = [document.getElementById('grand-total-display'), document.getElementById('mobile-grand-total')];
            totalDisplays.forEach(el => {
                if(el) {
                    el.style.opacity = '0.5'; el.style.transform = 'scale(0.95)';
                    setTimeout(() => {
                        el.innerText = formatRp(grandTotal);
                        el.style.opacity = '1'; el.style.transform = 'scale(1)';
                    }, 150);
                }
            });

            document.getElementById('input_grand_total').value = grandTotal;
            document.getElementById('input_total_diskon').value = totalDiskon;
        }

        function setCheckoutButtonsState(isEnabled, loadingText = 'Buat Pesanan Sekarang') {
            if (isEnabled) {
                btnSubmitDesktop.disabled = false;
                btnSubmitDesktop.innerHTML = `<div class="absolute inset-0 w-full h-full bg-gradient-to-r from-transparent via-white/10 to-transparent -translate-x-full group-hover:animate-shimmer"></div><i class="fas fa-file-invoice text-sm relative z-10"></i><span class="relative z-10">${loadingText}</span>`;
                if(btnSubmitMobile) { 
                    btnSubmitMobile.disabled = false; 
                    btnSubmitMobile.innerHTML = '<i class="fas fa-check text-xs relative z-10"></i> <span class="relative z-10">Pesan</span>'; 
                }
            } else {
                btnSubmitDesktop.disabled = true;
                btnSubmitDesktop.innerHTML = `<i class="fas fa-spinner fa-spin text-sm"></i> <span>${loadingText}</span>`;
                if(btnSubmitMobile) { 
                    btnSubmitMobile.disabled = true; 
                    btnSubmitMobile.innerHTML = `<i class="fas fa-spinner fa-spin text-xs"></i> <span>Tunggu...</span>`; 
                }
            }
        }

        // Event Listeners
        radioAddress.forEach(radio => radio.addEventListener('change', updateAddressUI));
        document.querySelectorAll('.manual-input').forEach(input => input.addEventListener('input', syncManualToHidden));
        tipePengambilan.addEventListener('change', triggerShippingFetch); 
        
        // Init UI pertama kali
        updateAddressUI();

        // ==========================================
        // VOUCHER GLOBAL
        // ==========================================
        window.applyVoucher = function() {
            const inputEl = document.getElementById('voucher-input');
            const code = inputEl.value.trim().toUpperCase();
            const btn = document.getElementById('btn-apply-voucher');
            const msg = document.getElementById('voucher-message');
            const tag = document.getElementById('applied-voucher-tag');
            const inputBox = document.getElementById('voucher-input-box');

            if(!code) return;

            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            btn.disabled = true;

            setTimeout(() => {
                if(code === 'PROMO10' || code === 'HEMAT10') {
                    currentVoucherCode = code;
                    currentDiscountPercent = 0.10; 
                    
                    msg.className = 'mt-2 text-[10px] font-bold text-emerald-600 flex items-center gap-1.5';
                    msg.innerHTML = '<i class="fas fa-check-circle"></i> Voucher berhasil diterapkan!';
                    msg.style.display = 'flex';

                    inputEl.value = '';
                    inputBox.classList.add('hidden');
                    
                    document.getElementById('applied-voucher-code').innerText = code;
                    tag.classList.remove('hidden');
                    tag.classList.add('flex');
                    document.getElementById('input-voucher-code').value = code;

                    calculateTotal();
                } else {
                    msg.className = 'mt-2 text-[10px] font-bold text-red-500 flex items-center gap-1.5';
                    msg.innerHTML = '<i class="fas fa-exclamation-circle"></i> Kode promo tidak valid / kedaluwarsa.';
                    msg.style.display = 'flex';
                    inputEl.classList.add('border-red-300', 'focus:border-red-500');
                }
                
                btn.innerHTML = 'Terapkan';
                btn.disabled = false;
                setTimeout(() => { msg.style.display = 'none'; inputEl.classList.remove('border-red-300', 'focus:border-red-500'); }, 3000);
            }, 800);
        }

        window.removeVoucher = function() {
            currentVoucherCode = null; currentDiscountPercent = 0;
            document.getElementById('input-voucher-code').value = '';
            document.getElementById('applied-voucher-tag').classList.add('hidden', 'flex');
            document.getElementById('voucher-input-box').classList.remove('hidden');
            document.getElementById('voucher-message').style.display = 'none';
            calculateTotal();
        }

        if(voucherInput) {
            voucherInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') { e.preventDefault(); window.applyVoucher(); }
            });
        }

        // ==========================================
        // SUBMIT CHECKOUT FORM (FINAL)
        // ==========================================
        document.getElementById('checkout-form').addEventListener('submit', async function(e) {
            e.preventDefault();

            if (document.querySelector('input[name="address_type"]:checked').value === 'manual') {
                syncManualToHidden();
                if (!final.nama.value || !final.telepon.value || !final.alamat.value || !final.area_id.value) {
                    Swal.fire({ icon: 'warning', title: 'Data Belum Lengkap', text: 'Mohon isi form alamat dan pilih lokasi dari dropdown pencarian agar layanan bisa dihitung.' });
                    return;
                }
            }

            setCheckoutButtonsState(false, "Memproses Transaksi...");

            try {
                const formData = new FormData(this);
                const response = await fetch("{{ route('checkout.process') }}", {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                    body: formData
                });

                const result = await response.json();

                if (result.status === 'success') {
                    const invoiceUrl = "{{ url('/pesanan') }}/" + result.kode_invoice; 
                    Swal.fire({
                        icon: 'success', title: 'Pesanan Dibuat!', text: 'Mengarahkan ke pembayaran...',
                        showConfirmButton: false, timer: 1500
                    }).then(() => { window.location.href = invoiceUrl; });
                } else {
                    Swal.fire({ icon: 'error', title: 'Gagal', text: result.message });
                    setCheckoutButtonsState(true);
                }
            } catch (error) {
                Swal.fire({ icon: 'error', title: 'Koneksi Terputus', text: 'Coba lagi nanti.' });
                setCheckoutButtonsState(true);
            }
        });
    </script>
</body>
</html>