<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; 
use Illuminate\Support\Facades\Auth;
use Midtrans\Config; 
use Midtrans\Snap;   

class TransactionController extends Controller
{
    // ==========================================================
    // 1. FUNGSI MENAMPILKAN RIWAYAT PESANAN (KODEMU)
    // ==========================================================
    public function userOrders(Request $request)
    {
        $userId = $request->user()->id;

        $orders = DB::table('tb_detail_transaksi as dt')
            ->join('tb_transaksi as t', 'dt.transaksi_id', '=', 't.id')
            ->join('tb_barang as b', 'dt.barang_id', '=', 'b.id')
            ->join('tb_toko as tk', 'b.toko_id', '=', 'tk.id')
            ->select(
                't.id',
                't.kode_invoice as invoice_no', // Disesuaikan dengan nama kolom DB web kamu
                'tk.nama_toko',
                't.status_pesanan_global as status_pesanan', 
                'b.nama_barang',
                'b.harga',
                'dt.jumlah as qty',
                't.total_final as total_harga',
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

    // ==========================================================
    // 2. FUNGSI BARU: GET DATA CHECKOUT (UNTUK REACT NATIVE)
    // ==========================================================
    public function getCheckoutData(Request $request)
    {
        $user = Auth::guard('sanctum')->user();
        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 401);
        }

        $itemsPerToko = [];
        $totalProduk = 0;
        $isDirectPurchase = $request->has('direct_purchase') && $request->direct_purchase == '1';

        if ($isDirectPurchase) {
            $productId = $request->input('product_id');
            $jumlah = $request->input('jumlah', 1);

            $item = DB::table('tb_barang as b')
                ->join('tb_toko as t', 'b.toko_id', '=', 't.id')
                ->select(
                    'b.id as barang_id', 'b.nama_barang', 'b.harga', 'b.gambar_utama', 'b.stok', 
                    't.id as toko_id', 't.nama_toko', 't.kota as kota_toko',
                    't.area_id as origin_area_id', 't.active_api_couriers'
                )
                ->where('b.id', $productId)
                ->first();

            if ($item) {
                $item->jumlah = $jumlah;
                $weight = 1000 * $jumlah; 

                $itemsPerToko[] = [
                    'toko_id'         => $item->toko_id,
                    'nama_toko'       => $item->nama_toko,
                    'origin_area_id'  => $item->origin_area_id,
                    'active_couriers' => $item->active_api_couriers,
                    'weight'          => $weight,
                    'subtotal'        => $item->harga * $jumlah,
                    'items'           => [$item]
                ];
                $totalProduk += $item->harga * $jumlah;
            }
        } else {
            // ALUR DARI KERANJANG - KUNCI PERBAIKAN BACKEND
            $rawSelectedItems = $request->input('selected_items');
            
            $selectedItems = [];
            if (is_array($rawSelectedItems)) {
                $selectedItems = $rawSelectedItems;
            } elseif (is_string($rawSelectedItems) && trim($rawSelectedItems) !== '') {
                $selectedItems = explode(',', $rawSelectedItems);
            } elseif (!empty($rawSelectedItems)) {
                $selectedItems = [$rawSelectedItems];
            }

            // Jika setelah dikonversi tetap kosong, return log info untuk debug
            if (empty($selectedItems)) {
                return response()->json([
                    'status' => 'error', 
                    'message' => 'Tidak ada barang yang dipilih atau format selected_items tidak valid.',
                    'debug_input' => $rawSelectedItems
                ], 400);
            }

            $items = DB::table('tb_keranjang as k')
                ->join('tb_barang as b', 'k.barang_id', '=', 'b.id')
                ->join('tb_toko as t', 'b.toko_id', '=', 't.id')
                ->select(
                    'k.id as keranjang_id', 'b.id as barang_id', 'b.nama_barang', 'b.harga', 'b.gambar_utama', 'k.jumlah', 
                    't.id as toko_id', 't.nama_toko', 't.kota as kota_toko',
                    't.area_id as origin_area_id', 't.active_api_couriers'
                )
                ->where('k.user_id', $user->id)
                ->whereIn('k.id', $selectedItems)
                ->get();

            $tempStore = [];
            foreach ($items as $item) {
                if (!isset($tempStore[$item->toko_id])) {
                    $tempStore[$item->toko_id] = [
                        'toko_id'         => $item->toko_id,
                        'nama_toko'       => $item->nama_toko,
                        'origin_area_id'  => $item->origin_area_id,
                        'active_couriers' => $item->active_api_couriers,
                        'weight'          => 0,
                        'subtotal'        => 0,
                        'items'           => []
                    ];
                }
                $tempStore[$item->toko_id]['items'][] = $item;
                $tempStore[$item->toko_id]['subtotal'] += ($item->harga * $item->jumlah);
                $tempStore[$item->toko_id]['weight'] += (1000 * $item->jumlah); 
                
                $totalProduk += ($item->harga * $item->jumlah);
            }
            $itemsPerToko = array_values($tempStore); 
        }

        return response()->json([
            'status'       => 'success',
            'total_produk' => $totalProduk,
            'stores'       => $itemsPerToko
        ], 200);
    }

