@extends('layouts.seller')

@section('title', 'Keamanan Akun & Password')

@push('styles')
<style>
    .otp-input { width: 45px; height: 55px; text-align: center; font-size: 24px; font-weight: 900; border-radius: 12px; border: 2px solid #e2e8f0; background: #f8fafc; color: #0f172a; transition: all 0.2s ease; }
    .otp-input:focus { border-color: #3b82f6; background: #ffffff; box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1); outline: none; }
</style>
@endpush

@push('scripts')
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
@endpush

@section('content')
<div class="min-h-screen bg-slate-50 p-4 md:p-6 lg:p-8 font-sans text-slate-900 pb-32">

    {{-- SETUP SWEETALERT TOAST --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        const Toast = Swal.mixin({
            toast: true, position: 'top-end', showConfirmButton: false, timer: 3000,
            customClass: { popup: 'rounded-2xl shadow-lg border border-slate-100' }
        });
        
        document.addEventListener('DOMContentLoaded', () => {
            @if(session('success'))
                Toast.fire({icon: 'success', title: '{{ session('success') }}'});
            @endif
            @if(session('error'))
                Swal.fire({title: 'Sesi Berakhir!', text: '{{ session('error') }}', icon: 'warning', customClass: { popup: 'rounded-3xl' }});
            @endif
        });
    </script>

    {{-- HEADER --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 mb-8">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-white border border-slate-200 rounded-2xl flex items-center justify-center text-red-500 shadow-sm flex-shrink-0">
                <i class="mdi mdi-shield-lock-outline text-2xl"></i>
            </div>
            <div>
                <h1 class="text-2xl font-black text-slate-900 tracking-tight">Keamanan Akun</h1>
                <p class="text-sm font-medium text-slate-500 mt-0.5 flex items-center gap-2">
                    <a href="{{ route('seller.shop.settings') }}" class="hover:text-blue-600 transition-colors">Pengaturan</a>
                    <i class="mdi mdi-chevron-right text-xs"></i>
                    <span class="text-slate-700 font-bold">Ubah Password</span>
                </p>
            </div>
        </div>
    </div>

    {{-- ALPINE COMPONENT --}}
    <div x-data="securityApp()" class="max-w-2xl mx-auto">

        {{-- STATE 1: Minta OTP --}}
        <div x-show="step === 1" x-transition.opacity.duration.500ms class="bg-white border border-slate-200 rounded-3xl p-8 shadow-sm text-center">
            <div class="w-24 h-24 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center mx-auto mb-6 border-8 border-white shadow-sm">
                <i class="mdi mdi-email-fast text-5xl"></i>
            </div>
            <h2 class="text-2xl font-black text-slate-900 mb-2">Verifikasi Email Anda</h2>
            <p class="text-sm text-slate-500 font-medium mb-8 max-w-md mx-auto leading-relaxed">
                Untuk alasan keamanan, kami perlu mengirimkan kode One-Time Password (OTP) ke email terdaftar Anda sebelum Anda dapat mengubah kata sandi.
            </p>

            <div class="bg-slate-50 border border-slate-200 rounded-2xl p-4 flex items-center gap-4 max-w-sm mx-auto mb-8 text-left">
                <div class="w-10 h-10 bg-white rounded-full flex items-center justify-center shadow-sm text-slate-400">
                    <i class="mdi mdi-at text-xl"></i>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Kirim ke Email</p>
                    <p class="text-sm font-black text-slate-800">{{ auth()->user()->email }}</p>
                </div>
            </div>

            <button type="button" @click="sendOtp" :disabled="loading" class="w-full sm:w-auto px-10 py-3.5 bg-slate-900 hover:bg-black text-white font-bold rounded-2xl shadow-lg shadow-slate-900/20 transition-all flex items-center justify-center gap-2 mx-auto outline-none disabled:opacity-70 disabled:cursor-not-allowed">
                <i class="mdi" :class="loading ? 'mdi-loading mdi-spin' : 'mdi-send-check'"></i>
                <span x-text="loading ? 'Mengirim...' : 'Kirim Kode OTP'"></span>
            </button>
        </div>

        {{-- STATE 2: Input OTP --}}
        <div x-show="step === 2" style="display: none;" x-transition.opacity.duration.500ms class="bg-white border border-slate-200 rounded-3xl p-8 shadow-sm text-center">
            <button type="button" @click="step = 1" class="absolute left-8 top-8 w-10 h-10 bg-slate-50 hover:bg-slate-100 text-slate-600 rounded-full flex items-center justify-center transition-colors outline-none">
                <i class="mdi mdi-arrow-left text-xl"></i>
            </button>

            <div class="w-24 h-24 bg-emerald-50 text-emerald-500 rounded-full flex items-center justify-center mx-auto mb-6 border-8 border-white shadow-sm">
                <i class="mdi mdi-shield-check text-5xl"></i>
            </div>
            <h2 class="text-2xl font-black text-slate-900 mb-2">Masukkan Kode OTP</h2>
            <p class="text-sm text-slate-500 font-medium mb-8 max-w-sm mx-auto leading-relaxed">
                Kami telah mengirimkan 6 digit kode unik ke email Anda. Silakan masukkan kode tersebut di bawah ini.
            </p>

            <div class="flex justify-center gap-2 sm:gap-3 mb-8" id="otp-container">
                <template x-for="(digit, index) in otp" :key="index">
                    <input type="text" maxlength="1" class="otp-input" x-model="otp[index]" @input="handleOtpInput($event, index)" @keydown.backspace="handleOtpBackspace($event, index)">
                </template>
            </div>

            <div class="flex flex-col items-center gap-4">
                <button type="button" @click="verifyOtp" :disabled="loading || otp.join('').length < 6" class="w-full sm:w-auto px-10 py-3.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-2xl shadow-lg shadow-blue-600/30 transition-all flex items-center justify-center gap-2 outline-none disabled:opacity-50 disabled:cursor-not-allowed">
                    <i class="mdi" :class="loading ? 'mdi-loading mdi-spin' : 'mdi-check-decagram'"></i>
                    <span x-text="loading ? 'Memverifikasi...' : 'Verifikasi & Lanjutkan'"></span>
                </button>
                <p class="text-xs font-bold text-slate-500">Belum menerima email? <button type="button" @click="sendOtp" class="text-blue-600 hover:text-blue-800 focus:outline-none">Kirim Ulang</button></p>
            </div>
        </div>

        {{-- STATE 3: Reset Password --}}
        <div x-show="step === 3" style="display: none;" x-transition.opacity.duration.500ms class="bg-white border border-slate-200 rounded-3xl p-8 shadow-sm">
            <div class="flex items-center gap-4 mb-8 pb-6 border-b border-slate-100">
                <div class="w-14 h-14 bg-red-50 text-red-500 rounded-2xl flex items-center justify-center shrink-0">
                    <i class="mdi mdi-lock-reset text-3xl"></i>
                </div>
                <div>
                    <h2 class="text-xl font-black text-slate-900">Buat Kata Sandi Baru</h2>
                    <p class="text-xs font-bold text-emerald-600 mt-1 flex items-center gap-1"><i class="mdi mdi-check-circle"></i> Identitas Terverifikasi</p>
                </div>
            </div>

            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-700 px-5 py-4 rounded-2xl mb-6 shadow-sm flex items-start gap-3">
                    <i class="mdi mdi-alert-circle text-xl mt-0.5"></i>
                    <div>
                        <h5 class="font-bold text-sm mb-1">Gagal Menyimpan!</h5>
                        <ul class="list-disc list-inside text-xs font-medium space-y-0.5">
                            @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <form action="{{ route('seller.shop.security.resetPassword') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="space-y-5">
                    <div>
                        <label class="block text-[11px] font-black text-slate-500 uppercase tracking-widest mb-2">Password Baru</label>
                        <div class="relative">
                            <i class="mdi mdi-lock-outline absolute left-4 top-3.5 text-slate-400 text-lg"></i>
                            <input type="password" name="new_password" class="w-full bg-slate-50 border border-slate-200 text-slate-900 text-sm font-bold rounded-xl pl-11 pr-4 py-3.5 focus:bg-white focus:ring-2 focus:ring-blue-600 outline-none transition-all placeholder-slate-400" placeholder="Ketik sandi baru Anda" required minlength="8">
                        </div>
                        <p class="text-[10px] font-bold text-slate-400 mt-2">*Gunakan kombinasi huruf kapital, angka, dan simbol untuk keamanan ekstra.</p>
                    </div>

                    <div>
                        <label class="block text-[11px] font-black text-slate-500 uppercase tracking-widest mb-2">Konfirmasi Password Baru</label>
                        <div class="relative">
                            <i class="mdi mdi-lock-check-outline absolute left-4 top-3.5 text-slate-400 text-lg"></i>
                            <input type="password" name="new_password_confirmation" class="w-full bg-slate-50 border border-slate-200 text-slate-900 text-sm font-bold rounded-xl pl-11 pr-4 py-3.5 focus:bg-white focus:ring-2 focus:ring-blue-600 outline-none transition-all placeholder-slate-400" placeholder="Ketik ulang sandi baru Anda" required minlength="8">
                        </div>
                    </div>

                    <div class="pt-6 mt-6 border-t border-slate-100 flex justify-end">
                        <button type="submit" class="w-full sm:w-auto px-10 py-3.5 bg-red-500 hover:bg-red-600 text-white font-bold rounded-xl shadow-lg shadow-red-500/20 transition-all flex items-center justify-center gap-2 outline-none" onclick="this.innerHTML='<i class=\'mdi mdi-loading mdi-spin\'></i> Menyimpan...'; this.classList.add('opacity-70');">
                            Simpan Password Baru
                        </button>
                    </div>
                </div>
            </form>
        </div>

    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('securityApp', () => ({
            step: {{ session('security_verified_at') ? 3 : 1 }},
            loading: false,
            otp: ['', '', '', '', '', ''],

            sendOtp() {
                this.loading = true;
                fetch("{{ route('seller.shop.security.sendOtp') }}", {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
                })
                .then(res => res.json())
                .then(data => {
                    this.loading = false;
                    if(data.status === 'success') {
                        Toast.fire({icon: 'success', title: 'OTP Terkirim ke Email'});
                        this.step = 2;
                        setTimeout(() => document.querySelector('.otp-input').focus(), 100);
                    } else {
                        Swal.fire({title: 'Gagal!', text: data.message, icon: 'error', customClass: { popup: 'rounded-3xl' }});
                    }
                })
                .catch(err => {
                    this.loading = false;
                    Swal.fire('Error Server', 'Tidak dapat mengirim OTP saat ini.', 'error');
                });
            },

            handleOtpInput(e, index) {
                const val = e.target.value;
                // Hanya boleh angka
                if (/[^0-9]/.test(val)) {
                    this.otp[index] = '';
                    return;
                }
                // Auto focus next
                if (val && index < 5) {
                    const inputs = document.querySelectorAll('.otp-input');
                    if(inputs[index + 1]) inputs[index + 1].focus();
                }
            },

            handleOtpBackspace(e, index) {
                if (!this.otp[index] && index > 0) {
                    const inputs = document.querySelectorAll('.otp-input');
                    if(inputs[index - 1]) inputs[index - 1].focus();
                }
            },

            verifyOtp() {
                const code = this.otp.join('');
                if(code.length < 6) return;

                this.loading = true;
                fetch("{{ route('seller.shop.security.verifyOtp') }}", {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    body: JSON.stringify({ otp: code })
                })
                .then(res => res.json())
                .then(data => {
                    this.loading = false;
                    if(data.status === 'success') {
                        Toast.fire({icon: 'success', title: 'Verifikasi Berhasil'});
                        this.step = 3;
                    } else {
                        Swal.fire({title: 'OTP Salah!', text: data.message, icon: 'error', customClass: { popup: 'rounded-3xl' }});
                        this.otp = ['', '', '', '', '', ''];
                        document.querySelector('.otp-input').focus();
                    }
                })
                .catch(err => {
                    this.loading = false;
                    Swal.fire('Error Server', 'Terjadi kesalahan sistem.', 'error');
                });
            }
        }));
    });
</script>
@endsection