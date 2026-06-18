<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http; // Wajib untuk hit API Biteship
use Illuminate\Support\Facades\Log; // Untuk logging Webhook Midtrans

class PageController extends Controller
{
    // =================================================================
    // 1. HALAMAN DAFTAR PRODUK (Katalog Utama Lengkap dengan Filter)
    // =================================================================
    public function produk(Request $request)
    {
        $categories = DB::table('tb_kategori')->orderBy('nama_kategori', 'ASC')->get();

        $dbCities = DB::table('tb_toko')
            ->where('status', 'active')
            ->whereNotNull('kota')
            ->where('kota', '!=', '')
            ->distinct()
            ->pluck('kota')
            ->toArray();

        $fallbackCities = [
            'Jakarta', 'Bogor', 'Depok', 'Tangerang', 'Bekasi', 'Bandung',
            'Surabaya', 'Semarang', 'Yogyakarta', 'Surakarta', 'Medan', 'Makassar',
            'Denpasar', 'Balikpapan', 'Samarinda', 'Batam', 'Padang', 'Subang',
            'Purwakarta', 'Karawang', 'Cirebon', 'Cimahi', 'Tasikmalaya', 'Garut', 'Sukabumi', 'Majalengka', 'Sumedang', 'Indramayu'
        ];

        $cityList = array_unique(array_merge($dbCities, $fallbackCities));

        if ($request->filled('lokasi') && !in_array($request->lokasi, $cityList)) {
            $cityList[] = $request->lokasi;
        }
        
        sort($cityList);

        $locations = collect($cityList)->map(function($city) {
            return (object) ['city_id' => $city, 'nama_kota' => $city];
        });

        $query = DB::table('tb_barang as b')
            ->join('tb_toko as t', 'b.toko_id', '=', 't.id')
            ->select(
                'b.id', 'b.nama_barang', 'b.slug', 'b.harga', 'b.gambar_utama', 'b.satuan_unit',
                't.nama_toko', 't.slug as toko_slug', 't.tier_toko', 't.kota as nama_kota'
            )
            ->where('b.is_active', 1)
            ->where('b.status_moderasi', 'approved')
            ->where('t.status', 'active');

        $raw_kategori = $request->kategori;
        $filter_kategori = is_array($raw_kategori) ? $raw_kategori : (!empty($raw_kategori) ? [$raw_kategori] : []);
        if (!empty($filter_kategori)) {
            $query->whereIn('b.kategori_id', $filter_kategori);
        }

        if ($request->has('kategori_text') && is_array($request->kategori_text)) {
            $query->where(function($q) use ($request) {
                foreach ($request->kategori_text as $text) {
                    $q->orWhere('b.nama_barang', 'LIKE', '%' . $text . '%');
                }
            });
        }

        if ($request->has('tier_toko') && is_array($request->tier_toko)) {
            $query->whereIn('t.tier_toko', $request->tier_toko);
        }

        if ($request->filled('lokasi')) {
            $query->where('t.kota', $request->lokasi);
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
    // 2. HALAMAN HASIL PENCARIAN
    // =================================================================
    public function search(Request $request)
    {
        $keyword = $request->input('query');
        $kategoriId = $request->input('kategori');

        $categories = DB::table('tb_kategori')->orderBy('nama_kategori', 'ASC')->get();

        $dbCities = DB::table('tb_toko')
            ->where('status', 'active')
            ->whereNotNull('kota')
            ->where('kota', '!=', '')
            ->distinct()
            ->pluck('kota')
            ->toArray();

        $fallbackCities = [
            'Jakarta', 'Bogor', 'Depok', 'Tangerang', 'Bekasi', 'Bandung',
            'Surabaya', 'Semarang', 'Yogyakarta', 'Surakarta', 'Medan', 'Makassar',
            'Denpasar', 'Balikpapan', 'Samarinda', 'Batam', 'Padang', 'Subang',
            'Purwakarta', 'Karawang', 'Cirebon', 'Cimahi', 'Tasikmalaya', 'Garut', 'Sukabumi', 'Majalengka', 'Sumedang', 'Indramayu'
        ];

        $cityList = array_unique(array_merge($dbCities, $fallbackCities));
        if ($request->filled('lokasi') && $request->lokasi !== 'semua' && !in_array($request->lokasi, $cityList)) {
            $cityList[] = $request->lokasi;
        }
        sort($cityList);

        $locations = collect($cityList)->map(function($city) {
            return (object) ['city_id' => $city, 'city_name' => $city];
        });

        $query = DB::table('tb_barang as b')
            ->join('tb_toko as t', 'b.toko_id', '=', 't.id')
            ->select('b.id', 'b.nama_barang', 'b.slug', 'b.harga', 'b.gambar_utama', 't.nama_toko', 't.slug as slug_toko', 't.tier_toko', 't.kota as kota_toko')
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

        if ($request->has('kategori_text') && is_array($request->kategori_text)) {
            $query->where(function($q) use ($request) {
                foreach ($request->kategori_text as $text) {
                    $q->orWhere('b.nama_barang', 'LIKE', '%' . $text . '%');
                }
            });
        }

        if ($request->has('tier_toko') && is_array($request->tier_toko)) {
            $query->whereIn('t.tier_toko', $request->tier_toko);
        }

        if ($request->filled('lokasi') && $request->lokasi !== 'semua') {
            $query->where('t.kota', $request->lokasi);
        }

        if ($request->filled('harga_min')) {
            $query->where('b.harga', '>=', $request->harga_min);
        }
        if ($request->filled('harga_max')) {
            $query->where('b.harga', '<=', $request->harga_max);
        }

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

        $products = $query->paginate(12)->appends($request->query());

        $filter_lokasi = $request->lokasi ?? 'semua';
        $filter_harga_min = $request->harga_min ?? '';
        $filter_harga_max = $request->harga_max ?? '';

        return view('pages.search', compact(
            'products', 'categories', 'keyword', 'kategoriId', 
            'locations', 'filter_lokasi', 'filter_harga_min', 'filter_harga_max'
        ));
    }

    // =================================================================
    // 3. HALAMAN DETAIL PRODUK
    // =================================================================
    public function detail($id)
    {
        $produk = DB::table('tb_barang as p')
            ->leftJoin('tb_kategori as k', 'p.kategori_id', '=', 'k.id')
            ->join('tb_toko as t', 'p.toko_id', '=', 't.id')
            ->select(
                'p.*', 
                'k.nama_kategori', 
                't.id as toko_id', 
                't.nama_toko', 
                't.slug as slug_toko', 
                't.logo_toko', 
                't.tier_toko', 
                't.kota as nama_kota_toko', 
                DB::raw("(SELECT COALESCE(SUM(jumlah), 0) FROM tb_detail_transaksi WHERE barang_id = p.id AND status_pesanan_item NOT IN ('dibatalkan', 'pengembalian_disetujui')) as stok_terjual")
            )
            ->where('p.id', $id)
            ->where('p.is_active', 1)
            ->where('p.status_moderasi', 'approved')
            ->where('t.status', 'active')
            ->first();

        if (!$produk) {
            return redirect()->route('produk.index')->with('error', 'Produk tidak ditemukan atau tidak aktif.');
        }

        $ulasan = DB::table('tb_review_produk as r')
            ->join('tb_user as u', 'r.user_id', '=', 'u.id')
            ->select('r.*', 'u.nama as nama_user', 'u.profile_picture_url as foto_user')
            ->where('r.barang_id', $id)
            ->orderByDesc('r.created_at')
            ->limit(5)
            ->get();

        $produkTerkait = DB::table('tb_barang as b')
            ->join('tb_toko as t', 'b.toko_id', '=', 't.id')
            ->select('b.id', 'b.nama_barang', 'b.harga', 'b.gambar_utama', 't.nama_toko', 't.tier_toko', 't.kota as kota_toko')
            ->where('b.kategori_id', $produk->kategori_id)
            ->where('b.id', '!=', $id)
            ->where('b.is_active', 1)
            ->where('b.status_moderasi', 'approved')
            ->where('t.status', 'active')
            ->limit(4)
            ->get();

        return view('pages.detail_produk', compact('produk', 'ulasan', 'produkTerkait'));
    }

    // =================================================================
    // 4. HALAMAN SEMUA TOKO
    // =================================================================
    public function semuaToko(Request $request)
    {
        $filter_lokasi = $request->query('lokasi', 'semua');
        $lat = $request->query('lat');
        $lng = $request->query('lng');

        $dbCities = DB::table('tb_toko')
            ->where('status', 'active')
            ->whereNotNull('kota')
            ->where('kota', '!=', '')
            ->distinct()
            ->pluck('kota')
            ->toArray();

        $fallbackCities = [
            'Jakarta', 'Bogor', 'Depok', 'Tangerang', 'Bekasi', 'Bandung',
            'Surabaya', 'Semarang', 'Yogyakarta', 'Surakarta', 'Medan', 'Makassar',
            'Denpasar', 'Balikpapan', 'Samarinda', 'Batam', 'Padang', 'Subang',
            'Purwakarta', 'Karawang', 'Cirebon', 'Cimahi', 'Tasikmalaya', 'Garut', 'Sukabumi', 'Majalengka', 'Sumedang', 'Indramayu'
        ];

        $cityList = array_unique(array_merge($dbCities, $fallbackCities));
        if ($filter_lokasi !== 'semua' && !empty($filter_lokasi) && !in_array($filter_lokasi, $cityList)) {
            $cityList[] = $filter_lokasi;
        }
        sort($cityList);

        $locations = collect($cityList)->map(function($city) {
            return (object) ['city_id' => $city, 'city_name' => $city];
        });

        $query = DB::table('tb_toko as t')
            ->select('t.id', 't.nama_toko', 't.slug', 't.deskripsi_toko', 't.logo_toko', 't.banner_toko', 't.tier_toko', 't.kota as city_name', 't.latitude', 't.longitude')
            ->selectSub(function ($q) {
                $q->from('tb_barang')->whereColumn('toko_id', 't.id')->where('is_active', 1)->where('status_moderasi', 'approved')->selectRaw('COUNT(id)');
            }, 'jumlah_produk')
            ->selectSub(function ($q) {
                $q->from('tb_toko_review')->whereColumn('toko_id', 't.id')->selectRaw('COALESCE(AVG(rating), 0)');
            }, 'rating')
            ->where('t.status', 'active');

        if ($lat && $lng) {
            $query->selectRaw('(6371 * acos(cos(radians(?)) * cos(radians(t.latitude)) * cos(radians(t.longitude) - radians(?)) + sin(radians(?)) * sin(radians(t.latitude)))) AS jarak_km', [$lat, $lng, $lat]);
            $query->orderBy('jarak_km', 'ASC'); 
        } else {
            $query->orderBy('t.nama_toko', 'ASC');
            if ($filter_lokasi !== 'semua' && !empty($filter_lokasi)) {
                $query->where('t.kota', $filter_lokasi);
            }
        }

        $mapQuery = clone $query;
        $allMapStores = $mapQuery->whereNotNull('t.latitude')->whereNotNull('t.longitude')->get();

        $stores = $query->paginate(12)->withQueryString();

        return view('pages.semua_toko', compact('locations', 'stores', 'filter_lokasi', 'allMapStores', 'lat', 'lng'));
    }

// =================================================================
    // 5. HALAMAN PROFIL TOKO (DENGAN FILTER & SORTING DEWA)
    // =================================================================
    public function detailToko(Request $request)
    {
        $slug = $request->query('slug');

        $toko = DB::table('tb_toko as t')
            ->select('t.*')
            ->where('t.slug', $slug)
            ->where('t.status', 'active')
            ->first();

        if (!$toko) { abort(404, 'Toko tidak ditemukan atau sedang tidak aktif.'); }

        $colors = ['#e53935', '#d81b60', '#8e24aa', '#5e35b1', '#3949ab', '#1e88e5', '#039be5', '#00acc1', '#00897b', '#43a047', '#7cb342', '#c0ca33', '#fdd835', '#ffb300', '#fb8c00', '#f4511e'];
        $storeColor = $colors[crc32($toko->nama_toko) % count($colors)];

        $words = explode(" ", $toko->nama_toko);
        $acronym = "";
        foreach ($words as $w) { $acronym .= mb_substr($w, 0, 1); }
        $storeInitials = strtoupper(substr($acronym, 0, 2));
        if (empty($storeInitials)) { $storeInitials = "TK"; }

        // --- CORE: LOGIKA FILTER KATEGORI & SORTING ---
        $query = DB::table('tb_barang as b')
            ->where('b.toko_id', $toko->id)
            ->where('b.is_active', 1)
            ->where('b.status_moderasi', 'approved')
            ->select('b.*');

        // 1. Filter Kategori
        if ($request->filled('kategori')) {
            $query->where('b.kategori_id', $request->kategori);
        }

        // 2. Sorting Harga & Terlaris
        if ($request->filled('sort')) {
            if ($request->sort == 'termurah') {
                $query->orderBy('b.harga', 'ASC');
            } elseif ($request->sort == 'termahal') {
                $query->orderBy('b.harga', 'DESC');
            } elseif ($request->sort == 'terlaris') {
                $query->selectRaw("(SELECT COALESCE(SUM(jumlah), 0) FROM tb_detail_transaksi WHERE barang_id = b.id AND status_pesanan_item NOT IN ('dibatalkan')) as stok_terjual")
                      ->orderBy('stok_terjual', 'DESC');
            } else {
                $query->orderBy('b.created_at', 'DESC'); // terbaru
            }
        } else {
            $query->orderBy('b.created_at', 'DESC'); // Default awal
        }

        // Tetap menempelkan query (slug, kategori, sort) di link pagination
        $products = $query->paginate(12)->appends($request->query());
        // ----------------------------------------------

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
    // 7. API KERANJANG
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
    // 8. HALAMAN CHECKOUT (Sempurna: Sinkronisasi Lokasi & Kurir Seller)
    // =================================================================
    public function checkout(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Silakan masuk untuk melanjutkan checkout.');
        }

        // PROTEKSI SOFT BAN: Customer diblokir tidak bisa checkout
        if (Auth::user()->is_banned) {
            return redirect()->route('home')->with('error', 'Akun Anda sedang ditangguhkan. Anda tidak dapat melakukan transaksi belanja.');
        }

        $userId = Auth::id();
        $userEmail = Auth::user()->email ?? 'customer@example.com';

        $alamatUser = DB::table('tb_user_alamat as ua')
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
                'area_id'   => $alamatUser->area_id ?? '',
                'lat'       => $alamatUser->latitude ?? '',
                'lng'       => $alamatUser->longitude ?? '',
                'kodepos'   => $alamatUser->kode_pos ?? '',
                'kecamatan' => '', 'kota' => '', 'provinsi' => ''
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
                ->select(
                    'b.id as barang_id', 'b.nama_barang', 'b.harga', 'b.gambar_utama', 'b.stok', 
                    't.id as toko_id', 't.nama_toko', 't.kota as kota_toko',
                    't.area_id as origin_area_id', 't.active_api_couriers'
                )
                ->where('b.id', $productId)
                ->first();

            if ($item) {
                $item->jumlah = $jumlah;
                $itemArray[] = $item;
                $itemsPerToko[$item->toko_id] = [
                    'nama_toko'       => $item->nama_toko, 
                    'kota_toko'       => $item->kota_toko, 
                    'origin_area_id'  => $item->origin_area_id, 
                    'active_couriers' => $item->active_api_couriers, 
                    'items'           => [$item]
                ];
                $totalProduk += $item->harga * $jumlah;
            }
        } else {
            $rawSelectedItems = $request->input('selected_items') ?? session('selected_items');

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
                ->select(
                    'k.id as keranjang_id', 'b.id as barang_id', 'b.nama_barang', 'b.harga', 'b.gambar_utama', 'k.jumlah', 
                    't.id as toko_id', 't.nama_toko', 't.kota as kota_toko',
                    't.area_id as origin_area_id', 't.active_api_couriers'
                )
                ->where('k.user_id', $userId)
                ->whereIn('k.id', $selectedItems)
                ->get();

            foreach ($items as $item) {
                $itemArray[] = $item;
                if (!isset($itemsPerToko[$item->toko_id])) {
                    $itemsPerToko[$item->toko_id] = [
                        'nama_toko'       => $item->nama_toko, 
                        'kota_toko'       => $item->kota_toko, 
                        'origin_area_id'  => $item->origin_area_id, 
                        'active_couriers' => $item->active_api_couriers, 
                        'items'           => []
                    ];
                }
                $itemsPerToko[$item->toko_id]['items'][] = $item;
                $totalProduk += $item->harga * $item->jumlah;
            }
        }

        if (empty($itemsPerToko)) {
            return redirect()->route('keranjang.index')->with('error', 'Data produk tidak valid atau keranjang kosong.');
        }

        // Ambil Pengaturan DP B2B
        $settingsData = DB::table('tb_pengaturan')->whereIn('setting_nama', ['enable_dp_system', 'min_nominal_dp', 'dp_percent', 'dp_expired_minutes'])->get();
        $dpSettings = [
            'enable_dp_system' => 0,
            'min_nominal_dp' => 10000000,
            'dp_percent' => 50,
            'dp_expired_minutes' => 1440
        ];
        foreach ($settingsData as $row) {
            $dpSettings[$row->setting_nama] = $row->setting_nilai;
        }

        return view('pages.checkout', compact('userEmail', 'alamatUser', 'addressData', 'isAlamatIncomplete', 'itemsPerToko', 'itemArray', 'totalProduk', 'isDirectPurchase', 'request', 'dpSettings'));
    }

    // =================================================================
    // 9. PROSES CHECKOUT (MARKETPLACE / MULTI-VENDOR LOGIC)
    // =================================================================
    public function prosesCheckout(Request $request)
    {
        try {
            DB::beginTransaction();

            $user = Auth::user();

            // PROTEKSI SOFT BAN
            if ($user->is_banned) {
                return response()->json(['success' => false, 'error' => 'Akun Anda sedang ditangguhkan. Tidak dapat memproses pesanan.'], 403);
            }

            $orderId = 'INV-' . time() . '-' . rand(100, 999);

            $totalProdukReal = 0;
            $totalDiskonReal = 0;
            $biayaPengirimanReal = 0;
            $rincianKurir = [];
            $itemsToProcess = [];

            // 1. KUMPULKAN BARANG
            if ($request->has('direct_purchase')) {
                $produk = DB::table('tb_barang')->where('id', $request->input('product_id'))->first();
                if ($produk) {
                    $qty = $request->input('jumlah', 1);
                    $itemsToProcess[] = [
                        'toko_id' => $produk->toko_id, 'barang_id' => $produk->id,
                        'nama_barang' => $produk->nama_barang, 'harga' => $produk->harga,
                        'jumlah' => $qty, 'subtotal' => $produk->harga * $qty
                    ];
                }
            } else {
                $selectedIds = is_array($request->selected_items) ? $request->selected_items : explode(',', $request->selected_items);
                $keranjangs = DB::table('tb_keranjang')
                    ->join('tb_barang', 'tb_keranjang.barang_id', '=', 'tb_barang.id')
                    ->whereIn('tb_keranjang.id', $selectedIds)->where('tb_keranjang.user_id', $user->id)
                    ->select('tb_keranjang.*', 'tb_barang.toko_id', 'tb_barang.harga', 'tb_barang.nama_barang')
                    ->get();
                foreach ($keranjangs as $item) {
                    $itemsToProcess[] = [
                        'toko_id' => $item->toko_id, 'barang_id' => $item->barang_id,
                        'nama_barang' => $item->nama_barang, 'harga' => $item->harga,
                        'jumlah' => $item->jumlah, 'subtotal' => $item->harga * $item->jumlah
                    ];
                }
            }

            // 2. KELOMPOKKAN BERDASARKAN TOKO
            $itemsPerToko = [];
            foreach ($itemsToProcess as $item) {
                $itemsPerToko[$item['toko_id']]['items'][] = $item;
                if (!isset($itemsPerToko[$item['toko_id']]['subtotal'])) $itemsPerToko[$item['toko_id']]['subtotal'] = 0;
                $itemsPerToko[$item['toko_id']]['subtotal'] += $item['subtotal'];
            }

            $tipePengambilan = $request->input('tipe_pengambilan') ?? 'kurir';
            $detailTransaksiData = [];
            
            // 3. PROSES ONGKIR, VOUCHER, & CATATAN KHUSUS PER-TOKO (THE MAGIC!)
            foreach ($itemsPerToko as $tokoId => $dataToko) {
                $ongkirToko = 0; 
                $kurirToko = NULL;
                
                // EKSTRAK ONGKIR TOKO INI
                if (in_array($tipePengambilan, ['kurir', 'armada']) && $request->has("shipping.$tokoId")) {
                    $shippingVal = $request->input("shipping.$tokoId");
                    if (!empty($shippingVal)) {
                        $parts = explode('_', $shippingVal);
                        $ongkirToko = (int) end($parts);
                        $kurirToko = strtoupper(str_replace("_" . $ongkirToko, "", $shippingVal));
                        $biayaPengirimanReal += $ongkirToko;
                        $rincianKurir[] = "Toko-$tokoId: " . $kurirToko . " (Rp" . number_format($ongkirToko, 0, ',', '.') . ")";
                    }
                }

                // EKSTRAK & VALIDASI VOUCHER TOKO INI DI DATABASE
                $diskonToko = 0;
                if ($request->has("voucher_toko.$tokoId") && !empty($request->input("voucher_toko.$tokoId"))) {
                    $voucherCode = $request->input("voucher_toko.$tokoId");
                    $cekVoucher = DB::table('vouchers')
                        ->where('kode_voucher', $voucherCode)->where('toko_id', $tokoId)->where('status', 'AKTIF')
                        ->where('tanggal_berakhir', '>', now())->where('min_pembelian', '<=', $dataToko['subtotal'])
                        ->first();
                        
                    if ($cekVoucher) {
                        if ($cekVoucher->tipe_diskon == 'PERSEN') {
                            $diskonHitung = $dataToko['subtotal'] * ($cekVoucher->nilai_diskon / 100);
                            $diskonToko = ($cekVoucher->maks_diskon && $diskonHitung > $cekVoucher->maks_diskon) ? $cekVoucher->maks_diskon : $diskonHitung;
                        } else { 
                            $diskonToko = $cekVoucher->nilai_diskon; 
                        }
                        $totalDiskonReal += $diskonToko;
                        // Kurangi kuota voucher di database!
                        DB::table('vouchers')->where('id', $cekVoucher->id)->increment('kuota_terpakai');
                    }
                }

                // EKSTRAK CATATAN PEMBELI UNTUK TOKO INI
                $catatanToko = $request->input("catatan_pembeli.$tokoId");

// SUSUN DETAIL TRANSAKSI & AMANKAN STOK
                foreach ($dataToko['items'] as $index => $item) {
                    
                    // CEK & POTONG STOK (MENGAMANKAN STOK SELAMA 20 MENIT)
                    $cekStok = DB::table('tb_barang')->where('id', $item['barang_id'])->first();
                    if(!$cekStok || $cekStok->stok < $item['jumlah']) {
                        throw new \Exception('Gagal: Stok "' . $item['nama_barang'] . '" tidak cukup / baru saja dibeli orang lain.');
                    }
                    // Kurangi stok sekarang juga!
                    DB::table('tb_barang')->where('id', $item['barang_id'])->decrement('stok', $item['jumlah']);
                    
                    $totalProdukReal += $item['subtotal'];
                    $detailTransaksiData[] = [
                        'transaksi_id'               => null, // Diupdate di bawah setelah master transaksi dibuat
                        'toko_id'                    => $item['toko_id'],
                        'barang_id'                  => $item['barang_id'],
                        'nama_barang_saat_transaksi' => $item['nama_barang'],
                        'harga_saat_transaksi'       => $item['harga'],
                        'jumlah'                     => $item['jumlah'],
                        'subtotal'                   => $item['subtotal'],
                        'metode_pengiriman'          => strtoupper($tipePengambilan),
                        'kurir_terpilih'             => $kurirToko,
                        'biaya_pengiriman_item'      => ($index == 0) ? $ongkirToko : 0, 
                        'catatan_pembeli'            => $catatanToko, 
                        'status_pesanan_item'        => 'menunggu_pembayaran'
                    ];
                }
            }

            $grandTotalReal = $totalProdukReal - $totalDiskonReal + $biayaPengirimanReal;

            // AMBIL SETTING DP UNTUK VALIDASI JIKA BUYER MEMILIH DP
            $tipePembayaranReq = $request->input('tipe_pembayaran', 'LUNAS');
            $tipePembayaranFinal = 'LUNAS';
            $jumlahDp = 0;
            $sisaTagihan = 0;
            $dpExpiredAt = null;
            $midtransGrossAmount = $grandTotalReal;
            
            if ($tipePembayaranReq === 'DP') {
                $dpSettingsData = DB::table('tb_pengaturan')->whereIn('setting_nama', ['enable_dp_system', 'min_nominal_dp', 'dp_percent', 'dp_expired_minutes'])->get()->pluck('setting_nilai', 'setting_nama');
                
                if (($dpSettingsData['enable_dp_system'] ?? 0) == 1 && $totalProdukReal >= ($dpSettingsData['min_nominal_dp'] ?? 10000000)) {
                    $tipePembayaranFinal = 'DP';
                    $dpPercent = $dpSettingsData['dp_percent'] ?? 50;
                    
                    // Hitung nominal DP dari Grand Total (Bukan hanya total produk)
                    $jumlahDp = round($grandTotalReal * ($dpPercent / 100));
                    $sisaTagihan = $grandTotalReal - $jumlahDp;
                    
                    $expiredMinutes = (int) ($dpSettingsData['dp_expired_minutes'] ?? 1440);
                    $dpExpiredAt = now()->addMinutes($expiredMinutes);
                    
                    $midtransGrossAmount = $jumlahDp; // Midtrans hanya charge DP
                    $rincianKurir[] = 'STATUS: MENUNGGU DP B2B';
                }
            }

            // 4. INSERT GLOBAL TRANSAKSI
            $transaksiId = DB::table('tb_transaksi')->insertGetId([
                'kode_invoice'              => $orderId, 
                'sumber_transaksi'          => 'ONLINE', 
                'user_id'                   => $user->id,
                'total_harga_produk'        => $totalProdukReal, 
                'total_diskon'              => $totalDiskonReal, 
                'total_final'               => $grandTotalReal,
                'tipe_pembayaran'           => $tipePembayaranFinal, 
                'jumlah_dp'                 => $jumlahDp,
                'sisa_tagihan'              => $sisaTagihan,
                'dp_expired_at'             => $dpExpiredAt,
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
                
                'catatan'                   => 'Marketplace Multi-Vendor Order. ' . implode(', ', $rincianKurir),
                'biaya_pengiriman'          => $biayaPengirimanReal, 
                'tipe_pengambilan'          => $tipePengambilan,
                'tanggal_transaksi'         => now()
            ]);

            // 5. RELASIKAN TRANSAKSI ID LALU INSERT KE DETAIL
            foreach ($detailTransaksiData as &$d) { $d['transaksi_id'] = $transaksiId; }
            DB::table('tb_detail_transaksi')->insert($detailTransaksiData);

            // 6. HAPUS KERANJANG
            if (!$request->has('direct_purchase')) {
                $selectedIds = is_array($request->selected_items) ? $request->selected_items : explode(',', $request->selected_items);
                DB::table('tb_keranjang')->where('user_id', $user->id)->whereIn('id', $selectedIds)->delete();
            }

            // 7. MIDTRANS GATEWAY
            $settings = DB::table('tb_pengaturan')->whereIn('setting_nama', ['midtrans_server_key', 'midtrans_is_production'])->pluck('setting_nilai', 'setting_nama');
            \Midtrans\Config::$serverKey = $settings['midtrans_server_key'] ?? '';
            \Midtrans\Config::$isProduction = ($settings['midtrans_is_production'] ?? '0') == '1';
            \Midtrans\Config::$isSanitized = true; 
            \Midtrans\Config::$is3ds = true;

            $snapToken = \Midtrans\Snap::getSnapToken([
                'transaction_details' => ['order_id' => $orderId, 'gross_amount' => (int) $midtransGrossAmount],
                'customer_details' => [
                    'first_name' => $request->input('shipping_nama_penerima') ?? $user->nama,
                    'email'      => $user->email,
                    'phone'      => $request->input('shipping_telepon_penerima') ?? $user->no_telepon,
                ]
            ]);

            DB::table('tb_transaksi')->where('id', $transaksiId)->update(['snap_token' => $snapToken]);

            DB::commit();
            return response()->json(['status' => 'success', 'kode_invoice' => $orderId]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => 'Kesalahan Sistem: ' . $e->getMessage()], 500);
        }
    }
    // =================================================================
    // 9.5 MIDTRANS WEBHOOK / FRONTEND CALLBACK (Status Pembayaran)
    // =================================================================
    public function updatePaymentStatus(Request $request)
    {
        try {
            DB::beginTransaction();
            $orderId = $request->order_id;
            
            DB::table('tb_transaksi')->where('kode_invoice', $orderId)->update([
                'status_pembayaran' => 'paid',
                'status_pesanan_global' => 'diproses',
                'updated_at' => now()
            ]);

            $transaksi = DB::table('tb_transaksi')->where('kode_invoice', $orderId)->first();
            if($transaksi) {
                DB::table('tb_detail_transaksi')->where('transaksi_id', $transaksi->id)->update([
                    'status_pesanan_item' => 'diproses'
                ]);
            }

            DB::commit();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false], 500);
        }
    }

    // Webhook Murni dari Server Midtrans (Opsional untuk integrasi production)
    public function midtransWebhook(Request $request)
    {
        $serverKey = DB::table('tb_pengaturan')->where('setting_nama', 'midtrans_server_key')->value('setting_nilai');
        $hashed = hash("sha512", $request->order_id . $request->status_code . $request->gross_amount . $serverKey);
        
        if ($hashed == $request->signature_key) {
            if ($request->transaction_status == 'capture' || $request->transaction_status == 'settlement') {
                $transaksi = DB::table('tb_transaksi')->where('kode_invoice', $request->order_id)->first();
                if($transaksi && $transaksi->status_pembayaran != 'paid') {
                    DB::table('tb_transaksi')->where('kode_invoice', $request->order_id)->update([
                        'status_pembayaran' => 'paid',
                        'status_pesanan_global' => 'diproses',
                        'updated_at' => now()
                    ]);
                    DB::table('tb_detail_transaksi')->where('transaksi_id', $transaksi->id)->update([
                        'status_pesanan_item' => 'diproses'
                    ]);
                }
            } elseif (in_array($request->transaction_status, ['cancel', 'deny', 'expire'])) {
                DB::table('tb_transaksi')->where('kode_invoice', $request->order_id)->update([
                    'status_pembayaran' => 'failed',
                    'status_pesanan_global' => 'dibatalkan',
                    'updated_at' => now()
                ]);
            }
            return response()->json(['status' => 'success']);
        }
        return response()->json(['status' => 'invalid signature'], 403);
    }

    // =================================================================
    // API Cek Ongkir (3 Mode: Ekspedisi / Armada / Pickup)
    // =================================================================
    public function cekOngkir(Request $request)
    {
        try {
            $tipe = $request->query('tipe', 'kurir');
            
            $originAreaId = $request->query('origin');
            $destinationAreaId = $request->query('destination');
            $weight = $request->query('weight', 1000);
            $tokoId = $request->query('toko_id');
            $destLat = $request->query('dest_lat');
            $destLng = $request->query('dest_lng');

            // FIX FATAL: Bersihkan string kurir dari format JSON sisaan masa lalu 
            $rawCouriers = $request->query('couriers', 'jne');
            $couriers = str_replace(['[', ']', '"', "'", ' '], '', $rawCouriers);

            // =================================================================
            // LOGIKA 1: PENGIRIMAN VIA ARMADA TOKO (GPS ONLY)
            // =================================================================
            if ($tipe === 'armada') {
                if (!$tokoId || !$destLat || !$destLng) {
                    return response()->json(['success' => false, 'error' => 'Gagal menghitung jarak. Pastikan pin peta alamat Anda sudah ditentukan dengan benar.']);
                }

                $toko = DB::table('tb_toko')->where('id', $tokoId)->first();
                if (!$toko || empty($toko->latitude) || empty($toko->longitude)) {
                    return response()->json(['success' => false, 'error' => 'Toko belum mengatur lokasi Gudang (GPS) mereka.']);
                }

                $prefs = json_decode($toko->logistics_preferences, true) ?? [];
                
                if (empty($prefs['custom_fleet']) || $prefs['custom_fleet'] != '1') {
                    return response()->json(['success' => false, 'error' => 'Toko ini tidak menyediakan layanan pengiriman Armada Internal. Silakan pilih pengiriman ekspedisi.']);
                }

                // Rumus Haversine (Jarak Garis Lurus GPS)
                $earthRadius = 6371; 
                $latFrom = deg2rad((float)$toko->latitude);
                $lonFrom = deg2rad((float)$toko->longitude);
                $latTo = deg2rad((float)$destLat);
                $lonTo = deg2rad((float)$destLng);

                $latDelta = $latTo - $latFrom;
                $lonDelta = $lonTo - $lonFrom;

                $a = sin($latDelta / 2) * sin($latDelta / 2) + cos($latFrom) * cos($latTo) * sin($lonDelta / 2) * sin($lonDelta / 2);
                $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
                $distance = $earthRadius * $c;
                $calcDist = $distance < 1 ? 1 : ceil($distance); 
                
                $maxDistance = (float)($prefs['fleet_max_distance'] ?? 50);

                if ($calcDist > $maxDistance) {
                    return response()->json(['success' => false, 'error' => "Jarak lokasi Anda ({$calcDist} KM) melebihi batas layanan armada toko ini ({$maxDistance} KM)."]);
                }

                $pricing = [];
                $pricePerKm = (float)($prefs['fleet_price_per_km'] ?? 5000);
                
                $pricing[] = [
                    'company' => 'armada_toko',
                    'courier_name' => 'Armada Internal Toko',
                    'courier_service_name' => 'Truk/Pickup (' . $calcDist . ' KM)',
                    'price' => $calcDist * $pricePerKm,
                    'duration' => 'Dikirim oleh tim gudang'
                ];

                if (isset($prefs['emergency_delivery']) && $prefs['emergency_delivery'] == '1') {
                    $pricing[] = [
                        'company' => 'cito_sameday',
                        'courier_name' => 'Pengiriman Darurat',
                        'courier_service_name' => 'CITO/Sameday (' . $calcDist . ' KM)',
                        'price' => 25000 + ($calcDist * 3000),
                        'duration' => 'Hari ini (Segera)'
                    ];
                }

                return response()->json(['success' => true, 'pricing' => $pricing]);
            }

            // =================================================================
            // LOGIKA 2: PENGIRIMAN VIA KURIR EKSPEDISI (BITESHIP API)
            // =================================================================
            if (empty($originAreaId) || empty($destinationAreaId)) {
                return response()->json(['success' => false, 'error' => 'Origin atau Destination belum lengkap.']);
            }

            $apiKey = DB::table('tb_pengaturan')->where('setting_nama', 'biteship_api_key')->value('setting_nilai');
            
            $response = \Illuminate\Support\Facades\Http::withHeaders([
                'Authorization' => $apiKey,
                'Content-Type'  => 'application/json'
            ])->post('https://api.biteship.com/v1/rates/couriers', [
                'origin_area_id' => $originAreaId,
                'destination_area_id' => $destinationAreaId,
                'couriers' => $couriers, // SUDAH BERSIH
                'items' => [
                    ['name' => 'Material Bangunan', 'value' => 50000, 'weight' => (int) $weight, 'quantity' => 1]
                ]
            ]);

            $data = $response->json();

            // BYPASS SALDO KOSONG
            if (!$response->successful() && isset($data['error']) && str_contains(strtolower($data['error']), 'balance')) {
                $bypassPricing = [];
                $allCouriers = explode(',', $couriers);
                foreach ($allCouriers as $index => $cCode) {
                    if (trim($cCode) !== '') {
                        $randomPrice = 15000 + ($index * 1000) + rand(100, 900);
                        $bypassPricing[] = [
                            'company' => trim($cCode),
                            'courier_name' => strtoupper(trim($cCode)),
                            'courier_service_name' => 'REG (Bypass Testing)',
                            'price' => $randomPrice,
                            'duration' => '1-3 days'
                        ];
                    }
                }
                return response()->json(['success' => true, 'pricing' => $bypassPricing]);
            }

            return response()->json($data);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => 'Koneksi gagal: ' . $e->getMessage()], 500);
        }
    }

