<!DOCTYPE html>
<html lang="id">
<head>
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
<body class="text-zinc-800 antialiased pt-[80px] pb-20">

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

    <main class="max-w-[1100px] mx-auto px-4 sm:px-6 py-8 lg:py-12">

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
                            {{-- Ganti route di bawah dengan rute pendaftaran seller Anda yang sebenarnya --}}
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

                {{-- ALAMAT PENGIRIMAN UTAMA --}}
                <div class="bg-white rounded-[2rem] shadow-soft border border-zinc-200 p-6 sm:p-8">
                    <div class="flex items-center justify-between mb-6 pb-4 border-b border-zinc-100">
                        <h3 class="text-lg font-black text-black flex items-center gap-2">
                            <i class="fas fa-map-marked-alt text-blue-600"></i> Alamat Pengiriman
                        </h3>
                        <a href="{{ route('profil.edit') }}#titik-lokasi"
                        class="text-xs font-bold text-blue-600 hover:text-blue-800 transition-colors">
                        Ubah Alamat
                        </a>
                    </div>

                    @if(strip_tags($alamatLengkapFormatted) !== '')
                        <div class="bg-zinc-50 border border-zinc-200 rounded-2xl p-5 flex gap-4">
                            <div class="w-10 h-10 rounded-full bg-white border border-zinc-200 shadow-sm flex items-center justify-center shrink-0">
                                <i class="fas fa-building text-blue-600"></i>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-black mb-1">Alamat Utama</h4>
                                <p class="text-sm font-medium text-zinc-600 leading-relaxed">
                                    {!! $alamatLengkapFormatted !!}
                                </p>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-8 bg-zinc-50 rounded-2xl border border-dashed border-zinc-300">
                            <i class="fas fa-map-pin text-3xl text-zinc-300 mb-3"></i>
                            <p class="text-sm font-semibold text-zinc-500">Anda belum mengatur alamat pengiriman.</p>
                            <button class="mt-3 text-xs font-bold bg-white border border-zinc-200 text-black px-4 py-2 rounded-lg shadow-sm hover:bg-zinc-100 transition-colors">Tambah Alamat Sekarang</button>
                        </div>
                    @endif
                </div>

            </div>
        </div>
    </main>
        {{-- Tambahkan baris ini --}}
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
    </script>
</body>
</html>
