@extends('layouts.seller')

@section('title', 'Manajemen Pesanan')

@push('styles')
<style>
    /* Sembunyikan scrollbar untuk Tab Menu agar terlihat clean seperti aplikasi Native */
    .hide-scrollbar::-webkit-scrollbar { display: none; }
    .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-slate-50 p-4 md:p-6 lg:p-8 font-sans text-slate-900 space-y-6">

    {{-- Notifikasi Sukses --}}
    @if(session('success'))
        <div class="flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-700 px-5 py-4 rounded-2xl shadow-sm mb-6 animate-[fadeIn_0.3s_ease-out]">
            <i class="mdi mdi-check-decagram text-2xl"></i>
            <span class="font-bold">{{ session('success') }}</span>
        </div>
    @endif

    {{-- HEADER PAGE --}}
    <div class="flex items-center gap-4">
        <div class="w-12 h-12 bg-white border border-slate-200 rounded-2xl flex items-center justify-center text-blue-600 shadow-sm flex-shrink-0">
            <i class="mdi mdi-clipboard-text-outline text-2xl"></i>
        </div>
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Manajemen Pesanan</h1>
            <p class="text-sm font-medium text-slate-500">Proses invoice pembeli, atur pengiriman, dan kelola logistik toko.</p>
        </div>
    </div>

    {{-- FILTER TABS (Pill Style) --}}
    <div class="flex gap-2 overflow-x-auto pb-2 hide-scrollbar border-b border-slate-200" id="statusTabs">
        <button class="f-tab bg-slate-900 text-white border-transparent px-5 py-2.5 rounded-xl text-sm font-bold whitespace-nowrap transition-all shadow-md shadow-slate-900/20" data-status="">Semua Pesanan</button>
        <button class="f-tab bg-transparent text-slate-500 hover:bg-slate-200 hover:text-slate-900 border-transparent px-5 py-2.5 rounded-xl text-sm font-bold whitespace-nowrap transition-all" data-status="menunggu_pembayaran">Belum Dibayar</button>
        <button class="f-tab bg-transparent text-slate-500 hover:bg-slate-200 hover:text-slate-900 border-transparent px-5 py-2.5 rounded-xl text-sm font-bold whitespace-nowrap transition-all" data-status="diproses">Perlu Diproses</button>
        <button class="f-tab bg-transparent text-slate-500 hover:bg-slate-200 hover:text-slate-900 border-transparent px-5 py-2.5 rounded-xl text-sm font-bold whitespace-nowrap transition-all" data-status="siap_kirim">Siap Kirim / Angkut</button>
        <button class="f-tab bg-transparent text-slate-500 hover:bg-slate-200 hover:text-slate-900 border-transparent px-5 py-2.5 rounded-xl text-sm font-bold whitespace-nowrap transition-all" data-status="dikirim">Sedang Dikirim</button>
        <button class="f-tab bg-transparent text-slate-500 hover:bg-slate-200 hover:text-slate-900 border-transparent px-5 py-2.5 rounded-xl text-sm font-bold whitespace-nowrap transition-all" data-status="sampai_tujuan">Selesai</button>
        <button class="f-tab bg-transparent text-slate-500 hover:bg-slate-200 hover:text-slate-900 border-transparent px-5 py-2.5 rounded-xl text-sm font-bold whitespace-nowrap transition-all" data-status="dibatalkan">Dibatalkan</button>
    </div>

    {{-- TOOLBAR (Search & Mass Action) --}}
    <div class="bg-white border border-slate-200 p-4 rounded-2xl shadow-sm flex flex-col lg:flex-row justify-between items-center gap-4">

        <div class="flex flex-col md:flex-row items-center gap-4 w-full lg:w-auto">
            {{-- Search Box --}}
            <div class="relative w-full md:w-80 group">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <i class="mdi mdi-magnify text-slate-400 group-focus-within:text-blue-500 transition-colors text-lg"></i>
                </div>
                <input type="text" id="orderSearchInput" class="w-full pl-11 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold text-slate-900 placeholder-slate-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:bg-white outline-none transition-all" placeholder="Cari Invoice / Nama...">
            </div>

            {{-- Source Filter --}}
            <div class="flex bg-slate-100 p-1 rounded-xl w-full md:w-auto">
                <button class="source-tab flex-1 md:flex-none px-4 py-2 rounded-lg text-xs font-bold transition-all bg-white text-slate-900 shadow-sm" data-source="all">Semua</button>
                <button class="source-tab flex-1 md:flex-none px-4 py-2 rounded-lg text-xs font-bold transition-all text-slate-500 hover:text-slate-700" data-source="online">Online</button>
                <button class="source-tab flex-1 md:flex-none px-4 py-2 rounded-lg text-xs font-bold transition-all text-slate-500 hover:text-slate-700" data-source="pos">POS</button>
            </div>
        </div>

        {{-- Mass Action --}}
        <form action="{{ route('seller.orders.massUpdate') }}" method="POST" id="mass-shipping-form" class="w-full md:w-auto">
            @csrf
            <div class="flex items-center justify-between md:justify-end gap-4 bg-slate-50 md:bg-transparent p-3 md:p-0 rounded-xl border border-slate-200 md:border-none">
                <label class="flex items-center gap-2 cursor-pointer group">
                    <input type="checkbox" id="select-all-orders" class="w-5 h-5 rounded border-slate-300 text-blue-600 focus:ring-blue-500 cursor-pointer transition-colors">
                    <span class="text-sm font-bold text-slate-700 group-hover:text-slate-900">Pilih Semua</span>
                </label>
                <button type="button" id="btn-mass-shipping" class="flex items-center gap-2 px-5 py-2.5 bg-slate-900 hover:bg-black text-white text-sm font-bold rounded-xl shadow-sm shadow-slate-900/20 disabled:opacity-50 disabled:cursor-not-allowed transition-all" disabled>
                    <i class="mdi mdi-truck-fast text-lg leading-none"></i>
                    <span>Proses Kirim (<span id="selected-count">0</span>)</span>
                </button>
            </div>
        </form>
    </div>

    {{-- AREA KONTEN PESANAN --}}
    <div id="orders-container" class="space-y-6">

        @if($groupedOrders->isEmpty())
            {{-- EMPTY STATE: Beneran Kosong dari DB --}}
            <div class="bg-white border border-slate-200 rounded-3xl py-20 px-6 text-center shadow-sm flex flex-col items-center justify-center">
                <div class="w-24 h-24 bg-slate-50 rounded-full flex items-center justify-center mb-6">
                    <i class="mdi mdi-package-variant-closed text-5xl text-slate-300"></i>
                </div>
                <h4 class="text-xl font-black text-slate-900 mb-2">Gudang Sedang Sepi</h4>
                <p class="text-sm font-medium text-slate-500 max-w-md">Belum ada pesanan yang masuk ke toko Anda. Perbanyak promosi untuk menarik pelanggan!</p>
            </div>
        @else
            {{-- EMPTY STATE DYNAMIC: Muncul saat filter tab kosong --}}
            <div id="dynamic-empty-state" class="hidden bg-white border border-slate-200 rounded-3xl py-16 px-6 text-center shadow-sm">
                <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-5">
                    <i class="mdi mdi-clipboard-search-outline text-4xl text-slate-300"></i>
                </div>
                <h5 class="text-lg font-black text-slate-900 mb-1">Tidak Ada Data</h5>
                <p class="text-sm font-medium text-slate-500" id="empty-state-text">Tidak ada pesanan pada status ini.</p>
            </div>

            @foreach($groupedOrders as $invoice => $items)
                @php
                    $sumber = $items[0]->sumber_transaksi ?? 'ONLINE';
                    $isPos = ($sumber === 'OFFLINE' || str_starts_with($invoice, 'POS-'));
                    $sourceTag = $isPos ? 'pos' : 'online';
                    
                    $headerBg = $isPos ? 'bg-orange-50/50' : 'bg-slate-50/50';
                    $accentColor = $isPos ? 'text-orange-600' : 'text-blue-600';
                    $badgeStyle = $isPos ? 'bg-orange-100 text-orange-700 border-orange-200' : 'bg-blue-100 text-blue-700 border-blue-200';
                @endphp
                {{-- KARTU ORDER (GROUP PER INVOICE) --}}
                <div class="order-group bg-white border border-slate-200 rounded-2xl shadow-sm hover:shadow-md hover:border-blue-200 transition-all duration-300 overflow-hidden" 
                     data-invoice="{{ $invoice }}" 
                     data-source="{{ $sourceTag }}">

                    {{-- Header Invoice --}}
                    <div class="{{ $headerBg }} px-6 py-4 border-b border-slate-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
                        <div class="flex flex-wrap items-center gap-4">
                            <div class="font-mono text-base font-black {{ $accentColor }} flex items-center gap-2">
                                <i class="mdi {{ $isPos ? 'mdi-cash-register' : 'mdi-receipt-text' }} text-lg"></i> {{ $invoice }}
                            </div>
                            <span class="px-2 py-0.5 rounded text-[10px] font-black uppercase tracking-tighter border {{ $badgeStyle }}">
                                {{ $isPos ? 'POS / Offline' : 'Online Store' }}
                            </span>
                            <div class="hidden sm:block w-1 h-1 bg-slate-300 rounded-full"></div>
                            <div class="text-sm font-bold text-slate-700 flex items-center gap-2">
                                <i class="mdi mdi-account-hard-hat text-slate-400 text-lg"></i> {{ $items[0]->nama_pelanggan }}
                            </div>
                        </div>
                        <div class="text-xs font-bold text-slate-500 flex items-center gap-1.5 bg-white px-3 py-1.5 rounded-lg border border-slate-200">
                            <i class="mdi mdi-calendar-clock text-slate-400"></i> {{ date('d M Y, H:i', strtotime($items[0]->tanggal_transaksi)) }} WIB
                        </div>
                    </div>

                    {{-- Isi Item Order --}}
                    <div class="divide-y divide-slate-100">
                        @foreach($items as $item)
                            @php
                                $status = $item->status_pesanan_item;
                                $badgeClass = 'bg-blue-50 text-blue-600 border-blue-200';
                                $statusText = 'Diproses';

                                if($status == 'menunggu_pembayaran') { $badgeClass = 'bg-amber-50 text-amber-600 border-amber-200'; $statusText = 'Belum Bayar'; }
                                elseif($status == 'siap_kirim') { $badgeClass = 'bg-emerald-50 text-emerald-600 border-emerald-200'; $statusText = 'Siap Angkut'; }
                                elseif($status == 'dikirim') { $badgeClass = 'bg-indigo-50 text-indigo-600 border-indigo-200'; $statusText = 'Dikirim'; }
                                elseif($status == 'sampai_tujuan') { $badgeClass = 'bg-slate-100 text-slate-600 border-slate-300'; $statusText = 'Selesai'; }
                                elseif(in_array($status, ['dibatalkan', 'ditolak'])) { $badgeClass = 'bg-red-50 text-red-600 border-red-200'; $statusText = 'Batal'; }
                            @endphp

                            <div class="order-item-row flex flex-col lg:flex-row items-start lg:items-center gap-5 p-6" data-status="{{ $status }}">

                                {{-- Checkbox --}}
                                <div class="hidden lg:flex w-8 justify-center flex-shrink-0">
                                    @if($status == 'siap_kirim')
                                        <input type="checkbox" name="detail_ids[]" value="{{ $item->detail_id }}" class="w-5 h-5 rounded border-slate-300 text-blue-600 focus:ring-blue-500 cursor-pointer order-checkbox" form="mass-shipping-form">
                                    @endif
                                </div>

                                {{-- Info Barang --}}
                                <div class="flex items-start gap-4 flex-1 min-w-[280px]">
                                    {{-- Checkbox Mobile --}}
                                    @if($status == 'siap_kirim')
                                        <input type="checkbox" name="detail_ids[]" value="{{ $item->detail_id }}" class="w-5 h-5 rounded border-slate-300 text-blue-600 focus:ring-blue-500 cursor-pointer order-checkbox lg:hidden mt-2" form="mass-shipping-form">
                                    @endif

                                    <img src="{{ asset('assets/uploads/products/' . ($item->gambar_utama ?? 'default.jpg')) }}" class="w-16 h-16 md:w-20 md:h-20 rounded-xl object-cover border border-slate-200 flex-shrink-0" alt="Material">
                                    <div>
                                        <h6 class="text-sm md:text-base font-bold text-slate-900 mb-2 leading-snug">{{ $item->nama_barang }}</h6>
                                        <span class="inline-flex items-center bg-slate-100 text-slate-600 text-xs font-bold px-2.5 py-1 rounded-md border border-slate-200">
                                            Qty: {{ $item->jumlah }}
                                        </span>
                                    </div>
                                </div>

                                {{-- Harga --}}
                                <div class="w-full lg:w-32 flex flex-row lg:flex-col justify-between lg:justify-center items-center lg:items-end">
                                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest lg:mb-1">Subtotal</span>
                                    <span class="text-base font-black text-slate-900">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                                </div>

                                {{-- Status Badge --}}
                                <div class="w-full lg:w-40 flex justify-start lg:justify-center items-center">
                                    <span class="px-3 py-1.5 rounded-lg border text-xs font-black uppercase tracking-wide {{ $badgeClass }}">
                                        {{ $statusText }}
                                    </span>
                                </div>

                                {{-- Form Aksi --}}
                                <div class="w-full lg:w-56 flex-shrink-0">
                                    @if(in_array($status, ['diproses', 'siap_kirim']))
                                        <form action="{{ route('seller.orders.updateStatus') }}" method="POST" class="flex flex-col gap-2">
                                            @csrf
                                            <input type="hidden" name="detail_id" value="{{ $item->detail_id }}">
                                            <select name="status_baru" class="w-full bg-slate-50 border border-slate-200 text-slate-700 text-sm font-bold rounded-xl px-3 py-2.5 focus:ring-2 focus:ring-blue-500 focus:outline-none cursor-pointer">
                                                <option value="diproses" {{ $status == 'diproses' ? 'selected' : '' }}>1. Siapkan Barang</option>
                                                <option value="siap_kirim" {{ $status == 'siap_kirim' ? 'selected' : '' }}>2. Siap Diangkut</option>
                                                <option value="dikirim" {{ $status == 'dikirim' ? 'selected' : '' }}>3. Kirim via Armada</option>
                                                <option value="ditolak">Tolak Pesanan (Habis)</option>
                                            </select>
                                            <button type="button" class="w-full flex items-center justify-center gap-1.5 bg-white hover:bg-blue-50 text-slate-700 hover:text-blue-700 border border-slate-200 hover:border-blue-300 text-sm font-bold rounded-xl px-3 py-2 transition-colors btn-submit-single">
                                                <i class="mdi mdi-check-circle-outline text-lg leading-none"></i> Simpan
                                            </button>
                                        </form>
                                    @else
                                        {{-- Ganti $item dengan variabel yang ada di @foreach Bos, biasanya $item atau $pesanan --}}
                                        <a href="{{ route('seller.orders.show', $item->kode_invoice) }}" class="...">
                                            <i class="mdi mdi-file-find-outline text-lg leading-none"></i> Lihat Rincian
                                        </a>
                                    @endif
                                </div>

                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {

    // --- 1. FILTER LOGIC (STATUS & SOURCE) ---
    const statusTabs = document.querySelectorAll('.f-tab');
    const sourceTabs = document.querySelectorAll('.source-tab');
    const orderGroups = document.querySelectorAll('.order-group');
    const emptyState = document.getElementById('dynamic-empty-state');
    const emptyText = document.getElementById('empty-state-text');
    const selectAllCb = document.getElementById('select-all-orders');
    const checkboxes = document.querySelectorAll('.order-checkbox');

    let currentFilterStatus = '';
    let currentFilterSource = 'all';

    function applyFilters() {
        let visibleCount = 0;

        orderGroups.forEach(group => {
            let groupSource = group.getAttribute('data-source');
            let items = group.querySelectorAll('.order-item-row');
            let groupHasVisibleItem = false;

            // Check Source first
            let sourceMatch = (currentFilterSource === 'all' || groupSource === currentFilterSource);

            items.forEach(item => {
                let itemStatus = item.getAttribute('data-status');
                
                // Item is visible if source matches AND (status is all OR status matches)
                if (sourceMatch && (currentFilterStatus === '' || itemStatus === currentFilterStatus)) {
                    item.classList.remove('hidden');
                    groupHasVisibleItem = true;
                    visibleCount++;
                } else {
                    item.classList.add('hidden');
                }
            });

            if (groupHasVisibleItem) {
                group.classList.remove('hidden');
            } else {
                group.classList.add('hidden');
            }
        });

        // Update UI Empty State
        if (emptyState) {
            if (visibleCount === 0) {
                emptyState.classList.remove('hidden');
                let statusName = document.querySelector(`.f-tab[data-status="${currentFilterStatus}"]`)?.innerText || 'Semua';
                let sourceName = document.querySelector(`.source-tab[data-source="${currentFilterSource}"]`)?.innerText || 'Semua';
                emptyText.innerHTML = `Tidak ada pesanan <strong>${sourceName}</strong> dengan status <strong>${statusName}</strong>.`;
            } else {
                emptyState.classList.add('hidden');
            }
        }

        // Reset Selection
        if(selectAllCb) selectAllCb.checked = false;
        checkboxes.forEach(cb => cb.checked = false);
        updateMassBtn();
    }

    // Status Tabs Click
    statusTabs.forEach(tab => {
        tab.addEventListener('click', function(e) {
            e.preventDefault();
            statusTabs.forEach(t => {
                t.classList.remove('bg-slate-900', 'text-white', 'shadow-md', 'shadow-slate-900/20');
                t.classList.add('bg-transparent', 'text-slate-500', 'hover:bg-slate-200', 'hover:text-slate-900');
            });
            this.classList.remove('bg-transparent', 'text-slate-500', 'hover:bg-slate-200', 'hover:text-slate-900');
            this.classList.add('bg-slate-900', 'text-white', 'shadow-md', 'shadow-slate-900/20');

            currentFilterStatus = this.getAttribute('data-status');
            applyFilters();
        });
    });

    // Source Tabs Click
    sourceTabs.forEach(tab => {
        tab.addEventListener('click', function() {
            sourceTabs.forEach(t => {
                t.classList.remove('bg-white', 'text-slate-900', 'shadow-sm');
                t.classList.add('text-slate-500', 'hover:text-slate-700');
            });
            this.classList.remove('text-slate-500', 'hover:text-slate-700');
            this.classList.add('bg-white', 'text-slate-900', 'shadow-sm');

            currentFilterSource = this.getAttribute('data-source');
            applyFilters();
        });
    });

    // Auto-klik tab berdasarkan URL parameter (?status=...)
    const currentUrlParams = new URLSearchParams(window.location.search);
    const activeStatus = currentUrlParams.get('status') || '';
    if(activeStatus) {
        let targetTab = document.querySelector(`.f-tab[data-status="${activeStatus}"]`);
        if(targetTab) targetTab.click();
    } else {
        applyFilters();
    }

    // --- 2. PENCARIAN (SEARCH) INVOICE/NAMA ---
    const searchInput = document.getElementById('orderSearchInput');
    if(searchInput) {
        searchInput.addEventListener('keyup', function() {
            let keyword = this.value.toLowerCase();
            orderGroups.forEach(group => {
                // Cari di text keseluruhan (Invoice & Nama)
                let textContent = group.textContent.toLowerCase();
                if (textContent.includes(keyword)) {
                    group.classList.remove('hidden');
                } else {
                    group.classList.add('hidden');
                }
            });
        });
    }

    // --- 3. LOGIKA CHECKBOX KIRIM MASSAL ---
    const massBtn = document.getElementById('btn-mass-shipping');
    const countSpan = document.getElementById('selected-count');

    function updateMassBtn() {
        let checkedCount = document.querySelectorAll('.order-checkbox:checked').length;
        if(countSpan) countSpan.textContent = checkedCount;
        if(massBtn) massBtn.disabled = checkedCount === 0;
    }

    if(selectAllCb) {
        selectAllCb.addEventListener('change', function() {
            // Hanya ceklis item yang TIDAK memiliki class 'hidden'
            checkboxes.forEach(cb => {
                let row = cb.closest('.order-item-row');
                if(!row.classList.contains('hidden')) {
                    cb.checked = this.checked;
                }
            });
            updateMassBtn();
        });
    }

    checkboxes.forEach(cb => {
        cb.addEventListener('change', function() {
            if(!this.checked && selectAllCb) selectAllCb.checked = false;
            updateMassBtn();
        });
    });

    // --- 4. SWEETALERT CONFIRMATION ---

    // Konfirmasi Kirim Massal
    if(massBtn) {
        massBtn.addEventListener('click', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Proses Pengiriman?',
                text: "Pastikan truk/armada sudah siap mengangkut pesanan yang dipilih.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#0f172a', // Slate 900
                cancelButtonColor: '#94a3b8',  // Slate 400
                confirmButtonText: 'Ya, Angkut!',
                cancelButtonText: 'Batal',
                customClass: { popup: 'rounded-3xl' } // UI Border radius SWAL
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('mass-shipping-form').submit();
                }
            });
        });
    }

    // Konfirmasi Update Satuan
    document.querySelectorAll('.btn-submit-single').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            let form = this.closest('form');
            let select = form.querySelector('select').options[form.querySelector('select').selectedIndex].text;

            Swal.fire({
                title: 'Update Status?',
                text: `Ubah status item ini menjadi: ${select}?`,
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#2563eb', // Blue 600
                cancelButtonColor: '#94a3b8',  // Slate 400
                confirmButtonText: 'Ya, Simpan',
                cancelButtonText: 'Batal',
                customClass: { popup: 'rounded-3xl' }
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });

});
</script>
@endpush
