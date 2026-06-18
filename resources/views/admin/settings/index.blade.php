@extends('layouts.admin')

@section('title', 'Konfigurasi Sistem Platform')

@push('styles')
<style>
    /* ========================================= */
    /* ==  PREMIUM SYSTEM SETTINGS CSS        == */
    /* ========================================= */

    /* Custom Form Input & Focus State */
    .form-control-custom { width: 100%; border-radius: 0.75rem; font-size: 0.875rem; font-weight: 700; transition: all 0.2s; outline: none; border: 1px solid #e2e8f0; }
    .form-control-custom:focus { box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15); border-color: #3b82f6; }

    /* Custom Input Group */
    .input-group { display: flex; align-items: stretch; width: 100%; flex-wrap: nowrap !important; }
    .input-group > .form-control-custom { flex: 1 1 0%; min-width: 0; }
    .input-group-text { display: flex; align-items: center; padding: 0.625rem 1rem; font-size: 0.875rem; font-weight: 800; border-radius: 0.75rem; border: 1px solid #e2e8f0; white-space: nowrap; }

    /* Perbaikan Sudut Border Radius agar menempel rapi */
    .input-group > .form-control-custom:not(:first-child) { border-top-left-radius: 0; border-bottom-left-radius: 0; }
    .input-group > .input-group-text:not(:first-child) { border-top-left-radius: 0; border-bottom-left-radius: 0; border-left: 0; }
    .input-group > .form-control-custom:not(:last-child) { border-top-right-radius: 0; border-bottom-right-radius: 0; border-right: 0; }
    .input-group > .input-group-text:not(:last-child) { border-top-right-radius: 0; border-bottom-right-radius: 0; border-right: 0; }

    /* MODERN TOGGLE SWITCH */
    .toggle-checkbox { display: none; }
    .toggle-label { width: 46px; height: 26px; border-radius: 30px; position: relative; cursor: pointer; transition: 0.3s; flex-shrink: 0; background: #cbd5e1; }
    .toggle-label::after { content: ''; position: absolute; top: 3px; left: 3px; width: 20px; height: 20px; border-radius: 50%; transition: 0.3s; background: white; box-shadow: 0 2px 4px rgba(0,0,0,0.2); }
    .toggle-checkbox:checked + .toggle-label { background: #10b981; }
    .toggle-checkbox:checked + .toggle-label::after { transform: translateX(20px); }

    /* IMAGE UPLOAD PREVIEW BOX */
    .img-preview-box { position: relative; width: 100%; border: 2px dashed #cbd5e1; border-radius: 1rem; overflow: hidden; display: flex; align-items: center; justify-content: center; flex-direction: column; background: #f8fafc; cursor: pointer; transition: all 0.3s; }
    .img-preview-box:hover { border-color: #3b82f6; background: #eff6ff; }
    .img-preview-box img { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; z-index: 10; display: none; }

    /* Custom Tab Styles */
    .tab-content > .tab-pane { display: none; }
    .tab-content > .active { display: block; animation: fade-in 0.4s ease-out; }
    @keyframes fade-in { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

    /* Floating Save Bar */
    .save-bar { position: fixed; bottom: 0; left: 0; right: 0; z-index: 1000; transition: padding-left 0.3s ease; border-top: 1px solid #e2e8f0; }
    @media (min-width: 1024px) { .save-bar { padding-left: 260px; } }

    /* ========================================= */
    /* == POLYFILL DARK MODE (ANTI-PUTIH)     == */
    /* ========================================= */
    .dark .dark\:bg-slate-950 { background-color: #020617 !important; }
    .dark .dark\:bg-slate-900 { background-color: #0f172a !important; }
    .dark .dark\:bg-slate-900\/90 { background-color: rgba(15, 23, 42, 0.9) !important; }
    .dark .dark\:bg-slate-800 { background-color: #1e293b !important; }
    .dark .dark\:bg-slate-800\/50 { background-color: rgba(30, 41, 59, 0.5) !important; }
    .dark .dark\:bg-slate-800\/40 { background-color: rgba(30, 41, 59, 0.4) !important; }
    .dark .dark\:bg-slate-700 { background-color: #334155 !important; }
    .dark .dark\:bg-transparent { background-color: transparent !important; }

    .dark .dark\:border-slate-800 { border-color: #1e293b !important; }
    .dark .dark\:border-slate-700 { border-color: #334155 !important; }
    .dark .dark\:border-slate-700\/50 { border-color: rgba(51, 65, 85, 0.5) !important; }

    .dark .dark\:bg-blue-500\/10 { background-color: rgba(59, 130, 246, 0.1) !important; }
    .dark .dark\:text-blue-400 { color: #60a5fa !important; }
    .dark .dark\:shadow-\[inset_0_1px_0_rgba\(255\,255\,255\,0\.05\)\] { box-shadow: inset 0 1px 0 rgba(255,255,255,0.05) !important; }

    .dark .form-control-custom { background-color: #0f172a !important; border-color: #334155 !important; color: #f8fafc !important; }
    .dark .form-control-custom:focus { border-color: #3b82f6 !important; box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2) !important; }
    .dark .input-group-text { background-color: #1e293b !important; border-color: #334155 !important; color: #94a3b8 !important; }

    .dark .img-preview-box { border-color: #334155; background: #0f172a; }
    .dark .img-preview-box:hover { border-color: #3b82f6; background: rgba(59, 130, 246, 0.1); }

    .dark .toggle-label { background: #334155 !important; }
    .dark .toggle-label::after { background: #94a3b8 !important; box-shadow: none !important; }
    .dark .toggle-checkbox:checked + .toggle-label { background: #059669 !important; }
    .dark .toggle-checkbox:checked + .toggle-label::after { background: white !important; }

    .dark .dark\:text-white { color: #ffffff !important; }
    .dark .dark\:text-slate-100 { color: #f1f5f9 !important; }
    .dark .dark\:text-slate-200 { color: #e2e8f0 !important; }
    .dark .dark\:text-slate-300 { color: #cbd5e1 !important; }
    .dark .dark\:text-slate-400 { color: #94a3b8 !important; }
    .dark .dark\:text-emerald-500 { color: #10b981 !important; }
    .dark .dark\:text-amber-500 { color: #f59e0b !important; }

    .dark .modal-content { background-color: #0f172a !important; border: 1px solid #1e293b !important; color: #f8fafc !important; }
    .dark .modal-header, .dark .modal-footer { border-color: #1e293b !important; }
    .dark .btn-close { filter: invert(1) grayscale(100%) brightness(200%); }

    .dark .dark\:bg-emerald-500\/10 { background-color: rgba(16, 185, 129, 0.1) !important; }
    .dark .dark\:border-emerald-500\/30 { border-color: rgba(16, 185, 129, 0.3) !important; }
    .dark .dark\:text-emerald-400 { color: #34d399 !important; }

    .dark .dark\:bg-purple-500\/10 { background-color: rgba(168, 85, 247, 0.1) !important; }
    .dark .dark\:border-purple-500\/30 { border-color: rgba(168, 85, 247, 0.3) !important; }
    .dark .dark\:text-purple-400 { color: #c084fc !important; }

    .dark .dark\:bg-amber-500\/10 { background-color: rgba(245, 158, 11, 0.1) !important; }
    .dark .dark\:border-amber-500\/30 { border-color: rgba(245, 158, 11, 0.3) !important; }
    .dark .dark\:text-amber-400 { color: #fbbf24 !important; }
</style>
@endpush

@section('content')

{{-- HEADER HALAMAN --}}
<div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-4 mb-8">
    <div>
        <h2 class="text-2xl md:text-3xl font-black text-slate-800 dark:text-white tracking-tight mb-1 transition-colors duration-300">
            Pengaturan Sistem & Website
        </h2>
        <div class="flex items-center gap-2 text-xs font-bold text-slate-500 dark:text-slate-400 transition-colors duration-300">
            <a href="{{ route('admin.dashboard') }}" class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors text-decoration-none">Dashboard</a>
            <i class="mdi mdi-chevron-right text-sm"></i>
            <span class="text-blue-600 dark:text-blue-400">Konfigurasi Platform</span>
        </div>
        <p class="text-[11px] font-bold text-slate-500 dark:text-slate-400 mt-2 m-0 max-w-xl leading-relaxed">
            Pusat kendali (Engine Room) untuk mengatur identitas, tampilan website, skema pembagian komisi, popup promo, regulasi seller, dan integrasi API.
        </p>
    </div>
</div>

{{-- WAJIB: enctype="multipart/form-data" untuk Upload Gambar Website --}}
<form action="{{ route('admin.settings.update') }}" method="POST" id="mainSettingsForm" enctype="multipart/form-data">
    @csrf

    <div class="flex flex-col lg:flex-row gap-8 pb-32 items-start relative">

        {{-- NAV PILLS (SIDEBAR KIRI PENGATURAN) --}}
        <div class="w-full lg:w-72 flex-shrink-0 lg:sticky lg:top-[90px] z-20 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-3 rounded-2xl shadow-sm transition-colors duration-300">
            <div class="nav flex flex-col space-y-1" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                @php
                    $tabs = [
                        ['id' => 'general', 'icon' => 'mdi-store-cog-outline', 'label' => 'Identitas & Umum'],
                        ['id' => 'frontend', 'icon' => 'mdi-monitor-dashboard', 'label' => 'Tampilan Website'],
                        ['id' => 'finance', 'icon' => 'mdi-cash-multiple', 'label' => 'Keuangan & Biaya'],
                        ['id' => 'api', 'icon' => 'mdi-code-json', 'label' => 'API & Integrasi'],
                        ['id' => 'catalog', 'icon' => 'mdi-shape-outline', 'label' => 'Aturan Katalog']
                    ];
                @endphp

                @foreach($tabs as $index => $tab)
                    <button class="nav-link w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-black transition-all outline-none text-left {{ $index === 0 ? 'active bg-blue-50 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400 shadow-inner dark:shadow-[inset_0_1px_0_rgba(255,255,255,0.05)]' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800/50 hover:text-slate-800 dark:hover:text-slate-200' }}"
                            id="tab-{{ $tab['id'] }}" data-bs-toggle="pill" data-bs-target="#panel-{{ $tab['id'] }}" type="button" role="tab">
                        <i class="mdi {{ $tab['icon'] }} text-xl"></i> {{ $tab['label'] }}
                    </button>
                @endforeach
            </div>
        </div>

        {{-- AREA KONTEN KANAN --}}
        <div class="flex-grow w-full max-w-4xl tab-content" id="v-pills-tabContent">

            {{-- 1. PANEL: IDENTITAS & UMUM --}}
            <div class="tab-pane fade show active" id="panel-general" role="tabpanel">
                <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-[2rem] p-6 lg:p-8 shadow-sm transition-colors duration-300">

                    {{-- Mode Maintenance --}}
                    <div class="flex justify-between items-center p-5 bg-rose-50 dark:bg-rose-500/10 border border-rose-200 dark:border-rose-500/20 rounded-2xl mb-6 transition-colors duration-300">
                        <div class="pr-4">
                            <strong class="block text-sm font-black text-rose-600 dark:text-rose-400 mb-1"><i class="mdi mdi-alert-outline"></i> Mode Pemeliharaan (Maintenance)</strong>
                            <span class="text-[11px] font-bold text-rose-500/80 dark:text-rose-400/80 leading-tight">Tutup akses website dari pembeli sementara waktu untuk perbaikan sistem.</span>
                        </div>
                        <div>
                            <input type="checkbox" class="toggle-checkbox" id="maintenanceToggle" name="maintenance_mode" value="1" {{ ($settings['maintenance_mode'] ?? '0') == '1' ? 'checked' : '' }}>
                            <label for="maintenanceToggle" class="toggle-label m-0" style="background: #fecaca;"></label>
                        </div>
                    </div>

                    <div class="border-b border-slate-100 dark:border-slate-800 pb-5 mb-6 mt-2">
                        <h3 class="text-xl font-black text-slate-800 dark:text-white m-0 flex items-center gap-2"><i class="mdi mdi-card-account-details-outline text-blue-500"></i> Profil Platform</h3>
                        <p class="text-xs font-bold text-slate-500 dark:text-slate-400 mt-1 mb-0">Informasi dasar yang akan ditampilkan ke pengguna web dan mesin pencari.</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-[11px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-2 ml-1">Nama Platform</label>
                            <input type="text" name="app_name" value="{{ $settings['app_name'] ?? 'Pondasikita' }}" class="form-control-custom p-3 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-white shadow-inner dark:shadow-none">
                        </div>
                        <div>
                            <label class="block text-[11px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-2 ml-1">Email Kontak Dukungan</label>
                            <input type="email" name="support_email" value="{{ $settings['support_email'] ?? '' }}" class="form-control-custom p-3 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-white shadow-inner dark:shadow-none" placeholder="cs@pondasikita.com">
                        </div>

                        {{-- Social Media Links --}}
                        <div>
                            <label class="block text-[11px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-2 ml-1">Link Instagram</label>
                            <div class="input-group">
                                <span class="input-group-text bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 border-r-0"><i class="mdi mdi-instagram text-rose-500 text-lg"></i></span>
                                <input type="url" name="social_instagram" value="{{ $settings['social_instagram'] ?? '' }}" class="form-control-custom bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-white shadow-inner dark:shadow-none" placeholder="https://instagram.com/...">
                            </div>
                        </div>
                        <div>
                            <label class="block text-[11px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-2 ml-1">Link Facebook / Tiktok</label>
                            <div class="input-group">
                                <span class="input-group-text bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 border-r-0"><i class="mdi mdi-facebook text-blue-600 text-lg"></i></span>
                                <input type="url" name="social_facebook" value="{{ $settings['social_facebook'] ?? '' }}" class="form-control-custom bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-white shadow-inner dark:shadow-none" placeholder="https://facebook.com/...">
                            </div>
                        </div>

                        <div class="md:col-span-2 border-t border-slate-100 dark:border-slate-800 pt-5 mt-2">
                            <label class="block text-[11px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-2 ml-1">Deskripsi Singkat (SEO Meta)</label>
                            <textarea name="seo_description" rows="3" class="form-control-custom p-4 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-white shadow-inner dark:shadow-none resize-none leading-relaxed">{{ $settings['seo_description'] ?? '' }}</textarea>
                            <p class="text-[10px] font-bold text-slate-400 mt-2 mb-0 ml-1">Tulis deskripsi memikat maksimal 160 karakter untuk optimasi pencarian di Google.</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 2. PANEL: TAMPILAN WEBSITE (FRONTEND) --}}
            <div class="tab-pane fade" id="panel-frontend" role="tabpanel">

                {{-- Banner Utama Website (Slider 4 Slot) --}}
                <div class="bg-white dark:bg-slate-900 border-t-4 border-t-blue-500 border-x border-b border-slate-200 dark:border-slate-800 rounded-3xl p-6 lg:p-8 shadow-sm transition-colors duration-300 mb-8">
                    <div class="border-b border-slate-100 dark:border-slate-800 pb-5 mb-6">
                        <h3 class="text-xl font-black text-slate-800 dark:text-white m-0 flex items-center gap-2"><i class="mdi mdi-monitor-dashboard text-blue-500"></i> Banner Utama Website (Slider)</h3>
                        <p class="text-xs font-bold text-slate-500 dark:text-slate-400 mt-1 mb-0">Maksimal 4 gambar banner untuk ditampilkan bergantian (slider) di halaman utama. Semua murni dari Anda.</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        @for($i = 1; $i <= 4; $i++)
                            @php
                                $imgKey = 'hero_image_' . $i;
                                $titleKey = 'hero_title_' . $i;
                                $descKey = 'hero_subtitle_' . $i;

                                // LOGIKA BARU ANTI-ERROR: Siapkan URL gambar jika ada
                                $imgSrc = !empty($settings[$imgKey]) ? asset('storage/' . $settings[$imgKey]) : '';
                            @endphp
                            <div class="bg-slate-50 dark:bg-slate-800/50 p-5 rounded-2xl border border-slate-200 dark:border-slate-700 transition-colors">
                                <label class="block text-[11px] font-black text-blue-600 dark:text-blue-400 uppercase tracking-widest mb-3 flex items-center gap-2">
                                    <span class="w-5 h-5 rounded bg-blue-100 dark:bg-blue-500/20 flex items-center justify-center">{{ $i }}</span> Slide Banner
                                </label>

                                <input type="file" name="{{ $imgKey }}" id="{{ $imgKey }}Input" class="hidden" accept="image/*" onchange="previewImage(this, '{{ $imgKey }}Preview', '{{ $imgKey }}Placeholder')">

                                {{-- Preview Box yang Menghilangkan Gambar Rusak Menggunakan onerror HTML --}}
                                <label for="{{ $imgKey }}Input" class="img-preview-box aspect-[21/9] md:aspect-[3/1] mb-4 border-2 border-dashed border-slate-300 dark:border-slate-600 hover:border-blue-500 dark:hover:border-blue-400 rounded-xl overflow-hidden relative flex items-center justify-center cursor-pointer bg-white dark:bg-slate-900 transition-colors">

                                    {{-- Elemen Upload Standby (Akan disembunyikan jika gambar berhasil dimuat) --}}
                                    <div id="{{ $imgKey }}Placeholder" class="text-center z-20 p-3 bg-white/80 dark:bg-slate-900/80 backdrop-blur rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm transition-colors" style="{{ $imgSrc ? 'display: none;' : 'display: block;' }}">
                                        <i class="mdi mdi-cloud-upload-outline text-2xl text-blue-500 mb-1"></i>
                                        <p class="text-[10px] font-black text-slate-700 dark:text-slate-300 m-0 uppercase tracking-widest">Upload Slide {{ $i }}</p>
                                    </div>

                                    {{-- Tag Image dengan fallback onerror yang kebal error --}}
                                    <img id="{{ $imgKey }}Preview" src="{{ $imgSrc }}"
                                         style="{{ $imgSrc ? 'display: block;' : 'display: none;' }}"
                                         onerror="this.style.display='none'; document.getElementById('{{ $imgKey }}Placeholder').style.display='block';"
                                         class="absolute inset-0 w-full h-full object-cover z-10">

                                </label>

                                <div class="space-y-3">
                                    <div>
                                        <input type="text" name="{{ $titleKey }}" value="{{ $settings[$titleKey] ?? '' }}" class="form-control-custom p-3 text-xs bg-white dark:bg-slate-900 border-slate-200 dark:border-slate-700" placeholder="Judul Teks Banner (Opsional)">
                                    </div>
                                    <div>
                                        <input type="text" name="{{ $descKey }}" value="{{ $settings[$descKey] ?? '' }}" class="form-control-custom p-3 text-xs bg-white dark:bg-slate-900 border-slate-200 dark:border-slate-700" placeholder="Sub-judul Banner (Opsional)">
                                    </div>
                                </div>
                            </div>
                        @endfor
                    </div>
                </div>

                {{-- Popup Promo (Ala Shopee) --}}
                <div class="bg-white dark:bg-slate-900 border-t-4 border-t-amber-500 border-x border-b border-slate-200 dark:border-slate-800 rounded-3xl p-6 lg:p-8 shadow-sm transition-colors duration-300 mb-8">
                    <div class="border-b border-slate-100 dark:border-slate-800 pb-5 mb-6">
                        <h3 class="text-xl font-black text-slate-800 dark:text-white m-0 flex items-center gap-2"><i class="mdi mdi-message-image text-amber-500"></i> Popup Promo (Welcome Screen)</h3>
                        <p class="text-xs font-bold text-slate-500 dark:text-slate-400 mt-1 mb-0">Tampilkan popup promosi otomatis saat user pertama kali membuka website (Seperti event Flash Sale, dll).</p>
                    </div>

                    <div class="flex justify-between items-center p-5 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700/50 rounded-2xl mb-6 transition-colors duration-300">
                        <div class="pr-4">
                            <strong class="block text-sm font-black text-slate-800 dark:text-white mb-1">Aktifkan Popup Promo</strong>
                            <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400 leading-tight">Mulai tampilkan popup promosi di halaman utama pengunjung.</span>
                        </div>
                        <div>
                            <input type="checkbox" class="toggle-checkbox" id="popupToggle" name="enable_welcome_popup" value="1" {{ ($settings['enable_welcome_popup'] ?? '0') == '1' ? 'checked' : '' }}>
                            <label for="popupToggle" class="toggle-label m-0"></label>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-[11px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-2 ml-1">Gambar Poster Popup</label>

                            <input type="file" name="popup_image" id="popupImageInput" class="hidden" accept="image/*" onchange="previewImage(this, 'popupPreview', 'popupPlaceholder')">

                            {{-- Aspect ratio portrait ala popup HP dengan Onerror Fallback --}}
                            @php
                                $popupSrc = !empty($settings['popup_image']) ? asset('storage/' . $settings['popup_image']) : '';
                            @endphp
                            <label for="popupImageInput" class="img-preview-box aspect-[3/4] max-w-xs mx-auto md:mx-0 border-2 border-dashed border-slate-300 dark:border-slate-700 hover:border-blue-500 rounded-xl overflow-hidden relative flex items-center justify-center cursor-pointer bg-white dark:bg-slate-900 transition-colors">

                                <div id="popupPlaceholder" class="text-center z-20 p-4 bg-white/80 dark:bg-slate-900/80 backdrop-blur rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm m-4 transition-colors" style="{{ $popupSrc ? 'display: none;' : 'display: block;' }}">
                                    <i class="mdi mdi-image-plus text-3xl text-amber-500 mb-1"></i>
                                    <p class="text-xs font-black text-slate-700 dark:text-slate-300 m-0">Upload Poster</p>
                                    <p class="text-[10px] font-bold text-slate-500 dark:text-slate-400 m-0 mt-1">Potret (600x800px)</p>
                                </div>

                                <img id="popupPreview" src="{{ $popupSrc }}"
                                     style="{{ $popupSrc ? 'display: block;' : 'display: none;' }}"
                                     onerror="this.style.display='none'; document.getElementById('popupPlaceholder').style.display='block';"
                                     class="absolute inset-0 w-full h-full object-cover z-10">
                            </label>
                        </div>

                        <div class="space-y-6 pt-2">
                            <div>
                                <label class="block text-[11px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-2 ml-1">Link Tujuan Popup (Opsional)</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 border-r-0"><i class="mdi mdi-link-variant"></i></span>
                                    <input type="url" name="popup_link" value="{{ $settings['popup_link'] ?? '' }}" class="form-control-custom bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-white shadow-inner dark:shadow-none" placeholder="https://domain.com/promo...">
                                </div>
                                <p class="text-[10px] font-bold text-slate-400 mt-2 mb-0 ml-1">Arahkan user ke halaman tertentu saat poster ditekan.</p>
                            </div>

                            <div>
                                <label class="block text-[11px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-2 ml-1">Frekuensi Tampil</label>
                                <select name="popup_frequency" class="form-control-custom p-3 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-white shadow-inner dark:shadow-none cursor-pointer">
                                    <option value="always" {{ ($settings['popup_frequency'] ?? 'always') == 'always' ? 'selected' : '' }}>Selalu tampil saat di-refresh</option>
                                    <option value="once_a_day" {{ ($settings['popup_frequency'] ?? '') == 'once_a_day' ? 'selected' : '' }}>Tampil 1 kali sehari per User</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Opsi Tampilan Lainnya --}}
                <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 lg:p-8 shadow-sm transition-colors duration-300">
                    <div class="border-b border-slate-100 dark:border-slate-800 pb-5 mb-6">
                        <h3 class="text-xl font-black text-slate-800 dark:text-white m-0 flex items-center gap-2"><i class="mdi mdi-view-dashboard-variant-outline text-emerald-500"></i> Widget Homepage Beranda</h3>
                        <p class="text-xs font-bold text-slate-500 dark:text-slate-400 mt-1 mb-0">Atur bagian-bagian khusus yang ingin ditampilkan di halaman depan web pembeli.</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="flex justify-between items-center p-4 border border-slate-200 dark:border-slate-700 rounded-xl">
                            <div>
                                <strong class="block text-sm font-black text-slate-800 dark:text-white">Section: Toko Resmi Teratas</strong>
                                <span class="text-[10px] font-bold text-slate-500 dark:text-slate-400">Tampilkan slider mitra toko "Official Store" di beranda.</span>
                            </div>
                            <div>
                                <input type="checkbox" class="toggle-checkbox" id="showTopStores" name="show_top_stores" value="1" {{ ($settings['show_top_stores'] ?? '1') == '1' ? 'checked' : '' }}>
                                <label for="showTopStores" class="toggle-label m-0"></label>
                            </div>
                        </div>

                        <div class="flex justify-between items-center p-4 border border-slate-200 dark:border-slate-700 rounded-xl">
                            <div>
                                <strong class="block text-sm font-black text-slate-800 dark:text-white">Section: Produk Paling Laris</strong>
                                <span class="text-[10px] font-bold text-slate-500 dark:text-slate-400">Otomatis menampilkan material dengan penjualan terbanyak.</span>
                            </div>
                            <div>
                                <input type="checkbox" class="toggle-checkbox" id="showBestSelling" name="show_best_selling" value="1" {{ ($settings['show_best_selling'] ?? '1') == '1' ? 'checked' : '' }}>
                                <label for="showBestSelling" class="toggle-label m-0"></label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 3. PANEL: KEUANGAN & BIAYA --}}
            <div class="tab-pane fade" id="panel-finance" role="tabpanel">
                {{-- SEGMEN: KOMISI SELLER --}}
                <div class="bg-white dark:bg-slate-900 border-t-4 border-t-blue-500 border-x border-b border-slate-200 dark:border-slate-800 rounded-3xl p-6 lg:p-8 shadow-sm transition-colors duration-300 mb-8">
                    <div class="border-b border-slate-100 dark:border-slate-800 pb-5 mb-6">
                        <h3 class="text-xl font-black text-slate-800 dark:text-white m-0 flex items-center gap-2"><i class="mdi mdi-percent-circle-outline text-blue-500"></i> Skema Komisi Mitra (Seller)</h3>
                        <p class="text-xs font-bold text-slate-500 dark:text-slate-400 mt-1 mb-0">Potongan persentase yang dibebankan kepada penjual setiap transaksi berhasil, dibedakan berdasarkan kasta/tier toko.</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        {{-- Reguler --}}
                        <div>
                            <label class="flex justify-between items-center text-[11px] font-black text-slate-600 dark:text-slate-400 uppercase tracking-widest mb-2 ml-1">
                                <span><i class="mdi mdi-storefront-outline"></i> Reguler</span>
                                <button type="button" class="info-btn btn-show-info flex items-center justify-center w-5 h-5 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-400 dark:text-slate-500 hover:bg-blue-50 hover:text-blue-500 dark:hover:bg-blue-500/20 dark:hover:text-blue-400 transition-all duration-300 outline-none group" data-title="Toko Reguler" data-desc="Toko baru atau penjual standar tanpa legalitas perusahaan. Disarankan diberi komisi sangat rendah (0% - 0.5%) sebagai strategi bakar uang untuk menarik minat seller bergabung.">
                                    <i class="mdi mdi-help text-xs group-hover:scale-110 transition-transform"></i>
                                </button>
                            </label>
                            <div class="input-group">
                                <input type="number" name="commission_regular_percent" value="{{ $settings['commission_regular_percent'] ?? '0.5' }}" step="0.1" min="0" class="form-control-input form-control-custom bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-white shadow-inner dark:shadow-none text-center text-lg">
                                <span class="input-group-text bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 border-l-0 text-slate-500">%</span>
                            </div>
                            <p class="text-[10px] font-bold text-slate-400 mt-2 mb-0 ml-1 text-center">Toko standar baru.</p>
                        </div>

                        {{-- Power Merchant --}}
                        <div>
                            <label class="flex justify-between items-center text-[11px] font-black text-emerald-600 dark:text-emerald-500 uppercase tracking-widest mb-2 ml-1">
                                <span><i class="mdi mdi-lightning-bolt"></i> Power Merchant</span>
                                <button type="button" class="info-btn btn-show-info flex items-center justify-center w-5 h-5 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-400 dark:text-slate-500 hover:bg-emerald-50 hover:text-emerald-500 dark:hover:bg-emerald-500/20 dark:hover:text-emerald-400 transition-all duration-300 outline-none group" data-title="Power Merchant" data-desc="Toko yang sudah laris dan punya reputasi baik. Disarankan komisi menengah (1.5% - 2%). Seller bersedia dipotong lebih tinggi karena mendapat Badge Hijau.">
                                    <i class="mdi mdi-help text-xs group-hover:scale-110 transition-transform"></i>
                                </button>
                            </label>
                            <div class="input-group">
                                <input type="number" name="commission_power_percent" value="{{ $settings['commission_power_percent'] ?? '2.0' }}" step="0.1" min="0" class="form-control-input form-control-custom bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/30 text-emerald-800 dark:text-emerald-400 shadow-inner dark:shadow-none text-center text-lg">
                                <span class="input-group-text bg-emerald-500 border border-emerald-500 border-l-0 text-white shadow-md shadow-emerald-500/20">%</span>
                            </div>
                            <p class="text-[10px] font-bold text-slate-400 mt-2 mb-0 ml-1 text-center">Toko laris reputasi baik.</p>
                        </div>

                        {{-- Official Store --}}
                        <div>
                            <label class="flex justify-between items-center text-[11px] font-black text-purple-600 dark:text-purple-400 uppercase tracking-widest mb-2 ml-1">
                                <span><i class="mdi mdi-check-decagram"></i> Official Store</span>
                                <button type="button" class="info-btn btn-show-info flex items-center justify-center w-5 h-5 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-400 dark:text-slate-500 hover:bg-purple-50 hover:text-purple-500 dark:hover:bg-purple-500/20 dark:hover:text-purple-400 transition-all duration-300 outline-none group" data-title="Official Store" data-desc="Distributor resmi (PT/CV) yang menjual partai besar B2B. Disarankan komisi tertinggi (3% - 5%) karena platform memberikan mereka akses langsung ke proyek kakap.">
                                    <i class="mdi mdi-help text-xs group-hover:scale-110 transition-transform"></i>
                                </button>
                            </label>
                            <div class="input-group">
                                <input type="number" name="commission_official_percent" value="{{ $settings['commission_official_percent'] ?? '4.0' }}" step="0.1" min="0" class="form-control-input form-control-custom bg-purple-50 dark:bg-purple-500/10 border border-purple-200 dark:border-purple-500/30 text-purple-800 dark:text-purple-400 shadow-inner dark:shadow-none text-center text-lg">
                                <span class="input-group-text bg-purple-500 border border-purple-500 border-l-0 text-white shadow-md shadow-purple-500/20">%</span>
                            </div>
                            <p class="text-[10px] font-bold text-slate-400 mt-2 mb-0 ml-1 text-center">Distributor / Pabrik B2B.</p>
                        </div>
                    </div>

                    <div class="mt-6 pt-5 border-t border-slate-100 dark:border-slate-800">
                        <label class="block text-[11px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-2 ml-1">Biaya Tetap per Transaksi (Opsional)</label>
                        <div class="input-group w-full md:w-64">
                            <span class="input-group-text bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 border-r-0 text-slate-500">Rp</span>
                            <input type="number" name="seller_fixed_fee" value="{{ $settings['seller_fixed_fee'] ?? '0' }}" class="form-control-input form-control-custom bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-white shadow-inner dark:shadow-none">
                        </div>
                        <p class="text-[10px] font-bold text-slate-400 mt-2 mb-0 ml-1">Dipotong flat dari pendapatan semua kasta toko di luar komisi persentase di atas.</p>
                    </div>
                </div>

                {{-- SEGMEN: BIAYA PELANGGAN --}}
                <div class="bg-white dark:bg-slate-900 border-t-4 border-t-amber-500 border-x border-b border-slate-200 dark:border-slate-800 rounded-3xl p-6 lg:p-8 shadow-sm transition-colors duration-300 mb-8">
                    <div class="border-b border-slate-100 dark:border-slate-800 pb-5 mb-6">
                        <h3 class="text-xl font-black text-slate-800 dark:text-white m-0 flex items-center gap-2"><i class="mdi mdi-credit-card-outline text-amber-500"></i> Biaya Penanganan Pembeli</h3>
                        <p class="text-xs font-bold text-slate-500 dark:text-slate-400 mt-1 mb-0">Biaya ekstra yang dibebankan ke pembeli untuk menutup biaya layanan Payment Gateway.</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-[11px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-2 ml-1">Biaya Handling QRIS (%)</label>
                            <div class="input-group">
                                <input type="number" name="fee_qris_percent" value="{{ $settings['fee_qris_percent'] ?? '1.5' }}" step="0.1" min="0.8" class="form-control-input form-control-custom bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-white shadow-inner dark:shadow-none">
                                <span class="input-group-text bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 border-l-0 text-slate-500">%</span>
                            </div>
                            <div class="flex items-center gap-1 text-[10px] font-black text-amber-600 dark:text-amber-500 mt-2 ml-1"><i class="mdi mdi-alert-circle"></i> Wajib di atas 0.7% (Potongan asli Midtrans).</div>
                        </div>

                        <div>
                            <label class="block text-[11px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-2 ml-1">Biaya Handling Virtual Account</label>
                            <div class="input-group">
                                <span class="input-group-text bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 border-r-0 text-slate-500">Rp</span>
                                <input type="number" name="fee_va_flat" value="{{ $settings['fee_va_flat'] ?? '5000' }}" min="4500" class="form-control-input form-control-custom bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-white shadow-inner dark:shadow-none">
                            </div>
                            <div class="flex items-center gap-1 text-[10px] font-black text-amber-600 dark:text-amber-500 mt-2 ml-1"><i class="mdi mdi-alert-circle"></i> Wajib di atas Rp 4.000.</div>
                        </div>

                        <div class="md:col-span-2 pt-4 mt-2 border-t border-slate-100 dark:border-slate-800">
                            <label class="block text-[11px] font-black text-emerald-600 dark:text-emerald-500 uppercase tracking-widest mb-2 ml-1">Biaya Layanan Jasa (Keuntungan Bersih Platform)</label>
                            <div class="input-group w-full md:w-80">
                                <span class="input-group-text bg-emerald-500 border border-emerald-500 border-r-0 text-white shadow-md shadow-emerald-500/20">Rp</span>
                                <input type="number" name="customer_service_fee" value="{{ $settings['customer_service_fee'] ?? '1000' }}" class="form-control-input form-control-custom bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/30 text-emerald-800 dark:text-emerald-400 shadow-inner dark:shadow-none text-lg">
                            </div>
                            <p class="text-[10px] font-bold text-slate-400 mt-2 mb-0 ml-1">Biaya flat tambahan di setiap checkout sebagai untung bersih Anda.</p>
                        </div>
                    </div>
                </div>

                {{-- SEGMEN: B2B DOWNPAYMENT --}}
                <div class="bg-white dark:bg-slate-900 border-t-4 border-t-emerald-500 border-x border-b border-slate-200 dark:border-slate-800 rounded-3xl p-6 lg:p-8 shadow-sm transition-colors duration-300">
                    <div class="border-b border-slate-100 dark:border-slate-800 pb-5 mb-6">
                        <h3 class="text-xl font-black text-slate-800 dark:text-white m-0 flex items-center gap-2"><i class="mdi mdi-handshake text-emerald-500"></i> Sistem B2B & Uang Muka (DP)</h3>
                        <p class="text-xs font-bold text-slate-500 dark:text-slate-400 mt-1 mb-0">Atur syarat pembelian proyek skala besar dengan sistem cicilan awal (DP).</p>
                    </div>

                    <div class="flex justify-between items-center p-5 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700/50 rounded-2xl mb-6">
                        <div class="pr-4">
                            <strong class="block text-sm font-black text-slate-800 dark:text-white mb-1">Aktifkan Pembayaran DP</strong>
                            <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400 leading-tight">Jika aktif, pembeli partai besar dapat checkout dengan DP.</span>
                        </div>
                        <div>
                            <input type="checkbox" class="toggle-checkbox" id="dpToggle" name="enable_dp_system" value="1" {{ ($settings['enable_dp_system'] ?? '0') == '1' ? 'checked' : '' }}>
                            <label for="dpToggle" class="toggle-label m-0"></label>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-[11px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-2 ml-1">Minimal Belanja (Opsi DP Muncul)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 border-r-0 text-slate-500">Rp</span>
                                <input type="number" name="min_nominal_dp" value="{{ $settings['min_nominal_dp'] ?? '10000000' }}" class="form-control-input form-control-custom bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-white shadow-inner dark:shadow-none">
                            </div>
                            <p class="text-[10px] font-bold text-slate-400 mt-2 mb-0 ml-1">Minimal total belanja.</p>
                        </div>
                        <div>
                            <label class="block text-[11px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-2 ml-1">Persentase Wajib DP</label>
                            <div class="input-group">
                                <input type="number" name="dp_percent" value="{{ $settings['dp_percent'] ?? '50' }}" max="99" class="form-control-input form-control-custom bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-white shadow-inner dark:shadow-none text-center text-lg">
                                <span class="input-group-text bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 border-l-0 text-slate-500">%</span>
                            </div>
                            <p class="text-[10px] font-bold text-slate-400 mt-2 mb-0 ml-1 text-center">Persentase dari total belanja.</p>
                        </div>
                        <div>
                            <label class="block text-[11px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-2 ml-1">Batas Waktu Pembayaran</label>
                            <div class="input-group">
                                <input type="number" name="dp_expired_minutes" value="{{ $settings['dp_expired_minutes'] ?? '1440' }}" class="form-control-input form-control-custom bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-white shadow-inner dark:shadow-none text-center text-lg">
                                <span class="input-group-text bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 border-l-0 text-slate-500">Menit</span>
                            </div>
                            <p class="text-[10px] font-bold text-slate-400 mt-2 mb-0 ml-1 text-center">Contoh: 1440 = 24 Jam.</p>
                        </div>
                    </div>
                </div>

            </div>

            {{-- 4. PANEL: API & INTEGRASI --}}
            <div class="tab-pane fade" id="panel-api" role="tabpanel">

                {{-- MIDTRANS --}}
                <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-[2rem] p-6 lg:p-8 shadow-sm transition-colors duration-300 mb-8">
                    <div class="border-b border-slate-100 dark:border-slate-800 pb-5 mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <div>
                            <h3 class="text-xl font-black text-slate-800 dark:text-white m-0 flex items-center gap-2">
                                <img src="https://midtrans.com/assets/img/midtrans-logo-black.svg" class="h-6 dark:invert dark:brightness-200" alt="Midtrans"> Payment Gateway
                            </h3>
                            <p class="text-xs font-bold text-slate-500 dark:text-slate-400 mt-2 mb-0">Konfigurasi kunci akses (Keys) agar sistem dapat menerima pembayaran otomatis.</p>
                        </div>

                        <div class="flex items-center gap-3 bg-slate-50 dark:bg-slate-800/50 px-4 py-2 border border-slate-200 dark:border-slate-700 rounded-xl">
                            <span class="text-xs font-black uppercase tracking-widest text-slate-700 dark:text-slate-300">Live Mode</span>
                            <div>
                                <input type="checkbox" class="toggle-checkbox" id="midtransToggle" name="midtrans_is_production" value="1" {{ ($settings['midtrans_is_production'] ?? '0') == '1' ? 'checked' : '' }}>
                                <label for="midtransToggle" class="toggle-label m-0"></label>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-5">
                        <div>
                            <label class="block text-[11px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-2 ml-1">Client Key</label>
                            <input type="text" name="midtrans_client_key" value="{{ $settings['midtrans_client_key'] ?? '' }}" class="form-control-custom p-3 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-white shadow-inner dark:shadow-none font-mono text-sm">
                        </div>
                        <div>
                            <label class="block text-[11px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-2 ml-1">Server Key <span class="text-rose-500 ml-1">*RAHASIA*</span></label>
                            <input type="password" name="midtrans_server_key" value="{{ $settings['midtrans_server_key'] ?? '' }}" class="form-control-custom p-3 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-white shadow-inner dark:shadow-none font-mono text-sm tracking-[0.2em]" placeholder="Midtrans-server-xxxxxxxxxxxx">
                        </div>
                    </div>
                </div>

                {{-- BITESHIP API --}}
                <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-[2rem] p-6 lg:p-8 shadow-sm transition-colors duration-300">
                    <div class="border-b border-slate-100 dark:border-slate-800 pb-5 mb-6">
                        <h3 class="text-xl font-black text-slate-800 dark:text-white m-0 flex items-center gap-2">
                            <i class="mdi mdi-api text-blue-500"></i> Biteship API
                        </h3>
                        <p class="text-xs font-bold text-slate-500 dark:text-slate-400 mt-1 mb-0">Hubungkan logistik dan sistem lokasi untuk perhitungan tarif ongkir otomatis ke seluruh Indonesia.</p>
                    </div>

                    <div>
                        <label class="block text-[11px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-2 ml-1">API Key Rahasia</label>
                        <input type="password" name="biteship_api_key" value="{{ $settings['biteship_api_key'] ?? '' }}" class="form-control-custom p-3 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-white shadow-inner dark:shadow-none font-mono text-sm tracking-[0.2em]" placeholder="biteship_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxx">
                        <p class="text-[10px] font-bold text-slate-400 mt-2 mb-0 ml-1">Dapatkan API Key di <a href="https://biteship.com/" target="_blank" class="text-blue-500 hover:underline">Dashboard Biteship</a> Anda.</p>
                    </div>
                </div>

            </div>

            {{-- 5. PANEL: ATURAN KATALOG --}}
            <div class="tab-pane fade" id="panel-catalog" role="tabpanel">
                <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-[2rem] p-6 lg:p-8 shadow-sm transition-colors duration-300">
                    <div class="border-b border-slate-100 dark:border-slate-800 pb-5 mb-6">
                        <h3 class="text-xl font-black text-slate-800 dark:text-white m-0 flex items-center gap-2"><i class="mdi mdi-security text-emerald-500"></i> Aturan Toko & Moderasi</h3>
                        <p class="text-xs font-bold text-slate-500 dark:text-slate-400 mt-1 mb-0">Tetapkan tingkat keketatan filter platform. Apakah seller dapat langsung berjualan atau harus menunggu izin Anda.</p>
                    </div>

                    <div class="flex justify-between items-center p-5 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700/50 rounded-2xl mb-4 transition-colors duration-300">
                        <div class="pr-4">
                            <strong class="block text-sm font-black text-slate-800 dark:text-white mb-1">Auto-Approve Material Baru</strong>
                            <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400 leading-tight">Jika aktif, produk yang diunggah seller langsung tayang ke publik tanpa perlu moderasi Admin terlebih dahulu.</span>
                        </div>
                        <div>
                            <input type="checkbox" class="toggle-checkbox" id="autoApproveProd" name="auto_approve_products" value="1" {{ ($settings['auto_approve_products'] ?? '0') == '1' ? 'checked' : '' }}>
                            <label for="autoApproveProd" class="toggle-label m-0"></label>
                        </div>
                    </div>

                    <div class="flex justify-between items-center p-5 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700/50 rounded-2xl transition-colors duration-300">
                        <div class="pr-4">
                            <strong class="block text-sm font-black text-slate-800 dark:text-white mb-1">Auto-Approve Pendaftaran Toko</strong>
                            <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400 leading-tight">Izinkan user yang baru mendaftar langsung mendapatkan akses dashboard penjual tanpa verifikasi Manual.</span>
                        </div>
                        <div>
                            <input type="checkbox" class="toggle-checkbox" id="autoApproveStore" name="auto_approve_stores" value="1" {{ ($settings['auto_approve_stores'] ?? '0') == '1' ? 'checked' : '' }}>
                            <label for="autoApproveStore" class="toggle-label m-0"></label>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
</form>

{{-- STICKY BOTTOM ACTION BAR --}}
<div class="save-bar bg-white/90 dark:bg-slate-900/90 backdrop-blur-md border-t border-slate-200 dark:border-slate-800 p-4 lg:p-5 flex justify-end shadow-[0_-10px_40px_-15px_rgba(0,0,0,0.1)] dark:shadow-[0_-10px_40px_-15px_rgba(0,0,0,0.5)] transition-colors duration-300">
    <button type="submit" form="mainSettingsForm" class="flex items-center gap-2 px-8 py-3.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-black rounded-xl shadow-lg shadow-blue-600/30 hover:-translate-y-1 transition-all outline-none w-full sm:w-auto justify-center">
        <i class="mdi mdi-content-save-check-outline text-xl leading-none"></i> SIMPAN SEMUA PERUBAHAN
    </button>
</div>

{{-- MODAL INFO KLIK --}}
<div class="modal fade" id="infoTierModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-[2rem] border-0 shadow-2xl overflow-hidden transition-colors duration-300">
            <div class="modal-header border-b border-slate-100 dark:border-slate-800 p-6 bg-white dark:bg-slate-900">
                <h5 class="text-lg font-black text-blue-600 dark:text-blue-400 m-0" id="infoModalTitle">
                    <i class="mdi mdi-information-outline"></i> Judul
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-6 bg-slate-50/50 dark:bg-slate-900 text-sm font-bold text-slate-700 dark:text-slate-300 leading-relaxed" id="infoModalDesc">
                Deskripsi
            </div>
            <div class="modal-footer border-t border-slate-100 dark:border-slate-800 p-6 bg-white dark:bg-slate-900">
                <button type="button" class="w-full px-5 py-3 rounded-xl font-black text-sm text-white bg-blue-600 hover:bg-blue-700 shadow-md shadow-blue-600/20 transition-all outline-none" data-bs-dismiss="modal">SAYA PAHAM</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // JS Logic for Image Preview with Foolproof Null checking
    function previewImage(input, previewElementId, placeholderId) {
        const previewEl = document.getElementById(previewElementId);
        const placeholderEl = document.getElementById(placeholderId);

        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                if(previewEl) {
                    previewEl.src = e.target.result;
                    previewEl.style.display = 'block';
                }
                if(placeholderEl) {
                    placeholderEl.style.display = 'none';
                }
            }
            reader.readAsDataURL(input.files[0]);
        } else {
            if(previewEl) {
                previewEl.src = '';
                previewEl.style.display = 'none';
            }
            if(placeholderEl) {
                placeholderEl.style.display = 'block';
            }
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.modal').forEach(modal => {
            document.body.appendChild(modal);
        });

        const modalTitle = document.getElementById('infoModalTitle');
        const modalDesc = document.getElementById('infoModalDesc');

        document.querySelectorAll('.btn-show-info').forEach(btn => {
            btn.addEventListener('click', function() {
                const title = this.getAttribute('data-title');
                const desc = this.getAttribute('data-desc');

                modalTitle.innerHTML = `<i class="mdi mdi-lightbulb-on-outline me-1"></i> Strategi ${title}`;
                modalDesc.innerHTML = desc;

                new bootstrap.Modal(document.getElementById('infoTierModal')).show();
            });
        });

        const tabLinks = document.querySelectorAll('[data-bs-toggle="pill"]');
        tabLinks.forEach(link => {
            link.addEventListener('shown.bs.tab', function(e) {
                tabLinks.forEach(l => {
                    l.classList.remove('bg-blue-50', 'dark:bg-blue-500/10', 'text-blue-600', 'dark:text-blue-400', 'shadow-inner', 'dark:shadow-[inset_0_1px_0_rgba(255,255,255,0.05)]');
                    l.classList.add('text-slate-500', 'dark:text-slate-400');
                });

                e.target.classList.add('bg-blue-50', 'dark:bg-blue-500/10', 'text-blue-600', 'dark:text-blue-400', 'shadow-inner', 'dark:shadow-[inset_0_1px_0_rgba(255,255,255,0.05)]');
                e.target.classList.remove('text-slate-500', 'dark:text-slate-400');
            });
        });
    });
</script>
@endpush