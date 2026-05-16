@extends('layouts.seller')

@section('title', 'Manajemen Pengiriman')

@section('content')

<style>
    /* ========================================= */
    /* ==  ENTERPRISE SELLER LOGISTICS CSS  == */
    /* ========================================= */

    .form-control-custom { width: 100%; border-radius: 0.5rem; font-size: 0.875rem; font-weight: 600; transition: all 0.2s; outline: none; border: 1px solid #cbd5e1; background-color: #f8fafc; }
    .form-control-custom:focus { background-color: #ffffff; box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15); border-color: #3b82f6; }

    .input-group { display: flex; align-items: stretch; width: 100%; }
    .input-group-text { display: flex; align-items: center; padding: 0.5rem 1rem; font-size: 0.875rem; font-weight: 700; background-color: #f1f5f9; border: 1px solid #cbd5e1; }
    .input-group-text.left { border-top-left-radius: 0.5rem; border-bottom-left-radius: 0.5rem; border-right: 0; }
    .input-group-text.right { border-top-right-radius: 0.5rem; border-bottom-right-radius: 0.5rem; border-left: 0; }
    .input-group .form-control-custom.right { border-top-right-radius: 0.5rem; border-bottom-right-radius: 0.5rem; border-top-left-radius: 0; border-bottom-left-radius: 0; }
    .input-group .form-control-custom.left { border-top-left-radius: 0.5rem; border-bottom-left-radius: 0.5rem; border-top-right-radius: 0; border-bottom-right-radius: 0; }

    .toggle-checkbox { display: none; }
    .toggle-label { width: 44px; height: 24px; border-radius: 24px; position: relative; cursor: pointer; transition: background-color 0.3s; flex-shrink: 0; background: #cbd5e1; border: 2px solid transparent; }
    .toggle-label::after { content: ''; position: absolute; top: 2px; left: 2px; width: 16px; height: 16px; border-radius: 50%; transition: transform 0.3s; background: white; box-shadow: 0 2px 4px rgba(0,0,0,0.2); }
    .toggle-checkbox:checked + .toggle-label { background: #2563eb; }
    .toggle-checkbox:checked + .toggle-label::after { transform: translateX(20px); }

    .service-tile { cursor: pointer; display: block; margin: 0; position: relative; }
    .service-input { position: absolute; opacity: 0; width: 0; height: 0; }
    .service-content { display: flex; align-items: center; gap: 1rem; padding: 1rem; border: 1px solid #e2e8f0; border-radius: 0.75rem; background-color: #ffffff; transition: all 0.2s ease-in-out; }
    .service-content:hover { border-color: #94a3b8; }

    .service-input:checked + .service-content { border-color: #2563eb; background-color: #eff6ff; box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.1); }
    .service-input:checked + .service-content .check-icon { opacity: 1; transform: scale(1); }
    .service-input:checked + .service-content .icon-box { background-color: #2563eb; color: #ffffff; border-color: #2563eb; }

    .icon-box { width: 3rem; height: 3rem; border-radius: 0.5rem; background-color: #f8fafc; border: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; color: #64748b; transition: all 0.2s; flex-shrink: 0; }
    
    /* Tambahan khusus styling Biteship agar beda dari Armada Toko */
    .icon-box-biteship { background-color: #e0e7ff; color: #4f46e5; border-color: #c7d2fe; }

    .check-icon { position: absolute; top: 1rem; right: 1rem; color: #2563eb; opacity: 0; transform: scale(0.5); transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1); font-size: 1.25rem; }

    .save-bar { position: fixed; bottom: 0; left: 0; right: 0; z-index: 40; padding: 1rem 2rem; background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px); border-top: 1px solid #e2e8f0; display: flex; justify-content: flex-end; box-shadow: 0 -4px 6px -1px rgba(0, 0, 0, 0.05); }
    @media (min-width: 1024px) { .save-bar { padding-left: 260px; } }

    @keyframes pulse-green { 0% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.4); } 70% { box-shadow: 0 0 0 6px rgba(16, 185, 129, 0); } 100% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); } }
    .api-live-indicator { width: 8px; height: 8px; background-color: #10b981; border-radius: 50%; display: inline-block; animation: pulse-green 2s infinite; }
</style>

<div class="min-h-screen bg-slate-50 pb-24 font-sans text-slate-800">

    {{-- SweetAlert Triggers --}}
    @if(session('success'))
        <script>document.addEventListener('DOMContentLoaded', () => Swal.fire({toast: true, position: 'top-end', icon: 'success', title: '{{ session('success') }}', showConfirmButton: false, timer: 3000, customClass: { popup: 'rounded-xl' }}));</script>
    @endif
    @if(session('error'))
        <script>document.addEventListener('DOMContentLoaded', () => Swal.fire({toast: true, position: 'top-end', icon: 'error', title: '{{ session('error') }}', showConfirmButton: false, timer: 4000, customClass: { popup: 'rounded-xl' }}));</script>
    @endif

    {{-- HEADER --}}
    <div class="bg-white border-b border-slate-200 px-6 py-8 md:px-10 md:py-10">
        <div class="max-w-6xl mx-auto flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
            <div>
                <h1 class="text-2xl font-black text-slate-900 tracking-tight flex items-center gap-3">
                    <i class="mdi mdi-truck-fast text-blue-600 text-3xl"></i> Layanan Pengiriman
                </h1>
                <p class="text-sm font-medium text-slate-500 mt-1 max-w-2xl">
                    Konfigurasikan armada logistik internal toko Anda dan aktifkan layanan ekspedisi nasional yang terintegrasi dengan API pusat.
                </p>
            </div>
            {{-- PERUBAHAN: Tombol Custom Dimatikan Sementara --}}
            <button type="button" class="px-5 py-2.5 bg-slate-100 border border-slate-200 text-slate-400 text-sm font-bold rounded-lg shadow-sm cursor-not-allowed flex items-center gap-2 flex-shrink-0" title="Dalam Pengembangan">
                <i class="mdi mdi-tools text-lg"></i> Custom Layanan (Coming Soon)
            </button>
        </div>
    </div>

    {{-- MAIN CONTENT --}}
    <div class="max-w-6xl mx-auto px-4 md:px-10 mt-8">

        <div class="bg-blue-50 border border-blue-200 rounded-xl p-5 mb-8 flex items-start gap-4 shadow-sm">
            <i class="mdi mdi-information text-blue-600 text-2xl mt-0.5"></i>
            <div>
                <h5 class="text-sm font-bold text-blue-900 mb-1">Strategi Pengiriman Material B2B</h5>
                <p class="text-xs font-medium text-blue-800/80 m-0 leading-relaxed">
                    Pesanan barang berat (seperti sak semen, besi beton) wajib dikirim menggunakan <b>Armada Internal Toko</b>. Sementara untuk barang ringan (paku, engsel), Anda dapat mengandalkan <b>Ekspedisi Nasional (Biteship)</b> yang tarifnya dihitung secara otomatis oleh sistem pusat.
                </p>
            </div>
        </div>

        <form action="{{ route('seller.pengaturan.pengiriman.store') }}" method="POST" id="mainLogisticsForm">
            @csrf
            <input type="hidden" name="action" value="save_preferences">

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

                {{-- ================================================= --}}
                {{-- KIRI: LAYANAN INTERNAL & CUSTOM (ARMADA TOKO)     --}}
                {{-- ================================================= --}}
                <div class="space-y-8">

                    @php
                        // Ambil pengaturan global Admin
                        $adminSettings = \Illuminate\Support\Facades\DB::table('tb_pengaturan')->pluck('setting_nilai', 'setting_nama')->toArray();
                        $tokoPrefs = json_decode($toko->logistics_preferences ?? '[]', true) ?: [];
                    @endphp

                    {{-- Card: Internal Toko --}}
                    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
                        <div class="px-6 py-4 border-b border-slate-100 flex items-center gap-3 bg-slate-50/50">
                            <div class="w-8 h-8 rounded bg-slate-200 text-slate-600 flex items-center justify-center"><i class="mdi mdi-store"></i></div>
                            <h3 class="text-base font-bold text-slate-800 m-0">Layanan Internal Toko</h3>
                        </div>

                        <div class="p-6 space-y-4">

                            {{-- BOPIS (Ambil di Toko) --}}
                            @if(($adminSettings['enable_store_pickup'] ?? '1') == '1')
                            <label class="service-tile w-full">
                                <input type="checkbox" name="preferences[bopis]" value="1" class="service-input" {{ isset($tokoPrefs['bopis']) && $tokoPrefs['bopis'] == '1' ? 'checked' : '' }}>
                                <div class="service-content">
                                    <div class="icon-box"><i class="mdi mdi-store-marker"></i></div>
                                    <div class="flex-1">
                                        <strong class="block text-sm font-bold text-slate-900 mb-0.5">Ambil di Toko (Pickup)</strong>
                                        <span class="text-xs font-medium text-slate-500">Pembeli mengambil langsung di gudang. Bebas Ongkir.</span>
                                    </div>
                                    <i class="mdi mdi-check-circle check-icon"></i>
                                </div>
                            </label>
                            @endif

                            {{-- Armada Toko --}}
                            @if(($adminSettings['enable_custom_fleet'] ?? '1') == '1')
                            <label class="service-tile w-full">
                                <input type="checkbox" name="preferences[custom_fleet]" id="fleetCheckbox" value="1" class="service-input" {{ isset($tokoPrefs['custom_fleet']) && $tokoPrefs['custom_fleet'] == '1' ? 'checked' : '' }} onchange="toggleFleetSettings()">
                                <div class="service-content">
                                    <div class="icon-box"><i class="mdi mdi-truck-flatbed"></i></div>
                                    <div class="flex-1">
                                        <strong class="block text-sm font-bold text-slate-900 mb-0.5">Armada Toko (Truk/Pickup)</strong>
                                        <span class="text-xs font-medium text-slate-500">Kirim material berat dengan armada mandiri Anda.</span>
                                    </div>
                                    <i class="mdi mdi-check-circle check-icon"></i>
                                </div>
                            </label>

                            {{-- Form Setting Armada --}}
                            <div id="fleetSettings" class="p-5 bg-slate-50 border border-slate-200 rounded-xl space-y-4 mt-2 transition-all" style="{{ isset($tokoPrefs['custom_fleet']) && $tokoPrefs['custom_fleet'] == '1' ? 'display: block;' : 'display: none;' }}">
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Tarif per Kilometer (Rp/Km)</label>
                                    <div class="input-group">
                                        <span class="input-group-text left">Rp</span>
                                        <input type="number" name="preferences[fleet_price_per_km]" value="{{ $tokoPrefs['fleet_price_per_km'] ?? '5000' }}" min="0" class="form-control-custom right px-3 py-2">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Maksimal Jangkauan (Km)</label>
                                    <div class="input-group">
                                        <input type="number" name="preferences[fleet_max_distance]" value="{{ $tokoPrefs['fleet_max_distance'] ?? $adminSettings['max_custom_fleet_distance'] ?? '50' }}" max="{{ $adminSettings['max_custom_fleet_distance'] ?? '100' }}" min="1" class="form-control-custom left px-3 py-2 text-right">
                                        <span class="input-group-text right">Km</span>
                                    </div>
                                    <p class="text-[10px] text-slate-500 mt-1.5 mb-0">Batas maksimal dari admin pusat: <b>{{ $adminSettings['max_custom_fleet_distance'] ?? '100' }} Km</b>.</p>
                                </div>
                            </div>
                            @endif

                            {{-- Pengiriman Darurat (CITO/Sameday) --}}
                            @if(($adminSettings['enable_emergency_delivery'] ?? '0') == '1')
                            <label class="service-tile w-full mt-4">
                                <input type="checkbox" name="preferences[emergency_delivery]" value="1" class="service-input" {{ isset($tokoPrefs['emergency_delivery']) && $tokoPrefs['emergency_delivery'] == '1' ? 'checked' : '' }}>
                                <div class="service-content !border-amber-200 hover:!border-amber-400">
                                    <div class="icon-box !bg-amber-50 !text-amber-600 !border-amber-200"><i class="mdi mdi-truck-fast"></i></div>
                                    <div class="flex-1">
                                        <strong class="block text-sm font-bold text-slate-900 mb-0.5">Pengiriman Darurat (CITO)</strong>
                                        <span class="text-xs font-medium text-slate-500">Sanggup mengirim material hari itu juga dengan tarif ekstra.</span>
                                    </div>
                                    <i class="mdi mdi-check-circle check-icon !text-amber-500"></i>
                                </div>
                            </label>
                            @endif

                        </div>
                    </div>

                    {{-- Card Layanan Custom COMING SOON --}}
                    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden relative">
                        <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                            <div class="flex items-center gap-3 opacity-50">
                                <div class="w-8 h-8 rounded bg-blue-100 text-blue-600 flex items-center justify-center"><i class="mdi mdi-cube-send"></i></div>
                                <h3 class="text-base font-bold text-slate-800 m-0">Layanan Buatan Anda</h3>
                            </div>
                        </div>

                        {{-- Overlay Coming Soon --}}
                        <div class="absolute inset-0 bg-white/60 backdrop-blur-[2px] z-10 flex flex-col items-center justify-center">
                            <div class="bg-slate-800 text-white text-xs font-black px-3 py-1.5 rounded-md tracking-widest uppercase mb-2 shadow-lg">Segera Hadir</div>
                        </div>

                        <div class="p-8 text-center opacity-40">
                            <i class="mdi mdi-crane text-5xl text-slate-300 mb-3"></i>
                            <h4 class="text-sm font-bold text-slate-700 mb-1">Fitur Sedang Dibangun</h4>
                            <p class="text-xs font-medium text-slate-500 max-w-xs mx-auto">
                                Nantinya Anda bisa membuat layanan mandiri seperti "Sewa Kuli Angkut" atau "Jasa Bongkar Muat" dengan tarif yang Anda tentukan sendiri.
                            </p>
                        </div>
                    </div>

                </div>

                {{-- ================================================= --}}
                {{-- KANAN: EKSPEDISI NASIONAL (API BITESHIP)          --}}
                {{-- ================================================= --}}
                <div class="space-y-6">

                    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
                        {{-- FIX UI: Mengubah header menjadi nuansa Biteship --}}
                        <div class="px-6 py-4 border-b border-slate-100 bg-indigo-50/50 flex justify-between items-center">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded bg-indigo-600 text-white flex items-center justify-center"><i class="mdi mdi-api"></i></div>
                                <div>
                                    <h3 class="text-base font-bold text-slate-800 m-0">Ekspedisi Nasional</h3>
                                    <p class="text-[10px] font-bold text-indigo-600 uppercase tracking-widest m-0">Powered by Biteship</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-1.5 px-2.5 py-1 bg-emerald-50 border border-emerald-200 rounded-full text-[10px] font-black uppercase tracking-widest text-emerald-600">
                                <span class="api-live-indicator"></span> API Terhubung
                            </div>
                        </div>

                        <div class="p-6">
                            <p class="text-xs font-medium text-slate-500 mb-4">Pilih layanan ekspedisi untuk pengiriman reguler, kargo, atau instan. Opsi yang muncul di bawah ini adalah ekspedisi yang telah diizinkan dan diaktifkan oleh Admin Pusat.</p>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                {{-- PERBAIKAN FATAL: Hack hidden agar form tidak error jika semua kurir di-uncheck --}}
                                <input type="hidden" name="seller_active_couriers[]" value="NONE_SELECTED_HACK">

                                @forelse($admin_active_couriers as $code)
                                    @if(isset($master_couriers[$code]))
                                        <label class="service-tile w-full" for="seller_{{ $code }}">
                                            {{-- PERBAIKAN FATAL: name="seller_active_couriers[]" agar sesuai dengan SellerController --}}
                                            <input type="checkbox" name="seller_active_couriers[]" value="{{ $code }}" id="seller_{{ $code }}" class="service-input" {{ in_array($code, $seller_active_couriers) ? 'checked' : '' }}>
                                            <div class="service-content">
                                                <div class="icon-box icon-box-biteship">
                                                    <i class="mdi {{ $master_couriers[$code]['icon'] }}"></i>
                                                </div>
                                                <div class="flex-1 pr-4">
                                                    <strong class="block text-sm font-bold text-slate-900 leading-tight mb-0.5">{{ $master_couriers[$code]['name'] }}</strong>
                                                    <span class="text-[11px] font-medium text-slate-500">{{ $master_couriers[$code]['type'] }}</span>
                                                </div>
                                                <i class="mdi mdi-check-circle check-icon"></i>
                                            </div>
                                        </label>
                                    @endif
                                @empty
                                    <div class="col-span-full p-5 bg-red-50 text-red-600 rounded-xl text-sm font-bold text-center border border-red-200">
                                        <i class="mdi mdi-alert-circle outline text-2xl block mb-2"></i>
                                        Sistem Pusat belum mengaktifkan ekspedisi Biteship apapun. Harap hubungi Admin.
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <div class="h-10"></div>
        </form>
    </div>
</div>

{{-- BOTTOM FIXED SAVE BAR --}}
<div class="save-bar">
    <button type="submit" form="mainLogisticsForm" class="px-8 py-3 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold rounded-xl shadow-lg shadow-blue-500/30 transition-all outline-none flex items-center gap-2">
        <i class="mdi mdi-content-save-check-outline text-lg"></i> Simpan Konfigurasi
    </button>
</div>

{{-- MODAL LAYANAN CUSTOM (TETAP ADA DI KODE TAPI DISEMBUNYIKAN FUNGSI TOMBOLNYA) --}}
<div id="kurirModal" class="fixed inset-0 z-[100] hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div id="modalOverlay" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity opacity-0 duration-300" onclick="closeModal()"></div>

    <div class="fixed inset-0 z-10 overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4 text-center">
            <div id="modalPanel" class="relative w-full max-w-md transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all opacity-0 scale-95 duration-300 border border-slate-200">
                <form action="{{ route('seller.pengaturan.pengiriman.store') }}" method="POST" id="kurirForm">
                    @csrf
                    <input type="hidden" name="action" id="form-action" value="tambah">
                    <input type="hidden" name="kurir_id" id="kurir_id">

                    <div class="bg-slate-50 px-6 py-4 border-b border-slate-200 flex items-center justify-between">
                        <h3 class="text-base font-black text-slate-900" id="modalTitle">Tambah Layanan Khusus</h3>
                        <button type="button" onclick="closeModal()" class="text-slate-400 hover:text-red-500 transition-colors outline-none">
                            <i class="mdi mdi-close text-xl"></i>
                        </button>
                    </div>

                    <div class="p-6 space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Nama Layanan</label>
                            <input type="text" name="nama_kurir" id="nama_kurir" class="form-control-custom px-4 py-2.5" placeholder="Cth: Sewa Kuli Angkut" required>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">Estimasi Waktu</label>
                                <input type="text" name="estimasi_waktu" id="estimasi_waktu" class="form-control-custom px-4 py-2.5" placeholder="Cth: 1-2 Hari" required>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">Biaya (Rp)</label>
                                <input type="number" name="biaya" id="biaya" class="form-control-custom px-4 py-2.5" placeholder="0 = Gratis" required min="0">
                            </div>
                        </div>
                    </div>

                    <div class="bg-slate-50 px-6 py-4 border-t border-slate-200 flex justify-end gap-3">
                        <button type="button" onclick="closeModal()" class="px-5 py-2.5 bg-white border border-slate-300 text-slate-700 font-bold rounded-xl hover:bg-slate-100 transition-colors text-sm outline-none">Batal</button>
                        <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-sm text-sm transition-colors outline-none">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<form id="deleteForm" method="POST" style="display: none;">
    @csrf @method('DELETE')
</form>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function toggleFleetSettings() {
        const checkbox = document.getElementById('fleetCheckbox');
        const settings = document.getElementById('fleetSettings');
        if(checkbox && settings) {
            settings.style.display = checkbox.checked ? 'block' : 'none';
        }
    }

    const modal = document.getElementById('kurirModal');
    const modalOverlay = document.getElementById('modalOverlay');
    const modalPanel = document.getElementById('modalPanel');

    function openModal(mode, data = null) {
        document.getElementById('kurirForm').reset();

        if(mode === 'tambah') {
            document.getElementById('modalTitle').textContent = 'Tambah Layanan Khusus';
            document.getElementById('form-action').value = 'tambah';
            document.getElementById('kurir_id').value = '';
        } else if(mode === 'edit' && data) {
            document.getElementById('modalTitle').textContent = 'Edit Layanan';
            document.getElementById('form-action').value = 'update';
            document.getElementById('kurir_id').value = data.id;
            document.getElementById('nama_kurir').value = data.nama_kurir;
            document.getElementById('estimasi_waktu').value = data.estimasi_waktu;
            document.getElementById('biaya').value = data.biaya;
        }

        modal.classList.remove('hidden');
        void modal.offsetWidth;
        modalOverlay.classList.replace('opacity-0', 'opacity-100');
        modalPanel.classList.replace('opacity-0', 'opacity-100');
        modalPanel.classList.replace('scale-95', 'scale-100');
    }

    function closeModal() {
        modalOverlay.classList.replace('opacity-100', 'opacity-0');
        modalPanel.classList.replace('opacity-100', 'opacity-0');
        modalPanel.classList.replace('scale-100', 'scale-95');
        setTimeout(() => modal.classList.add('hidden'), 300);
    }

    function confirmDelete(id) {
        Swal.fire({
            title: 'Hapus Layanan?',
            text: "Layanan ini akan dihapus permanen.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#94a3b8',
            confirmButtonText: 'Hapus',
            cancelButtonText: 'Batal',
            reverseButtons: true,
            customClass: { popup: 'rounded-2xl' }
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.getElementById('deleteForm');
                form.action = `/seller/pengaturan/pengiriman/${id}`;
                form.submit();
            }
        });
    }
</script>

@endsection