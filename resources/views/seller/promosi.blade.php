@extends('layouts.seller')

@section('title', 'Manajemen Harga Coret (Diskon)')

@push('styles')
<style>
    .hide-scrollbar::-webkit-scrollbar { display: none; }
    .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-slate-50 p-4 md:p-6 lg:p-8 font-sans text-slate-900 space-y-6">

    {{-- SETUP SWEETALERT TOAST --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        const Toast = Swal.mixin({
            toast: true, position: 'top-end', showConfirmButton: false, timer: 3000, timerProgressBar: true,
            customClass: { popup: 'rounded-2xl shadow-lg border border-slate-100' }
        });
    </script>
    @if(session('success'))
        <script>document.addEventListener('DOMContentLoaded', () => Toast.fire({icon: 'success', title: '{!! session('success') !!}'}));</script>
    @endif
    @if(session('error'))
        <script>document.addEventListener('DOMContentLoaded', () => Swal.fire({title: 'Gagal!', text: '{!! session('error') !!}', icon: 'error', customClass: { popup: 'rounded-3xl' }}));</script>
    @endif

    {{-- 1. HEADER & STATS --}}
    <div class="flex flex-col xl:flex-row justify-between items-start xl:items-center gap-6">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-white border border-slate-200 rounded-2xl flex items-center justify-center text-red-500 shadow-sm flex-shrink-0">
                <i class="mdi mdi-tag-multiple-outline text-2xl"></i>
            </div>
            <div>
                <h1 class="text-2xl font-black text-slate-900 tracking-tight">Harga Coret Produk</h1>
                <p class="text-sm font-medium text-slate-500 mt-0.5">Atur diskon produk agar lebih menarik perhatian pembeli.</p>
            </div>
        </div>

        <div class="flex bg-white border border-slate-200 rounded-2xl p-2 shadow-sm w-full xl:w-auto overflow-x-auto hide-scrollbar">
            <div class="px-5 py-2 border-r border-slate-100 min-w-[120px]">
                <h6 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Semua Promo</h6>
                <h3 class="text-xl font-black text-slate-800">{{ $stats['semua'] ?? 0 }}</h3>
            </div>
            <div class="px-5 py-2 border-r border-slate-100 min-w-[120px]">
                <h6 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Sedang Aktif</h6>
                <h3 class="text-xl font-black text-red-500">{{ $stats['aktif'] ?? 0 }}</h3>
            </div>
            <div class="px-5 py-2 min-w-[120px]">
                <h6 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Akan Datang</h6>
                <h3 class="text-xl font-black text-slate-800">{{ $stats['akan_datang'] ?? 0 }}</h3>
            </div>
        </div>
    </div>

    {{-- 2. TABS & TOOLBAR --}}
    <div class="bg-white border border-slate-200 rounded-3xl shadow-sm overflow-hidden flex flex-col">

        @php $tab = $currentTab ?? 'semua'; @endphp
        <div class="flex gap-6 px-6 pt-4 border-b border-slate-100 overflow-x-auto hide-scrollbar bg-slate-50/50">
            <a href="?tab=semua" class="pb-3 text-sm font-bold whitespace-nowrap transition-colors border-b-2 {{ $tab == 'semua' ? 'border-red-500 text-red-500' : 'border-transparent text-slate-500 hover:text-slate-800' }}">Semua</a>
            <a href="?tab=aktif" class="pb-3 text-sm font-bold whitespace-nowrap transition-colors border-b-2 {{ $tab == 'aktif' ? 'border-red-500 text-red-500' : 'border-transparent text-slate-500 hover:text-slate-800' }}">Sedang Berjalan</a>
            <a href="?tab=akan_datang" class="pb-3 text-sm font-bold whitespace-nowrap transition-colors border-b-2 {{ $tab == 'akan_datang' ? 'border-red-500 text-red-500' : 'border-transparent text-slate-500 hover:text-slate-800' }}">Akan Datang</a>
            <a href="?tab=tidak_aktif" class="pb-3 text-sm font-bold whitespace-nowrap transition-colors border-b-2 {{ $tab == 'tidak_aktif' ? 'border-red-500 text-red-500' : 'border-transparent text-slate-500 hover:text-slate-800' }}">Tidak Aktif / Selesai</a>
        </div>

        <div class="p-4 sm:p-6 border-b border-slate-100 bg-white">
            <form action="{{ route('seller.promotion.discounts') }}" method="GET" class="relative w-full md:max-w-md group m-0">
                <input type="hidden" name="tab" value="{{ $tab }}">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <i class="mdi mdi-magnify text-slate-400 group-focus-within:text-red-500 transition-colors text-lg"></i>
                </div>
                <input type="text" name="search" placeholder="Cari Nama Produk..." value="{{ request('search') }}" class="w-full pl-11 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold text-slate-900 placeholder-slate-400 focus:ring-2 focus:ring-red-500 focus:border-red-500 focus:bg-white outline-none transition-all shadow-sm">
            </form>
        </div>

        {{-- 3. TABEL PROMOSI --}}
        <div class="overflow-x-auto w-full">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200">
                        <th class="py-4 px-6 text-[11px] font-black text-slate-500 uppercase tracking-widest whitespace-nowrap">Informasi Produk</th>
                        <th class="py-4 px-6 text-[11px] font-black text-slate-500 uppercase tracking-widest whitespace-nowrap">Diskon</th>
                        <th class="py-4 px-6 text-[11px] font-black text-slate-500 uppercase tracking-widest whitespace-nowrap">Harga Akhir</th>
                        <th class="py-4 px-6 text-[11px] font-black text-slate-500 uppercase tracking-widest whitespace-nowrap">Periode Promo</th>
                        <th class="py-4 px-6 text-[11px] font-black text-slate-500 uppercase tracking-widest whitespace-nowrap text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($products as $p)
                        @php
                            $hasPromo = !empty($p->nilai_diskon) && $p->nilai_diskon > 0;
                            $now = \Carbon\Carbon::now();
                            $start = $p->diskon_mulai ? \Carbon\Carbon::parse($p->diskon_mulai) : null;
                            $end = $p->diskon_berakhir ? \Carbon\Carbon::parse($p->diskon_berakhir) : null;

                            $statusClass = 'bg-slate-100 text-slate-600 border-slate-200'; 
                            $statusText = 'Tidak Aktif';
                            
                            if ($hasPromo) {
                                if ($start && $end) {
                                    if ($now->between($start, $end)) { 
                                        $statusClass = 'bg-emerald-50 text-emerald-600 border-emerald-200'; 
                                        $statusText = 'Aktif'; 
                                    } elseif ($now->lt($start)) { 
                                        $statusClass = 'bg-amber-50 text-amber-600 border-amber-200'; 
                                        $statusText = 'Mendatang'; 
                                    } else { 
                                        $statusClass = 'bg-slate-100 text-slate-600 border-slate-200'; 
                                        $statusText = 'Berakhir'; 
                                    }
                                } else {
                                    // Jika tgl tidak diatur, anggap aktif terus (atau sesuaikan kebijakan)
                                    $statusClass = 'bg-emerald-50 text-emerald-600 border-emerald-200'; 
                                    $statusText = 'Aktif'; 
                                }
                            }

                            $hargaAkhir = $p->harga;
                            if ($hasPromo && $statusText == 'Aktif') {
                                if ($p->tipe_diskon == 'PERSEN') {
                                    $potongan = ($p->harga * $p->nilai_diskon) / 100;
                                    $hargaAkhir = $p->harga - $potongan;
                                } else {
                                    $hargaAkhir = $p->harga - $p->nilai_diskon;
                                }
                            }
                        @endphp
                        <tr class="hover:bg-slate-50/50 transition-colors group">

                            <td class="py-4 px-6">
                                <div class="flex items-center gap-4 min-w-[250px]">
                                    <img src="{{ asset('assets/uploads/products/' . ($p->gambar_utama ?? 'default.jpg')) }}" class="w-14 h-14 rounded-xl object-cover border border-slate-200 flex-shrink-0">
                                    <div>
                                        <h6 class="text-sm font-bold text-slate-900 leading-snug mb-1 line-clamp-2">{{ $p->nama_barang }}</h6>
                                        @if($hasPromo)
                                            <span class="text-xs font-bold text-slate-400 line-through">Rp {{ number_format($p->harga, 0, ',', '.') }}</span>
                                        @else
                                            <span class="text-xs font-bold text-slate-600">Rp {{ number_format($p->harga, 0, ',', '.') }}</span>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <td class="py-4 px-6 whitespace-nowrap">
                                @if($hasPromo)
                                    <div class="inline-flex items-center justify-center bg-red-50 text-red-600 border border-red-200 font-black text-xs px-2.5 py-1 rounded-lg">
                                        {{ $p->tipe_diskon == 'PERSEN' ? $p->nilai_diskon.'%' : 'Rp '.number_format($p->nilai_diskon, 0, ',', '.') }}
                                    </div>
                                @else
                                    <span class="text-slate-400 font-black">-</span>
                                @endif
                            </td>

                            <td class="py-4 px-6 whitespace-nowrap">
                                @if($hasPromo)
                                    <div class="text-base font-black text-red-500">Rp {{ number_format($hargaAkhir, 0, ',', '.') }}</div>
                                @else
                                    <span class="text-slate-400 font-black">-</span>
                                @endif
                            </td>

                            <td class="py-4 px-6 whitespace-nowrap">
                                @if($hasPromo)
                                    <div class="text-[11px] font-bold text-slate-500 mb-1.5">
                                        {{ $start ? $start->format('d M Y') : '-' }} - {{ $end ? $end->format('d M Y') : '-' }}
                                    </div>
                                    <span class="inline-flex px-2.5 py-1 rounded-lg border text-[10px] font-black uppercase tracking-widest {{ $statusClass }}">
                                        {{ $statusText }}
                                    </span>
                                @else
                                    <span class="inline-flex px-2.5 py-1 rounded-lg border text-[10px] font-black uppercase tracking-widest bg-slate-100 text-slate-500 border-slate-200">
                                        Tidak Aktif
                                    </span>
                                @endif
                            </td>

                            <td class="py-4 px-6 text-right">
                                <button type="button" onclick="openPromoModal({{ json_encode($p) }})" class="inline-flex items-center gap-1.5 px-4 py-2 bg-white border border-slate-200 text-slate-600 hover:bg-red-50 hover:text-red-600 hover:border-red-200 text-xs font-bold rounded-xl transition-all shadow-sm">
                                    <i class="mdi mdi-pencil-outline text-sm"></i> Atur Promo
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-16 text-center">
                                <div class="flex flex-col items-center justify-center opacity-60">
                                    <i class="mdi mdi-tag-off-outline text-6xl text-slate-300 mb-4"></i>
                                    <h5 class="text-lg font-black text-slate-800 mb-1">Tidak Ada Produk</h5>
                                    <p class="text-sm font-medium text-slate-500">Tidak ada data produk yang sesuai dengan filter.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($products->hasPages())
            <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex justify-center lg:justify-end">
                {{ $products->appends(request()->query())->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>
</div>

{{-- MODAL TAILWIND ATUR PROMO --}}
<div id="modalPromo" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div id="modalOverlay" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity opacity-0 duration-300" onclick="closePromoModal()"></div>

    <div class="fixed inset-0 z-10 overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div id="modalPanel" class="relative transform overflow-hidden rounded-3xl bg-white text-left shadow-2xl transition-all opacity-0 scale-95 duration-300 w-full sm:max-w-lg border border-slate-200">

                <form id="formPromo">
                    <input type="hidden" name="product_ids[]" id="input_product_id">

                    <div class="bg-slate-50 px-6 py-5 border-b border-slate-100 flex items-center justify-between">
                        <h3 class="text-lg font-black text-slate-900 flex items-center gap-2"><i class="mdi mdi-tag-plus text-red-500"></i> Atur Harga Coret</h3>
                        <button type="button" onclick="closePromoModal()" class="w-8 h-8 rounded-full bg-slate-200 hover:bg-red-100 text-slate-500 hover:text-red-500 flex items-center justify-center transition-colors">
                            <i class="mdi mdi-close text-lg leading-none"></i>
                        </button>
                    </div>

                    <div class="p-6 space-y-5">
                        <div class="bg-blue-50 border border-blue-100 p-4 rounded-2xl">
                            <label class="block text-[10px] font-black text-blue-400 uppercase tracking-widest mb-1">NAMA PRODUK</label>
                            <p id="modal_product_name" class="text-sm font-bold text-slate-900 m-0"></p>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-[11px] font-black text-slate-500 uppercase tracking-widest mb-2">Tipe Diskon</label>
                                <select name="tipe_diskon" id="modal_tipe_diskon" class="w-full bg-slate-50 border border-slate-200 text-slate-900 text-sm font-bold rounded-xl px-4 py-3 focus:bg-white focus:ring-2 focus:ring-red-500 outline-none cursor-pointer" required>
                                    <option value="PERSEN">Persentase (%)</option>
                                    <option value="NOMINAL">Nominal (Rp)</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[11px] font-black text-slate-500 uppercase tracking-widest mb-2">Nilai Diskon</label>
                                <input type="number" name="nilai_diskon" id="modal_nilai_diskon" class="w-full bg-slate-50 border border-slate-200 text-slate-900 text-sm font-bold rounded-xl px-4 py-3 focus:bg-white focus:ring-2 focus:ring-red-500 outline-none placeholder-slate-400" placeholder="Cth: 10" required>
                                <p class="text-[10px] font-bold text-slate-400 mt-1.5">*Isi 0 untuk mematikan promo.</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-[11px] font-black text-slate-500 uppercase tracking-widest mb-2">Mulai</label>
                                <input type="datetime-local" name="diskon_mulai" id="modal_diskon_mulai" class="w-full bg-slate-50 border border-slate-200 text-slate-900 text-sm font-bold rounded-xl px-4 py-3 focus:bg-white focus:ring-2 focus:ring-red-500 outline-none">
                            </div>
                            <div>
                                <label class="block text-[11px] font-black text-slate-500 uppercase tracking-widest mb-2">Berakhir</label>
                                <input type="datetime-local" name="diskon_berakhir" id="modal_diskon_berakhir" class="w-full bg-slate-50 border border-slate-200 text-slate-900 text-sm font-bold rounded-xl px-4 py-3 focus:bg-white focus:ring-2 focus:ring-red-500 outline-none">
                            </div>
                        </div>
                    </div>

                    <div class="bg-slate-50 px-6 py-4 border-t border-slate-100 flex flex-col sm:flex-row justify-end gap-3 rounded-b-3xl">
                        <button type="button" onclick="closePromoModal()" class="w-full sm:w-auto px-6 py-2.5 bg-white border border-slate-300 text-slate-700 font-bold rounded-xl hover:bg-slate-100 transition-colors">Batal</button>
                        <button type="button" id="btnSavePromo" class="w-full sm:w-auto px-6 py-2.5 bg-red-500 hover:bg-red-600 text-white font-bold rounded-xl shadow-sm shadow-red-500/20 transition-all flex items-center justify-center gap-2">
                            Simpan Promo
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>

<script>
    // FUNGSI MODAL TAILWIND
    const modalPromo = document.getElementById('modalPromo');
    const modalOverlay = document.getElementById('modalOverlay');
    const modalPanel = document.getElementById('modalPanel');

    function formatDatetimeLocal(dateStr) {
        if (!dateStr) return '';
        // Ubah format 'YYYY-MM-DD HH:MM:SS' menjadi 'YYYY-MM-DDTHH:MM'
        return dateStr.replace(' ', 'T').substring(0, 16);
    }

    function openPromoModal(product) {
        document.getElementById('input_product_id').value = product.id;
        document.getElementById('modal_product_name').innerText = product.nama_barang;
        document.getElementById('modal_tipe_diskon').value = product.tipe_diskon || 'PERSEN';
        document.getElementById('modal_nilai_diskon').value = product.nilai_diskon || '';
        document.getElementById('modal_diskon_mulai').value = formatDatetimeLocal(product.diskon_mulai);
        document.getElementById('modal_diskon_berakhir').value = formatDatetimeLocal(product.diskon_berakhir);

        modalPromo.classList.remove('hidden');
        void modalPromo.offsetWidth;
        modalOverlay.classList.replace('opacity-0', 'opacity-100');
        modalPanel.classList.replace('opacity-0', 'opacity-100');
        modalPanel.classList.replace('scale-95', 'scale-100');
    }

    function closePromoModal() {
        modalOverlay.classList.replace('opacity-100', 'opacity-0');
        modalPanel.classList.replace('opacity-100', 'opacity-0');
        modalPanel.classList.replace('scale-100', 'scale-95');
        setTimeout(() => modalPromo.classList.add('hidden'), 300);
    }

    // FUNGSI SIMPAN AJAX + LOADING STATE
    document.getElementById('btnSavePromo').addEventListener('click', function() {
        let btn = this;
        let originalText = btn.innerHTML;

        // Aktifkan Loading State
        btn.disabled = true;
        btn.innerHTML = '<i class="mdi mdi-loading mdi-spin"></i> Menyimpan...';
        btn.classList.add('opacity-70', 'cursor-not-allowed');

        let form = document.getElementById('formPromo');
        let formData = new FormData(form);
        let dataObj = Object.fromEntries(formData.entries());
        dataObj.product_ids = [document.getElementById('input_product_id').value];

        fetch("{{ route('seller.promotion.discounts.update') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify(dataObj)
        })
        .then(response => response.json())
        .then(data => {
            if(data.status === 'success') {
                Swal.fire({
                    toast: true, position: 'top-end', icon: 'success',
                    title: data.message, showConfirmButton: false, timer: 1500, customClass: { popup: 'rounded-2xl' }
                }).then(() => location.reload());
            } else {
                Swal.fire({title: 'Gagal!', text: data.message || 'Terjadi kesalahan', icon: 'error', customClass: { popup: 'rounded-3xl' }});
                resetBtn();
            }
        })
        .catch(err => {
            Swal.fire({title: 'Error!', text: 'Sistem gagal menghubungi server.', icon: 'error', customClass: { popup: 'rounded-3xl' }});
            resetBtn();
        });

        function resetBtn() {
            btn.disabled = false;
            btn.innerHTML = originalText;
            btn.classList.remove('opacity-70', 'cursor-not-allowed');
        }
    });
</script>
@endsection
