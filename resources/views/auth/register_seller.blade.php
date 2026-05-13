<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buka Toko - Pondasikita Enterprise</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Tailwind CSS CDN + Config Dewa --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'] },
                    colors: {
                        brand: { 50: '#eff6ff', 100: '#dbeafe', 500: '#3b82f6', 600: '#2563eb', 900: '#1e3a8a' },
                    },
                    boxShadow: {
                        'glow': '0 0 20px rgba(37,99,235,0.3)',
                        'soft': '0 4px 40px -4px rgba(0,0,0,0.03)',
                        'map-overlay': '0 10px 30px rgba(0,0,0,0.15)',
                    },
                    animation: {
                        'fade-in-up': 'fadeInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards',
                    },
                    keyframes: {
                        fadeInUp: { '0%': { opacity: 0, transform: 'translateY(20px)' }, '100%': { opacity: 1, transform: 'translateY(0)' } },
                        spin: { '0%': { transform: 'rotate(0deg)' }, '100%': { transform: 'rotate(360deg)' } }
                    }
                }
            }
        }
    </script>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
    {{-- LEAFLET CSS --}}
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    <style>
        html { scroll-behavior: smooth; }

        /* Custom Scrollbar untuk area form */
        .custom-scrollbar::-webkit-scrollbar { width: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }

        /* Glassmorphism Panel */
        .glass-panel {
            background: linear-gradient(145deg, rgba(255,255,255,0.05) 0%, rgba(255,255,255,0.01) 100%);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255,255,255,0.05);
        }

        /* Hilangkan panah di input number */
        input[type=number]::-webkit-inner-spin-button,
        input[type=number]::-webkit-outer-spin-button { -webkit-appearance: none; margin: 0; }

        /* Leaflet Map Custom Styling */
        .leaflet-control-zoom { border: none !important; box-shadow: 0 4px 15px rgba(0,0,0,0.1) !important; border-radius: 12px !important; overflow: hidden; margin-top: 20px !important; margin-left: 20px !important; }
        .leaflet-control-zoom a { background: rgba(255,255,255,0.9) !important; color: #3b82f6 !important; border: none !important; width: 40px !important; height: 40px !important; line-height: 40px !important; font-size: 18px !important; backdrop-filter: blur(10px); transition: all 0.3s; }
        .leaflet-control-zoom a:hover { background: #3b82f6 !important; color: white !important; }
        .glass-map-panel { background: rgba(255, 255, 255, 0.85); backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.5); }
        
        .loader-spin { animation: spin 1s linear infinite; }
    </style>
</head>
<body class="bg-white font-sans text-zinc-900 antialiased flex flex-col lg:flex-row min-h-screen overflow-hidden">

    {{-- ======================================================= --}}
    {{-- KIRI: SISI VISUAL (STICKY BRANDING) --}}
    {{-- ======================================================= --}}
    <div class="hidden lg:flex w-5/12 bg-zinc-950 relative flex-col justify-between p-12 z-0">

        {{-- Ambient Light FX --}}
        <div class="absolute inset-0 overflow-hidden pointer-events-none z-0 mix-blend-screen">
            <div class="absolute top-0 left-0 w-[600px] h-[600px] bg-blue-600/20 rounded-full blur-[120px] -translate-x-1/2 -translate-y-1/2"></div>
            <div class="absolute bottom-0 right-0 w-[500px] h-[500px] bg-indigo-600/20 rounded-full blur-[100px] translate-x-1/3 translate-y-1/3"></div>
        </div>

        <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')] opacity-[0.05] pointer-events-none z-10"></div>

        <div class="relative z-20 animate-fade-in-up">
            <a href="{{ url('/') }}" class="inline-flex items-center justify-center p-3 glass-panel rounded-2xl shadow-2xl hover:scale-105 transition-transform duration-500 mb-12">
                <img src="{{ asset('assets/image/Pondasikita.com.png') }}" alt="Logo" class="h-8 w-auto object-contain drop-shadow-md" onerror="this.outerHTML='<div class=\'text-white font-black text-xl px-2\'>P<span class=\'text-blue-600\'>.</span></div>'">
            </a>

            <h1 class="text-4xl xl:text-5xl font-black text-white leading-[1.1] tracking-tight mb-6">
                Ekspansi Bisnis<br>Material Anda<br>
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-indigo-400">Dimulai Dari Sini.</span>
            </h1>

            <p class="text-zinc-400 text-base font-medium leading-relaxed max-w-sm mb-12">
                Bergabunglah dengan ekosistem B2B terbesar. Capai ribuan kontraktor, kelola invoice digital, dan pantau logistik dalam satu platform pintar.
            </p>

            <div class="space-y-4">
                <div class="glass-panel p-4 rounded-2xl flex items-center gap-4">
                    <div class="w-10 h-10 rounded-xl bg-blue-500/20 text-blue-400 flex items-center justify-center shrink-0 border border-blue-500/20">
                        <i class="fas fa-chart-pie"></i>
                    </div>
                    <div>
                        <h4 class="text-white font-bold text-sm">Akses Pasar B2B</h4>
                    </div>
                </div>
                <div class="glass-panel p-4 rounded-2xl flex items-center gap-4">
                    <div class="w-10 h-10 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center shrink-0 border border-emerald-500/20">
                        <i class="fas fa-shield-check"></i>
                    </div>
                    <div>
                        <h4 class="text-white font-bold text-sm">Pembayaran Terjamin</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="relative z-20 text-zinc-600 text-[10px] font-black uppercase tracking-widest">
            © {{ date('Y') }} Pondasikita Enterprise
        </div>
    </div>

    {{-- ======================================================= --}}
    {{-- KANAN: SISI FORMULIR (SCROLLABLE) --}}
    {{-- ======================================================= --}}
    <div class="w-full lg:w-7/12 overflow-y-auto h-screen custom-scrollbar relative bg-white z-10 shadow-[-20px_0_40px_rgba(0,0,0,0.05)]">
        <div class="p-6 sm:p-12 lg:p-16 max-w-2xl mx-auto animate-fade-in-up">

            {{-- Logo Mobile --}}
            <div class="lg:hidden mb-8">
                <img src="{{ asset('assets/image/Pondasikita.com.png') }}" alt="Logo" class="h-8 w-auto" onerror="this.outerHTML='<div class=\'text-black font-black text-2xl\'>Pondasikita<span class=\'text-blue-600\'>.</span></div>'">
            </div>

            <div class="mb-10">
                <h2 class="text-3xl font-black text-black tracking-tight mb-2">Form Registrasi Toko</h2>
                <p class="text-zinc-500 font-medium text-sm">
                    Sudah memiliki akun toko? <a href="{{ route('seller.login') }}" class="text-blue-600 font-bold hover:underline transition-all">Masuk ke Dashboard</a>
                </p>
            </div>

            <form action="{{ route('seller.register.process') }}" method="POST" enctype="multipart/form-data" id="registerSellerForm" class="space-y-10">
                @csrf
                <input type="hidden" name="level" value="seller">
                
                {{-- Hidden Inputs Lokasi Toko GPS --}}
                <input type="hidden" name="latitude" id="input_latitude" value="{{ old('latitude', '-6.571589') }}">
                <input type="hidden" name="longitude" id="input_longitude" value="{{ old('longitude', '107.758736') }}">

                {{-- SECTION 1: INFO PEMILIK --}}
                <div>
                    <div class="flex items-center gap-3 mb-6 border-b border-zinc-100 pb-3">
                        <div class="w-8 h-8 rounded-full bg-blue-50 text-blue-600 font-black flex items-center justify-center text-sm">1</div>
                        <h3 class="text-lg font-black text-black">Informasi Pemilik</h3>
                    </div>

                    <div class="space-y-5">
                        <div class="relative group">
                            <label class="block text-[10px] font-black text-zinc-400 uppercase tracking-widest mb-1.5 ml-1">Nama Lengkap (Sesuai KTP) <span class="text-red-500">*</span></label>
                            <input type="text" name="nama_pemilik" required value="{{ old('nama_pemilik') }}" placeholder="Budi Santoso" class="w-full bg-zinc-50 border-2 border-zinc-200 text-black text-sm font-semibold rounded-2xl focus:bg-white focus:border-blue-600 focus:ring-4 focus:ring-blue-600/10 px-5 py-4 transition-all outline-none">
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div class="relative group">
                                <label class="block text-[10px] font-black text-zinc-400 uppercase tracking-widest mb-1.5 ml-1">Username Login <span class="text-red-500">*</span></label>
                                <input type="text" name="username" required value="{{ old('username') }}" placeholder="budimaterial" class="w-full bg-zinc-50 border-2 border-zinc-200 text-black text-sm font-semibold rounded-2xl focus:bg-white focus:border-blue-600 focus:ring-4 focus:ring-blue-600/10 px-5 py-4 transition-all outline-none lowercase">
                            </div>
                            <div class="relative group">
                                <label class="block text-[10px] font-black text-zinc-400 uppercase tracking-widest mb-1.5 ml-1">Email Profesional <span class="text-red-500">*</span></label>
                                <input type="email" name="email" required value="{{ old('email') }}" placeholder="budi@perusahaan.com" class="w-full bg-zinc-50 border-2 border-zinc-200 text-black text-sm font-semibold rounded-2xl focus:bg-white focus:border-blue-600 focus:ring-4 focus:ring-blue-600/10 px-5 py-4 transition-all outline-none">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div class="relative group">
                                <label class="block text-[10px] font-black text-zinc-400 uppercase tracking-widest mb-1.5 ml-1">Kata Sandi <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <input type="password" name="password" id="password" required placeholder="Min. 6 Karakter" class="w-full bg-zinc-50 border-2 border-zinc-200 text-black text-sm font-semibold rounded-2xl focus:bg-white focus:border-blue-600 focus:ring-4 focus:ring-blue-600/10 pl-5 pr-12 py-4 transition-all outline-none">
                                    <button type="button" onclick="toggleVisibility('password', 'eye1')" class="absolute inset-y-0 right-0 pr-4 flex items-center text-zinc-400 hover:text-black">
                                        <i class="fas fa-eye" id="eye1"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="relative group">
                                <label class="block text-[10px] font-black text-zinc-400 uppercase tracking-widest mb-1.5 ml-1">No. Handphone Pribadi <span class="text-red-500">*</span></label>
                                <input type="number" name="no_telepon" required value="{{ old('no_telepon') }}" placeholder="0812..." class="w-full bg-zinc-50 border-2 border-zinc-200 text-black text-sm font-semibold rounded-2xl focus:bg-white focus:border-blue-600 focus:ring-4 focus:ring-blue-600/10 px-5 py-4 transition-all outline-none">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- SECTION 2: INFO TOKO & LOKASI --}}
                <div>
                    <div class="flex items-center gap-3 mb-6 border-b border-zinc-100 pb-3">
                        <div class="w-8 h-8 rounded-full bg-zinc-900 text-white font-black flex items-center justify-center text-sm">2</div>
                        <h3 class="text-lg font-black text-black">Profil Bisnis / Toko</h3>
                    </div>

                    <div class="space-y-5">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div class="relative group">
                                <label class="block text-[10px] font-black text-zinc-400 uppercase tracking-widest mb-1.5 ml-1">Nama Toko Material <span class="text-red-500">*</span></label>
                                <input type="text" name="nama_toko" required value="{{ old('nama_toko') }}" placeholder="TB. Makmur Jaya" class="w-full bg-zinc-50 border-2 border-zinc-200 text-black text-sm font-semibold rounded-2xl focus:bg-white focus:border-blue-600 focus:ring-4 focus:ring-blue-600/10 px-5 py-4 transition-all outline-none">
                            </div>
                            <div class="relative group">
                                <label class="block text-[10px] font-black text-zinc-400 uppercase tracking-widest mb-1.5 ml-1">Telepon / WA Toko <span class="text-red-500">*</span></label>
                                <input type="number" name="telepon_toko" required value="{{ old('telepon_toko') }}" placeholder="0821..." class="w-full bg-zinc-50 border-2 border-zinc-200 text-black text-sm font-semibold rounded-2xl focus:bg-white focus:border-blue-600 focus:ring-4 focus:ring-blue-600/10 px-5 py-4 transition-all outline-none">
                            </div>
                        </div>

                        {{-- MAPS TITIK LOKASI TOKO --}}
                        <div class="bg-white rounded-[2rem] border border-zinc-200 p-2 relative overflow-hidden mt-6 mb-2">
                            <div class="p-3 sm:p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4 z-10 relative">
                                <div>
                                    <h2 class="text-base font-black text-black flex items-center gap-2">
                                        <i class="fas fa-map-marked-alt text-blue-600"></i> Titik Lokasi GPS Toko
                                    </h2>
                                    <p class="text-[11px] text-zinc-500 mt-1 font-medium">Geser pin merah ke lokasi persis gudang material Anda untuk kurir sistem.</p>
                                </div>
                                <button type="button" onclick="getLocation()" class="shrink-0 bg-zinc-900 hover:bg-blue-600 text-white font-bold py-2.5 px-4 rounded-xl transition-all text-xs flex items-center justify-center gap-2">
                                    <i class="fas fa-crosshairs"></i> Gunakan GPS HP
                                </button>
                            </div>

                            <div class="relative w-full h-[280px] rounded-[1.5rem] overflow-hidden border-2 border-white shadow-inner bg-zinc-100 z-10">
                                <div id="map" class="w-full h-full"></div>
                                <div class="absolute bottom-4 left-4 z-[400] glass-map-panel p-3 rounded-xl shadow-map-overlay">
                                    <div class="text-[10px] font-bold text-zinc-600 flex flex-col gap-0.5">
                                        <div class="flex gap-2"><span>Lat:</span> <span id="lat-display" class="text-blue-600 font-black">-</span></div>
                                        <div class="flex gap-2"><span>Lng:</span> <span id="lng-display" class="text-blue-600 font-black">-</span></div>
                                    </div>
                                </div>
                                <div id="map-loading" class="absolute inset-0 bg-white/80 backdrop-blur-sm z-[500] flex flex-col items-center justify-center opacity-0 pointer-events-none transition-opacity">
                                    <i class="fas fa-circle-notch fa-spin text-2xl text-blue-600 mb-2"></i>
                                    <span class="text-[10px] font-bold text-zinc-600 uppercase tracking-wider" id="loading-text">Menyelaraskan Lokasi...</span>
                                </div>
                            </div>
                        </div>

                        <div class="relative group">
                            <label class="block text-[10px] font-black text-zinc-400 uppercase tracking-widest mb-1.5 ml-1">Alamat Gudang Fisik <span class="text-red-500">*</span></label>
                            <textarea name="alamat_toko" id="alamat_toko" rows="3" required placeholder="Nama Jalan, Nomor Bangunan, RT/RW, Patokan..." class="custom-scrollbar w-full bg-zinc-50 border-2 border-zinc-200 text-black text-sm font-semibold rounded-2xl focus:bg-white focus:border-blue-600 focus:ring-4 focus:ring-blue-600/10 px-5 py-4 transition-all outline-none resize-none">{{ old('alamat_toko') }}</textarea>
                        </div>

                        {{-- BITESHIP AUTOCOMPLETE SEARCH --}}
                        <div class="relative group mt-2">
                            <label class="block text-[10px] font-black text-zinc-400 uppercase tracking-widest mb-1.5 ml-1">Wilayah Ekspedisi<span class="text-red-500">*</span></label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none" id="search-icon">
                                    <i class="fas fa-search text-zinc-400"></i>
                                </div>
                                <input type="text" id="search_area" placeholder="Ketik HANYA nama Kecamatan atau Kelurahan lalu pilih..." class="w-full bg-zinc-50 border-2 border-zinc-200 text-black text-sm font-semibold rounded-2xl focus:bg-white focus:border-blue-600 focus:ring-4 focus:ring-blue-600/10 pl-11 pr-5 py-4 transition-all outline-none" autocomplete="off">
                                <input type="hidden" name="area_id" id="area_id" value="{{ old('area_id') }}">
                                
                                <ul id="area_results" class="absolute z-[1000] w-full bg-white border border-zinc-200 rounded-xl shadow-lg mt-1 hidden max-h-60 overflow-y-auto custom-scrollbar">
                                    </ul>
                            </div>
                            <p class="text-[10px] text-zinc-500 mt-2 ml-1 font-medium"><i class="fas fa-info-circle text-blue-500"></i> Sangat Krusial: Klik nama daerah yang muncul di list dropdown agar sistem ongkos kirim toko Anda berfungsi.</p>
                        </div>

                        {{-- Custom File Upload --}}
                        <div class="relative group mt-4">
                            <label class="block text-[10px] font-black text-zinc-400 uppercase tracking-widest mb-1.5 ml-1">Logo Toko (Opsional)</label>
                            <div class="w-full bg-zinc-50 border-2 border-dashed border-zinc-300 rounded-2xl p-4 flex items-center gap-4 transition-colors hover:border-blue-500 relative">
                                <input type="file" name="logo_toko" id="logo_toko" accept="image/*" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" onchange="previewImage(this)">
                                <div class="w-12 h-12 rounded-xl bg-white border border-zinc-200 flex items-center justify-center shrink-0 overflow-hidden" id="logo-preview-container">
                                    <i class="fas fa-image text-zinc-300 text-lg" id="logo-icon"></i>
                                    <img id="logo-preview" class="w-full h-full object-cover hidden">
                                </div>
                                <div>
                                    <h4 class="text-sm font-bold text-zinc-700" id="file-name">Unggah Logo Anda</h4>
                                    <p class="text-[10px] font-medium text-zinc-400 mt-0.5">Klik area ini. Maksimal 2MB (JPG/PNG).</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Action Area --}}
                <div class="pt-4 pb-10">
                    <button type="submit" id="btnSubmit" class="w-full bg-black hover:bg-blue-600 text-white font-black py-4 rounded-2xl transition-all duration-300 shadow-[0_8px_20px_rgba(0,0,0,0.15)] hover:shadow-glow hover:-translate-y-1 text-base flex items-center justify-center gap-2">
                        Kirim Pendaftaran Toko <i class="fas fa-arrow-right"></i>
                    </button>
                    <p class="text-[10px] text-zinc-400 text-center mt-5 font-medium leading-relaxed max-w-sm mx-auto">
                        Dengan mendaftar, Anda menyetujui Perjanjian Kemitraan dan Kebijakan Privasi Penjual Pondasikita.
                    </p>
                </div>

            </form>
        </div>
    </div>

    {{-- SCRIPTS --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // 1. Password Toggle
            window.toggleVisibility = function(inputId, iconId) {
                const input = document.getElementById(inputId);
                const icon = document.getElementById(iconId);
                if (input.type === 'password') {
                    input.type = 'text'; icon.classList.replace('fa-eye', 'fa-eye-slash');
                } else {
                    input.type = 'password'; icon.classList.replace('fa-eye-slash', 'fa-eye');
                }
            }

            // 2. Custom Image Preview
            window.previewImage = function(input) {
                const preview = document.getElementById('logo-preview');
                const icon = document.getElementById('logo-icon');
                const fileName = document.getElementById('file-name');

                if (input.files && input.files[0]) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        preview.src = e.target.result;
                        preview.classList.remove('hidden');
                        icon.classList.add('hidden');
                        fileName.innerText = input.files[0].name;
                        fileName.classList.add('text-blue-600');
                    }
                    reader.readAsDataURL(input.files[0]);
                }
            }

            // 3. Loading State pada Tombol Submit
            document.getElementById('registerSellerForm').addEventListener('submit', function(e) {
                const areaInput = document.getElementById('area_id').value;
                if(!areaInput) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'warning', title: 'Data Belum Lengkap', text: "Mohon ketik dan klik nama Kecamatan/Kelurahan di kolom Wilayah Ekspedisi.",
                        confirmButtonColor: '#000000', customClass: { popup: 'rounded-[2rem]', confirmButton: 'rounded-xl px-8 py-3 font-bold' }
                    });
                    return;
                }

                const btn = document.getElementById('btnSubmit');
                setTimeout(() => {
                    btn.disabled = true;
                    btn.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> Memproses Data...';
                    btn.classList.add('opacity-80', 'cursor-not-allowed');
                    btn.classList.remove('hover:-translate-y-1', 'hover:bg-blue-600', 'hover:shadow-glow');
                }, 10);
            });

            // 4. MAPS LEAFLET (Lokasi Gudang)
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

            const loadingOverlay = document.getElementById('map-loading');
            const loadingText = document.getElementById('loading-text');

            async function performGeocoding(lat, lng) {
                loadingOverlay.style.opacity = '1';
                loadingText.innerText = "Membaca Peta...";

                try {
                    const res = await fetch(`https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${lat}&lon=${lng}&accept-language=id`);
                    const data = await res.json();

                    if(data.display_name) {
                        document.getElementById('alamat_toko').value = data.display_name;

                        let dName = data.address.suburb || data.address.village || data.address.town || data.address.district || '';
                        if(dName) {
                            document.getElementById('search_area').value = dName;
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
                            alert('Gagal mendeteksi GPS HP/Browser. Pastikan izin lokasi (location) menyala.');
                        },
                        { enableHighAccuracy: true, timeout: 5000, maximumAge: 0 }
                    );
                }
            };
            setTimeout(() => { map.invalidateSize(); }, 500);

            // 5. BITESHIP AUTOCOMPLETE SEARCH
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
                debounceTimer = setTimeout(() => { searchBiteshipAreas(keyword); }, 800);
            });

            document.addEventListener('click', function(e) {
                if (!searchInput.contains(e.target) && !resultsList.contains(e.target)) {
                    resultsList.classList.add('hidden');
                }
            });

            // 6. SweetAlert Flash Messages System
            @if(session('success'))
                Swal.fire({
                    icon: 'success', title: 'Pendaftaran Berhasil!', text: "{{ session('success') }}",
                    confirmButtonColor: '#000000', confirmButtonText: 'Login Sekarang', allowOutsideClick: false,
                    customClass: { popup: 'rounded-[2rem]', confirmButton: 'rounded-xl px-8 py-3 font-bold' }
                }).then((result) => {
                    if (result.isConfirmed) window.location.href = "{{ route('seller.login') }}";
                });
            @endif

            @if(session('error'))
                Swal.fire({
                    icon: 'error', title: 'Gagal!', text: "{{ session('error') }}", confirmButtonColor: '#000000',
                    customClass: { popup: 'rounded-[2rem]', confirmButton: 'rounded-xl px-8 py-3 font-bold' }
                });
            @endif

            @if($errors->any())
                let errorMessages = {!! json_encode($errors->all()) !!};
                Swal.fire({
                    icon: 'warning', title: 'Periksa Data Anda',
                    html: `<div style="text-align: left; font-size: 0.85rem; color: #dc2626; background: #fef2f2; padding: 15px; border-radius: 12px; border: 1px solid #fecaca; margin-top: 10px;">
                            <ul style="padding-left: 20px; margin: 0; font-weight: 500; line-height: 1.5;">
                                ${errorMessages.map(err => `<li>${err}</li>`).join('')}
                            </ul></div>`,
                    confirmButtonColor: '#000000',
                    customClass: { popup: 'rounded-[2rem]', confirmButton: 'rounded-xl px-8 py-3 font-bold' }
                });
            @endif

        });
    </script>
</body>
</html>