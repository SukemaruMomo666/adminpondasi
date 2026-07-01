<!DOCTYPE html>
<html lang="id">
<head>
    <link rel="icon" href="{{ asset('logopondasikita.png') }}" type="image/png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', 'Pondasikita - B2B Material Ecosystem')</title>

    {{-- 1. TAILWIND CSS & CONFIG DEWA --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'] },
                    colors: { brand: { 50: '#eff6ff', 500: '#3b82f6', 600: '#2563eb', 900: '#1e3a8a' } }
                }
            }
        }
    </script>

    {{-- 2. FONT & ICONS --}}
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    {{-- Slot untuk CSS tambahan per halaman (jika butuh) --}}
    @stack('styles')
    
    <style>
        /* Global Smooth Scroll & Scrollbar Minimalis */
        html { scroll-behavior: smooth; }
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    </style>
</head>
<body class="font-sans text-zinc-900 antialiased bg-[#fafafa] flex flex-col min-h-screen">

    {{-- NAVBAR GLOBAL --}}
    @include('partials.navbar')

    {{-- KONTEN DINAMIS BERADA DI SINI --}}
    <main class="flex-grow">
        @yield('content')
    </main>

    {{-- FOOTER GLOBAL --}}
    @include('partials.footer')

    {{-- 3. JAVASCRIPT GLOBAL --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    {{-- Slot untuk JS tambahan per halaman --}}
    @stack('scripts')
</body>
</html>
