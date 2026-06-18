@extends('layouts.seller')

@section('title', 'Rincian Pesanan - Pondasikita')

@section('content')
<div class="p-6">
    
    {{-- Header Section --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
        <div>
            <div class="flex items-center gap-3 mb-1">
                <a href="{{ route('seller.orders.index') }}" class="w-8 h-8 rounded-full bg-white border border-gray-200 flex items-center justify-center text-gray-600 hover:bg-gray-50 transition-colors">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <h1 class="text-2xl font-black text-gray-900 tracking-tight">Rincian Pesanan</h1>
            </div>
            <p class="text-sm text-gray-500 font-medium ml-11">
                No. Invoice: <span class="text-blue-600 font-bold uppercase">{{ $transaksi->kode_invoice }}</span> 
                &bull; {{ \Carbon\Carbon::parse($transaksi->tanggal_transaksi)->format('d M Y, H:i') }}
            </p>
        </div>

        {{-- Status Badge (Global) --}}
        <div>
            @php
                $statusColors = [
                    'paid' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
                    'pending' => 'bg-amber-100 text-amber-700 border-amber-200',
                    'failed' => 'bg-red-100 text-red-700 border-red-200',
                ];
                $colorClass = $statusColors[$transaksi->status_pembayaran] ?? 'bg-gray-100 text-gray-700 border-gray-200';
            @endphp
            <span class="px-4 py-2 rounded-xl text-xs font-black uppercase tracking-widest border {{ $colorClass }}">
                Pembayaran: {{ $transaksi->status_pembayaran }}
            </span>
        </div>
    </div>

    {{-- Main Grid Content --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- KIRI: DAFTAR BARANG YANG DIBELI (Kolom Besar) --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                    <h3 class="text-sm font-black text-gray-800 uppercase tracking-wide">Daftar Produk</h3>
                </div>
                
                <div class="p-6 space-y-6">
                    @foreach($detailItems as $item)
                    <div class="flex flex-col sm:flex-row gap-4 items-start pb-6 border-b border-gray-100 last:border-0 last:pb-0">
                        {{-- Gambar Produk --}}
                        <div class="w-20 h-20 shrink-0 bg-gray-100 rounded-xl border border-gray-200 overflow-hidden flex items-center justify-center">
                            @if($item->gambar_utama)
                                <img src="{{ asset('assets/uploads/products/' . $item->gambar_utama) }}" alt="Produk" class="w-full h-full object-cover">
                            @else
                                <i class="fas fa-box text-gray-400 text-2xl"></i>
                            @endif
                        </div>

                        {{-- Info Produk --}}
                        <div class="flex-1 w-full">
                            <div class="flex flex-col sm:flex-row justify-between items-start gap-2">
                                <div>
                                    <h4 class="font-bold text-gray-900 text-base leading-tight">{{ $item->nama_barang_saat_transaksi }}</h4>
                                    <p class="text-xs text-gray-500 font-mono mt-1">SKU: {{ $item->kode_barang ?? '-' }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="font-black text-gray-900">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</p>
                                    <p class="text-xs text-gray-500 font-medium">{{ $item->jumlah }} x Rp {{ number_format($item->harga_saat_transaksi, 0, ',', '.') }}</p>
                                </div>
                            </div>

                            {{-- Status Pengiriman Item & Metode --}}
                            <div class="mt-4 flex flex-wrap gap-2">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-gray-100 text-gray-700 text-[11px] font-bold">
                                    <i class="fas fa-truck text-gray-500"></i> {{ str_replace('_', ' ', $item->metode_pengiriman) }}
                                </span>
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-blue-50 text-blue-700 text-[11px] font-bold uppercase tracking-wider border border-blue-100">
                                    <i class="fas fa-info-circle"></i> Status: {{ str_replace('_', ' ', $item->status_pesanan_item) }}
                                </span>
                            </div>
                            
                            @if($item->catatan_pembeli)
                            <div class="mt-3 p-3 bg-amber-50 border border-amber-100 rounded-lg text-xs text-amber-800 font-medium">
                                <span class="font-bold">Catatan Pembeli:</span> "{{ $item->catatan_pembeli }}"
                            </div>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- KANAN: INFORMASI PELANGGAN & TOTAL (Kolom Kecil) --}}
        <div class="space-y-6">
            
            {{-- Box Info Pelanggan --}}
            <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-6 relative overflow-hidden">
                <div class="absolute top-0 right-0 w-24 h-24 bg-blue-50 rounded-bl-full -z-10"></div>
                <h3 class="text-sm font-black text-gray-800 uppercase tracking-wide mb-4"><i class="fas fa-user-circle text-blue-500 mr-2"></i> Info Pelanggan</h3>
                
                <div class="space-y-3">
                    <div>
                        <p class="text-[10px] font-bold text-gray-400 uppercase">Nama Penerima</p>
                        <p class="font-semibold text-gray-900 text-sm">{{ $transaksi->shipping_nama_penerima ?? $transaksi->nama_akun ?? 'Pelanggan Walk-In' }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-gray-400 uppercase">Nomor Telepon</p>
                        <p class="font-semibold text-gray-900 text-sm">{{ $transaksi->shipping_telepon_penerima ?? $transaksi->telp_akun ?? '-' }}</p>
                    </div>
                    @php
                        $isPos = ($transaksi->sumber_transaksi === 'OFFLINE' || str_starts_with($transaksi->kode_invoice, 'POS-'));
                    @endphp

                    @if(!$isPos)
                    <div>
                        <p class="text-[10px] font-bold text-gray-400 uppercase">Alamat Pengiriman</p>
                        <p class="font-medium text-gray-700 text-xs leading-relaxed mt-0.5">
                            {{ $transaksi->shipping_alamat_lengkap ?? 'Diambil di Toko / POS' }}<br>
                            @if($transaksi->shipping_kecamatan)
                                Kec. {{ $transaksi->shipping_kecamatan }}, {{ $transaksi->shipping_kota_kabupaten }}<br>
                                {{ $transaksi->shipping_provinsi }} - {{ $transaksi->shipping_kode_pos }}
                            @endif
                        </p>
                    </div>
                    @else
                    <div>
                        <p class="text-[10px] font-bold text-gray-400 uppercase">Metode Transaksi</p>
                        <span class="inline-flex mt-1 px-2 py-1 rounded bg-orange-500 text-white text-[10px] font-bold uppercase tracking-wider">
                            POINT OF SALE (KASIR)
                        </span>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Box Rincian Pembayaran --}}
            <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-6">
                <h3 class="text-sm font-black text-gray-800 uppercase tracking-wide mb-4"><i class="fas fa-receipt text-emerald-500 mr-2"></i> Rincian Pendapatan</h3>
                
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-500 font-medium">Total Harga Produk</span>
                        <span class="font-semibold text-gray-900">Rp {{ number_format($totalBelanjaToko, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-500 font-medium">Ongkos Kirim (Toko)</span>
                        <span class="font-semibold text-gray-900">Rp {{ number_format($totalOngkirToko, 0, ',', '.') }}</span>
                    </div>
                    
                    <div class="border-t border-dashed border-gray-200 pt-3 mt-3">
                        <div class="flex justify-between items-center">
                            <span class="font-black text-gray-900">Total Pendapatan</span>
                            <span class="font-black text-blue-600 text-lg">Rp {{ number_format($grandTotalToko, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                @if($transaksi->metode_pembayaran)
                <div class="mt-4 pt-4 border-t border-gray-100">
                    <p class="text-[10px] font-bold text-gray-400 uppercase mb-1">Metode Pembayaran</p>
                    <p class="font-semibold text-gray-800 text-sm mb-2"><i class="fas fa-wallet text-gray-400 mr-1.5"></i> {{ $transaksi->metode_pembayaran }}</p>

                    @if($transaksi->tipe_pembayaran == 'DP')
                        <div class="bg-orange-50 border border-orange-200 rounded-xl p-4 mt-3">
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-xs font-bold text-orange-700"><i class="fas fa-handshake mr-1"></i> Sistem B2B Uang Muka (DP)</span>
                                @if($transaksi->sisa_tagihan > 0)
                                    <span class="px-2 py-0.5 bg-orange-200 text-orange-800 text-[10px] font-black uppercase rounded">Belum Lunas</span>
                                @else
                                    <span class="px-2 py-0.5 bg-emerald-200 text-emerald-800 text-[10px] font-black uppercase rounded"><i class="fas fa-check"></i> Lunas</span>
                                @endif
                            </div>
                            <div class="flex justify-between items-center text-sm mb-1">
                                <span class="text-gray-600">Nominal DP Dibayar</span>
                                <span class="font-bold text-emerald-600">Rp {{ number_format($transaksi->jumlah_dp, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between items-center text-sm mb-3 border-b border-orange-200 pb-2">
                                <span class="text-gray-600 font-bold">Sisa Pelunasan (Tunai)</span>
                                <span class="font-black text-rose-600">Rp {{ number_format($transaksi->sisa_tagihan, 0, ',', '.') }}</span>
                            </div>
                            
                            @if($transaksi->sisa_tagihan > 0)
                                <form action="{{ route('seller.orders.pelunasan_dp') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="transaksi_id" value="{{ $transaksi->id }}">
                                    <button type="submit" class="w-full bg-orange-500 hover:bg-orange-600 text-white font-bold py-2 rounded-lg text-sm transition-colors" onclick="return confirm('Apakah Anda yakin pembeli sudah membayar lunas uang sisa tagihan secara tunai/offline?')">
                                        <i class="fas fa-check-double mr-1"></i> Konfirmasi Pelunasan & Selesai
                                    </button>
                                </form>
                                <p class="text-[10px] text-gray-500 mt-2 leading-tight text-center">Sistem akan mencatat User ID kasir dan Timestamp untuk audit pelunasan.</p>
                            @else
                                <div class="text-xs text-emerald-600 font-medium text-center">
                                    <i class="fas fa-info-circle"></i> Pelunasan telah diselesaikan.
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
                @endif
            </div>

        </div>
    </div>
</div>
@endsection