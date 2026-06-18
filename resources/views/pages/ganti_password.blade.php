<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Keamanan Akun - {{ Auth::user()->nama }} | Pondasikita</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    {{-- Tailwind CSS CDN --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'] },
                    colors: {
                        brand: { 50: '#eff6ff', 100: '#dbeafe', 500: '#3b82f6', 600: '#2563eb', 700: '#1d4ed8' },
                    },
                    boxShadow: {
                        'soft': '0 4px 40px -4px rgba(0,0,0,0.03)',
                        'glow': '0 0 20px rgba(37,99,235,0.3)',
                    },
                    animation: {
                        'fade-in': 'fadeIn 0.5s cubic-bezier(0.16, 1, 0.3, 1) forwards',
                    },
                    keyframes: {
                        fadeIn: { '0%': { opacity: 0, transform: 'translateY(10px)' }, '100%': { opacity: 1, transform: 'translateY(0)' } }
                    }
                }
            }
        }
    </script>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f4f4f5; }
        .step-container { display: none; }
        .step-container.active { display: block; animation: fadeIn 0.4s ease-in-out; }
    </style>
</head>
<body class="text-zinc-800 antialiased pt-[80px] flex flex-col min-h-screen">

    {{-- Include Navbar --}}
    @include('partials.navbar')

    {{-- BREADCRUMB MINIMALIS --}}
    <div class="bg-white border-b border-zinc-200 hidden md:block">
        <div class="max-w-[1100px] mx-auto px-4 sm:px-6 py-3">
            <nav class="flex text-xs font-semibold text-zinc-500 items-center gap-3">
                <a href="{{ url('/') }}" class="hover:text-black transition-colors">Beranda</a>
                <span class="w-1 h-1 rounded-full bg-zinc-300"></span>
                <a href="{{ route('profil.index') }}" class="hover:text-black transition-colors">Profil Pengguna</a>
                <span class="w-1 h-1 rounded-full bg-zinc-300"></span>
                <span class="text-zinc-900">Keamanan Akun</span>
            </nav>
        </div>
    </div>

    {{-- MAIN CONTENT --}}
    <main class="flex-grow w-full max-w-[600px] mx-auto px-4 sm:px-6 py-12 lg:py-16">

        <div class="bg-white rounded-[2.5rem] shadow-soft border border-zinc-200 overflow-hidden p-8 sm:p-12 animate-fade-in">
            
            <div class="text-center mb-8">
                <div class="w-20 h-20 bg-blue-50 text-blue-600 rounded-3xl mx-auto flex items-center justify-center text-3xl mb-5 shadow-inner">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <h1 class="text-3xl font-black text-black tracking-tight mb-2">Keamanan Akun</h1>
                <p class="text-sm font-medium text-zinc-500">Ubah password Anda melalui sistem verifikasi OTP untuk menjaga keamanan akun Anda tetap optimal.</p>
            </div>

            @php
                $emailPart = explode('@', Auth::user()->email);
                $maskedEmail = substr($emailPart[0], 0, 3) . '***@' . $emailPart[1];
                $hasVerified = session('password_change_otp_verified', false);
            @endphp

            {{-- TAHAP 1: KIRIM OTP --}}
            <div id="step-1" class="step-container {{ !$hasVerified ? 'active' : '' }}">
                <div class="bg-zinc-50 border border-zinc-200 rounded-3xl p-6 text-center mb-8">
                    <div class="w-12 h-12 bg-white rounded-full mx-auto flex items-center justify-center text-zinc-400 mb-4 shadow-sm">
                        <i class="fas fa-envelope-open-text text-xl"></i>
                    </div>
                    <p class="text-sm font-semibold text-zinc-700 leading-relaxed mb-1">Kami akan mengirimkan <strong>Kode OTP 6-Digit</strong> ke email Anda:</p>
                    <p class="text-lg font-black text-blue-600 tracking-wide">{{ $maskedEmail }}</p>
                </div>
                
                <button type="button" class="w-full bg-black hover:bg-blue-600 text-white font-black py-4 rounded-2xl transition-all shadow-md active:scale-95 flex justify-center items-center gap-2" onclick="sendOtp()" id="btn-send-otp">
                    <i class="fas fa-paper-plane"></i> Kirim Kode OTP Sekarang
                </button>
            </div>

            {{-- TAHAP 2: VERIFIKASI OTP --}}
            <div id="step-2" class="step-container text-center">
                <div class="mb-6">
                    <h3 class="text-lg font-black text-zinc-900 mb-2">Verifikasi Email Anda</h3>
                    <p class="text-sm font-medium text-zinc-500">Masukkan 6 digit kode yang telah kami kirimkan ke <b>{{ $maskedEmail }}</b></p>
                </div>

                <div class="mb-8">
                    <input type="text" id="otp_input" class="w-full bg-zinc-50 border border-zinc-200 text-zinc-900 text-center font-black rounded-2xl px-6 py-4 focus:bg-white focus:border-blue-600 focus:ring-4 focus:ring-blue-600/10 outline-none transition-all" placeholder="X X X X X X" maxlength="6" style="letter-spacing: 0.5em; font-size: 1.5rem;">
                </div>

                <button type="button" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-black py-4 rounded-2xl transition-all shadow-md shadow-blue-500/30 active:scale-95 flex justify-center items-center gap-2" onclick="verifyOtp()" id="btn-verify-otp">
                    <i class="fas fa-check-circle"></i> Validasi Kode OTP
                </button>

                <p class="text-xs font-bold text-zinc-400 mt-6">Belum menerima email? <button type="button" onclick="sendOtp()" class="text-blue-600 hover:underline">Kirim Ulang</button></p>
            </div>

            {{-- TAHAP 3: FORM GANTI PASSWORD --}}
            <form id="step-3" action="{{ route('profil.password.update') }}" method="POST" class="step-container {{ $hasVerified ? 'active' : '' }}">
                @csrf
                
                <div class="bg-emerald-50 border border-emerald-200 rounded-2xl p-4 flex items-center gap-3 mb-8">
                    <i class="fas fa-check-circle text-emerald-500 text-xl"></i>
                    <p class="text-xs font-bold text-emerald-700">Verifikasi berhasil! Silakan buat password baru Anda di bawah ini.</p>
                </div>

                <div class="space-y-5">
                    {{-- Password Baru --}}
                    <div>
                        <label class="block text-xs font-black text-zinc-500 uppercase tracking-widest mb-2">Password Baru</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i class="fas fa-key text-zinc-400"></i>
                            </div>
                            <input type="password" name="password_baru" id="pwd_baru" class="w-full bg-zinc-50 border border-zinc-200 text-zinc-900 text-sm font-bold rounded-xl pl-11 pr-12 py-3.5 focus:bg-white focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 outline-none transition-all" placeholder="Minimal 8 karakter" required>
                            <button type="button" class="absolute inset-y-0 right-0 pr-4 flex items-center text-zinc-400 hover:text-blue-600 transition-colors" onclick="toggleVisibility('pwd_baru', this)">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        @error('password_baru') <span class="text-xs font-bold text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    {{-- Konfirmasi Password --}}
                    <div>
                        <label class="block text-xs font-black text-zinc-500 uppercase tracking-widest mb-2">Ulangi Password</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i class="fas fa-shield-check text-zinc-400"></i>
                            </div>
                            <input type="password" name="password_baru_confirmation" id="pwd_confirm" class="w-full bg-zinc-50 border border-zinc-200 text-zinc-900 text-sm font-bold rounded-xl pl-11 pr-12 py-3.5 focus:bg-white focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 outline-none transition-all" placeholder="Ketik ulang password baru" required>
                            <button type="button" class="absolute inset-y-0 right-0 pr-4 flex items-center text-zinc-400 hover:text-blue-600 transition-colors" onclick="toggleVisibility('pwd_confirm', this)">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <button type="submit" class="w-full mt-8 bg-black hover:bg-blue-600 text-white font-black text-sm uppercase tracking-widest py-4 rounded-2xl transition-all shadow-md active:scale-95">
                    Simpan Password Baru
                </button>
            </form>

        </div>

        <div class="text-center mt-6">
            <a href="{{ route('profil.index') }}" class="inline-flex items-center justify-center gap-2 text-sm font-bold text-zinc-500 hover:text-black transition-colors px-6 py-2 rounded-full hover:bg-zinc-200/50">
                <i class="fas fa-arrow-left"></i> Kembali ke Pengaturan Profil
            </a>
        </div>

    </main>

    {{-- Include Footer --}}
    <div class="mt-auto">
        @include('partials.footer')
    </div>

    {{-- Scripts --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function toggleVisibility(inputId, btn) {
            const input = document.getElementById(inputId);
            const icon = btn.querySelector('i');
            if (input.type === "password") {
                input.type = "text";
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = "password";
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

        function switchStep(stepId) {
            document.querySelectorAll('.step-container').forEach(el => el.classList.remove('active'));
            document.getElementById(stepId).classList.add('active');
        }

        function sendOtp() {
            const btn = document.getElementById('btn-send-otp');
            const originalHtml = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Mengirim OTP...';

            fetch("{{ route('profil.password.send_otp') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                btn.disabled = false;
                btn.innerHTML = originalHtml;
                if (data.success) {
                    Swal.fire({ icon: 'success', title: 'Terkirim!', text: data.message, confirmButtonColor: '#2563eb', timer: 2000, customClass: { popup: 'rounded-3xl' }});
                    switchStep('step-2');
                } else {
                    Swal.fire({ icon: 'error', title: 'Gagal', text: data.message, confirmButtonColor: '#ef4444', customClass: { popup: 'rounded-3xl' }});
                }
            })
            .catch(error => {
                btn.disabled = false;
                btn.innerHTML = originalHtml;
                Swal.fire({ icon: 'error', title: 'Terjadi Kesalahan', text: 'Tidak dapat terhubung ke server.', confirmButtonColor: '#ef4444', customClass: { popup: 'rounded-3xl' }});
            });
        }

        function verifyOtp() {
            const otpVal = document.getElementById('otp_input').value;
            if (!otpVal || otpVal.length < 6) {
                Swal.fire({ icon: 'warning', title: 'Oops', text: 'Masukkan 6 digit OTP terlebih dahulu.', confirmButtonColor: '#f59e0b', customClass: { popup: 'rounded-3xl' }});
                return;
            }

            const btn = document.getElementById('btn-verify-otp');
            const originalHtml = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memverifikasi...';

            fetch("{{ route('profil.password.verify_otp') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ otp: otpVal })
            })
            .then(response => response.json())
            .then(data => {
                btn.disabled = false;
                btn.innerHTML = originalHtml;
                if (data.success) {
                    Swal.fire({ icon: 'success', title: 'Berhasil!', text: data.message, confirmButtonColor: '#2563eb', timer: 1500, customClass: { popup: 'rounded-3xl' }});
                    switchStep('step-3');
                } else {
                    Swal.fire({ icon: 'error', title: 'Gagal', text: data.message, confirmButtonColor: '#ef4444', customClass: { popup: 'rounded-3xl' }});
                }
            })
            .catch(error => {
                btn.disabled = false;
                btn.innerHTML = originalHtml;
                Swal.fire({ icon: 'error', title: 'Terjadi Kesalahan', text: 'Tidak dapat terhubung ke server.', confirmButtonColor: '#ef4444', customClass: { popup: 'rounded-3xl' }});
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            @if(session('success'))
                Swal.fire({ icon: 'success', title: 'Berhasil!', text: '{{ session('success') }}', confirmButtonColor: '#2563eb', customClass: { popup: 'rounded-3xl' }});
            @endif

            @if(session('error'))
                Swal.fire({ icon: 'error', title: 'Gagal!', text: '{{ session('error') }}', confirmButtonColor: '#ef4444', customClass: { popup: 'rounded-3xl' }});
            @endif
            
            // Auto focus for OTP input filtering non-numeric
            document.getElementById('otp_input').addEventListener('input', function (e) {
                this.value = this.value.replace(/[^0-9]/g, '');
            });
        });
    </script>
</body>
</html>