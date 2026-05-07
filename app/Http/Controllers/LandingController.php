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
    public function index()
    {
        $settingsData = DB::table('tb_pengaturan')->get();
        $settings = [];
        foreach ($settingsData as $s) {
            $settings[$s->setting_nama] = $s->setting_nilai;
        }

        $user = Auth::user();
        $cityId = 0;
        $districtId = 0;
        $tokoSectionTitle = "Mitra Toko Populer";

        if ($user) {
            $alamatUtama = DB::table('tb_user_alamat')
                ->where('user_id', $user->id)
                ->where('is_utama', 1)
                ->first();

            if ($alamatUtama) {
                $cityId = $alamatUtama->city_id;
                $districtId = $alamatUtama->district_id;
            }
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

        // Toko
        $queryToko = DB::table('tb_toko as t')
            ->join('cities as c', 't.city_id', '=', 'c.id')
            ->select('t.id', 't.nama_toko', 't.slug', 't.logo_toko', 't.banner_toko', 't.tier_toko', 'c.name as kota')
            ->selectSub(function ($query) {
                $query->from('tb_barang')
                    ->whereColumn('toko_id', 't.id')
                    ->where('is_active', 1)
                    ->where('status_moderasi', 'approved')
                    ->selectRaw('count(id)');
            }, 'jumlah_produk_aktif')
            ->where('t.status', 'active')
            ->where('t.status_operasional', 'Buka');

        if ($cityId > 0) {
            $tokoSectionTitle = "Toko Terdekat di Wilayah Anda";
            $queryToko->where(function($q) use ($cityId, $districtId) {
                $q->where('t.city_id', $cityId)->orWhere('t.district_id', $districtId);
            });
            $queryToko->orderByDesc('jumlah_produk_aktif')->orderBy('t.nama_toko');
        } else {
            $queryToko->orderByDesc('jumlah_produk_aktif');
        }

        $listToko = $queryToko->limit(4)->get();
        foreach ($listToko as $toko) {
            $toko->initials = $this->getStoreInitials($toko->nama_toko);
            $toko->color = $this->getStoreColor($toko->nama_toko);
        }

        // Produk
        $listProdukLokal = $cityId > 0 ? $this->getBestSellingProducts($cityId, $districtId) : [];
        $listProdukNasional = $this->getBestSellingProducts();

        // Flash Sale
        $flashSaleEvent = DB::table('tb_flash_sale_events')
            ->where('is_active', 1)
            ->where('tanggal_mulai', '<=', Carbon::now())
            ->where('tanggal_berakhir', '>=', Carbon::now())
            ->first();

        $flashSaleProducts = [];
        $flashSaleEndTime = null;

        if ($flashSaleEvent) {
            $flashSaleEndTime = $flashSaleEvent->tanggal_berakhir;
            $flashSaleProducts = DB::table('tb_flash_sale_produk as fsp')
                ->join('tb_barang as b', 'fsp.barang_id', '=', 'b.id')
                ->select('b.id', 'b.nama_barang', 'b.harga', 'b.gambar_utama', 'fsp.harga_flash_sale', 'fsp.stok_flash_sale')
                ->where('fsp.event_id', $flashSaleEvent->id)
                ->where('fsp.status_moderasi', 'approved')
                ->where('b.is_active', 1)
                ->limit(10)->get();
        }

        return view('landing', compact(
            'settings', 'promoBanners', 'categories', 'listToko', 'tokoSectionTitle',
            'listProdukLokal', 'listProdukNasional', 'flashSaleProducts', 'flashSaleEndTime', 'user'
        ));
    }


    // ==========================================
    // 2. UNTUK MOBILE APP (REACT NATIVE API)
    // ==========================================
    public function getApiData(Request $request)
    {
        try {
            // Settings
            $settings = DB::table('tb_pengaturan')->get()->pluck('setting_nilai', 'setting_nama');

            // Banners (Format full URL)
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

            // Categories
            $categories = DB::table('tb_kategori')->whereNull('parent_id')->get();
            foreach ($categories as $cat) {
                $cat->subkategori = DB::table('tb_kategori')->where('parent_id', $cat->id)->get();
            }

            // Flash Sale
            $flashSaleEvent = DB::table('tb_flash_sale_events')
                ->where('is_active', 1)
                ->where('tanggal_mulai', '<=', now())
                ->where('tanggal_berakhir', '>=', now())
                ->first();

            $flashSaleProducts = [];
            if ($flashSaleEvent) {
                $flashSaleProducts = DB::table('tb_flash_sale_produk as fsp')
                    ->join('tb_barang as b', 'fsp.barang_id', '=', 'b.id')
                    ->select('b.id', 'b.nama_barang', 'b.harga', 'b.gambar_utama', 'fsp.harga_flash_sale', 'fsp.stok_flash_sale')
                    ->where('fsp.event_id', $flashSaleEvent->id)
                    ->where('fsp.status_moderasi', 'approved')
                    ->where('b.is_active', 1)
                    ->limit(10)->get();

                // Format URL Gambar
                foreach($flashSaleProducts as $fs) {
                    $fs->gambar_utama = asset('assets/uploads/products/' . ($fs->gambar_utama ?? 'default.jpg'));
                }
            }

            // User & Location untuk Mobile API
            // Menggunakan guard sanctum jika user login via API
            $user = Auth::guard('sanctum')->user();
            $cityId = 0;
            $districtId = 0;
            $tokoSectionTitle = "Mitra Toko Populer";

            if ($user) {
                $alamatUtama = DB::table('tb_user_alamat')->where('user_id', $user->id)->where('is_utama', 1)->first();
                if ($alamatUtama) {
                    $cityId = $alamatUtama->city_id;
                    $districtId = $alamatUtama->district_id;
                }
            }

            // Stores
            $queryToko = DB::table('tb_toko as t')
                ->join('cities as c', 't.city_id', '=', 'c.id')
                ->select('t.id', 't.nama_toko', 't.logo_toko', 't.banner_toko', 't.tier_toko', 'c.name as kota')
                ->where('t.status', 'active')
                ->where('t.status_operasional', 'Buka');

            if ($cityId > 0) {
                $tokoSectionTitle = "Toko Terdekat di Wilayah Anda";
                $queryToko->where(function($q) use ($cityId, $districtId) {
                    $q->where('t.city_id', $cityId)->orWhere('t.district_id', $districtId);
                });
            }

            $stores = $queryToko->limit(4)->get();
            foreach ($stores as $toko) {
                $toko->initials = $this->getStoreInitials($toko->nama_toko);
                $toko->color = $this->getStoreColor($toko->nama_toko);
                $toko->logo_toko = $toko->logo_toko ? asset('assets/uploads/logos/' . $toko->logo_toko) : null;
                $toko->banner_toko = $toko->banner_toko ? asset('assets/uploads/banners/' . $toko->banner_toko) : null;
            }

            // Products
            $localProducts = $cityId > 0 ? $this->getBestSellingProducts($cityId, $districtId, true) : [];
            $nationalProducts = $this->getBestSellingProducts(null, null, true);

            return response()->json([
                'status' => 'success',
                'data' => [
                    'settings' => $settings,
                    'banners' => $banners,
                    'categories' => $categories,
                    'flash_sale' => [
                        'event' => $flashSaleEvent,
                        'products' => $flashSaleProducts
                    ],
                    'stores' => [
                        'title' => $tokoSectionTitle,
                        'list' => $stores
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
    private function getBestSellingProducts($cityId = null, $districtId = null, $isApi = false)
    {
        $query = DB::table('tb_barang as b')
            ->join('tb_toko as t', 'b.toko_id', '=', 't.id')
            ->leftJoin('cities as c', 't.city_id', '=', 'c.id')
            ->select(
                'b.id', 'b.nama_barang', 'b.harga', 'b.gambar_utama', 'b.tipe_diskon', 'b.nilai_diskon',
                't.nama_toko', 't.slug as slug_toko', 'c.name as kota_toko'
            )
            ->where('b.is_active', 1)
            ->where('b.status_moderasi', 'approved');

        if ($cityId) {
            $query->where(function($q) use ($cityId, $districtId) {
                $q->where('t.city_id', $cityId)
                  ->orWhere('t.district_id', $districtId);
            });
        }

        $query->selectSub(function ($q) {
            $q->from('tb_detail_transaksi')
              ->whereColumn('barang_id', 'b.id')
              ->where('status_pesanan_item', 'sampai_tujuan')
              ->selectRaw('COALESCE(SUM(jumlah), 0)');
        }, 'stok_terjual');

        $products = $query->orderByDesc('stok_terjual')->limit(10)->get();

        // Format URL gambar jika dipanggil oleh API Mobile
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
