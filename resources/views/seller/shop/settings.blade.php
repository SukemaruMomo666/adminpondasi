@extends('layouts.seller')

@section('title', 'Pengaturan Toko')

@push('styles')
<style>
    .tab-content { display: none; opacity: 0; transform: translateY(10px); transition: all 0.3s ease-out; }
    .tab-content.active { display: block; opacity: 1; transform: translateY(0); }
</style>
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
                Swal.fire({title: 'Gagal!', text: '{{ session('error') }}', icon: 'error', customClass: { popup: 'rounded-3xl' }});
            @endif
        });
    </script>

    {{-- HEADER --}}
    <div class="flex items-center gap-4 mb-8">
        <div class="w-12 h-12 bg-white border border-slate-200 rounded-2xl flex items-center justify-center text-blue-600 shadow-sm flex-shrink-0">
            <i class="mdi mdi-cog-outline text-2xl"></i>
        </div>
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Pengaturan Toko</h1>
            <p class="text-sm font-medium text-slate-500 mt-0.5">Kelola operasional, notifikasi, dan keamanan akun Anda.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

        {{-- NAVIGASI TAB VERTIKAL (KIRI) --}}
        <div class="lg:col-span-3">
            <div class="bg-white border border-slate-200 rounded-3xl p-3 shadow-sm sticky top-24">
                <nav class="flex flex-col gap-1">
                    <button type="button" onclick="switchTab('general')" id="btn-general" class="tab-btn flex items-center gap-3 px-4 py-3 rounded-2xl text-sm font-bold text-blue-700 bg-blue-50 transition-colors text-left">
                        <i class="mdi mdi-store-cog text-lg"></i> Operasional Toko
                    </button>
                    <button type="button" onclick="switchTab('notification')" id="btn-notification" class="tab-btn flex items-center gap-3 px-4 py-3 rounded-2xl text-sm font-bold text-slate-600 hover:bg-slate-50 hover:text-slate-900 transition-colors text-left">
                        <i class="mdi mdi-bell-outline text-lg"></i> Notifikasi
                    </button>
                    <a href="{{ route('seller.shop.security') }}" class="flex items-center gap-3 px-4 py-3 rounded-2xl text-sm font-bold text-slate-600 hover:bg-slate-50 hover:text-slate-900 transition-colors text-left">
                        <i class="mdi mdi-shield-lock-outline text-lg"></i> Keamanan Akun
                    </a>
                </nav>
            </div>
        </div>

        {{-- AREA KONTEN (KANAN) --}}
        <div class="lg:col-span-9">

            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-700 px-5 py-4 rounded-3xl mb-6 shadow-sm flex items-start gap-3">
                    <i class="mdi mdi-alert-circle text-xl mt-0.5"></i>
                    <div>
                        <h5 class="font-bold text-sm mb-1">Gagal Menyimpan!</h5>
                        <ul class="list-disc list-inside text-xs font-medium space-y-0.5">
                            @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            {{-- FORM 1: GENERAL & NOTIFICATION --}}
            <form action="{{ route('seller.shop.settings.update') }}" method="POST" id="formGeneral">
                @csrf
                @method('PUT')
                <input type="hidden" name="form_type" value="general">

                {{-- KONTEN TAB: OPERASIONAL TOKO --}}
                <div id="tab-general" class="tab-content active space-y-6">
                    <div class="bg-white border border-slate-200 rounded-3xl p-6 md:p-8 shadow-sm">
                        <div class="flex justify-between items-start gap-4 mb-6 pb-6 border-b border-slate-100">
                            <div>
                                <h3 class="text-base font-black text-slate-900 mb-1 flex items-center gap-2">
                                    <i class="mdi mdi-beach text-amber-500 text-xl"></i> Mode Libur Toko
                                </h3>
                                <p class="text-sm font-medium text-slate-500 leading-relaxed max-w-xl">
                                    Aktifkan mode libur jika Anda tidak dapat melayani pesanan. Pembeli tidak dapat memesan, tetapi pesanan berjalan wajib diselesaikan.
                                </p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer flex-shrink-0 mt-1">
                                <input type="checkbox" name="status_libur" class="sr-only peer" {{ ($toko->status_libur ?? 0) ? 'checked' : '' }}>
                                <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-amber-500"></div>
                            </label>
                        </div>

                        <div>
                            <h3 class="text-base font-black text-slate-900 mb-1 flex items-center gap-2">
                                <i class="mdi mdi-robot-outline text-indigo-500 text-xl"></i> Balasan Chat Otomatis
                            </h3>
                            <p class="text-sm font-medium text-slate-500 mb-4">Pesan otomatis yang akan dikirim instan kepada pembeli saat mereka mengirim chat pertama kali.</p>
                            <textarea name="pesan_otomatis" class="w-full bg-slate-50 border border-slate-200 text-slate-900 text-sm font-medium rounded-xl px-4 py-3 focus:bg-white focus:ring-2 focus:ring-blue-600 outline-none transition-all min-h-[120px] resize-none" placeholder="Cth: Halo! Selamat datang di toko kami. Mohon tunggu sebentar, pesan Anda akan segera kami balas...">{{ $toko->pesan_otomatis ?? '' }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- KONTEN TAB: NOTIFIKASI --}}
                <div id="tab-notification" class="tab-content space-y-6">
                    <div class="bg-white border border-slate-200 rounded-3xl p-6 md:p-8 shadow-sm">
                        <h3 class="text-base font-black text-slate-900 mb-6 pb-4 border-b border-slate-100 flex items-center gap-2">
                            <i class="mdi mdi-bell-ring-outline text-blue-500 text-xl"></i> Preferensi Pemberitahuan
                        </h3>

                        <div class="space-y-6">
                            <div class="flex justify-between items-center gap-4">
                                <div>
                                    <h6 class="text-sm font-bold text-slate-800">Email Pesanan Baru</h6>
                                    <p class="text-xs font-medium text-slate-500">Kirim pemberitahuan email segera setelah ada pesanan masuk.</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="notif_email_pesanan" class="sr-only peer" {{ ($notif['email_pesanan'] ?? true) ? 'checked' : '' }}>
                                    <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                </label>
                            </div>

                            <div class="flex justify-between items-center gap-4">
                                <div>
                                    <h6 class="text-sm font-bold text-slate-800">Notifikasi Pop-up Chat</h6>
                                    <p class="text-xs font-medium text-slate-500">Munculkan pop-up saat pembeli mengirimkan chat baru.</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="notif_push_chat" class="sr-only peer" {{ ($notif['push_chat'] ?? true) ? 'checked' : '' }}>
                                    <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                </label>
                            </div>

                            <div class="flex justify-between items-center gap-4">
                                <div>
                                    <h6 class="text-sm font-bold text-slate-800">Email Info & Promo Platform</h6>
                                    <p class="text-xs font-medium text-slate-500">Terima pembaruan berkala dari Pondasikita.</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="notif_email_promo" class="sr-only peer" {{ ($notif['email_promo'] ?? false) ? 'checked' : '' }}>
                                    <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- STICKY ACTION BAR --}}
                <div id="sticky-action-bar" class="fixed bottom-0 left-0 lg:left-[260px] right-0 bg-white/80 backdrop-blur-md border-t border-slate-200 px-6 py-4 flex items-center justify-between z-40 shadow-[0_-10px_15px_-3px_rgba(0,0,0,0.05)]">
                    <div class="hidden sm:block">
                        <p class="text-xs font-bold text-slate-500 m-0"><i class="mdi mdi-information text-blue-500"></i> Pastikan untuk menyimpan perubahan.</p>
                    </div>
                    <div class="flex gap-3 w-full sm:w-auto">
                        <button type="submit" class="w-full sm:w-auto flex items-center justify-center gap-2 px-8 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-sm shadow-blue-600/20 transition-all btn-save-loader">
                            <i class="mdi mdi-content-save"></i> Simpan Pengaturan
                        </button>
                    </div>
                </div>
            </form>

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Tab switching logic
    function switchTab(tabId) {
        document.querySelectorAll('.tab-content').forEach(el => el.classList.remove('active'));
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.className = 'tab-btn flex items-center gap-3 px-4 py-3 rounded-2xl text-sm font-bold text-slate-600 hover:bg-slate-50 hover:text-slate-900 transition-colors text-left';
        });

        document.getElementById('tab-' + tabId).classList.add('active');
        const activeBtn = document.getElementById('btn-' + tabId);
        activeBtn.className = 'tab-btn flex items-center gap-3 px-4 py-3 rounded-2xl text-sm font-bold text-blue-700 bg-blue-50 transition-colors text-left';

        // Save current tab to local storage
        localStorage.setItem('activeSettingsTab', tabId);
    }

    // Auto-open saved tab
    document.addEventListener('DOMContentLoaded', () => {
        const savedTab = localStorage.getItem('activeSettingsTab');
        if (savedTab && document.getElementById('tab-' + savedTab)) {
            switchTab(savedTab);
        }
    });

    // Loading State
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', function() {
            const btn = this.querySelector('.btn-save-loader');
            if(btn) {
                btn.innerHTML = '<i class="mdi mdi-loading mdi-spin text-lg leading-none"></i> Memproses...';
                btn.disabled = true;
                btn.classList.add('opacity-70', 'cursor-not-allowed');
            }
        });
    });
</script>
@endpush
