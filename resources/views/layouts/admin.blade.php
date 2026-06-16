<!DOCTYPE html>
<html lang="id"
      x-data="{ darkMode: localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches) }"
      x-init="$watch('darkMode', val => localStorage.setItem('theme', val ? 'dark' : 'light'))"
      :class="{ 'dark': darkMode }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') - Pondasikita Admin</title>

    {{-- Fonts & Icons --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@mdi/font@latest/css/materialdesignicons.min.css">

    {{-- Bootstrap & Tailwind CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: { extend: { fontFamily: { sans: ['Plus Jakarta Sans', 'sans-serif'] } } },
            corePlugins: { preflight: false }
        }
    </script>

    <style>
        /* Base Styling - Ultra Smooth & True Black OLED */
        body {
            margin: 0;
            padding: 0;
            font-family: 'Plus Jakarta Sans', sans-serif;
            overflow-x: hidden;
            display: flex;
            min-height: 100vh;
            @apply bg-slate-50 text-slate-800 transition-colors duration-500 ease-in-out;
        }

        .dark body {
            background: radial-gradient(circle at top left, #2e1065 0%, #0f172a 40%, #020617 100%);
            background-attachment: fixed;
            @apply text-slate-300;
        }

        [x-cloak] { display: none !important; }

        /* Layout Structure & Glassmorphism Dewa */
        .admin-sidebar {
            width: 260px;
            display: flex;
            flex-direction: column;
            position: fixed;
            height: 100vh;
            z-index: 1040;
            background: rgba(255, 255, 255, 0.3) !important;
            backdrop-filter: blur(60px) saturate(200%) !important;
            -webkit-backdrop-filter: blur(60px) saturate(200%) !important;
            border-right: 1px solid rgba(255, 255, 255, 0.4) !important;
            box-shadow: 4px 0 24px rgba(0,0,0,0.02);
            transition: transform 500ms cubic-bezier(0.4, 0, 0.2, 1);
        }

        .dark .admin-sidebar {
            background: rgba(15, 23, 42, 0.3) !important; /* deep slate with opacity */
            border-right: 1px solid rgba(255, 255, 255, 0.03) !important;
            box-shadow: 10px 0 50px rgba(0,0,0,0.2);
        }

        .admin-main-wrapper {
            flex-grow: 1;
            margin-left: 260px;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            position: relative;
            background: transparent !important;
            transition: all 500ms cubic-bezier(0.4, 0, 0.2, 1);
        }

        .admin-navbar {
            height: 65px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 1.5rem;
            position: sticky;
            top: 15px;
            margin: 0 1.5rem 1rem 1.5rem;
            z-index: 1030;
            background: rgba(255, 255, 255, 0.3) !important;
            backdrop-filter: blur(60px) saturate(200%) !important;
            -webkit-backdrop-filter: blur(60px) saturate(200%) !important;
            border: 1px solid rgba(255, 255, 255, 0.5) !important;
            border-radius: 24px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05) !important;
            transition: all 500ms;
        }

        .dark .admin-navbar {
            background: rgba(15, 23, 42, 0.3) !important;
            border: 1px solid rgba(255, 255, 255, 0.08) !important;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4) !important;
        }

        .admin-content {
            flex-grow: 1;
            padding: 2rem;
            position: relative;
            z-index: 10;
        }

        /* Glassmorphism Alerts Premium - Deep Neon Aesthetic */
        .alert {
            @apply border border-white/20 dark:border-white/[0.1] rounded-2xl font-bold text-sm shadow-[0_8px_30px_rgb(0,0,0,0.04)] dark:shadow-[0_8px_30px_rgb(0,0,0,0.5)] backdrop-blur-xl transition-all;
        }
        .alert-success { @apply bg-emerald-50/80 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-400; }
        .alert-danger { @apply bg-red-50/80 text-red-700 dark:bg-rose-500/20 dark:text-rose-400; }

        /* Responsive Mobile */
        @media (max-width: 991px) {
            .admin-sidebar { transform: translateX(-100%); }
            .admin-sidebar.sidebar-open { transform: translateX(0); }
            .admin-main-wrapper { margin-left: 0; }
            .admin-content { padding: 1.5rem 1rem; }
        }

        /* Scrollbar ala macOS - Darkened */
        ::-webkit-scrollbar { width: 10px; height: 10px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { border: 3px solid rgba(0,0,0,0); background-clip: padding-box; @apply bg-slate-400 dark:bg-slate-500 rounded-full transition-colors; }
        ::-webkit-scrollbar-thumb:hover { @apply bg-slate-500 dark:bg-slate-400; }
    </style>

    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/focus@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @stack('styles')
</head>

<body x-data="{ sidebarOpen: false }">

    {{-- Overlay untuk Mobile (Animasi Halus & Gelap) --}}
    <div x-show="sidebarOpen"
         x-transition:enter="transition-opacity ease-linear duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-30 bg-slate-900/60 dark:bg-[#000000]/80 backdrop-blur-sm lg:hidden"
         @click="sidebarOpen = false" x-cloak></div>

    {{-- 1. INCLUDE SIDEBAR ADMIN --}}
    <aside class="admin-sidebar" id="adminSidebar" :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'">
        @include('admin.partials.sidebar')
    </aside>

    <div class="admin-main-wrapper">

        {{-- 2. INCLUDE NAVBAR ADMIN --}}
        <header class="admin-navbar">
            <button class="btn btn-link text-slate-500 dark:text-slate-400 hover:text-blue-600 dark:hover:text-white p-0 d-lg-none me-3 transition-transform hover:scale-110 active:scale-95 outline-none" @click="sidebarOpen = true">
                <i class="mdi mdi-menu text-3xl leading-none"></i>
            </button>
            @include('admin.partials.navbar')
        </header>

        <main class="admin-content">

            {{-- Flash Message Success --}}
            @if(session('success'))
                <div x-data="{ show: true }" x-show="show"
                     x-transition:enter="transition ease-out duration-300 transform"
                     x-transition:enter-start="opacity-0 -translate-y-4"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     class="alert alert-success d-flex align-items-center mb-4 p-4" role="alert">
                    <i class="mdi mdi-check-circle-outline me-3 fs-4"></i>
                    <div class="flex-grow-1">
                        {{ session('success') }}
                    </div>
                    <button type="button" @click="show = false" class="btn-close ms-auto outline-none shadow-none border-0"></button>
                </div>
            @endif

            {{-- Flash Message Error --}}
            @if(session('error'))
                <div x-data="{ show: true }" x-show="show"
                     x-transition:enter="transition ease-out duration-300 transform"
                     x-transition:enter-start="opacity-0 -translate-y-4"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     class="alert alert-danger d-flex align-items-center mb-4 p-4" role="alert">
                    <i class="mdi mdi-alert-circle-outline me-3 fs-4"></i>
                    <div class="flex-grow-1">
                        {{ session('error') }}
                    </div>
                    <button type="button" @click="show = false" class="btn-close ms-auto outline-none shadow-none border-0"></button>
                </div>
            @endif

            @yield('content')
        </main>

        <footer class="mt-auto py-6 px-8 text-center">
            <p class="text-[10px] font-black text-slate-400 dark:text-slate-600 uppercase tracking-[0.3em] m-0 transition-colors duration-500">
                &copy; {{ date('Y') }} Pondasikita Platform &bull; Core Engine v2.0
            </p>
        </footer>
    </div>

    {{-- Bootstrap JS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Global standard tooltips initialization
        document.addEventListener('DOMContentLoaded', function() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]'))
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });
        });

        // Config Chart.js Global (Jika ada grafik di halaman)
        if (window.Chart) {
            Chart.defaults.font.family = "'Plus Jakarta Sans', sans-serif";
            Chart.defaults.color = darkMode ? '#94a3b8' : '#64748b';
        }
    </script>

    @stack('scripts')
</body>
</html>
