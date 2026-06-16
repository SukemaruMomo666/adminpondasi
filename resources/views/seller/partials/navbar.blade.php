{{-- NAVBAR TOP (DARK MODE) --}}
<header class="sticky top-0 z-40 flex items-center justify-between w-full h-[70px] px-4 md:px-6 bg-slate-900 border-b border-slate-800 shadow-sm">

    {{-- KIRI: Hamburger Menu (Muncul di Mobile/Tablet) --}}
    <div class="flex items-center gap-4">
        {{-- Tombol Toggle Sidebar --}}
        <button id="sidebarToggle" class="p-2 text-slate-400 rounded-xl hover:bg-slate-800 hover:text-white transition-colors lg:hidden focus:outline-none focus:ring-2 focus:ring-blue-600 focus:ring-offset-2 focus:ring-offset-slate-900">
            <i class="mdi mdi-menu text-2xl leading-none"></i>
        </button>

        {{-- Opsional: Breadcrumb atau Judul Halaman bisa ditaruh di sini --}}
        <div class="hidden lg:block">
            <span class="text-sm font-bold text-slate-400">Panel Kelola Toko</span>
        </div>
    </div>

    {{-- KANAN: Ikon Aksi & Profil --}}
    <div class="flex items-center gap-2 sm:gap-3">

        {{-- Dropdown Notifikasi --}}
        <div class="relative" id="notifDropdownContainer">
            {{-- Ikon Notifikasi (Lonceng) --}}
            <button onclick="toggleNotifMenu()" class="relative p-2.5 text-slate-400 rounded-xl hover:bg-slate-800 hover:text-white transition-all group focus:outline-none">
                <i class="mdi mdi-bell-outline text-xl group-hover:scale-110 transition-transform"></i>
                {{-- Badge Titik Merah --}}
                <span id="notifBadge" class="absolute top-2 right-2.5 w-2 h-2 bg-red-500 rounded-full border border-slate-900 hidden"></span>
            </button>

            {{-- Isi Menu Notifikasi --}}
            <div id="notifMenu" class="absolute right-0 mt-3 w-80 sm:w-96 bg-white border border-slate-200 rounded-2xl shadow-xl shadow-black/10 opacity-0 invisible transform scale-95 transition-all duration-200 origin-top-right z-50 flex flex-col overflow-hidden">
                <div class="p-4 border-b border-slate-100 flex justify-between items-center bg-slate-50">
                    <h3 class="text-sm font-black text-slate-900">Notifikasi</h3>
                    <button onclick="markAllNotifAsRead()" class="text-[10px] font-bold text-blue-600 hover:text-blue-800 transition-colors focus:outline-none">
                        Tandai Semua Dibaca
                    </button>
                </div>
                <div id="notifList" class="flex-1 overflow-y-auto max-h-[350px] p-2 flex flex-col custom-scrollbar">
                    {{-- Diisi via AJAX --}}
                    <div class="flex flex-col items-center justify-center p-8 text-center">
                        <i class="mdi mdi-loading mdi-spin text-3xl text-slate-300 mb-2"></i>
                        <p class="text-xs font-bold text-slate-500">Memuat...</p>
                    </div>
                </div>
                <div class="p-3 border-t border-slate-100 bg-slate-50 text-center hidden">
                    <a href="#" class="text-xs font-bold text-blue-600 hover:text-blue-800">Lihat Semua Notifikasi</a>
                </div>
            </div>
        </div>

        {{-- Garis Pemisah --}}
        <div class="w-px h-6 bg-slate-800 mx-1 sm:mx-2"></div>

        {{-- Dropdown Profil --}}
        <div class="relative" id="profileDropdownContainer">
            {{-- Tombol Profil --}}
            <button onclick="toggleProfileMenu()" class="flex items-center gap-3 p-1.5 pr-3 rounded-xl hover:bg-slate-800 border border-transparent hover:border-slate-700 transition-all group focus:outline-none">
                {{-- Avatar --}}
                <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center text-white font-black text-sm shadow-md shadow-blue-900/50">
                    {{ strtoupper(substr(Auth::user()->nama ?? 'S', 0, 1)) }}
                </div>

                {{-- Nama & Role (Sembunyi di layar kecil HP) --}}
                <div class="hidden md:flex flex-col text-left">
                    <span class="text-xs font-black text-slate-200 group-hover:text-white leading-tight">
                        {{ Str::limit(Auth::user()->nama ?? 'Seller', 15) }}
                    </span>
                    <span class="text-[10px] font-bold text-slate-500 uppercase">Seller</span>
                </div>

                {{-- Chevron Animasi --}}
                <i class="mdi mdi-chevron-down text-slate-500 group-hover:text-white transition-transform duration-300" id="profileChevron"></i>
            </button>

            {{-- Isi Menu Dropdown --}}
            <div id="profileMenu" class="absolute right-0 mt-3 w-48 bg-slate-800 border border-slate-700 rounded-2xl shadow-xl shadow-black/50 opacity-0 invisible transform scale-95 transition-all duration-200 origin-top-right">
                <div class="p-2 space-y-1">
                    <a href="{{ route('seller.shop.profile') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm font-bold text-slate-300 hover:text-white hover:bg-slate-700 rounded-xl transition-colors">
                        <i class="mdi mdi-account-circle-outline text-lg text-slate-400"></i> Profil Toko
                    </a>
                    <a href="{{ route('seller.shop.settings') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm font-bold text-slate-300 hover:text-white hover:bg-slate-700 rounded-xl transition-colors">
                        <i class="mdi mdi-cog-outline text-lg text-slate-400"></i> Pengaturan
                    </a>

                    <div class="h-px bg-slate-700 my-2"></div>

                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full flex items-center gap-3 px-3 py-2.5 text-sm font-bold text-red-400 hover:text-red-300 hover:bg-red-500/10 rounded-xl transition-colors">
                            <i class="mdi mdi-logout text-lg"></i> Keluar
                        </button>
                    </form>
                </div>
            </div>
        </div>

    </div>
