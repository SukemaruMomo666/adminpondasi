<!DOCTYPE html>
<html lang="id">
<head>
    <link rel="icon" href="{{ asset('logopondasikita.png') }}" type="image/png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pulihkan Sandi - Pondasikita B2B</title>

    {{-- Tailwind CSS CDN + Config Sesuai Tema Utama --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'] },
                    colors: { brand: { 50: '#eff6ff', 500: '#3b82f6', 600: '#2563eb', 900: '#1e3a8a' } },
                    animation: {
                        'blob': 'blob 10s infinite alternate',
                        'fade-in-up': 'fadeInUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards',
                    },
                    keyframes: {
                        blob: { '0%': { transform: 'translate(0px, 0px) scale(1)' }, '100%': { transform: 'translate(30px, -50px) scale(1.1)' } },
                        fadeInUp: { '0%': { opacity: 0, transform: 'translateY(20px)' }, '100%': { opacity: 1, transform: 'translateY(0)' } }
                    }
                }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        input:-webkit-autofill { -webkit-box-shadow: 0 0 0 50px white inset; }
        @keyframes shimmer { 100% { transform: translateX(100%); } }
        .animate-shimmer { animation: shimmer 2s infinite; }
        .bento-card { background: rgba(255, 255, 255, 0.03); border: 1px solid rgba(255, 255, 255, 0.05); backdrop-filter: blur(10px); border-radius: 2rem; padding: 1.5rem; transition: all 0.3s ease; }
        .bento-card:hover { background: rgba(255, 255, 255, 0.05); border-color: rgba(37, 99, 235, 0.3); transform: translateY(-2px); }
    </style>
</head>
<body class="bg-white font-sans text-zinc-900 antialiased flex flex-col lg:flex-row min-h-screen overflow-hidden">

    {{-- KIRI: SISI FORM (Identik dengan Login/Register) --}}
    <div class="w-full lg:w-1/2 flex items-center justify-center p-6 sm:p-12 relative overflow-y-auto h-screen custom-scrollbar z-20 bg-white">
        <div class="w-full max-w-md my-auto animate-fade-in-up pb-10">

            {{-- Logo Mobile & Back Button --}}
            <div class="mb-8 mt-4 lg:mt-0">
                <div class="inline-flex items-center justify-center p-2.5 bg-zinc-100 rounded-xl mb-6 shadow-sm lg:hidden">
                    <img src="{{ asset('assets/image/Pondasikita.com.png') }}" alt="Logo" class="h-8 w-auto object-contain" onerror="this.outerHTML='<div class=\'text-black font-black text-xl px-2\'>P<span class=\'text-blue-600\'>.</span></div>'">
                </div>
                
                <a href="{{ route('login') }}" 
                   class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-zinc-50 border border-zinc-200 text-zinc-700 hover:bg-zinc-900 hover:text-white hover:border-zinc-900 font-black text-[10px] uppercase tracking-widest transition-all duration-300 mb-8 group shadow-sm">
                    <i class="fas fa-arrow-left group-hover:-translate-x-1 transition-transform"></i> Kembali ke Login
                </a>

                <h2 class="text-3xl font-black text-black tracking-tight mb-2">Lupa Kata Sandi?</h2>
                <p class="text-zinc-500 font-medium text-sm leading-relaxed">
                    Jangan panik. Masukkan alamat email yang terhubung dengan akun Anda untuk mendapatkan instruksi pemulihan.
                </p>
            </div>

            <form action="{{ route('password.email') }}" method="POST" id="forgotPasswordForm" class="space-y-5">
                @csrf

                <div class="relative group">
                    <label class="block text-[10px] font-black text-zinc-400 uppercase tracking-widest mb-1.5 ml-1">Email Terdaftar</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="fas fa-envelope text-zinc-400 group-focus-within:text-blue-600 transition-colors"></i>
                        </div>
                        <input type="email" name="email" 
                               class="w-full bg-zinc-50 border border-zinc-200 text-black text-sm font-semibold rounded-2xl focus:bg-white focus:border-blue-600 focus:ring-4 focus:ring-blue-600/10 block pl-11 pr-4 py-3.5 transition-all outline-none placeholder:text-zinc-400" 
                               placeholder="john@perusahaan.com" required>
                    </div>
                </div>

                <button type="submit" id="submitBtn" 
                        class="group relative w-full bg-black hover:bg-blue-600 text-white font-black py-4 rounded-2xl transition-all duration-300 shadow-[0_4px_20px_rgba(0,0,0,0.15)] hover:shadow-[0_8px_30px_rgba(37,99,235,0.3)] hover:-translate-y-1 mt-4 overflow-hidden">
                    <div class="absolute inset-0 w-full h-full bg-gradient-to-r from-transparent via-white/20 to-transparent -translate-x-full group-hover:animate-shimmer"></div>
                    <span class="relative z-10 flex items-center justify-center gap-2">
                        Kirim Link Pemulihan <i class="fas fa-paper-plane text-xs"></i>
                    </span>
                </button>
            </form>

            <div class="mt-12 pt-8 border-t border-zinc-100 flex flex-col sm:flex-row items-center justify-between gap-4">
                <span class="text-[10px] font-black text-zinc-400 uppercase tracking-widest">Pondasikita Auth</span>
                <a href="https://wa.me/62xxxxxxxx" class="text-[11px] font-bold text-zinc-500 hover:text-emerald-500 transition-colors flex items-center gap-1.5">
                    <i class="fab fa-whatsapp text-sm"></i> Bantuan CS
                </a>
            </div>

        </div>
    </div>

    {{-- KANAN: SISI VISUAL (DARK CINEMATIC + BENTO GRID KALEM) --}}
    <div class="hidden lg:flex w-1/2 bg-[#09090b] relative items-center justify-center overflow-hidden p-12">
        {{-- Animated Abstract Glow (Sama dengan Register) --}}
        <div class="absolute top-0 left-0 w-full h-full overflow-hidden pointer-events-none">
            <div class="absolute top-10 right-10 w-[500px] h-[500px] bg-indigo-600/10 rounded-full blur-[120px] animate-blob"></div>
            <div class="absolute bottom-0 left-0 w-[400px] h-[400px] bg-blue-600/10 rounded-full blur-[100px] animate-blob" style="animation-delay: 1.5s;"></div>
            <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')] opacity-[0.02]"></div>
        </div>

        {{-- Cinematic Branding --}}
        <div class="relative z-10 w-full max-w-lg animate-fade-in-up">
            <h1 class="text-4xl font-black text-white leading-[1.2] tracking-tight mb-8">
                Akses Kembali<br>
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-indigo-400">Pusat Kendali Anda.</span>
            </h1>

            {{-- Kalem Bento Grid --}}
            <div class="grid grid-cols-2 gap-4">
                <div class="bento-card col-span-2 flex items-center gap-5">
                    <div class="w-12 h-12 rounded-2xl bg-blue-500/10 border border-blue-500/20 text-blue-400 flex items-center justify-center shrink-0">
                        <i class="fas fa-shield-check text-xl"></i>
                    </div>
                    <div>
                        <h4 class="text-white font-bold text-sm mb-1">Keamanan Data Terjamin</h4>
                        <p class="text-zinc-400 text-xs font-medium leading-relaxed">Tautan pemulihan dienkripsi secara khusus dan hanya berlaku untuk waktu yang terbatas.</p>
                    </div>
                </div>

                <div class="bento-card flex flex-col justify-center">
                    <div class="w-10 h-10 rounded-xl bg-white/5 text-zinc-300 flex items-center justify-center mb-4">
                        <i class="fas fa-bolt"></i>
                    </div>
                    <h4 class="text-white font-bold text-sm mb-1">Proses Instan</h4>
                    <p class="text-zinc-500 text-[10px] font-medium">Email terkirim seketika.</p>
                </div>

                <div class="bento-card flex flex-col justify-center">
                    <div class="w-10 h-10 rounded-xl bg-white/5 text-zinc-300 flex items-center justify-center mb-4">
                        <i class="fas fa-headset"></i>
                    </div>
                    <h4 class="text-white font-bold text-sm mb-1">Dukungan 24/7</h4>
                    <p class="text-zinc-500 text-[10px] font-medium">Tim CS siap membantu.</p>
                </div>
            </div>
        </div>

        <div class="absolute bottom-8 left-12 text-zinc-600 text-xs font-semibold">
            © {{ date('Y') }} Pondasikita Enterprise.
        </div>
    </div>

    {{-- SWEETALERT & LOGIC --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('forgotPasswordForm');
            const submitBtn = document.getElementById('submitBtn');

            form.addEventListener('submit', function() {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-circle-notch fa-spin mr-2"></i> Memproses...';
                submitBtn.classList.add('opacity-80', 'cursor-not-allowed');
            });

            @if(session('status'))
                Swal.fire({
                    icon: 'success',
                    title: 'Email Terkirim!',
                    text: '{{ session('status') }}',
                    confirmButtonColor: '#2563eb',
                    confirmButtonText: 'Tutup',
                    customClass: { popup: 'rounded-3xl', confirmButton: 'rounded-xl font-bold px-6 py-3' }
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
                    confirmButtonColor: '#000000',
                    confirmButtonText: 'Perbaiki',
                    customClass: { popup: 'rounded-3xl', confirmButton: 'rounded-xl font-bold px-6 py-3' }
                });
            @endif
        });
    </script>
</body>
</html>
