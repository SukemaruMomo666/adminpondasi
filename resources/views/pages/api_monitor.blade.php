@extends('layouts.admin')
@section('title', 'API & System Monitor')

@section('content')
<div class="space-y-6 animate-fade-in-up">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-2xl font-black text-white tracking-tight flex items-center gap-3 drop-shadow-md">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-purple-500 to-indigo-600 flex items-center justify-center shadow-lg shadow-purple-500/30">
                    <i class="fas fa-satellite-dish text-white text-lg"></i>
                </div>
                Monitor API & Sistem
            </h1>
            <p class="text-zinc-400 text-sm mt-1">Pantau status koneksi, limit (quota), dan kesehatan API pihak ketiga.</p>
        </div>
        <button onclick="window.location.reload()" class="bg-zinc-800 hover:bg-indigo-600 text-white px-5 py-2.5 rounded-xl text-sm font-bold transition-all shadow-lg hover:shadow-indigo-500/25 flex items-center gap-2 border border-zinc-700">
            <i class="fas fa-sync-alt"></i> Refresh Status
        </button>
    </div>

    {{-- Keterangan POTA --}}
    <div class="bg-indigo-900/30 border border-indigo-500/30 rounded-2xl p-4 flex gap-4 shadow-lg shadow-indigo-900/20">
        <div class="w-10 h-10 rounded-full bg-indigo-500/20 flex items-center justify-center shrink-0">
            <i class="fas fa-info-circle text-indigo-400 text-lg"></i>
        </div>
        <div>
            <h4 class="text-indigo-300 font-bold text-sm mb-1">Catatan Limitasi POTA (Gemini API)</h4>
            <p class="text-indigo-200/80 text-xs leading-relaxed">
                Google tidak menyediakan indikator persentase sisa kuota. Sistem ini melacak limitasi dengan melakukan PING jaringan. 
                Jika indikator berwarna <span class="text-red-400 font-bold">Merah (Limit)</span>, itu berarti Google menolak request (Error 429 Too Many Requests) karena batas wajar telah terlampaui.
            </p>
        </div>
    </div>

    {{-- Grid API Status --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        @foreach($apis as $api)
            <div class="bg-zinc-900/80 backdrop-blur-xl border border-zinc-800 rounded-2xl p-6 relative overflow-hidden group hover:border-zinc-700 transition-colors">
                
                {{-- Efek Glow --}}
                <div class="absolute -inset-24 bg-gradient-to-br {{ $api['status'] == 'active' ? 'from-emerald-500/10' : ($api['status'] == 'limit' ? 'from-red-500/10' : 'from-yellow-500/10') }} opacity-0 group-hover:opacity-100 transition-opacity blur-2xl"></div>

                <div class="relative flex justify-between items-start mb-6">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl {{ $api['type'] == 'ai' ? 'bg-indigo-500/20 text-indigo-400' : ($api['type'] == 'websocket' ? 'bg-blue-500/20 text-blue-400' : 'bg-emerald-500/20 text-emerald-400') }} flex items-center justify-center text-xl border border-white/5">
                            @if($api['type'] == 'ai')
                                <i class="fas fa-robot"></i>
                            @elseif($api['type'] == 'websocket')
                                <i class="fas fa-bolt"></i>
                            @else
                                <i class="fas fa-truck"></i>
                            @endif
                        </div>
                        <div>
                            <h3 class="text-white font-bold text-lg leading-tight">{{ $api['name'] }}</h3>
                            <div class="flex items-center gap-2 mt-1">
                                <span class="text-zinc-500 text-xs font-mono bg-zinc-950 px-2 py-0.5 rounded-md border border-zinc-800">{{ $api['key'] }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- Status Badge --}}
                    @if($api['status'] == 'active')
                        <div class="bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 px-3 py-1 rounded-full text-xs font-bold flex items-center gap-1.5 shadow-[0_0_10px_rgba(16,185,129,0.1)]">
                            <div class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></div>
                            {{ $api['message'] }}
                        </div>
                    @elseif($api['status'] == 'limit')
                        <div class="bg-red-500/10 border border-red-500/30 text-red-400 px-3 py-1 rounded-full text-xs font-bold flex items-center gap-1.5 shadow-[0_0_10px_rgba(239,68,68,0.1)]">
                            <i class="fas fa-exclamation-triangle"></i>
                            {{ $api['message'] }}
                        </div>
                    @else
                        <div class="bg-yellow-500/10 border border-yellow-500/30 text-yellow-400 px-3 py-1 rounded-full text-xs font-bold flex items-center gap-1.5 shadow-[0_0_10px_rgba(234,179,8,0.1)]">
                            <i class="fas fa-times-circle"></i>
                            {{ $api['message'] }}
                        </div>
                    @endif
                </div>

                {{-- Metrics --}}
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-zinc-950/50 rounded-xl p-4 border border-zinc-800/50">
                        <div class="text-zinc-400 text-xs mb-1">Response Time (Ping)</div>
                        <div class="text-white font-mono font-bold">
                            @if($api['latency'] > 0)
                                <span class="{{ $api['latency'] > 1000 ? 'text-yellow-400' : 'text-emerald-400' }}">{{ $api['latency'] }}</span> ms
                            @else
                                <span class="text-zinc-500">-</span>
                            @endif
                        </div>
                    </div>
                    <div class="bg-zinc-950/50 rounded-xl p-4 border border-zinc-800/50">
                        <div class="text-zinc-400 text-xs mb-1">Status Koneksi</div>
                        <div class="text-white font-bold text-sm flex items-center gap-2">
                            @if($api['status'] == 'active')
                                <i class="fas fa-check-circle text-emerald-400"></i> Terhubung
                            @elseif($api['status'] == 'limit')
                                <i class="fas fa-ban text-red-400"></i> Diblokir Sementara
                            @else
                                <i class="fas fa-unlink text-zinc-500"></i> Gagal Tersambung
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endforeach

        @if(empty($apis))
            <div class="col-span-full py-12 flex flex-col items-center justify-center bg-zinc-900/50 rounded-3xl border border-zinc-800 border-dashed">
                <i class="fas fa-box-open text-4xl text-zinc-600 mb-4"></i>
                <h3 class="text-lg font-bold text-white">Belum Ada API Terdaftar</h3>
                <p class="text-zinc-400 text-sm mt-1">Tambahkan kredensial POTA atau modul lain di konfigurasi environment Anda.</p>
            </div>
        @endif
    </div>
</div>
@endsection
