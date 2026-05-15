@extends('layouts.seller')

@section('title', 'Profil & Legalitas Toko')

@push('styles')
{{-- FONT AWESOME STABIL --}}
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
{{-- FONT AWESOME UNTUK MATCH DENGAN REFERENSI --}}
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
    /* HIDE SCROLLBAR BUT KEEP FUNCTIONALITY */
    .hide-scrollbar::-webkit-scrollbar { display: none; }
    .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

    /* CUSTOM TAILWIND UTILITIES */
    .shadow-soft { box-shadow: 0 4px 40px -4px rgba(0,0,0,0.03); }
    .shadow-float { box-shadow: 0 10px 30px -5px rgba(0,0,0,0.08); }
    .shadow-glow { box-shadow: 0 0 20px rgba(37,99,235,0.3); }

    .upload-overlay { transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1); }
    .group:hover .upload-overlay { opacity: 1; }

    .dropzone-active {
        border-color: #3b82f6 !important;
        background-color: #eff6ff !important;
        transform: scale(1.02);
    }

    .input-premium { transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1); }

    /* READONLY INPUT STYLE */
    .input-readonly {
        background-color: #f4f4f5 !important;
        color: #71717a !important;
        cursor: not-allowed;
        border-color: #e4e4e7 !important;
    }

    /* ANIMASI GPS BUTTON */
    @keyframes pulse-ring {
        0% { transform: scale(0.8); box-shadow: 0 0 0 0 rgba(37, 99, 235, 0.5); }
        70% { transform: scale(1); box-shadow: 0 0 0 10px rgba(37, 99, 235, 0); }
        100% { transform: scale(0.8); box-shadow: 0 0 0 0 rgba(37, 99, 235, 0); }
    }
    .gps-active {
        animation: pulse-ring 2s infinite;
        background-color: #1d4ed8 !important;
    }

    /* Scrollbar untuk dropdown & textarea */
    .custom-scrollbar::-webkit-scrollbar { width: 6px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }

    /* Hilangkan panah di input number */
    input[type=number]::-webkit-inner-spin-button,
    input[type=number]::-webkit-outer-spin-button { -webkit-appearance: none; margin: 0; }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-[#f4f4f5] p-4 md:p-6 lg:p-8 font-sans text-zinc-800 pb-36">

    {{-- SWEETALERT SETUP --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        const Toast = Swal.mixin({
            toast: true, position: 'top-end', showConfirmButton: false, timer: 4000,
            timerProgressBar: true, customClass: { popup: 'rounded-2xl shadow-float border border-zinc-100 font-sans' }
        });

        document.addEventListener('DOMContentLoaded', () => {
            @if(session('success'))
                Toast.fire({icon: 'success', title: '{{ session("success") }}'});
            @endif

            @if($errors->any())
                let errorList = "<ul class='text-left text-xs mt-2 space-y-1 list-disc pl-4 text-red-600 font-medium'>";
                @foreach($errors->all() as $error)
                    errorList += "<li>{{ $error }}</li>";
                @endforeach
                errorList += "</ul>";

                Swal.fire({
                    icon: 'error',
                    title: 'Validasi Gagal!',
                    html: '<p class="text-sm text-zinc-600">Mohon lengkapi data berikut:</p>' + errorList,
                    customClass: { popup: 'rounded-3xl shadow-float border border-zinc-100 font-sans' },
                    confirmButtonColor: '#3b82f6',
                    confirmButtonText: 'Baik, Saya Perbaiki'
                });
            @endif
        });
    </script>

    {{-- HEADER --}}
    <div class="mb-8 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h1 class="text-3xl font-black text-black tracking-tight">Profil & Legalitas Toko</h1>
            <p class="text-sm font-medium text-zinc-500 mt-1">Kelola informasi bisnis, dokumen legal, dan titik koordinat logistik armada Anda.</p>
        </div>
        @if(($toko->tier_toko ?? '') == 'official_store')
            <div class="bg-blue-50 text-blue-700 px-4 py-2 rounded-xl border border-blue-200 flex items-center gap-2">
                <i class="fas fa-check-circle text-lg"></i>
                <span class="text-xs font-black uppercase tracking-wider">Official Store Verified</span>
            </div>
        @endif
    </div>

    <form action="{{ route('seller.shop.profile.update') }}" method="POST" enctype="multipart/form-data" id="profileForm">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

            {{-- KOLOM KIRI: VISUAL & DATA LEGAL --}}
            <div class="lg:col-span-4 space-y-8 relative z-20">

                {{-- VISUAL BRANDING --}}
                <div class="bg-white rounded-[2rem] p-7 shadow-soft border border-zinc-200">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600">
                            <i class="fas fa-palette text-lg"></i>
                        </div>
                        <h3 class="text-base font-bold text-zinc-900">Visual Branding</h3>
                    </div>

                    {{-- Logo --}}
                    <div class="relative w-32 h-32 mx-auto rounded-full shadow-float p-1.5 bg-white mb-8 group cursor-pointer" onclick="document.getElementById('logoInput').click()">
                        @php $logoUrl = !empty($toko->logo_toko) ? asset('assets/uploads/logos/'.$toko->logo_toko) : 'https://ui-avatars.com/api/?name='.urlencode($toko->nama_toko ?? 'Toko').'&background=f4f4f5&color=52525b&size=300'; @endphp
                        <img id="logoPreview" src="{{ $logoUrl }}" class="w-full h-full rounded-full object-cover border border-zinc-100">
                        <div class="absolute inset-1.5 bg-black/50 rounded-full flex flex-col items-center justify-center text-white opacity-0 group-hover:opacity-100 transition-opacity backdrop-blur-sm">
                            <i class="fas fa-camera text-xl mb-1"></i>
                            <span class="text-[9px] font-bold tracking-widest uppercase">Ubah Logo</span>
                        </div>
                    </div>
                    <input type="file" id="logoInput" name="logo_toko" class="hidden" accept="image/jpeg,image/png,image/jpg" onchange="previewImage(this, 'logoPreview')">

                    {{-- Banner --}}
                    <div>
                        <div class="relative w-full h-32 rounded-2xl border-2 border-dashed border-zinc-200 overflow-hidden group cursor-pointer flex items-center justify-center bg-zinc-50 hover:bg-blue-50 hover:border-blue-300 transition-colors" onclick="document.getElementById('bannerInput').click()">
                            @if(!empty($toko->banner_toko))
                                <img id="bannerPreview" src="{{ asset('assets/uploads/banners/'.$toko->banner_toko) }}" class="absolute inset-0 w-full h-full object-cover">
                            @else
                                <img id="bannerPreview" src="" class="absolute inset-0 w-full h-full object-cover hidden">
                                <div id="bannerPlaceholder" class="text-center text-zinc-400 group-hover:text-blue-500 transition-colors">
                                    <i class="fas fa-image text-3xl mb-2"></i>
                                    <div class="text-xs font-bold">Upload Banner</div>
                                    <div class="text-[10px] font-medium mt-1 opacity-70">1200x300px (Max 2MB)</div>
                                </div>
                            @endif
                            <div class="absolute inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center text-white opacity-0 group-hover:opacity-100 transition-opacity">
                                <i class="fas fa-pen text-xl"></i>
                            </div>
                        </div>
                        <input type="file" id="bannerInput" name="banner_toko" class="hidden" accept="image/jpeg,image/png,image/jpg" onchange="previewImage(this, 'bannerPreview', 'bannerPlaceholder')">
                    </div>
                </div>

                {{-- DOKUMEN LEGALITAS (DRAG & DROP) --}}
                <div class="bg-white rounded-[2rem] p-7 shadow-soft border border-zinc-200">
                    <div class="mb-6">
                        <div class="flex items-center gap-3 mb-2">
                            <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600">
                                <i class="fas fa-file-contract text-lg"></i>
                            </div>
                            <h3 class="text-base font-bold text-zinc-900">Dokumen Bisnis</h3>
                        </div>
                        <p class="text-xs font-medium text-zinc-500 leading-relaxed">Syarat wajib untuk verifikasi lencana <span class="font-bold text-blue-600">Official Store</span>.</p>
                    </div>

                    <div class="space-y-5">
                        {{-- NIB --}}
                        <div>
                            <label class="block text-[11px] font-black text-zinc-400 uppercase tracking-widest mb-2">Dokumen NIB *</label>
                            <div id="dropzoneNIB" class="border-2 border-dashed border-zinc-200 rounded-2xl p-5 text-center cursor-pointer hover:border-blue-400 hover:bg-blue-50 transition-all duration-300 relative overflow-hidden" onclick="document.getElementById('nibInput').click()">
                                <input type="file" id="nibInput" name="dokumen_nib" class="hidden absolute inset-0" accept="application/pdf,image/jpeg,image/png,image/jpg" onchange="updateFileName(this, 'nibName', 'dropzoneNIB')">
                                <div class="relative z-10 flex flex-col items-center justify-center pointer-events-none">
                                    <i class="fas fa-cloud-upload-alt text-2xl text-zinc-300 mb-2 drop-icon"></i>
                                    <div id="nibName" class="text-xs font-bold text-zinc-600">
                                        @if(!empty($toko->dokumen_nib))
                                            <span class="text-emerald-600"><i class="fas fa-check-circle"></i> {{ $toko->dokumen_nib }}</span>
                                        @else
                                            Drag & drop atau klik disini
                                        @endif
                                    </div>
                                    <div class="text-[9px] text-zinc-400 mt-1 uppercase tracking-wider">PDF / JPG Max 5MB</div>
                                </div>
                            </div>
                        </div>

                        {{-- NPWP --}}
                        <div>
                            <label class="block text-[11px] font-black text-zinc-400 uppercase tracking-widest mb-2">Dokumen NPWP *</label>
                            <div id="dropzoneNPWP" class="border-2 border-dashed border-zinc-200 rounded-2xl p-5 text-center cursor-pointer hover:border-blue-400 hover:bg-blue-50 transition-all duration-300 relative overflow-hidden" onclick="document.getElementById('npwpInput').click()">
                                <input type="file" id="npwpInput" name="dokumen_npwp" class="hidden absolute inset-0" accept="application/pdf,image/jpeg,image/png,image/jpg" onchange="updateFileName(this, 'npwpName', 'dropzoneNPWP')">
                                <div class="relative z-10 flex flex-col items-center justify-center pointer-events-none">
                                    <i class="fas fa-cloud-upload-alt text-2xl text-zinc-300 mb-2 drop-icon"></i>
                                    <div id="npwpName" class="text-xs font-bold text-zinc-600">
                                        @if(!empty($toko->dokumen_npwp))
                                            <span class="text-emerald-600"><i class="fas fa-check-circle"></i> {{ $toko->dokumen_npwp }}</span>
                                        @else
                                            Drag & drop atau klik disini
                                        @endif
                                    </div>
                                    <div class="text-[9px] text-zinc-400 mt-1 uppercase tracking-wider">PDF / JPG Max 5MB</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- KOLOM KANAN: IDENTITAS & MAPS --}}
            <div class="lg:col-span-8 space-y-8 relative z-10">

                {{-- INFORMASI DASAR --}}
                <div class="bg-white rounded-[2rem] shadow-soft border border-zinc-200 p-6 sm:p-8">
                    <div class="flex items-center gap-3 mb-6 pb-4 border-b border-zinc-100">
                        <i class="fas fa-store text-blue-600 text-lg"></i>
                        <h3 class="text-lg font-black text-black">Informasi Identitas</h3>
                    </div>

                    <div class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-[11px] font-black text-zinc-400 uppercase tracking-widest mb-2">Nama Toko Resmi <span class="text-red-500">*</span></label>
                                <input type="text" name="nama_toko" class="input-premium w-full bg-zinc-50 border border-zinc-200 text-zinc-900 text-sm font-bold rounded-xl px-4 py-3.5 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none" value="{{ old('nama_toko', $toko->nama_toko ?? '') }}" required>
                            </div>
                            <div>
                                <label class="block text-[11px] font-black text-zinc-400 uppercase tracking-widest mb-2">WhatsApp Admin <span class="text-red-500">*</span></label>
                                <div class="flex border border-zinc-200 rounded-xl overflow-hidden focus-within:ring-2 focus-within:ring-blue-500/20 focus-within:border-blue-500 focus-within:bg-white bg-zinc-50 transition-all">
                                    <span class="bg-zinc-100 px-4 py-3.5 text-zinc-500 font-bold border-r border-zinc-200 flex items-center">+62</span>
                                    <input type="number" name="no_telepon" class="input-premium w-full px-4 py-3.5 text-sm font-bold outline-none bg-transparent" value="{{ old('no_telepon', ltrim($toko->telepon_toko ?? '', '0')) }}" placeholder="8123456xxxx" required>
                                </div>
                            </div>
                        </div>
                        <div>
                            <label class="block text-[11px] font-black text-zinc-400 uppercase tracking-widest mb-2">Slogan / Tagline Bisnis</label>
                            <input type="text" name="slogan" class="input-premium w-full bg-zinc-50 border border-zinc-200 text-zinc-900 text-sm font-bold rounded-xl px-4 py-3.5 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none" value="{{ old('slogan', $toko->slogan ?? '') }}" placeholder="Cth: Solusi Material Konstruksi Terbaik & Terpercaya">
                        </div>
                        <div>
                            <label class="block text-[11px] font-black text-zinc-400 uppercase tracking-widest mb-2">Deskripsi Lengkap Toko</label>
                            <textarea name="deskripsi_toko" class="input-premium w-full bg-zinc-50 border border-zinc-200 text-zinc-900 text-sm leading-relaxed font-medium rounded-xl px-4 py-4 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none min-h-[120px] resize-none custom-scrollbar" placeholder="Ceritakan sejarah, keunggulan, dan detail layanan armada pengiriman Anda...">{{ old('deskripsi_toko', $toko->deskripsi_toko ?? '') }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- LOKASI & PETA --}}
                <div class="bg-white rounded-[2rem] shadow-soft border border-zinc-200 p-6 sm:p-8">

                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600">
                                <i class="fas fa-map-marker-alt text-lg"></i>
                            </div>
                            <h3 class="text-lg font-black text-black">Alamat & Titik Koordinat</h3>
                        </div>
                    </div>

                    <p class="text-sm font-medium text-zinc-500 mb-6">Tentukan lokasi gudang/proyek Anda untuk akurasi pengiriman material (Armada Truk).</p>

                    <div class="space-y-6">

                        {{-- TOMBOL BUKA PETA MODAL --}}
                        <div class="bg-blue-50/50 border border-blue-100 p-5 rounded-2xl flex flex-col md:flex-row items-center gap-4">
                            <div class="w-full md:w-auto shrink-0">
                                <button type="button" onclick="openMapModal()" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-6 py-3.5 rounded-xl text-sm font-bold transition-all shadow-glow flex items-center justify-center gap-2">
                                    <i class="fas fa-map-pin"></i> Atur Peta & Alamat
                                </button>
                            </div>
                            <div class="w-full flex-1 flex gap-3">
                                <div class="flex-1 relative">
                                    <label class="absolute -top-2 left-3 bg-blue-50 px-1 text-[9px] font-black text-blue-600 uppercase tracking-widest">Latitude</label>
                                    <input type="text" name="latitude" id="mainLat" class="input-readonly w-full text-xs font-bold rounded-xl px-4 py-3 outline-none" value="{{ old('latitude', $toko->latitude ?? '-6.558935') }}" readonly required>
                                </div>
                                <div class="flex-1 relative">
                                    <label class="absolute -top-2 left-3 bg-blue-50 px-1 text-[9px] font-black text-blue-600 uppercase tracking-widest">Longitude</label>
                                    <input type="text" name="longitude" id="mainLng" class="input-readonly w-full text-xs font-bold rounded-xl px-4 py-3 outline-none" value="{{ old('longitude', $toko->longitude ?? '107.763321') }}" readonly required>
                                </div>
                            </div>
                        </div>

                        {{-- TAMPILAN READONLY HASIL DARI MODAL --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-[11px] font-black text-zinc-400 uppercase tracking-widest mb-2">Kabupaten / Kota</label>
                                <div class="relative">
                                    <input type="text" name="kota" id="inputKota" class="w-full input-readonly text-sm font-bold rounded-xl px-4 py-3 outline-none" value="{{ old('kota', $toko->kota ?? '') }}" readonly placeholder="Diatur melalui Peta">
                                    <i class="fas fa-lock absolute right-4 top-3.5 text-zinc-400 text-sm"></i>
                                </div>
                            </div>
                            <div>
                                <label class="block text-[11px] font-black text-zinc-400 uppercase tracking-widest mb-2">Kode Pos</label>
                                <div class="relative">
                                    <input type="text" name="kode_pos" id="inputKodePos" class="w-full input-readonly text-sm font-bold rounded-xl px-4 py-3 outline-none" value="{{ old('kode_pos', $toko->kode_pos ?? '') }}" readonly required placeholder="Diatur melalui Peta">
                                    <i class="fas fa-lock absolute right-4 top-3.5 text-zinc-400 text-sm"></i>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="block text-[11px] font-black text-zinc-400 uppercase tracking-widest mb-2">Alamat Detail (Detail Alamat Toko)</label>
                            <textarea id="mainAlamatDetail" name="alamat_toko" class="input-readonly custom-scrollbar w-full text-sm font-medium rounded-xl px-4 py-4 outline-none min-h-[90px] resize-none leading-relaxed" readonly required placeholder="Diatur melalui Modal Peta...">{{ old('alamat_toko', $toko->alamat_toko ?? '') }}</textarea>
                        </div>

                        <div>
                            <label class="block text-[11px] font-black text-zinc-400 uppercase tracking-widest mb-2">Wilayah Ekspedisi</label>
                            <div class="relative">
                                <input type="text" id="mainBiteshipLabel" class="input-readonly w-full text-sm font-bold rounded-xl px-4 py-3.5 outline-none" readonly placeholder="Diatur melalui Modal Peta..." value="{{ old('area_id', $toko->area_id ?? '') != '' ? 'Wilayah Terpilih (ID: '.old('area_id', $toko->area_id ?? '').')' : '' }}">
                                <i class="fas fa-lock absolute right-4 top-4 text-zinc-400 text-sm"></i>
                                {{-- Hidden Input yang disubmit ke database --}}
                                <input type="hidden" name="area_id" id="mainAreaId" value="{{ old('area_id', $toko->area_id ?? '') }}" required>
                            </div>
                        </div>

                    </div>
                </div>

                {{-- KEBIJAKAN B2B --}}
                <div class="bg-white rounded-[2rem] shadow-soft border border-zinc-200 p-6 sm:p-8">
                    <div class="flex items-center gap-3 mb-6 pb-4 border-b border-zinc-100">
                        <i class="fas fa-shield-alt text-blue-600 text-lg"></i>
                        <h3 class="text-lg font-black text-black">Kebijakan B2B</h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-[11px] font-black text-zinc-400 uppercase tracking-widest mb-2">Syarat Pemesanan</label>
                            <textarea name="catatan_toko" class="input-premium w-full bg-zinc-50 border border-zinc-200 text-zinc-900 text-sm font-medium rounded-xl px-4 py-4 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none min-h-[120px] resize-none custom-scrollbar" placeholder="Cth: Minimal pemesanan armada truk adalah...">{{ old('catatan_toko', $toko->catatan_toko ?? '') }}</textarea>
                        </div>
                        <div>
                            <label class="block text-[11px] font-black text-zinc-400 uppercase tracking-widest mb-2">Kebijakan Retur</label>
                            <textarea name="kebijakan_retur" class="input-premium w-full bg-red-50/30 border border-red-100 text-zinc-900 text-sm font-medium rounded-xl px-4 py-4 focus:bg-white focus:ring-2 focus:ring-red-500/20 focus:border-red-300 outline-none min-h-[120px] resize-none custom-scrollbar" placeholder="Cth: Material yang rusak karena pembeli tidak dapat diretur...">{{ old('kebijakan_retur', $toko->kebijakan_retur ?? '') }}</textarea>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        {{-- STICKY SAVE BAR --}}
        <div class="fixed bottom-0 left-0 lg:left-[260px] right-0 bg-white/95 backdrop-blur-md border-t border-zinc-200 px-6 py-5 flex items-center justify-between z-50 shadow-[0_-10px_40px_-10px_rgba(0,0,0,0.05)]">
            <div class="hidden sm:flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-blue-50 flex items-center justify-center text-blue-600">
                    <i class="fas fa-check"></i>
                </div>
                <div>
                    <p class="text-sm font-bold text-zinc-900 m-0">Siap menyimpan perubahan?</p>
                    <p class="text-[11px] font-semibold text-zinc-500 uppercase tracking-wider">Pastikan koordinat peta dan wilayah ekspedisi akurat.</p>
                </div>
            </div>
            <button type="submit" id="btnSubmitProfile" class="w-full sm:w-auto px-8 py-3.5 bg-zinc-900 hover:bg-black text-white font-bold rounded-xl shadow-float transition-all flex items-center justify-center gap-2 transform hover:-translate-y-0.5 text-sm">
                <i class="fas fa-save"></i> SIMPAN PROFIL TOKO
            </button>
        </div>
    </form>
</div>

{{-- ========================================================================= --}}
{{-- MODAL POPUP PETA & ALAMAT --}}
{{-- ========================================================================= --}}
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
<style>
    /* Paksa map mengambil ruang yang cukup */
    #modalMap { width: 100% !important; height: 250px !important; display: block; z-index: 10; }
    
    /* Perbaikan mutlak untuk Tailwind Reset yang menghancurkan Leaflet */
    .leaflet-container { z-index: 10 !important; font-family: 'Inter', sans-serif; }
    .leaflet-container img { max-width: none !important; max-height: none !important; margin: 0 !important; padding: 0 !important; }
    .leaflet-control-container .leaflet-control { z-index: 40 !important; }
</style>

{{-- MODAL POPUP LEAFLET MAP --}}
<div id="mapModal" class="fixed inset-0 z-[9999] hidden flex items-center justify-center bg-black/60 backdrop-blur-sm px-4">
    <div class="bg-white rounded-3xl shadow-float w-full max-w-4xl max-h-[90vh] flex flex-col transform transition-all scale-95 opacity-0 overflow-hidden" id="mapModalContent">

        {{-- Header Modal (Fixed) --}}
        <div class="p-5 border-b border-zinc-100 flex justify-between items-center bg-zinc-50 shrink-0">
            <div>
                <h3 class="font-black text-lg text-zinc-900 flex items-center gap-2">
                    <i class="fas fa-map-marked-alt text-blue-600"></i> Atur Peta & Alamat Lengkap
                </h3>
                <p class="text-xs text-zinc-500 font-medium mt-1">Lengkapi titik lokasi dan detail alamat pengiriman Anda.</p>
            </div>
            <button type="button" onclick="closeMapModal()" class="w-8 h-8 rounded-full bg-zinc-200 hover:bg-red-100 hover:text-red-600 text-zinc-500 flex items-center justify-center transition-colors">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>

        {{-- Body Modal (Scrollable Tengah) --}}
        <div class="p-5 space-y-6 overflow-y-auto custom-scrollbar flex-1 relative pb-20">
            
            {{-- Search & Tools --}}
            <div class="flex flex-col sm:flex-row gap-3">
                <div class="relative flex-1 group">
                    <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-zinc-400 group-focus-within:text-blue-500"></i>
                    <input type="text" id="searchLokasi" class="w-full bg-white border border-zinc-300 text-zinc-900 text-sm font-bold rounded-xl pl-11 pr-4 py-3 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none" placeholder="Cari area di peta...">
                </div>
                <div class="flex gap-2">
                    <button type="button" onclick="cariLokasiMap()" id="btnSearchMap" class="bg-zinc-900 hover:bg-black text-white px-6 py-3 rounded-xl text-sm font-bold shadow-md transition-all whitespace-nowrap">
                        Cari
                    </button>
                    <button type="button" onclick="getLocation()" id="btnGps" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-3 rounded-xl text-sm font-bold shadow-glow transition-all shrink-0" title="Gunakan Lokasi Saya Saat Ini">
                        <i class="fas fa-crosshairs"></i>
                    </button>
                </div>
            </div>

            {{-- Map Container in Modal --}}
            <div class="relative w-full rounded-2xl border border-zinc-200 z-10 overflow-hidden bg-zinc-100">
                <div id="modalMap"></div>
                <div id="map-loading" class="absolute inset-0 bg-white/70 backdrop-blur-sm z-[500] flex flex-col items-center justify-center opacity-0 pointer-events-none transition-opacity">
                    <i class="fas fa-circle-notch fa-spin text-3xl text-blue-600 mb-3"></i>
                    <span class="text-xs font-bold text-zinc-600 tracking-wider uppercase" id="loading-text">Menyelaraskan Lokasi...</span>
                </div>
            </div>

            {{-- Result Display Geocoding --}}
            <div class="bg-blue-50/50 border border-blue-100 rounded-xl p-4 flex gap-4 items-center">
                <i class="fas fa-info-circle text-2xl text-blue-500 shrink-0"></i>
                <div class="flex-1">
                    <div class="text-[10px] font-black text-blue-600 uppercase tracking-widest mb-1">Hasil Geocoding Peta</div>
                    <div class="text-sm font-semibold text-zinc-800" id="tempAddressText">Menunggu lokasi dipilih...</div>
                </div>
            </div>

            {{-- ALAMAT MANUAL (DALAM MODAL) --}}
            <div>
                <label class="block text-[11px] font-black text-zinc-400 uppercase tracking-widest mb-2">Alamat Detail (Detail Alamat Toko) <span class="text-red-500">*</span></label>
                <textarea id="modalAlamatDetail" class="input-premium custom-scrollbar w-full bg-zinc-50 border border-zinc-200 text-zinc-900 text-sm font-medium rounded-xl px-4 py-4 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none min-h-[90px] resize-none leading-relaxed" placeholder="Contoh: Jl. Raya Pantura No. 45. Gudang atap seng biru, gerbang besi hitam."></textarea>
            </div>

            {{-- BITESHIP AUTOCOMPLETE SEARCH (DALAM MODAL) --}}
            <div class="border-t border-zinc-100 pt-6">
                <div class="flex items-center justify-between mb-2">
                    <label class="block text-[11px] font-black text-zinc-400 uppercase tracking-widest">Kecamatan Ekspedisi<span class="text-red-500">*</span></label>
                </div>
                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none" id="search-icon">
                        <i class="fas fa-search text-zinc-400 group-focus-within:text-blue-500"></i>
                    </div>
                    <input type="text" id="search_area" placeholder="Ketik manual nama Kecamatan atau Kelurahan lalu pilih dari dropdown..." class="input-premium w-full bg-zinc-50 border border-zinc-200 text-zinc-900 text-sm font-bold rounded-xl pl-11 pr-4 py-3.5 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none" autocomplete="off">
                    <input type="hidden" id="modalAreaId">
                    
                    {{-- FIX: Z-Index diperbesar agar tidak tertimpa --}}
                    <ul id="area_results" class="absolute z-[9999] w-full bg-white border border-zinc-200 rounded-xl shadow-lg mt-1 hidden max-h-60 overflow-y-auto custom-scrollbar"></ul>
                </div>
                <p class="text-[10px] text-zinc-500 mt-2 font-medium"><i class="fas fa-info-circle text-blue-500"></i> <b>Krusial:</b> Anda wajib mengetik dan memilih hasil dari dropdown agar sistem ongkos kirim toko (JNE, POS, dll) berfungsi.</p>
            </div>

        </div>

        {{-- Footer Modal (Fixed at bottom) --}}
        <div class="p-5 border-t border-zinc-100 bg-zinc-50 flex justify-end gap-3 shrink-0 relative z-20">
            <button type="button" onclick="closeMapModal()" class="px-6 py-2.5 bg-white border border-zinc-300 text-zinc-700 font-bold rounded-xl hover:bg-zinc-100 transition-all">Batal</button>
            
            {{-- FIX: Tombol Simpan sekarang aman dari tumpang tindih --}}
            <button type="button" onclick="saveMapLocation()" class="px-8 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-glow transition-all cursor-pointer">Simpan Konfigurasi</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
{{-- SCRIPT LEAFLET --}}
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {

        // ==========================================
        // 1. INIT VARIABLE PETA
        // ==========================================
        // Elemen Form Utama
        const mainLat = document.getElementById('mainLat');
        const mainLng = document.getElementById('mainLng');
        const inputKota = document.getElementById('inputKota');
        const inputKodePos = document.getElementById('inputKodePos');
        const mainAlamatDetail = document.getElementById('mainAlamatDetail');
        const mainAreaId = document.getElementById('mainAreaId');
        const mainBiteshipLabel = document.getElementById('mainBiteshipLabel');

        // Elemen Modal
        const mapModal = document.getElementById('mapModal');
        const mapModalContent = document.getElementById('mapModalContent');
        const tempAddressText = document.getElementById('tempAddressText');
        const searchInputMap = document.getElementById('searchLokasi');
        const modalAlamatDetail = document.getElementById('modalAlamatDetail');
        const searchInputBiteship = document.getElementById('search_area');
        const modalAreaId = document.getElementById('modalAreaId');

        let map = null;
        let marker = null;

        let tempLat = parseFloat(mainLat.value) || -6.558935;
        let tempLng = parseFloat(mainLng.value) || 107.763321;
        let tempCity = inputKota.value || '';
        let tempPos = inputKodePos.value || '';
        let tempFullAddress = ''; // Untuk nampung geocoding

        // ==========================================
        // 2. MANAJEMEN MODAL & INIT MAP
        // ==========================================
        window.openMapModal = function() {
            // Sinkronisasi data awal dari form utama ke dalam modal
            modalAlamatDetail.value = mainAlamatDetail.value;
            modalAreaId.value = mainAreaId.value;
            if (mainAreaId.value) {
                searchInputBiteship.value = mainBiteshipLabel.value || 'Wilayah Terpilih';
            }

            mapModal.classList.remove('hidden');

            setTimeout(() => {
                mapModalContent.classList.remove('scale-95', 'opacity-0');
                mapModalContent.classList.add('scale-100', 'opacity-100');

                setTimeout(() => {
                    if(!map) {
                        map = L.map('modalMap', {
                            center: [tempLat, tempLng],
                            zoom: 15,
                            zoomControl: false
                        });

                        L.control.zoom({ position: 'topright' }).addTo(map);
                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                            maxZoom: 19,
                            attribution: '© OpenStreetMap'
                        }).addTo(map);

                        const redIcon = L.icon({
                            iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
                            shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                            iconSize: [25, 41], iconAnchor: [12, 41], popupAnchor: [1, -34]
                        });

                        marker = L.marker([tempLat, tempLng], { draggable: true, icon: redIcon }).addTo(map);

                        marker.on('dragend', function (e) {
                            const p = e.target.getLatLng();
                            updateMapPosition(p.lat, p.lng);
                        });

                        map.on('click', function(e) {
                            updateMapPosition(e.latlng.lat, e.latlng.lng);
                        });
                    } else {
                        map.setView([tempLat, tempLng], 15);
                        marker.setLatLng([tempLat, tempLng]);
                    }

                    map.invalidateSize();
                    window.dispatchEvent(new Event('resize'));

                    getAddressFromCoords(tempLat, tempLng);

                }, 500);

            }, 50);
        };

        window.closeMapModal = function() {
            mapModalContent.classList.remove('scale-100', 'opacity-100');
            mapModalContent.classList.add('scale-95', 'opacity-0');
            setTimeout(() => { mapModal.classList.add('hidden'); }, 300);
        };

window.saveMapLocation = function() {
            // Validasi Input Modal
            if (!modalAlamatDetail.value.trim()) {
                Swal.fire({icon: 'warning', title: 'Data Belum Lengkap', text: 'Alamat Detail Manual tidak boleh kosong.', customClass: { popup: 'rounded-2xl' }});
                return;
            }
            if (!modalAreaId.value) {
                Swal.fire({icon: 'warning', title: 'Data Belum Lengkap', text: 'Kecamatan Ekspedisi Biteship wajib diketik dan dipilih dari daftar pencarian.', customClass: { popup: 'rounded-2xl' }});
                return;
            }

            // Pindahkan data Koordinat & Kota dari Modal ke Form Utama
            mainLat.value = tempLat.toFixed(8);
            mainLng.value = tempLng.toFixed(8);
            inputKota.value = tempCity;
            inputKodePos.value = tempPos;
            
            // Pindahkan data Alamat & Biteship ke Form Utama
            mainAlamatDetail.value = modalAlamatDetail.value;
            mainAreaId.value = modalAreaId.value;
            document.getElementById('mainBiteshipLabel').value = searchInputBiteship.value;

            // Tutup Modal dan Tampilkan Pesan Sukses
            closeMapModal();
            Toast.fire({icon: 'success', title: 'Konfigurasi Alamat Disimpan!'});
        };
        // ==========================================
        // 3. FUNGSI GEOCODING PETA (OPENSTREETMAP)
        // ==========================================
        const loadingOverlay = document.getElementById('map-loading');
        const loadingText = document.getElementById('loading-text');

        function updateMapPosition(lat, lng) {
            tempLat = lat;
            tempLng = lng;
            marker.setLatLng([lat, lng]);
            map.panTo([lat, lng]);
            getAddressFromCoords(lat, lng);
        }

        async function getAddressFromCoords(lat, lng) {
            loadingOverlay.style.opacity = '1';
            loadingText.innerText = "Membaca Peta...";
            try {
                const response = await fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&zoom=18&addressdetails=1`);
                const data = await response.json();
                if(data && data.address) {
                    const addr = data.address;
                    tempCity = addr.city || addr.town || addr.municipality || addr.county || addr.state_district || "Tidak Ditemukan";
                    tempPos = addr.postcode || "";
                    tempFullAddress = data.display_name || "";

                    tempAddressText.innerHTML = `<b>${tempCity}</b>, ${tempPos}<br><span class="text-[10px] text-zinc-500 font-medium">${tempFullAddress}</span>`;
                    
                    // Otomatis bantu isi text alamat kalau masih kosong
                    if(modalAlamatDetail.value.trim() === '') {
                        modalAlamatDetail.value = tempFullAddress;
                    }

                    // KITA MATIKAN AUTO-WRITE BITESHIP!
                    // Seller wajib cari secara manual.
                }
            } catch (error) {
                tempAddressText.innerHTML = "Gagal menghubungi satelit pencarian.";
            } finally {
                loadingOverlay.style.opacity = '0';
            }
        }

        window.cariLokasiMap = async function() {
            const query = searchInputMap.value;
            if(query.length < 3) return Toast.fire({icon: 'warning', title: 'Ketik minimal 3 karakter pencarian.'});

            const btn = document.getElementById('btnSearchMap');
            const originalText = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            btn.disabled = true;

            try {
                const response = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}`);
                const data = await response.json();

                if(data.length > 0) {
                    updateMapPosition(parseFloat(data[0].lat), parseFloat(data[0].lon));
                    map.setZoom(16);
                } else {
                    Toast.fire({icon: 'error', title: 'Lokasi peta tidak ditemukan.'});
                }
            } catch(e) {
                Toast.fire({icon: 'error', title: 'Koneksi error.'});
            } finally {
                btn.innerHTML = originalText;
                btn.disabled = false;
            }
        };

        searchInputMap.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') { e.preventDefault(); cariLokasiMap(); }
        });

        // Tombol GPS HP
        window.getLocation = function() {
            const btnGps = document.getElementById('btnGps');
            if (navigator.geolocation) {
                btnGps.classList.add('gps-active');
                loadingOverlay.style.opacity = '1';
                loadingText.innerText = "Mencari Sinyal GPS...";

                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        updateMapPosition(position.coords.latitude, position.coords.longitude);
                        map.setZoom(17);
                        btnGps.classList.remove('gps-active');
                    },
                    function(error) {
                        btnGps.classList.remove('gps-active');
                        loadingOverlay.style.opacity = '0';
                        Swal.fire({icon: 'error', text: 'Gagal mendeteksi lokasi. Pastikan izin lokasi (location) menyala di browser.'});
                    },
                    { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
                );
            } else {
                Swal.fire({icon: 'error', title: 'Browser Tidak Mendukung GPS'});
            }
        };

        // ==========================================
        // 4. BITESHIP AUTOCOMPLETE SEARCH (DI DALAM MODAL)
        // ==========================================
        const resultsList = document.getElementById('area_results');
        const searchIconBiteship = document.getElementById('search-icon');
        let debounceTimer;

        async function searchBiteshipAreas(keyword) {
            searchIconBiteship.innerHTML = '<i class="fas fa-spinner fa-spin text-blue-500"></i>';
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
                            searchInputBiteship.value = `${area.name}, ${area.administrative_division_level_2_name}`;
                            modalAreaId.value = area.id; 
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
                searchIconBiteship.innerHTML = '<i class="fas fa-search text-zinc-400 group-focus-within:text-blue-500"></i>';
            }
        }

        searchInputBiteship.addEventListener('input', function() {
            const keyword = this.value.trim();
            clearTimeout(debounceTimer);
            
            if (keyword.length < 3) {
                resultsList.classList.add('hidden');
                return;
            }
            debounceTimer = setTimeout(() => { searchBiteshipAreas(keyword); }, 800);
        });

        // Hide results if clicked outside
        document.addEventListener('click', function(e) {
            if (!searchInputBiteship.contains(e.target) && !resultsList.contains(e.target)) {
                resultsList.classList.add('hidden');
            }
        });

        // ==========================================
        // 5. PREVIEW GAMBAR & DRAG DROP
        // ==========================================
        function setupDropzone(dropzoneId, inputId, nameId) {
            const dropzone = document.getElementById(dropzoneId);
            const input = document.getElementById(inputId);
            const icon = dropzone.querySelector('.drop-icon');

            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                dropzone.addEventListener(eventName, e => { e.preventDefault(); e.stopPropagation(); }, false);
            });

            ['dragenter', 'dragover'].forEach(eventName => {
                dropzone.addEventListener(eventName, () => {
                    dropzone.classList.add('dropzone-active');
                    icon.classList.remove('text-zinc-300');
                    icon.classList.add('text-blue-500');
                }, false);
            });

            ['dragleave', 'drop'].forEach(eventName => {
                dropzone.addEventListener(eventName, () => {
                    dropzone.classList.remove('dropzone-active');
                    icon.classList.remove('text-blue-500');
                    icon.classList.add('text-zinc-300');
                }, false);
            });

            dropzone.addEventListener('drop', (e) => {
                input.files = e.dataTransfer.files;
                updateFileName(input, nameId, dropzoneId);
            });
        }

        setupDropzone('dropzoneNIB', 'nibInput', 'nibName');
        setupDropzone('dropzoneNPWP', 'npwpInput', 'npwpName');

        window.updateFileName = function(input, targetId, dropzoneId) {
            if(input.files && input.files.length > 0) {
                document.getElementById(targetId).innerHTML = `<span class="text-blue-600 flex items-center gap-1 justify-center"><i class="fas fa-check-circle"></i> ${input.files[0].name}</span>`;
                document.getElementById(dropzoneId).classList.add('border-blue-400', 'bg-blue-50');
            }
        }

        window.previewImage = function(input, previewId, placeholderId = null) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = e => {
                    document.getElementById(previewId).src = e.target.result;
                    document.getElementById(previewId).classList.remove('hidden');
                    if(placeholderId) document.getElementById(placeholderId).classList.add('hidden');
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        // ==========================================
        // 6. SUBMIT LOADER & VALIDASI FORM UTAMA
        // ==========================================
        document.getElementById('profileForm').addEventListener('submit', function(e) {
            const areaInput = document.getElementById('mainAreaId').value;
            if(!areaInput) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning', title: 'Data Lokasi Belum Lengkap', text: "Mohon klik 'Atur Peta & Alamat' untuk melengkapi Wilayah Ekspedisi.",
                    confirmButtonColor: '#3b82f6', customClass: { popup: 'rounded-[2rem]', confirmButton: 'rounded-xl px-8 py-3 font-bold' }
                });
                return;
            }

            const btn = document.getElementById('btnSubmitProfile');
            setTimeout(() => {
                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> MEMPROSES...';
                btn.classList.add('opacity-80', 'cursor-not-allowed', 'scale-95');
                btn.disabled = true;
            }, 10);
        });



        // ==========================================
        // 7. TRIK HACK: AUTO-LOAD KOTA DARI KOORDINAT
        // ==========================================
        async function autoLoadKota() {
            const lat = document.getElementById('mainLat').value;
            const lng = document.getElementById('mainLng').value;
            const fieldKota = document.getElementById('inputKota');

            // Cek jika koordinat ada dan kolom kota masih kosong
            if (lat && lng && fieldKota.value.trim() === '') {
                fieldKota.value = "Menerjemahkan satelit...";
                try {
                    // Tembak API OpenStreetMap secara diam-diam di background
                    const response = await fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&zoom=18&addressdetails=1`);
                    const data = await response.json();
                    
                    if(data && data.address) {
                        const addr = data.address;
                        // Cari nama kota/kabupaten yang paling cocok
                        const cityName = addr.city || addr.town || addr.municipality || addr.county || addr.state_district || "Tidak Ditemukan";
                        fieldKota.value = cityName; // Tampilkan ke layar!
                    } else {
                        fieldKota.value = "Tidak Ditemukan";
                    }
                } catch (error) {
                    fieldKota.value = "Gagal memuat kota";
                }
            }
        }

        // Jalankan trik ini otomatis saat halaman selesai dimuat!
        autoLoadKota();

    });
</script>
@endpush        