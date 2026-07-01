<!DOCTYPE html>
<html lang="id">
<head>
    <link rel="icon" href="{{ asset('logopondasikita.png') }}" type="image/png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') - Pondasikita Seller</title>

    {{-- Font Inter untuk kesan UI Premium --}}
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    {{-- Ikon Material Design --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@mdi/font@latest/css/materialdesignicons.min.css">

    {{-- TAILWIND CSS --}}
    <script src="https://cdn.tailwindcss.com"></script>

    {{-- Konfigurasi Font Default Tailwind ke Inter --}}
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    }
                }
            }
        }
    </script>

    <style>
        /* Custom Scrollbar Global (Elegan & Tipis) */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

        /* Cegah scroll horizontal yang tidak sengaja */
        body { overflow-x: hidden; }
    </style>
    @stack('styles')
</head>
<body class="bg-slate-50 text-slate-900 antialiased selection:bg-blue-500 selection:text-white">

    <div class="flex min-h-screen w-full relative">

        {{-- 1. SIDEBAR --}}
        {{-- Pastikan di file sidebar.blade.php Anda menambahkan class ini di tag <aside> paling atas:
             -translate-x-full lg:translate-x-0
             (Agar di mobile tersembunyi, di laptop muncul) --}}
        @include('seller.partials.sidebar')

        {{-- Overlay Gelap untuk Mobile (Muncul saat sidebar dibuka di HP) --}}
        <div id="sidebarOverlay" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-40 hidden lg:hidden transition-opacity duration-300 opacity-0 cursor-pointer"></div>

        {{-- 2. KONTEN UTAMA --}}
        {{-- KUNCI UTAMA: lg:ml-[260px] akan mendorong konten agar tidak tertimpa sidebar di desktop --}}
        <div class="flex flex-col flex-1 min-w-0 transition-all duration-300 lg:ml-[260px]">

            {{-- NAVBAR --}}
            @include('seller.partials.navbar')

            {{-- ISI HALAMAN (Dashboard, Produk, dll) --}}
            <main class="flex-grow w-full">
                @yield('content')
            </main>

            {{-- FOOTER --}}
            <div class="px-4 md:px-6 lg:px-8">
                <footer class="mt-10 pt-6 pb-8 border-t border-slate-200">
                    <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                        <div class="text-sm font-medium text-slate-500">
                            &copy; {{ date('Y') }} <span class="font-black text-slate-800">Pondasikita</span> Seller Center.
                            <span class="hidden sm:inline">All rights reserved.</span>
                        </div>
                        <div class="flex items-center gap-4 sm:gap-6 text-sm font-bold text-slate-400">
                            <a href="#" class="hover:text-blue-600 transition-colors">Pusat Bantuan</a>
                            <div class="w-1 h-1 bg-slate-300 rounded-full"></div>
                            <a href="#" class="hover:text-blue-600 transition-colors">Kebijakan Privasi</a>
                        </div>
                    </div>
                </footer>
            </div>

        </div>
    </div>

    {{-- Script Global --}}
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    {{-- Logic Toggle Sidebar Responsif (Pengganti jQuery Lama) --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const toggleBtn = document.getElementById('sidebarToggle'); // Tombol di navbar
            const overlay = document.getElementById('sidebarOverlay');

            // Fungsi untuk membuka/menutup sidebar di layar HP
            function toggleMobileSidebar() {
                // Hapus/tambah class translate untuk memunculkan sidebar
                sidebar.classList.toggle('-translate-x-full');

                // Urus overlay background gelap
                if (overlay.classList.contains('hidden')) {
                    overlay.classList.remove('hidden');
                    // setTimeout sedikit agar animasi transisi opacity terbaca oleh browser
                    setTimeout(() => overlay.classList.remove('opacity-0'), 10);
                } else {
                    overlay.classList.add('opacity-0');
                    setTimeout(() => overlay.classList.add('hidden'), 300); // 300ms sesuai durasi transisi
                }
            }

            // Jalankan fungsi jika tombol hamburger diklik
            if (toggleBtn) {
                toggleBtn.addEventListener('click', toggleMobileSidebar);
            }

            // Tutup sidebar jika user mengklik area gelap (overlay)
            if (overlay) {
                overlay.addEventListener('click', toggleMobileSidebar);
            }
        });
    </script>

    @stack('scripts')
</body>
</html>

