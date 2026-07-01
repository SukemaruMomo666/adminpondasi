<!DOCTYPE html>
<html lang="id">
<head>
    <link rel="icon" href="{{ asset('logopondasikita.png') }}" type="image/png">
    <meta charset="UTF-8">
    <title>Profil Saya - {{ $user->nama }} | Pondasikita</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    {{-- Tailwind CSS CDN + Config Dewa --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'] },
                    colors: {
                        brand: { 50: '#eff6ff', 100: '#dbeafe', 500: '#3b82f6', 600: '#2563eb', 700: '#1d4ed8' },
                        surface: '#fcfcfd',
                    },
                    boxShadow: {
                        'soft': '0 4px 40px -4px rgba(0,0,0,0.03)',
                        'float': '0 10px 30px -5px rgba(0,0,0,0.08)',
                        'glow': '0 0 20px rgba(37,99,235,0.3)',
                    },
                    animation: {
                        'fade-in': 'fadeIn 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards',
                    },
                    keyframes: {
                        fadeIn: { '0%': { opacity: 0, transform: 'translateY(20px)' }, '100%': { opacity: 1, transform: 'translateY(0)' } }
                    }
                }
            }
        }
    </script>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f4f4f5; }
        .bg-mesh { background-image: radial-gradient(at 80% 0%, hsla(225,100%,56%,0.15) 0px, transparent 50%), radial-gradient(at 0% 100%, hsla(240,100%,70%,0.1) 0px, transparent 50%); }
    </style>
