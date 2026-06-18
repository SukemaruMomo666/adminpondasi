@extends('layouts.seller')

@section('title', 'Penilaian & Kepuasan Pelanggan')

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

    {{-- 1. HEADER --}}
    <div class="flex items-center gap-4 mb-6">
        <div class="w-12 h-12 bg-white border border-slate-200 rounded-2xl flex items-center justify-center text-amber-500 shadow-sm flex-shrink-0">
            <i class="mdi mdi-star-circle-outline text-2xl"></i>
        </div>
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Penilaian & Ulasan Pelanggan</h1>
            <p class="text-sm font-medium text-slate-500 mt-0.5">Tinjau kepuasan pembeli dan berikan respons profesional untuk menjaga reputasi toko.</p>
        </div>
    </div>

    {{-- 2. SUMMARY ANALYTICS CARD --}}
    <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-sm grid grid-cols-1 lg:grid-cols-12 gap-8 items-center relative overflow-hidden">

        {{-- Nilai Rata-Rata --}}
        <div class="lg:col-span-3 flex flex-col items-center justify-center text-center lg:border-r border-slate-200 lg:pr-8 relative z-10">
            <div class="text-6xl font-black text-slate-900 leading-none mb-2">
                {{ number_format($summary->avg_rating ?? 0, 1) }}<span class="text-2xl text-slate-400 font-bold">/5.0</span>
            </div>
            <div class="flex items-center gap-1 text-amber-400 text-2xl mb-2">
                @for($i=1; $i<=5; $i++)
                    <i class="mdi mdi-star{{ $i <= round($summary->avg_rating ?? 0) ? '' : '-outline' }}"></i>
                @endfor
            </div>
            <div class="text-[11px] font-black text-slate-400 uppercase tracking-widest">{{ number_format($summary->total_reviews ?? 0) }} Penilaian Diterima</div>
        </div>

        {{-- Breakdown Progress Bars --}}
        <div class="lg:col-span-5 flex flex-col justify-center gap-3 lg:border-r border-slate-200 lg:pr-8 relative z-10">
            @php $totalRevs = $summary->total_reviews > 0 ? $summary->total_reviews : 1; @endphp
            @for($i=5; $i>=1; $i--)
                @php
                    $count = $ratingCounts[$i] ?? 0;
                    $percent = ($count / $totalRevs) * 100;
                @endphp
                <div class="flex items-center gap-3 text-sm font-bold text-slate-500">
                    <div class="flex items-center gap-1 w-12 justify-end">{{ $i }} <i class="mdi mdi-star text-amber-400 text-lg leading-none"></i></div>
                    <div class="flex-1 h-2 bg-slate-100 rounded-full overflow-hidden">
                        <div class="h-full bg-amber-400 rounded-full transition-all duration-1000" style="width: {{ $percent }}%;"></div>
                    </div>
                    <div class="w-10 text-right">{{ number_format($count) }}</div>
                </div>
            @endfor
        </div>

        {{-- Metrics Lanjutan --}}
        <div class="lg:col-span-4 grid grid-cols-2 gap-6 relative z-10">
            <div>
                <span class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Respons Chat</span>
                <span class="text-xl font-black text-emerald-500">{{ $performa['chat_response_rate'] }}</span>
            </div>
            <div>
                <span class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Waktu Respons</span>
                <span class="text-xl font-black text-slate-800">{{ $performa['chat_response_time'] }}</span>
            </div>
            <div>
                <span class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Tingkat Batal</span>
                <span class="text-xl font-black text-red-500">{{ $performa['cancellation_rate'] }}</span>
            </div>
            <div>
                <span class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Keterlambatan</span>
                <span class="text-xl font-black text-slate-800">{{ $performa['late_shipment_rate'] }}</span>
            </div>
        </div>

        {{-- Watermark Latar --}}
        <i class="mdi mdi-chart-box-outline absolute -bottom-10 -right-10 text-9xl text-slate-50 pointer-events-none z-0"></i>
    </div>

    {{-- 3. TABS FILTER BINTANG --}}
    <div class="flex gap-4 overflow-x-auto pb-4 hide-scrollbar">
        <a href="{{ route('seller.service.reviews') }}" class="px-5 py-2.5 rounded-xl text-sm font-bold whitespace-nowrap transition-all shadow-sm {{ $starFilter == 'all' ? 'bg-slate-900 text-white shadow-slate-900/20' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">Semua Ulasan</a>
        @for($i=5; $i>=1; $i--)
            <a href="{{ route('seller.service.reviews', ['star' => $i]) }}" class="px-5 py-2.5 rounded-xl text-sm font-bold whitespace-nowrap transition-all shadow-sm flex items-center gap-1.5 {{ $starFilter == $i ? 'bg-slate-900 text-white shadow-slate-900/20' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                {{ $i }} <i class="mdi mdi-star {{ $starFilter == $i ? 'text-amber-400' : 'text-slate-400' }}"></i>
            </a>
        @endfor
    </div>

    {{-- 4. DAFTAR ULASAN --}}
    <div class="bg-white border border-slate-200 rounded-3xl overflow-hidden shadow-sm">
        @if ($reviews->count() > 0)
            <div class="divide-y divide-slate-100">
                @foreach($reviews as $review)
                    <div class="p-6 flex flex-col sm:flex-row gap-5 hover:bg-slate-50/50 transition-colors">

                        {{-- Avatar Pembeli --}}
                        <div class="w-12 h-12 rounded-full bg-blue-50 text-blue-600 border border-blue-100 flex items-center justify-center font-black text-lg flex-shrink-0 uppercase">
                            {{ substr($review->nama_user, 0, 1) }}
                        </div>

                        {{-- Konten Ulasan --}}
                        <div class="flex-1 min-w-0">

                            {{-- Header Ulasan --}}
                            <div class="flex flex-wrap justify-between items-center mb-1.5 gap-2">
                                <h4 class="text-base font-black text-slate-900 truncate">{{ $review->nama_user }}</h4>
                                <span class="text-xs font-bold text-slate-400"><i class="mdi mdi-calendar-clock"></i> {{ \Carbon\Carbon::parse($review->created_at)->format('d M Y, H:i') }} WIB</span>
                            </div>

                            {{-- Bintang --}}
                            <div class="flex gap-1 text-base mb-3">
                                @for($i=1; $i<=5; $i++)
                                    <i class="mdi mdi-star {{ $i > $review->rating ? 'text-slate-200' : 'text-amber-400' }}"></i>
                                @endfor
                            </div>

                            {{-- Teks Ulasan --}}
                            @if(!empty($review->ulasan))
                                <div class="text-sm font-medium text-slate-700 leading-relaxed mb-4">"{!! nl2br(e($review->ulasan)) !!}"</div>
                            @else
                                <div class="text-sm font-medium text-slate-400 italic mb-4">Pembeli tidak meninggalkan ulasan tertulis.</div>
                            @endif

                            {{-- Gambar Ulasan --}}
                            @if(!empty($review->gambar_ulasan))
                                <div class="mb-4">
                                    <img src="{{ asset('assets/uploads/reviews/' . $review->gambar_ulasan) }}" alt="Foto Ulasan" class="w-24 h-24 object-cover rounded-xl border border-slate-200 shadow-sm cursor-zoom-in" onclick="window.open(this.src, '_blank')">
                                </div>
                            @endif

                            {{-- Produk yang Dibeli --}}
                            @if(!empty($review->nama_barang))
                                <div class="inline-flex items-center gap-3 bg-slate-50 border border-slate-200 p-2 pr-4 rounded-xl mb-4">
                                    <img src="{{ asset('assets/uploads/products/' . ($review->gambar_barang ?? 'default.jpg')) }}" class="w-10 h-10 object-cover rounded-lg border border-slate-200 bg-white" onerror="this.src='https://placehold.co/50'">
                                    <div class="flex flex-col">
                                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Varian Produk</span>
                                        <span class="text-xs font-bold text-slate-700 truncate max-w-[200px]">{{ $review->nama_barang }}</span>
                                    </div>
                                </div>
                            @endif

                            {{-- Balasan Penjual --}}
                            @if(!empty($review->balasan_penjual))
                                <div class="bg-blue-50/50 border-l-4 border-l-blue-600 p-4 rounded-r-xl mt-2">
                                    <div class="text-[11px] font-black text-blue-600 uppercase tracking-widest flex items-center gap-1.5 mb-2">
                                        <i class="mdi mdi-store"></i> Balasan Toko Anda
                                    </div>
                                    <p class="text-sm font-medium text-slate-700 leading-relaxed m-0">{!! nl2br(e($review->balasan_penjual)) !!}</p>
                                </div>
                            @else
                                <form action="{{ route('seller.service.reviews.reply') }}" method="POST" class="mt-2 relative">
                                    @csrf
                                    <input type="hidden" name="review_id" value="{{ $review->id }}">
                                    <textarea name="balasan" class="w-full bg-slate-50 border border-slate-200 text-slate-900 text-sm font-medium rounded-2xl p-4 pb-14 focus:bg-white focus:ring-2 focus:ring-blue-600 outline-none transition-all placeholder-slate-400 resize-none min-h-[100px]" placeholder="Tulis balasan publik yang sopan untuk ulasan ini..." required></textarea>
                                    <div class="absolute bottom-3 right-3">
                                        <button type="button" class="btn-submit-reply flex items-center gap-1.5 px-4 py-2 bg-slate-900 hover:bg-black text-white text-xs font-bold rounded-xl shadow-sm shadow-slate-900/20 transition-all">
                                            <i class="mdi mdi-send text-base leading-none"></i> Kirim Balasan
                                        </button>
                                    </div>
                                </form>
                            @endif

                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Pagination --}}
            @if($reviews->hasPages())
                <div class="p-6 bg-slate-50 border-t border-slate-100 flex justify-center lg:justify-end">
                    {{ $reviews->appends(request()->query())->links('pagination::bootstrap-5') }}
                </div>
            @endif

        @else
            <div class="py-20 flex flex-col items-center justify-center opacity-60 text-center">
                <i class="mdi mdi-star-off-outline text-6xl text-slate-300 mb-4"></i>
                <h4 class="text-xl font-black text-slate-800 mb-1">Belum Ada Penilaian</h4>
                <p class="text-sm font-medium text-slate-500">Tidak ada ulasan pembeli yang sesuai dengan filter saat ini.</p>
            </div>
        @endif
    </div>

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {

    // Konfirmasi SweetAlert sebelum mengirim balasan
    document.querySelectorAll('.btn-submit-reply').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            let form = this.closest('form');
            let text = form.querySelector('textarea').value.trim();

            if(text === '') {
                Swal.fire({title: 'Peringatan', text: 'Balasan tidak boleh kosong!', icon: 'warning', customClass: { popup: 'rounded-3xl' }});
                return;
            }

            Swal.fire({
                title: 'Publikasikan Balasan?',
                text: "Balasan Anda akan terlihat oleh publik di halaman produk. Pastikan bahasa yang digunakan sopan dan profesional.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#0f172a',
                cancelButtonColor: '#94a3b8',
                confirmButtonText: 'Ya, Publikasikan',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                customClass: { popup: 'rounded-3xl' }
            }).then((result) => {
                if (result.isConfirmed) {
                    btn.innerHTML = '<i class="mdi mdi-loading mdi-spin text-base leading-none"></i> Mengirim...';
                    btn.disabled = true;
                    btn.classList.add('opacity-70', 'cursor-not-allowed');
                    form.submit();
                }
            });
        });
    });

});
</script>
@endpush
