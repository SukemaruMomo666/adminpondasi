<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seller Login - Pondasikita Seller Centre</title>

    {{-- Tailwind CSS CDN + Config Dewa --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'] },
                    colors: {
                        // Konsisten: Monokrom & Biru
                        brand: { 50: '#eff6ff', 100: '#dbeafe', 500: '#3b82f6', 600: '#2563eb', 900: '#1e3a8a' },
                        surface: '#fcfcfd',
                    },
                    animation: {
                        'blob': 'blob 12s infinite alternate',
                        'blob-reverse': 'blobReverse 15s infinite alternate',
                        'fade-in-up': 'fadeInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards',
                    },
                    keyframes: {
                        blob: { '0%': { transform: 'translate(0px, 0px) scale(1)' }, '100%': { transform: 'translate(30px, -50px) scale(1.1)' } },
                        blobReverse: { '0%': { transform: 'translate(0px, 0px) scale(1.1)' }, '100%': { transform: 'translate(-40px, 40px) scale(0.9)' } },
                        fadeInUp: { '0%': { opacity: 0, transform: 'translateY(20px)' }, '100%': { opacity: 1, transform: 'translateY(0)' } }
                    }
                }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        input:-webkit-autofill { -webkit-box-shadow: 0 0 0 50px white inset; }
        .glass-panel {
            background: linear-gradient(145deg, rgba(255,255,255,0.03) 0%, rgba(255,255,255,0.01) 100%);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255,255,255,0.05);
        }
    </style>
