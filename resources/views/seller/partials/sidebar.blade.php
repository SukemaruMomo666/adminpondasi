<aside class="w-[260px] h-screen bg-slate-900 border-r border-slate-800 flex flex-col fixed left-0 top-0 z-50 transition-transform duration-300" id="sidebar">

    {{-- 1. BRAND LOGO --}}
    <div class="px-6 py-5 border-b border-slate-800 flex-shrink-0">
        <a href="{{ route('home') }}" class="flex items-center gap-3 group" title="Lihat Tampilan Toko">
            {{-- Aksen biru terang untuk logo di dark mode --}}
            <div class="w-9 h-9 bg-blue-600 rounded-xl flex items-center justify-center text-white shadow-lg shadow-blue-900/50 group-hover:scale-105 transition-transform">
                <i class="mdi mdi-storefront text-xl"></i>
            </div>
            <div>
                <h2 class="text-xl font-black text-white tracking-tight leading-none">Pondasikita</h2>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Seller Center</span>
            </div>
        </a>
    </div>

    {{-- 2. USER PROFILE CARD --}}
    <div class="px-6 py-4 border-b border-slate-800 flex-shrink-0">
        <div class="flex items-center gap-3 p-2 rounded-xl hover:bg-slate-800 transition-colors cursor-pointer border border-transparent hover:border-slate-700">
            <div class="w-10 h-10 rounded-full bg-slate-700 flex items-center justify-center font-bold text-white shadow-sm">
                {{ strtoupper(substr(Auth::user()->nama ?? 'S', 0, 1)) }}
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-[10px] font-bold text-slate-400 uppercase">Selamat Datang,</p>
                <p class="text-sm font-black text-slate-100 truncate" title="{{ Auth::user()->nama ?? 'Seller' }}">
                    {{ Str::limit(Auth::user()->nama ?? 'Seller', 15) }}
                </p>
            </div>
        </div>
    </div>

    {{-- 3. NAVIGATION MENU --}}
    <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-1 custom-scrollbar-dark">

        {{-- DASHBOARD --}}
        @php $isDashboard = request()->routeIs('seller.dashboard'); @endphp
        <a href="{{ route('seller.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-bold transition-all {{ $isDashboard ? 'bg-blue-600 text-white shadow-md shadow-blue-900/50' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
            <i class="mdi mdi-view-dashboard text-xl {{ $isDashboard ? 'text-white' : 'text-slate-500' }}"></i>
            <span>Dashboard</span>
        </a>

        {{-- POS --}}
        @php $isPos = request()->routeIs('seller.pos.index'); @endphp
        <a href="{{ route('seller.pos.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-bold transition-all {{ $isPos ? 'bg-blue-600 text-white shadow-md shadow-blue-900/50' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
            <i class="mdi mdi-point-of-sale text-xl {{ $isPos ? 'text-white' : 'text-slate-500' }}"></i>
            <span>Point of Sale</span>
        </a>

        {{-- LABEL: MANAJEMEN PENJUALAN --}}
        <div class="px-3 pt-5 pb-2 text-[10px] font-black text-slate-500 uppercase tracking-widest">Manajemen Penjualan</div>

        {{-- PESANAN (DROPDOWN) --}}
        @php
            $isPenjualanOpen = request()->routeIs('seller.orders.*') || request()->routeIs('seller.pengaturan.pengiriman');
        @endphp
        <div>
            <button onclick="toggleSidebarMenu(this)" class="w-full flex items-center justify-between px-3 py-2.5 rounded-xl text-sm font-bold transition-all {{ $isPenjualanOpen ? 'bg-slate-800 text-white' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                <div class="flex items-center gap-3">
                    <i class="mdi mdi-package text-xl {{ $isPenjualanOpen ? 'text-blue-500' : 'text-slate-500' }}"></i>
                    <span>Pesanan</span>
                </div>
                <i class="mdi mdi-chevron-down text-lg transition-transform duration-300 nav-chevron {{ $isPenjualanOpen ? 'rotate-180 text-blue-500' : 'text-slate-500' }}"></i>
            </button>
            <div class="grid transition-all duration-300 ease-in-out nav-content {{ $isPenjualanOpen ? 'grid-rows-[1fr] opacity-100 mt-1' : 'grid-rows-[0fr] opacity-0' }}">
                <div class="overflow-hidden">
                    <ul class="pl-11 pr-2 py-1 space-y-1 relative before:absolute before:left-6 before:top-0 before:bottom-0 before:w-px before:bg-slate-700">
                        @php $routesPesanan = [
                            ['route' => 'seller.orders.index', 'label' => 'Daftar Pesanan'],
                            ['route' => 'seller.orders.return', 'label' => 'Pengembalian'],
                            ['route' => 'seller.pengaturan.pengiriman', 'label' => 'Pengiriman']
                        ]; @endphp
                        @foreach($routesPesanan as $r)
                        <li>
                            <a href="{{ route($r['route']) }}" class="block px-3 py-2 rounded-lg text-xs font-bold transition-all {{ request()->routeIs($r['route']) ? 'text-white bg-slate-800 relative before:absolute before:-left-5 before:top-1/2 before:-translate-y-1/2 before:w-2 before:h-2 before:bg-blue-500 before:rounded-full' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">
                                {{ $r['label'] }}
                            </a>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>

        {{-- LABEL: PRODUK --}}
        <div class="px-3 pt-5 pb-2 text-[10px] font-black text-slate-500 uppercase tracking-widest">Katalog</div>

        {{-- PRODUK (DROPDOWN) --}}
        @php $isProductOpen = request()->routeIs('seller.products.*'); @endphp
        <div>
            <button onclick="toggleSidebarMenu(this)" class="w-full flex items-center justify-between px-3 py-2.5 rounded-xl text-sm font-bold transition-all {{ $isProductOpen ? 'bg-slate-800 text-white' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                <div class="flex items-center gap-3">
                    <i class="mdi mdi-cube-unfolded text-xl {{ $isProductOpen ? 'text-blue-500' : 'text-slate-500' }}"></i>
                    <span>Produk</span>
                </div>
                <i class="mdi mdi-chevron-down text-lg transition-transform duration-300 nav-chevron {{ $isProductOpen ? 'rotate-180 text-blue-500' : 'text-slate-500' }}"></i>
            </button>
            <div class="grid transition-all duration-300 ease-in-out nav-content {{ $isProductOpen ? 'grid-rows-[1fr] opacity-100 mt-1' : 'grid-rows-[0fr] opacity-0' }}">
                <div class="overflow-hidden">
                    <ul class="pl-11 pr-2 py-1 space-y-1 relative before:absolute before:left-6 before:top-0 before:bottom-0 before:w-px before:bg-slate-700">
                        <li><a href="{{ route('seller.products.index') }}" class="block px-3 py-2 rounded-lg text-xs font-bold transition-all {{ request()->routeIs('seller.products.index') ? 'text-white bg-slate-800 relative before:absolute before:-left-5 before:top-1/2 before:-translate-y-1/2 before:w-2 before:h-2 before:bg-blue-500 before:rounded-full' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">Produk Saya</a></li>
                        <li><a href="{{ route('seller.products.create') }}" class="block px-3 py-2 rounded-lg text-xs font-bold transition-all {{ request()->routeIs('seller.products.create') ? 'text-white bg-slate-800 relative before:absolute before:-left-5 before:top-1/2 before:-translate-y-1/2 before:w-2 before:h-2 before:bg-blue-500 before:rounded-full' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">Tambah Produk</a></li>
                    </ul>
                </div>
            </div>
        </div>

        {{-- PROMOSI (DROPDOWN) --}}
        @php $isPromoOpen = request()->routeIs('seller.promotion.*'); @endphp
        <div>
            <button onclick="toggleSidebarMenu(this)" class="w-full flex items-center justify-between px-3 py-2.5 rounded-xl text-sm font-bold transition-all {{ $isPromoOpen ? 'bg-slate-800 text-white' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                <div class="flex items-center gap-3">
                    <i class="mdi mdi-ticket-percent text-xl {{ $isPromoOpen ? 'text-blue-500' : 'text-slate-500' }}"></i>
                    <span>Pusat Promosi</span>
                </div>
                <i class="mdi mdi-chevron-down text-lg transition-transform duration-300 nav-chevron {{ $isPromoOpen ? 'rotate-180 text-blue-500' : 'text-slate-500' }}"></i>
            </button>
            <div class="grid transition-all duration-300 ease-in-out nav-content {{ $isPromoOpen ? 'grid-rows-[1fr] opacity-100 mt-1' : 'grid-rows-[0fr] opacity-0' }}">
                <div class="overflow-hidden">
                    <ul class="pl-11 pr-2 py-1 space-y-1 relative before:absolute before:left-6 before:top-0 before:bottom-0 before:w-px before:bg-slate-700">
                        <li><a href="{{ route('seller.promotion.discounts') }}" class="block px-3 py-2 rounded-lg text-xs font-bold transition-all {{ request()->routeIs('seller.promotion.discounts') ? 'text-white bg-slate-800 relative before:absolute before:-left-5 before:top-1/2 before:-translate-y-1/2 before:w-2 before:h-2 before:bg-blue-500 before:rounded-full' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">Diskon Produk</a></li>
                        <li><a href="{{ route('seller.promotion.vouchers') }}" class="block px-3 py-2 rounded-lg text-xs font-bold transition-all {{ request()->routeIs('seller.promotion.vouchers') ? 'text-white bg-slate-800 relative before:absolute before:-left-5 before:top-1/2 before:-translate-y-1/2 before:w-2 before:h-2 before:bg-blue-500 before:rounded-full' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">Voucher Toko</a></li>
                    </ul>
                </div>
            </div>
        </div>

        {{-- LABEL: OPERASIONAL --}}
        <div class="px-3 pt-5 pb-2 text-[10px] font-black text-slate-500 uppercase tracking-widest">Operasional</div>

        {{-- LAYANAN PEMBELI --}}
        @php $isServiceOpen = request()->routeIs('seller.service.*'); @endphp
        <div>
            <button onclick="toggleSidebarMenu(this)" class="w-full flex items-center justify-between px-3 py-2.5 rounded-xl text-sm font-bold transition-all {{ $isServiceOpen ? 'bg-slate-800 text-white' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                <div class="flex items-center gap-3">
                    <i class="mdi mdi-headset-mic text-xl {{ $isServiceOpen ? 'text-blue-500' : 'text-slate-500' }}"></i>
                    <span>Layanan Pembeli</span>
                </div>
                <i class="mdi mdi-chevron-down text-lg transition-transform duration-300 nav-chevron {{ $isServiceOpen ? 'rotate-180 text-blue-500' : 'text-slate-500' }}"></i>
            </button>
            <div class="grid transition-all duration-300 ease-in-out nav-content {{ $isServiceOpen ? 'grid-rows-[1fr] opacity-100 mt-1' : 'grid-rows-[0fr] opacity-0' }}">
                <div class="overflow-hidden">
                    <ul class="pl-11 pr-2 py-1 space-y-1 relative before:absolute before:left-6 before:top-0 before:bottom-0 before:w-px before:bg-slate-700">
                        <li><a href="{{ route('seller.service.chat') }}" class="block px-3 py-2 rounded-lg text-xs font-bold transition-all {{ request()->routeIs('seller.service.chat') ? 'text-white bg-slate-800 relative before:absolute before:-left-5 before:top-1/2 before:-translate-y-1/2 before:w-2 before:h-2 before:bg-blue-500 before:rounded-full' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">Manajemen Chat</a></li>
                        <li><a href="{{ route('seller.service.reviews') }}" class="block px-3 py-2 rounded-lg text-xs font-bold transition-all {{ request()->routeIs('seller.service.reviews') ? 'text-white bg-slate-800 relative before:absolute before:-left-5 before:top-1/2 before:-translate-y-1/2 before:w-2 before:h-2 before:bg-blue-500 before:rounded-full' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">Penilaian Toko</a></li>
                    </ul>
                </div>
            </div>
        </div>

        {{-- KEUANGAN --}}
        @php $isFinanceOpen = request()->routeIs('seller.finance.*'); @endphp
        <div>
            <button onclick="toggleSidebarMenu(this)" class="w-full flex items-center justify-between px-3 py-2.5 rounded-xl text-sm font-bold transition-all {{ $isFinanceOpen ? 'bg-slate-800 text-white' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                <div class="flex items-center gap-3">
                    <i class="mdi mdi-currency-usd text-xl {{ $isFinanceOpen ? 'text-blue-500' : 'text-slate-500' }}"></i>
                    <span>Keuangan</span>
                </div>
                <i class="mdi mdi-chevron-down text-lg transition-transform duration-300 nav-chevron {{ $isFinanceOpen ? 'rotate-180 text-blue-500' : 'text-slate-500' }}"></i>
            </button>
            <div class="grid transition-all duration-300 ease-in-out nav-content {{ $isFinanceOpen ? 'grid-rows-[1fr] opacity-100 mt-1' : 'grid-rows-[0fr] opacity-0' }}">
                <div class="overflow-hidden">
                    <ul class="pl-11 pr-2 py-1 space-y-1 relative before:absolute before:left-6 before:top-0 before:bottom-0 before:w-px before:bg-slate-700">
                        <li><a href="{{ route('seller.finance.income') }}" class="block px-3 py-2 rounded-lg text-xs font-bold transition-all {{ request()->routeIs('seller.finance.income') ? 'text-white bg-slate-800 relative before:absolute before:-left-5 before:top-1/2 before:-translate-y-1/2 before:w-2 before:h-2 before:bg-blue-500 before:rounded-full' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">Penghasilan Toko</a></li>
                        <li><a href="{{ route('seller.finance.bank') }}" class="block px-3 py-2 rounded-lg text-xs font-bold transition-all {{ request()->routeIs('seller.finance.bank') ? 'text-white bg-slate-800 relative before:absolute before:-left-5 before:top-1/2 before:-translate-y-1/2 before:w-2 before:h-2 before:bg-blue-500 before:rounded-full' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">Rekening Bank</a></li>
                    </ul>
                </div>
            </div>
        </div>

        {{-- DATA & TOKO --}}
        @php $isDataOpen = request()->routeIs('seller.data.*'); @endphp
        <div>
            <button onclick="toggleSidebarMenu(this)" class="w-full flex items-center justify-between px-3 py-2.5 rounded-xl text-sm font-bold transition-all {{ $isDataOpen ? 'bg-slate-800 text-white' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                <div class="flex items-center gap-3">
                    <i class="mdi mdi-chart-bar text-xl {{ $isDataOpen ? 'text-blue-500' : 'text-slate-500' }}"></i>
                    <span>Data Analitik</span>
                </div>
                <i class="mdi mdi-chevron-down text-lg transition-transform duration-300 nav-chevron {{ $isDataOpen ? 'rotate-180 text-blue-500' : 'text-slate-500' }}"></i>
            </button>
            <div class="grid transition-all duration-300 ease-in-out nav-content {{ $isDataOpen ? 'grid-rows-[1fr] opacity-100 mt-1' : 'grid-rows-[0fr] opacity-0' }}">
                <div class="overflow-hidden">
                    <ul class="pl-11 pr-2 py-1 space-y-1 relative before:absolute before:left-6 before:top-0 before:bottom-0 before:w-px before:bg-slate-700">
                        <li><a href="{{ route('seller.data.performance') }}" class="block px-3 py-2 rounded-lg text-xs font-bold transition-all {{ request()->routeIs('seller.data.performance') ? 'text-white bg-slate-800 relative before:absolute before:-left-5 before:top-1/2 before:-translate-y-1/2 before:w-2 before:h-2 before:bg-blue-500 before:rounded-full' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">Performa Toko</a></li>
                        <li><a href="{{ route('seller.data.health') }}" class="block px-3 py-2 rounded-lg text-xs font-bold transition-all {{ request()->routeIs('seller.data.health') ? 'text-white bg-slate-800 relative before:absolute before:-left-5 before:top-1/2 before:-translate-y-1/2 before:w-2 before:h-2 before:bg-blue-500 before:rounded-full' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">Kesehatan Toko</a></li>
                    </ul>
                </div>
            </div>
        </div>

        @php $isShopOpen = request()->routeIs('seller.shop.*'); @endphp
        <div>
            <button onclick="toggleSidebarMenu(this)" class="w-full flex items-center justify-between px-3 py-2.5 rounded-xl text-sm font-bold transition-all {{ $isShopOpen ? 'bg-slate-800 text-white' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                <div class="flex items-center gap-3">
                    <i class="mdi mdi-store text-xl {{ $isShopOpen ? 'text-blue-500' : 'text-slate-500' }}"></i>
                    <span>Pengaturan Toko</span>
                </div>
                <i class="mdi mdi-chevron-down text-lg transition-transform duration-300 nav-chevron {{ $isShopOpen ? 'rotate-180 text-blue-500' : 'text-slate-500' }}"></i>
            </button>
            <div class="grid transition-all duration-300 ease-in-out nav-content {{ $isShopOpen ? 'grid-rows-[1fr] opacity-100 mt-1' : 'grid-rows-[0fr] opacity-0' }}">
                <div class="overflow-hidden">
                    <ul class="pl-11 pr-2 py-1 space-y-1 relative before:absolute before:left-6 before:top-0 before:bottom-0 before:w-px before:bg-slate-700">
                        <li><a href="{{ route('seller.shop.profile') }}" class="block px-3 py-2 rounded-lg text-xs font-bold transition-all {{ request()->routeIs('seller.shop.profile') ? 'text-white bg-slate-800 relative before:absolute before:-left-5 before:top-1/2 before:-translate-y-1/2 before:w-2 before:h-2 before:bg-blue-500 before:rounded-full' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">Profil Toko</a></li>
                        <li><a href="{{ route('seller.shop.tier') }}" class="block px-3 py-2 rounded-lg text-xs font-bold transition-all {{ request()->routeIs('seller.shop.tier') ? 'text-white bg-slate-800 relative before:absolute before:-left-5 before:top-1/2 before:-translate-y-1/2 before:w-2 before:h-2 before:bg-amber-500 before:rounded-full' : 'text-slate-400 hover:text-white hover:bg-slate-800' }} d-flex justify-content-between align-items-center"><span>Kenaikan Level</span> <i class="mdi mdi-rocket-launch text-amber-500"></i></a></li>
                        <li><a href="{{ route('seller.shop.decoration') }}" class="block px-3 py-2 rounded-lg text-xs font-bold transition-all {{ request()->routeIs('seller.shop.decoration') ? 'text-white bg-slate-800 relative before:absolute before:-left-5 before:top-1/2 before:-translate-y-1/2 before:w-2 before:h-2 before:bg-blue-500 before:rounded-full' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">Dekorasi Toko</a></li>
                        <li><a href="{{ route('seller.shop.settings') }}" class="block px-3 py-2 rounded-lg text-xs font-bold transition-all {{ request()->routeIs('seller.shop.settings') ? 'text-white bg-slate-800 relative before:absolute before:-left-5 before:top-1/2 before:-translate-y-1/2 before:w-2 before:h-2 before:bg-blue-500 before:rounded-full' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">Pengaturan</a></li>
                    </ul>
                </div>
            </div>
        </div>

    </nav>

    {{-- 5. FOOTER (LOGOUT) --}}
    <div class="p-4 border-t border-slate-800 flex-shrink-0">
        <form action="{{ route('logout') }}" method="POST" id="sidebar-logout-form">
            @csrf
            {{-- Tombol Keluar --}}
            <button type="button" onclick="document.getElementById('sidebar-logout-form').submit();" class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-slate-800/50 text-slate-300 hover:bg-red-500/10 hover:text-red-400 hover:border-red-500/20 border border-transparent rounded-xl text-sm font-bold transition-colors">
                <i class="mdi mdi-logout text-lg"></i>
                <span>Keluar</span>
            </button>
        </form>
    </div>
