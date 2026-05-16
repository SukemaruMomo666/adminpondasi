@extends('layouts.admin')

@section('title', 'Regulasi Logistik & Pengiriman')

@push('styles')
<style>
    /* ========================================= */
    /* ==  PREMIUM LOGISTICS CSS (LIGHT & DARK) == */
    /* ========================================= */

    .hover-lift { transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.3s ease; }
    .hover-lift:hover { transform: translateY(-4px); }

    .input-group { display: flex; align-items: stretch; width: 100%; }
    .input-group-text { display: flex; align-items: center; padding: 0.5rem 1rem; font-size: 0.875rem; font-weight: 700; border-top-right-radius: 0.75rem; border-bottom-right-radius: 0.75rem; border: 1px solid #e2e8f0; border-left: 0; }

    .form-control-input { flex: 1 1 auto; width: 1%; min-width: 0; padding: 0.625rem 1rem; border-top-left-radius: 0.75rem; border-bottom-left-radius: 0.75rem; outline: none; transition: all 0.2s; border: 1px solid #e2e8f0; }

    /* MODERN TOGGLE SWITCH */
    .toggle-checkbox { display: none; }
    .toggle-label { width: 50px; height: 26px; border-radius: 30px; position: relative; cursor: pointer; transition: 0.3s; flex-shrink: 0; background: #cbd5e1; }
    .toggle-label::after { content: ''; position: absolute; top: 3px; left: 3px; width: 20px; height: 20px; border-radius: 50%; transition: 0.3s; background: white; box-shadow: 0 2px 4px rgba(0,0,0,0.2); }
    .toggle-checkbox:checked + .toggle-label { background: #10b981; }
    .toggle-checkbox:checked + .toggle-label::after { transform: translateX(24px); }

    /* COURIER CARD SELECTION */
    .courier-box { cursor: pointer; transition: all 0.2s; }
    .courier-checkbox { display: none; }
    .courier-checkbox:checked + .courier-content { border-color: #3b82f6 !important; background-color: #eff6ff !important; }
    .courier-checkbox:checked + .courier-content .check-icon { opacity: 1 !important; transform: scale(1) !important; }

    /* Floating Save Button */
    .btn-save-floating { position: fixed; bottom: 40px; right: 40px; z-index: 50; transition: all 0.3s; }
    .btn-save-floating:hover { transform: translateY(-5px); }

    /* ========================================= */
    /* == POLYFILL DARK MODE (ANTI-PUTIH)     == */
    /* ========================================= */
    .dark .dark\:bg-slate-950 { background-color: #020617 !important; }
    .dark .dark\:bg-slate-900 { background-color: #0f172a !important; }
    .dark .dark\:bg-slate-800 { background-color: #1e293b !important; }
    .dark .dark\:bg-slate-800\/50 { background-color: rgba(30, 41, 59, 0.5) !important; }
    .dark .dark\:bg-slate-800\/40 { background-color: rgba(30, 41, 59, 0.4) !important; }
    .dark .dark\:bg-slate-700 { background-color: #334155 !important; }
    .dark .dark\:bg-transparent { background-color: transparent !important; }

    .dark .dark\:border-slate-800 { border-color: #1e293b !important; }
    .dark .dark\:border-slate-700 { border-color: #334155 !important; }
    .dark .dark\:border-slate-700\/50 { border-color: rgba(51, 65, 85, 0.5) !important; }

    /* Input & Toggle Dark Mode */
    .dark .input-group-text { background-color: #1e293b !important; border-color: #334155 !important; color: #94a3b8 !important; }
    .dark .form-control-input { background-color: #0f172a !important; border-color: #334155 !important; color: #f8fafc !important; }
    .dark .toggle-label { background: #334155 !important; }
    .dark .toggle-label::after { background: #94a3b8 !important; box-shadow: none !important; }
    .dark .toggle-checkbox:checked + .toggle-label { background: #059669 !important; }
    .dark .toggle-checkbox:checked + .toggle-label::after { background: white !important; }

    /* Courier Card Dark Mode */
    .dark .courier-checkbox:checked + .courier-content { background-color: rgba(59, 130, 246, 0.1) !important; border-color: #3b82f6 !important; }

    /* Typography & Icons */
    .dark .dark\:text-white { color: #ffffff !important; }
    .dark .dark\:text-slate-100 { color: #f1f5f9 !important; }
    .dark .dark\:text-slate-200 { color: #e2e8f0 !important; }
    .dark .dark\:text-slate-300 { color: #cbd5e1 !important; }
    .dark .dark\:text-slate-400 { color: #94a3b8 !important; }
    .dark .dark\:text-blue-400 { color: #60a5fa !important; }
    .dark .dark\:text-amber-400 { color: #fbbf24 !important; }

    /* Custom Sections */
    .dark .dark\:bg-blue-500\/10 { background-color: rgba(59, 130, 246, 0.1) !important; }
    .dark .dark\:border-blue-500\/20 { border-color: rgba(59, 130, 246, 0.2) !important; }
    .dark .dark\:bg-amber-500\/10 { background-color: rgba(245, 158, 11, 0.1) !important; }
</style>
@endpush

@section('content')

{{-- SWEETALERT SETUP UNTUK NOTIFIKASI --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const Toast = Swal.mixin({
            toast: true, position: 'top-end', showConfirmButton: false, timer: 3500,
            timerProgressBar: true, customClass: { popup: 'rounded-2xl shadow-lg border border-slate-100' }
        });

        @if(session('success'))
            Toast.fire({icon: 'success', title: '{{ session("success") }}'});
        @endif

        @if($errors->any())
            Swal.fire({
                icon: 'error', title: 'Gagal Menyimpan!',
                text: 'Periksa kembali data yang Anda masukkan.',
                confirmButtonColor: '#3b82f6',
                customClass: { popup: 'rounded-3xl shadow-xl border border-slate-100' }
            });
        @endif
    });
</script>

<form action="{{ route('admin.logistics.update') }}" method="POST" class="pb-24">
    @csrf

    {{-- HERO HEADER --}}
    <div class="relative bg-gradient-to-br from-slate-800 to-slate-950 dark:from-slate-900 dark:to-slate-950 rounded-3xl p-8 mb-8 overflow-hidden shadow-xl shadow-slate-900/10 dark:shadow-none transition-colors duration-300">
        {{-- Abstract Decorative Background --}}
        <div class="absolute -right-20 -top-20 opacity-10 pointer-events-none">
            <i class="mdi mdi-map-marker-path text-[250px] text-white"></i>
        </div>
        <div class="relative z-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
            <div>
                <div class="flex items-center gap-2 text-xs font-bold text-slate-400 mb-2 transition-colors duration-300">
                    <a href="{{ route('admin.dashboard') }}" class="hover:text-blue-400 transition-colors text-decoration-none">Dashboard</a>
                    <i class="mdi mdi-chevron-right text-sm"></i>
                    <span class="text-blue-400">Konfigurasi Sistem</span>
                </div>
                <h2 class="text-3xl font-black text-white tracking-tight mb-2">Logistik & Distribusi Global</h2>
                <p class="text-slate-400 text-sm font-bold m-0 max-w-2xl leading-relaxed">
                    Atur ketersediaan ekspedisi API pihak ketiga (Biteship) yang diizinkan beroperasi di platform Pondasikita. Kurir yang dipilih di sini akan tersedia untuk diaktifkan oleh masing-masing Penjual.
                </p>
            </div>

            {{-- Quick Stats Snippet --}}
            <div class="bg-white/10 backdrop-blur-md border border-white/20 px-5 py-3 rounded-2xl flex items-center gap-4 hidden sm:flex">
                <div class="w-10 h-10 rounded-full bg-emerald-500/20 text-emerald-300 flex items-center justify-center text-xl">
                    <i class="mdi mdi-api"></i>
                </div>
                <div>
                    <div class="text-[10px] font-black text-slate-300 uppercase tracking-widest">Status Biteship</div>
                    <div class="text-sm font-black text-white">
                        @if(!empty($api_couriers) && count($api_couriers) > 1)
                            <span class="text-emerald-400">Terkoneksi & Live</span>
                        @else
                            <span class="text-red-400">Gagal / Offline</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

        {{-- KOLOM KIRI (ARMADA MANDIRI & METODE TOKO B2B) --}}
        <div class="lg:col-span-7 space-y-6">

            <div class="bg-white dark:bg-slate-900 border-t-4 border-t-amber-500 border-x border-b border-slate-200 dark:border-slate-800 rounded-2xl shadow-sm transition-colors duration-300 overflow-hidden">
                <div class="p-6 border-b border-slate-100 dark:border-slate-800 bg-amber-50/30 dark:bg-transparent">
                    <h3 class="text-lg font-black text-slate-800 dark:text-white flex items-center gap-2 m-0">
                        <i class="mdi mdi-truck-flatbed text-amber-500 text-2xl"></i> Regulasi Armada Internal Toko
                    </h3>
                    <p class="text-xs font-bold text-slate-500 dark:text-slate-400 mt-2 mb-0 leading-relaxed">
                        Pengaturan izin bagi penjual (seller) untuk mengatur opsi logistik secara mandiri tanpa pihak ketiga.
                    </p>
                </div>

                <div class="p-6 space-y-6">

                    {{-- Switch 1: Ambil di Toko (BOPIS) --}}
                    <div class="flex justify-between items-center p-4 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700/50 rounded-xl transition-colors duration-300">
                        <div class="pr-4">
                            <strong class="block text-sm font-black text-slate-800 dark:text-white mb-0.5 flex items-center gap-1.5"><i class="mdi mdi-store-marker text-emerald-500"></i> Izinkan "Ambil di Toko" (BOPIS)</strong>
                            <span class="text-[10px] font-bold text-slate-500 dark:text-slate-400">Izinkan seller menawarkan opsi pembeli mengambil pesanan langsung di gudang mereka secara gratis.</span>
                        </div>
                        <div>
                            <input type="hidden" name="enable_store_pickup" value="0">
                            <input type="checkbox" class="toggle-checkbox" id="pickupToggle" name="enable_store_pickup" value="1" {{ ($settings['enable_store_pickup'] ?? '1') == '1' ? 'checked' : '' }}>
                            <label for="pickupToggle" class="toggle-label"></label>
                        </div>
                    </div>

                    {{-- Switch 2: Aktifkan Armada Toko --}}
                    <div class="flex justify-between items-center p-4 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700/50 rounded-xl transition-colors duration-300">
                        <div class="pr-4">
                            <strong class="block text-sm font-black text-slate-800 dark:text-white mb-0.5">Izin Armada Internal Penjual</strong>
                            <span class="text-[10px] font-bold text-slate-500 dark:text-slate-400">Izinkan seller menggunakan truk/mobil pikap sendiri dan men-setting tarif per-KM di dashboard mereka.</span>
                        </div>
                        <div>
                            <input type="hidden" name="enable_custom_fleet" value="0">
                            <input type="checkbox" class="toggle-checkbox" id="fleetToggle" name="enable_custom_fleet" value="1" {{ ($settings['enable_custom_fleet'] ?? '1') == '1' ? 'checked' : '' }}>
                            <label for="fleetToggle" class="toggle-label"></label>
                        </div>
                    </div>

                    {{-- Switch 3: Pengiriman Darurat (Sameday) --}}
                    <div class="flex justify-between items-center p-4 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700/50 rounded-xl transition-colors duration-300">
                        <div class="pr-4">
                            <strong class="block text-sm font-black text-slate-800 dark:text-white mb-0.5">Izin Pengiriman Darurat (Sameday Toko)</strong>
                            <span class="text-[10px] font-bold text-slate-500 dark:text-slate-400">Berikan akses seller untuk membuka layanan antar kilat di hari yang sama dengan tarif premium.</span>
                        </div>
                        <div>
                            <input type="hidden" name="enable_emergency_delivery" value="0">
                            <input type="checkbox" class="toggle-checkbox" id="emergencyToggle" name="enable_emergency_delivery" value="1" {{ ($settings['enable_emergency_delivery'] ?? '0') == '1' ? 'checked' : '' }}>
                            <label for="emergencyToggle" class="toggle-label"></label>
                        </div>
                    </div>

                    {{-- Input Batasan Global Armada Internal --}}
                    <div class="mt-8 pt-6 border-t border-slate-100 dark:border-slate-800">
                        <h4 class="text-xs font-black text-slate-800 dark:text-white uppercase tracking-widest mb-4">Pembatasan Armada Internal</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-2">Batas Maksimal Jarak</label>
                                <div class="input-group">
                                    <input type="number" name="max_custom_fleet_distance" value="{{ $settings['max_custom_fleet_distance'] ?? '50' }}" placeholder="Cth: 50" class="form-control-input bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 font-bold text-sm shadow-inner dark:shadow-none">
                                    <span class="input-group-text bg-slate-100 dark:bg-slate-800/50 border-slate-200 dark:border-slate-700 border-l-0 text-slate-600 dark:text-slate-300">KM</span>
                                </div>
                                <p class="text-[10px] font-bold text-slate-500 dark:text-slate-400 mt-2 m-0 leading-tight">Maksimal jarak yang boleh di-setting seller.</p>
                            </div>

                            <div>
                                <label class="block text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-2">Minimal Berat Kargo</label>
                                <div class="input-group">
                                    <input type="number" name="min_heavy_cargo_weight" value="{{ $settings['min_heavy_cargo_weight'] ?? '0' }}" placeholder="Cth: 50" class="form-control-input bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 font-bold text-sm shadow-inner dark:shadow-none">
                                    <span class="input-group-text bg-slate-100 dark:bg-slate-800/50 border-slate-200 dark:border-slate-700 border-l-0 text-slate-600 dark:text-slate-300">KG</span>
                                </div>
                                <p class="text-[10px] font-bold text-slate-500 dark:text-slate-400 mt-2 m-0 leading-tight">Isi 0 jika bebas. (Agar armada truk eksklusif barang berat).</p>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        {{-- KOLOM KANAN (EKSPEDISI API BITESHIP) --}}
        <div class="lg:col-span-5 space-y-6">

            <div class="bg-white dark:bg-slate-900 border-t-4 border-t-blue-500 border-x border-b border-slate-200 dark:border-slate-800 rounded-2xl shadow-sm transition-colors duration-300 overflow-hidden">
                <div class="p-6 border-b border-slate-100 dark:border-slate-800 bg-blue-50/30 dark:bg-transparent">
                    <h3 class="text-lg font-black text-slate-800 dark:text-white flex items-center gap-2 m-0">
                        <i class="mdi mdi-api text-blue-500 text-2xl"></i> Master Kurir (Biteship API)
                    </h3>
                    <div class="bg-amber-50 dark:bg-amber-500/10 border border-amber-200 dark:border-amber-500/30 p-4 rounded-xl mt-4">
                        <p class="text-[11px] font-bold text-amber-700 dark:text-amber-400 m-0 leading-relaxed text-justify">
                            <i class="mdi mdi-information text-lg mr-1 mb-1 block"></i>
                            <strong>HIERARKI SISTEM:</strong> Anda sebagai Super Admin bertugas menentukan "Daftar Kurir Global" yang boleh beroperasi di platform Pondasikita. Kurir yang Anda beri centang di bawah ini, nantinya akan muncul di <strong>Dashboard Seller</strong> agar mereka bisa memilih kurir mana saja yang cocok dengan lokasi gudang mereka.
                        </p>
                    </div>
                </div>

                @php
                    // Ambil data aktif dari DB (string jadi array)
                    $active_api_couriers_string = $settings['api_active_couriers'] ?? '';
                    $active_api_couriers = empty($active_api_couriers_string) ? [] : explode(',', $active_api_couriers_string);
                @endphp

                <div class="p-6">
                    
                    {{-- ALERT BILA API GAGAL --}}
                    @if(empty($api_couriers) || count($api_couriers) <= 1)
                        <div class="bg-red-50 border border-red-200 p-4 rounded-xl mb-6">
                            <p class="text-xs font-bold text-red-600 m-0"><i class="mdi mdi-wifi-off text-lg mr-1"></i> Tidak dapat terhubung dengan API Biteship. Pastikan API Key di menu Pengaturan Situs sudah benar.</p>
                        </div>
                    @endif

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-1 xl:grid-cols-2 gap-4">
                        {{-- HACK: Hidden input untuk bypass error array kosong --}}
                        <input type="hidden" name="api_active_couriers[]" value="NONE_SELECTED_HACK">
                        
                        @foreach($api_couriers as $code => $kurir)
                            <label class="courier-box w-full relative m-0 hover-lift" for="api_{{ $code }}">
                                <input type="checkbox" name="api_active_couriers[]" value="{{ $code }}" id="api_{{ $code }}" class="courier-checkbox" {{ in_array($code, $active_api_couriers) ? 'checked' : '' }}>

                                <div class="courier-content flex flex-col justify-center p-4 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-xl h-full transition-colors duration-200 relative">
                                    <div class="flex items-center gap-3">
                                        {{-- Icon Box --}}
                                        <div class="w-10 h-10 rounded-lg bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-700 flex items-center justify-center flex-shrink-0 shadow-sm z-10">
                                            <i class="mdi {{ $kurir['icon'] ?? 'mdi-truck-delivery' }} text-blue-500 dark:text-blue-400 text-xl"></i>
                                        </div>

                                        {{-- Info --}}
                                        <div class="flex-1 pr-4 z-10">
                                            <strong class="block text-sm font-black text-slate-800 dark:text-white leading-tight mb-1">{{ $kurir['name'] }}</strong>
                                            <span class="block text-[9px] font-bold text-slate-500 dark:text-slate-400 leading-tight uppercase tracking-wider line-clamp-2" title="{{ $kurir['type'] }}">{{ $kurir['type'] }}</span>
                                        </div>
                                    </div>

                                    {{-- Checkmark Overlay --}}
                                    <div class="check-icon absolute top-3 right-3 text-blue-600 dark:text-blue-400 opacity-0 scale-50 transition-all duration-300 z-20">
                                        <i class="mdi mdi-check-circle text-xl bg-white dark:bg-slate-900 rounded-full"></i>
                                    </div>
                                </div>
                            </label>
                        @endforeach
                    </div>

                    {{-- Aturan Umum Global Tambahan --}}
                    <div class="mt-8 pt-6 border-t border-slate-100 dark:border-slate-800">
                        <h4 class="text-xs font-black text-slate-800 dark:text-white uppercase tracking-widest mb-4">Kebijakan Global Platform</h4>

                        {{-- Fitur Asuransi Wajib --}}
                        <div class="flex justify-between items-center mb-5">
                            <div class="pr-3">
                                <strong class="block text-xs font-black text-slate-800 dark:text-white mb-0.5">Wajibkan Asuransi Ekspedisi</strong>
                                <span class="text-[10px] font-bold text-slate-500 dark:text-slate-400 leading-tight">Sistem akan memaksa asuransi otomatis (default) saat pembeli checkout untuk semua transaksi kurir.</span>
                            </div>
                            <div>
                                <input type="hidden" name="force_insurance" value="0">
                                <input type="checkbox" class="toggle-checkbox" id="insuranceToggle" name="force_insurance" value="1" {{ ($settings['force_insurance'] ?? '0') == '1' ? 'checked' : '' }}>
                                <label for="insuranceToggle" class="toggle-label"></label>
                            </div>
                        </div>

                        {{-- Fitur Gratis Ongkir Bersyarat --}}
                        <div>
                            <label class="block text-[10px] font-black text-emerald-600 dark:text-emerald-500 uppercase tracking-widest mb-2"><i class="mdi mdi-tag-heart"></i> Syarat Subsidi Ongkir (Sistem)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-emerald-50 dark:bg-emerald-900/20 border-emerald-200 dark:border-emerald-700 border-r-0 text-emerald-600 dark:text-emerald-400">Rp</span>
                                <input type="number" name="free_shipping_threshold" value="{{ $settings['free_shipping_threshold'] ?? '0' }}" class="form-control-input bg-white dark:bg-slate-800 border border-emerald-200 dark:border-emerald-700 text-slate-800 dark:text-white focus:border-emerald-500 font-bold text-sm shadow-inner dark:shadow-none" placeholder="Isi 0 untuk nonaktifkan">
                            </div>
                            <p class="text-[10px] font-bold text-slate-500 dark:text-slate-400 mt-2 m-0 leading-tight text-justify">Isi nominal minimum belanja (Cth: 5000000). Jika total belanja pembeli mencapai angka ini, ongkos kirim akan disubsidi penuh oleh sistem platform (bukan seller).</p>
                        </div>
                    </div>
                </div>

            </div>

        </div>

    </div>

    {{-- FLOATING ACTION BUTTON --}}
    <button type="submit" class="btn-save-floating flex items-center justify-center gap-2 px-6 py-3.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-black rounded-full shadow-lg shadow-blue-600/30 hover:-translate-y-1 transition-all outline-none border border-blue-500/50">
        <i class="mdi mdi-content-save-check-outline text-xl"></i> TERAPKAN REGULASI LOGISTIK
    </button>
</form>
@endsection