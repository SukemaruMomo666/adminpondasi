{{-- ========================================================
     ABSOLUTE CINEMA NAVBAR (BUG-FIXED SHADOW BLEED & AVATAR)
     ======================================================== --}}
<header class="fixed top-0 inset-x-0 z-40 bg-white/80 backdrop-blur-xl border-b border-zinc-200 shadow-[0_4px_30px_rgba(0,0,0,0.03)] h-20 transition-all duration-500 flex items-center">
    <div class="max-w-7xl mx-auto px-4 w-full flex items-center justify-between gap-4">

        {{-- KIRI: Tombol Sidebar & Logo --}}
        <div class="flex items-center gap-5 shrink-0">
            {{-- Tombol Buka Sidebar --}}
            <button id="btn-open-sidebar" class="group relative w-10 h-10 flex items-center justify-center rounded-2xl bg-zinc-50 border border-zinc-200 text-zinc-600 hover:bg-black hover:text-white hover:border-black transition-all duration-300 focus:outline-none focus:ring-4 focus:ring-zinc-200">
                <i class="fas fa-bars text-lg group-hover:scale-110 transition-transform"></i>
            </button>

            {{-- Logo --}}
            <a href="{{ url('/') }}" class="hidden sm:flex items-center gap-2 group">
                <div class="w-8 h-8 bg-black rounded-lg flex items-center justify-center text-white font-black group-hover:bg-blue-600 transition-colors duration-300 shadow-md">P</div>
                <h3 class="text-2xl font-black text-black tracking-tight">Pondasikita<span class="text-blue-600 animate-pulse">.</span></h3>
            </a>
        </div>

       {{-- TENGAH: Search Bar Enterprise --}}
        <form action="{{ route('search') }}" method="GET" class="flex-1 max-w-2xl relative group hidden md:block">
            <input
                type="text"
                id="search-input"
                name="query"
                placeholder="Cari semen, baja ringan, vendor..."
                value="{{ request('query') }}"
                class="w-full bg-zinc-100 border-2 border-transparent text-zinc-800 text-sm font-medium rounded-2xl focus:bg-white focus:border-blue-600 focus:ring-4 focus:ring-blue-600/10 block pl-6 pr-14 py-3 transition-all outline-none placeholder:text-zinc-400 shadow-inner group-focus-within:shadow-lg group-focus-within:shadow-blue-600/5"
            >
            {{-- Tombol Kaca Pembesar (Submit / Enter) --}}
            <button type="submit" class="absolute right-2 top-1/2 transform -translate-y-1/2 flex items-center justify-center w-9 h-9 rounded-xl bg-blue-600 text-white hover:bg-blue-700 hover:scale-105 transition-all duration-300 shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-600/50 cursor-pointer">
                <i class="fas fa-search"></i>
            </button>
        </form>

        {{-- KANAN: Menu & Keranjang --}}
        <nav class="flex items-center gap-3 lg:gap-6 shrink-0">
            <ul class="hidden lg:flex items-center gap-6">
                <li><a href="{{ route('produk.index') }}" class="text-sm font-bold text-zinc-500 hover:text-black transition-colors">Katalog</a></li>
                <li><a href="{{ route('toko.index') }}" class="text-sm font-bold text-zinc-500 hover:text-black transition-colors">Mitra</a></li>
            </ul>

            <div class="w-[2px] h-6 bg-zinc-200 hidden lg:block rounded-full"></div>

            <button class="md:hidden w-10 h-10 flex items-center justify-center rounded-2xl bg-zinc-50 text-zinc-600">
                <i class="fas fa-search"></i>
            </button>

            <a href="{{ route('keranjang.index') }}" class="relative w-12 h-12 flex items-center justify-center rounded-2xl bg-zinc-50 border border-zinc-200 text-zinc-700 hover:bg-blue-600 hover:border-blue-600 hover:text-white transition-all duration-300 shadow-sm group">
                <i class="fas fa-shopping-cart text-lg group-hover:-translate-y-0.5 transition-transform"></i>
                @if(isset($total_item_keranjang) && $total_item_keranjang > 0)
                    <span class="absolute -top-1.5 -right-1.5 bg-black text-white text-[10px] font-black px-2 py-1 rounded-full border-[3px] border-white shadow-md transform group-hover:scale-110 transition-transform">
                        {{ $total_item_keranjang }}
                    </span>
                @else
                    <span class="absolute top-3 right-3 w-2 h-2 bg-blue-500 rounded-full border-2 border-white opacity-0 group-hover:opacity-100 transition-opacity"></span>
                @endif
            </a>

            @auth
            <a href="{{ route('profil.index') }}" class="hidden md:flex items-center gap-3 p-1.5 pr-4 rounded-full bg-zinc-50 border border-zinc-200 hover:bg-black hover:text-white transition-all group">
                {{-- FIX AVATAR NAVBAR ATAS --}}
                @if(Auth::user()->profile_picture_url)
                    <img src="{{ asset('assets/uploads/avatars/' . Auth::user()->profile_picture_url) }}" class="w-8 h-8 rounded-full object-cover shadow-inner" onerror="this.src='{{ asset('assets/uploads/avatars/person-icon-1680.png') }}'">
                @else
                    <div class="w-8 h-8 rounded-full bg-blue-600 text-white flex items-center justify-center text-xs font-black shadow-inner">
                        {{ strtoupper(substr(Auth::user()->nama, 0, 1)) }}
                    </div>
                @endif
                <span class="text-xs font-bold text-zinc-700 group-hover:text-white transition-colors truncate max-w-[80px]">
                    {{ explode(' ', Auth::user()->nama)[0] }}
                </span>
            </a>
            @else
            <a href="{{ route('login') }}" class="hidden md:flex items-center gap-2 px-5 py-2.5 rounded-2xl bg-black text-white text-sm font-bold hover:bg-blue-600 hover:shadow-lg hover:shadow-blue-600/30 transition-all duration-300">
                Masuk <i class="fas fa-arrow-right text-xs"></i>
            </a>
            @endauth
        </nav>
    </div>
