<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Pesanan - Pondasikita</title>
    
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
                        'glow': '0 0 20px rgba(37,99,235,0.2)',
                    }
                }
            }
        }
    </script>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f4f4f5; }
        
        /* Custom Scrollbar untuk Tab Menu (Mirip App Mobile) */
        .hide-scrollbar::-webkit-scrollbar { display: none; }
        .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; scroll-behavior: smooth; }
        
        /* Glass effect untuk Sticky Header */
        .sticky-glass {
            background: rgba(244, 244, 245, 0.85);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
        }
    </style>
</head>
<body class="text-zinc-800 antialiased pt-[80px]">

    @include('partials.navbar')

    <main class="max-w-[1100px] mx-auto px-4 sm:px-6 py-8 lg:py-12">

        {{-- HEADER & STATS SUMMARY --}}
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-8">
            <div>
                <h1 class="text-3xl lg:text-4xl font-black text-black tracking-tight">Pesanan Saya</h1>
                <p class="text-sm font-medium text-zinc-500 mt-2">Pantau pengadaan material dan riwayat transaksi proyek Anda.</p>
            </div>

            {{-- Mini Stats --}}
            <div class="flex items-center gap-3">
                <div class="bg-white border border-zinc-200 px-5 py-3 rounded-2xl shadow-soft">
                    <span class="block text-[10px] font-black text-zinc-400 uppercase tracking-widest">Total Transaksi</span>
                    <span class="text-xl font-black text-black leading-none">{{ $orders->count() }}</span>
                </div>
                <div class="bg-blue-600 px-5 py-3 rounded-2xl shadow-glow">
                    <span class="block text-[10px] font-black text-blue-200 uppercase tracking-widest">Pesanan Aktif</span>
                    <span class="text-xl font-black text-white leading-none">
                        {{ $orders->whereNotIn('status_pesanan_global', ['selesai', 'dibatalkan'])->count() }}
                    </span>
                </div>
            </div>
        </div>

        {{-- ======================================================= --}}
        {{-- TAB FILTER NAVIGATION (STICKY & E-COMMERCE STYLE) --}}
        {{-- ======================================================= --}}
        @if ($orders->isNotEmpty())
        <div class="sticky top-[70px] z-30 sticky-glass pb-4 pt-2 -mx-4 px-4 sm:mx-0 sm:px-0 border-b border-zinc-200/60 mb-8">
            <div class="flex overflow-x-auto gap-2 hide-scrollbar snap-x" id="tab-container">
                <button class="tab-btn snap-start px-6 py-2.5 rounded-full text-[13px] font-black whitespace-nowrap transition-all duration-300 border border-transparent" data-target="semua">Semua</button>
                <button class="tab-btn snap-start px-6 py-2.5 rounded-full text-[13px] font-black whitespace-nowrap transition-all duration-300 border border-transparent" data-target="menunggu_pembayaran">Belum Dibayar</button>
                <button class="tab-btn snap-start px-6 py-2.5 rounded-full text-[13px] font-black whitespace-nowrap transition-all duration-300 border border-transparent" data-target="dikemas">Dikemas</button>
                <button class="tab-btn snap-start px-6 py-2.5 rounded-full text-[13px] font-black whitespace-nowrap transition-all duration-300 border border-transparent" data-target="dikirim">Dikirim</button>
                <button class="tab-btn snap-start px-6 py-2.5 rounded-full text-[13px] font-black whitespace-nowrap transition-all duration-300 border border-transparent" data-target="selesai">Selesai</button>
                <button class="tab-btn snap-start px-6 py-2.5 rounded-full text-[13px] font-black whitespace-nowrap transition-all duration-300 border border-transparent" data-target="komplain">Pengembalian</button>
                <button class="tab-btn snap-start px-6 py-2.5 rounded-full text-[13px] font-black whitespace-nowrap transition-all duration-300 border border-transparent" data-target="dibatalkan">Dibatalkan</button>
            </div>
        </div>
        @endif

        {{-- ======================================================= --}}
        {{-- MAIN CONTENT AREA --}}
        {{-- ======================================================= --}}
        @if ($orders->isEmpty())
            {{-- EMPTY STATE GLOBAL --}}
            <div class="bg-white rounded-[3rem] shadow-soft border border-zinc-200 p-16 text-center animate-fade-in flex flex-col items-center justify-center">
                <div class="relative mb-8">
                    <div class="w-24 h-24 bg-zinc-50 rounded-full flex items-center justify-center shadow-inner">
                        <i class="fas fa-box-open text-4xl text-zinc-300"></i>
                    </div>
                    <div class="absolute -top-2 -right-2 w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center animate-bounce">
                        <i class="fas fa-search text-blue-500 text-xs"></i>
                    </div>
                </div>
                <h2 class="text-2xl font-black text-black mb-3">Belum Ada Material Dipesan</h2>
                <p class="text-zinc-500 font-medium max-w-sm mb-10 leading-relaxed">Mulai bangun proyek impianmu sekarang. Telusuri katalog material terlengkap kami.</p>
                <a href="{{ route('produk.index') }}" class="bg-black hover:bg-blue-600 text-white font-bold py-4 px-10 rounded-2xl transition-all shadow-xl hover:-translate-y-1">
                    <i class="fas fa-shopping-cart mr-2"></i> Belanja Material
                </a>
            </div>
        @else
            {{-- DAFTAR PESANAN --}}
            <div class="space-y-5" id="order-list">
                
                {{-- Notifikasi Filter Kosong --}}
                <div id="empty-filter-state" class="hidden bg-white/50 backdrop-blur-sm border-2 border-dashed border-zinc-300 rounded-[2.5rem] p-16 text-center animate-fade-in">
                    <i class="fas fa-folder-open text-4xl text-zinc-300 mb-5 block"></i>
                    <h3 class="text-xl font-black text-zinc-800">Tidak Ada Pesanan</h3>
                    <p class="text-sm font-medium text-zinc-500 mt-2">Belum ada transaksi yang masuk dalam kategori ini.</p>
                </div>

                @foreach($orders as $row)
                    @php
                        // ==========================================
                        // LOGIKA PENGELOMPOKAN TAB BARU TINGKAT DEWA
                        // ==========================================
                        $rawStatus = $row->status_pesanan_global;
                        $filterGroup = 'semua';
                        
                        if ($rawStatus == 'menunggu_pembayaran') { 
                            $filterGroup = 'menunggu_pembayaran'; 
                        } elseif (in_array($rawStatus, ['diproses', 'siap_kirim'])) { 
                            $filterGroup = 'dikemas'; 
                        } elseif (in_array($rawStatus, ['dikirim', 'sampai_tujuan'])) { 
                            $filterGroup = 'dikirim'; 
                        } elseif ($rawStatus == 'selesai') { 
                            $filterGroup = 'selesai'; 
                        } elseif ($rawStatus == 'komplain') { 
                            $filterGroup = 'komplain'; 
                        } elseif ($rawStatus == 'dibatalkan') { 
                            $filterGroup = 'dibatalkan'; 
                        }

                        // Logika Warna & Ikon B2B Premium
                        $statusCfg = [
                            'menunggu_pembayaran' => ['color' => 'bg-amber-50 text-amber-600 border-amber-200', 'icon' => 'fa-wallet'],
                            'diproses' => ['color' => 'bg-blue-50 text-blue-600 border-blue-200', 'icon' => 'fa-box-open'],
                            'siap_kirim' => ['color' => 'bg-indigo-50 text-indigo-600 border-indigo-200', 'icon' => 'fa-box-check'],
                            'dikirim' => ['color' => 'bg-purple-50 text-purple-600 border-purple-200', 'icon' => 'fa-truck-fast'],
                            'selesai' => ['color' => 'bg-emerald-50 text-emerald-600 border-emerald-200', 'icon' => 'fa-shield-check'],
                            'sampai_tujuan' => ['color' => 'bg-emerald-50 text-emerald-600 border-emerald-200', 'icon' => 'fa-house-circle-check'],
                            'dibatalkan' => ['color' => 'bg-red-50 text-red-600 border-red-200', 'icon' => 'fa-ban'],
                            'komplain' => ['color' => 'bg-orange-50 text-orange-600 border-orange-200', 'icon' => 'fa-triangle-exclamation'],
                            'default' => ['color' => 'bg-zinc-50 text-zinc-600 border-zinc-200', 'icon' => 'fa-circle-info'],
                        ];
                        $cfg = $statusCfg[$row->status_pesanan_global] ?? $statusCfg['default'];
                    @endphp

                    {{-- Card Pesanan --}}
                    <div class="order-card bg-white rounded-[2rem] shadow-sm border border-zinc-200 overflow-hidden transition-all duration-300 hover:shadow-float hover:border-zinc-300 group" data-group="{{ $filterGroup }}">
                        
                        {{-- Card Header: Nomor Invoice & Status --}}
                        <div class="bg-zinc-50/70 border-b border-zinc-100 px-6 sm:px-8 py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl bg-white border border-zinc-200 flex items-center justify-center shadow-sm">
                                    <i class="fas fa-file-invoice text-zinc-400 group-hover:text-blue-600 transition-colors text-sm"></i>
                                </div>
                                <div>
                                    <span class="block text-[9px] font-black text-zinc-500 uppercase tracking-[0.2em] mb-0.5 flex items-center gap-2">
                                        No. Invoice 
                                        <i class="fas fa-copy cursor-pointer text-zinc-300 hover:text-blue-500 transition-colors" title="Salin ID" onclick="navigator.clipboard.writeText('{{ $row->kode_invoice }}'); alert('Invoice disalin!');"></i>
                                    </span>
                                    <h6 class="font-black text-black tracking-tight leading-none uppercase text-sm">{{ $row->kode_invoice }}</h6>
                                </div>
                            </div>

                            <div class="flex items-center self-start sm:self-auto">
                                <div class="px-3 py-1.5 rounded-lg border {{ $cfg['color'] }} text-[10px] font-black tracking-widest uppercase flex items-center gap-2 shadow-sm">
                                    <i class="fas {{ $cfg['icon'] }}"></i>
                                    {{ str_replace('_', ' ', $row->status_pesanan_global) }}
                                </div>
                            </div>
                        </div>

                        {{-- Card Body: Rincian Harga & Aksi --}}
                        <div class="px-6 sm:px-8 py-6 flex flex-col md:flex-row md:items-center justify-between gap-6">
                            <div class="flex items-center gap-6">
                                <div class="hidden sm:block border-r border-zinc-100 pr-6">
                                    <span class="block text-[9px] font-black text-zinc-400 uppercase tracking-widest mb-1.5">Waktu Transaksi</span>
                                    <div class="flex items-center gap-2">
                                        <div class="text-center bg-zinc-50 rounded-lg border border-zinc-100 px-2 py-1">
                                            <span class="block text-lg font-black text-black leading-none">{{ \Carbon\Carbon::parse($row->tanggal_transaksi)->format('d') }}</span>
                                            <span class="text-[9px] font-bold text-zinc-500 uppercase">{{ \Carbon\Carbon::parse($row->tanggal_transaksi)->format('M') }}</span>
                                        </div>
                                        <div class="h-6 w-px bg-zinc-200"></div>
                                        <span class="text-xs font-bold text-zinc-500">{{ \Carbon\Carbon::parse($row->tanggal_transaksi)->format('H:i') }} WIB</span>
                                    </div>
                                </div>

                                <div>
                                    <span class="block text-[9px] font-black text-zinc-400 uppercase tracking-widest mb-1">Total Belanja</span>
                                    <div class="text-2xl font-black text-black tracking-tighter flex items-start gap-1">
                                        <span class="text-xs font-bold mt-1.5 text-zinc-400">Rp</span>
                                        {{ number_format($row->total_final, 0, ',', '.') }}
                                    </div>
                                </div>
                            </div>

                            {{-- Tombol Aksi Estetik --}}
                            <div class="flex items-center gap-3 w-full md:w-auto mt-2 md:mt-0 border-t border-zinc-100 pt-5 md:border-0 md:pt-0">
                                @if($row->status_pesanan_global == 'menunggu_pembayaran')
                                    <a href="{{ route('pesanan.lacak', $row->kode_invoice) }}" class="flex-1 md:flex-none bg-blue-600 hover:bg-blue-700 text-white font-black py-3 px-6 rounded-xl transition-all duration-300 shadow-[0_4px_14px_rgba(37,99,235,0.3)] hover:shadow-[0_6px_20px_rgba(37,99,235,0.4)] text-[11px] uppercase tracking-wider flex items-center justify-center gap-2 text-center">
                                        Bayar Sekarang
                                    </a>
                                @else
                                    <a href="{{ route('pesanan.lacak', $row->kode_invoice) }}" class="flex-1 md:flex-none bg-zinc-900 hover:bg-zinc-800 text-white font-black py-3 px-6 rounded-xl transition-all duration-300 shadow-md text-[11px] uppercase tracking-wider flex items-center justify-center gap-2 text-center group/btn">
                                        Lihat Detail <i class="fas fa-arrow-right text-[10px] group-hover/btn:translate-x-1 transition-transform"></i>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        {{-- Bantuan Section --}}
        <div class="mt-16 bg-zinc-950 rounded-[3rem] p-10 lg:p-14 relative overflow-hidden shadow-2xl">
            <div class="absolute top-0 right-0 w-64 h-64 bg-blue-600/20 rounded-full blur-[80px]"></div>
            <div class="relative z-10 grid md:grid-cols-2 gap-8 items-center">
                <div>
                    <h3 class="text-xl font-black text-white mb-3">Butuh Bantuan Pengadaan?</h3>
                    <p class="text-zinc-400 text-sm font-medium leading-relaxed">Hubungi Customer Service resmi Pondasikita jika Anda menemui kendala dalam pesanan atau komplain material.</p>
                </div>
                <div class="flex flex-wrap gap-3 md:justify-end">
                    <a href="mailto:support@pondasikita.com" class="bg-white/5 border border-white/10 text-white font-bold py-2.5 px-6 rounded-xl hover:bg-white/10 transition-colors text-xs flex items-center gap-2"><i class="fas fa-envelope"></i> Email Support</a>
                    <a href="#" class="bg-blue-600 hover:bg-blue-500 text-white font-bold py-2.5 px-6 rounded-xl shadow-lg transition-all text-xs flex items-center gap-2"><i class="fab fa-whatsapp text-lg"></i> WhatsApp Hotline</a>
                </div>
            </div>
        </div>

    </main>

    @include('partials.footer')
    @include('partials.chat')
    <script src="{{ asset('assets/js/navbar.js') }}"></script>

    {{-- Script Logika Filter Tab Menu Setara E-Commerce Besar --}}
    @if ($orders->isNotEmpty())
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tabs = document.querySelectorAll('.tab-btn');
            const cards = document.querySelectorAll('.order-card');
            const emptyFilterState = document.getElementById('empty-filter-state');
            const tabContainer = document.getElementById('tab-container');

            // Fungsi Utama Filtering
            function filterOrders(targetStatus) {
                let visibleCount = 0;

                cards.forEach(card => {
                    const cardGroup = card.getAttribute('data-group');
                    if (targetStatus === 'semua' || cardGroup === targetStatus) {
                        card.style.display = 'block';
                        // Refresh animasi biar keren
                        card.classList.remove('animate-fade-in');
                        void card.offsetWidth; // Trigger reflow
                        card.classList.add('animate-fade-in'); 
                        visibleCount++;
                    } else {
                        card.style.display = 'none';
                    }
                });

                // Tampilkan pesan kosong jika tidak ada pesanan di tab tersebut
                if (visibleCount === 0) {
                    emptyFilterState.classList.remove('hidden');
                } else {
                    emptyFilterState.classList.add('hidden');
                }
            }

            // Fungsi merubah warna Tab Menu
            function updateTabStyle(activeTab) {
                tabs.forEach(tab => {
                    if (tab === activeTab) {
                        // Style Tab Aktif (Hitam Elegan dengan border)
                        tab.classList.add('bg-zinc-900', 'text-white', 'border-zinc-900', 'shadow-md');
                        tab.classList.remove('bg-white', 'text-zinc-500', 'border-zinc-200', 'hover:bg-zinc-50');
                    } else {
                        // Style Tab Inaktif (Putih outline)
                        tab.classList.remove('bg-zinc-900', 'text-white', 'border-zinc-900', 'shadow-md');
                        tab.classList.add('bg-white', 'text-zinc-500', 'border-zinc-200', 'hover:bg-zinc-50');
                    }
                });
                
                // Auto-scroll tab supaya yang aktif selalu kelihatan di layar HP
                activeTab.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
            }

            // Event Listener ke setiap Tab
            tabs.forEach(tab => {
                tab.addEventListener('click', () => {
                    const target = tab.getAttribute('data-target');
                    updateTabStyle(tab);
                    filterOrders(target);
                });
            });

            // Set Default Tab saat halaman diload (Pilih Belum Dibayar)
            const defaultTab = document.querySelector('.tab-btn[data-target="menunggu_pembayaran"]');
            if (defaultTab) {
                updateTabStyle(defaultTab);
                filterOrders('menunggu_pembayaran');
            }
        });
    </script>
    @endif
</body>
</html>