</head>
<body class="bg-white font-sans text-zinc-900 antialiased flex min-h-screen overflow-hidden">

    {{-- ======================================================= --}}
    {{-- KIRI: SISI VISUAL (DARK MODE - BLUE NEBULA) --}}
    {{-- ======================================================= --}}
    <div class="hidden lg:flex w-1/2 bg-zinc-950 relative items-center justify-center overflow-hidden p-12 z-0">

        {{-- Animated Abstract Glow (Konsisten Biru & Indigo) --}}
        <div class="absolute inset-0 overflow-hidden pointer-events-none z-0 mix-blend-screen">
            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[800px] h-[800px] transform-gpu">
                <div class="absolute top-10 left-10 w-[400px] h-[400px] bg-blue-600/20 rounded-full blur-[100px] animate-blob transform-gpu"></div>
                <div class="absolute bottom-10 right-10 w-[500px] h-[500px] bg-indigo-600/20 rounded-full blur-[120px] animate-blob-reverse transform-gpu" style="animation-delay: -5s;"></div>
            </div>
        </div>

        {{-- Texture Overlay --}}
        <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')] opacity-[0.05] pointer-events-none z-10"></div>
        <div class="absolute inset-0 bg-gradient-to-b from-transparent via-zinc-950/50 to-zinc-950 z-10 pointer-events-none"></div>

        {{-- Cinematic Branding Content --}}
        <div class="relative z-20 w-full max-w-lg animate-fade-in-up">

            <div class="mb-10 inline-flex items-center justify-center p-3 glass-panel rounded-2xl shadow-2xl transform hover:scale-105 transition-transform duration-500">
                <img src="{{ asset('assets/image/Pondasikita.com.png') }}" alt="Logo" class="h-10 w-auto object-contain drop-shadow-[0_0_15px_rgba(255,255,255,0.2)]" onerror="this.outerHTML='<div class=\'text-white font-black text-2xl px-2\'>P<span class=\'text-blue-600\'>.</span> Seller</div>'">
            </div>

            <h1 class="text-5xl font-black text-white leading-[1.1] tracking-tight mb-6">
                Pusat Kendali<br>
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-indigo-400">Bisnis Material Anda.</span>
            </h1>

            <p class="text-zinc-400 text-lg font-medium leading-relaxed mb-12">
                Kelola inventaris, pantau analitik penjualan secara real-time, dan jangkau ribuan kontraktor B2B di seluruh Indonesia hanya dari satu dashboard.
            </p>

            {{-- Glassmorphism Features --}}
            <div class="space-y-4">
                <div class="glass-panel p-5 rounded-[2rem] flex items-center gap-4 shadow-[0_8px_32px_rgba(0,0,0,0.3)] group hover:bg-white/10 transition-colors duration-500 border border-white/5">
                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white text-lg shadow-[0_0_20px_rgba(37,99,235,0.4)] shrink-0 group-hover:scale-110 transition-transform duration-500">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div>
                        <h4 class="text-white font-bold text-sm tracking-wide">Analitik Pintar</h4>
                        <p class="text-zinc-400 text-xs mt-1">Pantau performa penjualan dan konversi toko Anda.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="absolute bottom-8 left-12 text-zinc-600 text-[10px] font-bold uppercase tracking-widest z-20">
            © {{ date('Y') }} Pondasikita Seller Centre
        </div>
    </div>

    {{-- ======================================================= --}}
    {{-- KANAN: SISI FORM LOGIN (CLEAN WHITE & BLACK) --}}
    {{-- ======================================================= --}}
    <div class="w-full lg:w-1/2 flex items-center justify-center p-6 sm:p-12 relative overflow-y-auto bg-white z-10 shadow-[-20px_0_40px_rgba(0,0,0,0.05)]">
        <div class="w-full max-w-md animate-fade-in-up" style="animation-delay: 0.1s;">

            {{-- Logo Mobile --}}
            <div class="lg:hidden mb-8">
                <img src="{{ asset('assets/image/Pondasikita.com.png') }}" alt="Logo" class="h-10 w-auto" onerror="this.outerHTML='<div class=\'text-black font-black text-3xl\'>Pondasikita<span class=\'text-blue-600\'>.</span></div>'">
                <span class="inline-block mt-2 bg-black text-white text-[9px] font-black px-2 py-1 rounded uppercase tracking-widest">Seller Centre</span>
            </div>

            <div class="mb-10">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-zinc-100 text-zinc-700 text-[10px] font-black uppercase tracking-widest mb-4 border border-zinc-200">
                    <i class="fas fa-store text-blue-600"></i> Portal Penjual
                </div>
                <h2 class="text-3xl font-black text-black tracking-tight mb-2">Akses Toko Anda</h2>
                <p class="text-zinc-500 font-medium text-sm">
                    Ingin mulai berjualan? <a href="{{ route('seller.register') }}" class="text-blue-600 font-bold hover:underline transition-all">Daftar sebagai Penjual</a>
                </p>
            </div>

            <form action="{{ route('seller.login.process') }}" method="POST" id="sellerLoginForm" class="space-y-5">
                @csrf

                {{-- Input Username/Email --}}
                <div class="relative group">
                    <label class="block text-[10px] font-black text-zinc-400 uppercase tracking-widest mb-1.5 ml-1">Kredensial Login</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="fas fa-user text-zinc-400 group-focus-within:text-blue-600 transition-colors"></i>
                        </div>
                        <input type="text" id="username" name="username" placeholder="Email atau Username Toko" required value="{{ old('username') }}" class="w-full bg-zinc-50 border-2 border-zinc-200 text-black text-sm font-semibold rounded-2xl focus:bg-white focus:border-blue-600 focus:ring-4 focus:ring-blue-600/10 block pl-11 pr-4 py-4 transition-all outline-none placeholder:text-zinc-400 placeholder:font-medium">
                    </div>
                </div>

                {{-- Input Password --}}
                <div class="relative group">
                    <label class="block text-[10px] font-black text-zinc-400 uppercase tracking-widest mb-1.5 ml-1">Kata Sandi</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="fas fa-lock text-zinc-400 group-focus-within:text-blue-600 transition-colors"></i>
                        </div>
                        <input type="password" id="password" name="password" placeholder="Masukkan Kata Sandi" required class="w-full bg-zinc-50 border-2 border-zinc-200 text-black text-sm font-semibold rounded-2xl focus:bg-white focus:border-blue-600 focus:ring-4 focus:ring-blue-600/10 block pl-11 pr-12 py-4 transition-all outline-none placeholder:text-zinc-400 placeholder:font-medium">

                        {{-- Toggle Password Visibility --}}
                        <button type="button" onclick="togglePassword()" class="absolute inset-y-0 right-0 pr-4 flex items-center text-zinc-400 hover:text-black transition-colors focus:outline-none">
                            <i class="fas fa-eye" id="eyeIcon"></i>
                        </button>
                    </div>
                </div>

                <div class="flex items-center justify-between pt-1">
                    <label class="flex items-center gap-2 cursor-pointer group">
                        <div class="relative flex items-center justify-center shrink-0">
                            <input type="checkbox" class="peer sr-only">
                            <div class="w-5 h-5 rounded-[6px] border-2 border-zinc-300 peer-checked:bg-black peer-checked:border-black transition-all duration-300 flex items-center justify-center group-hover:border-zinc-400">
                                <i class="fas fa-check text-white text-[10px] opacity-0 peer-checked:opacity-100 scale-50 peer-checked:scale-100 transition-all duration-300"></i>
                            </div>
                        </div>
                        <span class="text-xs font-bold text-zinc-500 group-hover:text-black transition-colors select-none">Ingat Saya</span>
                    </label>
                    <a href="{{ url('/lupa-password') }}" class="text-xs font-bold text-zinc-500 hover:text-blue-600 transition-colors">Lupa Sandi?</a>
                </div>

                {{-- Submit Button --}}
                <button type="submit" id="btnSubmit" class="w-full bg-black hover:bg-blue-600 text-white font-black py-4 rounded-2xl transition-all duration-300 shadow-[0_8px_20px_rgba(0,0,0,0.15)] hover:shadow-[0_12px_30px_rgba(37,99,235,0.3)] hover:-translate-y-1 hover:scale-[1.01] mt-6 flex items-center justify-center gap-2">
                    Masuk ke Seller Centre <i class="fas fa-arrow-right text-xs"></i>
                </button>
            </form>
        </div>
    </div>

    {{-- SweetAlert & Logic Scripts --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Toggle Password
        function togglePassword() {
            const pwd = document.getElementById('password');
            const icon = document.getElementById('eyeIcon');
            if (pwd.type === 'password') {
                pwd.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                pwd.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

        // Form Submit Loading State
        document.getElementById('sellerLoginForm').addEventListener('submit', function() {
            const btn = document.getElementById('btnSubmit');
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> Mengautentikasi...';
            btn.classList.add('opacity-80', 'cursor-not-allowed');
            btn.classList.remove('hover:-translate-y-1', 'hover:scale-[1.01]', 'hover:bg-blue-600');
        });

        // Flash Messages from Laravel Session
        @if(session('success'))
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: 'Berhasil!',
                text: "{{ session('success') }}",
                showConfirmButton: false,
                timer: 3000,
                customClass: { popup: 'rounded-2xl border border-zinc-100 shadow-xl' }
            });
        @endif

        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Akses Ditolak',
                html: "{!! session('error') !!}",
                confirmButtonColor: '#09090b',
                customClass: { popup: 'rounded-[2rem]', confirmButton: 'rounded-xl px-8 py-3 font-bold' }
            });
        @endif

        @if($errors->any())
            Swal.fire({
                icon: 'error',
                title: 'Validasi Gagal',
                html: `
                    <div style="text-align: left; font-size: 0.85rem; color: #dc2626; background: #fef2f2; padding: 15px; border-radius: 12px; border: 1px solid #fecaca; margin-top: 10px;">
                        <ul style="padding-left: 20px; margin: 0; font-weight: 500; line-height: 1.5;">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                `,
                confirmButtonColor: '#09090b',
                customClass: { popup: 'rounded-[2rem]', confirmButton: 'rounded-xl px-8 py-3 font-bold' }
            });
        @endif
    </script>
</body>
</html>
