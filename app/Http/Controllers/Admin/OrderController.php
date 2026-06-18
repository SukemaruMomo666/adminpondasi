<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Menampilkan Dasbor Global Orders (Multi-Vendor & B2B Logic)
     */
    public function index(Request $request)
    {
        $status = $request->get('status', 'semua');
        $sumber = $request->get('sumber', 'semua');
        $search = $request->get('search');

        // 1. Statistik Live berdasarkan detail transaksi
        $stats = [
            'total' => DB::table('tb_detail_transaksi')->count(),
            'perlu_dikirim' => DB::table('tb_detail_transaksi')->whereIn('status_pesanan_item', ['diproses', 'siap_kirim'])->count(),
            'sedang_dikirim' => DB::table('tb_detail_transaksi')->where('status_pesanan_item', 'dikirim')->count(),
            'komplain' => DB::table('tb_transaksi')->where('status_pesanan_global', 'komplain')->count(),
        ];

        // 2. Query Utama: Join Multi-Vendor dengan Logika DP B2B
        $query = DB::table('tb_detail_transaksi as dt')
            ->join('tb_transaksi as trx', 'dt.transaksi_id', '=', 'trx.id')
            ->join('tb_toko as t', 'dt.toko_id', '=', 't.id')
            ->join('tb_user as u', 'trx.user_id', '=', 'u.id')
            ->select(
                'dt.id',
                'trx.id as transaksi_id',
                'trx.kode_invoice',
                'trx.tanggal_transaksi',
                'trx.status_pembayaran',
                'trx.sumber_transaksi',
                'dt.status_pesanan_item as status_pesanan',
                't.nama_toko',
                'u.nama as nama_pembeli',
                'u.email as email_pembeli',
                'dt.kurir_terpilih as kurir_pengiriman',
                'dt.metode_pengiriman',
                'dt.resi_pengiriman as nomor_resi',

                // Mendukung Sistem DP B2B
                'trx.tipe_pembayaran',
                'trx.jumlah_dp',
                'trx.sisa_tagihan',

                // Total nilai per-toko (Subtotal barang + Ongkir khusus item ini)
                DB::raw('(dt.subtotal + dt.biaya_pengiriman_item) as total_final')
            );

        // Filter Tab Status
        if ($status !== 'semua') {
            $mapStatus = [
                'pending'  => 'menunggu_pembayaran',
                'diproses' => 'diproses',
                'dikirim'  => 'dikirim',
                'selesai'  => 'selesai',
                'komplain' => 'komplain'
            ];

            if (isset($mapStatus[$status])) {
                $query->where('dt.status_pesanan_item', $mapStatus[$status]);
            }
        }

        // Filter Sumber Transaksi (ONLINE / OFFLINE)
        if ($sumber !== 'semua') {
            if ($sumber === 'offline') {
                $query->where(function($q) {
                    $q->where('trx.sumber_transaksi', 'OFFLINE')
                      ->orWhere('trx.kode_invoice', 'LIKE', 'POS-%');
                });
            } else {
                // Online
                $query->where('trx.sumber_transaksi', 'ONLINE')
                      ->where('trx.kode_invoice', 'NOT LIKE', 'POS-%');
            }
        }

        // Pencarian dinamis
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('trx.kode_invoice', 'LIKE', "%$search%")
                  ->orWhere('u.nama', 'LIKE', "%$search%")
                  ->orWhere('t.nama_toko', 'LIKE', "%$search%");
            });
        }

        $orders = $query->latest('trx.tanggal_transaksi')->paginate(15)->withQueryString();

        return view('admin.orders.index', compact('orders', 'status', 'sumber', 'search', 'stats'));
    }

    /**
     * Menampilkan Detail Pesanan yang Sangat Terperinci (Mewah)
     */
    public function show($id)
    {
        // 1. Ambil data utama pesanan (Item spesifik dari toko tertentu)
        $order = DB::table('tb_detail_transaksi as dt')
            ->join('tb_transaksi as trx', 'dt.transaksi_id', '=', 'trx.id')
            ->join('tb_toko as t', 'dt.toko_id', '=', 't.id')
            ->join('tb_user as u', 'trx.user_id', '=', 'u.id')
            ->select(
                'dt.*',
                'trx.kode_invoice', 'trx.tanggal_transaksi', 'trx.status_pembayaran',
                'trx.tipe_pembayaran', 'trx.jumlah_dp', 'trx.sisa_tagihan',
                'trx.metode_pembayaran', 'trx.customer_service_fee', 'trx.customer_handling_fee',
                'trx.shipping_nama_penerima', 'trx.shipping_label_alamat', 'trx.shipping_telepon_penerima',
                'trx.shipping_alamat_lengkap', 'trx.shipping_kecamatan', 'trx.shipping_kota_kabupaten',
                'trx.shipping_provinsi', 'trx.shipping_kode_pos', 'trx.tipe_pengambilan', 'trx.sumber_transaksi',
                'trx.catatan as catatan_global', 'trx.bayar', 'trx.kembali',
                't.nama_toko', 't.telepon_toko as telp_toko', // <-- Bug "no_telepon" diperbaiki
                'u.nama as nama_pembeli', 'u.email as email_pembeli'
            )
            ->where('dt.id', $id)
            ->first();

        if (!$order) {
            return redirect()->route('admin.orders.index')->with('error', 'Detail Pesanan tidak ditemukan.');
        }

        // 2. Ambil list produk apa saja yang dibeli di toko tersebut dalam 1 nomor invoice yang sama
        $items = DB::table('tb_detail_transaksi as dt')
            ->leftJoin('tb_barang as b', 'dt.barang_id', '=', 'b.id')
            ->where('dt.transaksi_id', $order->transaksi_id)
            ->where('dt.toko_id', $order->toko_id)
            ->select(
                'dt.nama_barang_saat_transaksi as nama_barang',
                'dt.harga_saat_transaksi as harga_saat_ini',
                'dt.jumlah as jumlah_item',
                'dt.subtotal',
                'dt.biaya_pengiriman_item',
                'b.gambar_utama as foto_barang' // <-- Bug foto_barang diperbaiki
            )
            ->get();

        // 3. Kalkulasi ulang Total & Ongkir dari semua item pesanan dari toko ini
        $order->subtotal = $items->sum('subtotal');
        // Jika 1 paket, kita ambil ongkir paling besar (atau sum() jika logikanya per item)
        $order->biaya_pengiriman_item = $items->max('biaya_pengiriman_item');

        return view('admin.orders.show', compact('order', 'items'));
    }
}