</header>

{{-- SCRIPT LOGIC DROPDOWN PROFIL --}}
<script>
    function toggleProfileMenu() {
        const menu = document.getElementById('profileMenu');
        const chevron = document.getElementById('profileChevron');

        if (menu.classList.contains('opacity-0')) {
            // Buka Dropdown
            menu.classList.remove('opacity-0', 'invisible', 'scale-95');
            menu.classList.add('opacity-100', 'visible', 'scale-100');
            chevron.classList.add('rotate-180');
        } else {
            // Tutup Dropdown
            menu.classList.add('opacity-0', 'invisible', 'scale-95');
            menu.classList.remove('opacity-100', 'visible', 'scale-100');
            chevron.classList.remove('rotate-180');
        }
    }

    // Klik di luar untuk menutup Dropdown (Fitur wajib UI Modern)
    document.addEventListener('click', function(event) {
        // Profil Dropdown
        const profileContainer = document.getElementById('profileDropdownContainer');
        if (profileContainer && !profileContainer.contains(event.target)) {
            const menu = document.getElementById('profileMenu');
            const chevron = document.getElementById('profileChevron');
            if(menu) {
                menu.classList.add('opacity-0', 'invisible', 'scale-95');
                menu.classList.remove('opacity-100', 'visible', 'scale-100');
            }
            if(chevron) chevron.classList.remove('rotate-180');
        }

        // Notif Dropdown
        const notifContainer = document.getElementById('notifDropdownContainer');
        if (notifContainer && !notifContainer.contains(event.target)) {
            const menu = document.getElementById('notifMenu');
            if(menu) {
                menu.classList.add('opacity-0', 'invisible', 'scale-95');
                menu.classList.remove('opacity-100', 'visible', 'scale-100');
            }
        }
    });

    // ==============================================================
    // LOGIKA NOTIFIKASI
    // ==============================================================
    function toggleNotifMenu() {
        const menu = document.getElementById('notifMenu');
        if (menu.classList.contains('opacity-0')) {
            menu.classList.remove('opacity-0', 'invisible', 'scale-95');
            menu.classList.add('opacity-100', 'visible', 'scale-100');
            fetchNotifications();
        } else {
            menu.classList.add('opacity-0', 'invisible', 'scale-95');
            menu.classList.remove('opacity-100', 'visible', 'scale-100');
        }
    }

    function fetchNotifications() {
        fetch('{{ route("seller.notifications.fetch") }}')
            .then(res => res.json())
            .then(data => {
                const badge = document.getElementById('notifBadge');
                const list = document.getElementById('notifList');
                
                if (data.unread_count > 0) {
                    badge.classList.remove('hidden');
                } else {
                    badge.classList.add('hidden');
                }

                if (data.notifications.length === 0) {
                    list.innerHTML = `
                        <div class="flex flex-col items-center justify-center p-8 text-center">
                            <i class="mdi mdi-bell-sleep text-4xl text-slate-200 mb-2"></i>
                            <p class="text-xs font-bold text-slate-500">Belum ada notifikasi baru.</p>
                        </div>
                    `;
                    return;
                }

                let html = '';
                data.notifications.forEach(notif => {
                    html += `
                        <div onclick="markNotifAsRead('${notif.id}', '${notif.url}')" class="flex gap-3 p-3 hover:bg-slate-50 rounded-xl cursor-pointer transition-colors border-b border-slate-50 last:border-0 relative group">
                            <div class="w-10 h-10 rounded-full bg-${notif.color}-50 text-${notif.color}-500 flex items-center justify-center shrink-0 border border-${notif.color}-100">
                                <i class="mdi ${notif.icon} text-lg"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h4 class="text-xs font-black text-slate-900 truncate pr-4">${notif.title}</h4>
                                <p class="text-[11px] text-slate-500 line-clamp-2 mt-0.5 leading-snug">${notif.message}</p>
                                <span class="text-[9px] font-bold text-slate-400 mt-1.5 block">${notif.created_at}</span>
                            </div>
                            <div class="w-2 h-2 rounded-full bg-blue-500 absolute right-3 top-4 shadow-sm shadow-blue-500/50"></div>
                        </div>
                    `;
                });
                list.innerHTML = html;
            })
            .catch(err => console.error(err));
    }

    function markNotifAsRead(id, url) {
        fetch(`{{ url('seller/notifications/read') }}/${id}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        }).then(() => {
            if (url && url !== '#') {
                window.location.href = url;
            } else {
                fetchNotifications();
            }
        });
    }

    function markAllNotifAsRead() {
        const btn = event.target;
        btn.innerHTML = 'Memproses...';
        fetch('{{ route("seller.notifications.readAll") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        }).then(() => {
            btn.innerHTML = 'Tandai Semua Dibaca';
            fetchNotifications();
        });
    }

    // Auto-fetch unread count on page load
    document.addEventListener('DOMContentLoaded', () => {
        fetch('{{ route("seller.notifications.fetch") }}')
            .then(res => res.json())
            .then(data => {
                if(data.unread_count > 0) document.getElementById('notifBadge').classList.remove('hidden');
            });
    });
</script>
