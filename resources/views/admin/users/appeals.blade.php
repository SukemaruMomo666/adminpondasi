@extends('layouts.admin')

@section('title', 'Banding Penangguhan Akun')

@push('styles')
<style>
    /* Radio Button Custom Styling */
    .decision-card input:checked + .card-content {
        transform: scale(1.05);
    }
    
    /* We use standard CSS for states that are hard to do with classes only when combined with Peer/Has */
    .decision-card-active-disetujui {
        border-color: #10b981 !important;
        background-color: #ecfdf5 !important;
    }
    .dark .decision-card-active-disetujui {
        background-color: rgba(6, 78, 59, 0.4) !important;
    }

    .decision-card-active-ditolak {
        border-color: #ef4444 !important;
        background-color: #fef2f2 !important;
    }
    .dark .decision-card-active-ditolak {
        background-color: rgba(153, 27, 27, 0.4) !important;
    }
</style>
@endpush

@section('content')
<div class="space-y-4 pb-8">
    {{-- HEADER --}}
    <div class="bg-white dark:!bg-slate-900 p-4 sm:p-6 rounded-2xl border border-slate-200 dark:!border-slate-700 shadow-sm flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-indigo-50 dark:!bg-indigo-900/50 text-indigo-600 dark:!text-indigo-400 rounded-xl flex items-center justify-center border border-indigo-100 dark:!border-indigo-700">
                <i class="mdi mdi-gavel text-2xl"></i>
            </div>
            <div>
                <h2 class="text-xl font-black text-slate-900 dark:!text-white mb-0.5">Pusat Banding Akun</h2>
                <div class="flex items-center gap-2">
                    <span class="px-2 py-0.5 bg-amber-100 dark:!bg-amber-900/50 text-amber-700 dark:!text-amber-400 text-[9px] font-bold uppercase tracking-wider rounded border border-amber-200 dark:!border-amber-700">Moderasi Diperlukan</span>
                    <p class="text-xs font-medium text-slate-600 dark:!text-slate-400 m-0">Tinjau permohonan dengan teliti.</p>
                </div>
            </div>
        </div>
        
        <div class="px-5 py-2 bg-slate-50 dark:!bg-slate-800 rounded-xl border border-slate-200 dark:!border-slate-700">
            <div class="text-[10px] font-bold text-slate-500 dark:!text-slate-400 uppercase tracking-wide mb-0.5 text-center">Total Kasus</div>
            <div class="text-lg font-black text-slate-900 dark:!text-white text-center">{{ $appeals->total() }}</div>
        </div>
    </div>

    {{-- TABEL DATA --}}
    <div class="bg-white dark:!bg-slate-900 border border-slate-200 dark:!border-slate-700 rounded-2xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-[800px]">
                <thead>
                    <tr class="bg-slate-50 dark:!bg-slate-800 border-b border-slate-200 dark:!border-slate-700">
                        <th class="px-5 py-3 text-[11px] font-bold text-slate-700 dark:!text-slate-300 uppercase tracking-wider">Pengguna</th>
                        <th class="px-5 py-3 text-[11px] font-bold text-slate-700 dark:!text-slate-300 uppercase tracking-wider">Pernyataan</th>
                        <th class="px-5 py-3 text-[11px] font-bold text-slate-700 dark:!text-slate-300 uppercase tracking-wider text-center">Bukti</th>
                        <th class="px-5 py-3 text-[11px] font-bold text-slate-700 dark:!text-slate-300 uppercase tracking-wider text-center">Status</th>
                        <th class="px-5 py-3 text-[11px] font-bold text-slate-700 dark:!text-slate-300 uppercase tracking-wider text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:!divide-slate-700">
                    @forelse($appeals as $appeal)
                    <tr class="hover:bg-slate-50 dark:hover:!bg-slate-800/80 transition-all duration-200">
                        <td class="px-5 py-4 align-middle">
                            <div class="flex flex-col gap-1">
                                <span class="font-bold text-slate-900 dark:!text-white text-sm">{{ $appeal->nama }}</span>
                                <div class="flex items-center gap-2">
                                    <span class="px-1.5 py-0.5 bg-indigo-50 dark:!bg-indigo-900/50 text-indigo-600 dark:!text-indigo-300 text-[9px] font-bold rounded uppercase border border-indigo-100 dark:!border-indigo-700/50">{{ $appeal->level }}</span>
                                    <span class="text-[11px] font-medium text-slate-500 dark:!text-slate-400"><i class="mdi mdi-clock-outline"></i> {{ \Carbon\Carbon::parse($appeal->created_at)->diffForHumans() }}</span>
                                </div>
                                <span class="text-[11px] font-medium text-slate-600 dark:!text-slate-400 truncate max-w-[150px]">{{ $appeal->email }}</span>
                            </div>
                        </td>
                        <td class="px-5 py-4 align-middle max-w-[200px]">
                            <p class="text-xs font-medium text-slate-700 dark:!text-slate-300 leading-relaxed m-0 line-clamp-2" title="{{ $appeal->alasan_banding }}">
                                "{{ $appeal->alasan_banding }}"
                            </p>
                        </td>
                        <td class="px-5 py-4 align-middle text-center">
                            @if($appeal->bukti_pendukung)
                                <a href="{{ asset('assets/uploads/appeals/' . $appeal->bukti_pendukung) }}" target="_blank" 
                                   class="inline-flex w-8 h-8 rounded-lg bg-slate-50 dark:!bg-slate-800 border border-slate-200 dark:!border-slate-600 items-center justify-center text-slate-600 dark:!text-slate-300 hover:text-indigo-600 dark:hover:!text-indigo-400 transition-colors">
                                    <i class="mdi mdi-file-image-outline text-base"></i>
                                </a>
                            @else
                                <div class="w-8 h-8 rounded-lg bg-slate-50 dark:!bg-slate-800 border border-dashed border-slate-200 dark:!border-slate-700 flex items-center justify-center mx-auto text-slate-400 dark:!text-slate-500">
                                    <i class="mdi mdi-image-off-outline text-base"></i>
                                </div>
                            @endif
                        </td>
                        <td class="px-5 py-4 align-middle text-center">
                            @if($appeal->status === 'pending')
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 bg-amber-50 dark:!bg-amber-900/30 text-amber-700 dark:!text-amber-400 text-[10px] font-bold rounded-full border border-amber-200 dark:!border-amber-700">
                                    <span class="w-1 h-1 rounded-full bg-amber-500 animate-pulse"></span> Pending
                                </span>
                            @elseif($appeal->status === 'disetujui')
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 bg-emerald-50 dark:!bg-emerald-900/30 text-emerald-700 dark:!text-emerald-400 text-[10px] font-bold rounded-full border border-emerald-200 dark:!border-emerald-700">
                                    <i class="mdi mdi-check font-bold"></i> Disetujui
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 bg-rose-50 dark:!bg-rose-900/30 text-rose-700 dark:!text-rose-400 text-[10px] font-bold rounded-full border border-rose-200 dark:!border-rose-700">
                                    <i class="mdi mdi-close font-bold"></i> Ditolak
                                </span>
                            @endif
                        </td>
                        <td class="px-5 py-4 align-middle text-right">
                            @if($appeal->status === 'pending')
                                <button type="button" onclick='openReviewModal(@json($appeal))' class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 dark:!bg-indigo-600 dark:hover:!bg-indigo-500 text-white text-[11px] font-bold rounded-md transition-colors border border-indigo-700">
                                    Tinjau Kasus
                                </button>
                            @else
                                <span class="text-[11px] font-bold text-slate-400 dark:!text-slate-500 italic">Selesai</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-5 py-12 text-center bg-white dark:!bg-slate-900">
                            <i class="mdi mdi-shield-check-outline text-4xl text-slate-300 dark:!text-slate-600 mb-2 block"></i>
                            <h3 class="text-base font-bold text-slate-700 dark:!text-slate-300">Belum Ada Banding</h3>
                            <p class="text-xs font-medium text-slate-500 dark:!text-slate-400">Semua kasus telah tertangani.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($appeals->hasPages())
            <div class="px-5 py-3 bg-slate-50 dark:!bg-slate-800 border-t border-slate-200 dark:!border-slate-700">
                {{ $appeals->links('pagination::tailwind') }}
            </div>
        @endif
    </div>
