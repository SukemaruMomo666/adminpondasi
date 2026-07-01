<!DOCTYPE html>
<html lang="id">
<head>
    <link rel="icon" href="{{ asset('logopondasikita.png') }}" type="image/png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Keranjang Belanja - Pondasikita B2B</title>
    
    {{-- Tailwind CSS CDN + Config Sesuai Tema --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'] },
                    colors: {
                        brand: { 50: '#eff6ff', 100: '#dbeafe', 500: '#3b82f6', 600: '#2563eb', 700: '#1d4ed8' }
                    },
                    boxShadow: {
                        'premium': '0 20px 40px -15px rgba(0,0,0,0.05)',
                        'bottom-nav': '0 -10px 40px rgba(0,0,0,0.08)',
                    },
                    animation: {
                        'fade-in-up': 'fadeInUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards',
                    },
                    keyframes: {
                        fadeInUp: { '0%': { opacity: 0, transform: 'translateY(20px)' }, '100%': { opacity: 1, transform: 'translateY(0)' } }
                    }
                }
            }
        }
    </script>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body { font-family: 'Inter', sans-serif; background-color: #fafafa; }

        /* Remove Number Input Arrows */
        input[type=number]::-webkit-inner-spin-button,
        input[type=number]::-webkit-outer-spin-button { -webkit-appearance: none; margin: 0; }
        input[type=number] { -moz-appearance: textfield; }

        /* Shimmer Effect for Buttons */
        @keyframes shimmer { 100% { transform: translateX(100%); } }
        .animate-shimmer { animation: shimmer 2.5s infinite; }
        
        .price-transition { transition: transform 0.15s cubic-bezier(0.4, 0, 0.2, 1); }
    </style>
</head>
<body class="flex flex-col min-h-screen text-zinc-900 antialiased pt-[80px]">

    @include('partials.navbar')

    {{-- BREADCRUMB MINIMALIS --}}
    <div class="bg-white border-b border-zinc-100 hidden md:block relative z-10">
        <div class="max-w-[1400px] mx-auto px-4 lg:px-8 py-4">
            <nav class="flex text-[10px] font-black uppercase tracking-[0.2em] text-zinc-400 items-center gap-3">
                <a href="{{ url('/') }}" class="hover:text-blue-600 transition-colors">Beranda</a>
                <i class="fas fa-chevron-right text-[8px] opacity-30"></i>
                <span class="text-zinc-900">Keranjang Belanja</span>
            </nav>
        </div>
    </div>

    <main class="flex-grow max-w-[1400px] mx-auto w-full px-4 lg:px-8 py-8 lg:py-12">

        {{-- Header Keranjang --}}
        <div class="mb-10 animate-fade-in-up text-center flex flex-col items-center">
            <h1 class="text-4xl lg:text-5xl font-black text-zinc-900 leading-tight tracking-tighter">
                Keranjang <span class="text-blue-600 italic">Belanja.</span>
            </h1>
            <p class="text-sm font-medium text-zinc-500 mt-3 max-w-lg mx-auto">
                Periksa kembali material untuk proyek Anda sebelum melakukan checkout.
            </p>
        </div>

        {{-- ======================================================= --}}
        {{-- KONDISI 1: BELUM LOGIN --}}
        {{-- ======================================================= --}}
        @if(isset($is_guest) && $is_guest)
            <div class="bg-white rounded-[2.5rem] shadow-sm border border-zinc-100 p-12 text-center animate-fade-in-up flex flex-col items-center justify-center min-h-[500px] relative overflow-hidden">
                <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/stardust.png')] opacity-[0.03]"></div>

                <div class="w-24 h-24 bg-zinc-50 border border-zinc-100 rounded-[2rem] flex items-center justify-center mb-8 relative z-10 shadow-inner">
                    <i class="fas fa-lock text-4xl text-zinc-300"></i>
                </div>
                <h2 class="text-3xl font-black text-zinc-900 tracking-tight mb-3 relative z-10">Akses Dibatasi</h2>
                <p class="text-zinc-500 font-medium max-w-md mb-10 relative z-10">Anda harus masuk ke akun B2B Anda terlebih dahulu untuk melihat dan mengelola keranjang belanja.</p>

                <a href="{{ route('login') }}" class="group relative bg-zinc-950 hover:bg-blue-600 text-white font-black py-4 px-10 rounded-2xl transition-all duration-500 shadow-xl hover:shadow-blue-500/25 hover:-translate-y-1 overflow-hidden z-10">
                    <div class="absolute inset-0 w-full h-full bg-gradient-to-r from-transparent via-white/10 to-transparent -translate-x-full group-hover:animate-shimmer"></div>
                    <span class="relative">Masuk ke Akun <i class="fas fa-arrow-right ml-2 text-xs"></i></span>
                </a>
            </div>

        {{-- ======================================================= --}}
        {{-- KONDISI 2: KERANJANG KOSONG --}}
        {{-- ======================================================= --}}
        @elseif(isset($groupedCart) && $groupedCart->isEmpty())
            <div class="bg-white rounded-[2.5rem] shadow-sm border border-zinc-100 p-12 text-center animate-fade-in-up flex flex-col items-center justify-center min-h-[500px] relative overflow-hidden">
                <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-64 h-64 bg-blue-50 rounded-full blur-[80px] pointer-events-none"></div>

                <div class="w-24 h-24 bg-white border border-zinc-100 rounded-[2rem] flex items-center justify-center mb-8 relative z-10 shadow-premium">
                    <i class="fas fa-shopping-basket text-4xl text-blue-500"></i>
                </div>
                <h2 class="text-3xl font-black text-zinc-900 tracking-tight mb-3 relative z-10">Keranjang Masih Kosong</h2>
                <p class="text-zinc-500 font-medium max-w-md mb-10 relative z-10">Mulai eksplorasi ribuan material terbaik untuk membangun proyek impian Anda.</p>

                <a href="{{ route('produk.index') }}" class="group relative bg-blue-600 text-white font-black py-4 px-10 rounded-2xl transition-all duration-500 shadow-xl hover:shadow-blue-500/30 hover:-translate-y-1 overflow-hidden z-10">
                    <div class="absolute inset-0 w-full h-full bg-gradient-to-r from-transparent via-white/20 to-transparent -translate-x-full group-hover:animate-shimmer"></div>
                    <span class="relative">Mulai Belanja <i class="fas fa-arrow-right ml-2 text-xs"></i></span>
                </a>
            </div>

        {{-- ======================================================= --}}
        {{-- KONDISI 3: KERANJANG TERISI (12-COL GRID) --}}
        {{-- ======================================================= --}}
        @else
            <div class="flex flex-col lg:grid lg:grid-cols-12 gap-10 items-start relative">

                {{-- BAGIAN KIRI: LIST BARANG (Col-span-8) --}}
                <div class="w-full lg:col-span-8 flex flex-col gap-6 animate-fade-in-up">

                    {{-- Master Checkbox (Pilih Semua) --}}
                    <div class="bg-white rounded-[2rem] border border-zinc-100 p-5 flex items-center gap-4 shadow-sm">
                        <label class="flex items-center gap-4 cursor-pointer group">
                            <div class="relative flex items-center justify-center shrink-0">
                                <input type="checkbox" id="check-all" class="peer sr-only" checked onchange="toggleAllCheckboxes(this)">
                                <div class="w-6 h-6 rounded-lg border-2 border-zinc-200 peer-checked:bg-blue-600 peer-checked:border-blue-600 transition-all duration-300 flex items-center justify-center group-hover:border-blue-400">
                                    <i class="fas fa-check text-white text-xs opacity-0 peer-checked:opacity-100 scale-50 peer-checked:scale-100 transition-all duration-300"></i>
                                </div>
                            </div>
                            <span class="text-sm font-bold text-zinc-600 group-hover:text-zinc-900 transition-colors select-none uppercase tracking-widest">Pilih Semua Item</span>
                        </label>
                    </div>

                    {{-- Loop Per Toko --}}
                    @foreach($groupedCart as $namaToko => $items)
                        <div class="store-group bg-white rounded-[2.5rem] shadow-sm border border-zinc-100 overflow-hidden group/store transition-all hover:border-blue-100">

                            {{-- Header Toko --}}
                            <div class="bg-zinc-50/50 border-b border-zinc-100 px-8 py-5 flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-2xl bg-white border border-zinc-200 shadow-sm flex items-center justify-center text-blue-600 shrink-0">
                                        <i class="fas fa-store text-sm"></i>
                                    </div>
                                    <h3 class="font-black text-base text-zinc-900 tracking-tight">{{ $namaToko }}</h3>
                                </div>
                                <span class="text-[10px] font-black text-zinc-400 uppercase tracking-widest bg-white px-3 py-1 rounded-lg border border-zinc-100">Verified</span>
                            </div>

                            {{-- Loop Barang dalam Toko --}}
                            <div class="p-8 flex flex-col gap-8">
                                @foreach($items as $item)
                                    <div class="flex flex-col sm:flex-row gap-5 sm:gap-6 pb-8 border-b border-zinc-50 border-dashed last:border-0 last:pb-0 relative" id="cart-row-{{ $item->cart_id }}">

                                        {{-- Custom Checkbox Item & Image --}}
                                        <div class="flex gap-5 shrink-0">
                                            <label class="flex items-start mt-8 cursor-pointer">
                                                <div class="relative flex items-center justify-center shrink-0">
                                                    <input type="checkbox" class="peer sr-only js-item-checkbox"
                                                           data-id="{{ $item->cart_id }}"
                                                           data-price="{{ $item->harga }}"
                                                           data-qty="{{ $item->jumlah }}" checked>
                                                    <div class="w-6 h-6 rounded-lg border-2 border-zinc-200 peer-checked:bg-blue-600 peer-checked:border-blue-600 transition-all duration-300 flex items-center justify-center hover:border-blue-400">
                                                        <i class="fas fa-check text-white text-xs opacity-0 peer-checked:opacity-100 scale-50 peer-checked:scale-100 transition-all duration-300"></i>
                                                    </div>
                                                </div>
                                            </label>

                                            {{-- Gambar Produk --}}
                                            <div class="w-24 h-24 rounded-2xl bg-zinc-50 border border-zinc-100 overflow-hidden shrink-0 group relative">
                                                <img src="{{ asset('assets/uploads/products/' . ($item->gambar_utama ?? 'default.jpg')) }}" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110" onerror="this.onerror=null; this.src='{{ asset('assets/uploads/products/default.jpg') }}';">
                                            </div>
                                        </div>

                                        {{-- Info & Action --}}
                                        <div class="flex-1 min-w-0 flex flex-col">
                                            <h4 class="text-base font-bold text-zinc-800 line-clamp-2 leading-snug mb-2 hover:text-blue-600 transition-colors cursor-pointer">{{ $item->nama_barang }}</h4>
                                            <div class="text-xl font-black text-zinc-900 tracking-tight mb-4">Rp{{ number_format($item->harga, 0, ',', '.') }}</div>

                                            <div class="mt-auto flex flex-wrap items-center justify-between gap-4">

                                                {{-- Qty Control (Premium Pill UI) --}}
                                                <div class="flex items-center bg-zinc-50 border border-zinc-200 rounded-xl p-1">
                                                    <button type="button" class="w-8 h-8 flex items-center justify-center text-zinc-400 hover:bg-white hover:text-zinc-900 hover:shadow-sm rounded-lg transition-all font-black" onclick="updateQty({{ $item->cart_id }}, -1)">-</button>
                                                    <input type="text" id="qty-input-{{ $item->cart_id }}" class="w-10 text-center font-black text-sm text-zinc-900 outline-none bg-transparent" value="{{ $item->jumlah }}" readonly>
                                                    <button type="button" class="w-8 h-8 flex items-center justify-center text-blue-600 hover:bg-white hover:shadow-sm rounded-lg transition-all font-black" onclick="updateQty({{ $item->cart_id }}, 1)">+</button>
                                                </div>

                                                {{-- Tombol Hapus --}}
                                                <button type="button" onclick="hapusItem({{ $item->cart_id }})" class="text-zinc-400 hover:text-red-500 transition-all px-3 py-2 hover:bg-red-50 rounded-xl flex items-center gap-2 group/del border border-transparent hover:border-red-100">
                                                    <i class="fas fa-trash-alt text-sm group-hover/del:scale-110 transition-transform"></i>
                                                    <span class="text-[10px] font-black uppercase tracking-widest hidden sm:inline">Hapus</span>
                                                </button>

                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- BAGIAN KANAN: RINGKASAN BELANJA (Col-span-4 Sticky) --}}
                <div class="w-full lg:col-span-4 lg:sticky lg:top-28 animate-fade-in-up" style="animation-delay: 0.1s;">

                    {{-- Form Checkout (Data Tersembunyi) --}}
                    <form action="{{ route('checkout') }}" method="POST" id="checkout-form">
                        @csrf
                        <input type="hidden" name="selected_items" id="selected-items-input">
                        {{-- Hidden input buat data voucher jika ada ke backend --}}
                        <input type="hidden" name="voucher_code" id="input-voucher-code">

                        <div class="bg-white rounded-[2.5rem] shadow-premium border border-zinc-100 p-8 relative overflow-hidden">
                            {{-- Ornamen Halus --}}
                            <div class="absolute -top-10 -right-10 w-32 h-32 bg-blue-50 rounded-full blur-2xl pointer-events-none"></div>

                            <h3 class="text-[10px] font-black text-zinc-400 uppercase tracking-[0.3em] mb-8 border-b border-zinc-50 pb-4 relative z-10">
                                Ringkasan Tagihan
                            </h3>

                            {{-- Subtotal & Diskon --}}
                            <div class="space-y-4 mb-6 text-sm relative z-10">
                                <div class="flex justify-between items-center text-zinc-500 font-medium">
                                    <span>Total Material (<span id="summary-count" class="font-bold text-zinc-900">0</span>)</span>
                                    <span id="summary-price" class="font-bold text-zinc-900">Rp0</span>
                                </div>
                                <div class="flex justify-between items-center text-zinc-500 font-medium">
                                    <span>Diskon Platform</span>
                                    <span id="summary-discount" class="font-black text-emerald-500">- Rp0</span>
                                </div>
                            </div>

                            {{-- ======================================================= --}}
                            {{-- FITUR VOUCHER GLOBAL TINGKAT DEWA --}}
                            {{-- ======================================================= --}}
                            <div class="mb-8 border-t border-zinc-100 pt-6 relative z-10">
                                <span class="text-[10px] font-black text-zinc-900 uppercase tracking-widest mb-3 flex items-center gap-1.5">
                                    <i class="fas fa-ticket-alt text-blue-500"></i> Makin Hemat Pakai Promo
                                </span>
                                
                                {{-- Box Input Promo --}}
                                <div id="voucher-input-box" class="flex items-center gap-2">
                                    <div class="relative flex-1">
                                        <input type="text" id="voucher-input" placeholder="Masukkan kode" class="w-full bg-zinc-50 border border-zinc-200 text-zinc-800 text-xs font-bold rounded-xl focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 block px-4 py-3.5 transition-all outline-none uppercase placeholder:normal-case placeholder:font-medium placeholder:text-zinc-400">
                                    </div>
                                    <button type="button" onclick="applyVoucher()" id="btn-apply-voucher" class="bg-zinc-900 hover:bg-blue-600 text-white px-5 py-3.5 rounded-xl font-black transition-colors shadow-md text-[10px] uppercase tracking-widest shrink-0 w-[100px] flex justify-center items-center">
                                        Terapkan
                                    </button>
                                </div>

                                {{-- Pesan Notifikasi Promo --}}
                                <div id="voucher-message" class="mt-2 hidden"></div>

                                {{-- Tag Promo Terpasang --}}
                                <div id="applied-voucher-tag" class="hidden mt-3 items-center justify-between bg-blue-50 border border-blue-200 px-3 py-2 rounded-lg">
                                    <div class="flex items-center gap-2">
                                        <div class="w-6 h-6 rounded-full bg-blue-600 flex items-center justify-center text-white text-[10px]">
                                            <i class="fas fa-check"></i>
                                        </div>
                                        <span class="text-xs font-black text-blue-700 uppercase tracking-wider" id="applied-voucher-code">PROMO10</span>
                                    </div>
                                    <button type="button" onclick="removeVoucher()" class="text-red-500 hover:text-red-700 p-1 transition-colors" title="Hapus Voucher"><i class="fas fa-times"></i></button>
                                </div>
                            </div>

                            {{-- Total Harga Akhir --}}
                            <div class="pt-6 border-t border-zinc-100 border-dashed mb-8 relative z-10">
                                <div class="flex justify-between items-end">
                                    <span class="text-[11px] font-black text-zinc-900 uppercase tracking-widest">Total Bayar</span>
                                    <span id="summary-total-price" class="text-3xl lg:text-4xl font-black text-blue-600 tracking-tighter leading-none price-transition">Rp0</span>
                                </div>
                            </div>

                            {{-- Tombol Checkout Desktop --}}
                            <button type="submit" id="btn-checkout-desktop" class="hidden lg:flex group relative w-full bg-zinc-950 hover:bg-blue-600 text-white font-black py-5 rounded-[1.5rem] transition-all duration-500 shadow-xl hover:shadow-blue-500/25 hover:-translate-y-1 items-center justify-center gap-2 overflow-hidden disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:bg-zinc-950 disabled:hover:translate-y-0 disabled:hover:shadow-none relative z-10">
                                <div class="absolute inset-0 w-full h-full bg-gradient-to-r from-transparent via-white/10 to-transparent -translate-x-full group-hover:animate-shimmer"></div>
                                <span class="relative text-xs uppercase tracking-widest">Lanjut Pembayaran <i class="fas fa-chevron-right ml-1 text-[10px]"></i></span>
                            </button>

                            <p class="text-[10px] font-bold text-zinc-400 text-center mt-6 uppercase tracking-widest relative z-10">
                                <i class="fas fa-shield-alt mr-1"></i> Transaksi Aman 100%
                            </p>
                        </div>
                    </form>

                </div>
            </div>
        @endif

    </main>

    {{-- ======================================================= --}}
    {{-- MOBILE STICKY BOTTOM BAR --}}
    {{-- ======================================================= --}}
    @if(isset($groupedCart) && !$groupedCart->isEmpty() && !(isset($is_guest) && $is_guest))
        <div class="fixed bottom-0 left-0 w-full bg-white/90 backdrop-blur-xl border-t border-zinc-200 p-5 pb-safe shadow-bottom-nav z-50 lg:hidden flex items-center justify-between gap-4">
            <div class="flex flex-col flex-1 min-w-0">
                <span class="text-[10px] font-black text-zinc-500 uppercase tracking-widest mb-1">Total Tagihan</span>
                <span id="mobile-summary-total" class="text-2xl font-black text-blue-600 tracking-tighter truncate price-transition">Rp0</span>
            </div>
            <button type="button" onclick="submitCheckoutForm()" id="btn-checkout-mobile" class="w-auto px-8 bg-blue-600 text-white font-black py-4 rounded-2xl active:scale-95 transition-transform disabled:opacity-50 disabled:cursor-not-allowed text-xs uppercase tracking-widest shadow-lg shadow-blue-500/30">
                Checkout
            </button>
        </div>
    @endif
    
    @include('partials.chat')
    @include('partials.footer')

    <script src="{{ asset('assets/js/navbar.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Variabel untuk menyimpan state Diskon Voucher
        let currentDiscountPercent = 0; // Cth: 0.1 = 10%
        let currentVoucherCode = null;

        document.addEventListener('DOMContentLoaded', function() {
            calculateTotal();

            const checkboxes = document.querySelectorAll('.js-item-checkbox');
            checkboxes.forEach(cb => {
                cb.addEventListener('change', function() {
                    calculateTotal();
                    updateMasterCheckboxState();
                });
            });

            // Enter key trigger for voucher
            const voucherInput = document.getElementById('voucher-input');
            if(voucherInput) {
                voucherInput.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        applyVoucher();
                    }
                });
            }
        });

        // ==========================================
        // LOGIKA VOUCHER GLOBAL
        // ==========================================
        function applyVoucher() {
            const inputEl = document.getElementById('voucher-input');
            const code = inputEl.value.trim().toUpperCase();
            const btn = document.getElementById('btn-apply-voucher');
            const msg = document.getElementById('voucher-message');
            const tag = document.getElementById('applied-voucher-tag');
            const tagCode = document.getElementById('applied-voucher-code');
            const inputBox = document.getElementById('voucher-input-box');

            if(!code) return;

            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            btn.disabled = true;

            // Simulasi Request API (Bisa diganti logika ajax sesungguhnya nanti)
            setTimeout(() => {
                if(code === 'PROMO10') {
                    // Berhasil: Diskon 10%
                    currentVoucherCode = code;
                    currentDiscountPercent = 0.10; 
                    
                    msg.className = 'mt-2 text-[10px] font-bold text-emerald-600 flex items-center gap-1.5';
                    msg.innerHTML = '<i class="fas fa-check-circle"></i> Voucher berhasil diterapkan!';
                    msg.style.display = 'flex';

                    // Update UI Input Box -> Tag Badge
                    inputEl.value = '';
                    inputBox.classList.add('hidden');
                    
                    tagCode.innerText = code;
                    tag.classList.remove('hidden');
                    tag.classList.add('flex');
                    
                    document.getElementById('input-voucher-code').value = code;

                    // Kalkulasi ulang total harga
                    calculateTotal();

                } else {
                    // Gagal
                    msg.className = 'mt-2 text-[10px] font-bold text-red-500 flex items-center gap-1.5';
                    msg.innerHTML = '<i class="fas fa-exclamation-circle"></i> Kode voucher tidak valid atau kedaluwarsa.';
                    msg.style.display = 'flex';
                    inputEl.classList.add('border-red-300', 'focus:border-red-500', 'bg-red-50');
                }
                
                btn.innerHTML = 'Terapkan';
                btn.disabled = false;
                
                // Hilangkan pesan error setelah 3 detik
                setTimeout(() => { 
                    msg.style.display = 'none'; 
                    inputEl.classList.remove('border-red-300', 'focus:border-red-500', 'bg-red-50'); 
                }, 3000);

            }, 800); // Simulasi delay API
        }

        function removeVoucher() {
            currentVoucherCode = null;
            currentDiscountPercent = 0;
            document.getElementById('input-voucher-code').value = '';
            
            document.getElementById('applied-voucher-tag').classList.add('hidden');
            document.getElementById('applied-voucher-tag').classList.remove('flex');
            
            document.getElementById('voucher-input-box').classList.remove('hidden');
            document.getElementById('voucher-message').style.display = 'none';
            
            // Kalkulasi ulang tanpa diskon
            calculateTotal();
        }

        // ==========================================
        // LOGIKA CHECKBOX MASTER
        // ==========================================
        function toggleAllCheckboxes(masterCb) {
            const isChecked = masterCb.checked;
            const checkboxes = document.querySelectorAll('.js-item-checkbox');
            checkboxes.forEach(cb => { cb.checked = isChecked; });
            calculateTotal();
        }

        function updateMasterCheckboxState() {
            const masterCb = document.getElementById('check-all');
            if(!masterCb) return;
            const allCheckboxes = document.querySelectorAll('.js-item-checkbox');
            const checkedCheckboxes = document.querySelectorAll('.js-item-checkbox:checked');
            masterCb.checked = (allCheckboxes.length > 0 && allCheckboxes.length === checkedCheckboxes.length);
        }

        // ==========================================
        // KALKULATOR TOTAL + DISKON
        // ==========================================
        function calculateTotal() {
            let totalBarang = 0;
            let totalHargaAsli = 0;
            let selectedIds = [];

            const checkboxes = document.querySelectorAll('.js-item-checkbox:checked');
            checkboxes.forEach(cb => {
                let qty = parseInt(cb.getAttribute('data-qty'));
                let price = parseInt(cb.getAttribute('data-price'));

                totalBarang += qty;
                totalHargaAsli += (qty * price);
                selectedIds.push(cb.getAttribute('data-id'));
            });

            // Hitung Diskon
            let totalDiskon = totalHargaAsli * currentDiscountPercent;
            let totalAkhir = totalHargaAsli - totalDiskon;

            // Format Rupiah
            const formatRupiah = (num) => 'Rp' + Math.round(num).toLocaleString('id-ID');

            // DOM Elements
            const elCount = document.getElementById('summary-count');
            const elPrice = document.getElementById('summary-price');
            const elDiscount = document.getElementById('summary-discount');
            const elTotal = document.getElementById('summary-total-price');
            const elInput = document.getElementById('selected-items-input');
            const elBtnDesktop = document.getElementById('btn-checkout-desktop');
            const elTotalMobile = document.getElementById('mobile-summary-total');
            const elBtnMobile = document.getElementById('btn-checkout-mobile');

            if(elCount) elCount.innerText = totalBarang;
            if(elPrice) elPrice.innerText = formatRupiah(totalHargaAsli);
            if(elDiscount) elDiscount.innerText = '- ' + formatRupiah(totalDiskon);

            // Animasi transisi harga akhir
            const animatePriceChange = (element, newText) => {
                if(!element) return;
                element.style.transform = 'scale(0.95)';
                element.style.opacity = '0.5';
                setTimeout(() => {
                    element.innerText = newText;
                    element.style.transform = 'scale(1)';
                    element.style.opacity = '1';
                }, 150);
            };

            animatePriceChange(elTotal, formatRupiah(totalAkhir));
            animatePriceChange(elTotalMobile, formatRupiah(totalAkhir));

            if(elInput) elInput.value = selectedIds.join(',');

            // Atur tombol Checkout aktif/nonaktif
            const isDisabled = (totalBarang === 0);
            if(elBtnDesktop) elBtnDesktop.disabled = isDisabled;
            if(elBtnMobile) elBtnMobile.disabled = isDisabled;
        }

        function submitCheckoutForm() {
            document.getElementById('checkout-form').submit();
        }

        // ==========================================
        // UPDATE QUANTITY AJAX
        // ==========================================
        async function updateQty(cartId, change) {
            const input = document.getElementById(`qty-input-${cartId}`);
            const checkbox = document.querySelector(`.js-item-checkbox[data-id="${cartId}"]`);
            let currentQty = parseInt(input.value);
            let newQty = currentQty + change;

            if (newQty < 1) return;
            input.value = '...';

            try {
                const res = await fetch('{{ route('keranjang.update') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ cart_id: cartId, jumlah: newQty })
                });

                if (res.ok) {
                    input.value = newQty;
                    checkbox.setAttribute('data-qty', newQty);
                    input.style.transform = 'scale(1.2)';
                    setTimeout(() => input.style.transform = 'scale(1)', 150);
                    calculateTotal();
                } else {
                    input.value = currentQty;
                    Swal.fire({ icon: 'error', title: 'Gagal', text: 'Gagal memperbarui jumlah', customClass: { popup: 'rounded-3xl' }});
                }
            } catch (error) {
                input.value = currentQty;
            }
        }

        // ==========================================
        // HAPUS ITEM AJAX
        // ==========================================
        function hapusItem(cartId) {
            Swal.fire({
                title: 'Hapus Material?',
                text: "Item ini akan dikeluarkan dari keranjang.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#09090b',
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal',
                customClass: { popup: 'rounded-3xl', confirmButton: 'rounded-xl font-bold', cancelButton: 'rounded-xl font-bold' }
            }).then(async (result) => {
                if (result.isConfirmed) {
                    try {
                        const res = await fetch('{{ route('keranjang.hapus') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({ cart_id: cartId })
                        });

                        if (res.ok) {
                            const row = document.getElementById(`cart-row-${cartId}`);
                            row.style.transition = 'all 0.4s cubic-bezier(0.4, 0, 0.2, 1)';
                            row.style.opacity = '0';
                            row.style.transform = 'translateX(-20px)';

                            setTimeout(() => {
                                row.remove();
                                calculateTotal();

                                if(document.querySelectorAll('.js-item-checkbox').length === 0) {
                                    window.location.reload();
                                } else {
                                    document.querySelectorAll('.store-group').forEach(store => {
                                        const itemsInStore = store.querySelectorAll('.js-item-checkbox');
                                        if(itemsInStore.length === 0) {
                                            store.style.transition = 'all 0.4s ease';
                                            store.style.opacity = '0';
                                            store.style.transform = 'scale(0.95)';
                                            setTimeout(() => store.remove(), 400);
                                        }
                                    });
                                }
                            }, 400);
                        }
                    } catch (error) {
                        Swal.fire({ icon: 'error', title: 'Error', text: 'Koneksi terputus', customClass: { popup: 'rounded-3xl' }});
                    }
                }
            });
        }
    </script>
</body>
</html>
