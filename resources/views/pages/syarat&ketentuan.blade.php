<!DOCTYPE html>
<html lang="id">
<head>
    <link rel="icon" href="{{ asset('logopondasikita.png') }}" type="image/png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Syarat & Ketentuan - Pondasikita B2B</title>

    {{-- Tailwind CSS CDN & Config --}}
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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body { background-color: #f4f4f5; }
        
        /* Custom Scrollbar Elegan untuk Kotak T&C */
        .tnc-scrollbar::-webkit-scrollbar { width: 8px; }
        .tnc-scrollbar::-webkit-scrollbar-track { background: #f4f4f5; border-radius: 10px; }
        .tnc-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; border: 2px solid #f4f4f5; }
        .tnc-scrollbar:hover::-webkit-scrollbar-thumb { background: #94a3b8; }

        /* Custom Checkbox (Tampil kotak rapi) */
        .custom-checkbox { appearance: none; background-color: #fff; margin: 0; width: 1.5rem; height: 1.5rem; border: 2px solid #e2e8f0; border-radius: 0.5rem; display: grid; place-content: center; cursor: pointer; transition: all 0.2s ease-in-out; flex-shrink: 0; }
        .custom-checkbox::before { content: ""; width: 0.75rem; height: 0.75rem; transform: scale(0); transition: 120ms transform ease-in-out; background-color: white; transform-origin: center; clip-path: polygon(14% 44%, 0 65%, 50% 100%, 100% 16%, 80% 0%, 43% 62%); }
        .custom-checkbox:checked { background-color: #2563eb; border-color: #2563eb; }
        .custom-checkbox:checked::before { transform: scale(1); }
        .custom-checkbox:disabled { background-color: #f1f5f9; border-color: #cbd5e1; cursor: not-allowed; }

        /* Shimmer Animasi untuk Tombol Aktif */
        @keyframes shimmer { 100% { transform: translateX(100%); } }
        .animate-shimmer { animation: shimmer 2s infinite; }
    </style>
</head>
<body class="font-sans text-zinc-900 antialiased min-h-screen flex items-center justify-center p-4 lg:p-8 relative overflow-hidden">

    {{-- Background Ornaments --}}
    <div class="absolute inset-0 pointer-events-none overflow-hidden">
        <div class="absolute -top-40 -right-40 w-96 h-96 bg-blue-600/10 rounded-full blur-[100px]"></div>
        <div class="absolute -bottom-40 -left-40 w-96 h-96 bg-indigo-600/10 rounded-full blur-[100px]"></div>
    </div>

    {{-- Card Utama --}}
    <div class="w-full max-w-4xl bg-white rounded-[2.5rem] shadow-[0_20px_60px_-15px_rgba(0,0,0,0.05)] border border-zinc-100 flex flex-col overflow-hidden relative z-10">
        
        {{-- Header Card --}}
        <div class="px-8 py-6 border-b border-zinc-100 flex items-center justify-between bg-white">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center text-xl">
                    <i class="fas fa-file-signature"></i>
                </div>
                <div>
                    <h1 class="text-xl font-black text-zinc-900 tracking-tight">Syarat & Ketentuan</h1>
                    <p class="text-xs font-semibold text-zinc-400 uppercase tracking-widest mt-1">Pondasikita Enterprise</p>
                </div>
            </div>
            <a href="{{ route('login') }}" class="text-zinc-400 hover:text-zinc-900 transition-colors w-10 h-10 flex items-center justify-center rounded-full hover:bg-zinc-100">
                <i class="fas fa-times"></i>
            </a>
        </div>

        {{-- Konten Form --}}
        <form action="{{ route('register') }}" method="GET" id="tncForm" class="flex flex-col">
            
            {{-- Kotak Teks yang Harus di-Scroll --}}
            <div class="p-8 bg-white">
                <div class="relative rounded-2xl border border-zinc-200 bg-zinc-50 overflow-hidden group">
                    
                    {{-- Indikator Scroll (Mengingatkan user) --}}
                    <div id="scroll-indicator" class="absolute bottom-4 left-1/2 -translate-x-1/2 bg-zinc-900/80 backdrop-blur-sm text-white text-[10px] font-bold uppercase tracking-widest px-4 py-2 rounded-full flex items-center gap-2 transition-all duration-500 z-10 shadow-lg pointer-events-none">
                        <i class="fas fa-arrow-down animate-bounce"></i> Scroll sampai bawah untuk menyetujui
                    </div>

                    {{-- Area Teks Panjang --}}
                    <div id="tnc-content" class="h-[400px] overflow-y-auto tnc-scrollbar p-6 md:p-8 text-sm text-zinc-600 leading-relaxed space-y-6 relative">
                        <h2 class="text-lg font-black text-black">1. Pendahuluan</h2>
                        <p>Selamat datang di Pondasikita. Dengan mendaftar dan menggunakan layanan B2B e-commerce Pondasikita, Anda secara otomatis menyetujui seluruh syarat dan ketentuan yang berlaku di bawah ini. Harap membaca dengan saksama sebelum melanjutkan proses pendaftaran akun perusahaan Anda.</p>
                        
                        <h2 class="text-lg font-black text-black">2. Akun dan Keamanan</h2>
                        <p>a. Anda bertanggung jawab penuh untuk menjaga kerahasiaan kata sandi akun perusahaan Anda.<br>b. Pondasikita tidak bertanggung jawab atas kerugian yang ditimbulkan akibat kelalaian pengguna dalam menjaga akses akun.<br>c. Setiap akun yang didaftarkan harus mewakili entitas bisnis (B2B) yang sah secara hukum di Indonesia.</p>

                        <h2 class="text-lg font-black text-black">3. Transaksi dan Pembayaran</h2>
                        <p>a. Segala bentuk transaksi jual beli material wajib dilakukan melalui sistem pembayaran resmi yang disediakan oleh Pondasikita.<br>b. Transaksi di luar sistem (*bypass*) merupakan pelanggaran berat dan dapat mengakibatkan pemblokiran akun secara permanen.<br>c. Harga yang tertera pada katalog belum termasuk PPN, kecuali jika dijelaskan sebaliknya pada halaman detail tagihan.</p>

                        <h2 class="text-lg font-black text-black">4. Pengiriman Material</h2>
                        <p>a. Estimasi pengiriman disesuaikan dengan ketersediaan armada dan jadwal rute dari masing-masing Mitra/Toko.<br>b. Pembeli wajib memastikan akses jalan menuju lokasi proyek dapat dilalui oleh armada truk yang bersangkutan.</p>

                        <h2 class="text-lg font-black text-black">5. Kebijakan Retur (Pengembalian Barang)</h2>
                        <p>Barang yang sudah dibeli dan diterima sesuai dengan surat jalan tidak dapat dikembalikan, kecuali terdapat cacat pabrik atau kesalahan spesifikasi dari pihak pengirim yang dilaporkan maksimal 1x24 jam sejak barang tiba di lokasi proyek.</p>

                        <h2 class="text-lg font-black text-black">6. Perubahan Syarat dan Ketentuan</h2>
                        <p>Pondasikita berhak sewaktu-waktu mengubah, menambah, atau menghapus bagian dari Syarat & Ketentuan ini. Perubahan akan diinformasikan melalui email terdaftar atau notifikasi dasbor.</p>

                        {{-- Tanda akhir dokumen --}}
                        <div class="pt-8 pb-4 text-center border-t border-zinc-200 mt-8">
                            <i class="fas fa-check-circle text-emerald-500 text-3xl mb-2"></i>
                            <p class="font-bold text-zinc-900">Akhir Dokumen Syarat & Ketentuan</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Footer Aksi (Checkbox & Tombol) --}}
            <div class="px-8 py-6 border-t border-zinc-100 bg-zinc-50 flex flex-col sm:flex-row items-center justify-between gap-6">
                
                {{-- Checkbox (Default Disabled) --}}
                <label id="agree-label" class="flex items-center gap-4 cursor-not-allowed opacity-50 transition-all duration-300 w-full sm:w-auto">
                    <div class="relative">
                        <input type="checkbox" id="agree-checkbox" name="agree" disabled class="custom-checkbox peer">
                        {{-- Gembok Icon Overlay saat disabled --}}
                        <i id="lock-icon" class="fas fa-lock absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 text-[10px] text-zinc-400 peer-disabled:block hidden"></i>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-sm font-bold text-zinc-900">Saya Setuju</span>
                        <span class="text-[10px] text-zinc-500 font-medium">Saya telah membaca & menyetujui aturan di atas.</span>
                    </div>
                </label>

                {{-- Tombol Submit (Default Disabled) --}}
                <button type="submit" id="btn-submit" disabled class="group relative w-full sm:w-auto bg-zinc-900 text-white font-black py-4 px-10 rounded-2xl transition-all duration-500 overflow-hidden disabled:bg-zinc-200 disabled:text-zinc-400 disabled:cursor-not-allowed hover:bg-blue-600 hover:shadow-lg hover:shadow-blue-500/30 hover:-translate-y-0.5">
                    <div class="absolute inset-0 w-full h-full bg-gradient-to-r from-transparent via-white/20 to-transparent -translate-x-full group-hover:animate-shimmer hidden group-enabled:block"></div>
                    <span class="relative flex items-center justify-center gap-2 text-xs uppercase tracking-widest">
                        Lanjut Daftar <i class="fas fa-arrow-right"></i>
                    </span>
                </button>

            </div>
        </form>
    </div>

    {{-- LOGIKA JAVASCRIPT --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tncContent = document.getElementById('tnc-content');
            const scrollIndicator = document.getElementById('scroll-indicator');
            const agreeLabel = document.getElementById('agree-label');
            const agreeCheckbox = document.getElementById('agree-checkbox');
            const btnSubmit = document.getElementById('btn-submit');
            
            let isScrolledToBottom = false;

            // Fungsi untuk mengecek apakah sudah mentok bawah
            function checkScroll() {
                // Rumus: Total tinggi scroll - posisi scroll saat ini <= tinggi container yang terlihat
                // Angka +2 digunakan sebagai toleransi desimal piksel di berbagai browser
                if (tncContent.scrollHeight - tncContent.scrollTop <= tncContent.clientHeight + 2) {
                    
                    if (!isScrolledToBottom) {
                        isScrolledToBottom = true;
                        
                        // 1. Hilangkan indikator "Scroll ke bawah"
                        scrollIndicator.classList.add('opacity-0', '-translate-y-4');
                        
                        // 2. Buka gembok dan aktifkan checkbox
                        agreeLabel.classList.remove('opacity-50', 'cursor-not-allowed');
                        agreeLabel.classList.add('cursor-pointer');
                        agreeCheckbox.disabled = false;
                        
                        // Efek flash hijau menandakan sudah terbuka
                        tncContent.classList.add('ring-2', 'ring-emerald-500');
                        setTimeout(() => tncContent.classList.remove('ring-2', 'ring-emerald-500'), 500);
                    }
                }
            }

            // Jalankan cek saat user men-scroll
            tncContent.addEventListener('scroll', checkScroll);

            // Jalankan cek sekali saat halaman dimuat (Jaga-jaga jika teks ternyata pendek dan tidak perlu di-scroll)
            checkScroll();

            // Logika ketika checkbox dicentang/dihapus centangnya
            agreeCheckbox.addEventListener('change', function() {
                if (this.checked) {
                    btnSubmit.disabled = false;
                } else {
                    btnSubmit.disabled = true;
                }
            });
        });
    </script>
</body>
</html>
