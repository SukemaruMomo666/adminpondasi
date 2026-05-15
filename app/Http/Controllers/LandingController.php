<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class LandingController extends Controller
{
    // ==========================================
    // 1. UNTUK WEBSITE (BLADE VIEW)
    // ==========================================
    public function index(Request $request)
    {
        $settingsData = DB::table('tb_pengaturan')->get();
        $settings = [];
        foreach ($settingsData as $s) {
            $settings[$s->setting_nama] = $s->setting_nilai;
        }

        $user = Auth::user();
        $areaId = null;
        
        // ---------------------------------------------------------
        // 1. PRIORITAS UTAMA: Ambil dari GPS Perangkat Real-Time
        // (Dikirim via URL parameter ?lat=...&lng=...)
        // ---------------------------------------------------------
        $userLat = $request->input('lat');
        $userLng = $request->input('lng');

        // 2. Jika GPS Browser belum diizinkan/dikirim, ambil dari alamat profil user yang login
        if (!$userLat || !$userLng) {
            if ($user) {
                $alamatUtama = DB::table('tb_user_alamat')
                    ->where('user_id', $user->id)
                    ->where('is_utama', 1)
                    ->first();

                if ($alamatUtama && $alamatUtama->latitude && $alamatUtama->longitude) {
                    $areaId = $alamatUtama->area_id; // Format Biteship
                    $userLat = $alamatUtama->latitude;
                    $userLng = $alamatUtama->longitude;
                }
            }
        }

        // 3. Jika Guest & GPS ditolak, set Default Koordinat (Subang)
        if (!$userLat || !$userLng) {
            $userLat = -6.571589;
            $userLng = 107.758736;
            $areaId = 'IDNP83719';
        }

        // Banners
        $promoBanners = [
            (object)['title' => 'Pekan Diskon Baja', 'desc' => 'Dapatkan potongan harga khusus untuk pembelian baja ringan volume besar minggu ini.', 'img' => 'https://images.unsplash.com/photo-1504307651254-35680f356dfd?q=80&w=1000&auto=format&fit=crop', 'link' => '#'],
            (object)['title' => 'Gratis Ongkir Se-Jawa', 'desc' => 'Subsidi ongkos kirim hingga Rp500.000 untuk minimal transaksi 50 Juta.', 'img' => 'https://images.unsplash.com/photo-1587293852726-70cdb56c2866?q=80&w=1000&auto=format&fit=crop', 'link' => '#'],
            (object)['title' => 'Mitra Baru: Semen Tiga Roda', 'desc' => 'Kini tersedia semen kualitas premium langsung dari pabrik dengan harga termurah.', 'img' => 'https://images.unsplash.com/photo-1503387762-592deb58ef4e?q=80&w=1000&auto=format&fit=crop', 'link' => '#']
        ];

        // Kategori
        $kategoriUtama = DB::table('tb_kategori')->whereNull('parent_id')->get();
        $kategoriAnak = DB::table('tb_kategori')->whereNotNull('parent_id')->get();
        foreach ($kategoriUtama as $utama) {
            $utama->subkategori = $kategoriAnak->where('parent_id', $utama->id)->values();
        }
        $categories = $kategoriUtama;

        // ---------------------------------------------------------
        // SECTION 1: MITRA TOKO TERPOPULER (GLOBAL NASIONAL)
        // ---------------------------------------------------------
        $listToko = DB::table('tb_toko as t')
            // FIX: Mengambil kolom kota secara langsung, BUKAN area_id
            ->select('t.id', 't.nama_toko', 't.slug', 't.logo_toko', 't.banner_toko', 't.tier_toko', 't.kota')
            ->selectSub(function ($query) {
                $query->from('tb_barang')
                    ->whereColumn('toko_id', 't.id')
                    ->where('is_active', 1)
                    ->where('status_moderasi', 'approved')
                    ->selectRaw('count(id)');
            }, 'jumlah_produk_aktif')
            ->where('t.status', 'active')
            ->where('t.status_operasional', 'Buka')
            ->orderByDesc('jumlah_produk_aktif')
            ->limit(4)->get();

        foreach ($listToko as $toko) {
            $toko->initials = $this->getStoreInitials($toko->nama_toko);
            $toko->color = $this->getStoreColor($toko->nama_toko);
        }

        // ---------------------------------------------------------
        // SECTION 2: TOKO TERDEKAT (HYPER LOCAL 1 KOTA / 50 KM)
        // ---------------------------------------------------------
        $listTokoTerdekat = DB::table('tb_toko as t')
            // FIX: Mengambil kolom kota secara langsung, BUKAN area_id
            ->select('t.id', 't.nama_toko', 't.slug', 't.logo_toko', 't.banner_toko', 't.tier_toko', 't.kota')
            ->selectRaw('(6371 * acos(cos(radians(?)) * cos(radians(t.latitude)) * cos(radians(t.longitude) - radians(?)) + sin(radians(?)) * sin(radians(t.latitude)))) AS jarak_km', [$userLat, $userLng, $userLat])
            ->where('t.status', 'active')
            ->where('t.status_operasional', 'Buka')
            ->whereNotNull('t.latitude')
            ->having('jarak_km', '<=', 50) // Batas Radius 1 Kota (50 KM)
            ->orderBy('jarak_km', 'ASC')
            ->limit(4)->get();

        // Fallback: Jika tidak ada toko dalam radius 50km, ambilkan yang paling dekat saja agar "TAMPIL TERUS"
        if ($listTokoTerdekat->isEmpty()) {
            $listTokoTerdekat = DB::table('tb_toko as t')
                // FIX: Mengambil kolom kota secara langsung, BUKAN area_id
                ->select('t.id', 't.nama_toko', 't.slug', 't.logo_toko', 't.banner_toko', 't.tier_toko', 't.kota')
                ->selectRaw('(6371 * acos(cos(radians(?)) * cos(radians(t.latitude)) * cos(radians(t.longitude) - radians(?)) + sin(radians(?)) * sin(radians(t.latitude)))) AS jarak_km', [$userLat, $userLng, $userLat])
                ->where('t.status', 'active')
                ->where('t.status_operasional', 'Buka')
                ->whereNotNull('t.latitude')
                ->orderBy('jarak_km', 'ASC')
                ->limit(4)->get();
        }

        foreach ($listTokoTerdekat as $toko) {
            $toko->initials = $this->getStoreInitials($toko->nama_toko);
            $toko->color = $this->getStoreColor($toko->nama_toko);
        }

        // ---------------------------------------------------------
        // SECTION 3: KAGET DISKON (FLASH SALE + DISKON BEBAS TOKO)
        // ---------------------------------------------------------
        $flashSaleProducts = collect();
        $flashSaleEndTime = Carbon::now()->endOfDay()->toDateTimeString();

        // A. Ambil dari Event Flash Sale Resmi (Jika Ada)
        $fsEvent = DB::table('tb_flash_sale_events')
            ->where('is_active', 1)
            ->where('tanggal_mulai', '<=', now())
            ->where('tanggal_berakhir', '>=', now())
            ->first();

        if ($fsEvent) {
            $flashSaleEndTime = $fsEvent->tanggal_berakhir;
            $fsItems = DB::table('tb_flash_sale_produk as fsp')
                ->join('tb_barang as b', 'fsp.barang_id', '=', 'b.id')
                ->join('tb_toko as t', 'b.toko_id', '=', 't.id')
                ->select(
                    'b.id', 'b.nama_barang', 'b.harga', 'b.gambar_utama', 'b.tipe_diskon', 'b.nilai_diskon',
                    // FIX: Mengambil kolom kota secara langsung
                    'fsp.harga_flash_sale', 'fsp.stok_flash_sale', 't.kota as kota_toko'
                )
                ->selectSub(function ($q) {
                    $q->from('tb_detail_transaksi')
                      ->whereColumn('barang_id', 'b.id')
                      ->where('status_pesanan_item', 'sampai_tujuan')
                      ->selectRaw('COALESCE(SUM(jumlah), 0)');
                }, 'stok_terjual')
                ->where('fsp.event_id', $fsEvent->id)
                ->where('fsp.status_moderasi', 'approved')
                ->where('b.is_active', 1)
                ->get();
            $flashSaleProducts = $flashSaleProducts->merge($fsItems);
        }

        // B. Ambil dari Diskon Bebas Toko di Sekitar (Hyper Local) untuk melengkapi agar TAMPIL TERUS
        $localDiscounts = DB::table('tb_barang as b')
            ->join('tb_toko as t', 'b.toko_id', '=', 't.id')
            ->select(
                'b.id', 'b.nama_barang', 'b.harga', 'b.gambar_utama', 'b.tipe_diskon', 'b.nilai_diskon',
                // FIX: Mengambil kolom kota secara langsung
                DB::raw('NULL as harga_flash_sale'), DB::raw('100 as stok_flash_sale'), 't.kota as kota_toko'
            )
            ->selectRaw('(6371 * acos(cos(radians(?)) * cos(radians(t.latitude)) * cos(radians(t.longitude) - radians(?)) + sin(radians(?)) * sin(radians(t.latitude)))) AS jarak_km', [$userLat, $userLng, $userLat])
            ->selectSub(function ($q) {
                $q->from('tb_detail_transaksi')
                  ->whereColumn('barang_id', 'b.id')
                  ->where('status_pesanan_item', 'sampai_tujuan')
                  ->selectRaw('COALESCE(SUM(jumlah), 0)');
            }, 'stok_terjual')
            ->where('b.is_active', 1)
            ->where('b.status_moderasi', 'approved')
            ->whereNotNull('t.latitude')
            ->where('b.nilai_diskon', '>', 0)
            ->having('jarak_km', '<=', 50)
            ->orderBy('jarak_km', 'ASC')
            ->limit(10)
            ->get();

        // Gabungkan Flash Sale + Diskon Bebas, hapus duplikat
        $flashSaleProducts = $flashSaleProducts->merge($localDiscounts)->unique('id')->take(10);

        // ---------------------------------------------------------
        // SECTION 4 & 5: PRODUK GLOBAL & MATERIAL LOKAL (HYPER LOCAL)
        // ---------------------------------------------------------
        $listProdukNasional = $this->getBestSellingProducts(null, null, null);
        $listProdukLokal = $this->getBestSellingProducts($userLat, $userLng, $areaId);

        return view('landing', compact(
            'settings', 'promoBanners', 'categories', 
            'listToko', 'listTokoTerdekat', 
            'listProdukLokal', 'listProdukNasional', 
            'flashSaleProducts', 'flashSaleEndTime', 'user'
        ));
    }


    // ==========================================
    // 2. UNTUK MOBILE APP (REACT NATIVE API)
    // ==========================================
    public function getApiData(Request $request)
    {
        try {
            $settings = DB::table('tb_pengaturan')->get()->pluck('setting_nilai', 'setting_nama');

            $banners = [];
            if (!empty($settings['hero_image'])) {
                $banners[] = [
                    'title' => $settings['hero_title'] ?? 'PondasiKita',
                    'desc' => $settings['hero_subtitle'] ?? '',
                    'img' => asset('storage/' . $settings['hero_image'])
                ];
            }
            for ($i = 1; $i <= 4; $i++) {
                if (!empty($settings["hero_image_$i"])) {
                    $banners[] = [
                        'title' => $settings["hero_title_$i"] ?? '',
                        'desc' => $settings["hero_subtitle_$i"] ?? '',
                        'img' => asset('storage/' . $settings["hero_image_$i"])
                    ];
                }
            }

            $categories = DB::table('tb_kategori')->whereNull('parent_id')->get();
            foreach ($categories as $cat) {
                $cat->subkategori = DB::table('tb_kategori')->where('parent_id', $cat->id)->get();
            }

            $user = Auth::guard('sanctum')->user();
            $areaId = null;
            
            // Prioritas GPS dari API Client
            $userLat = $request->input('lat');
            $userLng = $request->input('lng');

            if (!$userLat || !$userLng) {
                if ($user) {
                    $alamatUtama = DB::table('tb_user_alamat')->where('user_id', $user->id)->where('is_utama', 1)->first();
                    if ($alamatUtama) {
                        $areaId = $alamatUtama->area_id;
                        $userLat = $alamatUtama->latitude;
                        $userLng = $alamatUtama->longitude;
                    }
                }
            }

            if (!$userLat || !$userLng) {
                $userLat = -6.571589;
                $userLng = 107.758736;
                $areaId = 'IDNP83719';
            }

            // Top Stores Global
            $stores = DB::table('tb_toko as t')
                // FIX: Mengambil kolom kota secara langsung
                ->select('t.id', 't.nama_toko', 't.logo_toko', 't.banner_toko', 't.tier_toko', 't.kota')
                ->where('t.status', 'active')
                ->where('t.status_operasional', 'Buka')
                ->limit(4)->get();

            // Nearby Stores Hyper Local
            $nearbyStores = DB::table('tb_toko as t')
                // FIX: Mengambil kolom kota secara langsung
                ->select('t.id', 't.nama_toko', 't.logo_toko', 't.banner_toko', 't.tier_toko', 't.kota')
                ->selectRaw('(6371 * acos(cos(radians(?)) * cos(radians(t.latitude)) * cos(radians(t.longitude) - radians(?)) + sin(radians(?)) * sin(radians(t.latitude)))) AS jarak_km', [$userLat, $userLng, $userLat])
                ->where('t.status', 'active')
                ->where('t.status_operasional', 'Buka')
                ->whereNotNull('t.latitude')
                ->having('jarak_km', '<=', 50)
                ->orderBy('jarak_km', 'ASC')
                ->limit(4)->get();

            $mergeStores = array_merge($stores->toArray(), $nearbyStores->toArray());
            foreach ($mergeStores as $toko) {
                $toko->initials = $this->getStoreInitials($toko->nama_toko);
                $toko->color = $this->getStoreColor($toko->nama_toko);
                $toko->logo_toko = $toko->logo_toko ? asset('assets/uploads/logos/' . $toko->logo_toko) : null;
                $toko->banner_toko = $toko->banner_toko ? asset('assets/uploads/banners/' . $toko->banner_toko) : null;
            }

            $localProducts = $this->getBestSellingProducts($userLat, $userLng, $areaId, true);
            $nationalProducts = $this->getBestSellingProducts(null, null, null, true);

            return response()->json([
                'status' => 'success',
                'data' => [
                    'settings' => $settings,
                    'banners' => $banners,
                    'categories' => $categories,
                    'stores' => [
                        'global_list' => $stores,
                        'nearby_list' => $nearbyStores
                    ],
                    'products' => [
                        'local' => $localProducts,
                        'national' => $nationalProducts
                    ]
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }


    // ==========================================
    // 3. PRIVATE HELPER FUNCTIONS
    // ==========================================
    private function getBestSellingProducts($userLat = null, $userLng = null, $areaId = null, $isApi = false)
    {
        $query = DB::table('tb_barang as b')
            ->join('tb_toko as t', 'b.toko_id', '=', 't.id')
            ->select(
                'b.id', 'b.nama_barang', 'b.harga', 'b.gambar_utama', 'b.tipe_diskon', 'b.nilai_diskon',
                // FIX: Mengambil kolom kota secara langsung
                't.nama_toko', 't.slug as slug_toko', 't.kota as kota_toko'
            )
            ->where('b.is_active', 1)
            ->where('b.status_moderasi', 'approved');

        if ($userLat && $userLng) {
            // Filter jarak GPS Real-Time
            $query->whereNotNull('t.latitude')
                  ->whereNotNull('t.longitude')
                  ->selectRaw('(6371 * acos(cos(radians(?)) * cos(radians(t.latitude)) * cos(radians(t.longitude) - radians(?)) + sin(radians(?)) * sin(radians(t.latitude)))) AS jarak_km', [$userLat, $userLng, $userLat])
                  ->orderBy('jarak_km', 'ASC'); 
        } elseif ($areaId) {
            $query->where('t.area_id', $areaId);
        }

        $query->selectSub(function ($q) {
            $q->from('tb_detail_transaksi')
              ->whereColumn('barang_id', 'b.id')
              ->where('status_pesanan_item', 'sampai_tujuan')
              ->selectRaw('COALESCE(SUM(jumlah), 0)');
        }, 'stok_terjual');

        // Jika GPS aktif, urutkan berdasarkan jarak terdekat dulu, lalu penjualan
        if ($userLat && $userLng) {
            $query->orderByDesc('stok_terjual');
        } else {
            $query->orderByDesc('stok_terjual');
        }

        $products = $query->limit(10)->get();

        // Tambahkan path image khusus untuk API
        if ($isApi) {
            foreach($products as $p) {
                $p->gambar_utama = asset('assets/uploads/products/' . ($p->gambar_utama ?? 'default.jpg'));
            }
        }

        return $products;
    }

    private function getStoreInitials($nama)
    {
        if (empty($nama)) return "TK";
        $words = explode(" ", $nama);
        $acronym = "";
        foreach ($words as $w) {
            $acronym .= mb_substr($w, 0, 1);
        }
        return strtoupper(substr($acronym, 0, 2));
    }

    private function getStoreColor($nama)
    {
        $colors = ['#e53935', '#d81b60', '#8e24aa', '#5e35b1', '#3949ab', '#1e88e5', '#039be5', '#00acc1', '#00897b', '#43a047', '#7cb342', '#c0ca33', '#fdd835', '#ffb300', '#fb8c00', '#f4511e'];
        $index = crc32($nama) % count($colors);
        return $colors[$index];
    }
}