// =================================================================
    // 10. RIWAYAT PESANAN SAYA (Dengan Auto-Catch Midtrans)
    // =================================================================
    public function pesanan(Request $request)
    {
        if (!Auth::check()) return redirect()->route('login');

        // AUTO-CATCH DARI MIDTRANS REDIRECT (Native URL)
        if ($request->has('transaction_status') && in_array($request->transaction_status, ['settlement', 'capture'])) {
            $orderId = $request->order_id;
            $transaksi = DB::table('tb_transaksi')->where('kode_invoice', $orderId)->first();
            
            if ($transaksi && $transaksi->status_pembayaran !== 'paid') {
                DB::table('tb_transaksi')->where('id', $transaksi->id)->update([
                    'status_pembayaran' => 'paid',
                    'status_pesanan_global' => 'diproses'
                    // Kolom updated_at dihapus dari sini
                ]);
                DB::table('tb_detail_transaksi')->where('transaksi_id', $transaksi->id)->update([
                    'status_pesanan_item' => 'diproses'
                ]);
                
                // Redirect bersih agar URL tidak kotor panjang-panjang
                return redirect()->route('pesanan.index')->with('success', 'Pembayaran berhasil dikonfirmasi sistem!');
            }
        }

        $orders = DB::table('tb_transaksi')
            ->where('user_id', Auth::id())
            ->orderBy('tanggal_transaksi', 'desc')
            ->get();

        return view('pages.pesanan_index', compact('orders'));
    }

    // =================================================================
    // 11. DETAIL, LACAK, & AKSI PESANAN (ENTERPRISE LIFECYCLE)
    // =================================================================
    public function lacakPesanan(Request $request, $kode_invoice)
    {
        if (!Auth::check()) return redirect()->route('login');

        // AUTO-CATCH DARI JS POPUP MIDTRANS (onSuccess)
        if ($request->has('transaction_status') && in_array($request->transaction_status, ['settlement', 'capture'])) {
            $transaksi = DB::table('tb_transaksi')->where('kode_invoice', $kode_invoice)->first();
            
            if ($transaksi && $transaksi->status_pembayaran !== 'paid') {
                DB::table('tb_transaksi')->where('id', $transaksi->id)->update([
                    'status_pembayaran' => 'paid',
                    'status_pesanan_global' => 'diproses'
                    // Kolom updated_at dihapus dari sini
                ]);
                DB::table('tb_detail_transaksi')->where('transaksi_id', $transaksi->id)->update([
                    'status_pesanan_item' => 'diproses'
                ]);

                // Redirect bersih agar URL kembali rapi
                return redirect()->route('pesanan.lacak', $kode_invoice)->with('success', 'Pembayaran Lunas Terverifikasi!');
            }
        }

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

        // Dinamis Berdasarkan Status Global
        $trackingLogs = [];
        $trackingLogs[] = ['status' => 'Pesanan Dibuat', 'desc' => 'Pesanan berhasil dicatat oleh sistem.', 'time' => $order->tanggal_transaksi];
        
        if ($order->status_pembayaran == 'paid') {
            // Karena tidak ada updated_at, kita pakai tanggal_transaksi untuk log bayar
            $trackingLogs[] = ['status' => 'Pembayaran Terverifikasi', 'desc' => 'Dana telah diverifikasi oleh Midtrans.', 'time' => $order->tanggal_transaksi];
        }

        if (in_array($order->status_pesanan_global, ['diproses', 'siap_kirim', 'dikirim', 'sampai_tujuan', 'selesai'])) {
            $trackingLogs[] = ['status' => 'Pesanan Dikemas', 'desc' => 'Pesanan Anda sedang dikemas dan disiapkan oleh Penjual.', 'time' => $order->tanggal_transaksi];
        }

        if (in_array($order->status_pesanan_global, ['dikirim', 'sampai_tujuan', 'selesai'])) {
            $trackingLogs[] = ['status' => 'Dalam Pengiriman', 'desc' => 'Paket telah diserahkan ke pihak logistik.', 'time' => $order->tanggal_transaksi];
        }

        if (in_array($order->status_pesanan_global, ['selesai', 'sampai_tujuan'])) {
            $trackingLogs[] = ['status' => 'Pesanan Selesai', 'desc' => 'Pesanan telah diterima dengan baik.', 'time' => $order->tanggal_transaksi];
        }

        // Urutkan dari yang terbaru (Desc) untuk UI Timeline
        $trackingLogs = array_reverse($trackingLogs);

        // FITUR BARU: Cek barang mana yang sudah diulas
        $reviewed_items = DB::table('tb_review_produk')
            ->where('user_id', Auth::id())
            ->pluck('detail_transaksi_id')
            ->toArray();

        return view('pages.pesanan_lacak', compact('order', 'items', 'clientKey', 'trackingLogs', 'reviewed_items'));
    }

    // =================================================================
    // 12. PROFIL & PASSWORD
    // =================================================================
    public function profil()
    {
        if (!Auth::check()) { return redirect()->route('login'); }
        $user = Auth::user();
        
        $alamatUtama = DB::table('tb_user_alamat as ua')
            ->where('ua.user_id', $user->id)
            ->where('ua.is_utama', 1)
            ->first();

        $alamatLengkapFormatted = '-';
        if ($alamatUtama) {
            $alamatLengkapFormatted = $alamatUtama->alamat_lengkap . (!empty($alamatUtama->kode_pos) ? ' (Kode Pos: ' . $alamatUtama->kode_pos . ')' : '');
        }

        return view('pages.profil', compact('user', 'alamatLengkapFormatted'));
    }

    public function editProfil()
    {
        if (!Auth::check()) return redirect()->route('login');
        $user = Auth::user();
        
        $alamatUtama = DB::table('tb_user_alamat')->where('user_id', $user->id)->where('is_utama', 1)->first();
        
        return view('pages.edit_profil', compact('user', 'alamatUtama'));
    }

    public function updateProfil(Request $request)
    {
        if (!Auth::check()) return redirect()->route('login');

        $user = Auth::user();

        $request->validate([
            'nama' => 'required|string|max:255',
            'area_id' => 'required',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ], [
            'area_id.required' => 'Area / Kecamatan wajib dipilih dari dropdown Biteship yang muncul.',
            'foto.image' => 'File harus berupa gambar JPG/PNG.',
            'foto.max' => 'Ukuran foto maksimal 2MB.'
        ]);

        $fotoName = $user->profile_picture_url ?? null;
        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $fotoName = time() . '_avatar_' . $user->id . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('assets/uploads/avatars'), $fotoName);
        }

        DB::table('tb_user')->where('id', $user->id)->update([
            'nama' => $request->nama,
            'no_telepon' => $request->no_telepon,
            'tanggal_lahir' => $request->tanggal_lahir,
            'jenis_kelamin' => $request->jenis_kelamin,
            'profile_picture_url' => $fotoName,
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
            'area_id'          => $request->area_id, 
            'latitude'         => $request->latitude,
            'longitude'        => $request->longitude,
            'is_utama'         => 1,
        ];

        if ($alamatUtama) {
            DB::table('tb_user_alamat')->where('id', $alamatUtama->id)->update($dataAlamat);
        } else {
            DB::table('tb_user_alamat')->insert($dataAlamat);
        }

        return redirect()->route('profil.index')->with('success', 'Data Profil & Alamat berhasil disimpan permanen!');
    }

    public function gantiPassword() { return view('pages.ganti_password'); }

    public function sendOtpPassword(Request $request)
    {
        $user = Auth::user();
        $otp = rand(100000, 999999);
        
        session(['password_change_otp' => $otp, 'password_change_otp_time' => now()]);

        try {
            \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\OtpSecurityMail($otp, $user->nama));
            return response()->json(['success' => true, 'message' => 'OTP telah dikirim ke email Anda (' . $user->email . ')']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal mengirim email. Silakan coba lagi nanti.'], 500);
        }
    }

    public function verifyOtpPassword(Request $request)
    {
        $request->validate(['otp' => 'required|numeric']);
        
        $sessionOtp = session('password_change_otp');
        $sessionTime = session('password_change_otp_time');

        if (!$sessionOtp || !$sessionTime) {
            return response()->json(['success' => false, 'message' => 'Sesi OTP tidak ditemukan. Silakan kirim ulang OTP.']);
        }

        if (now()->diffInMinutes($sessionTime) > 5) {
            session()->forget(['password_change_otp', 'password_change_otp_time']);
            return response()->json(['success' => false, 'message' => 'OTP telah kedaluwarsa. Silakan kirim ulang OTP.']);
        }

        if ($request->otp != $sessionOtp) {
            return response()->json(['success' => false, 'message' => 'Kode OTP salah. Periksa kembali email Anda.']);
        }

        session(['password_change_otp_verified' => true]);
        return response()->json(['success' => true, 'message' => 'OTP berhasil diverifikasi!']);
    }

    public function updatePassword(Request $request)
    {
        if (!session('password_change_otp_verified')) {
            return back()->with('error', 'Akses ditolak: Anda harus memverifikasi OTP terlebih dahulu.');
        }

        $request->validate([
            'password_baru' => 'required|min:8|confirmed'
        ]);

        DB::table('tb_user')->where('id', Auth::id())->update(['password' => Hash::make($request->password_baru), 'updated_at' => now()]);
        
        session()->forget(['password_change_otp', 'password_change_otp_time', 'password_change_otp_verified']);

        return back()->with('success', 'Password berhasil diperbarui dengan aman!');
    }

    public function searchBiteshipAPI(Request $request)
    {
        $keyword = $request->query('q');
        
        if (empty($keyword) || strlen($keyword) < 3) {
            return response()->json(['areas' => []]);
        }

        $apiKey = DB::table('tb_pengaturan')->where('setting_nama', 'biteship_api_key')->value('setting_nilai');

        $response = \Illuminate\Support\Facades\Http::withHeaders([
            'Authorization' => $apiKey
        ])->get("https://api.biteship.com/v1/maps/areas", [
            'countries' => 'ID',
            'input' => $keyword,
            'type' => 'single'
        ]);

        if ($response->successful()) {
            return $response->json();
        }

        return response()->json(['areas' => []], 500);
    }

    // =================================================================
    // AKSI TRANSAKSI: Batalkan, Terima, & Komplain (Siklus Enterprise)
    // =================================================================

    // Aksi 1: Batalkan Pesanan (Hanya jika belum dibayar)
    public function batalkanPesanan(Request $request)
    {
        try {
            DB::beginTransaction();
            $order = DB::table('tb_transaksi')->where('kode_invoice', $request->order_id)->where('user_id', Auth::id())->first();
            
            if(!$order || $order->status_pembayaran === 'paid') {
                return back()->with('error', 'Pesanan tidak bisa dibatalkan karena sudah dibayar atau diproses.');
            }

            DB::table('tb_transaksi')->where('id', $order->id)->update(['status_pesanan_global' => 'dibatalkan', 'status_pembayaran' => 'failed']);
            
            // Kembalikan Stok Barang ke Sistem
            $items = DB::table('tb_detail_transaksi')->where('transaksi_id', $order->id)->get();
            foreach($items as $item) {
                DB::table('tb_detail_transaksi')->where('id', $item->id)->update(['status_pesanan_item' => 'dibatalkan']);
                DB::table('tb_barang')->where('id', $item->barang_id)->increment('stok', $item->jumlah);
            }

            DB::commit();
            return back()->with('success', 'Pesanan berhasil dibatalkan. Stok telah dikembalikan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan sistem saat membatalkan pesanan.');
        }
    }

    // Aksi 2: Selesaikan Pesanan (Customer konfirmasi barang diterima)
    public function terimaPesanan(Request $request)
    {
        try {
            DB::beginTransaction();
            $order = DB::table('tb_transaksi')->where('kode_invoice', $request->order_id)->where('user_id', Auth::id())->first();
            
            if(!$order || !in_array($order->status_pesanan_global, ['diproses', 'siap_kirim', 'dikirim'])) {
                return back()->with('error', 'Pesanan belum bisa diselesaikan saat ini.');
            }

            // Ubah Status Jadi Selesai
            DB::table('tb_transaksi')->where('id', $order->id)->update(['status_pesanan_global' => 'selesai']);
            
            // Update detail item dan TAMBAH SALDO SELLER (Pelepasan Dana Otomatis)
            $items = DB::table('tb_detail_transaksi')->where('transaksi_id', $order->id)->get();
            foreach($items as $item) {
                DB::table('tb_detail_transaksi')->where('id', $item->id)->update(['status_pesanan_item' => 'sampai_tujuan']);
                
                // Tambahkan uang subtotal belanjaan ke Dompet Toko / Penjual!
                DB::table('tb_toko')->where('id', $item->toko_id)->increment('saldo_aktif', $item->subtotal);
            }

            DB::commit();
            return back()->with('success', 'Terima kasih! Pesanan telah selesai dan dana diteruskan ke pihak Penjual.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan saat menyelesaikan pesanan.');
        }
    }

    // Aksi 3: Ajukan Pengembalian / Komplain
    public function ajukanPengembalian(Request $request)
    {
        $request->validate([
            'alasan' => 'required|string',
            'bukti_foto' => 'required|image'
        ]);

        // PROTEKSI SOFT BAN
        if (Auth::user()->is_banned) {
            return back()->with('error', 'Akun Anda sedang ditangguhkan. Anda tidak dapat mengajukan komplain atau ulasan.');
        }

        try {
            DB::beginTransaction();
            $order = DB::table('tb_transaksi')->where('kode_invoice', $request->order_id)->where('user_id', Auth::id())->first();

            // Upload Foto Bukti Kerusakan
            $fotoName = time() . '_retur.' . $request->file('bukti_foto')->extension();
            $request->file('bukti_foto')->move(public_path('assets/uploads/komplain'), $fotoName);

            $firstItem = DB::table('tb_detail_transaksi')->where('transaksi_id', $order->id)->first();

            // Catat Laporan Komplain
            DB::table('tb_komplain')->insert([
                'transaksi_id' => $order->id,
                'toko_id' => $firstItem->toko_id,
                'user_id' => Auth::id(),
                'alasan_komplain' => $request->alasan,
                'bukti_foto_1' => $fotoName,
                'status_komplain' => 'investigasi',
                'created_at' => now()
            ]);

            // Bekukan status pesanan jadi "Komplain"
            DB::table('tb_transaksi')->where('id', $order->id)->update(['status_pesanan_global' => 'komplain']);

            DB::commit();
            return back()->with('success', 'Pengajuan komplain/pengembalian berhasil dikirim ke Penjual & Admin POTA.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal mengajukan komplain.');
        }
    }

    /**
     * FITUR DEWA: Submit Review Pelanggan (Perfect Flow)
     */
    public function submitReview(Request $request)
    {
        $request->validate([
            'detail_id' => 'required|integer',
            'rating' => 'required|integer|min:1|max:5',
            'ulasan' => 'required|string|max:1000',
            'foto' => 'nullable|image|max:2048',
        ]);

        try {
            DB::beginTransaction();

            $item = DB::table('tb_detail_transaksi')->where('id', $request->detail_id)->first();
            if (!$item) return back()->with('error', 'Item tidak ditemukan.');

            $order = DB::table('tb_transaksi')->where('id', $item->transaksi_id)->where('user_id', Auth::id())->first();
            if (!$order || $order->status_pesanan_global !== 'selesai') {
                return back()->with('error', 'Anda hanya bisa memberikan ulasan untuk pesanan yang sudah selesai.');
            }

            // Batas waktu 14 hari
            $diff = now()->diffInDays(\Carbon\Carbon::parse($order->updated_at));
            if ($diff > 14) {
                return back()->with('error', 'Maaf, batas waktu pemberian ulasan (14 hari) telah habis.');
            }

            // Cek apakah sudah diulas?
            $exists = DB::table('tb_review_produk')->where('detail_transaksi_id', $item->id)->exists();
            if ($exists) return back()->with('error', 'Anda sudah memberikan ulasan untuk item ini.');

            // Auto-Moderasi (Filter Kata Kasar & Link)
            $badWords = ['anjing', 'babi', 'bangsat', 'kontol', 'memek', 'ngentot', 'http', 'www', '.com', '.id', '.net'];
            $isHidden = false;
            foreach ($badWords as $word) {
                if (stripos(strtolower($request->ulasan), $word) !== false) {
                    $isHidden = true;
                    break;
                }
            }

            // Upload Foto
            $fotoName = null;
            if ($request->hasFile('foto')) {
                $fotoName = time() . '_rev_' . Auth::id() . '.' . $request->file('foto')->extension();
                $request->file('foto')->move(public_path('assets/uploads/reviews'), $fotoName);
            }

            // Simpan Review
            $isRewarded = false;
            if ($fotoName) {
                // Berikan Reward Poin (50 Poin)
                DB::table('tb_user')->where('id', Auth::id())->increment('poin', 50);
                $isRewarded = true;
            }

            DB::table('tb_review_produk')->insert([
                'detail_transaksi_id' => $item->id,
                'barang_id' => $item->barang_id,
                'user_id' => Auth::id(),
                'rating' => $request->rating,
                'ulasan' => $request->ulasan,
                'gambar_ulasan' => $fotoName,
                'is_hidden' => $isHidden,
                'is_rewarded' => $isRewarded,
                'created_at' => now()
            ]);

            // Hitung Ulang Rating Produk & Toko
            $this->recalculateRating($item->barang_id, $item->toko_id);

            DB::commit();

            $msg = 'Ulasan berhasil disimpan!';
            if ($isRewarded) $msg .= ' Selamat! Anda mendapatkan 50 Poin karena menyertakan foto.';
            if ($isHidden) $msg .= ' (Ulasan Anda sedang ditinjau karena mengandung kata sensitif)';

            return back()->with('success', $msg);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal mengirim ulasan: ' . $e->getMessage());
        }
    }

    private function recalculateRating($barang_id, $toko_id)
    {
        // 1. Update Produk
        $statsBarang = DB::table('tb_review_produk')
            ->where('barang_id', $barang_id)
            ->where('is_hidden', 0)
            ->selectRaw('AVG(rating) as avg_rating, COUNT(id) as total_rev')
            ->first();

        DB::table('tb_barang')->where('id', $barang_id)->update([
            'rating_rata' => $statsBarang->avg_rating ?? 0,
            'jumlah_ulasan' => $statsBarang->total_rev ?? 0
        ]);

        // 2. Update Toko
        $statsToko = DB::table('tb_review_produk as rp')
            ->join('tb_barang as b', 'rp.barang_id', '=', 'b.id')
            ->where('b.toko_id', $toko_id)
            ->where('rp.is_hidden', 0)
            ->selectRaw('AVG(rp.rating) as avg_rating, COUNT(rp.id) as total_rev')
            ->first();

        DB::table('tb_toko')->where('id', $toko_id)->update([
            'rating_toko' => $statsToko->avg_rating ?? 0,
            'jumlah_ulasan_toko' => $statsToko->total_rev ?? 0
        ]);
    }
    // API: Toggle Follow / Unfollow Toko
    public function toggleFollow(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['status' => 'error', 'message' => 'Silakan login terlebih dahulu.'], 401);
        }

        $userId = Auth::id();
        $tokoId = $request->toko_id;

        // Cek apakah sudah follow?
        $existing = DB::table('tb_toko_follower')->where('toko_id', $tokoId)->where('user_id', $userId)->first();

        if ($existing) {
            // Jika sudah, maka UNFOLLOW (Hapus)
            DB::table('tb_toko_follower')->where('id', $existing->id)->delete();
            $action = 'unfollowed';
        } else {
            // Jika belum, maka FOLLOW (Tambah)
            DB::table('tb_toko_follower')->insert([
                'toko_id' => $tokoId,
                'user_id' => $userId,
                'created_at' => now()
            ]);
            $action = 'followed';
        }

        // Ambil jumlah follower terbaru untuk di-update di layar secara realtime
        $totalFollowers = DB::table('tb_toko_follower')->where('toko_id', $tokoId)->count();

        return response()->json([
            'status' => 'success',
            'action' => $action,
            'total_followers' => $totalFollowers
        ]);
    }
}