    // ==========================================================
    // 3. FUNGSI PROSES CHECKOUT (UPGRADE SETARA WEB)
    // ==========================================================
    public function checkout(Request $request)
    {
        $user = Auth::guard('sanctum')->user();
        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'Sesi berakhir, silakan login ulang.'], 401);
        }
        
        DB::beginTransaction();
        try {
            $orderId = 'INV-' . time() . '-' . rand(100, 999);
            $totalProdukReal = 0;
            $itemsToProcess = [];

            // A. PENGHITUNGAN PRODUK
            if ($request->has('direct_purchase') && $request->direct_purchase == '1') {
                $produk = DB::table('tb_barang')->where('id', $request->input('product_id'))->first();
                if ($produk) {
                    $qty = $request->input('jumlah', 1);
                    $subtotal = $produk->harga * $qty;
                    $totalProdukReal += $subtotal;
                    $itemsToProcess[] = [
                        'toko_id' => $produk->toko_id, 'barang_id' => $produk->id,
                        'nama_barang' => $produk->nama_barang, 'harga' => $produk->harga,
                        'jumlah' => $qty, 'subtotal' => $subtotal
                    ];
                }
            } else {
                $rawItems = $request->input('selected_items');
                $selectedIds = is_array($rawItems) ? $rawItems : explode(',', $rawItems);
                $keranjangs = DB::table('tb_keranjang')
                    ->join('tb_barang', 'tb_keranjang.barang_id', '=', 'tb_barang.id')
                    ->whereIn('tb_keranjang.id', $selectedIds)
                    ->where('tb_keranjang.user_id', $user->id)
                    ->select('tb_keranjang.*', 'tb_barang.toko_id', 'tb_barang.harga', 'tb_barang.nama_barang')
                    ->get();

                foreach ($keranjangs as $item) {
                    $subtotal = $item->harga * $item->jumlah;
                    $totalProdukReal += $subtotal;
                    $itemsToProcess[] = [
                        'toko_id' => $item->toko_id, 'barang_id' => $item->barang_id,
                        'nama_barang' => $item->nama_barang, 'harga' => $item->harga,
                        'jumlah' => $item->jumlah, 'subtotal' => $subtotal
                    ];
                }
            }

            // B. PENENTUAN ALAMAT
            $alamat = [
                'label' => 'Alamat Pengiriman', 'nama' => $user->nama, 'telepon' => $user->no_telepon,
                'alamat_lengkap' => '', 'kecamatan' => '', 'kota' => '', 'provinsi' => '', 'kode_pos' => ''
            ];

            if ($request->input('address_type') === 'saved') {
                $alamatUser = DB::table('tb_user_alamat')->where('user_id', $user->id)->where('is_utama', 1)->first();
                if ($alamatUser) {
                    $alamat['label'] = $alamatUser->label_alamat ?? 'Alamat Utama';
                    $alamat['nama'] = $alamatUser->nama_penerima;
                    $alamat['telepon'] = $alamatUser->telepon_penerima;
                    $alamat['alamat_lengkap'] = $alamatUser->alamat_lengkap;
                }
            } else {
                $alamat['label'] = 'Alamat Manual';
                $alamat['nama'] = $request->input('manual_nama') ?? $user->nama;
                $alamat['telepon'] = $request->input('manual_telepon') ?? $user->no_telepon;
                $alamat['alamat_lengkap'] = $request->input('manual_alamat') ?? '';
                $alamat['kecamatan'] = $request->input('manual_kecamatan') ?? '';
                $alamat['kota'] = $request->input('manual_kota') ?? '';
            }

            // C. KALKULASI ONGKIR DARI REQUEST REACT NATIVE
            $biayaPengirimanReal = 0;
            $rincianKurir = [];
            $couriers = $request->input('couriers', []); // format array JSON dari HP

            if ($request->input('tipe_pengambilan') === 'kurir' && !empty($couriers)) {
                foreach ($couriers as $tokoId => $shippingVal) {
                    if (!empty($shippingVal)) {
                        $parts = explode('_', $shippingVal);
                        $hargaOngkir = (int) end($parts);
                        $biayaPengirimanReal += $hargaOngkir;
                        
                        $namaKurir = str_replace("_" . $hargaOngkir, "", $shippingVal);
                        $rincianKurir[] = "Toko-$tokoId: " . strtoupper($namaKurir);
                    }
                }
            }

            // D. GRAND TOTAL & INSERT KE DB
            $grandTotalReal = $totalProdukReal + $biayaPengirimanReal;
            $catatanFinal = "Pesan Via Mobile App";
            if (!empty($rincianKurir)) {
                $catatanFinal .= " | Kurir: " . implode(', ', $rincianKurir);
            }

            $transaksiId = DB::table('tb_transaksi')->insertGetId([
                'kode_invoice'              => $orderId,
                'sumber_transaksi'          => 'ONLINE',
                'user_id'                   => $user->id,
                'total_harga_produk'        => $totalProdukReal,
                'total_diskon'              => 0,
                'total_final'               => $grandTotalReal,
                'tipe_pembayaran'           => 'LUNAS',
                'status_pembayaran'         => 'pending',
                'status_pesanan_global'     => 'menunggu_pembayaran',
                
                'shipping_label_alamat'     => $alamat['label'],
                'shipping_nama_penerima'    => $alamat['nama'],
                'shipping_telepon_penerima' => $alamat['telepon'],
                'shipping_alamat_lengkap'   => $alamat['alamat_lengkap'],
                'shipping_kecamatan'        => $alamat['kecamatan'],
                'shipping_kota_kabupaten'   => $alamat['kota'],
                
                'catatan'                   => $catatanFinal,
                'biaya_pengiriman'          => $biayaPengirimanReal,
                'tipe_pengambilan'          => $request->input('tipe_pengambilan') ?? 'kurir',
                'tanggal_transaksi'         => now(),
                'created_at'                => now(),
                'updated_at'                => now(),
            ]);

            foreach ($itemsToProcess as $item) {
                DB::table('tb_detail_transaksi')->insert([
                    'transaksi_id'               => $transaksiId,
                    'toko_id'                    => $item['toko_id'],
                    'barang_id'                  => $item['barang_id'],
                    'nama_barang_saat_transaksi' => $item['nama_barang'],
                    'harga_saat_transaksi'       => $item['harga'],
                    'jumlah'                     => $item['jumlah'],
                    'subtotal'                   => $item['subtotal']
                ]);
            }

            // Hapus isi keranjang jika bukan beli langsung
            if (!$request->has('direct_purchase')) {
                DB::table('tb_keranjang')->where('user_id', $user->id)->whereIn('id', $selectedIds)->delete();
            }

            // E. GENERATE MIDTRANS SNAP TOKEN DARI DATABASE (Sama seperti Web)
            $settings = DB::table('tb_pengaturan')->whereIn('setting_nama', ['midtrans_server_key', 'midtrans_is_production'])->pluck('setting_nilai', 'setting_nama');
            Config::$serverKey = $settings['midtrans_server_key'] ?? env('MIDTRANS_SERVER_KEY');
            Config::$isProduction = ($settings['midtrans_is_production'] ?? '0') == '1';
            Config::$isSanitized = true;
            Config::$is3ds = true;

            $snapToken = Snap::getSnapToken([
                'transaction_details' => ['order_id' => $orderId, 'gross_amount' => (int) $grandTotalReal],
                'customer_details' => [
                    'first_name' => $alamat['nama'],
                    'email'      => $user->email,
                    'phone'      => $alamat['telepon'],
                ]
            ]);

            // Update Snap Token ke Tabel
            DB::table('tb_transaksi')->where('id', $transaksiId)->update(['snap_token' => $snapToken]);

            DB::commit();

            // Tentukan URL berdasarkan environment
            $baseUrl = Config::$isProduction ? 'https://app.midtrans.com/snap/v2/vtweb/' : 'https://app.sandbox.midtrans.com/snap/v2/vtweb/';

            return response()->json([
                'status' => 'success',
                'kode_invoice' => $orderId,
                'redirect_url' => $baseUrl . $snapToken
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack(); 
            return response()->json([
                'status' => 'error',
                'message' => 'Sistem Error: ' . $e->getMessage()
            ], 500);
        }
    }

    // ==========================================================
    // 4. API CEK ONGKIR BITESHIP (UNTUK REACT NATIVE)
    // ==========================================================
    public function cekOngkir(Request $request)
    {
        try {
            $apiKey = DB::table('tb_pengaturan')->where('setting_nama', 'biteship_api_key')->value('setting_nilai');
            
            if (empty($apiKey)) {
                return response()->json(['success' => false, 'error' => 'API Key Biteship belum disetting.']);
            }

            $originAreaId = $request->query('origin');
            $destinationAreaId = $request->query('destination');
            $weight = $request->query('weight', 1000);
            $couriers = $request->query('couriers', 'jne'); 

            if (empty($originAreaId) || empty($destinationAreaId)) {
                return response()->json(['success' => false, 'error' => 'Origin atau Destination Area ID tidak valid/kosong.']);
            }

            $response = \Illuminate\Support\Facades\Http::withHeaders([
                'Authorization' => $apiKey,
                'Content-Type'  => 'application/json'
            ])->post('https://api.biteship.com/v1/rates/couriers', [
                'origin_area_id' => $originAreaId,
                'destination_area_id' => $destinationAreaId,
                'couriers' => $couriers, 
                'items' => [
                    [
                        'name' => 'Barang PondasiKita',
                        'description' => 'Paket Material',
                        'value' => 50000,
                        'weight' => (int) $weight,
                        'quantity' => 1
                    ]
                ]
            ]);

            return $response->json();

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => 'Koneksi API gagal: ' . $e->getMessage()], 500);
        }
    }
}