</head>
{{-- FIX 1: Hapus pb-20, tambahkan flex, flex-col, dan min-h-screen --}}
<body class="text-zinc-800 antialiased pt-[80px] flex flex-col min-h-screen">

    {{-- Include Navbar --}}
    @include('partials.navbar')

    {{-- BREADCRUMB MINIMALIS --}}
    <div class="bg-white border-b border-zinc-200 hidden md:block">
        <div class="max-w-[1100px] mx-auto px-4 sm:px-6 py-3">
            <nav class="flex text-xs font-semibold text-zinc-500 items-center gap-3">
                <a href="{{ url('/') }}" class="hover:text-black transition-colors">Beranda</a>
                <span class="w-1 h-1 rounded-full bg-zinc-300"></span>
                <span class="text-zinc-900">Profil Pengguna</span>
            </nav>
        </div>
    </div>

    {{-- BAN ALERT UNTUK CUSTOMER (RINGAN) --}}
    @if(Auth::user()->is_banned && Auth::user()->ban_type === 'ringan')
    <div class="max-w-[1100px] mx-auto px-4 sm:px-6 mt-6">
        <div class="bg-amber-50 border-2 border-amber-200 rounded-[2rem] p-6 flex flex-col md:flex-row items-center gap-6 shadow-sm border-dashed animate-fade-in">
            <div class="w-16 h-16 bg-amber-100 rounded-2xl flex items-center justify-center text-amber-600 shadow-inner shrink-0">
                <i class="fas fa-exclamation-triangle text-2xl"></i>
            </div>
            <div class="flex-1 text-center md:text-left">
                <h4 class="text-xl font-black text-amber-800 mb-1">Akses Akun Dibatasi!</h4>
                <p class="text-sm font-bold text-amber-600/80">Akun Anda dalam status penangguhan RINGAN karena: <b>{{ Auth::user()->ban_reason }}</b>. Anda tidak dapat melakukan transaksi hingga banding disetujui.</p>
            </div>
            <a href="#section-banding" class="px-6 py-3 bg-amber-500 hover:bg-amber-600 text-white font-black rounded-xl transition-all shadow-lg shadow-amber-200 no-underline whitespace-nowrap">
                Ajukan Banding Sekarang
            </a>
        </div>
    </div>
    @endif

    {{-- FIX 2: Tambahkan flex-grow dan w-full agar main content mendorong footer ke bawah --}}
    <main class="flex-grow w-full max-w-[1100px] mx-auto px-4 sm:px-6 py-8 lg:py-12">

        <div class="mb-8">
            <h1 class="text-3xl font-black text-black tracking-tight">Akun Saya</h1>
            <p class="text-sm font-medium text-zinc-500 mt-1">Kelola informasi data diri, keamanan, dan preferensi akun B2B Anda.</p>
        </div>

        {{-- LAYOUT GRID 12 KOLOM: Kiri (Identity), Kanan (Details & CTA) --}}
        <div class="flex flex-col lg:grid lg:grid-cols-12 gap-8 items-start">

            {{-- ========================================== --}}
            {{-- KOLOM KIRI: IDENTITY CARD (Col-span-4) --}}
            {{-- ========================================== --}}
            <div class="w-full lg:col-span-4 lg:sticky lg:top-28 animate-fade-in">
                <div class="bg-white rounded-[2rem] shadow-soft border border-zinc-200 overflow-hidden">

                    {{-- Cover Banner --}}
                    <div class="h-32 bg-gradient-to-r from-blue-600 to-indigo-700 relative bg-mesh">
                        <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')] opacity-10"></div>
                    </div>

                    {{-- Avatar Overlap --}}
                    <div class="relative -mt-16 flex justify-center">
                        <div class="w-32 h-32 rounded-full p-1.5 bg-white shadow-float relative group">
                            <img src="{{ asset('assets/uploads/avatars/' . ($user->profile_picture_url ?? 'person.png')) }}"
                                 class="w-full h-full rounded-full object-cover border border-zinc-100"
                                 onerror="this.onerror=null;this.src='{{ asset('assets/uploads/avatars/person-icon-1680.png') }}';">

                            {{-- Hover Change Photo Button --}}
                            <button onclick="alert('Fitur ganti foto profil segera hadir!')" class="absolute inset-1.5 bg-black/50 rounded-full flex flex-col items-center justify-center text-white opacity-0 group-hover:opacity-100 transition-opacity backdrop-blur-sm cursor-pointer">
                                <i class="fas fa-camera text-xl mb-1"></i>
                                <span class="text-[9px] font-bold tracking-widest uppercase">Ubah Foto</span>
                            </button>
                        </div>
                    </div>

                    {{-- User Info --}}
                    <div class="p-6 pt-4 text-center">
                        <h2 class="text-xl font-black text-zinc-900 mb-0.5">{{ $user->nama ?? 'Pengguna B2B' }}</h2>
                        <p class="text-sm font-semibold text-zinc-500 mb-4">{{ '@' . $user->username }}</p>

                        <div class="flex flex-wrap items-center justify-center gap-2 mb-6">
                            <span class="bg-zinc-900 text-white px-3 py-1 rounded-lg text-[10px] font-black uppercase tracking-widest">
                                {{ $user->level ?? 'Customer' }}
                            </span>
                            <span class="bg-blue-50 text-blue-600 px-3 py-1 rounded-lg text-[10px] font-black uppercase tracking-widest border border-blue-100">
                                Aktif Sejak {{ \Carbon\Carbon::parse($user->created_at)->format('Y') }}
                            </span>
                        </div>

                        <div class="w-12 h-1 bg-zinc-200 rounded-full mx-auto mb-6"></div>

                        {{-- Action Buttons --}}
                        <div class="flex flex-col gap-3">
                            <a href="{{ route('profil.edit') }}" class="w-full bg-white border-2 border-zinc-200 text-zinc-700 hover:border-black hover:text-black hover:bg-zinc-50 font-bold py-3 rounded-xl transition-all flex items-center justify-center gap-2 text-sm shadow-sm">
                                <i class="fas fa-user-edit"></i> Edit Profil
                            </a>
                            @auth
                            <form action="{{ route('logout') }}" method="POST" class="m-0">
                                @csrf
                                <button type="submit" class="w-full flex items-center justify-center gap-2 bg-white border-2 border-zinc-100 text-zinc-400 hover:text-red-600 hover:border-red-100 hover:bg-red-50 font-bold py-3 rounded-xl transition-all text-sm group">
                                    <i class="fas fa-power-off group-hover:rotate-12 transition-transform"></i> Logout
                                </button>
                            </form>
                            @endauth
                            <a href="{{ route('profil.password') }}" class="w-full bg-transparent text-zinc-500 hover:text-blue-600 font-bold py-3 rounded-xl transition-all flex items-center justify-center gap-2 text-sm hover:bg-blue-50">
                                <i class="fas fa-shield-alt"></i> Pengaturan Keamanan
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ========================================== --}}
            {{-- KOLOM KANAN: DETIL & LOGIKA SELLER (Col-span-8) --}}
            {{-- ========================================== --}}
            <div class="w-full lg:col-span-8 flex flex-col gap-8 animate-fade-in" style="animation-delay: 0.1s;">

                {{-- LOGIKA UNDANGAN SELLER (Rata Kanan/Atas, Hanya untuk Customer Biasa) --}}
                @if(isset($user->level) && strtolower($user->level) !== 'seller' && strtolower($user->level) !== 'admin')
                    <div class="bg-zinc-900 rounded-[2rem] p-8 sm:p-10 relative overflow-hidden shadow-float group border border-zinc-800 flex flex-col sm:flex-row items-center justify-between gap-6">
                        {{-- Ornamen Cahaya --}}
                        <div class="absolute -top-10 -right-10 w-48 h-48 bg-blue-600/30 rounded-full blur-[60px] group-hover:bg-blue-500/40 transition-colors duration-500"></div>
                        <div class="absolute -bottom-10 -left-10 w-32 h-32 bg-indigo-600/30 rounded-full blur-[50px]"></div>

                        <div class="relative z-10 text-center sm:text-left">
                            <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/10 text-white text-[10px] font-black uppercase tracking-widest mb-3 border border-white/10 backdrop-blur-md">
                                <i class="fas fa-rocket text-yellow-400"></i> Peluang Bisnis
                            </div>
                            <h3 class="text-2xl sm:text-3xl font-black text-white leading-tight mb-2">Ingin Menjadi Pemasok?</h3>
                            <p class="text-zinc-400 text-sm font-medium max-w-md">Tingkatkan skala bisnis Anda. Daftar sebagai Mitra Toko dan raih ribuan kontraktor B2B di seluruh Indonesia.</p>
                        </div>

                        {{-- Tombol Buka Toko --}}
                        <div class="relative z-10 shrink-0 w-full sm:w-auto">
                            <a href="{{ route('seller.register') }}" class="block w-full text-center bg-blue-600 hover:bg-blue-500 text-white font-black px-6 py-4 rounded-xl transition-all duration-300 shadow-glow hover:-translate-y-1 hover:shadow-[0_0_30px_rgba(37,99,235,0.6)]">
                                Buka Toko Sekarang <i class="fas fa-arrow-right ml-1"></i>
                            </a>
                        </div>
                    </div>
                @endif

                {{-- INFORMASI PRIBADI --}}
                <div class="bg-white rounded-[2rem] shadow-soft border border-zinc-200 p-6 sm:p-8">
                    <div class="flex items-center justify-between mb-8 pb-4 border-b border-zinc-100">
                        <h3 class="text-lg font-black text-black flex items-center gap-2">
                            <i class="fas fa-address-card text-blue-600"></i> Data Pribadi
                        </h3>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-8 gap-x-6">
                        <div class="flex flex-col gap-1.5">
                            <span class="text-[11px] font-black text-zinc-400 uppercase tracking-widest">Username</span>
                            <span class="text-sm font-bold text-zinc-900">{{ $user->username }}</span>
                        </div>
                        <div class="flex flex-col gap-1.5">
                            <span class="text-[11px] font-black text-zinc-400 uppercase tracking-widest">Nama Lengkap</span>
                            <span class="text-sm font-bold text-zinc-900">{{ $user->nama ?? '-' }}</span>
                        </div>
                        <div class="flex flex-col gap-1.5">
                            <span class="text-[11px] font-black text-zinc-400 uppercase tracking-widest">Email Address</span>
                            <span class="text-sm font-bold text-zinc-900 flex items-center gap-2">
                                {{ $user->email ?? '-' }}
                                @if($user->email)
                                    <i class="fas fa-check-circle text-emerald-500 text-xs" title="Terverifikasi"></i>
                                @endif
                            </span>
                        </div>
                        <div class="flex flex-col gap-1.5">
                            <span class="text-[11px] font-black text-zinc-400 uppercase tracking-widest">Nomor Telepon</span>
                            <span class="text-sm font-bold text-zinc-900">{{ $user->no_telepon ?? '-' }}</span>
                        </div>
                        <div class="flex flex-col gap-1.5">
                            <span class="text-[11px] font-black text-zinc-400 uppercase tracking-widest">Jenis Kelamin</span>
                            <span class="text-sm font-bold text-zinc-900">{{ ucfirst($user->jenis_kelamin ?? '-') }}</span>
                        </div>
                        <div class="flex flex-col gap-1.5">
                            <span class="text-[11px] font-black text-zinc-400 uppercase tracking-widest">Tanggal Lahir</span>
                            <span class="text-sm font-bold text-zinc-900">
                                {{ !empty($user->tanggal_lahir) ? \Carbon\Carbon::parse($user->tanggal_lahir)->translatedFormat('d F Y') : '-' }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- SEKSI BANDING AKUN (KHUSUS CUSTOMER BANNED RINGAN) --}}
                @if(Auth::user()->is_banned && Auth::user()->ban_type === 'ringan')
                <div id="section-banding" class="bg-white rounded-[2rem] shadow-soft border-2 border-amber-100 p-6 sm:p-8">
                    <div class="flex items-center justify-between mb-6 pb-4 border-b border-zinc-100">
                        <h3 class="text-lg font-black text-black flex items-center gap-2">
                            <i class="fas fa-gavel text-amber-500"></i> Pengajuan Banding Akun
                        </h3>
                    </div>

                    @php
                        $appeal = DB::table('tb_banding_akun')->where('user_id', Auth::id())->orderByDesc('created_at')->first();
                    @endphp

                    @if($appeal && $appeal->status === 'pending')
                        <div class="bg-amber-50 border border-amber-200 rounded-2xl p-6 text-center">
                            <i class="fas fa-hourglass-half text-4xl text-amber-500 mb-4"></i>
                            <h4 class="text-base font-black text-amber-900 mb-1">Banding Sedang Diproses</h4>
                            <p class="text-sm font-medium text-amber-700">Admin Pondasikita sedang meninjau alasan dan bukti Anda. Mohon tunggu dalam 1x24 jam.</p>
                        </div>
                    @elseif($appeal && $appeal->status === 'ditolak')
                        <div class="bg-red-50 border border-red-100 rounded-2xl p-5 mb-6">
                            <h5 class="text-sm font-black text-red-800 flex items-center gap-2 mb-2 uppercase">
                                <i class="fas fa-times-circle"></i> Banding Sebelumnya Ditolak
                            </h5>
                            <p class="text-xs font-bold text-red-600 mb-0">Catatan Admin: {{ $appeal->catatan_admin }}</p>
                        </div>
                        <form action="{{ route('account.appeal') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                            @csrf
                            <div>
                                <label class="block text-xs font-black text-zinc-500 uppercase tracking-widest mb-2">Penjelasan Tambahan / Perbaikan</label>
                                <textarea name="alasan_banding" rows="4" class="w-full bg-zinc-50 border border-zinc-200 rounded-xl p-4 text-sm font-bold focus:ring-2 focus:ring-blue-500 outline-none transition-all" placeholder="Tuliskan klarifikasi terbaru Anda di sini..."></textarea>
                            </div>
                            <div>
                                <label class="block text-xs font-black text-zinc-500 uppercase tracking-widest mb-2">Dokumen/Foto Pendukung Baru (Opsional)</label>
                                <input type="file" name="bukti_pendukung" class="w-full text-xs font-bold text-zinc-500 file:mr-4 file:py-2.5 file:px-6 file:rounded-xl file:border-0 file:text-xs file:font-black file:bg-blue-600 file:text-white hover:file:bg-blue-700 cursor-pointer">
                            </div>
                            <button type="submit" class="w-full py-4 bg-red-600 hover:bg-red-700 text-white font-black rounded-xl text-sm uppercase tracking-widest transition-all shadow-lg shadow-red-200">
                                Kirim Ulang Banding Sekarang
                            </button>
                        </form>
                    @else
                        <form action="{{ route('account.appeal') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                            @csrf
                            <p class="text-xs font-semibold text-zinc-500 leading-relaxed mb-4 italic">*Gunakan formulir ini jika Anda merasa pembatasan akun Anda adalah kekeliruan atau Anda sudah memperbaiki kesalahan yang ada.</p>
                            <div>
                                <label class="block text-xs font-black text-zinc-500 uppercase tracking-widest mb-2">Alasan & Klarifikasi Banding</label>
                                <textarea name="alasan_banding" rows="4" class="w-full bg-zinc-50 border border-zinc-200 rounded-xl p-4 text-sm font-bold focus:ring-2 focus:ring-blue-500 outline-none transition-all" placeholder="Tuliskan alasan mengapa akun Anda harus dipulihkan..."></textarea>
                            </div>
                            <div>
                                <label class="block text-xs font-black text-zinc-500 uppercase tracking-widest mb-2">Unggah Bukti Pendukung (Opsional)</label>
                                <input type="file" name="bukti_pendukung" class="w-full text-xs font-bold text-zinc-500 file:mr-4 file:py-2.5 file:px-6 file:rounded-xl file:border-0 file:text-xs file:font-black file:bg-zinc-900 file:text-white hover:file:bg-black cursor-pointer">
                            </div>
                            <button type="submit" class="w-full py-4 bg-blue-600 hover:bg-blue-700 text-white font-black rounded-xl text-sm uppercase tracking-widest transition-all shadow-lg shadow-blue-200">
                                Ajukan Banding Sekarang
                            </button>
                        </form>
                    @endif
                </div>
                @endif
            </div>
        </div>
    </main>

    @include('partials.chat')
    @include('partials.footer')
    
    <script src="{{ asset('assets/js/navbar.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        @if(session('success'))
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: "{{ session('success') }}",
                showConfirmButton: false,
                timer: 3000,
                customClass: { popup: 'rounded-2xl shadow-float border border-zinc-100' }
            });
        @endif

        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: "{{ session('error') }}",
                confirmButtonColor: '#3b82f6',
                customClass: { popup: 'rounded-[2rem]' }
            });
        @endif

        @if($errors->any())
            Swal.fire({
                icon: 'error',
                title: 'Validasi Gagal',
                html: '<ul class="text-left text-xs font-bold text-red-600 list-disc pl-5">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>',
                confirmButtonColor: '#3b82f6',
                customClass: { popup: 'rounded-[2rem]' }
            });
        @endif
    </script>
</body>
</html>
