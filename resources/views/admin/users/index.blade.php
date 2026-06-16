@extends('layouts.admin')

@section('title', 'User Management Center')

@push('styles')
<style>
    /* ========================================= */
    /* ==  PREMIUM USER DIRECTORY CSS         == */
    /* ========================================= */
    .hover-lift { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
    .hover-lift:hover { transform: translateY(-4px); }

    .table-wrapper::-webkit-scrollbar { height: 6px; }
    .table-wrapper::-webkit-scrollbar-track { background: transparent; }
    .table-wrapper::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    .table-wrapper::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    .dark .table-wrapper::-webkit-scrollbar-thumb { background: #475569; }
    .dark .table-wrapper::-webkit-scrollbar-thumb:hover { background: #64748b; }

    /* ========================================= */
    /* == POLYFILL DARK MODE (ANTI-PUTIH)     == */
    /* ========================================= */
    
    /* Layout Backgrounds */
    .dark .dark\:bg-slate-900 { background-color: #0f172a !important; }
    .dark .dark\:bg-slate-800 { background-color: #1e293b !important; }
    .dark .dark\:bg-slate-800\/80 { background-color: rgba(30, 41, 59, 0.8) !important; } /* INI OBAT BUG-NYA */
    .dark .dark\:bg-slate-800\/50 { background-color: rgba(30, 41, 59, 0.5) !important; }
    .dark .dark\:bg-slate-800\/40 { background-color: rgba(30, 41, 59, 0.4) !important; }
    .dark .dark\:bg-slate-800\/30 { background-color: rgba(30, 41, 59, 0.3) !important; }
    .dark .dark\:bg-slate-700 { background-color: #334155 !important; }

    /* Borders */
    .dark .dark\:border-slate-800 { border-color: #1e293b !important; }
    .dark .dark\:border-slate-800\/80 { border-color: rgba(30, 41, 59, 0.8) !important; }
    .dark .dark\:border-slate-700 { border-color: #334155 !important; }
    .dark .dark\:border-slate-700\/50 { border-color: rgba(51, 65, 85, 0.5) !important; }

    /* Text Colors */
    .dark .dark\:text-white { color: #ffffff !important; }
    .dark .dark\:text-slate-100 { color: #f1f5f9 !important; }
    .dark .dark\:text-slate-200 { color: #e2e8f0 !important; }
    .dark .dark\:text-slate-300 { color: #cbd5e1 !important; }
    .dark .dark\:text-slate-400 { color: #94a3b8 !important; }
    .dark .dark\:text-slate-500 { color: #64748b !important; }
    .dark .dark\:text-blue-400 { color: #60a5fa !important; }

    /* MODAL & FORM BOOTSTRAP OVERRIDE */
    .dark .modal-content { background-color: #0f172a !important; border: 1px solid #1e293b !important; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5) !important; color: #f8fafc !important; }
    .dark .modal-header, .dark .border-b { border-bottom-color: #1e293b !important; }
    .dark .form-control, .dark .form-select { background-color: #1e293b !important; border-color: #334155 !important; color: #f8fafc !important; }
    .dark .form-control:focus, .dark .form-select:focus { border-color: #3b82f6 !important; box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.15) !important; }
    .dark .form-control::placeholder { color: #64748b !important; }
    .dark .btn-close { filter: invert(1) grayscale(100%) brightness(200%); opacity: 0.5; }
    .dark .btn-close:hover { opacity: 1; }
    
    /* Pagination */
    .dark .pagination .page-link { background-color: #1e293b; border-color: #334155; color: #cbd5e1; }
    .dark .pagination .page-item.active .page-link { background-color: #3b82f6; border-color: #3b82f6; color: white; }

    /* BADGE GLOWING DARK MODE */
    .dark .dark\:bg-rose-500\/10 { background-color: rgba(244, 63, 94, 0.15) !important; }
    .dark .dark\:text-rose-400 { color: #fb7185 !important; }
    .dark .dark\:border-rose-500\/20 { border-color: rgba(244, 63, 94, 0.2) !important; }

    .dark .dark\:bg-amber-500\/10 { background-color: rgba(245, 158, 11, 0.15) !important; }
    .dark .dark\:text-amber-400 { color: #fbbf24 !important; }
    .dark .dark\:border-amber-500\/20 { border-color: rgba(245, 158, 11, 0.2) !important; }

    .dark .dark\:bg-blue-500\/10 { background-color: rgba(59, 130, 246, 0.15) !important; }
    .dark .dark\:border-blue-500\/20 { border-color: rgba(59, 130, 246, 0.2) !important; }

    .dark .dark\:bg-emerald-500\/10 { background-color: rgba(16, 185, 129, 0.15) !important; }
    .dark .dark\:text-emerald-400 { color: #34d399 !important; }
    .dark .dark\:border-emerald-500\/20 { border-color: rgba(16, 185, 129, 0.2) !important; }
</style>
@endpush

@section('content')

{{-- Menampilkan Error Validasi Form Modal --}}
@if($errors->any())
<div class="bg-red-50 dark:bg-rose-500/10 border border-red-200 dark:border-rose-500/20 rounded-2xl p-4 mb-6 shadow-sm flex items-start justify-between">
    <div class="flex gap-3">
        <i class="mdi mdi-alert-circle text-red-500 dark:text-rose-400 text-xl mt-0.5"></i>
        <div>
            <strong class="text-sm font-black text-red-700 dark:text-rose-400 block mb-1">Proses Gagal:</strong>
            <ul class="text-xs font-bold text-red-600 dark:text-rose-300 m-0 pl-4 space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
    <button type="button" class="text-red-400 hover:text-red-600 dark:hover:text-rose-300 outline-none" onclick="this.parentElement.style.display='none'"><i class="mdi mdi-close text-lg"></i></button>
</div>
@endif

{{-- HEADER HALAMAN --}}
<div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-4 mb-8">
    <div>
        <h2 class="text-2xl md:text-3xl font-black text-slate-800 dark:text-white tracking-tight mb-1 transition-colors duration-300">
            User Directory
        </h2>
        <div class="flex items-center gap-2 text-xs font-bold text-slate-500 dark:text-slate-400 transition-colors duration-300">
            <a href="{{ route('admin.dashboard') }}" class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors text-decoration-none">Dashboard</a>
            <i class="mdi mdi-chevron-right text-sm"></i>
            <span class="text-blue-600 dark:text-blue-400">Kelola Pengguna</span>
        </div>
    </div>
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.users.export', ['level' => $level_filter]) }}" class="px-4 py-2.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 text-sm font-bold rounded-xl transition-all shadow-sm outline-none flex items-center gap-2 text-decoration-none">
            <i class="mdi mdi-file-excel-outline text-lg"></i> Export CSV
        </a>

        {{-- TOMBOL HANYA UNTUK SUPER ADMIN --}}
        @if(auth()->user()->admin_role === 'super')
            <button class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold rounded-xl shadow-lg shadow-blue-600/30 transition-all outline-none flex items-center gap-2" data-bs-toggle="modal" data-bs-target="#modalAddAdmin">
                <i class="mdi mdi-shield-plus-outline text-lg"></i> Admin Baru
            </button>
        @endif
    </div>
</div>

{{-- GRID STATISTIK CEPAT --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
    <div class="bg-white dark:bg-slate-800/40 border border-slate-200 dark:border-slate-700/50 p-5 rounded-2xl flex items-center gap-4 shadow-sm hover-lift transition-colors duration-300 group">
        <div class="w-12 h-12 rounded-xl bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 flex items-center justify-center text-2xl flex-shrink-0 group-hover:scale-110 transition-transform">
            <i class="mdi mdi-account-group"></i>
        </div>
        <div>
            <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-0.5">Total User</div>
            <div class="text-2xl font-black text-slate-800 dark:text-white leading-none">{{ number_format($stats['total']) }}</div>
        </div>
    </div>

    <div class="bg-white dark:bg-slate-800/40 border border-slate-200 dark:border-slate-700/50 p-5 rounded-2xl flex items-center gap-4 shadow-sm hover-lift transition-colors duration-300 group">
        <div class="w-12 h-12 rounded-xl bg-orange-50 dark:bg-orange-500/10 text-orange-600 dark:text-orange-400 flex items-center justify-center text-2xl flex-shrink-0 group-hover:scale-110 transition-transform">
            <i class="mdi mdi-storefront"></i>
        </div>
        <div>
            <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-0.5">Penjual (Seller)</div>
            <div class="text-2xl font-black text-slate-800 dark:text-white leading-none">{{ number_format($stats['seller']) }}</div>
        </div>
    </div>

    <div class="bg-white dark:bg-slate-800/40 border border-slate-200 dark:border-slate-700/50 p-5 rounded-2xl flex items-center gap-4 shadow-sm hover-lift transition-colors duration-300 group">
        <div class="w-12 h-12 rounded-xl bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-2xl flex-shrink-0 group-hover:scale-110 transition-transform">
            <i class="mdi mdi-account-check"></i>
        </div>
        <div>
            <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-0.5">Customer Aktif</div>
            <div class="text-2xl font-black text-slate-800 dark:text-white leading-none">{{ number_format($stats['customer']) }}</div>
        </div>
    </div>

    <div class="bg-white dark:bg-slate-800/40 border border-slate-200 dark:border-slate-700/50 p-5 rounded-2xl flex items-center gap-4 shadow-sm hover-lift transition-colors duration-300 group">
        <div class="w-12 h-12 rounded-xl bg-rose-50 dark:bg-rose-500/10 text-rose-600 dark:text-rose-400 flex items-center justify-center text-2xl flex-shrink-0 group-hover:scale-110 transition-transform">
            <i class="mdi mdi-account-off"></i>
        </div>
        <div>
            <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-0.5">Akun Diblokir</div>
            <div class="text-2xl font-black text-slate-800 dark:text-white leading-none">{{ number_format($stats['banned']) }}</div>
        </div>
    </div>
</div>

{{-- KARTU UTAMA (TABEL & FILTER) --}}
<div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl shadow-sm overflow-hidden transition-colors duration-300">

    {{-- Filter Header --}}
    <div class="p-5 border-b border-slate-100 dark:border-slate-800/80 bg-white dark:bg-slate-900 flex flex-col md:flex-row justify-between items-center gap-4 transition-colors duration-300">

        {{-- Pill Filters --}}
        <div class="flex p-1 bg-slate-100 dark:bg-slate-800/50 rounded-xl border border-slate-200 dark:border-slate-700/50 w-full md:w-auto overflow-x-auto hide-scrollbar">
            @foreach(['semua', 'admin', 'seller', 'customer'] as $lv)
                <a href="{{ route('admin.users.index', ['level' => $lv, 'search' => $search]) }}"
                   class="px-4 py-2 text-xs font-black capitalize rounded-lg transition-all text-decoration-none outline-none whitespace-nowrap {{ $level_filter == $lv ? 'bg-white dark:bg-slate-700 text-blue-600 dark:text-white shadow-sm border border-slate-200 dark:border-slate-600' : 'text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200' }}">
                    {{ $lv }}
                </a>
            @endforeach
        </div>

        {{-- Search Input --}}
        <form action="{{ route('admin.users.index') }}" method="GET" class="relative w-full md:w-80">
            <input type="hidden" name="level" value="{{ $level_filter }}">
            <i class="mdi mdi-magnify absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 dark:text-slate-500 text-lg"></i>
            <input type="text" name="search" value="{{ $search }}"
                   class="w-full pl-11 pr-4 py-2.5 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-bold text-slate-800 dark:text-white placeholder:text-slate-400 dark:placeholder:text-slate-500 focus:bg-white dark:focus:bg-slate-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition-all shadow-inner dark:shadow-none"
                   placeholder="Cari Nama, Email, ID...">
        </form>
    </div>

    {{-- Tabel Data --}}
    <div class="overflow-x-auto table-wrapper">
        <table class="w-full text-left border-collapse whitespace-nowrap">
            <thead>
                <tr class="bg-slate-100 dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 transition-colors duration-300">
                    <th class="px-6 py-4 w-12 text-center align-middle">
                        <input type="checkbox" class="w-4 h-4 text-blue-600 bg-white dark:bg-slate-800 border-slate-300 dark:border-slate-600 rounded focus:ring-blue-500">
                    </th>
                    <th class="px-6 py-4 text-[10px] font-black text-slate-700 dark:text-slate-200 uppercase tracking-widest align-middle">Identitas Pengguna</th>
                    <th class="px-6 py-4 text-[10px] font-black text-slate-700 dark:text-slate-200 uppercase tracking-widest align-middle">Kontak & Info</th>
                    <th class="px-6 py-4 text-[10px] font-black text-slate-700 dark:text-slate-200 uppercase tracking-widest align-middle">Kasta Akun</th>
                    <th class="px-6 py-4 text-[10px] font-black text-slate-700 dark:text-slate-200 uppercase tracking-widest align-middle">Bergabung</th>
                    <th class="px-6 py-4 text-[10px] font-black text-slate-700 dark:text-slate-200 uppercase tracking-widest align-middle">Status</th>
                    <th class="px-6 py-4 text-[10px] font-black text-slate-700 dark:text-slate-200 uppercase tracking-widest text-right align-middle">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800/50">
                @forelse($users as $user)
                <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors duration-200">
                    <td class="px-6 py-4 text-center align-middle">
                        <input type="checkbox" class="w-4 h-4 text-blue-600 bg-white dark:bg-slate-800 border-slate-300 dark:border-slate-600 rounded focus:ring-blue-500">
                    </td>
                    <td class="px-6 py-4 align-middle">
                        <div class="flex items-center gap-4">
                            <div class="relative w-11 h-11 flex-shrink-0">
                                <img src="{{ $user->profile_picture_url ?? 'https://ui-avatars.com/api/?name='.urlencode($user->nama).'&background=random&color=fff' }}" class="w-full h-full rounded-xl object-cover shadow-sm border border-slate-100 dark:border-slate-700">
                                <span class="absolute -bottom-1 -right-1 w-3.5 h-3.5 rounded-full border-2 border-white dark:border-slate-900 {{ $user->status_online == 'online' ? 'bg-emerald-500' : 'bg-slate-400 dark:bg-slate-500' }}" title="{{ ucfirst($user->status_online) }}"></span>
                            </div>
                            <div>
                                <div class="text-sm font-black text-slate-800 dark:text-slate-100">{{ $user->nama }}</div>
                                <div class="text-[11px] font-bold text-slate-500 dark:text-slate-400 mt-0.5">
                                    ID: #{{ str_pad($user->id, 5, '0', STR_PAD_LEFT) }} <span class="mx-1 opacity-50">•</span> <span class="text-blue-600 dark:text-blue-400">@ {{ $user->username }}</span>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 align-middle">
                        <div class="flex flex-col gap-1">
                            <div class="text-[11px] font-black text-slate-700 dark:text-slate-300 flex items-center gap-1.5"><i class="mdi mdi-email-outline text-slate-400 dark:text-slate-500 text-sm"></i> {{ $user->email }}</div>
                            <div class="text-[11px] font-bold text-slate-500 dark:text-slate-400 flex items-center gap-1.5"><i class="mdi mdi-phone-outline text-sm"></i> {{ $user->no_telepon ?? 'Tidak ada nomor' }}</div>
                        </div>
                    </td>
                    <td class="px-6 py-4 align-middle">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[10px] font-black tracking-widest border
                            @if($user->level == 'admin') bg-rose-50 text-rose-600 border-rose-100 dark:bg-rose-500/10 dark:text-rose-400 dark:border-rose-500/20
                            @elseif($user->level == 'seller') bg-amber-50 text-amber-600 border-amber-100 dark:bg-amber-500/10 dark:text-amber-400 dark:border-amber-500/20
                            @else bg-blue-50 text-blue-600 border-blue-100 dark:bg-blue-500/10 dark:text-blue-400 dark:border-blue-500/20 @endif">
                            <i class="mdi {{ $user->level == 'seller' ? 'mdi-storefront-outline' : ($user->level == 'admin' ? 'mdi-shield-crown-outline' : 'mdi-account-outline') }} text-sm"></i>
                            {{ strtoupper($user->level) }}
                            @if($user->level == 'admin' && $user->admin_role)
                                ({{ strtoupper($user->admin_role) }})
                            @endif
                        </span>
                    </td>
                    <td class="px-6 py-4 align-middle">
                        <div class="text-xs font-black text-slate-700 dark:text-slate-300">{{ \Carbon\Carbon::parse($user->created_at)->format('d M Y') }}</div>
                    </td>
                    <td class="px-6 py-4 align-middle">
                        @if($user->is_banned)
                            <div class="flex flex-col gap-1">
                                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-[10px] font-black tracking-wider bg-slate-100 text-slate-500 border border-slate-200 dark:bg-slate-800 dark:text-slate-400 dark:border-slate-700 line-through">
                                    <i class="mdi mdi-cancel"></i> Diblokir ({{ strtoupper($user->ban_type) }})
                                </span>
                                @if($user->banned_until)
                                    <span class="text-[9px] font-bold text-rose-500">Hingga: {{ $user->banned_until->format('d/m/Y') }}</span>
                                @endif
                            </div>
                        @else
                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-[10px] font-black tracking-wider bg-emerald-50 text-emerald-600 border border-emerald-200 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-500/20 shadow-sm dark:shadow-none">
                                <i class="mdi mdi-check-decagram"></i> Aktif
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right align-middle">
                        <div class="flex items-center justify-end gap-2">
                            {{-- Btn Detail --}}
                            <button type="button" class="w-8 h-8 rounded-lg bg-blue-50 dark:bg-blue-500/10 border border-blue-100 dark:border-blue-500/20 flex items-center justify-center text-blue-600 dark:text-blue-400 hover:bg-blue-600 hover:text-white dark:hover:bg-blue-500 transition-all shadow-sm outline-none btn-detail"
                                    data-nama="{{ $user->nama }}"
                                    data-username="{{ $user->username }}"
                                    data-email="{{ $user->email }}"
                                    data-phone="{{ $user->no_telepon ?? '-' }}"
                                    data-level="{{ strtoupper($user->level) }} {{ $user->level == 'admin' ? '('.strtoupper($user->admin_role).')' : '' }}"
                                    data-join="{{ \Carbon\Carbon::parse($user->created_at)->format('d M Y, H:i') }}"
                                    data-img="{{ $user->profile_picture_url ?? 'https://ui-avatars.com/api/?name='.urlencode($user->nama).'&background=random&color=fff' }}"
                                    data-bs-toggle="modal" data-bs-target="#modalDetailUser" title="Lihat Profil">
                                <i class="mdi mdi-eye text-base"></i>
                            </button>

                            {{-- Btn Edit --}}
                            <button type="button" class="w-8 h-8 rounded-lg bg-amber-50 dark:bg-amber-500/10 border border-amber-100 dark:border-amber-500/20 flex items-center justify-center text-amber-600 dark:text-amber-400 hover:bg-amber-500 hover:text-white dark:hover:bg-amber-500 transition-all shadow-sm outline-none btn-edit"
                                    data-url="{{ route('admin.users.update', $user->id) }}"
                                    data-nama="{{ $user->nama }}"
                                    data-email="{{ $user->email }}"
                                    data-phone="{{ $user->no_telepon ?? '' }}"
                                    data-level="{{ $user->level }}"
                                    data-role="{{ $user->admin_role }}"
                                    data-bs-toggle="modal" data-bs-target="#modalEditUser" title="Edit Pengguna">
                                <i class="mdi mdi-pencil text-base"></i>
                            </button>

                            {{-- Btn Block --}}
                            @if ($user->id !== auth()->id())
                                <form id="form-ban-{{ $user->id }}" action="{{ route('admin.users.toggleBan', $user->id) }}" method="POST" class="m-0">
                                    @csrf
                                    <input type="hidden" name="ban_type" id="ban-type-{{ $user->id }}">
                                    <input type="hidden" name="ban_reason" id="ban-reason-{{ $user->id }}">
                                    <input type="hidden" name="banned_until" id="ban-until-{{ $user->id }}">
                                    
                                    <button type="button" 
                                            class="w-8 h-8 rounded-lg border flex items-center justify-center transition-all shadow-sm outline-none {{ $user->is_banned ? 'bg-emerald-50 dark:bg-emerald-500/10 border-emerald-100 dark:border-emerald-500/20 text-emerald-600 dark:text-emerald-400 hover:bg-emerald-500 hover:text-white dark:hover:bg-emerald-500' : 'bg-rose-50 dark:bg-rose-500/10 border-rose-100 dark:border-rose-500/20 text-rose-600 dark:text-rose-400 hover:bg-rose-600 hover:text-white dark:hover:bg-rose-500' }}"
                                            onclick="handleBanToggle('{{ $user->id }}', '{{ $user->nama }}', {{ $user->is_banned ? 'true' : 'false' }})"
                                            title="{{ $user->is_banned ? 'Aktifkan Akun' : 'Blokir Akun' }}">
                                        <i class="mdi {{ $user->is_banned ? 'mdi-account-check-outline' : 'mdi-account-cancel-outline' }} text-base"></i>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-20 px-6">
                        <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-slate-50 dark:bg-slate-800/50 mb-4">
                            <i class="mdi mdi-account-search-outline text-4xl text-slate-300 dark:text-slate-600"></i>
                        </div>
                        <h5 class="text-base font-black text-slate-700 dark:text-slate-300 mb-1">Tidak ada pengguna ditemukan</h5>
                        <p class="text-xs font-bold text-slate-500 dark:text-slate-500 m-0">Coba gunakan kata kunci pencarian atau filter yang berbeda.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Footer Tabel & Pagination --}}
    <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-800/80 bg-slate-50/50 dark:bg-slate-900/50 flex flex-col md:flex-row justify-between items-center gap-4 transition-colors duration-300">
        <div class="text-xs font-bold text-slate-500 dark:text-slate-400">
            Menampilkan <span class="text-blue-600 dark:text-blue-400 font-black">{{ $users->firstItem() ?? 0 }}</span> - <span class="text-blue-600 dark:text-blue-400 font-black">{{ $users->lastItem() ?? 0 }}</span> dari <span class="text-slate-800 dark:text-white font-black">{{ $users->total() }}</span> pengguna
        </div>
        <div class="pagination-wrapper">
            {{ $users->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>

{{-- ============================================================================== --}}
{{-- MODAL AREA (DIROMBAK AGAR SIMETRIS & MENDUKUNG DARK MODE)                      --}}
{{-- ============================================================================== --}}

{{-- MODAL TAMBAH ADMIN BARU --}}
<div class="modal fade" id="modalAddAdmin" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-[2rem] border-0 shadow-2xl overflow-hidden transition-colors duration-300">
            <div class="modal-header p-6 bg-white dark:bg-slate-900 border-b border-slate-100 dark:border-slate-800">
                <h5 class="font-black text-slate-800 dark:text-white flex items-center gap-2 m-0 text-base">
                    <i class="mdi mdi-shield-account-outline text-blue-600 text-xl"></i> Tambah Administrator
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-6 bg-slate-50/50 dark:bg-slate-900">
                <form action="{{ route('admin.users.store') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-[11px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-2 ml-1">Nama Lengkap</label>
                        <input type="text" name="nama" class="form-control rounded-xl py-3 font-bold text-sm shadow-inner dark:shadow-none" placeholder="Cth: Budiman Santoso" required>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-[11px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-2 ml-1">Username</label>
                            <input type="text" name="username" class="form-control rounded-xl py-3 font-bold text-sm shadow-inner dark:shadow-none" placeholder="Cth: budiman" required>
                        </div>
                        <div>
                            <label class="block text-[11px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-2 ml-1">Email Resmi</label>
                            <input type="email" name="email" class="form-control rounded-xl py-3 font-bold text-sm shadow-inner dark:shadow-none" placeholder="budiman@pondasikita.com" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-[11px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-2 ml-1">Otoritas / Hak Akses</label>
                        <select name="admin_role" class="form-select rounded-xl py-3 font-bold text-sm shadow-inner dark:shadow-none cursor-pointer" required>
                            <option value="" disabled selected>-- Pilih Hak Akses --</option>
                            <option value="cs">Customer Service (Kelola Pengguna & Komplain)</option>
                            <option value="finance">Finance (Keuangan, Payout & Laporan)</option>
                            <option value="super">Super Admin (Akses Penuh Semua Sistem)</option>
                        </select>
                    </div>

                    <div class="mb-6">
                        <label class="block text-[11px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-2 ml-1">Password Akun</label>
                        <input type="password" name="password" class="form-control rounded-xl py-3 font-bold text-sm shadow-inner dark:shadow-none" placeholder="Minimal 6 Karakter" required minlength="6">
                    </div>

                    <div class="flex gap-3 justify-end mt-2 pt-2">
                        <button type="button" class="w-full sm:w-auto px-6 py-3 rounded-xl font-bold text-sm text-slate-600 dark:text-slate-300 bg-slate-200/50 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 transition-colors outline-none" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="w-full sm:w-auto px-8 py-3 rounded-xl font-black text-sm text-white bg-blue-600 hover:bg-blue-700 shadow-md shadow-blue-600/20 transition-all outline-none">BUAT AKUN</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- MODAL EDIT PENGGUNA --}}
<div class="modal fade" id="modalEditUser" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-[2rem] border-0 shadow-2xl overflow-hidden transition-colors duration-300">
            <div class="modal-header p-6 bg-white dark:bg-slate-900 border-b border-slate-100 dark:border-slate-800">
                <h5 class="font-black text-slate-800 dark:text-white flex items-center gap-2 m-0 text-base">
                    <i class="mdi mdi-account-edit-outline text-amber-500 text-xl"></i> Edit Data Pengguna
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-6 bg-slate-50/50 dark:bg-slate-900">
                <form id="formEditUser" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-[11px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-2 ml-1">Nama Lengkap</label>
                        <input type="text" name="nama" id="editNama" class="form-control rounded-xl py-3 font-bold text-sm shadow-inner dark:shadow-none" required>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-[11px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-2 ml-1">Email Resmi</label>
                            <input type="email" name="email" id="editEmail" class="form-control rounded-xl py-3 font-bold text-sm shadow-inner dark:shadow-none" required>
                        </div>
                        <div>
                            <label class="block text-[11px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-2 ml-1">No Telepon</label>
                            <input type="text" name="no_telepon" id="editPhone" class="form-control rounded-xl py-3 font-bold text-sm shadow-inner dark:shadow-none">
                        </div>
                    </div>

                    {{-- FORM PILIHAN ROLE HANYA BISA DIAKSES OLEH SUPER ADMIN --}}
                    @if(auth()->user()->admin_role === 'super')
                    <div class="mb-4 p-4 bg-amber-50 dark:bg-amber-500/10 border border-amber-100 dark:border-amber-500/20 rounded-2xl" id="editRoleContainer" style="display: none;">
                        <label class="block text-[11px] font-black text-amber-600 dark:text-amber-400 uppercase tracking-widest mb-2 ml-1">Ubah Otoritas Admin</label>
                        <select name="admin_role" id="editRole" class="form-select rounded-xl py-3 font-bold text-sm shadow-inner dark:shadow-none cursor-pointer border-amber-200 dark:border-amber-500/30">
                            <option value="cs">Customer Service</option>
                            <option value="finance">Finance</option>
                            <option value="super">Super Admin</option>
                        </select>
                        <div class="text-[10px] font-bold text-amber-500 mt-2 ml-1 flex items-center gap-1"><i class="mdi mdi-alert-circle"></i> Berdampak pada akses menu mereka.</div>
                    </div>
                    @endif

                    <div class="mb-6">
                        <label class="block text-[11px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-2 ml-1">Reset Password (Opsional)</label>
                        <input type="password" name="password" class="form-control rounded-xl py-3 font-bold text-sm shadow-inner dark:shadow-none" placeholder="Kosongkan jika tidak diubah">
                    </div>

                    <div class="flex gap-3 justify-end mt-2 pt-2">
                        <button type="button" class="w-full sm:w-auto px-6 py-3 rounded-xl font-bold text-sm text-slate-600 dark:text-slate-300 bg-slate-200/50 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 transition-colors outline-none" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="w-full sm:w-auto px-8 py-3 rounded-xl font-black text-sm text-white bg-amber-500 hover:bg-amber-600 shadow-md shadow-amber-500/20 transition-all outline-none">SIMPAN</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- MODAL DETAIL PENGGUNA --}}
<div class="modal fade" id="modalDetailUser" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-[2rem] border-0 shadow-2xl overflow-hidden transition-colors duration-300">
            <div class="modal-header border-b-0 p-6 pb-0 bg-white dark:bg-slate-900 relative">
                <button type="button" class="btn-close absolute top-6 right-6 z-10" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center pt-0 pb-8 px-6 border-b border-slate-100 dark:border-slate-800 bg-white dark:bg-slate-900">
                <div class="w-28 h-28 mx-auto mb-4 relative rounded-[1.5rem] p-1.5 bg-white dark:bg-slate-800 shadow-lg border border-slate-100 dark:border-slate-700">
                    <img id="detImg" src="" class="w-full h-full rounded-xl object-cover">
                </div>
                <h4 class="text-2xl font-black text-slate-800 dark:text-white mb-1 tracking-tight" id="detNama">Nama User</h4>
                <p class="text-sm font-bold text-blue-500 dark:text-blue-400 mb-5">@<span id="detUsername">username</span></p>
                <span class="inline-flex items-center px-5 py-2 rounded-full text-xs font-black tracking-widest bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-300 border border-slate-200 dark:border-slate-700 shadow-sm" id="detLevel">ROLE</span>
            </div>
            <div class="modal-body p-6 bg-slate-50/50 dark:bg-slate-900">
                <div class="grid grid-cols-1 gap-4">
                    <div class="flex flex-col gap-1.5 p-4 bg-white dark:bg-slate-800/80 rounded-2xl border border-slate-100 dark:border-slate-700 shadow-sm">
                        <span class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest">Alamat Email</span>
                        <span class="text-sm font-bold text-slate-800 dark:text-slate-200 flex items-center gap-2"><i class="mdi mdi-email-outline text-blue-500 text-lg"></i> <span id="detEmail">email</span></span>
                    </div>
                    <div class="flex flex-col gap-1.5 p-4 bg-white dark:bg-slate-800/80 rounded-2xl border border-slate-100 dark:border-slate-700 shadow-sm">
                        <span class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest">Nomor Telepon</span>
                        <span class="text-sm font-bold text-slate-800 dark:text-slate-200 flex items-center gap-2"><i class="mdi mdi-phone-outline text-emerald-500 text-lg"></i> <span id="detPhone">phone</span></span>
                    </div>
                    <div class="flex flex-col gap-1.5 p-4 bg-white dark:bg-slate-800/80 rounded-2xl border border-slate-100 dark:border-slate-700 shadow-sm">
                        <span class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest">Tanggal Bergabung</span>
                        <span class="text-sm font-bold text-slate-800 dark:text-slate-200 flex items-center gap-2"><i class="mdi mdi-calendar-clock-outline text-amber-500 text-lg"></i> <span id="detJoin">date</span></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function handleBanToggle(userId, userName, isCurrentlyBanned) {
        if (isCurrentlyBanned) {
            Swal.fire({
                title: 'Aktifkan Akun?',
                text: `Apakah Anda yakin ingin mengaktifkan kembali akun ${userName}?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#10b981',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Aktifkan!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(`form-ban-${userId}`).submit();
                }
            });
        } else {
            Swal.fire({
                title: `<span class="text-xl font-black uppercase tracking-tight">Blokir ${userName}</span>`,
                html: `
                    <div class="text-left mt-4">
                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">Tipe Pemblokiran</label>
                        <select id="swal-ban-type" class="w-full px-4 py-3 rounded-xl border border-slate-200 text-sm font-bold outline-none focus:ring-2 focus:ring-rose-500/20 mb-4">
                            <option value="ringan">BAN RINGAN (Peringatan/Pelanggaran Ringan)</option>
                            <option value="berat">BAN BERAT (Pelanggaran Serius/Permanen)</option>
                        </select>

                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">Alasan Pemblokiran</label>
                        <textarea id="swal-ban-reason" rows="3" class="w-full px-4 py-3 rounded-xl border border-slate-200 text-sm font-bold outline-none focus:ring-2 focus:ring-rose-500/20 mb-4" placeholder="Tulis alasan detail..."></textarea>

                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">Blokir Hingga (Opsional)</label>
                        <input type="datetime-local" id="swal-ban-until" class="w-full px-4 py-3 rounded-xl border border-slate-200 text-sm font-bold outline-none focus:ring-2 focus:ring-rose-500/20">
                        <p class="text-[10px] text-slate-400 mt-1 italic font-bold">*Kosongkan jika ingin blokir selamanya/permanen</p>
                    </div>
                `,
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'BLOKIR SEKARANG',
                cancelButtonText: 'BATAL',
                preConfirm: () => {
                    const type = document.getElementById('swal-ban-type').value;
                    const reason = document.getElementById('swal-ban-reason').value;
                    const until = document.getElementById('swal-ban-until').value;

                    if (!reason) {
                        Swal.showValidationMessage('Alasan pemblokiran wajib diisi!');
                        return false;
                    }

                    return { type, reason, until };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(`ban-type-${userId}`).value = result.value.type;
                    document.getElementById(`ban-reason-${userId}`).value = result.value.reason;
                    document.getElementById(`ban-until-${userId}`).value = result.value.until;
                    document.getElementById(`form-ban-${userId}`).submit();
                }
            });
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        
        // PERBAIKAN BUG MODAL TERKUNCI / DI BELAKANG BACKDROP
        document.querySelectorAll('.modal').forEach(modal => {
            document.body.appendChild(modal);
        });

        // Detail User Logic
        document.querySelectorAll('.btn-detail').forEach(btn => {
            btn.addEventListener('click', function() {
                document.getElementById('detImg').src = this.getAttribute('data-img');
                document.getElementById('detNama').innerText = this.getAttribute('data-nama');
                document.getElementById('detUsername').innerText = this.getAttribute('data-username');
                document.getElementById('detEmail').innerText = this.getAttribute('data-email');
                document.getElementById('detPhone').innerText = this.getAttribute('data-phone');
                document.getElementById('detLevel').innerText = this.getAttribute('data-level');
                document.getElementById('detJoin').innerText = this.getAttribute('data-join');
            });
        });

        // Edit User Logic
        const roleContainer = document.getElementById('editRoleContainer');
        const roleSelect = document.getElementById('editRole');

        document.querySelectorAll('.btn-edit').forEach(btn => {
            btn.addEventListener('click', function() {
                document.getElementById('formEditUser').action = this.getAttribute('data-url');
                document.getElementById('editNama').value = this.getAttribute('data-nama');
                document.getElementById('editEmail').value = this.getAttribute('data-email');

                let phone = this.getAttribute('data-phone');
                document.getElementById('editPhone').value = (phone && phone !== '-') ? phone : '';

                if(roleContainer) {
                    if (this.getAttribute('data-level') === 'admin') {
                        roleContainer.style.display = 'block';
                        roleSelect.value = this.getAttribute('data-role');
                    } else {
                        roleContainer.style.display = 'none';
                    }
                }
            });
        });
    });
</script>
@endpush