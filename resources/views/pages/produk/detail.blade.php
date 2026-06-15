<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ $product->nama_barang }} - Pondasikita</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Tailwind CSS CDN --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'] },
                    colors: {
                        brand: {
                            50: '#eff6ff', 500: '#3b82f6', 600: '#2563eb', 700: '#1d4ed8',
                        }
                    }
                }
            }
        }
    </script>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        body { font-family: 'Inter', sans-serif; background-color: #fafafa; }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        
        /* Hapus Panah Up/Down di Input Number */
        input[type=number]::-webkit-inner-spin-button, 
        input[type=number]::-webkit-outer-spin-button { -webkit-appearance: none; margin: 0; }
        input[type=number] { -moz-appearance: textfield; }

        @keyframes shimmer { 100% { transform: translateX(100%); } }
        .animate-shimmer { animation: shimmer 2.5s infinite; }

        /* Smooth Scrolling */
        html { scroll-behavior: smooth; }

        /* Transisi Harga Super Mulus */
        .price-transition { transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1); }
    </style>
</head>
<body class="text-zinc-900 antialiased pt-[80px]">

    @include('partials.navbar')

    <main class="max-w-[1440px] mx-auto px-4 lg:px-10 py-8 lg:py-12">
        <div class="flex flex-col lg:grid lg:grid-cols-12 gap-12 items-start">

            {{-- ========================================== --}}
            {{-- KOLOM 1: VISUAL (Sticky Gambar Produk) --}}
            {{-- ========================================== --}}
            <div class="w-full lg:col-span-4 lg:sticky lg:top-28">
                <div class="space-y-6">
                    <div class="relative group aspect-square bg-white rounded-[2.5rem] border border-zinc-100 shadow-sm overflow-hidden p-3 transition-all duration-500 hover:shadow-xl hover:shadow-blue-500/10">
                        <img src="{{ asset('assets/uploads/products/' . ($gallery_images[0] ?? 'default.jpg')) }}" id="mainProductImage"
                             class="w-full h-full object-cover rounded-[2rem] transition-all duration-500 ease-out group-hover:scale-105 cursor-zoom-in"
                             onerror="this.src='{{ asset('assets/uploads/products/default.jpg') }}'">

                        {{-- Badge Kondisi --}}
                        <div class="absolute top-8 left-8">
                            <span class="bg-white/90 backdrop-blur-md px-4 py-2 rounded-full text-[10px] font-black uppercase tracking-widest text-zinc-900 border border-white/40 shadow-sm">
                                <i class="fas fa-box-check text-blue-600 mr-1"></i> Kondisi: Baru
                            </span>
                        </div>
                    </div>

                    {{-- Thumbnails Gallery --}}
                    <div class="flex gap-4 overflow-x-auto no-scrollbar py-2 px-1">
                        @foreach ($gallery_images as $index => $img)
                            <button onclick="changeImage(this, '{{ asset('assets/uploads/products/' . $img) }}')"
                                    class="thumb-btn shrink-0 w-20 h-20 rounded-2xl bg-white border-2 overflow-hidden transition-all duration-300 {{ $index == 0 ? 'border-brand-600 shadow-md ring-4 ring-brand-50 scale-105' : 'border-zinc-100 opacity-60 hover:opacity-100 hover:scale-105' }}">
                                <img src="{{ asset('assets/uploads/products/' . $img) }}" class="w-full h-full object-cover">
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- ========================================== --}}
            {{-- KOLOM 2: INFO & SPESIFIKASI --}}
            {{-- ========================================== --}}
            <div class="w-full lg:col-span-5 space-y-10">
                
                {{-- Header Produk --}}
                <div class="space-y-6">
                    <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-zinc-100/80 border border-zinc-200 text-zinc-500 text-[10px] font-black uppercase tracking-widest">
                        <i class="fas fa-tag text-zinc-400"></i> {{ $product->nama_kategori }}
                    </div>

                    <h1 class="text-3xl lg:text-5xl font-black text-zinc-900 leading-[1.15] tracking-tighter break-words">
                        {{ $product->nama_barang }}
                    </h1>

                    <div class="flex items-center gap-6">
                        <div class="flex items-center gap-2.5 bg-yellow-50 px-3 py-1.5 rounded-xl border border-yellow-100">
                            <div class="flex text-yellow-500 text-sm"><i class="fas fa-star"></i></div>
                            <span class="text-sm font-black text-yellow-700">{{ number_format($avg_rating, 1) }}</span>
                            <span class="text-xs font-bold text-yellow-600/70">({{ $jumlah_ulasan }} Ulasan)</span>
                        </div>
                        <div class="w-1.5 h-1.5 rounded-full bg-zinc-300"></div>
                        <div class="text-sm font-bold text-zinc-500 italic">Terjual <span class="text-zinc-900 font-black not-italic">{{ number_format($product->stok_terjual ?? 0) }}</span></div>
                    </div>

                    {{-- Harga Produk & Diskon --}}
                    @php
                        $now = \Carbon\Carbon::now();
                        $isPromo = !empty($product->nilai_diskon) && $product->nilai_diskon > 0;
                        if ($isPromo && $product->diskon_mulai && $product->diskon_berakhir) {
                            $start = \Carbon\Carbon::parse($product->diskon_mulai);
                            $end = \Carbon\Carbon::parse($product->diskon_berakhir);
                            if (!$now->between($start, $end)) { $isPromo = false; }
                        }

                        $hargaFinal = $product->harga;
                        if($isPromo) {
                            if($product->tipe_diskon == 'PERSEN') {
                                $hargaFinal = $product->harga - ($product->harga * ($product->nilai_diskon / 100));
                            } else {
                                $hargaFinal = $product->harga - $product->nilai_diskon;
                            }
                        }
                    @endphp

                    <div class="space-y-1">
                        @if($isPromo)
                            <div class="flex items-center gap-3">
                                <span class="px-2 py-0.5 bg-red-100 text-red-600 text-[10px] font-black rounded-md">{{ $product->tipe_diskon == 'PERSEN' ? $product->nilai_diskon.'%' : 'DISKON' }}</span>
                                <span class="text-zinc-400 text-lg line-through font-bold tracking-tight">Rp{{ number_format($product->harga, 0, ',', '.') }}</span>
                            </div>
                        @endif
                        <div class="text-4xl lg:text-5xl font-black text-blue-600 tracking-tighter">
                            Rp{{ number_format($hargaFinal, 0, ',', '.') }}
                            <span class="text-sm text-zinc-400 font-bold tracking-normal">/ {{ $product->satuan_unit ?? 'Unit' }}</span>
                        </div>
                    </div>
                </div>

                {{-- Specs Grid --}}
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-white border border-zinc-100 p-6 rounded-[2rem] flex items-center gap-4 transition-all duration-300 hover:border-brand-200 hover:shadow-lg hover:shadow-brand-500/5 group">
                        <div class="w-12 h-12 rounded-2xl bg-brand-50 flex items-center justify-center text-brand-600 transition-transform group-hover:scale-110 group-hover:rotate-6"><i class="fas fa-weight-hanging text-lg"></i></div>
                        <div>
                            <p class="text-[10px] font-black text-zinc-400 uppercase tracking-widest mb-0.5">Berat</p>
                            <p class="text-base font-black text-zinc-900">{{ number_format($product->berat_kg ?? 1, 2) }} Kg</p>
                        </div>
                    </div>
                    <div class="bg-white border border-zinc-100 p-6 rounded-[2rem] flex items-center gap-4 transition-all duration-300 hover:border-emerald-200 hover:shadow-lg hover:shadow-emerald-500/5 group">
                        <div class="w-12 h-12 rounded-2xl bg-emerald-50 flex items-center justify-center text-emerald-500 transition-transform group-hover:scale-110 group-hover:-rotate-6"><i class="fas fa-shield-check text-lg"></i></div>
                        <div>
                            <p class="text-[10px] font-black text-zinc-400 uppercase tracking-widest mb-0.5">Garansi</p>
                            <p class="text-base font-black text-zinc-900">Tersedia</p>
                        </div>
                    </div>
                </div>

                {{-- Deskripsi --}}
                <div class="bg-white rounded-[2.5rem] border border-zinc-100 p-8 lg:p-10 space-y-8 shadow-sm relative overflow-hidden">
                    <div class="absolute top-0 right-0 p-8 opacity-[0.02]">
                        <i class="fas fa-align-left text-9xl"></i>
                    </div>
                    <h3 class="text-xs font-black text-brand-600 uppercase tracking-[0.3em] flex items-center gap-4 relative z-10">
                        Deskripsi Material
                        <div class="h-px bg-brand-100 flex-1"></div>
                    </h3>
                    <div class="text-sm md:text-base text-zinc-600 leading-relaxed font-medium break-words space-y-4 relative z-10">
                        {!! nl2br(e($product->deskripsi)) ?: '<span class="italic text-zinc-400">Deskripsi produk tidak tersedia.</span>' !!}
                    </div>
                </div>

                {{-- REVIEWS --}}
                <div id="reviews" class="bg-white rounded-[2.5rem] border border-zinc-100 p-8 lg:p-10 space-y-10 shadow-sm">
                    <div class="grid md:grid-cols-12 gap-8 items-center border-b border-zinc-100 pb-10">
                        <div class="md:col-span-5 flex flex-col items-center md:items-start">
                            <span class="text-[10px] font-black text-zinc-400 uppercase tracking-widest mb-4">Total Kepuasan</span>
                            <div class="flex items-center gap-5">
                                <div class="text-7xl font-black text-zinc-900 tracking-tighter leading-none">{{ number_format($avg_rating, 1) }}</div>
                                <div class="flex flex-col gap-1.5">
                                    <div class="flex text-amber-400 text-[10px]">
                                        @for($i=0; $i<5; $i++) <i class="fas fa-star {{ $i < round($avg_rating) ? '' : 'text-zinc-200' }}"></i> @endfor
                                    </div>
                                    <p class="text-[10px] font-black text-zinc-500 uppercase tracking-widest">{{ $jumlah_ulasan }} Review</p>
                                </div>
                            </div>
                        </div>
                        <div class="md:col-span-7 flex flex-col gap-2.5">
                            @foreach([5, 4, 3, 2, 1] as $star)
                                @php
                                    $count = DB::table('tb_review_produk')->where('barang_id', $product->id)->where('rating', $star)->count();
                                    $percent = $jumlah_ulasan > 0 ? ($count / $jumlah_ulasan) * 100 : 0;
                                @endphp
                                <div class="flex items-center gap-3 group">
                                    <div class="flex items-center gap-1.5 w-10 shrink-0">
                                        <span class="text-[11px] font-black text-zinc-700">{{ $star }}</span>
                                        <i class="fas fa-star text-[9px] text-amber-400"></i>
                                    </div>
                                    <div class="flex-1 h-2 bg-zinc-100 rounded-full overflow-hidden border border-zinc-200/50">
                                        <div class="h-full bg-brand-500 rounded-full transition-all duration-1000 group-hover:brightness-110" style="width: {{ $percent }}%"></div>
                                    </div>
                                    <span class="text-[10px] font-bold text-zinc-400 w-8 text-right">{{ round($percent) }}%</span>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Comment List --}}
                    <div class="space-y-8">
                        @forelse ($reviews as $ulasan)
                            <div class="flex gap-5 group">
                                <div class="w-12 h-12 rounded-[1rem] bg-zinc-100 flex-shrink-0 flex items-center justify-center font-black text-zinc-400 border border-zinc-200 transition-all duration-300 group-hover:bg-brand-600 group-hover:text-white group-hover:border-brand-600 shadow-sm">
                                    {{ strtoupper(substr($ulasan->username, 0, 1)) }}
                                </div>
                                <div class="space-y-2 flex-1 min-w-0">
                                    <div class="flex items-center justify-between">
                                        <h5 class="text-sm font-black text-zinc-900 truncate pr-4">{{ $ulasan->username }}</h5>
                                        <span class="text-[9px] font-bold text-zinc-400 uppercase shrink-0">{{ \Carbon\Carbon::parse($ulasan->created_at)->diffForHumans() }}</span>
                                    </div>
                                    <div class="flex text-amber-400 text-[9px] gap-0.5">
                                        @for($i=0; $i<5; $i++) <i class="fas fa-star {{ $i < $ulasan->rating ? '' : 'text-zinc-200' }}"></i> @endfor
                                    </div>
                                    <p class="text-sm text-zinc-600 leading-relaxed break-words">{{ $ulasan->ulasan }}</p>
                                    @if(!empty($ulasan->gambar_ulasan))
                                        <div class="mt-3">
                                            <img src="{{ asset('assets/uploads/reviews/' . $ulasan->gambar_ulasan) }}" class="w-20 h-20 object-cover rounded-xl border border-zinc-200 shadow-sm transition-transform hover:scale-105 cursor-zoom-in">
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-8">
                                <div class="w-16 h-16 bg-zinc-50 rounded-full flex items-center justify-center mx-auto mb-3 border border-dashed border-zinc-200 text-zinc-300">
                                    <i class="fas fa-comments text-2xl"></i>
                                </div>
                                <p class="text-[10px] font-black text-zinc-400 uppercase tracking-widest">Belum Ada Ulasan</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- ========================================== --}}
            {{-- KOLOM 3: CHECKOUT & TOKO (Sticky Kanan) --}}
            {{-- ========================================== --}}
            <div class="w-full lg:col-span-3 space-y-6 lg:sticky lg:top-28">

                {{-- Checkout Card (Kalkulator) --}}
                <div class="bg-white rounded-[2.5rem] border border-zinc-100 p-8 shadow-[0_20px_50px_-10px_rgba(0,0,0,0.05)] relative overflow-hidden">
                    <h3 class="text-[10px] font-black text-zinc-400 uppercase tracking-[0.3em] mb-6 border-b border-zinc-100 pb-4">Konfirmasi Pembelian</h3>

                    {{-- FIX: Hapus Action URL dari Form, biarkan JS yang urus AJAX-nya --}}
                    <form id="formTambahKeranjang" class="space-y-6" action="">
                        @csrf
                        <input type="hidden" name="barang_id" value="{{ $product->id }}">

                        {{-- Qty Selector Modern --}}
                        <div class="flex items-center justify-between gap-4">
                            <div class="flex items-center bg-zinc-50 rounded-xl p-1 border border-zinc-200 flex-1">
                                <button type="button" onclick="updateQty(-1)" class="w-10 h-10 flex items-center justify-center font-black text-zinc-500 hover:text-zinc-900 hover:bg-zinc-200 rounded-lg transition-colors">-</button>
                                <input type="number" id="inputQty" name="jumlah" value="1" min="1" max="{{ $product->stok ?? 9999 }}" class="w-full text-center bg-transparent font-black text-base outline-none text-zinc-900">
                                <button type="button" onclick="updateQty(1)" class="w-10 h-10 flex items-center justify-center font-black text-blue-600 hover:bg-blue-100 rounded-lg transition-colors">+</button>
                            </div>
                            <div class="text-right shrink-0">
                                <p class="text-[8px] font-black text-zinc-400 uppercase tracking-widest leading-none mb-1">Sisa Stok</p>
                                <p class="text-sm font-black text-zinc-900">{{ number_format($product->stok ?? 0) }} <span class="text-[9px] text-zinc-500">{{ $product->satuan_unit ?? 'Unit' }}</span></p>
                            </div>
                        </div>

                        {{-- Total Price --}}
                        <div class="py-5 border-t border-zinc-100">
                            <span class="text-[10px] font-black text-zinc-500 uppercase tracking-[0.2em] block mb-1">Subtotal Harga</span>
                            <div class="text-3xl font-black text-blue-600 tracking-tighter price-transition" id="subtotalDisplay">
                                Rp{{ number_format($hargaFinal, 0, ',', '.') }}
                            </div>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="space-y-3">
                            <button type="button" id="btnKeranjang" class="w-full py-4 bg-white border-2 border-zinc-200 text-zinc-800 rounded-2xl font-black text-xs uppercase tracking-widest hover:border-zinc-900 hover:bg-zinc-900 hover:text-white transition-all active:scale-95 shadow-sm">
                                <i class="fas fa-cart-plus mr-1.5"></i> Keranjang
                            </button>

                            <button type="button" id="btnBeliLangsung" class="group relative w-full py-4 bg-blue-600 text-white rounded-2xl font-black text-xs uppercase tracking-widest overflow-hidden shadow-lg shadow-blue-600/30 transition-all hover:bg-blue-700 active:scale-95">
                                <div class="absolute inset-0 w-full h-full bg-gradient-to-r from-transparent via-white/20 to-transparent -translate-x-full group-hover:animate-shimmer"></div>
                                <span class="relative z-10">Beli Sekarang <i class="fas fa-arrow-right ml-1 text-[10px]"></i></span>
                            </button>
                        </div>
                    </form>
                </div>

                {{-- Store Card Info --}}
                <div class="bg-white rounded-[2.5rem] border border-zinc-100 shadow-sm overflow-hidden group">
                    <div class="h-20 bg-zinc-900 relative">
                        <div class="absolute inset-0 opacity-20" style="background-image: radial-gradient(circle at center, #2563eb 1px, transparent 1px); background-size: 14px 14px;"></div>
                    </div>
                    <div class="px-6 pb-6 relative">
                        <div class="absolute -top-10 left-6">
                            @if (!empty($product->logo_toko))
                                <img src="{{ asset('assets/uploads/logos/' . $product->logo_toko) }}" class="w-16 h-16 rounded-2xl border-[4px] border-white shadow-md object-cover bg-white">
                            @else
                                <div class="w-16 h-16 rounded-2xl border-[4px] border-white shadow-md bg-zinc-950 flex items-center justify-center text-white font-black text-xl uppercase">{{ $storeInitials ?? 'TK' }}</div>
                            @endif
                        </div>
                        <div class="pt-10 space-y-4">
                            <div class="space-y-0.5">
                                <h4 class="font-black text-lg text-zinc-900 truncate flex items-center gap-1.5">
                                    {{ $product->nama_toko }}
                                </h4>
                                
                                @if(isset($product->tier_toko) && in_array($product->tier_toko, ['official', 'official_store']))
                                    <span class="inline-block bg-purple-100 text-purple-700 text-[8px] font-black uppercase tracking-widest px-2 py-0.5 rounded border border-purple-200"><i class="fas fa-crown"></i> Official Store</span>
                                @elseif(isset($product->tier_toko) && in_array($product->tier_toko, ['power', 'power_merchant']))
                                    <span class="inline-block bg-emerald-100 text-emerald-700 text-[8px] font-black uppercase tracking-widest px-2 py-0.5 rounded border border-emerald-200"><i class="fas fa-check-circle"></i> Power Merchant</span>
                                @endif

                                @php
                                    $cityName = $product->nama_kota_toko ?? 'Nasional';
                                    if(str_starts_with($cityName, 'IDN')) $cityName = 'Lokasi Terverifikasi';
                                @endphp
                                <p class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest flex items-center gap-1.5 mt-2">
                                    <i class="fas fa-map-pin text-red-400"></i> {{ $cityName }}
                                </p>
                            </div>

                            <div class="flex items-center gap-2 w-full pt-2">
                                <a href="{{ url('pages/toko?slug=' . $product->slug_toko) }}" class="flex-1 py-2.5 bg-zinc-50 hover:bg-zinc-900 hover:text-white text-zinc-700 text-center text-[10px] font-black uppercase tracking-widest rounded-xl transition-colors border border-zinc-200">
                                   Kunjungi
                                </a>

                                @auth
                                    <button type="button" onclick="triggerOpenChat({{ $product->toko_id }}, '{{ addslashes($product->nama_toko) }}', '{{ $storeInitials ?? 'TK' }}')" class="flex-1 py-2.5 bg-blue-50 hover:bg-blue-600 hover:text-white text-blue-600 text-center text-[10px] font-black uppercase tracking-widest rounded-xl transition-colors border border-blue-200">
                                        <i class="fas fa-comments mr-1"></i> Chat
                                    </button>
                                @else
                                    <button type="button" onclick="requireChatLogin()" class="flex-1 py-2.5 bg-blue-50 hover:bg-blue-600 hover:text-white text-blue-600 text-center text-[10px] font-black uppercase tracking-widest rounded-xl transition-colors border border-blue-200">
                                        <i class="fas fa-comments mr-1"></i> Chat
                                    </button>
                                @endauth
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </main>

    @include('partials.footer')
    @include('partials.chat')

    {{-- SWEETALERT & JAVASCRIPT LOGIC TINGKAT DEWA --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // 1. Variabel Core
        const basePrice = {{ $hargaFinal ?? $product->harga ?? 0 }};
        const maxStock = {{ $product->stok ?? 1 }};
        const inputQty = document.getElementById('inputQty');
        const subtotalDisplay = document.getElementById('subtotalDisplay');
        const formKeranjang = document.getElementById('formTambahKeranjang');

        // 2. Format Rupiah
        function formatRupiah(angka) {
            return 'Rp' + angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }

        // 3. Logic Update Qty & Animasi Harga (Anti Bug Input Manual)
        function processSubtotalChange(newVal) {
            inputQty.value = newVal;
            
            // Efek Smooth Fade & Scale saat harga berubah
            subtotalDisplay.style.opacity = '0.5';
            subtotalDisplay.style.transform = 'scale(0.95)';
            
            setTimeout(() => {
                subtotalDisplay.innerText = formatRupiah(basePrice * newVal);
                subtotalDisplay.style.opacity = '1';
                subtotalDisplay.style.transform = 'scale(1)';
            }, 150);
        }

        // Tombol +/-
        function updateQty(change) {
            let currentVal = parseInt(inputQty.value);
            if (isNaN(currentVal)) currentVal = 1;

            let newVal = currentVal + change;
            if (newVal >= 1 && newVal <= maxStock) {
                processSubtotalChange(newVal);
            } else if (newVal > maxStock) {
                Swal.fire({ toast: true, position: 'top-end', icon: 'warning', title: 'Stok tidak mencukupi!', showConfirmButton: false, timer: 2000 });
            }
        }

        // Input Manual via Keyboard
        inputQty.addEventListener('input', function() {
            let val = parseInt(this.value);
            if (isNaN(val) || val < 1) {
                val = 1;
            } else if (val > maxStock) {
                val = maxStock;
                Swal.fire({ toast: true, position: 'top-end', icon: 'warning', title: 'Maksimal stok ' + maxStock, showConfirmButton: false, timer: 2000 });
            }
            processSubtotalChange(val);
        });

        // 4. Animasi Ganti Gambar (Gallery)
        function changeImage(btn, url) {
            const mainImg = document.getElementById('mainProductImage');
            mainImg.style.opacity = "0.5";
            mainImg.style.transform = "scale(0.95)";
            
            setTimeout(() => {
                mainImg.src = url;
                mainImg.style.opacity = "1";
                mainImg.style.transform = "scale(1)";
            }, 200);

            document.querySelectorAll('.thumb-btn').forEach(el => {
                el.classList.remove('border-brand-600', 'shadow-md', 'ring-4', 'ring-brand-50', 'scale-105');
                el.classList.add('border-zinc-100', 'opacity-60');
            });
            btn.classList.add('border-brand-600', 'shadow-md', 'ring-4', 'ring-brand-50', 'scale-105');
            btn.classList.remove('opacity-60');
        }

        // 5. Fungsi Peringatan Chat (Bagi yang belum login)
        function requireChatLogin() {
            Swal.fire({
                icon: 'lock',
                title: 'Akses Ditolak',
                text: 'Silakan login terlebih dahulu untuk memulai obrolan dengan penjual.',
                confirmButtonText: 'Login Sekarang',
                confirmButtonColor: '#2563eb',
                showCancelButton: true,
                cancelButtonText: 'Batal',
                customClass: { popup: 'rounded-3xl' }
            }).then((result) => {
                if (result.isConfirmed) { window.location.href = "{{ route('login') }}"; }
            });
        }

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
            }
        }

        // 6. LOGIKA TOMBOL CHECKOUT & KERANJANG
        document.addEventListener('DOMContentLoaded', function() {
            const btnKeranjang = document.getElementById('btnKeranjang');
            const btnBeliLangsung = document.getElementById('btnBeliLangsung');

            // --- A. TAMBAH KE KERANJANG (AJAX) ---
            if (btnKeranjang) {
                btnKeranjang.addEventListener('click', async function() {
                    @guest
                        requireChatLogin(); return;
                    @endguest

                    const originalText = btnKeranjang.innerHTML;
                    btnKeranjang.innerHTML = '<i class="fas fa-spinner fa-spin mr-1.5"></i> Memproses...';
                    btnKeranjang.disabled = true;

                    try {
                        const formData = new FormData(formKeranjang);
                        const response = await fetch('{{ route('keranjang.tambah') }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            },
                            body: formData
                        });

                        const result = await response.json();

                        if (response.ok && result.status === 'success') {
                            Swal.fire({
                                icon: 'success', title: 'Berhasil!', text: 'Material masuk ke keranjang Anda.',
                                showConfirmButton: false, timer: 1500, customClass: { popup: 'rounded-3xl' }
                            });
                            setTimeout(() => window.location.reload(), 1500);
                        } else {
                            throw new Error(result.message || 'Gagal menambahkan ke keranjang');
                        }
                    } catch (error) {
                        Swal.fire({ icon: 'error', title: 'Oops...', text: error.message, customClass: { popup: 'rounded-3xl' } });
                    } finally {
                        btnKeranjang.innerHTML = originalText;
                        btnKeranjang.disabled = false;
                    }
                });
            }

            // --- B. BELI LANGSUNG (REDIRECT KE CHECKOUT) ---
            if (btnBeliLangsung) {
                btnBeliLangsung.addEventListener('click', function() {
                    @guest
                        requireChatLogin(); return;
                    @endguest

                    // Ambil value qty saat ini
                    let qty = inputQty.value;
                    
                    // Redirect langsung ke URL Checkout dengan parameter
                    window.location.href = `{{ url('checkout') }}?product_id={{ $product->id }}&jumlah=${qty}`;
                });
            }
        });
    </script>
</body>
</html>