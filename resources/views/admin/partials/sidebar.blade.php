{{-- ==============================================================================
     GOD-TIER ADMIN SIDEBAR (resources/views/admin/partials/sidebar.blade.php)
     Dilengkapi Tema Dinamis, Glassmorphism & True Black OLED Aesthetic (Bug-Free)
     ============================================================================== --}}

@php
    // Ambil role admin yang sedang login
    $adminRole = Auth::user()->admin_role ?? 'super';
@endphp

<div class="flex flex-col h-full w-full bg-transparent text-slate-700 dark:text-slate-300 relative overflow-hidden transition-colors duration-500">

    {{-- Latar Belakang Efek Glow Ambient (Sangat halus untuk OLED) --}}
    <div class="absolute top-0 left-0 w-full h-64 bg-gradient-to-b from-blue-500/5 to-transparent pointer-events-none z-0 hidden dark:block"></div>

    {{-- ==========================================
         HEADER / BRANDING
         ========================================== --}}
    <div class="h-[70px] flex items-center px-6 border-b border-slate-200/60 dark:border-slate-800 relative z-20 flex-shrink-0 transition-colors duration-500">
        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 outline-none group w-full text-decoration-none">
            <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-indigo-600 to-violet-700 flex items-center justify-center shadow-lg shadow-indigo-500/20 dark:shadow-[0_0_20px_rgba(99,102,241,0.2)] group-hover:scale-105 group-hover:shadow-[0_0_30px_rgba(99,102,241,0.4)] ring-1 ring-white/10 transition-all duration-300">
                <span class="text-white font-black text-lg font-mono">P</span>
            </div>
            <div class="flex flex-col">
                <span class="text-slate-900 dark:text-white font-black text-base tracking-tight leading-tight group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">Pondasikita</span>
                <span class="text-[9px] font-bold text-slate-500 dark:text-slate-500 uppercase tracking-[0.25em] leading-tight opacity-80">Core Dashboard</span>
            </div>
        </a>
    </div>

    {{-- ==========================================
         PROFILE CARD
         ========================================== --}}
    <div class="p-5 border-b border-slate-200/60 dark:border-slate-800 relative z-20 flex-shrink-0 bg-slate-50/30 dark:bg-transparent transition-colors duration-500">
        <div class="flex items-center gap-3 group">
            <div class="w-11 h-11 rounded-full bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 flex items-center justify-center text-indigo-600 dark:text-indigo-400 font-black shadow-sm dark:shadow-none relative group-hover:scale-105 transition-all duration-300">
                {{ strtoupper(substr(Auth::user()->nama ?? 'A', 0, 1)) }}
                {{-- Indikator Online --}}
                <span class="absolute bottom-0.5 right-0.5 w-2.5 h-2.5 bg-emerald-500 border-2 border-white dark:border-slate-800 rounded-full shadow-[0_0_10px_rgba(16,185,129,0.5)]"></span>
            </div>
            <div class="flex flex-col flex-1 overflow-hidden">
                <span class="text-sm font-black text-slate-800 dark:text-white truncate transition-colors duration-300">{{ Auth::user()->nama ?? 'Administrator' }}</span>

                {{-- Label Role Dinamis --}}
                <span class="text-[9px] font-black tracking-widest uppercase mt-0.5 flex items-center gap-1.5
                    @if($adminRole == 'super') text-amber-500/80
                    @elseif($adminRole == 'finance') text-emerald-500/80
                    @else text-indigo-500/80 @endif transition-colors duration-300">

                    @if($adminRole == 'super')
                        <i class="mdi mdi-shield-crown text-sm"></i> Master Admin
                    @elseif($adminRole == 'finance')
                        <i class="mdi mdi-cash-lock text-sm"></i> Financial
                    @else
                        <i class="mdi mdi-headset text-sm"></i> Support
                    @endif
                </span>
            </div>
        </div>
    </div>

    {{-- ==========================================
         MAIN NAVIGATION (SCROLLABLE)
         ========================================== --}}
    <div class="flex-1 overflow-y-auto hide-scrollbar py-4 px-3 space-y-1 relative z-20">

        <div class="px-4 pb-2 pt-1">
            <p class="text-[9px] font-black uppercase tracking-[0.2em] text-slate-400 dark:text-slate-600 transition-colors duration-300">Console</p>
        </div>

        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-300 outline-none text-decoration-none group relative overflow-hidden {{ request()->routeIs('admin.dashboard') ? 'bg-indigo-600 dark:bg-indigo-600 text-white font-black shadow-lg shadow-indigo-600/20' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-white/5 hover:text-indigo-600 dark:hover:text-indigo-400 font-bold' }}">
            <i class="mdi mdi-view-dashboard-outline text-xl transition-transform duration-300 group-hover:scale-110 {{ request()->routeIs('admin.dashboard') ? 'text-white' : '' }}"></i>
            <span class="text-sm flex-1">Dashboard</span>
            @if(request()->routeIs('admin.dashboard'))
                <div class="w-1.5 h-1.5 rounded-full bg-white/40 animate-pulse"></div>
            @endif
        </a>

        {{-- ======================================================== --}}
        {{-- AREA MANAJEMEN: HANYA UNTUK SUPER ADMIN & ADMIN CS       --}}
        {{-- ======================================================== --}}
        @if(in_array($adminRole, ['super', 'cs']))
            <div class="px-4 pb-2 pt-5">
                <p class="text-[9px] font-black uppercase tracking-[0.2em] text-slate-400 dark:text-slate-600 transition-colors duration-300">Registry</p>
            </div>

            <a href="{{ route('admin.users.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-300 outline-none text-decoration-none group relative overflow-hidden {{ request()->routeIs('admin.users.index') ? 'bg-indigo-600 dark:bg-indigo-600 text-white font-black shadow-lg shadow-indigo-600/20' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-white/5 hover:text-indigo-600 dark:hover:text-indigo-400 font-bold' }}">
                <i class="mdi mdi-account-group-outline text-xl transition-transform duration-300 group-hover:scale-110 {{ request()->routeIs('admin.users.index') ? 'text-white' : '' }}"></i>
                <span class="text-sm flex-1">Kelola Pengguna</span>
            </a>

            <a href="{{ route('admin.users.appeals') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-300 outline-none text-decoration-none group relative overflow-hidden {{ request()->routeIs('admin.users.appeals') ? 'bg-indigo-600 dark:bg-indigo-600 text-white font-black shadow-lg shadow-indigo-600/20' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-white/5 hover:text-indigo-600 dark:hover:text-indigo-400 font-bold' }}">
                <i class="mdi mdi-gavel text-xl transition-transform duration-300 group-hover:scale-110 {{ request()->routeIs('admin.users.appeals') ? 'text-white' : '' }}"></i>
                <span class="text-sm flex-1">Banding Akun</span>
            </a>

            <a href="{{ route('admin.orders.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-300 outline-none text-decoration-none group relative overflow-hidden {{ request()->routeIs('admin.orders.*') ? 'bg-indigo-600 dark:bg-indigo-600 text-white font-black shadow-lg shadow-indigo-600/20' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-white/5 hover:text-indigo-600 dark:hover:text-indigo-400 font-bold' }}">
                <i class="mdi mdi-monitor-dashboard text-xl transition-transform duration-300 group-hover:scale-110 {{ request()->routeIs('admin.orders.*') ? 'text-white' : '' }}"></i>
                <span class="text-sm flex-1">Pesanan Global</span>
            </a>

            <a href="{{ route('admin.disputes.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-300 outline-none text-decoration-none group relative overflow-hidden {{ request()->routeIs('admin.disputes.*') ? 'bg-indigo-600 dark:bg-indigo-600 text-white font-black shadow-lg shadow-indigo-600/20' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-white/5 hover:text-indigo-600 dark:hover:text-indigo-400 font-bold' }}">
                <i class="mdi mdi-gavel text-xl transition-transform duration-300 group-hover:scale-110 {{ request()->routeIs('admin.disputes.*') ? 'text-white' : 'dark:group-hover:text-indigo-400' }}"></i>
                <span class="text-sm flex-1">Pusat Resolusi</span>
                <span class="relative flex h-2 w-2">
                  <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
                  <span class="relative inline-flex rounded-full h-2 w-2 bg-rose-500 shadow-[0_0_8px_rgba(244,63,94,0.6)]"></span>
                </span>
            </a>

            @php
                $isStoreActive = request()->routeIs('admin.stores.*') || request()->routeIs('admin.products.*');
            @endphp

            <div x-data="{ open: {{ $isStoreActive ? 'true' : 'false' }} }" class="mt-1">
<button @click="open = !open"
        class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-300 outline-none text-decoration-none group relative overflow-hidden border-0 bg-transparent"
        :class="{
            'bg-indigo-600 dark:bg-indigo-600 text-white font-black shadow-lg shadow-indigo-600/20': open || {{ $isStoreActive ? 'true' : 'false' }},
            'text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-white/5 hover:text-indigo-600 dark:hover:text-indigo-400 font-bold': !(open || {{ $isStoreActive ? 'true' : 'false' }})
        }">
                    <i class="mdi mdi-store-outline text-xl transition-transform duration-300 group-hover:scale-110 {{ ($isStoreActive) ? 'text-white' : '' }}"></i>
                    <span class="text-sm flex-1 text-left">Toko & Produk</span>
                    <i class="mdi mdi-chevron-down text-lg transition-transform duration-300" :class="open ? 'rotate-180 text-white' : ''"></i>
                </button>

                {{-- Isi Sub-Menu --}}
                <div x-show="open"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 -translate-y-2"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-collapse
                     class="mt-1 space-y-1 pl-10 pr-2 pb-2">

                    <a href="{{ route('admin.stores.index') }}" class="flex items-center gap-3 py-2 px-3 rounded-lg text-sm font-bold text-decoration-none transition-all duration-300 {{ request()->routeIs('admin.stores.*') ? 'text-indigo-700 dark:text-white bg-white/10' : 'text-slate-500 dark:text-slate-500 hover:text-indigo-600 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-white/5' }}">
                        <span class="w-1 h-1 rounded-full transition-colors duration-300 {{ request()->routeIs('admin.stores.*') ? 'bg-white' : 'bg-slate-400' }}"></span>
                        Kelola Toko
                    </a>

                    <a href="{{ route('admin.products.index') }}" class="flex items-center gap-3 py-2 px-3 rounded-lg text-sm font-bold text-decoration-none transition-all duration-300 {{ request()->routeIs('admin.products.*') ? 'text-indigo-700 dark:text-white bg-white/10' : 'text-slate-500 dark:text-slate-500 hover:text-indigo-600 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-white/5' }}">
                        <span class="w-1 h-1 rounded-full transition-colors duration-300 {{ request()->routeIs('admin.products.*') ? 'bg-white' : 'bg-slate-400' }}"></span>
                        Moderasi Produk
                    </a>
                </div>
            </div>
        @endif

        {{-- ======================================================== --}}
        {{-- AREA KEUANGAN & SISTEM: HANYA SUPER ADMIN & FINANCE      --}}
        {{-- ======================================================== --}}
        @if(in_array($adminRole, ['super', 'finance']))
            <div class="px-4 pb-2 pt-5">
                <p class="text-[9px] font-black uppercase tracking-[0.2em] text-slate-400 dark:text-slate-600 transition-colors duration-300">Financial</p>
            </div>

            <a href="{{ route('admin.payouts.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-300 outline-none text-decoration-none group relative overflow-hidden {{ request()->routeIs('admin.payouts.*') ? 'bg-indigo-600 dark:bg-indigo-600 text-white font-black shadow-lg shadow-indigo-600/20' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-white/5 hover:text-indigo-600 dark:hover:text-indigo-400 font-bold' }}">
                <i class="mdi mdi-wallet-outline text-xl transition-transform duration-300 group-hover:scale-110 {{ request()->routeIs('admin.payouts.*') ? 'text-white' : '' }}"></i>
                <span class="text-sm flex-1">Payout</span>
            </a>

            <a href="{{ route('admin.reports.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-300 outline-none text-decoration-none group relative overflow-hidden {{ request()->routeIs('admin.reports.*') ? 'bg-indigo-600 dark:bg-indigo-600 text-white font-black shadow-lg shadow-indigo-600/20' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-white/5 hover:text-indigo-600 dark:hover:text-indigo-400 font-bold' }}">
                <i class="mdi mdi-file-chart-outline text-xl transition-transform duration-300 group-hover:scale-110 {{ request()->routeIs('admin.reports.*') ? 'text-white' : '' }}"></i>
                <span class="text-sm flex-1">Laporan Global</span>
            </a>

            <div class="px-4 pb-2 pt-5">
                <p class="text-[9px] font-black uppercase tracking-[0.2em] text-slate-400 dark:text-slate-600 transition-colors duration-300">System</p>
            </div>

            <a href="{{ route('admin.logistics.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-300 outline-none text-decoration-none group relative overflow-hidden {{ request()->routeIs('admin.logistics.*') ? 'bg-indigo-600 dark:bg-indigo-600 text-white font-black shadow-lg shadow-indigo-600/20' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-white/5 hover:text-indigo-600 dark:hover:text-indigo-400 font-bold' }}">
                <i class="mdi mdi-truck-delivery-outline text-xl transition-transform duration-300 group-hover:scale-110 {{ request()->routeIs('admin.logistics.*') ? 'text-white' : '' }}"></i>
                <span class="text-sm flex-1">Logistik</span>
            </a>

            <a href="{{ route('admin.settings.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-300 outline-none text-decoration-none group relative overflow-hidden {{ request()->routeIs('admin.settings.*') ? 'bg-indigo-600 dark:bg-indigo-600 text-white font-black shadow-lg shadow-indigo-600/20' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-white/5 hover:text-indigo-600 dark:hover:text-indigo-400 font-bold' }}">
                <i class="mdi mdi-cog-outline text-xl transition-transform duration-300 group-hover:scale-110 {{ request()->routeIs('admin.settings.*') ? 'text-white' : '' }}"></i>
                <span class="text-sm flex-1">Pengaturan Umum</span>
            </a>
        @endif
    </div>

    {{-- ==========================================
         FOOTER LOGOUT (DANGER ZONE)
         ========================================== --}}
    <div class="p-4 border-t border-slate-200/60 dark:border-slate-800 bg-slate-50/30 dark:bg-transparent flex-shrink-0 z-20 transition-colors duration-500">
        <form method="POST" action="{{ route('logout') }}" id="form-logout-sidebar" class="w-full m-0">
            @csrf
            <button type="button" onclick="document.getElementById('form-logout-sidebar').submit();" class="w-full flex items-center justify-center gap-2 px-4 py-3 bg-transparent hover:bg-red-50 dark:hover:bg-rose-500/10 border border-slate-200 dark:border-rose-500/50 hover:border-red-200 dark:hover:border-rose-500 text-slate-600 dark:text-rose-500 hover:text-red-600 dark:hover:text-rose-400 dark:hover:shadow-[0_0_15px_rgba(244,63,94,0.2)] rounded-xl transition-all duration-300 outline-none group text-decoration-none shadow-sm dark:shadow-none focus:ring-4 focus:ring-red-500/20 active:scale-[0.98]">
                <i class="mdi mdi-power text-xl transition-transform duration-300 group-hover:scale-110 group-hover:rotate-12"></i>
                <span class="text-sm font-black tracking-wide">Keluar Sistem</span>
            </button>
        </form>
    </div>
</div>
