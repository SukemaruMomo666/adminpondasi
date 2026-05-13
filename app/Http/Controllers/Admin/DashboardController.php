<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Tampilan Utama Dashboard Admin
     */
    public function index()
    {
        // 1. Ambil Tugas Moderasi / Antrean
        $tugas = [
            'toko_pending' => DB::table('tb_toko')->where('status', 'pending')->count(),
            'produk_pending' => DB::table('tb_barang')->where('status_moderasi', 'pending')->count(),
            'payout_pending' => DB::table('tb_payouts')->where('status', 'pending')->count(), 
            'komplain_aktif' => DB::table('tb_komplain')->whereIn('status_komplain', ['investigasi', 'menunggu_tanggapan_toko'])->count(),
        ];

        // 2. Ambil Statistik Global Platform
        $statistik = [
            'total_penjualan' => DB::table('tb_transaksi')->where('status_pesanan_global', 'selesai')->sum('total_final'),
            'total_pengguna' => DB::table('tb_user')->count(),
            'total_toko' => DB::table('tb_toko')->where('status', 'active')->count(),
            'total_produk' => DB::table('tb_barang')
                                ->where('status_moderasi', 'approved')
                                ->where('is_active', 1)
                                ->count(),
        ];

        // 3. Data Grafik (Pengguna Baru 7 Hari Terakhir)
        $chart_labels = [];
        $chart_values = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $chart_labels[] = $date->translatedFormat('D'); 
            $chart_values[] = DB::table('tb_user')->whereDate('created_at', $date)->count();
        }

        // 4. Ambil Top Performance Toko Bangunan (Limit 5 untuk Dashboard Depan)
        $queryToko = DB::table('tb_toko as t')
            ->select('t.id', 't.nama_toko', 't.logo_toko', 't.tier_toko', 't.area_id as nama_kota'); // <- PERBAIKAN: Hapus JOIN cities, ganti jadi area_id

        // Subquery: Hitung GMV (Hanya pesanan yang sudah sampai)
        $queryToko->selectSub(function ($q) {
            $q->from('tb_detail_transaksi as dt')
              ->whereColumn('dt.toko_id', 't.id')
              ->where('dt.status_pesanan_item', 'sampai_tujuan')
              ->selectRaw('COALESCE(SUM(dt.subtotal), 0)');
        }, 'total_gmv');

        // Subquery: Hitung Jumlah Transaksi Berhasil
        $queryToko->selectSub(function ($q) {
            $q->from('tb_detail_transaksi as dt')
              ->whereColumn('dt.toko_id', 't.id')
              ->where('dt.status_pesanan_item', 'sampai_tujuan')
              ->selectRaw('COUNT(dt.id)');
        }, 'total_order');

        // Subquery: Hitung Rating Rata-rata
        $queryToko->selectSub(function ($q) {
            $q->from('tb_toko_review as trv')
              ->whereColumn('trv.toko_id', 't.id')
              ->selectRaw('COALESCE(AVG(trv.rating), 0)');
        }, 'rating');

        $topToko = $queryToko->orderByDesc('total_gmv')->limit(5)->get();

        // Pastikan nama view ini sesuai dengan file Anda (biasanya 'admin.dashboard' atau 'admin.dashboard.index')
        return view('admin.dashboard', compact('tugas', 'statistik', 'chart_labels', 'chart_values', 'topToko'));
    }

    /**
     * Halaman Terpisah: Peringkat Toko Lengkap (Leaderboard)
     */
    public function topStores(Request $request)
    {
        $search = $request->search;
        $sort = $request->sort ?? 'gmv'; // Default urutkan berdasarkan penjualan tertinggi

        $query = DB::table('tb_toko as t')
            ->select('t.id', 't.nama_toko', 't.logo_toko', 't.tier_toko', 't.area_id as nama_kota'); // <- PERBAIKAN: Hapus JOIN cities, ganti jadi area_id

        // Subquery: Hitung GMV
        $query->selectSub(function ($q) {
            $q->from('tb_detail_transaksi as dt')
              ->whereColumn('dt.toko_id', 't.id')
              ->where('dt.status_pesanan_item', 'sampai_tujuan')
              ->selectRaw('COALESCE(SUM(dt.subtotal), 0)');
        }, 'total_gmv');

        // Subquery: Hitung Transaksi
        $query->selectSub(function ($q) {
            $q->from('tb_detail_transaksi as dt')
              ->whereColumn('dt.toko_id', 't.id')
              ->where('dt.status_pesanan_item', 'sampai_tujuan')
              ->selectRaw('COUNT(dt.id)');
        }, 'total_order');

        // Subquery: Hitung Rating
        $query->selectSub(function ($q) {
            $q->from('tb_toko_review as trv')
              ->whereColumn('trv.toko_id', 't.id')
              ->selectRaw('COALESCE(AVG(trv.rating), 0)');
        }, 'rating');

        // Fitur Pencarian
        if ($search) {
            $query->where('t.nama_toko', 'like', "%{$search}%");
        }

        // Fitur Sorting (Urutkan)
        if ($sort == 'gmv') {
            $query->orderByDesc('total_gmv');
        } elseif ($sort == 'order') {
            $query->orderByDesc('total_order');
        } elseif ($sort == 'rating') {
            $query->orderByDesc('rating');
        }

        // Pagination 15 data per halaman
        $topToko = $query->paginate(15)->withQueryString();

        return view('admin.dashboard.top_stores', compact('topToko', 'search', 'sort'));
    }
}