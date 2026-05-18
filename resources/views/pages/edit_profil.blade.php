<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Profil & Alamat - Pondasikita</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Tailwind CSS CDN --}}
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
                        'sticky-bottom': '0 -10px 40px rgba(0,0,0,0.05)',
                        'map-overlay': '0 10px 30px rgba(0,0,0,0.15)',
                    },
                    animation: {
                        'fade-in-up': 'fadeInUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards',
                        'pulse-slow': 'pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite',
                    },
                    keyframes: {
                        fadeInUp: { '0%': { opacity: 0, transform: 'translateY(20px)' }, '100%': { opacity: 1, transform: 'translateY(0)' } }
                    }
                }
            }
        }
    </script>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    <style>
        html { scroll-behavior: smooth; }
        body { font-family: 'Inter', sans-serif; background-color: #f4f4f5; }

        input[type=number]::-webkit-inner-spin-button,
        input[type=number]::-webkit-outer-spin-button { -webkit-appearance: none; margin: 0; }
        input[type=number] { -moz-appearance: textfield; }
        select { -webkit-appearance: none; -moz-appearance: none; appearance: none; }

        .custom-scrollbar::-webkit-scrollbar { width: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }

        .leaflet-control-zoom { border: none !important; box-shadow: 0 4px 15px rgba(0,0,0,0.1) !important; border-radius: 12px !important; overflow: hidden; margin-top: 20px !important; margin-left: 20px !important; }
        .leaflet-control-zoom a { background: rgba(255,255,255,0.9) !important; color: #3b82f6 !important; border: none !important; width: 40px !important; height: 40px !important; line-height: 40px !important; font-size: 18px !important; backdrop-filter: blur(10px); transition: all 0.3s; }
        .leaflet-control-zoom a:hover { background: #3b82f6 !important; color: white !important; }
        .glass-map-panel { background: rgba(255, 255, 255, 0.85); backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.5); }
        .loader-spin { animation: spin 1s linear infinite; }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
    </style>
</head>

{{-- FIX 1: HAPUS pb-32 agar gak ada space bolong di bawah footer --}}
<body class="text-zinc-800 antialiased pt-[80px] flex flex-col min-h-screen">

    @include('partials.navbar')

{{-- TOP NAVIGATION BAR --}}
    <div class="bg-white border-b border-zinc-200 sticky top-[80px] z-30 shadow-sm hidden md:block">
        <div class="max-w-[1200px] mx-auto px-4 sm:px-6 py-3 flex items-center justify-between">
            <a href="{{ route('profil.index') }}" class="flex items-center gap-2 text-sm font-bold text-zinc-500 hover:text-black transition-colors group">
                <div class="w-8 h-8 rounded-full bg-zinc-100 flex items-center justify-center group-hover:bg-zinc-200 transition-colors">
                    <i class="fas fa-arrow-left"></i>
                </div>
                Kembali ke Profil
            </a>
            <div class="flex items-center gap-3">
                <span class="w-2 h-2 rounded-full bg-blue-600 animate-pulse-slow"></span>
                <span class="text-xs font-black tracking-widest uppercase text-zinc-400">Pengaturan Akun & Geolocation</span>
            </div>
        </div>
    </div>

    <div class="md:hidden px-4 pt-6 pb-2">
        <a href="{{ route('profil.index') }}" class="inline-flex items-center gap-2 text-sm font-bold text-zinc-600">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    {{-- FIX 2: Ganti mb-24 jadi pb-32 supaya konten gak ketutupan floating bar, tapi footer tetep nempel --}}
    <main class="flex-grow w-full max-w-[1200px] mx-auto px-4 sm:px-6 py-6 md:py-10 pb-32">
        
        {{-- BLOK ERROR JIKA VALIDASI GAGAL --}}
        @if ($errors->any())
            <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-r-xl shadow-sm animate-fade-in-up">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle text-red-500 text-lg mr-3"></i>
                    <h3 class="text-red-800 font-bold text-sm">Gagal Menyimpan Data</h3>
                </div>
                <ul class="mt-2 list-disc list-inside text-xs text-red-600 ml-7">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="mb-8 md:mb-12 text-center md:text-left">
            <h1 class="text-3xl lg:text-4xl font-black text-black tracking-tight mb-2">Perbarui Profil & Lokasi</h1>
            <p class="text-sm font-medium text-zinc-500 max-w-2xl">Pastikan data identitas dan titik koordinat pengiriman Anda akurat untuk menghindari kendala logistik.</p>
        </div>

        <form action="{{ route('profil.update') }}" method="POST" enctype="multipart/form-data" id="editProfileForm">
            @csrf

            {{-- Hidden Input Koordinat --}}
            <input type="hidden" name="latitude" id="input_latitude" value="{{ $alamatUtama->latitude ?? '-6.571589' }}">
            <input type="hidden" name="longitude" id="input_longitude" value="{{ $alamatUtama->longitude ?? '107.758736' }}">

            <div class="flex flex-col lg:grid lg:grid-cols-12 gap-8 xl:gap-12 items-start">

                {{-- KOLOM KIRI: FOTO PROFIL --}}
                <div class="w-full lg:col-span-4 lg:sticky lg:top-40 animate-fade-in-up" style="animation-delay: 0.1s;">
                    <div class="bg-white rounded-[2.5rem] shadow-soft border border-zinc-200 p-8 flex flex-col items-center text-center relative overflow-hidden group">
                        <div class="absolute top-0 inset-x-0 h-32 bg-gradient-to-b from-blue-50 to-white"></div>
                        <div class="absolute top-0 right-0 w-32 h-32 bg-blue-100/50 rounded-full blur-3xl -translate-y-1/2 translate-x-1/2"></div>
                        <h3 class="text-xs font-black text-blue-600 uppercase tracking-[0.2em] mb-8 relative z-10">Avatar Pengguna</h3>

                        <div class="relative z-10 w-44 h-44 rounded-full p-2 bg-white shadow-float border border-zinc-100 mb-8 transition-transform group-hover:scale-105 duration-500">
                            <img src="{{ asset('assets/uploads/avatars/' . ($user->profile_picture_url ?? 'person.png')) }}" id="preview-img" class="w-full h-full rounded-full object-cover" onerror="this.src='{{ asset('assets/uploads/avatars/person-icon-1680.png') }}'">
                            <input type="file" name="foto" id="foto-input" class="hidden" accept="image/jpeg, image/png, image/jpg">
                            <button type="button" onclick="document.getElementById('foto-input').click()" class="absolute inset-2 bg-black/50 rounded-full flex flex-col items-center justify-center text-white opacity-0 group-hover:opacity-100 transition-all duration-300 backdrop-blur-sm cursor-pointer border-2 border-dashed border-white/60">
                                <i class="fas fa-camera text-3xl mb-2 translate-y-2 group-hover:translate-y-0 transition-transform"></i>
                                <span class="text-[10px] font-bold uppercase tracking-wider">Ganti Foto</span>
                            </button>
                        </div>

                        <div class="bg-zinc-50 rounded-2xl p-4 w-full">
                            <p class="text-[11px] text-zinc-500 leading-relaxed font-medium">Format: <strong class="text-zinc-800">JPG, PNG</strong><br>Maksimal Ukuran: <strong class="text-red-500">2 MB</strong></p>
                        </div>
                        <button type="button" onclick="document.getElementById('foto-input').click()" class="mt-6 w-full bg-zinc-900 hover:bg-blue-600 text-white font-black py-4 rounded-xl transition-all shadow-lg text-sm flex items-center justify-center gap-2">
                            <i class="fas fa-cloud-upload-alt"></i> Unggah Foto
                        </button>
                    </div>
                </div>

                {{-- KOLOM KANAN: FORM INPUT --}}
                <div class="w-full lg:col-span-8 flex flex-col gap-8 animate-fade-in-up" style="animation-delay: 0.2s;">

                    {{-- CARD 1: INFORMASI DASAR --}}
                    <div class="bg-white rounded-[2.5rem] shadow-soft border border-zinc-200 p-6 sm:p-10 relative overflow-hidden">
                        <div class="absolute top-0 left-0 w-2 h-full bg-zinc-800"></div>
                        <div class="mb-8 border-b border-zinc-100 pb-4">
                            <h2 class="text-xl font-black text-black">Informasi Akun Dasar</h2>
                            <p class="text-sm text-zinc-500 mt-1">Data utama profil Anda.</p>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div class="relative group sm:col-span-2">
                                <label class="block text-[11px] font-black text-zinc-400 uppercase tracking-widest mb-2 ml-1">Nama Profil <span class="text-red-500">*</span></label>
                                <input type="text" name="nama" value="{{ old('nama', $user->nama) }}" required class="w-full bg-zinc-50 border-2 border-zinc-200 text-black text-sm font-semibold rounded-2xl focus:bg-white focus:border-blue-600 focus:ring-4 focus:ring-blue-600/10 px-5 py-3.5 transition-all outline-none">
                            </div>
                            <div class="relative group">
                                <label class="block text-[11px] font-black text-zinc-400 uppercase tracking-widest mb-2 ml-1">Nomor Akun WhatsApp</label>
                                <input type="number" name="no_telepon" value="{{ old('no_telepon', $user->no_telepon) }}" class="w-full bg-zinc-50 border-2 border-zinc-200 text-black text-sm font-semibold rounded-2xl focus:bg-white focus:border-blue-600 focus:ring-4 focus:ring-blue-600/10 px-5 py-3.5 transition-all outline-none">
                            </div>
                            <div class="relative group">
                                <label class="block text-[11px] font-black text-zinc-400 uppercase tracking-widest mb-2 ml-1">Tanggal Lahir</label>
                                <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir', empty($user->tanggal_lahir) ? '' : \Carbon\Carbon::parse($user->tanggal_lahir)->format('Y-m-d')) }}" class="w-full bg-zinc-50 border-2 border-zinc-200 text-black text-sm font-semibold rounded-2xl focus:bg-white focus:border-blue-600 focus:ring-4 focus:ring-blue-600/10 px-5 py-3.5 transition-all outline-none">
                            </div>
                            <div class="relative group sm:col-span-2">
                                <label class="block text-[11px] font-black text-zinc-400 uppercase tracking-widest mb-2 ml-1">Jenis Kelamin</label>
                                <div class="relative">
                                    <select name="jenis_kelamin" class="w-full bg-zinc-50 border-2 border-zinc-200 text-black text-sm font-semibold rounded-2xl focus:bg-white focus:border-blue-600 focus:ring-4 focus:ring-blue-600/10 px-5 py-3.5 transition-all outline-none cursor-pointer">
                                        <option value="">Tidak Disebutkan</option>
                                        <option value="Laki-laki" {{ old('jenis_kelamin', $user->jenis_kelamin) == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                                        <option value="Perempuan" {{ old('jenis_kelamin', $user->jenis_kelamin) == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                                    </select>
                                    <div class="absolute inset-y-0 right-0 pr-5 flex items-center pointer-events-none"><i class="fas fa-chevron-down text-zinc-400 text-xs"></i></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- CARD 2: MAPS BOX --}}
                    <div id="titik-lokasi" class="scroll-mt-24 bg-white rounded-[2.5rem] shadow-premium border border-zinc-200 p-2 sm:p-4 relative overflow-hidden">
                        <div class="p-4 sm:p-6 pb-2 flex flex-col sm:flex-row sm:items-center justify-between gap-4 z-10 relative">
                            <div>
                                <h2 class="text-xl font-black text-black flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                                        <i class="fas fa-map-marked-alt"></i>
                                    </div>
                                    Titik Koordinat Lokasi
                                </h2>
                                <p class="text-xs text-zinc-500 mt-2 font-medium">Geser pin merah di bawah ini ke lokasi rumah/proyek Anda untuk kurir instan/toko.</p>
                            </div>
                            <button type="button" onclick="getLocation()" class="shrink-0 bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-5 rounded-xl shadow-glow transition-all text-xs flex items-center justify-center gap-2 active:scale-95">
                                <i class="fas fa-crosshairs fa-spin-hover"></i> Gunakan GPS Saya
                            </button>
                        </div>
                        <div class="relative w-full h-[350px] sm:h-[450px] rounded-[2rem] overflow-hidden border-4 border-white shadow-inner mt-4 z-10 bg-zinc-100">
                            <div id="map" class="w-full h-full"></div>
                            <div class="absolute bottom-6 left-6 z-[400] glass-map-panel p-4 rounded-2xl shadow-map-overlay min-w-[200px] pointer-events-none transition-all">
                                <div class="flex items-center gap-2 mb-2">
                                    <span class="relative flex h-3 w-3">
                                      <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                      <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500"></span>
                                    </span>
                                    <span class="text-[10px] font-black text-zinc-800 uppercase tracking-widest">Pin Terkunci</span>
                                </div>
                                <div class="text-[10px] font-bold text-zinc-500 flex flex-col gap-1">
                                    <div class="flex justify-between"><span>Lat:</span> <span id="lat-display" class="text-blue-600 font-black">-</span></div>
                                    <div class="flex justify-between"><span>Lng:</span> <span id="lng-display" class="text-blue-600 font-black">-</span></div>
                                </div>
                            </div>
                            <div id="map-loading" class="absolute inset-0 bg-white/70 backdrop-blur-sm z-[500] flex flex-col items-center justify-center opacity-0 pointer-events-none transition-opacity">
                                <i class="fas fa-circle-notch fa-spin text-3xl text-blue-600 mb-3"></i>
                                <span class="text-xs font-bold text-zinc-600 tracking-wider uppercase" id="loading-text">Menganalisis Alamat...</span>
                            </div>
                        </div>
                    </div>

{{-- CARD 3: DETAIL ALAMAT PENGIRIMAN --}}
{{-- Tambahkan border-l-8 border-l-emerald-500 langsung di bungkusnya --}}
<div class="bg-white rounded-[2.5rem] shadow-soft border border-zinc-200 border-l-8 border-l-emerald-500 p-6 sm:p-10 relative mb-4">
    
    {{-- div absolute warna hijaunya DIHAPUS SAJA Bang! --}}
    
    <div class="mb-8 border-b border-zinc-100 pb-4">
                        
                        <div class="mb-8 border-b border-zinc-100 pb-4">
                            <h2 class="text-xl font-black text-black">Detail Pengiriman</h2>
                            <p class="text-sm text-zinc-500 mt-1">Lengkapi data untuk keperluan resi ekspedisi.</p>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            
                            {{-- Nama Penerima --}}
                            <div class="relative group">
                                <label class="block text-[11px] font-black text-zinc-400 uppercase tracking-widest mb-2 ml-1">Nama Penerima</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none"><i class="fas fa-user text-zinc-300"></i></div>
                                    <input type="text" name="nama_penerima" value="{{ old('nama_penerima', $alamatUtama->nama_penerima ?? $user->nama) }}" placeholder="Budi Santoso" class="w-full bg-zinc-50 border-2 border-zinc-200 text-black text-sm font-semibold rounded-2xl focus:bg-white focus:border-blue-600 focus:ring-4 focus:ring-blue-600/10 pl-11 pr-5 py-3.5 transition-all outline-none">
                                </div>
                            </div>

                            {{-- No HP Penerima --}}
                            <div class="relative group">
                                <label class="block text-[11px] font-black text-zinc-400 uppercase tracking-widest mb-2 ml-1">No. Handphone Penerima</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none"><i class="fas fa-phone text-zinc-300"></i></div>
                                    <input type="number" name="telepon_penerima" value="{{ old('telepon_penerima', $alamatUtama->telepon_penerima ?? $user->no_telepon) }}" placeholder="0812..." class="w-full bg-zinc-50 border-2 border-zinc-200 text-black text-sm font-semibold rounded-2xl focus:bg-white focus:border-blue-600 focus:ring-4 focus:ring-blue-600/10 pl-11 pr-5 py-3.5 transition-all outline-none">
                                </div>
                            </div>

                            {{-- Label Alamat --}}
                            <div class="relative group">
                                <label class="block text-[11px] font-black text-zinc-400 uppercase tracking-widest mb-2 ml-1">Label (Rumah/Proyek)</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none"><i class="fas fa-tag text-zinc-300"></i></div>
                                    <input type="text" name="label_alamat" value="{{ old('label_alamat', $alamatUtama->label_alamat ?? 'Rumah') }}" class="w-full bg-zinc-50 border-2 border-zinc-200 text-black text-sm font-semibold rounded-2xl focus:bg-white focus:border-blue-600 focus:ring-4 focus:ring-blue-600/10 pl-11 pr-5 py-3.5 transition-all outline-none">
                                </div>
                            </div>

                            {{-- Kode Pos --}}
                            <div class="relative group">
                                <label class="block text-[11px] font-black text-zinc-400 uppercase tracking-widest mb-2 ml-1">Kode Pos</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none"><i class="fas fa-mail-bulk text-zinc-300"></i></div>
                                    <input type="number" id="kode_pos" name="kode_pos" value="{{ old('kode_pos', $alamatUtama->kode_pos ?? '') }}" placeholder="12345" class="w-full bg-zinc-50 border-2 border-zinc-200 text-black text-sm font-semibold rounded-2xl focus:bg-white focus:border-blue-600 focus:ring-4 focus:ring-blue-600/10 pl-11 pr-5 py-3.5 transition-all outline-none">
                                </div>
                            </div>

                            {{-- Alamat Lengkap Textarea --}}
                            <div class="relative group sm:col-span-2">
                                <label class="block text-[11px] font-black text-zinc-400 uppercase tracking-widest mb-2 ml-1 flex justify-between items-center">
                                    <span>Alamat Lengkap (Patokan Rumah)</span>
                                    <span class="text-[9px] text-blue-500 bg-blue-50 px-2 py-0.5 rounded border border-blue-100"><i class="fas fa-magic"></i> Auto-Fill Active</span>
                                </label>
                                <textarea name="alamat_lengkap" id="alamat_lengkap" rows="3" class="custom-scrollbar w-full bg-white border-2 border-zinc-200 text-black text-sm font-semibold rounded-2xl focus:border-blue-600 focus:ring-4 focus:ring-blue-600/10 px-5 py-4 transition-all outline-none resize-none shadow-inner" placeholder="Alamat otomatis atau ketik manual beserta RT/RW dan patokan rumah di sini...">{{ old('alamat_lengkap', $alamatUtama->alamat_lengkap ?? '') }}</textarea>
                            </div>

                            <div class="relative group sm:col-span-2 pt-4 border-t border-zinc-100">
                                <h4 class="text-xs font-black text-black mb-4"><i class="fas fa-box text-zinc-400 mr-2"></i> Area Pengiriman Ekspedisi</h4>
                            </div>

                            {{-- BITESHIP AUTOCOMPLETE SEARCH --}}
                            <div class="relative group sm:col-span-2">
                                <label class="block text-[11px] font-black text-zinc-400 uppercase tracking-widest mb-2 ml-1">Kecamatan / Kelurahan<span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none" id="search-icon">
                                        <i class="fas fa-search text-zinc-400"></i>
                                    </div>
                                    <input type="text" id="search_area" placeholder="Tolong Masukkan Nama Kecamatan atau Kelurahan..." class="w-full bg-zinc-50 border-2 border-zinc-200 text-black text-sm font-semibold rounded-2xl focus:bg-white focus:border-blue-600 focus:ring-4 focus:ring-blue-600/10 pl-11 pr-5 py-3.5 transition-all outline-none" autocomplete="off">
                                    <input type="hidden" name="area_id" id="area_id" value="{{ old('area_id', $alamatUtama->area_id ?? '') }}" required>
                                    
                                    <ul id="area_results" class="absolute z-[1000] w-full bg-white border border-zinc-200 rounded-xl shadow-lg mt-1 hidden max-h-60 overflow-y-auto custom-scrollbar">
                                    </ul>
                                </div>
                                <p class="text-[10px] text-zinc-500 mt-2 font-medium"><i class="fas fa-info-circle text-blue-500"></i> Klik hasil yang muncul di kotak dropdown agar <strong>area_id</strong> tersimpan dan ongkir bisa dihitung.</p>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

</div>
            {{-- Formnya kita tutup di sini aja Bang --}}
        </form>
    </main>
{{-- NATIVE CSS STICKY BOTTOM BAR --}}
    <div class="sticky bottom-0 w-full bg-white/95 backdrop-blur-xl border-t border-zinc-200 p-4 sm:p-5 shadow-[0_-10px_40px_rgba(0,0,0,0.05)] z-10">
        <div class="max-w-[1200px] mx-auto flex items-center justify-between gap-4">
            <div class="hidden md:flex items-center gap-3">
                <div class="w-10 h-10 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center text-lg"><i class="fas fa-shield-check"></i></div>
                <div>
                    <h4 class="font-bold text-black text-sm leading-none mb-1">Keamanan Data</h4>
                    <p class="text-[10px] text-zinc-500 font-medium">Data dienkripsi secara end-to-end.</p>
                </div>
            </div>
            <div class="flex items-center gap-3 w-full md:w-auto">
                <a href="{{ route('profil.index') }}" class="flex-1 md:flex-none bg-zinc-100 hover:bg-zinc-200 text-zinc-700 font-bold py-3.5 px-6 rounded-xl transition-colors text-center text-sm">Batalkan</a>
                
                {{-- HTML5 TRICK: Pakai form="editProfileForm" agar tombol ini tetap bisa nge-submit form meskipun posisinya ada di luar tag <form>! --}}
                <button type="submit" form="editProfileForm" id="btnSubmit" class="flex-1 md:flex-none bg-black hover:bg-blue-600 text-white font-black py-3.5 px-10 rounded-xl shadow-glow hover:-translate-y-1 transition-all duration-300 text-sm flex items-center justify-center gap-2">
                    <i class="fas fa-save"></i> Simpan Data Pribadi
                </button>
            </div>
        </div>
    </div>

    @include('partials.footer')

    {{-- ======================================================== --}}
    {{-- LOGIKA JAVASCRIPT: LEAFLET & BITESHIP AUTOCOMPLETE       --}}
    {{-- ======================================================== --}}

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // --- 1. SETUP UI INTERAKSI BIASA ---
            const fotoInput = document.getElementById('foto-input');
            const previewImg = document.getElementById('preview-img');

            fotoInput.addEventListener('change', function(event) {
                const [file] = event.target.files;
                if (file) {
                    previewImg.style.opacity = 0;
                    previewImg.style.transform = 'scale(0.9)';
                    setTimeout(() => {
                        previewImg.src = URL.createObjectURL(file);
                        previewImg.style.opacity = 1;
                        previewImg.style.transform = 'scale(1)';
                    }, 200);
                }
            });

            const form = document.getElementById('editProfileForm');
            const btnSubmit = document.getElementById('btnSubmit');
            form.addEventListener('submit', function() {
                setTimeout(() => {
                    btnSubmit.disabled = true;
                    btnSubmit.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';
                    btnSubmit.classList.add('opacity-70', 'cursor-not-allowed');
                    btnSubmit.classList.remove('hover:-translate-y-1', 'shadow-glow');
                }, 10);
            });

            // --- 2. BITESHIP AUTOCOMPLETE SEARCH ---
            const searchInput = document.getElementById('search_area');
            const areaIdInput = document.getElementById('area_id');
            const resultsList = document.getElementById('area_results');
            const searchIcon = document.getElementById('search-icon');
            let debounceTimer;

            async function searchBiteshipAreas(keyword) {
                searchIcon.innerHTML = '<i class="fas fa-spinner loader-spin text-blue-500"></i>';
                
                try {
                    const response = await fetch(`/api/biteship/search?q=${encodeURIComponent(keyword)}`);
                    const data = await response.json();
                    
                    resultsList.innerHTML = '';
                    
                    if(data.areas && data.areas.length > 0) {
                        data.areas.forEach(area => {
                            const li = document.createElement('li');
                            li.className = 'px-4 py-3 hover:bg-blue-50 cursor-pointer border-b border-zinc-100 last:border-0 transition-colors';
                            li.innerHTML = `
                                <div class="font-bold text-sm text-zinc-800">${area.name}</div>
                                <div class="text-[11px] text-zinc-500">${area.administrative_division_level_2_name}, ${area.administrative_division_level_1_name}</div>
                            `;
                            li.addEventListener('click', () => {
                                searchInput.value = `${area.name}, ${area.administrative_division_level_2_name}`;
                                areaIdInput.value = area.id; 
                                resultsList.classList.add('hidden');
                            });
                            resultsList.appendChild(li);
                        });
                        resultsList.classList.remove('hidden');
                    } else {
                        resultsList.innerHTML = '<li class="px-4 py-3 text-sm text-zinc-500 text-center">Area tidak ditemukan</li>';
                        resultsList.classList.remove('hidden');
                    }
                } catch (error) {
                    console.error("Gagal mengambil data Biteship", error);
                } finally {
                    searchIcon.innerHTML = '<i class="fas fa-search text-zinc-400"></i>';
                }
            }

            searchInput.addEventListener('input', function() {
                const keyword = this.value.trim();
                clearTimeout(debounceTimer);
                
                if (keyword.length < 3) {
                    resultsList.classList.add('hidden');
                    return;
                }

                debounceTimer = setTimeout(() => {
                    searchBiteshipAreas(keyword);
                }, 800);
            });

            document.addEventListener('click', function(e) {
                if (!searchInput.contains(e.target) && !resultsList.contains(e.target)) {
                    resultsList.classList.add('hidden');
                }
            });

            // --- 3. LOGIKA MAPS LEAFLET ---
            let currentLat = parseFloat(document.getElementById('input_latitude').value) || -6.571589;
            let currentLng = parseFloat(document.getElementById('input_longitude').value) || 107.758736;

            const map = L.map('map', { zoomControl: false, scrollWheelZoom: false }).setView([currentLat, currentLng], 14);
            L.control.zoom({ position: 'topleft' }).addTo(map);
            L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
                attribution: '&copy; OpenStreetMap contributors', maxZoom: 19
            }).addTo(map);

            const customIcon = L.divIcon({
                className: 'custom-pin',
                html: `<div style="background: #e11d48; width: 24px; height: 24px; border-radius: 50% 50% 50% 0; transform: rotate(-45deg); border: 4px solid white; box-shadow: 0 5px 15px rgba(0,0,0,0.3);"></div>`,
                iconSize: [24, 24], iconAnchor: [12, 24]
            });
            const marker = L.marker([currentLat, currentLng], { draggable: true, icon: customIcon }).addTo(map);

            document.getElementById('lat-display').innerText = currentLat.toFixed(6);
            document.getElementById('lng-display').innerText = currentLng.toFixed(6);

            // --- 4. ENGINE GEOCODING (OPENSTREETMAP -> BITESHIP) ---
            const loadingOverlay = document.getElementById('map-loading');
            const loadingText = document.getElementById('loading-text');

            async function performGeocoding(lat, lng) {
                loadingOverlay.style.opacity = '1';
                loadingText.innerText = "Satelit Melacak Lokasi...";

                try {
                    const res = await fetch(`https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${lat}&lon=${lng}&accept-language=id`);
                    const data = await res.json();

                    if(data.display_name) {
                        document.getElementById('alamat_lengkap').value = data.display_name;
                        if(data.address.postcode) document.getElementById('kode_pos').value = data.address.postcode;

                        let dName = data.address.suburb || data.address.village || data.address.town || data.address.district || '';
                        if(dName) {
                            searchInput.value = dName;
                            searchBiteshipAreas(dName);
                        }
                    }
                } catch (error) {
                    console.error("Geocoding Error:", error);
                } finally {
                    loadingOverlay.style.opacity = '0';
                }
            }

            marker.on('dragend', function(e) {
                const position = marker.getLatLng();
                document.getElementById('input_latitude').value = position.lat;
                document.getElementById('input_longitude').value = position.lng;
                document.getElementById('lat-display').innerText = position.lat.toFixed(6);
                document.getElementById('lng-display').innerText = position.lng.toFixed(6);

                map.panTo(position);
                performGeocoding(position.lat, position.lng);
            });

            window.getLocation = function() {
                if (navigator.geolocation) {
                    loadingOverlay.style.opacity = '1';
                    loadingText.innerText = "Mencari Sinyal GPS...";
                    navigator.geolocation.getCurrentPosition(
                        (position) => {
                            const lat = position.coords.latitude;
                            const lng = position.coords.longitude;

                            map.setView([lat, lng], 16);
                            marker.setLatLng([lat, lng]);

                            document.getElementById('input_latitude').value = lat;
                            document.getElementById('input_longitude').value = lng;
                            document.getElementById('lat-display').innerText = lat.toFixed(6);
                            document.getElementById('lng-display').innerText = lng.toFixed(6);

                            performGeocoding(lat, lng);
                        },
                        (error) => {
                            loadingOverlay.style.opacity = '0';
                            alert('Gagal mendeteksi GPS. Pastikan izin lokasi di browser Anda menyala.');
                        },
                        { enableHighAccuracy: true, timeout: 5000, maximumAge: 0 }
                    );
                } else {
                    alert("Browser Anda tidak mendukung deteksi lokasi.");
                }
            };

            setTimeout(() => { map.invalidateSize(); }, 500);
        }); // <--- FIX 3: PENUTUP DOMContentLoaded YANG HILANG!

        // ========================================================
        // LOGIKA PINTAR: FLOATING BAR BERHENTI DI ATAS FOOTER
        // ========================================================
        document.addEventListener('scroll', function() {
            const floatingBar = document.getElementById('floating-bottom-bar');
            const footer = document.querySelector('footer'); 

            if (floatingBar && footer) {
                const footerRect = footer.getBoundingClientRect();
                const windowHeight = window.innerHeight;

                // Jika bagian atas footer mulai masuk/terlihat di layar (viewport)
                if (footerRect.top < windowHeight) {
                    // Dorong floating bar ke atas secara instan sejauh porsi footer yang terlihat
                    floatingBar.style.transform = `translateY(-${windowHeight - footerRect.top}px)`;
                } else {
                    // Kembalikan ke posisi normal (nempel di bawah layar)
                    floatingBar.style.transform = 'translateY(0)';
                }
            }
        });
    </script>
</body>
</html>