</header>

{{-- ========================================================
     COMMAND CENTER SIDEBAR (DARK MODE)
     ======================================================== --}}
<div id="sidebar-overlay" class="fixed inset-0 bg-zinc-950/60 backdrop-blur-md z-50 opacity-0 invisible transition-all duration-500"></div>

<div id="modern-sidebar" class="fixed top-0 left-0 h-screen w-[85%] max-w-[360px] bg-[#09090b] z-[60] shadow-[30px_0_60px_rgba(0,0,0,0.5)] transform -translate-x-[130%] transition-transform duration-500 ease-[cubic-bezier(0.87,0,0.13,1)] flex flex-col border-r border-zinc-800">

    <div class="p-6 border-b border-zinc-800 bg-zinc-900/50 flex justify-between items-start relative overflow-hidden">
        <div class="absolute -top-10 -right-10 w-32 h-32 bg-blue-600/20 rounded-full blur-[40px]"></div>

        @auth
            <div class="flex items-center gap-4 relative z-10">
                {{-- FIX AVATAR SIDEBAR KIRI --}}
                @if(Auth::user()->profile_picture_url)
                    <img src="{{ asset('assets/uploads/avatars/' . Auth::user()->profile_picture_url) }}" class="w-14 h-14 rounded-2xl object-cover shadow-[0_0_20px_rgba(37,99,235,0.4)] border border-blue-400/30" onerror="this.src='{{ asset('assets/uploads/avatars/person-icon-1680.png') }}'">
                @else
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-blue-600 to-blue-800 text-white flex items-center justify-center text-2xl font-black shadow-[0_0_20px_rgba(37,99,235,0.4)] border border-blue-400/30">
                        {{ strtoupper(substr(Auth::user()->nama, 0, 1)) }}
                    </div>
                @endif
                <div class="flex flex-col">
                    <span class="text-[10px] font-black tracking-widest text-zinc-500 uppercase">Selamat Datang</span>
                    <h4 class="text-lg font-black text-white mt-0.5 line-clamp-1">{{ Auth::user()->nama }}</h4>
                    <div class="inline-flex items-center gap-1.5 bg-zinc-800/80 border border-zinc-700 px-2 py-0.5 rounded-md mt-1.5 w-max">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span class="text-[10px] font-bold text-zinc-300 uppercase">{{ Auth::user()->level }}</span>
                    </div>
                </div>
            </div>
        @else
            <div class="flex items-center gap-4 relative z-10">
                <div class="w-14 h-14 rounded-2xl bg-zinc-800 border border-zinc-700 text-zinc-500 flex items-center justify-center text-xl">
                    <i class="fas fa-user-lock"></i>
                </div>
                <div>
                    <h4 class="text-base font-black text-white">Tamu POTA</h4>
                    <a href="{{ route('login') }}" class="text-xs font-bold text-blue-500 hover:text-blue-400 mt-1 inline-block transition-colors">Sign In / Register &rarr;</a>
                </div>
            </div>
        @endauth

        <button id="btn-close-sidebar" class="relative z-10 text-zinc-500 hover:text-white w-8 h-8 rounded-full flex items-center justify-center bg-zinc-800/50 hover:bg-red-500 transition-all border border-zinc-700 hover:border-red-500">
            <i class="fas fa-times"></i>
        </button>
    </div>

    <div class="flex-1 overflow-y-auto px-4 py-8 scrollbar-thin scrollbar-thumb-zinc-700 scrollbar-track-transparent">
        <div class="mb-8">
            <h5 class="text-[10px] font-black text-zinc-600 uppercase tracking-[0.2em] mb-4 pl-4">Eksplorasi</h5>
            <ul class="space-y-1.5">
                <li>
                    <a href="{{ url('/') }}" class="flex items-center gap-4 px-4 py-3 rounded-2xl text-zinc-400 font-semibold hover:bg-blue-600/10 hover:text-white transition-all group border border-transparent hover:border-blue-500/20">
                        <div class="w-9 h-9 rounded-xl bg-zinc-800/50 group-hover:bg-blue-600 text-zinc-500 group-hover:text-white flex items-center justify-center transition-all shadow-sm group-hover:shadow-[0_0_15px_rgba(37,99,235,0.4)]">
                            <i class="fas fa-home"></i>
                        </div>
                        Beranda Utama
                    </a>
                </li>
                <li>
                    <a href="{{ route('produk.index') }}" class="flex items-center gap-4 px-4 py-3 rounded-2xl text-zinc-400 font-semibold hover:bg-blue-600/10 hover:text-white transition-all group border border-transparent hover:border-blue-500/20">
                        <div class="w-9 h-9 rounded-xl bg-zinc-800/50 group-hover:bg-blue-600 text-zinc-500 group-hover:text-white flex items-center justify-center transition-all shadow-sm group-hover:shadow-[0_0_15px_rgba(37,99,235,0.4)]">
                            <i class="fas fa-layer-group"></i>
                        </div>
                        Katalog Material
                    </a>
                </li>
                <li>
                    <a href="{{ route('toko.index') }}" class="flex items-center gap-4 px-4 py-3 rounded-2xl text-zinc-400 font-semibold hover:bg-blue-600/10 hover:text-white transition-all group border border-transparent hover:border-blue-500/20">
                        <div class="w-9 h-9 rounded-xl bg-zinc-800/50 group-hover:bg-blue-600 text-zinc-500 group-hover:text-white flex items-center justify-center transition-all shadow-sm group-hover:shadow-[0_0_15px_rgba(37,99,235,0.4)]">
                            <i class="fas fa-store"></i>
                        </div>
                        Direktori Mitra
                    </a>
                </li>
            </ul>
        </div>

        @auth
            <div>
                <h5 class="text-[10px] font-black text-zinc-600 uppercase tracking-[0.2em] mb-4 pl-4">Manajemen Akun</h5>
                <ul class="space-y-1.5">
                    <li>
                        <a href="{{ route('profil.index') }}" class="flex items-center gap-4 px-4 py-3 rounded-2xl text-zinc-400 font-semibold hover:bg-zinc-800 hover:text-white transition-all group">
                            <div class="w-9 h-9 rounded-xl bg-zinc-900 border border-zinc-800 group-hover:border-zinc-600 text-zinc-500 group-hover:text-white flex items-center justify-center transition-all">
                                <i class="fas fa-id-badge"></i>
                            </div>
                            Profil Saya
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('pesanan.index') }}" class="flex items-center gap-4 px-4 py-3 rounded-2xl text-zinc-400 font-semibold hover:bg-zinc-800 hover:text-white transition-all group relative overflow-hidden">
                            <div class="w-9 h-9 rounded-xl bg-zinc-900 border border-zinc-800 group-hover:border-zinc-600 text-zinc-500 group-hover:text-white flex items-center justify-center transition-all">
                                <i class="fas fa-receipt"></i>
                            </div>
                            Riwayat Transaksi
                            <span class="absolute right-4 bg-blue-600 text-white text-[9px] font-black px-2 py-0.5 rounded-full">BARU</span>
                        </a>
                    </li>

                    @if(Auth::user()->level === 'admin')
                        <li class="mt-6 pt-6 border-t border-zinc-800">
                            <a href="{{ url('/app_admin/dashboard_mimin.php') }}" class="flex items-center gap-4 px-4 py-3.5 rounded-2xl text-white font-bold bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 transition-all shadow-[0_10px_20px_rgba(37,99,235,0.2)] group">
                                <div class="w-9 h-9 rounded-xl bg-black/20 text-white flex items-center justify-center">
                                    <i class="fas fa-bolt group-hover:scale-110 transition-transform"></i>
                                </div>
                                Admin Console
                            </a>
                        </li>
                    @elseif(Auth::user()->level === 'seller')
                        <li class="mt-6 pt-6 border-t border-zinc-800">
                            <a href="{{ route('seller.dashboard') }}" class="flex items-center gap-4 px-4 py-3.5 rounded-2xl text-black font-bold bg-gradient-to-r from-white to-zinc-200 hover:from-white hover:to-white transition-all shadow-[0_10px_20px_rgba(255,255,255,0.1)] group">
                                <div class="w-9 h-9 rounded-xl bg-black text-white flex items-center justify-center">
                                    <i class="fas fa-store group-hover:scale-110 transition-transform"></i>
                                </div>
                                Toko Saya
                            </a>
                        </li>
                    @endif
                </ul>
            </div>
        @endauth
    </div>

    
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const btnOpen = document.getElementById('btn-open-sidebar');
        const btnClose = document.getElementById('btn-close-sidebar');
        const sidebar = document.getElementById('modern-sidebar');
        const overlay = document.getElementById('sidebar-overlay');

        function openSidebar(e) {
            e.preventDefault();
            sidebar.classList.remove('-translate-x-[130%]');
            overlay.classList.remove('opacity-0', 'invisible');
            overlay.classList.add('opacity-100', 'visible');
            document.body.style.overflow = 'hidden';
        }

        function closeSidebar() {
            sidebar.classList.add('-translate-x-[130%]');
            overlay.classList.remove('opacity-100', 'visible');
            overlay.classList.add('opacity-0', 'invisible');
            document.body.style.overflow = '';
        }

        if(btnOpen) btnOpen.addEventListener('click', openSidebar);
        if(btnClose) btnClose.addEventListener('click', closeSidebar);
        if(overlay) overlay.addEventListener('click', closeSidebar);

        // Keyboard Shortcut (Ctrl/Cmd + K) untuk Search
        const searchInput = document.getElementById('search-input');
        document.addEventListener('keydown', function(e) {
            if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                e.preventDefault();
                if(searchInput) searchInput.focus();
            }
        });
    });
</script>