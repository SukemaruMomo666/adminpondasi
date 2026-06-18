@extends('layouts.admin')

@section('title', 'Pengajuan Kenaikan Level Toko')

@push('styles')
<style>
    /* ========================================= */
    /* == POLYFILL DARK MODE (ANTI-PUTIH)     == */
    /* ========================================= */
    .dark .dark\:bg-slate-900 { background-color: #0f172a !important; }
    .dark .dark\:bg-slate-800 { background-color: #1e293b !important; }
    .dark .dark\:bg-slate-800\/50 { background-color: rgba(30, 41, 59, 0.5) !important; }
    .dark .dark\:bg-slate-800\/40 { background-color: rgba(30, 41, 59, 0.4) !important; }
    .dark .dark\:bg-slate-800\/30 { background-color: rgba(30, 41, 59, 0.3) !important; }
    .dark .dark\:hover\:bg-slate-800\/30:hover { background-color: rgba(30, 41, 59, 0.3) !important; }
    .dark .dark\:bg-slate-700 { background-color: #334155 !important; }

    .dark .dark\:border-slate-800 { border-color: #1e293b !important; }
    .dark .dark\:border-slate-800\/80 { border-color: rgba(30, 41, 59, 0.8) !important; }
    .dark .dark\:border-slate-700 { border-color: #334155 !important; }
    .dark .dark\:border-slate-700\/50 { border-color: rgba(51, 65, 85, 0.5) !important; }
    .dark .dark\:border-slate-600 { border-color: #475569 !important; }

    .dark .dark\:text-white { color: #ffffff !important; }
    .dark .dark\:text-slate-200 { color: #e2e8f0 !important; }
    .dark .dark\:text-slate-300 { color: #cbd5e1 !important; }
    .dark .dark\:text-slate-400 { color: #94a3b8 !important; }
    .dark .dark\:text-slate-500 { color: #64748b !important; }

    /* Custom Colors */
    .dark .dark\:bg-blue-500\/10 { background-color: rgba(59, 130, 246, 0.1) !important; }
    .dark .dark\:bg-blue-500\/5 { background-color: rgba(59, 130, 246, 0.05) !important; }
    .dark .dark\:border-blue-500\/20 { border-color: rgba(59, 130, 246, 0.2) !important; }
    .dark .dark\:text-blue-400 { color: #60a5fa !important; }

    .dark .dark\:bg-amber-500\/10 { background-color: rgba(245, 158, 11, 0.1) !important; }
    .dark .dark\:border-amber-500\/20 { border-color: rgba(245, 158, 11, 0.2) !important; }
    .dark .dark\:text-amber-400 { color: #fbbf24 !important; }

    .dark .dark\:bg-emerald-500\/10 { background-color: rgba(16, 185, 129, 0.1) !important; }
    .dark .dark\:border-emerald-500\/20 { border-color: rgba(16, 185, 129, 0.2) !important; }
    .dark .dark\:text-emerald-400 { color: #34d399 !important; }

    .dark .dark\:bg-rose-500\/10 { background-color: rgba(244, 63, 94, 0.1) !important; }
    .dark .dark\:border-rose-500\/20 { border-color: rgba(244, 63, 94, 0.2) !important; }
    .dark .dark\:text-rose-400 { color: #fb7185 !important; }
    
    .dark .dark\:bg-purple-500\/10 { background-color: rgba(168, 85, 247, 0.1) !important; }
    .dark .dark\:border-purple-500\/20 { border-color: rgba(168, 85, 247, 0.2) !important; }
    .dark .dark\:text-purple-400 { color: #c084fc !important; }
</style>
@endpush

@section('content')
<div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-4 mb-8">
    <div>
        <h2 class="text-2xl md:text-3xl font-black text-slate-800 dark:text-white tracking-tight mb-1 transition-colors duration-300">
            Pengajuan Naik Level
        </h2>
        <div class="flex items-center gap-2 text-xs font-bold text-slate-500 dark:text-slate-400 transition-colors duration-300">
            <a href="{{ route('admin.stores.index') }}" class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors text-decoration-none">Manajemen Toko</a>
            <i class="mdi mdi-chevron-right text-sm"></i>
            <span class="text-blue-600 dark:text-blue-400">Daftar Pengajuan Tier</span>
        </div>
    </div>
    <div class="bg-blue-50 dark:bg-blue-500/10 border border-blue-100 dark:border-blue-500/20 px-4 py-2.5 rounded-xl flex items-center gap-3 transition-colors duration-300">
        <i class="mdi mdi-shield-check-outline text-blue-500 text-xl"></i>
        <span class="text-xs font-bold text-blue-700 dark:text-blue-400">Verifikasi data dan performa toko sebelum menyetujui kenaikan level.</span>
    </div>
</div>

<div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-[1.5rem] shadow-sm overflow-hidden">
    {{-- Tabs Filter --}}
    <div class="p-5 border-b border-slate-100 dark:border-slate-800/80 bg-white dark:bg-slate-900 flex justify-between items-center transition-colors duration-300">
        <div class="flex p-1 bg-slate-50 dark:bg-slate-800/50 rounded-xl border border-slate-200 dark:border-slate-700/50 overflow-x-auto filter-wrapper">
            @php
                $tabs = [
                    'pending' => 'Menunggu',
                    'revision' => 'Butuh Revisi',
                    'approved' => 'Disetujui',
                    'rejected' => 'Ditolak',
                    'semua' => 'Semua Data'
                ];
            @endphp
            @foreach($tabs as $val => $label)
                <a href="{{ route('admin.stores.tierApplications', ['status' => $val]) }}"
                   class="px-4 py-2 text-xs font-black capitalize rounded-lg transition-all text-decoration-none outline-none whitespace-nowrap {{ $status_filter == $val ? 'bg-white dark:bg-slate-700 text-blue-600 dark:text-white shadow-sm border border-slate-200 dark:border-slate-600' : 'text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>
    </div>

    {{-- Tabel Data --}}
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse whitespace-nowrap">
            <thead>
                <tr class="bg-slate-50 dark:bg-slate-800/30 border-b border-slate-200 dark:border-slate-800">
                    <th class="px-6 py-4 text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest">Toko & Pemilik</th>
                    <th class="px-6 py-4 text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest">Level Transisi</th>
                    <th class="px-6 py-4 text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest">Performa Snapshot</th>
                    <th class="px-6 py-4 text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest">Status</th>
                    <th class="px-6 py-4 text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800/50">
                @forelse($applications as $app)
                <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors duration-200">
                    <td class="px-6 py-5">
                        <div class="flex items-center gap-3">
                            <img src="{{ $app->logo_toko ? asset('storage/'.$app->logo_toko) : 'https://placehold.co/100x100?text='.substr($app->nama_toko,0,1) }}" class="w-10 h-10 rounded-lg object-cover shadow-sm border dark:border-slate-700">
                            <div>
                                <div class="text-sm font-black text-slate-800 dark:text-white">{{ $app->nama_toko }}</div>
                                <div class="text-[11px] font-bold text-slate-500 dark:text-slate-400">{{ $app->nama_pemilik }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-5">
                        <div class="flex items-center gap-2">
                            <span class="text-[10px] font-black text-slate-400 uppercase">{{ str_replace('_', ' ', $app->tier_saat_ini) }}</span>
                            <i class="mdi mdi-arrow-right text-blue-500"></i>
                            <span class="text-xs font-black text-blue-600 dark:text-blue-400 uppercase">{{ str_replace('_', ' ', $app->tier_tujuan) }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-5">
                        @php $meta = json_decode($app->metadata_syarat, true); @endphp
                        <div class="space-y-1">
                            <div class="text-[11px] font-bold text-slate-600 dark:text-slate-300 flex items-center gap-1.5">
                                <i class="mdi mdi-star text-warning"></i> Rating: {{ $meta['rating_snapshot'] ?? '-' }}
                            </div>
                            <div class="text-[11px] font-bold text-slate-600 dark:text-slate-300 flex items-center gap-1.5">
                                <i class="mdi mdi-shopping text-blue-500"></i> Pesanan: {{ $meta['pesanan_snapshot'] ?? '-' }}
                            </div>
                            <div class="flex gap-2 mt-1">
                                <span class="text-[9px] font-black px-1.5 py-0.5 rounded {{ ($meta['nib_snapshot'] ?? false) ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-400' }}">NIB</span>
                                <span class="text-[9px] font-black px-1.5 py-0.5 rounded {{ ($meta['npwp_snapshot'] ?? false) ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-400' }}">NPWP</span>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-5">
                        @php
                            $st = $app->status;
                            $stClass = '';
                            if($st == 'pending') $stClass = 'bg-amber-100 text-amber-700 border-amber-200 dark:bg-amber-500/10 dark:text-amber-400 dark:border-amber-500/20';
                            elseif($st == 'approved') $stClass = 'bg-emerald-100 text-emerald-700 border-emerald-200 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-500/20';
                            elseif($st == 'revision') $stClass = 'bg-blue-100 text-blue-700 border-blue-200 dark:bg-blue-500/10 dark:text-blue-400 dark:border-blue-500/20';
                            else $stClass = 'bg-rose-100 text-rose-700 border-rose-200 dark:bg-rose-500/10 dark:text-rose-400 dark:border-rose-500/20';
                        @endphp
                        <span class="px-2.5 py-1 rounded-md text-[10px] font-black uppercase tracking-wider border {{ $stClass }}">
                            {{ $st }}
                        </span>
                    </td>
                    <td class="px-6 py-5 text-center">
                        @if($st == 'pending' || $st == 'revision')
                        <button class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-blue-50 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400 border border-blue-100 dark:border-blue-500/20 hover:bg-blue-600 hover:text-white transition-all outline-none btn-process"
                            data-id="{{ $app->id }}"
                            data-toko="{{ $app->nama_toko }}"
                            data-catatan="{{ $app->catatan_penjual }}">
                            <i class="mdi mdi-eye-check-outline text-lg"></i>
                        </button>
                        @else
                        <span class="text-xs font-bold text-slate-400">-</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-20 bg-slate-50/30 dark:bg-slate-800/30">
                        <i class="mdi mdi-clipboard-text-off-outline text-4xl text-slate-300 dark:text-slate-600 mb-3 block"></i>
                        <h5 class="text-base font-black text-slate-700 dark:text-slate-300">Belum ada pengajuan masuk</h5>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-800">
        {{ $applications->links('pagination::bootstrap-5') }}
    </div>
</div>

{{-- Modal Proses --}}
<div class="modal fade" id="modalProcess" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-2xl rounded-3xl overflow-hidden dark:bg-slate-900 dark:border-slate-700">
            <div class="modal-header bg-slate-50 dark:bg-slate-800/50 border-b dark:border-slate-700 p-6">
                <h5 class="modal-title font-black text-slate-800 dark:text-white">Tinjau Pengajuan: <span id="modalTokoName"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formProcess" method="POST">
                @csrf
                <div class="modal-body p-6">
                    <div class="bg-blue-50 dark:bg-blue-500/5 border border-blue-100 dark:border-blue-500/20 rounded-2xl p-4 mb-5">
                        <strong class="text-[10px] font-black text-blue-600 dark:text-blue-400 uppercase tracking-widest block mb-2">Catatan Penjual:</strong>
                        <p id="modalSellerNote" class="text-sm font-bold text-slate-700 dark:text-slate-300 m-0"></p>
                    </div>

                    <div class="mb-4">
                        <label class="form-label text-[10px] font-black text-slate-500 uppercase tracking-widest">Tindakan Admin</label>
                        <select name="action" class="form-select rounded-xl font-bold text-sm border-slate-200 dark:bg-slate-800 dark:border-slate-700 dark:text-white" required>
                            <option value="approve">Setujui (Naik Level)</option>
                            <option value="revision">Minta Revisi Data</option>
                            <option value="reject">Tolak Pengajuan</option>
                        </select>
                    </div>

                    <div class="mb-0">
                        <label class="form-label text-[10px] font-black text-slate-500 uppercase tracking-widest">Alasan / Pesan ke Penjual</label>
                        <textarea name="alasan" class="form-control rounded-xl font-bold text-sm border-slate-200 dark:bg-slate-800 dark:border-slate-700 dark:text-white" rows="3" placeholder="Contoh: Selamat! Toko Anda resmi menjadi Power Merchant."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-slate-50 dark:bg-slate-800/50 border-t dark:border-slate-700 p-4">
                    <button type="button" class="px-5 py-2.5 rounded-xl text-xs font-black text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-white transition-all" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-blue-600 text-white text-xs font-black shadow-lg hover:bg-blue-700 transition-all">Simpan Keputusan</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const modal = new bootstrap.Modal(document.getElementById('modalProcess'));
        
        document.querySelectorAll('.btn-process').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const toko = this.getAttribute('data-toko');
                const catatan = this.getAttribute('data-catatan') || 'Tidak ada catatan.';
                
                document.getElementById('modalTokoName').innerText = toko;
                document.getElementById('modalSellerNote').innerText = catatan;
                document.getElementById('formProcess').action = `/portal-rahasia-pks/stores/tier-applications/${id}/process`;
                
                modal.show();
            });
        });
    });
</script>
@endpush

<style>
    .font-black { font-weight: 900; }
    .shadow-2xl { box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); }

    /* Override Bootstrap Pagination for Dark Mode */
    .dark .pagination .page-link { background-color: #1e293b; border-color: #334155; color: #cbd5e1; }
    .dark .pagination .page-item.active .page-link { background-color: #3b82f6; border-color: #3b82f6; color: white; }
    .dark .pagination .page-item.disabled .page-link { background-color: #0f172a; color: #475569; border-color: #1e293b; }

    /* Dark Mode Modal Polyfill */
    .dark .modal-content { background-color: #0f172a !important; border: 1px solid #1e293b !important; }
    .dark .btn-close { filter: invert(1) grayscale(100%) brightness(200%); opacity: 0.5; }
    .dark .btn-close:hover { opacity: 1; }
</style>
@endsection