</div>

{{-- MODAL --}}
<div id="modalReview" class="fixed inset-0 z-[2000] hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen p-2 sm:p-4 text-center">
        <div class="fixed inset-0 transition-opacity bg-slate-900/50 dark:!bg-black/80 backdrop-blur-sm" aria-hidden="true" onclick="closeModal()"></div>
        
        <div class="relative bg-white dark:!bg-slate-900 rounded-2xl text-left shadow-xl transform transition-all w-full max-w-lg max-h-[85vh] flex flex-col border border-slate-200 dark:!border-slate-700">
            
            <form id="formProcessAppeal" method="POST" class="flex flex-col h-full overflow-hidden bg-white dark:!bg-slate-900 rounded-2xl">
                @csrf
                
                {{-- Header Modal --}}
                <div class="px-5 py-4 flex items-center justify-between border-b border-slate-200 dark:!border-slate-700 bg-slate-50 dark:!bg-slate-800 rounded-t-2xl shrink-0">
                    <div>
                        <h3 class="text-base font-black text-slate-900 dark:!text-white">Keputusan Moderasi</h3>
                        <p class="text-[10px] font-bold text-slate-500 dark:!text-slate-400">ID: <span id="modalId" class="font-mono text-indigo-600 dark:!text-indigo-400">#0000</span></p>
                    </div>
                    <button type="button" onclick="closeModal()" class="text-slate-400 hover:text-rose-500 dark:hover:!text-rose-400 p-1.5 bg-white dark:!bg-slate-700 rounded-md border border-slate-200 dark:!border-slate-600">
                        <i class="mdi mdi-close text-lg"></i>
                    </button>
                </div>

                {{-- Body Modal --}}
                <div class="p-5 overflow-y-auto flex-1 space-y-4">
                    <div class="flex items-center gap-3 p-3 bg-slate-50 dark:!bg-slate-800 rounded-xl border border-slate-200 dark:!border-slate-700">
                        <div id="modalAvatar" class="w-10 h-10 rounded-lg bg-indigo-100 dark:!bg-indigo-900 flex items-center justify-center text-indigo-700 dark:!text-indigo-300 font-black text-base border border-indigo-200 dark:!border-indigo-700">?</div>
                        <div>
                            <div id="modalName" class="text-xs font-black text-slate-900 dark:!text-white">Nama Pengguna</div>
                            <div id="modalEmail" class="text-[10px] font-medium text-slate-600 dark:!text-slate-400">email@example.com</div>
                        </div>
                    </div>

                    <div>
                        <label class="text-[10px] font-bold text-slate-700 dark:!text-slate-300 mb-1.5 block uppercase tracking-wide">Pernyataan Pengguna</label>
                        <div class="bg-indigo-50/50 dark:!bg-slate-800 rounded-xl p-3 border border-indigo-100 dark:!border-slate-700 max-h-24 overflow-y-auto">
                            <p id="viewReason" class="text-xs font-medium text-slate-800 dark:!text-slate-200 italic m-0"></p>
                        </div>
                    </div>

                    <div>
                        <label class="text-[10px] font-bold text-slate-700 dark:!text-slate-300 mb-2 block text-center uppercase tracking-wide">Tentukan Keputusan</label>
                        <div class="flex gap-3 justify-center">
                            <label class="decision-card group relative flex flex-col items-center justify-center p-3 bg-white dark:!bg-slate-800 border-2 border-slate-200 dark:!border-slate-600 rounded-xl cursor-pointer transition-all duration-200 overflow-hidden w-28 has-[:checked]:border-emerald-500 has-[:checked]:bg-emerald-50 dark:has-[:checked]:!bg-emerald-900/40">
                                <input type="radio" name="status" value="disetujui" class="absolute opacity-0 z-10 cursor-pointer w-full h-full" required>
                                <div class="card-content flex flex-col items-center gap-1.5 transition-transform duration-200 group-has-[:checked]:scale-105">
                                    <i class="mdi mdi-check-circle text-2xl text-emerald-500 dark:!text-emerald-400"></i>
                                    <span class="text-[11px] font-black text-slate-900 dark:!text-white">Terima</span>
                                </div>
                            </label>
                            
                            <label class="decision-card group relative flex flex-col items-center justify-center p-3 bg-white dark:!bg-slate-800 border-2 border-slate-200 dark:!border-slate-600 rounded-xl cursor-pointer transition-all duration-200 overflow-hidden w-28 has-[:checked]:border-rose-500 has-[:checked]:bg-rose-50 dark:has-[:checked]:!bg-rose-900/40">
                                <input type="radio" name="status" value="ditolak" class="absolute opacity-0 z-10 cursor-pointer w-full h-full" required>
                                <div class="card-content flex flex-col items-center gap-1.5 transition-transform duration-200 group-has-[:checked]:scale-105">
                                    <i class="mdi mdi-close-circle text-2xl text-rose-500 dark:!text-rose-400"></i>
                                    <span class="text-[11px] font-black text-slate-900 dark:!text-white">Tolak</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div>
                        <label class="text-[10px] font-bold text-slate-700 dark:!text-slate-300 mb-1.5 block uppercase tracking-wide">Catatan untuk Pengguna</label>
                        <textarea name="catatan_admin" rows="2" class="w-full bg-slate-50 dark:!bg-slate-800 border-2 border-slate-200 dark:!border-slate-700 rounded-xl p-2.5 text-xs font-medium text-slate-900 dark:!text-white focus:ring-2 focus:ring-indigo-500 outline-none resize-none" placeholder="Masukkan alasan keputusan..." required></textarea>
                    </div>
                </div>

                {{-- Footer Modal --}}
                <div class="px-5 py-3 bg-slate-50 dark:!bg-slate-800 border-t border-slate-200 dark:!border-slate-700 rounded-b-2xl flex justify-end gap-2 shrink-0">
                    <button type="button" onclick="closeModal()" class="px-4 py-2 text-[11px] font-bold text-slate-600 dark:!text-slate-300 hover:bg-slate-200 dark:hover:!bg-slate-700 rounded-md transition-colors">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 dark:!bg-indigo-500 hover:bg-indigo-700 text-white text-[11px] font-bold rounded-md transition-colors border border-indigo-700">Simpan Keputusan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openReviewModal(appeal) {
        // Reset form to clear previous selections/text
        const form = document.getElementById('formProcessAppeal');
        form.reset();

        document.getElementById('viewReason').innerText = appeal.alasan_banding || '-';
        document.getElementById('modalId').innerText = '#' + (appeal.id ? appeal.id.toString().padStart(4, '0') : '0000');
        document.getElementById('modalName').innerText = appeal.nama || 'Pengguna';
        document.getElementById('modalEmail').innerText = appeal.email || '-';
        document.getElementById('modalAvatar').innerText = (appeal.nama || '?').charAt(0).toUpperCase();
        
        form.action = "{{ url('portal-rahasia-pks/users/appeals') }}/" + appeal.id + "/process";
        
        const modal = document.getElementById('modalReview');
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeModal() {
        document.getElementById('modalReview').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }
    
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') closeModal();
    });
</script>
@endsection