<?php

namespace App\Http\Controllers\Api\Mobile;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;


class TransactionController extends Controller
{
    public function userOrders(Request $request)
    {
        $userId = $request->user()->id;

        // Mengambil histori transaksi/pesanan berdasarkan user_id
        $orders = DB::table('tb_detail_transaksi as dt')
            ->join('tb_transaksi as t', 'dt.transaksi_id', '=', 't.id')
            ->join('tb_barang as b', 'dt.barang_id', '=', 'b.id')
            ->join('tb_toko as tk', 'b.toko_id', '=', 'tk.id')
            ->select(
                't.id',
                't.invoice_no', // nomor invoice jika ada
                'tk.nama_toko',
                't.status_pesanan', // dikemas, dikirim, selesai, dll
                'b.nama_barang',
                'b.harga',
                'dt.jumlah as qty',
                't.total_harga',
                'b.gambar_utama'
            )
            ->where('t.user_id', $userId)
            ->orderBy('t.created_at', 'DESC')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $orders
        ], 200);
    }   
}