</aside>

{{-- SCRIPT UNTUK MENU DROPDOWN --}}
<script>
    function toggleSidebarMenu(button) {
        const content = button.nextElementSibling;
        const chevron = button.querySelector('.nav-chevron');

        if (content.classList.contains('grid-rows-[1fr]')) {
            // Tutup Menu
            content.classList.replace('grid-rows-[1fr]', 'grid-rows-[0fr]');
            content.classList.replace('opacity-100', 'opacity-0');
            content.classList.remove('mt-1');
            chevron.classList.remove('rotate-180', 'text-blue-500');
            button.classList.remove('bg-slate-800', 'text-white');
            button.classList.add('text-slate-400');
        } else {
            // Buka Menu
            content.classList.replace('grid-rows-[0fr]', 'grid-rows-[1fr]');
            content.classList.replace('opacity-0', 'opacity-100');
            content.classList.add('mt-1');
            chevron.classList.add('rotate-180', 'text-blue-500');
            button.classList.add('bg-slate-800', 'text-white');
            button.classList.remove('text-slate-400');
        }
    }
</script>

{{-- CSS UNTUK CUSTOM SCROLLBAR --}}
<style>
    .custom-scrollbar-dark::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar-dark::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar-dark::-webkit-scrollbar-thumb { background: #334155; border-radius: 10px; }
    .custom-scrollbar-dark:hover::-webkit-scrollbar-thumb { background: #475569; }
</style>
