<!DOCTYPE html>
<html lang="id">
<head>
    <link rel="icon" href="{{ asset('logopondasikita.png') }}" type="image/png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buat Akun - Pondasikita B2B</title>

    {{-- Tailwind CSS CDN + Config Dewa --}}
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
        /* Scrollbar untuk Modal T&C */
        .tnc-scrollbar::-webkit-scrollbar { width: 6px; }
        .tnc-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .tnc-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        /* Checkbox Modal Custom */
        .modal-checkbox { appearance: none; background-color: #fff; margin: 0; width: 1.5rem; height: 1.5rem; border: 2px solid #e2e8f0; border-radius: 0.5rem; display: grid; place-content: center; cursor: pointer; transition: all 0.2s ease-in-out; }
        .modal-checkbox::before { content: ""; width: 0.75rem; height: 0.75rem; transform: scale(0); transition: 120ms transform ease-in-out; background-color: white; transform-origin: center; clip-path: polygon(14% 44%, 0 65%, 50% 100%, 100% 16%, 80% 0%, 43% 62%); }
        .modal-checkbox:checked { background-color: #2563eb; border-color: #2563eb; }
        .modal-checkbox:checked::before { transform: scale(1); }
        .modal-checkbox:disabled { background-color: #f1f5f9; border-color: #cbd5e1; cursor: not-allowed; }
    </style>
</head>
<body class="bg-white font-sans text-zinc-900 antialiased flex flex-col lg:flex-row-reverse min-h-screen overflow-hidden relative">

    {{-- KANAN: SISI VISUAL (CINEMATIC DARK) --}}
    <div class="hidden lg:flex w-1/2 bg-[#09090b] relative items-center justify-center overflow-hidden p-12">
        {{-- Animated Abstract Glow --}}
        <div class="absolute top-0 left-0 w-full h-full overflow-hidden pointer-events-none">
            <div class="absolute top-10 right-10 w-[500px] h-[500px] bg-indigo-600/20 rounded-full blur-[120px] animate-blob"></div>
            <div class="absolute bottom-0 left-0 w-[400px] h-[400px] bg-blue-600/20 rounded-full blur-[100px] animate-blob" style="animation-delay: 1.5s;"></div>
            <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')] opacity-[0.03]"></div>
        </div>

        {{-- Cinematic Branding --}}
        <div class="relative z-10 w-full max-w-lg animate-fade-in-up">
            <h1 class="text-5xl font-black text-white leading-[1.1] tracking-tight mb-6">
                Mulailah Bangun<br>
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-indigo-400">Kerajaan Anda.</span>
            </h1>
            <p class="text-zinc-400 text-lg font-medium leading-relaxed mb-10">
                Bergabung dengan ratusan kontraktor dan pemilik proyek yang telah mempercayakan suplai material mereka melalui ekosistem digital kami.
            </p>

            <div class="space-y-4">
                <div class="bg-white/5 border border-white/10 backdrop-blur-md p-5 rounded-2xl flex items-center gap-4 transition-transform hover:-translate-y-1">
                    <div class="w-10 h-10 rounded-full bg-blue-500/20 text-blue-400 flex items-center justify-center shrink-0"><i class="fas fa-tags"></i></div>
                    <div><h4 class="text-white font-bold text-sm">Harga Spesial B2B</h4></div>
                </div>
                <div class="bg-white/5 border border-white/10 backdrop-blur-md p-5 rounded-2xl flex items-center gap-4 transition-transform hover:-translate-y-1" style="animation-delay: 0.1s;">
                    <div class="w-10 h-10 rounded-full bg-emerald-500/20 text-emerald-400 flex items-center justify-center shrink-0"><i class="fas fa-truck-fast"></i></div>
                    <div><h4 class="text-white font-bold text-sm">Logistik Terintegrasi</h4></div>
                </div>
            </div>
        </div>

        <div class="absolute bottom-8 left-12 text-zinc-500 text-xs font-semibold">
            © {{ date('Y') }} Pondasikita Enterprise.
        </div>
    </div>

    {{-- KIRI: SISI FORM REGISTER --}}
    <div class="w-full lg:w-1/2 flex items-center justify-center p-6 sm:p-12 relative overflow-y-auto h-screen custom-scrollbar">
        <div class="w-full max-w-md my-auto animate-fade-in-up pb-10">

            <div class="mb-8 mt-4 lg:mt-0">
                <div class="inline-flex items-center justify-center p-2.5 bg-zinc-100 rounded-xl mb-6 shadow-sm lg:hidden">
                    <img src="{{ asset('assets/image/Pondasikita.com.png') }}" alt="Logo" class="h-8 w-auto object-contain" onerror="this.outerHTML='<div class=\'text-black font-black text-xl px-2\'>P<span class=\'text-blue-500\'>.</span></div>'">
                </div>
                <h2 class="text-3xl font-black text-black tracking-tight mb-2">Buat Akun Baru</h2>
                <p class="text-zinc-500 font-medium text-sm">
                    Sudah tergabung? <a href="{{ route('login') }}" class="text-blue-600 font-bold hover:underline transition-all">Masuk di sini</a>
                </p>
            </div>

            <form action="{{ route('register.process') }}" method="POST" id="registerForm" class="space-y-4">
                @csrf

                {{-- Row 1: Username & Nama --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="relative group">
                        <label class="block text-[10px] font-black text-zinc-400 uppercase tracking-widest mb-1.5 ml-1">Username</label>
                        <input type="text" name="username" class="w-full bg-zinc-50 border border-zinc-200 text-black text-sm font-semibold rounded-2xl focus:bg-white focus:border-blue-600 focus:ring-4 focus:ring-blue-600/10 block px-4 py-3.5 transition-all outline-none placeholder:text-zinc-400" placeholder="johndoe" value="{{ old('username') }}" required>
                    </div>
                    <div class="relative group">
                        <label class="block text-[10px] font-black text-zinc-400 uppercase tracking-widest mb-1.5 ml-1">Nama Lengkap</label>
                        <input type="text" name="nama" class="w-full bg-zinc-50 border border-zinc-200 text-black text-sm font-semibold rounded-2xl focus:bg-white focus:border-blue-600 focus:ring-4 focus:ring-blue-600/10 block px-4 py-3.5 transition-all outline-none placeholder:text-zinc-400" placeholder="John Doe" value="{{ old('nama') }}" required>
                    </div>
                </div>

                {{-- Row 2: Email --}}
                <div class="relative group">
                    <label class="block text-[10px] font-black text-zinc-400 uppercase tracking-widest mb-1.5 ml-1">Email Profesional</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none"><i class="fas fa-envelope text-zinc-400 group-focus-within:text-blue-600 transition-colors"></i></div>
                        <input type="email" name="email" class="w-full bg-zinc-50 border border-zinc-200 text-black text-sm font-semibold rounded-2xl focus:bg-white focus:border-blue-600 focus:ring-4 focus:ring-blue-600/10 block pl-11 pr-4 py-3.5 transition-all outline-none placeholder:text-zinc-400" placeholder="john@perusahaan.com" value="{{ old('email') }}" required>
                    </div>
                </div>

               {{-- KATA SANDI --}}
                <div class="relative group">
                    <label class="block text-[10px] font-black text-zinc-400 uppercase tracking-widest mb-1.5 ml-1">Kata Sandi</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="fas fa-lock text-zinc-400 group-focus-within:text-blue-600 transition-colors"></i>
                        </div>
                        <input type="password" name="password" id="reg-password" oninput="cekKecocokanSandi()" class="w-full bg-zinc-50 border border-zinc-200 text-black text-sm font-semibold rounded-2xl focus:bg-white focus:border-blue-600 focus:ring-4 focus:ring-blue-600/10 block pl-11 pr-12 py-3.5 transition-all outline-none placeholder:text-zinc-400" placeholder="Minimal 8 karakter" required>
                        <button type="button" onclick="toggleRegPassword('reg-password', 'eyeReg')" class="absolute inset-y-0 right-0 pr-4 flex items-center text-zinc-400 hover:text-black transition-colors focus:outline-none"><i class="fas fa-eye" id="eyeReg"></i></button>
                    </div>
                </div>

                {{-- KONFIRMASI SANDI --}}
                <div class="relative group">
                    <label class="block text-[10px] font-black text-zinc-400 uppercase tracking-widest mb-1.5 ml-1">Konfirmasi Sandi</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i id="icon-conf" class="fas fa-shield-alt text-zinc-400 group-focus-within:text-blue-600 transition-colors"></i>
                        </div>
                        <input type="password" name="password_confirmation" id="reg-password-conf" oninput="cekKecocokanSandi()" class="w-full bg-zinc-50 border border-zinc-200 text-black text-sm font-semibold rounded-2xl focus:bg-white focus:border-blue-600 focus:ring-4 focus:ring-blue-600/10 block pl-11 pr-12 py-3.5 transition-all outline-none placeholder:text-zinc-400" placeholder="Ulangi kata sandi" required>
                        <button type="button" onclick="toggleRegPassword('reg-password-conf', 'eyeRegConf')" class="absolute inset-y-0 right-0 pr-4 flex items-center text-zinc-400 hover:text-black transition-colors focus:outline-none"><i class="fas fa-eye" id="eyeRegConf"></i></button>
                    </div>
                    <p id="password-warning" class="hidden text-red-500 text-[10px] font-bold mt-2 ml-1 flex items-center gap-1.5 transition-all">
                        <i class="fas fa-exclamation-circle"></i> Kata sandi tidak cocok
                    </p>
                    <p id="password-success" class="hidden text-emerald-500 text-[10px] font-bold mt-2 ml-1 flex items-center gap-1.5 transition-all">
                        <i class="fas fa-check-circle"></i> Kata sandi cocok
                    </p>
                </div>

                {{-- TRIGGER SYARAT KETENTUAN (MODAL) --}}
                <div class="mt-6 border-2 border-dashed border-zinc-200 bg-zinc-50 hover:bg-blue-50/50 hover:border-blue-300 rounded-2xl p-4 flex items-center justify-between cursor-pointer transition-colors group" onclick="openTncModal()">
                    <div class="flex items-center gap-3">
                        <div class="relative flex items-center justify-center shrink-0">
                            {{-- Checkbox Asli (Hidden, required by form) --}}
                            <input type="checkbox" id="main-agree-cb" name="agree" class="peer sr-only" required>
                            {{-- Visual Checkbox --}}
                            <div class="w-5 h-5 rounded-md border-2 border-zinc-300 peer-checked:bg-blue-600 peer-checked:border-blue-600 flex items-center justify-center transition-all group-hover:border-blue-400">
                                <i class="fas fa-check text-white text-[10px] opacity-0 peer-checked:opacity-100"></i>
                            </div>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-xs font-bold text-zinc-800">Syarat & Ketentuan</span>
                            <span class="text-[10px] font-medium text-red-500" id="tnc-status-text">*Wajib dibaca & disetujui</span>
                        </div>
                    </div>
                    <div class="w-8 h-8 rounded-full bg-white border border-zinc-200 flex items-center justify-center text-zinc-400 group-hover:text-blue-600 shadow-sm transition-colors">
                        <i class="fas fa-chevron-right text-xs"></i>
                    </div>
                </div>

                {{-- Submit Button (Default Disabled) --}}
                <button type="submit" id="submitBtn" disabled class="w-full bg-zinc-900 text-white font-black py-4 rounded-2xl transition-all duration-300 shadow-[0_4px_20px_rgba(0,0,0,0.15)] mt-6 disabled:opacity-50 disabled:cursor-not-allowed disabled:bg-zinc-300 disabled:shadow-none enabled:hover:bg-blue-600 enabled:hover:shadow-[0_8px_30px_rgba(37,99,235,0.3)] enabled:hover:-translate-y-1">
                    <span id="btn-text">Mulai Berbelanja</span>
                </button>

            </form>
        </div>
    </div>

    {{-- ======================================================== --}}
    {{-- MODAL SYARAT & KETENTUAN (FULL SCREEN OVERLAY) --}}
    {{-- ======================================================== --}}
    <div id="tnc-modal" class="fixed inset-0 bg-zinc-950/80 backdrop-blur-sm z-50 flex items-center justify-center p-4 opacity-0 pointer-events-none transition-opacity duration-300">
        <div class="w-full max-w-2xl bg-white rounded-[2rem] shadow-2xl flex flex-col overflow-hidden max-h-[90vh] transform scale-95 transition-transform duration-300" id="tnc-modal-content">
            
            {{-- Header Modal --}}
            <div class="px-6 py-5 border-b border-zinc-100 flex items-center justify-between bg-zinc-50">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-blue-100 text-blue-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-file-signature text-lg"></i>
                    </div>
                    <h3 class="text-lg font-black text-zinc-900 tracking-tight">Syarat & Ketentuan</h3>
                </div>
                <button type="button" onclick="closeTncModal()" class="w-10 h-10 bg-white border border-zinc-200 rounded-full flex items-center justify-center text-zinc-500 hover:text-red-500 transition-colors">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            {{-- Area Scrollable T&C --}}
            <div class="relative flex-1 overflow-hidden bg-white">
                
                {{-- Indikator Scroll Bawah --}}
                <div id="scroll-indicator" class="absolute bottom-4 left-1/2 -translate-x-1/2 bg-zinc-900/90 text-white text-[10px] font-bold uppercase tracking-widest px-4 py-2.5 rounded-full flex items-center gap-2 transition-all duration-500 z-10 shadow-lg pointer-events-none">
                    <i class="fas fa-arrow-down animate-bounce"></i> Scroll untuk menyetujui
                </div>

                {{-- Teks --}}
                <div id="tnc-text-area" class="h-full overflow-y-auto tnc-scrollbar p-6 md:p-8 text-sm text-zinc-600 leading-relaxed space-y-6">
                    <h2 class="text-base font-black text-black">1. Pendahuluan</h2>
                    <p>Selamat datang di Pondasikita. Dengan mendaftar dan menggunakan layanan B2B e-commerce Pondasikita, Anda secara otomatis menyetujui seluruh syarat dan ketentuan yang berlaku di bawah ini. Harap membaca dengan saksama sebelum melanjutkan proses pendaftaran akun perusahaan Anda.</p>
                    
                    <h2 class="text-base font-black text-black">2. Akun dan Keamanan</h2>
                    <p>a. Anda bertanggung jawab penuh untuk menjaga kerahasiaan kata sandi akun perusahaan Anda.<br>b. Pondasikita tidak bertanggung jawab atas kerugian yang ditimbulkan akibat kelalaian pengguna dalam menjaga akses akun.<br>c. Setiap akun yang didaftarkan harus mewakili entitas bisnis (B2B) yang sah secara hukum di Indonesia.</p>

                    <h2 class="text-base font-black text-black">3. Transaksi dan Pembayaran</h2>
                    <p>a. Segala bentuk transaksi jual beli material wajib dilakukan melalui sistem pembayaran resmi yang disediakan oleh Pondasikita.<br>b. Transaksi di luar sistem (*bypass*) merupakan pelanggaran berat dan dapat mengakibatkan pemblokiran akun secara permanen.<br>c. Harga yang tertera pada katalog belum termasuk PPN, kecuali jika dijelaskan sebaliknya pada halaman detail tagihan.</p>

                    <h2 class="text-base font-black text-black">4. Pengiriman Material</h2>
                    <p>a. Estimasi pengiriman disesuaikan dengan ketersediaan armada dan jadwal rute dari masing-masing Mitra/Toko.<br>b. Pembeli wajib memastikan akses jalan menuju lokasi proyek dapat dilalui oleh armada truk yang bersangkutan.</p>

                    <h2 class="text-base font-black text-black">5. Kebijakan Retur (Pengembalian Barang)</h2>
                    <p>Barang yang sudah dibeli dan diterima sesuai dengan surat jalan tidak dapat dikembalikan, kecuali terdapat cacat pabrik atau kesalahan spesifikasi dari pihak pengirim yang dilaporkan maksimal 1x24 jam sejak barang tiba di lokasi proyek.</p>

                    <div class="pt-8 pb-4 text-center border-t border-zinc-200 mt-8">
                        <i class="fas fa-check-circle text-emerald-500 text-3xl mb-2"></i>
                        <p class="font-bold text-zinc-900">Akhir Dokumen Syarat & Ketentuan</p>
                    </div>
                </div>
            </div>

            {{-- Footer Modal (Aksi) --}}
            <div class="p-6 border-t border-zinc-100 bg-zinc-50 flex flex-col sm:flex-row items-center justify-between gap-4">
                <label id="modal-agree-label" class="flex items-center gap-3 cursor-not-allowed opacity-50 transition-all duration-300">
                    <div class="relative">
                        <input type="checkbox" id="modal-checkbox" disabled class="modal-checkbox peer">
                        <i id="lock-icon" class="fas fa-lock absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 text-[10px] text-zinc-400 peer-disabled:block hidden"></i>
                    </div>
                    <span class="text-sm font-bold text-zinc-900">Saya Mengerti & Setuju</span>
                </label>

                <button type="button" id="btn-modal-submit" disabled onclick="acceptTnc()" class="w-full sm:w-auto bg-zinc-900 text-white font-black py-3 px-8 rounded-xl transition-all disabled:bg-zinc-200 disabled:text-zinc-400 disabled:cursor-not-allowed enabled:hover:bg-blue-600 enabled:hover:-translate-y-0.5">
                    Setuju & Tutup
                </button>
            </div>
        </div>
    </div>


    {{-- SWEETALERT & LOGIC --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // LOGIKA KECOCOKAN PASSWORD
        function cekKecocokanSandi() {
            const pass = document.getElementById('reg-password').value;
            const conf = document.getElementById('reg-password-conf');
            const confVal = conf.value;
            
            const warningText = document.getElementById('password-warning');
            const successText = document.getElementById('password-success');
            const iconConf = document.getElementById('icon-conf');

            conf.classList.remove('border-zinc-200', 'focus:border-blue-600', 'focus:ring-blue-600/10', 'border-red-500', 'focus:border-red-500', 'focus:ring-red-500/10', 'border-emerald-500', 'focus:border-emerald-500', 'focus:ring-emerald-500/10');
            iconConf.classList.remove('text-zinc-400', 'group-focus-within:text-blue-600', 'text-red-500', 'text-emerald-500');

            if (confVal === '') {
                conf.classList.add('border-zinc-200', 'focus:border-blue-600', 'focus:ring-blue-600/10');
                iconConf.classList.add('text-zinc-400', 'group-focus-within:text-blue-600');
                warningText.classList.add('hidden');
                successText.classList.add('hidden');
            } else if (pass !== confVal) {
                conf.classList.add('border-red-500', 'focus:border-red-500', 'focus:ring-red-500/10');
                iconConf.classList.add('text-red-500');
                warningText.classList.remove('hidden');
                successText.classList.add('hidden');
            } else {
                conf.classList.add('border-emerald-500', 'focus:border-emerald-500', 'focus:ring-emerald-500/10');
                iconConf.classList.add('text-emerald-500');
                warningText.classList.add('hidden');
                successText.classList.remove('hidden');
            }
        }

        // LOGIKA MATA PASSWORD
        function toggleRegPassword(inputId, iconId) {
            const pwd = document.getElementById(inputId);
            const icon = document.getElementById(iconId);
            if (pwd.type === 'password') {
                pwd.type = 'text'; icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                pwd.type = 'password'; icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        }

        // LOGIKA MODAL SYARAT & KETENTUAN (SCROLL TO ACCEPT)
        const tncModal = document.getElementById('tnc-modal');
        const tncModalContent = document.getElementById('tnc-modal-content');
        const tncTextArea = document.getElementById('tnc-text-area');
        const scrollIndicator = document.getElementById('scroll-indicator');
        const modalAgreeLabel = document.getElementById('modal-agree-label');
        const modalCheckbox = document.getElementById('modal-checkbox');
        const btnModalSubmit = document.getElementById('btn-modal-submit');
        
        // Element di Form Utama
        const mainAgreeCb = document.getElementById('main-agree-cb');
        const submitBtn = document.getElementById('submitBtn');
        const tncStatusText = document.getElementById('tnc-status-text');
        
        let isScrolledToBottom = false;

        function openTncModal() {
            tncModal.classList.remove('opacity-0', 'pointer-events-none');
            setTimeout(() => tncModalContent.classList.remove('scale-95'), 50);
            checkScroll(); // Cek jika layar besar dan teks langsung habis
        }

        function closeTncModal() {
            tncModalContent.classList.add('scale-95');
            setTimeout(() => tncModal.classList.add('opacity-0', 'pointer-events-none'), 200);
        }

        // Cek Scroll mentok bawah
        function checkScroll() {
            if (tncTextArea.scrollHeight - tncTextArea.scrollTop <= tncTextArea.clientHeight + 2) {
                if (!isScrolledToBottom) {
                    isScrolledToBottom = true;
                    scrollIndicator.classList.add('opacity-0', '-translate-y-4');
                    modalAgreeLabel.classList.remove('opacity-50', 'cursor-not-allowed');
                    modalAgreeLabel.classList.add('cursor-pointer');
                    modalCheckbox.disabled = false;
                    
                    tncTextArea.classList.add('ring-2', 'ring-emerald-500');
                    setTimeout(() => tncTextArea.classList.remove('ring-2', 'ring-emerald-500'), 500);
                }
            }
        }

        tncTextArea.addEventListener('scroll', checkScroll);

        modalCheckbox.addEventListener('change', function() {
            btnModalSubmit.disabled = !this.checked;
        });

        // Saat user klik Setuju di dalam modal
        function acceptTnc() {
            closeTncModal();
            // Centang checkbox rahasia di form utama
            mainAgreeCb.checked = true;
            // Aktifkan tombol Submit utama
            submitBtn.disabled = false;
            // Ubah teks status di luar
            tncStatusText.textContent = "Syarat & Ketentuan Disetujui";
            tncStatusText.classList.replace('text-red-500', 'text-emerald-500');
        }

        // SUBMIT FORM UTAMA
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('registerForm');

            form.addEventListener('submit', function(e) {
                if(!mainAgreeCb.checked) {
                    e.preventDefault();
                    Swal.fire({ icon: 'warning', title: 'Perhatian', text: 'Silakan baca dan setujui Syarat Ketentuan terlebih dahulu.', customClass: { popup: 'rounded-3xl' } });
                    return;
                }

                submitBtn.disabled = true;
                document.getElementById('btn-text').innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Sedang Membuat Akun...';
                submitBtn.classList.add('opacity-70', 'cursor-not-allowed');
            });

            @if(session('success'))
                Swal.fire({
                    icon: 'success', title: 'Akun Dibuat!', text: '{{ session('success') }}',
                    confirmButtonColor: '#000000', confirmButtonText: 'Masuk Sekarang', allowOutsideClick: false,
                    customClass: { popup: 'rounded-3xl', confirmButton: 'rounded-xl font-bold px-6 py-3' }
                }).then((result) => {
                    if (result.isConfirmed) { window.location.href = "{{ route('login') }}"; }
                });
            @endif

            @if($errors->any())
                Swal.fire({
                    icon: 'error', title: 'Validasi Gagal',
                    html: `<div style="text-align: left; font-size: 0.85rem; color: #dc2626; background: #fef2f2; padding: 15px; border-radius: 12px; border: 1px solid #fecaca; margin-top: 10px;"><ul style="padding-left: 20px; margin: 0; font-weight: 500; line-height: 1.5;">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>`,
                    confirmButtonColor: '#000000', confirmButtonText: 'Perbaiki',
                    customClass: { popup: 'rounded-3xl', confirmButton: 'rounded-xl font-bold px-6 py-3' }
                });
            @endif
        });
    </script>
</body>
</html>
