{{-- ========================================================
     PREMIUM B&W FOOTER (TAILWIND CSS) - APP EDITION
     ======================================================== --}}
<footer class="bg-[#050505] text-zinc-400 pt-20 pb-28 md:pb-10 border-t-2 border-zinc-800 relative overflow-hidden font-sans">

    {{-- Subtle Blue Glow Background (Opsional, agar tidak terlalu gelap) --}}
    <div class="absolute top-0 left-1/4 w-96 h-96 bg-blue-600/5 rounded-full filter blur-[100px] pointer-events-none"></div>

    <div class="max-w-7xl mx-auto px-6 relative z-10">

        {{-- MAIN CONTENT GRID --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-8 mb-20">

            {{-- ================= KOLOM KIRI: APP PROMO (5 Cols) ================= --}}
            <div class="lg:col-span-5 flex flex-col sm:flex-row gap-8 items-center sm:items-start text-center sm:text-left">

                {{-- CSS Phone Mockup (Tanpa gambar eksternal) --}}
                <div class="relative w-44 h-[310px] bg-zinc-950 rounded-[2.5rem] border-[6px] border-zinc-800 shadow-[0_0_30px_rgba(59,130,246,0.1)] overflow-hidden flex-shrink-0 flex flex-col group">
                    {{-- Notch --}}
                    <div class="absolute top-0 inset-x-0 h-4 bg-zinc-800 rounded-b-xl w-20 mx-auto z-20"></div>
                    {{-- Screen --}}
                    <div class="flex-1 bg-zinc-900 p-3 pt-8 flex flex-col gap-3 relative">
                        {{-- App UI Abstract --}}
                        <div class="w-full h-8 bg-zinc-800 rounded-md"></div>
                        <div class="w-2/3 h-4 bg-zinc-800 rounded-md"></div>
                        <div class="grid grid-cols-2 gap-2 mt-2">
                            <div class="w-full h-16 bg-zinc-800/50 rounded-lg group-hover:bg-blue-600/20 transition-colors"></div>
                            <div class="w-full h-16 bg-zinc-800/50 rounded-lg group-hover:bg-blue-600/20 transition-colors"></div>
                        </div>
                        <div class="w-full h-24 bg-zinc-800/50 rounded-lg mt-auto mb-2"></div>
                        {{-- Fake Button --}}
                        <div class="w-full h-10 bg-blue-600 rounded-lg flex items-center justify-center text-white text-[10px] font-bold shadow-[0_0_15px_rgba(37,99,235,0.4)]">Pondasikita App</div>
                    </div>
                </div>

                {{-- App Text & Buttons --}}
                <div class="flex flex-col justify-center h-full">
                    <img src="{{ asset('logopondasikita.png') }}" alt="Pondasikita Logo" class="h-10 w-auto mb-4">
                    <p class="text-sm leading-relaxed text-zinc-400 mb-8 max-w-sm">
                        Kelola RAB proyek, lacak material *real-time*, dan dapatkan diskon B2B khusus di aplikasi. Unduh gratis sekarang!
                    </p>

                    <div class="flex flex-col gap-3 w-full sm:w-auto">
                        {{-- Tombol Android (Active) --}}
                        <a href="#" class="flex items-center justify-center sm:justify-start gap-4 bg-white hover:bg-zinc-200 text-black px-6 py-3.5 rounded-xl transition-all duration-300 group shadow-lg">
                            <i class="fab fa-google-play text-2xl group-hover:scale-110 transition-transform text-black"></i>
                            <div class="text-left">
                                <div class="text-[10px] uppercase font-bold text-zinc-600 leading-none mb-1">Dapatkan di</div>
                                <div class="text-sm font-black leading-none tracking-wide">Google Play</div>
                            </div>
                        </a>

                        {{-- Tombol iOS (Coming Soon) --}}
                        <div class="flex items-center justify-center sm:justify-start gap-4 bg-zinc-900 border border-zinc-800 text-zinc-500 px-6 py-3.5 rounded-xl cursor-not-allowed relative overflow-hidden">
                            <i class="fab fa-apple text-3xl"></i>
                            <div class="text-left">
                                <div class="text-[10px] uppercase font-bold text-blue-500 leading-none mb-1 flex items-center gap-1">
                                    <span class="w-1.5 h-1.5 bg-blue-500 rounded-full animate-pulse"></span> Segera Hadir
                                </div>
                                <div class="text-sm font-black leading-none tracking-wide">App Store</div>
                            </div>
                            <div class="absolute inset-0 bg-black/40 z-10"></div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ================= KOLOM KANAN: LINKS (7 Cols) ================= --}}
            <div class="lg:col-span-7 grid grid-cols-2 md:grid-cols-3 gap-8 lg:pl-10">

                {{-- Link Grup 1 --}}
                <div>
                    <h4 class="text-white font-black mb-6 uppercase tracking-widest text-xs">Jelajahi</h4>
                    <ul class="space-y-4 text-sm font-medium text-zinc-400">
                        <li><a href="#" class="hover:text-blue-400 hover:translate-x-1 inline-block transition-all duration-300">Beranda</a></li>
                        <li><a href="#" class="hover:text-blue-400 hover:translate-x-1 inline-block transition-all duration-300">Kategori Material</a></li>
                        <li><a href="#" class="hover:text-blue-400 hover:translate-x-1 inline-block transition-all duration-300">Daftar Mitra Toko</a></li>
                        <li><a href="#" class="hover:text-blue-400 hover:translate-x-1 inline-block transition-all duration-300">Promo Proyek</a></li>
                        <li><a href="#" class="hover:text-blue-400 hover:translate-x-1 inline-block transition-all duration-300">Pondasikita Blog</a></li>
                    </ul>
                </div>

                {{-- Link Grup 2 --}}
                <div>
                    <h4 class="text-white font-black mb-6 uppercase tracking-widest text-xs">Layanan</h4>
                    <ul class="space-y-4 text-sm font-medium text-zinc-400">
                        <li><a href="#" class="hover:text-blue-400 hover:translate-x-1 inline-block transition-all duration-300">Cara Pembayaran</a></li>
                        <li><a href="#" class="hover:text-blue-400 hover:translate-x-1 inline-block transition-all duration-300">Lacak Pengiriman</a></li>
                        <li><a href="#" class="hover:text-blue-400 hover:translate-x-1 inline-block transition-all duration-300">Kebijakan Retur 7 Hari</a></li>
                        <li><a href="#" class="hover:text-blue-400 hover:translate-x-1 inline-block transition-all duration-300">Pondasikita B2B</a></li>
                        <li><a href="#" class="hover:text-blue-400 hover:translate-x-1 inline-block transition-all duration-300">FAQ</a></li>
                    </ul>
                </div>

                {{-- Link Grup 3 (Hubungi Kami) --}}
                <div class="col-span-2 md:col-span-1">
                    <h4 class="text-white font-black mb-6 uppercase tracking-widest text-xs">Hubungi Kami</h4>
                    <ul class="space-y-4 text-sm font-medium text-zinc-400">
                        <li>
                            <a href="mailto:cs@pondasikita.com" class="hover:text-blue-400 transition-colors flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-zinc-900 border border-zinc-800 flex items-center justify-center text-white"><i class="fas fa-envelope text-xs"></i></div>
                                cs@pondasikita.com
                            </a>
                        </li>
                        <li>
                            <a href="tel:0211500768" class="hover:text-blue-400 transition-colors flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-zinc-900 border border-zinc-800 flex items-center justify-center text-white"><i class="fas fa-phone-alt text-xs"></i></div>
                                (021) 1500-POTA
                            </a>
                        </li>
                        <li class="flex items-start gap-3 mt-4 text-xs leading-relaxed text-zinc-500">
                            <i class="fas fa-building mt-1 text-zinc-600"></i>
                            Pondasikita HQ<br>
                            Jl. Jenderal Sudirman Kav. 21<br>
                            Jakarta Selatan, 12190
                        </li>
                    </ul>
                </div>

            </div>
        </div>

        {{-- ================= MIDDLE SECTION: SOCIAL & PAYMENTS ================= --}}
        <div class="border-t border-zinc-800/80 pt-8 pb-8 flex flex-col md:flex-row items-center justify-between gap-8">

            {{-- Social Media (Minimalist B&W) --}}
            <div class="flex flex-wrap justify-center gap-3">
                @foreach(['facebook-f', 'instagram', 'twitter', 'youtube', 'tiktok'] as $icon)
                    <a href="#" class="w-10 h-10 rounded-full bg-white text-black hover:bg-blue-600 hover:text-white flex items-center justify-center transition-all duration-300 shadow-md">
                        <i class="fab fa-{{ $icon }} text-lg"></i>
                    </a>
                @endforeach
            </div>

            {{-- Language Toggle & Certifications --}}
            <div class="flex items-center gap-6">
                <div class="flex items-center gap-2 text-sm font-bold text-zinc-500">
                    <button class="hover:text-white transition-colors">EN</button>
                    <span>|</span>
                    <button class="text-white">ID</button>
                </div>
            </div>

            {{-- Payment Icons (Grayscale -> Color on hover) --}}
            <div class="flex flex-wrap justify-center gap-3">
                <div class="h-8 px-3 bg-zinc-900 border border-zinc-800 rounded flex items-center justify-center text-zinc-500 hover:text-white transition-colors"><i class="fab fa-cc-visa text-xl"></i></div>
                <div class="h-8 px-3 bg-zinc-900 border border-zinc-800 rounded flex items-center justify-center text-zinc-500 hover:text-white transition-colors"><i class="fab fa-cc-mastercard text-xl"></i></div>
                <div class="h-8 px-3 bg-zinc-900 border border-zinc-800 rounded flex items-center justify-center text-zinc-500 hover:text-white transition-colors"><i class="fas fa-building-columns text-lg"></i></div>
                <div class="h-8 px-3 bg-zinc-900 border border-zinc-800 rounded flex items-center justify-center text-zinc-500 hover:text-white transition-colors font-bold text-[10px] tracking-wider">QRIS</div>
            </div>

        </div>

        {{-- ================= BOTTOM SECTION: COPYRIGHT ================= --}}
        <div class="border-t border-zinc-800/80 pt-6 flex flex-col md:flex-row justify-between items-center gap-4 text-xs font-medium text-zinc-600">
            <p>&copy; {{ date('Y') }} Pondasikita. Hak Cipta Dilindungi.</p>
            <div class="flex gap-4">
                <a href="#" class="hover:text-zinc-300 transition-colors">Kebijakan Privasi</a>
                <a href="#" class="hover:text-zinc-300 transition-colors">Syarat & Ketentuan</a>
            </div>
        </div>

    </div>

</footer>
