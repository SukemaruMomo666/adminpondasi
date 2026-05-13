<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class PageController extends Controller
{
    // =================================================================
    // 1. HALAMAN DAFTAR PRODUK (Katalog Utama Lengkap dengan Filter)
    // =================================================================
    public function produk(Request $request)
    {
        $categories = DB::table('tb_kategori')->orderBy('nama_kategori', 'ASC')->get();

        $locations = DB::table('tb_toko as t')
            ->join('cities as c', 't.city_id', '=', 'c.id')
            ->select('t.city_id', 'c.name as nama_kota')
            ->where('t.status', 'active')
            ->whereNotNull('t.city_id')
            ->distinct()
            ->orderBy('c.name', 'ASC')
            ->get();

        // TAMBAHKAN 't.tier_toko' DI SINI AGAR BADGE MUNCUL
        $query = DB::table('tb_barang as b')
            ->join('tb_toko as t', 'b.toko_id', '=', 't.id')
            ->leftJoin('cities as c', 't.city_id', '=', 'c.id')
            ->select(
                'b.id', 'b.nama_barang', 'b.harga', 'b.gambar_utama', 'b.satuan_unit',
                't.nama_toko', 't.slug as toko_slug', 't.tier_toko', 'c.name as nama_kota'
            )
            ->where('b.is_active', 1)
            ->where('b.status_moderasi', 'approved')
            ->where('t.status', 'active');

        // FILTER 1: Kategori Utama
        $raw_kategori = $request->kategori;
        $filter_kategori = is_array($raw_kategori) ? $raw_kategori : (!empty($raw_kategori) ? [$raw_kategori] : []);
        if (!empty($filter_kategori)) {
            $query->whereIn('b.kategori_id', $filter_kategori);
        }

        // FILTER 2: Kategori Text (Accordion)
        if ($request->has('kategori_text') && is_array($request->kategori_text)) {
            $query->where(function($q) use ($request) {
                foreach ($request->kategori_text as $text) {
                    $q->orWhere('b.nama_barang', 'LIKE', '%' . $text . '%');
                }
            });
        }

        // FILTER 3: Jenis Mitra (Official / Pro)
        if ($request->has('tier_toko') && is_array($request->tier_toko)) {
            $query->whereIn('t.tier_toko', $request->tier_toko);
        }

        // FILTER 4: Lokasi & Harga
        if ($request->filled('lokasi')) {
            $query->where('t.city_id', $request->lokasi);
        }
        if ($request->filled('harga_min')) {
            $query->where('b.harga', '>=', $request->harga_min);
        }
        if ($request->filled('harga_max')) {
            $query->where('b.harga', '<=', $request->harga_max);
        }
        if ($request->filled('query')) {
            $keyword = '%' . $request->query('query') . '%';
            $query->where(function($q) use ($keyword) {
                $q->where('b.nama_barang', 'like', $keyword)
                  ->orWhere('t.nama_toko', 'like', $keyword);
            });
        }

        // SORTING (Paling Baru, Termurah, Termahal)
        if ($request->filled('sort')) {
            if ($request->sort == 'termurah') {
                $query->orderBy('b.harga', 'ASC');
            } elseif ($request->sort == 'termahal') {
                $query->orderBy('b.harga', 'DESC');
            } else {
                $query->orderBy('b.created_at', 'DESC');
            }
        } else {
            $query->orderBy('b.created_at', 'DESC');
        }

        $products = $query->paginate(12)->withQueryString();

        $filter_lokasi = $request->lokasi ?? '';
        $filter_harga_min = $request->harga_min ?? '';
        $filter_harga_max = $request->harga_max ?? '';

        return view('pages.produk', compact(
            'categories', 'locations', 'products',
            'filter_kategori', 'filter_lokasi', 'filter_harga_min', 'filter_harga_max'
        ));
    }

    // =================================================================
    // 2. HALAMAN HASIL PENCARIAN (Search Bar & Filter Kategori)
    // =================================================================
    public function search(Request $request)
    {
        $keyword = $request->input('query');
        $kategoriId = $request->input('kategori');

        $categories = DB::table('tb_kategori')->orderBy('nama_kategori', 'ASC')->get();

        // TAMBAHKAN 't.tier_toko' DI SINI AGAR BADGE MUNCUL
        $query = DB::table('tb_barang as b')
            ->join('tb_toko as t', 'b.toko_id', '=', 't.id')
            ->leftJoin('cities as c', 't.city_id', '=', 'c.id')
            ->select('b.id', 'b.nama_barang', 'b.harga', 'b.gambar_utama', 't.nama_toko', 't.slug as slug_toko', 't.tier_toko', 'c.name as kota_toko')
            ->where('b.is_active', 1)
            ->where('b.status_moderasi', 'approved')
            ->where('t.status', 'active');

        if (!empty($keyword)) {
            $query->where(function($q) use ($keyword) {
                $q->where('b.nama_barang', 'like', '%' . $keyword . '%')
                  ->orWhere('t.nama_toko', 'like', '%' . $keyword . '%');
            });
        }

        if (!empty($kategoriId)) {
            $query->where('b.kategori_id', $kategoriId);
        }

        // FILTER JENIS MITRA UNTUK HALAMAN SEARCH
        if ($request->has('tier_toko') && is_array($request->tier_toko)) {
            $query->whereIn('t.tier_toko', $request->tier_toko);
        }

        $products = $query->paginate(12)->appends($request->query());

        return view('pages.search', compact('products', 'categories', 'keyword', 'kategoriId'));
    }

    // =================================================================
    // 3. HALAMAN DETAIL PRODUK
    // =================================================================
    public function detailProduk(Request $request)
    {
        $id = $request->query('id');

        $produk = DB::table('tb_barang as b')
            ->join('tb_toko as t', 'b.toko_id', '=', 't.id')
            ->leftJoin('cities as c', 't.city_id', '=', 'c.id')
            ->select('b.*', 't.nama_toko', 't.slug as toko_slug', 'c.name as kota_toko', 't.logo_toko')
            ->where('b.id', $id)
            ->first();

        if (!$produk) {
            return redirect()->route('produk.index')->with('error', 'Produk tidak ditemukan.');
        }

        $ulasan = DB::table('tb_review_produk as r')
            ->join('tb_user as u', 'r.user_id', '=', 'u.id')
            ->select('r.*', 'u.nama as nama_user')
            ->where('r.barang_id', $id)
            ->orderByDesc('r.created_at')
            ->limit(5)
            ->get();

        return view('pages.detail_produk', compact('produk', 'ulasan'));
    }

    // =================================================================
    // 4. HALAMAN SEMUA TOKO
    // =================================================================
    public function semuaToko(Request $request)
    {
        $filter_lokasi = $request->query('lokasi', 'semua');

        $locations = DB::table('tb_toko as t')
            ->join('cities as c', 't.city_id', '=', 'c.id')
            ->select('t.city_id', 'c.name as city_name')
            ->where('t.status', 'active')
            ->whereNotNull('t.city_id')
            ->distinct()
            ->orderBy('c.name', 'ASC')
            ->get();

$query = DB::table('tb_toko as t')
            ->join('cities as c', 't.city_id', '=', 'c.id')
            ->select(
                't.id', 't.nama_toko', 't.slug', 't.deskripsi_toko',
                't.logo_toko', 't.banner_toko', 't.tier_toko', 't.city_id', 'c.name as city_name' // <--- Tambahkan t.tier_toko di sini
            )
            ->selectSub(function ($q) {
                $q->from('tb_barang')
                  ->whereColumn('toko_id', 't.id')
                  ->where('is_active', 1)
                  ->where('status_moderasi', 'approved')
                  ->selectRaw('COUNT(id)');
            }, 'jumlah_produk')
            ->selectSub(function ($q) {
                $q->from('tb_toko_review')
                  ->whereColumn('toko_id', 't.id')
                  ->selectRaw('COALESCE(AVG(rating), 0)');
            }, 'rating')
            ->where('t.status', 'active');

        if ($filter_lokasi !== 'semua' && !empty($filter_lokasi)) {
            $query->where('t.city_id', $filter_lokasi);
        }

        $query->orderBy('t.nama_toko', 'ASC');
        $stores = $query->paginate(12)->withQueryString();

        return view('pages.semua_toko', compact('locations', 'stores', 'filter_lokasi'));
    }

    // =================================================================
    // 5. HALAMAN PROFIL TOKO (Katalog Toko)
    // =================================================================
    public function detailToko(Request $request)
    {
        $slug = $request->query('slug');

        $toko = DB::table('tb_toko as t')
            ->leftJoin('cities as c', 't.city_id', '=', 'c.id')
            ->select('t.*', 'c.name as kota')
            ->where('t.slug', $slug)
            ->where('t.status', 'active')
            ->first();

        if (!$toko) {
            abort(404, 'Toko tidak ditemukan atau sedang tidak aktif.');
        }

        $colors = ['#e53935', '#d81b60', '#8e24aa', '#5e35b1', '#3949ab', '#1e88e5', '#039be5', '#00acc1', '#00897b', '#43a047', '#7cb342', '#c0ca33', '#fdd835', '#ffb300', '#fb8c00', '#f4511e'];
        $storeColor = $colors[crc32($toko->nama_toko) % count($colors)];

        $words = explode(" ", $toko->nama_toko);
        $acronym = "";
        foreach ($words as $w) { $acronym .= mb_substr($w, 0, 1); }
        $storeInitials = strtoupper(substr($acronym, 0, 2));
        if (empty($storeInitials)) { $storeInitials = "TK"; }

        $products = DB::table('tb_barang as b')
            ->where('b.toko_id', $toko->id)
            ->where('b.is_active', 1)
            ->where('b.status_moderasi', 'approved')
            ->paginate(12);

        return view('pages.detail_toko', compact('toko', 'products', 'storeColor', 'storeInitials'));
    }

    // =================================================================
    // 6. HALAMAN KERANJANG
    // =================================================================
    public function keranjang()
    {
        if (!Auth::check()) {
            return view('pages.keranjang', ['is_guest' => true]);
        }

        $cartItems = DB::table('tb_keranjang as k')
            ->join('tb_barang as b', 'k.barang_id', '=', 'b.id')
            ->join('tb_toko as t', 'b.toko_id', '=', 't.id')
            ->select(
                'k.id as cart_id', 'k.jumlah',
                'b.id as barang_id', 'b.nama_barang', 'b.harga', 'b.gambar_utama', 'b.stok',
                't.nama_toko', 't.id as toko_id'
            )
            ->where('k.user_id', Auth::id())
            ->orderBy('t.nama_toko', 'ASC')
            ->get();

        $groupedCart = $cartItems->groupBy('nama_toko');

        return view('pages.keranjang', compact('groupedCart', 'cartItems'));
    }

    // =================================================================
    // 7. API KERANJANG (TAMBAH, UPDATE, HAPUS)
    // =================================================================
    public function tambahKeranjang(Request $request)
    {
        if (!Auth::check()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Silakan masuk (login) terlebih dahulu untuk menambah barang ke keranjang.'
            ], 401);
        }

        $userId = Auth::id();
        $barangId = $request->barang_id;
        $jumlah = $request->jumlah ?? 1;

        $existing = DB::table('tb_keranjang')
            ->where('user_id', $userId)
            ->where('barang_id', $barangId)
            ->first();

        if ($existing) {
            DB::table('tb_keranjang')->where('id', $existing->id)->update(['jumlah' => $existing->jumlah + $jumlah]);
        } else {
            DB::table('tb_keranjang')->insert([
                'user_id' => $userId,
                'barang_id' => $barangId,
                'jumlah' => $jumlah,
            ]);
        }

        return response()->json(['status' => 'success', 'message' => 'Barang berhasil ditambahkan!']);
    }

    public function updateKeranjang(Request $request)
    {
        if (!Auth::check()) return response()->json(['status' => 'error'], 401);

        DB::table('tb_keranjang')
            ->where('id', $request->cart_id)
            ->where('user_id', Auth::id())
            ->update(['jumlah' => $request->jumlah]);

        return response()->json(['status' => 'success']);
    }

    public function hapusKeranjang(Request $request)
    {
        if (!Auth::check()) return response()->json(['status' => 'error'], 401);

        DB::table('tb_keranjang')
            ->where('id', $request->cart_id)
            ->where('user_id', Auth::id())
            ->delete();

        return response()->json(['status' => 'success']);
    }

    // =================================================================
    // 8. HALAMAN CHECKOUT (SUDAH FIX count() ERROR)
    // =================================================================
    public function checkout(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Silakan masuk untuk melanjutkan checkout.');
        }

        $userId = Auth::id();
        $userEmail = Auth::user()->email ?? 'customer@example.com';

        $alamatUser = DB::table('tb_user_alamat as ua')
            ->leftJoin('provinces as p', 'ua.province_id', '=', 'p.id')
            ->leftJoin('cities as c', 'ua.city_id', '=', 'c.id')
            ->leftJoin('districts as d', 'ua.district_id', '=', 'd.id')
            ->select('ua.*', 'p.name as province_name', 'c.name as city_name', 'd.name as district_name')
            ->where('ua.user_id', $userId)
            ->where('ua.is_utama', 1)
            ->first();

        $isAlamatIncomplete = !$alamatUser || empty($alamatUser->nama_penerima) || empty($alamatUser->alamat_lengkap);

        $addressData = null;
        if ($alamatUser) {
            $addressData = [
                'label'     => $alamatUser->label_alamat ?? 'Alamat Utama',
                'nama'      => $alamatUser->nama_penerima ?? '',
                'telepon'   => $alamatUser->telepon_penerima ?? '',
                'alamat'    => $alamatUser->alamat_lengkap ?? '',
                'kecamatan' => $alamatUser->district_name ?? '',
                'kota'      => $alamatUser->city_name ?? '',
                'provinsi'  => $alamatUser->province_name ?? '',
                'kodepos'   => $alamatUser->kode_pos ?? ''
            ];
        }

        $itemsPerToko = [];
        $itemArray = [];
        $totalProduk = 0;
        $isDirectPurchase = $request->has('product_id');

        if ($isDirectPurchase) {
            $productId = $request->input('product_id');
            $jumlah = $request->input('jumlah', 1);

            $item = DB::table('tb_barang as b')
                ->join('tb_toko as t', 'b.toko_id', '=', 't.id')
                ->leftJoin('cities as c', 't.city_id', '=', 'c.id')
                ->select('b.id as barang_id', 'b.nama_barang', 'b.harga', 'b.gambar_utama', 'b.stok', 't.id as toko_id', 't.nama_toko', 'c.name as kota_toko')
                ->where('b.id', $productId)
                ->first();

            if ($item) {
                $item->jumlah = $jumlah;
                $itemArray[] = $item;
                $itemsPerToko[$item->toko_id] = [
                    'nama_toko' => $item->nama_toko, 'kota_toko' => $item->kota_toko, 'items' => [$item]
                ];
                $totalProduk += $item->harga * $jumlah;
            }
        } else {
            $rawSelectedItems = $request->input('selected_items') ?? session('selected_items');

            // PAKSA MENJADI ARRAY AGAR TIDAK ERROR "COUNT()"
            $selectedItems = [];
            if (is_array($rawSelectedItems)) {
                $selectedItems = $rawSelectedItems;
            } elseif (is_string($rawSelectedItems)) {
                $selectedItems = explode(',', $rawSelectedItems);
            } elseif (!empty($rawSelectedItems)) {
                $selectedItems = [$rawSelectedItems];
            }

            if (empty($selectedItems)) {
                return redirect()->route('keranjang.index')->with('error', 'Tidak ada barang yang dipilih untuk checkout.');
            }

            $items = DB::table('tb_keranjang as k')
                ->join('tb_barang as b', 'k.barang_id', '=', 'b.id')
                ->join('tb_toko as t', 'b.toko_id', '=', 't.id')
                ->leftJoin('cities as c', 't.city_id', '=', 'c.id')
                ->select('k.id as keranjang_id', 'b.id as barang_id', 'b.nama_barang', 'b.harga', 'b.gambar_utama', 'k.jumlah', 't.id as toko_id', 't.nama_toko', 'c.name as kota_toko')
                ->where('k.user_id', $userId)
                ->whereIn('k.id', $selectedItems)
                ->get();

            foreach ($items as $item) {
                $itemArray[] = $item;
                if (!isset($itemsPerToko[$item->toko_id])) {
                    $itemsPerToko[$item->toko_id] = [
                        'nama_toko' => $item->nama_toko, 'kota_toko' => $item->kota_toko, 'items' => []
                    ];
                }
                $itemsPerToko[$item->toko_id]['items'][] = $item;
                $totalProduk += $item->harga * $item->jumlah;
            }
        }

        if (empty($itemsPerToko)) {
            return redirect()->route('keranjang.index')->with('error', 'Data produk tidak valid atau keranjang kosong.');
        }

        return view('pages.checkout', compact('userEmail', 'alamatUser', 'addressData', 'isAlamatIncomplete', 'itemsPerToko', 'itemArray', 'totalProduk', 'isDirectPurchase', 'request'));
    }

    // =================================================================
    // 9. PROSES CHECKOUT (ALUR REDIRECT)
    // =================================================================
    public function prosesCheckout(Request $request)
    {
        try {
            $user = Auth::user();
            $grandTotal = $request->input('grand_total');
            $orderId = 'INV-' . time() . '-' . rand(100, 999);

            $biayaPengiriman = $grandTotal - $request->input('total_produk_subtotal');

            // 1. Simpan Transaksi Induk
            $transaksiId = DB::table('tb_transaksi')->insertGetId([
                'kode_invoice'              => $orderId,
                'sumber_transaksi'          => 'ONLINE',
                'user_id'                   => $user->id,
                'total_harga_produk'        => $request->input('total_produk_subtotal'),
                'total_diskon'              => 0,
                'total_final'               => $grandTotal,
                'tipe_pembayaran'           => 'LUNAS',
                'status_pembayaran'         => 'pending',
                'status_pesanan_global'     => 'menunggu_pembayaran',
                'shipping_label_alamat'     => $request->input('shipping_label_alamat'),
                'shipping_nama_penerima'    => $request->input('shipping_nama_penerima'),
                'shipping_telepon_penerima' => $request->input('shipping_telepon_penerima'),
                'shipping_alamat_lengkap'   => $request->input('shipping_alamat_lengkap'),
                'shipping_kecamatan'        => $request->input('shipping_kecamatan'),
                'shipping_kota_kabupaten'   => $request->input('shipping_kota_kabupaten'),
                'shipping_provinsi'         => $request->input('shipping_provinsi'),
                'shipping_kode_pos'         => $request->input('shipping_kode_pos'),
                'catatan'                   => $request->input('catatan'),
                'biaya_pengiriman'          => $biayaPengiriman,
                'tipe_pengambilan'          => $request->input('tipe_pengambilan') ?? 'pengiriman',
                'tanggal_transaksi'         => now()
            ]);

            // 2. Simpan Rincian Barang
            if ($request->has('direct_purchase')) {
                $produk = DB::table('tb_barang')->where('id', $request->input('product_id'))->first();
                if ($produk) {
                    DB::table('tb_detail_transaksi')->insert([
                        'transaksi_id'               => $transaksiId,
                        'toko_id'                    => $produk->toko_id,
                        'barang_id'                  => $produk->id,
                        'nama_barang_saat_transaksi' => $produk->nama_barang,
                        'harga_saat_transaksi'       => $produk->harga,
                        'jumlah'                     => $request->input('jumlah', 1),
                        'subtotal'                   => $produk->harga * $request->input('jumlah', 1)
                    ]);
                }
            } else {
                $selectedIds = is_array($request->selected_items) ? $request->selected_items : explode(',', $request->selected_items);
                $keranjangs = DB::table('tb_keranjang')
                    ->join('tb_barang', 'tb_keranjang.barang_id', '=', 'tb_barang.id')
                    ->whereIn('tb_keranjang.id', $selectedIds)
                    ->select('tb_keranjang.*', 'tb_barang.toko_id', 'tb_barang.harga', 'tb_barang.nama_barang')
                    ->get();

                foreach ($keranjangs as $item) {
                    DB::table('tb_detail_transaksi')->insert([
                        'transaksi_id'               => $transaksiId,
                        'toko_id'                    => $item->toko_id,
                        'barang_id'                  => $item->barang_id,
                        'nama_barang_saat_transaksi' => $item->nama_barang,
                        'harga_saat_transaksi'       => $item->harga,
                        'jumlah'                     => $item->jumlah,
                        'subtotal'                   => $item->harga * $item->jumlah
                    ]);
                }
                DB::table('tb_keranjang')->where('user_id', $user->id)->whereIn('id', $selectedIds)->delete();
            }

            // 3. Midtrans Logic
            $settings = DB::table('tb_pengaturan')->whereIn('setting_nama', ['midtrans_server_key', 'midtrans_is_production'])->pluck('setting_nilai', 'setting_nama');
            \Midtrans\Config::$serverKey = $settings['midtrans_server_key'] ?? '';
            \Midtrans\Config::$isProduction = ($settings['midtrans_is_production'] ?? '0') == '1';
            \Midtrans\Config::$isSanitized = true;
            \Midtrans\Config::$is3ds = true;

            $snapToken = \Midtrans\Snap::getSnapToken([
                'transaction_details' => ['order_id' => $orderId, 'gross_amount' => (int) $grandTotal],
                'customer_details' => [
                    'first_name' => $request->input('shipping_nama_penerima') ?? $user->nama,
                    'email' => $user->email,
                    'phone' => $request->input('shipping_telepon_penerima') ?? $user->no_telepon,
                ]
            ]);

            DB::table('tb_transaksi')->where('id', $transaksiId)->update(['snap_token' => $snapToken]);

            return response()->json(['status' => 'success', 'kode_invoice' => $orderId]);

        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Kesalahan: ' . $e->getMessage()], 500);
        }
    }

    // =================================================================
    // 10. RIWAYAT PESANAN SAYA
    // =================================================================
    public function pesanan()
    {
        if (!Auth::check()) return redirect()->route('login');

        $orders = DB::table('tb_transaksi')
            ->where('user_id', Auth::id())
            ->orderBy('tanggal_transaksi', 'desc')
            ->get();

        return view('pages.pesanan_index', compact('orders'));
    }

    // =================================================================
    // 11. DETAIL & LACAK PENGIRIMAN
    // =================================================================
    public function lacakPesanan($kode_invoice)
    {
        if (!Auth::check()) return redirect()->route('login');

        $order = DB::table('tb_transaksi')
            ->where('kode_invoice', $kode_invoice)
            ->where('user_id', Auth::id())
            ->first();

        if (!$order) { abort(404, 'Pesanan tidak ditemukan.'); }

        $items = DB::table('tb_detail_transaksi as dt')
            ->leftJoin('tb_barang as b', 'dt.barang_id', '=', 'b.id')
            ->select('dt.*', 'b.gambar_utama')
            ->where('dt.transaksi_id', $order->id)
            ->get();

        $clientKey = DB::table('tb_pengaturan')
            ->where('setting_nama', 'midtrans_client_key')
            ->value('setting_nilai');

        $trackingLogs = [
            ['status' => 'Menunggu Pembayaran', 'desc' => 'Pesanan berhasil dibuat, silakan selesaikan pembayaran.', 'time' => $order->tanggal_transaksi],
        ];

        return view('pages.pesanan_lacak', compact('order', 'items', 'clientKey', 'trackingLogs'));
    }

    // =================================================================
    // 12. PROFIL & PASSWORD
    // =================================================================
    public function profil()
    {
        if (!Auth::check()) { return redirect()->route('login'); }
        $user = Auth::user();
        $alamatUtama = DB::table('tb_user_alamat as ua')
            ->leftJoin('districts as d', 'ua.district_id', '=', 'd.id')
            ->leftJoin('cities as c', 'ua.city_id', '=', 'c.id')
            ->leftJoin('provinces as p', 'ua.province_id', '=', 'p.id')
            ->select('ua.*', 'd.name as district_name', 'c.name as city_name', 'p.name as province_name')
            ->where('ua.user_id', $user->id)->where('ua.is_utama', 1)->first();

        $alamatLengkapFormatted = '-';
        if ($alamatUtama) {
            $alamatLengkapFormatted = $alamatUtama->alamat_lengkap . '<br>Kec. ' . ($alamatUtama->district_name ?? 'Tidak Diketahui') . ', ' . ($alamatUtama->city_name ?? 'Tidak Diketahui') . ',<br>' . ($alamatUtama->province_name ?? 'Tidak Diketahui') . (!empty($alamatUtama->kode_pos) ? ', ' . $alamatUtama->kode_pos : '');
        }

        return view('pages.profil', compact('user', 'alamatLengkapFormatted'));
    }

    public function editProfil()
    {
        if (!Auth::check()) return redirect()->route('login');
        $user = Auth::user();
        $alamatUtama = DB::table('tb_user_alamat')->where('user_id', $user->id)->where('is_utama', 1)->first();
        $provinces = DB::table('provinces')->orderBy('name', 'ASC')->get();
        $cities = $alamatUtama ? DB::table('cities')->where('province_id', $alamatUtama->province_id)->orderBy('name', 'ASC')->get() : [];
        $districts = $alamatUtama ? DB::table('districts')->where('city_id', $alamatUtama->city_id)->orderBy('name', 'ASC')->get() : [];
        return view('pages.edit_profil', compact('user', 'alamatUtama', 'provinces', 'cities', 'districts'));
    }

    public function updateProfil(Request $request)
    {
        if (!Auth::check()) return redirect()->route('login');

        $user = Auth::user();

        $request->validate([
            'nama' => 'required|string|max:255',
            'province_id' => 'required',
            'city_id' => 'required',
            'district_id' => 'required',
        ], [
            'province_id.required' => 'Provinsi wajib diisi.',
            'city_id.required' => 'Kota wajib diisi.',
            'district_id.required' => 'Kecamatan wajib diisi.',
        ]);

        DB::table('tb_user')->where('id', $user->id)->update([
            'nama' => $request->nama,
            'no_telepon' => $request->no_telepon,
            'updated_at' => now()
        ]);

        $alamatUtama = DB::table('tb_user_alamat')
            ->where('user_id', $user->id)
            ->where('is_utama', 1)
            ->first();

        $dataAlamat = [
            'user_id'          => $user->id,
            'nama_penerima'    => $request->nama_penerima ?? $request->nama,
            'telepon_penerima' => $request->telepon_penerima ?? $request->no_telepon,
            'label_alamat'     => $request->label_alamat ?? 'Rumah',
            'alamat_lengkap'   => $request->alamat_lengkap,
            'kode_pos'         => $request->kode_pos,
            'province_id'      => $request->province_id,
            'city_id'          => $request->city_id,
            'district_id'      => $request->district_id,
            'latitude'         => $request->latitude,
            'longitude'        => $request->longitude,
            'is_utama'         => 1,
            'updated_at'       => now()
        ];

        if ($alamatUtama) {
            DB::table('tb_user_alamat')->where('id', $alamatUtama->id)->update($dataAlamat);
        } else {
            $dataAlamat['created_at'] = now();
            DB::table('tb_user_alamat')->insert($dataAlamat);
        }

        return redirect()->route('profil.index')->with('success', 'Data Profil & Alamat berhasil disimpan permanen!');
    }

    public function gantiPassword() { return view('pages.ganti_password'); }

    public function updatePassword(Request $request)
    {
        $request->validate(['password_lama' => 'required', 'password_baru' => 'required|min:8|confirmed']);
        if (!Hash::check($request->password_lama, Auth::user()->password)) { return back()->with('error', 'Password lama salah!'); }
        DB::table('tb_user')->where('id', Auth::id())->update(['password' => Hash::make($request->password_baru), 'updated_at' => now()]);
        return back()->with('success', 'Password berhasil diperbarui!');
    }

    // =================================================================
    // 14. API: LAZY LOAD / ON-DEMAND DISTRICTS (KECAMATAN)
    // =================================================================
    public function getDistrictsOnDemand($city_id)
    {
        $districts = DB::table('districts')->where('city_id', $city_id)->orderBy('name', 'ASC')->get();

        if ($districts->count() > 0) {
            return response()->json($districts);
        }

        $apiKey = DB::table('tb_pengaturan')->where('setting_nama', 'rajaongkir_api_key')->value('setting_nilai');

        if ($apiKey) {
            $resDist = \Illuminate\Support\Facades\Http::withHeaders(['key' => $apiKey])
                ->get("https://rajaongkir.komerce.id/api/v1/destination/district/{$city_id}");

            if ($resDist->successful() && isset($resDist['data'])) {
                foreach ($resDist['data'] as $dist) {
                    DB::table('districts')->updateOrInsert(
                        ['id'      => $dist['id']],
                        [
                            'city_id' => $city_id,
                            'name'    => strtoupper($dist['name'])
                        ]
                    );
                }
                $districts = DB::table('districts')->where('city_id', $city_id)->orderBy('name', 'ASC')->get();
            }
        }

        return response()->json($districts);
    }

    // =================================================================
    // 15. API: BIKIN KECAMATAN OTOMATIS JIKA TIDAK ADA DI DATABASE
    // =================================================================
    public function getOrCreateDistrict(Request $request)
    {
        $request->validate([
            'city_id' => 'required',
            'name' => 'required|string'
        ]);

        $cityId = $request->city_id;
        // Rapikan teks (contoh: "cibogo" jadi "Cibogo")
        $distName = ucwords(strtolower(trim($request->name)));

        // Cek apakah kecamatan sudah ada di tabel districts
        $existing = DB::table('districts')
            ->where('city_id', $cityId)
            ->whereRaw('LOWER(name) = ?', [strtolower($distName)])
            ->first();

        // Kalau ternyata sudah ada, kembalikan datanya
        if ($existing) {
            return response()->json([
                'id' => $existing->id,
                'name' => $existing->name
            ]);
        }

        // KALAU BELUM ADA, BIKIN BARU!
        try {
            $newId = DB::table('districts')->insertGetId([
                'city_id' => $cityId,
                'name' => $distName
            ]);

            return response()->json([
                'id' => $newId,
                'name' => $distName
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error', 
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
