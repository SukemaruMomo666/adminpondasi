<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SellerController extends Controller
{
    /**
     * Helper untuk selalu mendapatkan toko yang valid
     */
    private function getToko()
    {
        $toko = DB::table('tb_toko')->where('user_id', Auth::id())->first();
        if (!$toko) {
            abort(403, 'Akses Ditolak: Anda belum memiliki data Toko.');
        }
        return $toko;
    }

    // =========================================================================
    // 1. HALAMAN DASHBOARD SELLER
    // =========================================================================
    public function index()
    {
        $user = Auth::user();

        $toko = DB::table('tb_toko')->where('user_id', $user->id)->first();

        if (!$toko) {
            return redirect()->route('home')->with('error', 'Anda belum memiliki toko.');
        }

        $tokoId = $toko->id;

        $totalPenjualan = DB::table('tb_detail_transaksi')
            ->where('toko_id', $tokoId)
            ->sum('subtotal');

        $totalPesanan = DB::table('tb_detail_transaksi')
            ->where('toko_id', $tokoId)
            ->distinct('transaksi_id')
            ->count('transaksi_id');

        $totalItemTerjual = DB::table('tb_detail_transaksi')
            ->where('toko_id', $tokoId)
            ->sum('jumlah');

        $totalProdukAktif = DB::table('tb_barang')
            ->where('toko_id', $tokoId)
            ->where('is_active', 1)
            ->count();

        $tahunSekarang = date('Y');
        $penjualanTahunan = array_fill(1, 12, 0);

        $dataGrafik = DB::table('tb_detail_transaksi as d')
            ->join('tb_transaksi as t', 'd.transaksi_id', '=', 't.id')
            ->selectRaw('MONTH(t.tanggal_transaksi) as bulan, SUM(d.subtotal) as total')
            ->where('d.toko_id', $tokoId)
            ->whereYear('t.tanggal_transaksi', $tahunSekarang)
            ->groupBy('bulan')
            ->get();

        foreach ($dataGrafik as $data) {
            $penjualanTahunan[$data->bulan] = (float) $data->total;
        }

        $labelsBulan = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'];

        $topProduk = DB::table('tb_detail_transaksi as d')
            ->join('tb_barang as b', 'd.barang_id', '=', 'b.id')
            ->select('b.nama_barang', DB::raw('SUM(d.jumlah) as total_terjual'))
            ->where('d.toko_id', $tokoId)
            ->groupBy('d.barang_id', 'b.nama_barang')
            ->orderByDesc('total_terjual')
            ->limit(5)
            ->get();

        $topProdukLabels = $topProduk->pluck('nama_barang');
        $topProdukData = $topProduk->pluck('total_terjual');

        return view('seller.dashboard', compact(
            'toko', 'totalPenjualan', 'totalPesanan', 'totalItemTerjual', 'totalProdukAktif',
            'labelsBulan', 'penjualanTahunan', 'topProdukLabels', 'topProdukData', 'tahunSekarang'
        ));
    }

    // =========================================================================
    // TAMPILAN PROFIL TOKO
    // =========================================================================
    public function profile()
    {
        $toko = $this->getToko();
        return view('seller.shop.profile', compact('toko'));
    }

    // =========================================================================
    // UPDATE PROFILE (FIXED BITESHIP & LEAFLET)
    // =========================================================================
    public function updateProfile(Request $request)
    {
        $toko = $this->getToko();

        $request->validate([
            'nama_toko'       => 'required|string|max:100',
            'slogan'          => 'nullable|string|max:255',
            'deskripsi_toko'  => 'nullable|string',
            'catatan_toko'    => 'nullable|string',
            'kebijakan_retur' => 'nullable|string',
            'no_telepon'      => 'required|string|max:20',
            'alamat_toko'     => 'required|string', 
            'area_id'         => 'required|string|max:255', 
            'kota'            => 'nullable|string|max:100', 
            'kode_pos'        => 'nullable|string|max:20',
            'latitude'        => 'required|string|max:50',
            'longitude'       => 'required|string|max:50',
            'logo_toko'       => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'banner_toko'     => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'dokumen_nib'     => 'nullable|file|mimes:pdf,jpeg,png,jpg,webp|max:5120',
            'dokumen_npwp'    => 'nullable|file|mimes:pdf,jpeg,png,jpg,webp|max:5120',
        ]);

        $dataUpdate = [
            'nama_toko'       => $request->nama_toko,
            'slogan'          => $request->slogan,
            'deskripsi_toko'  => $request->deskripsi_toko,
            'catatan_toko'    => $request->catatan_toko,
            'kebijakan_retur' => $request->kebijakan_retur,
            'telepon_toko'    => $request->no_telepon,
            'alamat_toko'     => $request->alamat_toko,
            'area_id'         => $request->area_id, 
            'kota'            => $request->kota,        
            'kode_pos'        => $request->kode_pos,
            'latitude'        => $request->latitude,
            'longitude'       => $request->longitude,
            'updated_at'      => now()
        ];

        if (\Illuminate\Support\Facades\Schema::hasColumn('tb_toko', 'province_id')) {
            $dataUpdate['province_id'] = null;
            $dataUpdate['city_id'] = null;
            $dataUpdate['district_id'] = null;
        }

        if ($request->hasFile('logo_toko')) {
            $logo = $request->file('logo_toko');
            $logoName = 'logo_' . \Illuminate\Support\Str::random(10) . '.' . $logo->getClientOriginalExtension();

            if (!empty($toko->logo_toko)) {
                $oldPath = public_path('assets/uploads/logos/' . $toko->logo_toko);
                if (\Illuminate\Support\Facades\File::exists($oldPath)) { \Illuminate\Support\Facades\File::delete($oldPath); }
            }

            if(!\Illuminate\Support\Facades\File::exists(public_path('assets/uploads/logos'))) { \Illuminate\Support\Facades\File::makeDirectory(public_path('assets/uploads/logos'), 0777, true); }
            $logo->move(public_path('assets/uploads/logos'), $logoName);
            $dataUpdate['logo_toko'] = $logoName;
        }

        if ($request->hasFile('banner_toko')) {
            $banner = $request->file('banner_toko');
            $bannerName = 'banner_' . \Illuminate\Support\Str::random(10) . '.' . $banner->getClientOriginalExtension();

            if (!empty($toko->banner_toko)) {
                $oldBannerPath = public_path('assets/uploads/banners/' . $toko->banner_toko);
                if (\Illuminate\Support\Facades\File::exists($oldBannerPath)) { \Illuminate\Support\Facades\File::delete($oldBannerPath); }
            }

            if(!\Illuminate\Support\Facades\File::exists(public_path('assets/uploads/banners'))) { \Illuminate\Support\Facades\File::makeDirectory(public_path('assets/uploads/banners'), 0777, true); }
            $banner->move(public_path('assets/uploads/banners'), $bannerName);
            $dataUpdate['banner_toko'] = $bannerName;
        }

        $legalPath = public_path('assets/uploads/legalitas');
        if(!\Illuminate\Support\Facades\File::exists($legalPath)) { \Illuminate\Support\Facades\File::makeDirectory($legalPath, 0777, true); }

        if ($request->hasFile('dokumen_nib')) {
            $nib = $request->file('dokumen_nib');
            $nibName = 'NIB_' . $toko->id . '_' . \Illuminate\Support\Str::random(5) . '.' . $nib->getClientOriginalExtension();
            if (!empty($toko->dokumen_nib) && \Illuminate\Support\Facades\File::exists($legalPath . '/' . $toko->dokumen_nib)) { \Illuminate\Support\Facades\File::delete($legalPath . '/' . $toko->dokumen_nib); }
            $nib->move($legalPath, $nibName);
            $dataUpdate['dokumen_nib'] = $nibName;
        }

        if ($request->hasFile('dokumen_npwp')) {
            $npwp = $request->file('dokumen_npwp');
            $npwpName = 'NPWP_' . $toko->id . '_' . \Illuminate\Support\Str::random(5) . '.' . $npwp->getClientOriginalExtension();
            if (!empty($toko->dokumen_npwp) && \Illuminate\Support\Facades\File::exists($legalPath . '/' . $toko->dokumen_npwp)) { \Illuminate\Support\Facades\File::delete($legalPath . '/' . $toko->dokumen_npwp); }
            $npwp->move($legalPath, $npwpName);
            $dataUpdate['dokumen_npwp'] = $npwpName;
        }

        \Illuminate\Support\Facades\DB::table('tb_toko')->where('id', $toko->id)->update($dataUpdate);

        return redirect()->route('seller.shop.profile')->with('success', 'Profil Toko, Lokasi Peta, & Wilayah Ekspedisi berhasil diperbarui!');
    }

    // =========================================================================
    // 2. HALAMAN MANAJEMEN PESANAN MASUK
    // =========================================================================
    public function pesanan(Request $request)
    {
        $user = Auth::user();

        $toko = DB::table('tb_toko')->where('user_id', $user->id)->first();
        if (!$toko) {
            return redirect()->route('seller.dashboard')->with('error', 'Data toko tidak ditemukan.');
        }

        $pesananRaw = DB::table('tb_detail_transaksi as d')
            ->join('tb_transaksi as t', 'd.transaksi_id', '=', 't.id')
            ->join('tb_barang as b', 'd.barang_id', '=', 'b.id')
            ->join('tb_user as u', 't.user_id', '=', 'u.id')
            ->where('d.toko_id', $toko->id)
            ->select(
                'd.id as detail_id', 'd.jumlah', 'd.subtotal', 'd.status_pesanan_item',
                't.kode_invoice', 't.tanggal_transaksi', 't.sumber_transaksi',
                'b.nama_barang', 'b.gambar_utama',
                'u.nama as nama_pelanggan'
            )
            ->orderBy('t.tanggal_transaksi', 'desc')
            ->get();

        $groupedOrders = $pesananRaw->groupBy('kode_invoice');

        $statusMap = [
            'Semua' => 'Semua',
            'Belum Bayar' => 'menunggu_pembayaran',
            'Perlu Diproses' => 'diproses',
            'Siap Kirim' => 'siap_kirim',
            'Dikirim' => 'dikirim',
            'Selesai' => 'sampai_tujuan',
            'Dibatalkan' => 'dibatalkan'
        ];

        $currentFilter = $request->query('status', '');

        return view('seller.pesanan', compact('groupedOrders', 'statusMap', 'currentFilter'));
    }

    public function updateOrderStatus(Request $request)
    {
        $request->validate([
            'detail_id' => 'required|integer',
            'status_baru' => 'required|string'
        ]);

        $toko = DB::table('tb_toko')->where('user_id', Auth::id())->first();

        DB::table('tb_detail_transaksi')
            ->where('id', $request->detail_id)
            ->where('toko_id', $toko->id)
            ->update(['status_pesanan_item' => $request->status_baru]);

        return redirect()->back()->with('success', 'Status pesanan berhasil diperbarui!');
    }

    public function massUpdateOrderStatus(Request $request)
    {
        if (!$request->has('detail_ids') || empty($request->detail_ids)) {
            return redirect()->back()->with('error', 'Pilih setidaknya satu pesanan untuk diproses.');
        }

        $toko = DB::table('tb_toko')->where('user_id', Auth::id())->first();

        DB::table('tb_detail_transaksi')
            ->whereIn('id', $request->detail_ids)
            ->where('toko_id', $toko->id)
            ->update(['status_pesanan_item' => 'dikirim']);

        return redirect()->back()->with('success', count($request->detail_ids) . ' Pesanan berhasil diproses ke pengiriman!');
    }

    // =========================================================================
    // 3. HALAMAN PENGEMBALIAN PESANAN (RETURN/REFUND)
    // =========================================================================
    public function pengembalian(Request $request)
    {
        $user = Auth::user();

        $toko = DB::table('tb_toko')->where('user_id', $user->id)->first();
        if (!$toko) {
            return redirect()->route('seller.dashboard')->with('error', 'Data toko tidak ditemukan.');
        }

        $currentFilter = $request->query('status', '');

        if(DB::getSchemaBuilder()->hasTable('tb_komplain')) {
            $query = DB::table('tb_komplain as k')
                ->join('tb_transaksi as t', 'k.transaksi_id', '=', 't.id')
                ->join('tb_user as u', 'k.user_id', '=', 'u.id')
                ->where('k.toko_id', $toko->id)
                ->select(
                    'k.id as id_return',
                    'k.alasan_komplain as alasan',
                    'k.bukti_foto_1 as bukti_foto',
                    'k.status_komplain as status',
                    'k.created_at as tanggal_pengajuan',
                    't.kode_invoice',
                    'u.nama as nama_pelanggan',
                    DB::raw("'Material Retur' as nama_barang"),
                    DB::raw("'default.jpg' as gambar_utama"),
                    DB::raw("1 as jumlah"),
                    't.total_final as total_pengembalian'
                )
                ->orderBy('k.created_at', 'desc');

            if ($currentFilter != '') {
                if($currentFilter == 'menunggu_respon') {
                    $query->whereIn('k.status_komplain', ['investigasi', 'menunggu_tanggapan_toko']);
                } elseif ($currentFilter == 'disetujui') {
                    $query->where('k.status_komplain', 'refund_pembeli');
                } elseif ($currentFilter == 'ditolak') {
                    $query->whereIn('k.status_komplain', ['teruskan_dana_toko', 'selesai']);
                }
            }

            $returnsRaw = $query->get();

            $returns = $returnsRaw->map(function($item) {
                if(in_array($item->status, ['investigasi', 'menunggu_tanggapan_toko'])) {
                    $item->status = 'menunggu_respon';
                } elseif ($item->status == 'refund_pembeli') {
                    $item->status = 'disetujui';
                } elseif (in_array($item->status, ['teruskan_dana_toko', 'selesai'])) {
                    $item->status = 'ditolak';
                }
                return $item;
            });
        } else {
            $returns = collect();
        }

        return view('seller.pengembalian', compact('returns', 'currentFilter'));
    }

    public function processPengembalian(Request $request)
    {
        $request->validate([
            'id_return' => 'required',
            'action' => 'required|in:approve,reject'
        ]);

        $toko = DB::table('tb_toko')->where('user_id', Auth::id())->first();

        $statusBaru = $request->action == 'approve' ? 'refund_pembeli' : 'teruskan_dana_toko';

        if(DB::getSchemaBuilder()->hasTable('tb_komplain')) {
            DB::table('tb_komplain')
                ->where('id', $request->id_return)
                ->where('toko_id', $toko->id)
                ->update(['status_komplain' => $statusBaru, 'updated_at' => now()]);
        }

        $msg = $request->action == 'approve'
               ? 'Pengembalian dana disetujui. Dana akan direfund ke pembeli.'
               : 'Komplain ditolak. Dana transaksi akan diteruskan ke saldo toko Anda.';

        return redirect()->back()->with('success', $msg);
    }

// =========================================================================
    // 4. PENGATURAN PENGIRIMAN (LOGISTIK B2B) - SINKRONISASI SELLER & BITESHIP
    // =========================================================================
    public function pengaturanPengiriman()
    {
        $user = Auth::user();

        $toko = DB::table('tb_toko')->where('user_id', $user->id)->first();
        if (!$toko) {
            return redirect()->route('seller.dashboard')->with('error', 'Data toko tidak ditemukan.');
        }

        $kurirList = DB::table('tb_kurir_toko')
            ->where('toko_id', $toko->id)
            ->orderBy('tipe_kurir', 'asc')
            ->orderBy('nama_kurir')
            ->get();

        $tipeOrder = [
            'TOKO' => 'Armada Toko (Khusus Material Berat/Curah)',
            'PIHAK_KETIGA' => 'Kurir Ekspedisi (Barang Ringan/Kecil)'
        ];

        $groupedKurir = [];
        foreach ($kurirList as $kurir) {
            $groupedKurir[$kurir->tipe_kurir][] = $kurir;
        }

        $settingsData = DB::table('tb_pengaturan')->get();
        $adminSettings = [];
        foreach ($settingsData as $row) {
            $adminSettings[$row->setting_nama] = $row->setting_nilai;
        }

        $admin_active_couriers_string = $adminSettings['api_active_couriers'] ?? '';
        $admin_active_couriers = empty($admin_active_couriers_string) ? [] : explode(',', $admin_active_couriers_string);

        $courier_dictionary = [
            'jne'      => ['name' => 'JNE Express', 'type' => 'Reguler, Kargo & Truking', 'icon' => 'mdi-truck-fast'],
            'jnt'      => ['name' => 'J&T Express', 'type' => 'Reguler & Kargo', 'icon' => 'mdi-truck-delivery'],
            'sicepat'  => ['name' => 'SiCepat', 'type' => 'Reguler, Kargo & Sameday', 'icon' => 'mdi-lightning-bolt'],
            'pos'      => ['name' => 'POS Indonesia', 'type' => 'Reguler & Kargo', 'icon' => 'mdi-postbox'],
            'tiki'     => ['name' => 'TIKI', 'type' => 'Reguler & Sameday', 'icon' => 'mdi-truck-outline'],
            'ninja'    => ['name' => 'Ninja Xpress', 'type' => 'Reguler', 'icon' => 'mdi-ninja'],
            'lion'     => ['name' => 'Lion Parcel', 'type' => 'Reguler & Kargo', 'icon' => 'mdi-airplane-takeoff'],
            'anteraja' => ['name' => 'AnterAja', 'type' => 'Reguler, Kargo & Sameday', 'icon' => 'mdi-truck-check'],
            'paxel'    => ['name' => 'Paxel', 'type' => 'Sameday & Frozen', 'icon' => 'mdi-package-variant'],
            'gosend'   => ['name' => 'GoSend', 'type' => 'Instant & Sameday', 'icon' => 'mdi-motorbike'],
            'grab'     => ['name' => 'GrabExpress', 'type' => 'Instant & Sameday', 'icon' => 'mdi-motorbike'],
            'lalamove' => ['name' => 'Lalamove', 'type' => 'Instant & Armada Besar', 'icon' => 'mdi-truck-flatbed'],
            'borzo'    => ['name' => 'Borzo', 'type' => 'Instant Delivery', 'icon' => 'mdi-motorbike'],
            'indah'    => ['name' => 'Indah Logistik', 'type' => 'Kargo Berat', 'icon' => 'mdi-truck-flatbed'],
            'wahana'   => ['name' => 'Wahana Express', 'type' => 'Kargo & Ekonomi', 'icon' => 'mdi-weight-kilogram'],
            'sap'      => ['name' => 'SAP Express', 'type' => 'Reguler & Kargo', 'icon' => 'mdi-map-marker-path'],
            'ide'      => ['name' => 'ID Express', 'type' => 'Reguler', 'icon' => 'mdi-truck-fast-outline'],
            'sentral'  => ['name' => 'Sentral Cargo', 'type' => 'Kargo Domestik', 'icon' => 'mdi-package-variant-closed'],
            'rex'      => ['name' => 'REX Express', 'type' => 'Kargo & Dokumen', 'icon' => 'mdi-truck-cargo-container'],
            'rpx'      => ['name' => 'RPX', 'type' => 'Reguler & Kargo', 'icon' => 'mdi-truck-delivery'],
        ];

        $master_couriers = [];
        foreach ($admin_active_couriers as $code) {
            if (isset($courier_dictionary[$code])) {
                $master_couriers[$code] = $courier_dictionary[$code];
            }
        }

        // ====================================================================
        // FIX: GUNAKAN NAMA KOLOM `active_api_couriers` SESUAI DATABASE ABANG
        // Sekaligus handle format JSON sisaan kode lama jika ada
        // ====================================================================
        $seller_active_couriers_raw = $toko->active_api_couriers ?? '';
        
        if (is_string($seller_active_couriers_raw) && str_starts_with($seller_active_couriers_raw, '[')) {
            // Jika format lama masih berupa JSON ["jne", "jnt"]
            $seller_active_couriers = json_decode($seller_active_couriers_raw, true) ?? [];
        } else {
            // Jika format baru string koma "jne,jnt"
            $seller_active_couriers = empty($seller_active_couriers_raw) ? [] : explode(',', $seller_active_couriers_raw);
        }

        return view('seller.pengaturan_pengiriman', compact(
            'groupedKurir', 'tipeOrder', 'toko', 'adminSettings', 'admin_active_couriers', 'master_couriers', 'seller_active_couriers'
        ));
    }

    /**
     * MENGELOLA PENYIMPANAN PENGATURAN LOGISTIK & LAYANAN KUSTOM SELLER
     */
    public function storePengiriman(Request $request)
    {
        $toko = DB::table('tb_toko')->where('user_id', Auth::id())->first();

        if (!$toko) {
            return redirect()->back()->with('error', 'Toko tidak ditemukan.');
        }

        // =========================================================================
        // A. LOGIKA SIMPAN PENGATURAN UTAMA (Form Kiri & Kanan API)
        // =========================================================================
        if ($request->action === 'save_preferences' || empty($request->action)) {
            
            // 1. Simpan Ekspedisi API (Biteship) yang dichecklist Seller
            $couriers = $request->input('seller_active_couriers', []);
            $couriers = array_filter($couriers, function($value) {
                return $value !== 'NONE_SELECTED_HACK';
            });
            $couriersString = implode(',', array_values($couriers));

            // 2. Simpan Preferences Armada Sendiri (Pickup, Truk Mandiri, dll)
            $preferences = $request->input('preferences', []);
            if (!isset($preferences['bopis'])) $preferences['bopis'] = '0';
            if (!isset($preferences['custom_fleet'])) $preferences['custom_fleet'] = '0';

            // 3. Update Database Toko
            // FIX FATAL: Nama kolom disesuaikan menjadi `active_api_couriers`
            DB::table('tb_toko')->where('id', $toko->id)->update([
                'active_api_couriers'   => $couriersString, // <--- INI BIANG KEROKNYA TADI BANG
                'logistics_preferences' => json_encode($preferences),
                'updated_at'            => now()
            ]);

            return redirect()->back()->with('success', 'Konfigurasi logistik dan kurir toko berhasil disinkronkan!');
        }

        // =========================================================================
        // B. LOGIKA TAMBAH LAYANAN KUSTOM ARMADA SENDIRI (Modal Form)
        // =========================================================================
        if ($request->action === 'tambah') {
            $request->validate([
                'nama_kurir'     => 'required|string|max:100',
                'estimasi_waktu' => 'required|string|max:50',
                'biaya'          => 'required|numeric|min:0',
            ]);

            DB::table('tb_kurir_toko')->insert([
                'toko_id'        => $toko->id,
                'tipe_kurir'     => 'CUSTOM',
                'nama_kurir'     => $request->nama_kurir,
                'estimasi_waktu' => $request->estimasi_waktu,
                'biaya'          => $request->biaya,
                'is_active'      => 1,
                'created_at'     => now(),
                'updated_at'     => now()
            ]);

            return redirect()->back()->with('success', 'Layanan khusus berhasil ditambahkan!');
        }

        // =========================================================================
        // C. LOGIKA EDIT LAYANAN KUSTOM ARMADA SENDIRI (Modal Form)
        // =========================================================================
        if ($request->action === 'update') {
            $request->validate([
                'kurir_id'       => 'required|integer',
                'nama_kurir'     => 'required|string|max:100',
                'estimasi_waktu' => 'required|string|max:50',
                'biaya'          => 'required|numeric|min:0',
            ]);

            DB::table('tb_kurir_toko')
                ->where('id', $request->kurir_id)
                ->where('toko_id', $toko->id)
                ->update([
                    'nama_kurir'     => $request->nama_kurir,
                    'estimasi_waktu' => $request->estimasi_waktu,
                    'biaya'          => $request->biaya,
                    'updated_at'     => now()
                ]);

            return redirect()->back()->with('success', 'Layanan khusus berhasil diperbarui!');
        }

        return redirect()->back()->with('error', 'Aksi tidak dikenali.');
    }

    // =========================================================================
    // 5. PUSAT PROMOSI (MANAJEMEN HARGA CORET)
    // =========================================================================
    public function promosi(Request $request)
    {
        $user = Auth::user();
        $toko = DB::table('tb_toko')->where('user_id', $user->id)->first();

        if (!$toko) {
            return redirect()->route('seller.dashboard')->with('error', 'Data toko tidak ditemukan.');
        }

        $now = now();

        $stats = [
            'semua' => DB::table('tb_barang')
                ->where('toko_id', $toko->id)
                ->whereNotNull('nilai_diskon')
                ->where('nilai_diskon', '>', 0)
                ->count(),

            'aktif' => DB::table('tb_barang')
                ->where('toko_id', $toko->id)
                ->whereNotNull('nilai_diskon')
                ->where('nilai_diskon', '>', 0)
                ->where('diskon_mulai', '<=', $now)
                ->where('diskon_berakhir', '>=', $now)
                ->count(),

            'akan_datang' => DB::table('tb_barang')
                ->where('toko_id', $toko->id)
                ->whereNotNull('nilai_diskon')
                ->where('nilai_diskon', '>', 0)
                ->where('diskon_mulai', '>', $now)
                ->count(),

            'tidak_aktif' => DB::table('tb_barang')
                ->where('toko_id', $toko->id)
                ->where(function($q) use ($now) {
                    $q->whereNull('nilai_diskon')
                      ->orWhere('nilai_diskon', 0)
                      ->orWhere('diskon_berakhir', '<', $now);
                })->count(),
        ];

        $query = DB::table('tb_barang')->where('toko_id', $toko->id);

        if($request->has('search') && $request->search != '') {
            $query->where('nama_barang', 'like', '%'.$request->search.'%');
        }

        $currentTab = $request->query('tab', 'semua');

        if ($currentTab == 'aktif') {
            $query->whereNotNull('nilai_diskon')->where('nilai_diskon', '>', 0)
                  ->where('diskon_mulai', '<=', $now)->where('diskon_berakhir', '>=', $now);
        } elseif ($currentTab == 'akan_datang') {
            $query->whereNotNull('nilai_diskon')->where('nilai_diskon', '>', 0)
                  ->where('diskon_mulai', '>', $now);
        } elseif ($currentTab == 'tidak_aktif') {
            $query->where(function($q) use ($now) {
                $q->whereNull('nilai_diskon')
                  ->orWhere('nilai_diskon', 0)
                  ->orWhere('diskon_berakhir', '<', $now);
            });
        }

        $products = $query->orderBy('updated_at', 'desc')->paginate(10);

        return view('seller.promosi', compact('products', 'currentTab', 'stats'));
    }

    public function updateDiscount(Request $request)
    {
        $request->validate([
            'product_ids' => 'required|array',
            'tipe_diskon' => 'nullable|in:NOMINAL,PERSEN',
            'nilai_diskon' => 'nullable|numeric|min:0',
            'diskon_mulai' => 'nullable|date',
            'diskon_berakhir' => 'nullable|date|after:diskon_mulai'
        ]);

        $toko = DB::table('tb_toko')->where('user_id', Auth::id())->first();

        if(empty($request->nilai_diskon) || $request->nilai_diskon == 0) {
            DB::table('tb_barang')
                ->whereIn('id', $request->product_ids)
                ->where('toko_id', $toko->id)
                ->update([
                    'tipe_diskon' => null, 'nilai_diskon' => null,
                    'diskon_mulai' => null, 'diskon_berakhir' => null,
                    'updated_at' => now()
                ]);
            return response()->json(['status' => 'success', 'message' => 'Diskon berhasil dihapus / dinonaktifkan.']);
        }

        DB::table('tb_barang')
            ->whereIn('id', $request->product_ids)
            ->where('toko_id', $toko->id)
            ->update([
                'tipe_diskon' => $request->tipe_diskon,
                'nilai_diskon' => $request->nilai_diskon,
                'diskon_mulai' => $request->diskon_mulai,
                'diskon_berakhir' => $request->diskon_berakhir,
                'updated_at' => now()
            ]);

        return response()->json(['status' => 'success', 'message' => 'Promo Harga Coret berhasil diterapkan.']);
    }

    // =========================================================================
    // 6. HALAMAN VOUCHER TOKO (ENTERPRISE LOGIC)
    // =========================================================================
    public function voucher(Request $request)
    {
        $user = Auth::user();
        $toko = DB::table('tb_toko')->where('user_id', $user->id)->first();

        if (!$toko) {
            return redirect()->route('seller.dashboard')->with('error', 'Data toko tidak ditemukan.');
        }

        $stats = [
            'aktif' => DB::table('vouchers')->where('toko_id', $toko->id)->where('status', 'AKTIF')->count(),
            'terpakai' => DB::table('vouchers')->where('toko_id', $toko->id)->sum('kuota_terpakai') ?? 0,
        ];

        // Filter dasar: WAJIB milik toko ini
        $query = DB::table('vouchers')->where('toko_id', $toko->id);

        if($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('kode_voucher', 'like', '%'.$search.'%')
                  ->orWhere('deskripsi', 'like', '%'.$search.'%');
            });
        }

        $currentTab = $request->query('tab', 'semua');
        
        if($currentTab == 'aktif') {
            $query->where('status', 'AKTIF')->where('tanggal_berakhir', '>=', now());
        } elseif($currentTab == 'habis') {
            $query->whereRaw('kuota_terpakai >= kuota');
        } elseif($currentTab == 'nonaktif') {
            $query->where(function($q) {
                $q->where('status', 'TIDAK_AKTIF')
                  ->orWhere('tanggal_berakhir', '<', now());
            });
        }

        $voucher_list = $query->orderBy('id', 'desc')->paginate(10);

        return view('seller.voucher', compact('voucher_list', 'stats', 'currentTab'));
    }

    public function storeVoucher(Request $request)
    {
        $request->validate([
            'kode_voucher' => 'required|string|max:12|unique:vouchers,kode_voucher',
            'deskripsi' => 'required|string|max:255',
            'tipe_diskon' => 'required|in:RUPIAH,PERSEN',
            'nilai_diskon' => 'required|numeric|min:1',
            'min_pembelian' => 'required|numeric|min:0',
            'kuota' => 'required|integer|min:1',
            'tanggal_mulai' => 'required|date',
            'tanggal_berakhir' => 'required|date|after:tanggal_mulai',
        ]);

        $toko = DB::table('tb_toko')->where('user_id', Auth::id())->first();

        $maksDiskon = null;
        if ($request->tipe_diskon == 'PERSEN') {
            if ($request->nilai_diskon > 100) return back()->with('error', 'Diskon persen tidak boleh lebih dari 100%');
            $maksDiskon = $request->maks_diskon > 0 ? $request->maks_diskon : null;
        }

        DB::table('vouchers')->insert([
            'toko_id' => $toko->id,
            'kode_voucher' => strtoupper($request->kode_voucher),
            'deskripsi' => $request->deskripsi,
            'tipe_diskon' => $request->tipe_diskon,
            'nilai_diskon' => $request->nilai_diskon,
            'maks_diskon' => $maksDiskon,
            'min_pembelian' => $request->min_pembelian,
            'kuota' => $request->kuota,
            'kuota_terpakai' => 0,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_berakhir' => $request->tanggal_berakhir,
            'status' => 'AKTIF'
        ]);

        return redirect()->route('seller.promotion.vouchers')->with('success', 'Voucher berhasil diterbitkan!');
    }

    public function toggleVoucher(Request $request)
    {
        $toko = DB::table('tb_toko')->where('user_id', Auth::id())->first();

        $status_baru = $request->is_active ? 'AKTIF' : 'TIDAK_AKTIF';

        $updated = DB::table('vouchers')
            ->where('id', $request->voucher_id)
            ->where('toko_id', $toko->id)
            ->update(['status' => $status_baru]);

        if($updated) return response()->json(['status' => 'success']);
        return response()->json(['status' => 'error'], 400);
    }

    public function destroyVoucher($id)
    {
        $user = Auth::user();
        $toko = DB::table('tb_toko')->where('user_id', $user->id)->first();

        if (!$toko) {
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json(['status' => 'error', 'message' => 'Data toko tidak ditemukan.']);
            }
            return redirect()->route('seller.dashboard')->with('error', 'Data toko tidak ditemukan.');
        }

        $voucher = DB::table('vouchers')->where('id', $id)->first();

        if (!$voucher) {
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json(['status' => 'error', 'message' => 'Voucher tidak ada di database.']);
            }
            return redirect()->back()->with('error', 'Voucher tidak ada di database.');
        }

        if ($voucher->toko_id != $toko->id) {
            $pesanError = "Ditolak! Voucher ini tercatat milik Toko ID: " . ($voucher->toko_id ?? 'KOSONG') . ", sedangkan Toko Anda adalah ID: " . $toko->id;
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json(['status' => 'error', 'message' => $pesanError]);
            }
            return redirect()->back()->with('error', $pesanError);
        }

        DB::table('vouchers')->where('id', $id)->delete();

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json(['status' => 'success', 'message' => 'Voucher berhasil dihapus!']);
        }

        return redirect()->back()->with('success', 'Voucher berhasil dihapus secara permanen!');
    }

    // =========================================================================
    // 7. MANAJEMEN CHAT (ENTERPRISE GRADE)
    // =========================================================================
    public function chat()
    {
        return view('seller.chat');
    }

    public function getChatList()
    {
        $user = Auth::user();
        $toko = DB::table('tb_toko')->where('user_id', $user->id)->first();

        if (!$toko) return response()->json(['status' => 'error', 'message' => 'Toko tidak ditemukan']);

        $chats = DB::table('chats as c')
            ->join('tb_user as u', 'c.customer_id', '=', 'u.id')
            ->where('c.toko_id', $toko->id)
            ->select(
                'c.id',
                'u.nama as nama_pelanggan',
                DB::raw('(SELECT message_text FROM messages m WHERE m.chat_id = c.id ORDER BY timestamp DESC LIMIT 1) as last_message'),
                DB::raw('(SELECT timestamp FROM messages m WHERE m.chat_id = c.id ORDER BY timestamp DESC LIMIT 1) as last_time')
            )
            ->orderByRaw('last_time DESC NULLS LAST')
            ->get();

        $formattedChats = $chats->map(function($chat) {
            if ($chat->last_time) {
                $date = \Carbon\Carbon::parse($chat->last_time);
                $chat->time_display = $date->isToday() ? $date->format('H:i') : $date->format('d/m/y');
            } else {
                $chat->time_display = '';
            }
            return $chat;
        });

        return response()->json(['status' => 'success', 'data' => $formattedChats]);
    }

    public function getMessages($chatId)
    {
        $user = Auth::user();
        $toko = DB::table('tb_toko')->where('user_id', $user->id)->first();

        $validChat = DB::table('chats')->where('id', $chatId)->where('toko_id', $toko->id)->exists();
        if (!$validChat) return response()->json(['status' => 'error', 'message' => 'Unauthorized']);

        $messages = DB::table('messages')
            ->where('chat_id', $chatId)
            ->orderBy('timestamp', 'asc')
            ->get();

        $formattedMessages = $messages->map(function($msg) use ($user) {
            $text = $msg->message_text;
            $type = 'text';
            $fileName = '';

            if (filter_var($text, FILTER_VALIDATE_URL)) {
                if (preg_match('/\.(jpg|jpeg|png|gif|webp)$/i', $text)) {
                    $type = 'image';
                } elseif (preg_match('/\.webm$/i', $text)) {
                    $type = 'audio';
                } elseif (strpos($text, 'chat_media_') !== false) {
                    $type = 'file';
                    $fileName = basename($text);
                }
            }

            return [
                'id' => $msg->id,
                'is_mine' => ($msg->sender_id == $user->id),
                'text' => $text,
                'type' => $type, 
                'fileName' => $fileName,
                'time' => \Carbon\Carbon::parse($msg->timestamp)->format('H:i')
            ];
        });

        return response()->json(['status' => 'success', 'data' => $formattedMessages]);
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'chat_id' => 'required|integer',
            'message' => 'required' 
        ]);

        $user = Auth::user();
        $toko = DB::table('tb_toko')->where('user_id', $user->id)->first();

        if (!$toko) {
            return response()->json(['status' => 'error', 'message' => 'Toko tidak ditemukan'], 404);
        }

        $validChat = DB::table('chats')->where('id', $request->chat_id)->where('toko_id', $toko->id)->exists();
        if (!$validChat) return response()->json(['status' => 'error'], 403);

        $messageText = $request->message;

        if ($request->has('type') && $request->type != 'text') {
            if (preg_match('/^data:(\w+\/[\w+-.]+);base64,/', $request->message, $matches)) {
                $base64Data = substr($request->message, strpos($request->message, ',') + 1);
                $fileData = base64_decode($base64Data);

                $extension = 'png'; 
                if ($request->type == 'audio') {
                    $extension = 'webm'; 
                } elseif ($request->type == 'file' && $request->has('file_name')) {
                    $extension = pathinfo($request->file_name, PATHINFO_EXTENSION);
                } elseif ($request->type == 'image') {
                    $extension = explode('/', $matches[1])[1]; 
                    if($extension == 'jpeg') $extension = 'jpg';
                }

                $fileName = 'chat_media_' . time() . '_' . uniqid() . '.' . $extension;
                $folderPath = 'assets/uploads/chat';
                $destinationPath = public_path($folderPath);

                if (!file_exists($destinationPath)) {
                    mkdir($destinationPath, 0775, true);
                }

                if (file_put_contents($destinationPath . '/' . $fileName, $fileData)) {
                    $messageText = url($folderPath . '/' . $fileName);
                } else {
                    return response()->json(['status' => 'error', 'message' => 'Gagal menyimpan file ke server.'], 500);
                }
            }
        }

        DB::table('messages')->insert([
            'chat_id' => $request->chat_id,
            'sender_id' => $user->id,
            'message_text' => $messageText, 
            'timestamp' => now()
        ]);

        return response()->json(['status' => 'success']);
    }

    // =========================================================================
    // 8. POINT OF SALE (KASIR)
    // =========================================================================
    public function pos()
    {
        return view('seller.pos');
    }

    public function getPosCategories()
    {
        $toko = DB::table('tb_toko')->where('user_id', Auth::id())->first();

        $categories = DB::table('tb_kategori')
            ->whereIn('id', function($query) use ($toko) {
                $query->select('kategori_id')
                      ->from('tb_barang')
                      ->where('toko_id', $toko->id);
            })->get();

        return response()->json($categories);
    }

    public function getPosProducts()
    {
        $toko = DB::table('tb_toko')->where('user_id', Auth::id())->first();

        $products = DB::table('tb_barang')
            ->where('toko_id', $toko->id)
            ->select('id', 'kode_barang', 'nama_barang', 'harga', 'stok', 'kategori_id', 'gambar_utama')
            ->where('stok', '>', 0)
            ->orderBy('nama_barang', 'asc')
            ->get();

        return response()->json($products);
    }

    public function processPosCheckout(Request $request)
    {
        $userId = $request->user_id;

        if (empty($userId)) {
            return response()->json(['status' => 'error', 'message' => 'ID Validasi tidak ditemukan! Coba refresh halaman.'], 400);
        }

        $toko = DB::table('tb_toko')->where('user_id', $userId)->first();
        if (!$toko) {
            return response()->json(['status' => 'error', 'message' => 'Toko tidak valid atau tidak ditemukan.'], 404);
        }

        try {
            DB::beginTransaction();

            $invoice = 'POS-' . strtoupper(substr($toko->nama_toko, 0, 3)) . '-' . date('ymdHis');
            
            $transaksiId = DB::table('tb_transaksi')->insertGetId([
                'kode_invoice'          => $invoice,
                'sumber_transaksi'      => 'OFFLINE',
                'user_id'               => $userId, 
                'total_harga_produk'    => $request->total,
                'total_final'           => $request->total,
                'bayar'                 => $request->amount_paid,
                'kembali'               => $request->amount_paid - $request->total,
                'metode_pembayaran'     => $request->payment_method,
                'status_pembayaran'     => 'paid',
                'status_pesanan_global' => 'selesai',
                'tanggal_transaksi'     => now(),
                'catatan'               => 'Pembelian POS (' . $toko->nama_toko . ') | Dilayani Kasir: ' . $request->kasir_name
            ]);

            foreach ($request->cart as $item) {
                DB::table('tb_detail_transaksi')->insert([
                    'transaksi_id'               => $transaksiId,
                    'toko_id'                    => $toko->id,
                    'barang_id'                  => $item['id'],
                    'nama_barang_saat_transaksi' => $item['nama_barang'],
                    'harga_saat_transaksi'       => $item['harga'],
                    'jumlah'                     => $item['qty'],
                    'subtotal'                   => $item['harga'] * $item['qty'],
                    'metode_pengiriman'          => 'AMBIL_DI_TOKO',
                    'status_pesanan_item'        => 'sampai_tujuan',
                ]);

                DB::table('tb_barang')->where('id', $item['id'])->decrement('stok', $item['qty']);
                
                DB::table('tb_stok_histori')->insert([
                    'barang_id'       => $item['id'],
                    'jumlah'          => -$item['qty'],
                    'tipe_pergerakan' => 'sale',
                    'referensi'       => $invoice,
                    'keterangan'      => 'Penjualan via POS',
                    'created_at'      => now()
                ]);
            }

            DB::commit();

            return response()->json(['status' => 'success', 'message' => 'Transaksi berhasil!', 'invoice' => $invoice]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => 'Gagal menyimpan transaksi: ' . $e->getMessage()], 500);
        }
    }

    public function printStruk($invoice)
    {
        $userId = Auth::id();
        $toko = DB::table('tb_toko')->where('user_id', $userId)->first();

        if (!$toko) {
            abort(404, 'Toko tidak ditemukan.');
        }

        $transaksi = DB::table('tb_transaksi')
            ->where('kode_invoice', $invoice)
            ->where('user_id', $userId) 
            ->first();

        if (!$transaksi) {
            abort(404, 'Transaksi tidak ditemukan.');
        }

        $details = DB::table('tb_detail_transaksi')
            ->where('transaksi_id', $transaksi->id)
            ->get();

        return view('seller.pos_print', compact('toko', 'transaksi', 'details'));
    }

    // =========================================================================
    // 9. PENILAIAN TOKO (REVIEWS - ENTERPRISE GRADE)
    // =========================================================================
    public function reviews(Request $request)
    {
        $user = Auth::user();
        $toko = DB::table('tb_toko')->where('user_id', $user->id)->first();
        
        if (!$toko) {
            return redirect()->route('seller.dashboard')->with('error', 'Data toko tidak ditemukan.');
        }

        $summary = DB::table('tb_toko_review')
            ->where('toko_id', $toko->id)
            ->selectRaw('AVG(rating) as avg_rating, COUNT(id) as total_reviews')
            ->first();

        $ratingCountsRaw = DB::table('tb_toko_review')
            ->where('toko_id', $toko->id)
            ->select('rating', DB::raw('count(*) as total'))
            ->groupBy('rating')
            ->pluck('total', 'rating')->toArray();

        $ratingCounts = [
            5 => $ratingCountsRaw[5] ?? 0,
            4 => $ratingCountsRaw[4] ?? 0,
            3 => $ratingCountsRaw[3] ?? 0,
            2 => $ratingCountsRaw[2] ?? 0,
            1 => $ratingCountsRaw[1] ?? 0,
        ];

        $performa = [
            'chat_response_rate' => "95%",
            'chat_response_time' => "≈ 1 jam",
            'cancellation_rate' => "0.5%",
            'late_shipment_rate' => "1.2%"
        ];

        $starFilter = $request->query('star', 'all');

        $query = DB::table('tb_toko_review as r')
            ->join('tb_user as u', 'r.user_id', '=', 'u.id')
            ->leftJoin('tb_detail_transaksi as dt', function($join) {
                $join->on('r.transaksi_id', '=', 'dt.transaksi_id')
                     ->on('r.toko_id', '=', 'dt.toko_id');
            })
            ->leftJoin('tb_barang as b', 'dt.barang_id', '=', 'b.id')
            ->where('r.toko_id', $toko->id)
            ->select(
                'r.id', 
                'r.rating', 
                'r.ulasan', 
                'r.balasan_penjual', 
                'r.created_at',
                'u.nama as nama_user',
                'b.nama_barang',        
                'b.gambar_utama as gambar_barang'
            )
            ->groupBy(
                'r.id', 
                'r.rating', 
                'r.ulasan', 
                'r.balasan_penjual', 
                'r.created_at', 
                'u.nama', 
                'b.nama_barang', 
                'b.gambar_utama'
            )
            ->orderBy('r.created_at', 'desc');

        if ($starFilter !== 'all' && is_numeric($starFilter)) {
            $query->where('r.rating', $starFilter);
        }

        $reviews = $query->paginate(10);

        return view('seller.reviews', compact('summary', 'ratingCounts', 'performa', 'reviews', 'starFilter'));
    }

    public function replyReview(Request $request)
    {
        $request->validate([
            'review_id' => 'required|integer',
            'balasan' => 'required|string|max:500'
        ]);

        $toko = DB::table('tb_toko')->where('user_id', Auth::id())->first();

        DB::table('tb_toko_review')
            ->where('id', $request->review_id)
            ->where('toko_id', $toko->id)
            ->update([
                'balasan_penjual' => $request->balasan,
                'updated_at' => now()
            ]);

        return redirect()->back()->with('success', 'Balasan ulasan berhasil dipublikasikan!');
    }

    // =========================================================================
    // 10. PENGHASILAN TOKO & DOMPET (FINANCE - ENTERPRISE)
    // =========================================================================
    public function income(Request $request)
    {
        $user = Auth::user();
        $toko = DB::table('tb_toko')->where('user_id', $user->id)->first();

        if (!$toko) {
            return redirect()->route('seller.dashboard')->with('error', 'Data toko tidak ditemukan.');
        }

        $perlu_isi_rekening = empty($toko->nomor_rekening);
        $saldo_aktif = $toko->saldo_aktif;

        $penghasilan_pending = DB::table('tb_detail_transaksi as d')
            ->join('tb_transaksi as t', 'd.transaksi_id', '=', 't.id')
            ->where('d.toko_id', $toko->id)
            ->whereIn('d.status_pesanan_item', ['diproses', 'siap_kirim', 'dikirim'])
            ->where('t.status_pembayaran', 'paid')
            ->sum('d.subtotal');

        $dilepas_bulan_ini = DB::table('tb_detail_transaksi as d')
            ->join('tb_transaksi as t', 'd.transaksi_id', '=', 't.id')
            ->where('d.toko_id', $toko->id)
            ->where('d.status_pesanan_item', 'sampai_tujuan')
            ->whereBetween('t.tanggal_transaksi', [now()->startOfMonth(), now()->endOfMonth()])
            ->sum('d.subtotal');

        $tab = $request->query('tab', 'dilepas');
        $query = DB::table('tb_detail_transaksi as d')
            ->join('tb_transaksi as t', 'd.transaksi_id', '=', 't.id')
            ->where('d.toko_id', $toko->id);

        if ($tab == 'pending') {
            $query->whereIn('d.status_pesanan_item', ['diproses', 'siap_kirim', 'dikirim'])->where('t.status_pembayaran', 'paid');
        } else {
            $query->where('d.status_pesanan_item', 'sampai_tujuan');
        }

        $transaksi_list = $query->select('t.kode_invoice', 't.tanggal_transaksi', 'd.status_pesanan_item', 't.metode_pembayaran', 'd.subtotal')
                                ->orderBy('t.tanggal_transaksi', 'desc')
                                ->paginate(10);

        $riwayat_payout = DB::table('tb_payouts')->where('toko_id', $toko->id)->orderBy('tanggal_request', 'desc')->limit(5)->get();

        return view('seller.income', compact(
            'penghasilan_pending', 'saldo_aktif', 'dilepas_bulan_ini',
            'transaksi_list', 'tab', 'riwayat_payout',
            'toko', 'perlu_isi_rekening' 
        ));
    }

    public function requestPayout(Request $request)
    {
        $request->validate(['jumlah_payout' => 'required|numeric|min:50000']);

        $toko = DB::table('tb_toko')->where('user_id', Auth::id())->first();

        if (empty($toko->nomor_rekening)) {
            return back()->with('error', 'Gagal! Harap atur nomor rekening di profil toko Anda.');
        }

        if ($request->jumlah_payout > $toko->saldo_aktif) {
            return back()->with('error', 'Saldo tidak cukup.');
        }

        DB::beginTransaction();
        try {
            $payoutId = DB::table('tb_payouts')->insertGetId([
                'toko_id' => $toko->id,
                'jumlah_payout' => $request->jumlah_payout,
                'status' => 'pending',
                'tanggal_request' => now()
            ]);

            DB::table('tb_toko')->where('id', $toko->id)->decrement('saldo_aktif', $request->jumlah_payout);

            DB::commit();
            return back()->with('success', 'Permintaan penarikan berhasil diajukan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan sistem.');
        }
    }

    // =========================================================================
    // 11. REKENING BANK (ENTERPRISE GRADE)
    // =========================================================================
    public function bank()
    {
        $user = Auth::user();
        $toko = DB::table('tb_toko')->where('user_id', $user->id)->first();

        if (!$toko) {
            return redirect()->route('seller.dashboard')->with('error', 'Data toko tidak ditemukan.');
        }

        $daftar_bank = [
            'BCA', 'Bank Mandiri', 'BNI', 'BRI', 'BSI (Bank Syariah Indonesia)',
            'CIMB Niaga', 'Bank Permata', 'Bank Danamon', 'SeaBank', 'Bank Jago',
            'BNC (Bank Neo Commerce)', 'Bank Raya'
        ];

        return view('seller.bank', compact('toko', 'daftar_bank'));
    }

    public function updateBank(Request $request)
    {
        $request->validate([
            'nama_bank' => 'required|string|max:50',
            'no_rekening' => 'required|string|max:50|regex:/^[0-9]+$/',
            'nama_pemilik' => 'required|string|max:100',
        ]);

        $user = Auth::user();

        DB::table('tb_toko')->where('user_id', $user->id)->update([
            'rekening_bank' => $request->nama_bank,
            'nomor_rekening' => $request->no_rekening,
            'atas_nama_rekening' => strtoupper($request->nama_pemilik),
            'updated_at' => now()
        ]);

        return redirect()->back()->with('success', 'Data Rekening Bank berhasil disimpan dan siap digunakan untuk pencairan dana.');
    }

    public function destroyBank()
    {
        $user = Auth::user();

        DB::table('tb_toko')->where('user_id', $user->id)->update([
            'rekening_bank' => null,
            'nomor_rekening' => null,
            'atas_nama_rekening' => null,
            'updated_at' => now()
        ]);

        return redirect()->back()->with('success', 'Rekening bank berhasil dihapus.');
    }

    // =========================================================================
    // 12. DATA PERFORMA TOKO (STATISTIK GRAFIK ASLI DARI DATABASE)
    // =========================================================================
    public function performance()
    {
        $user = Auth::user();
        $toko = DB::table('tb_toko')->where('user_id', $user->id)->first();

        if (!$toko) {
            return redirect()->route('seller.dashboard')->with('error', 'Data toko tidak ditemukan.');
        }

        $tokoId = $toko->id;

        $totalPenjualan = DB::table('tb_detail_transaksi')
            ->where('toko_id', $tokoId)
            ->whereIn('status_pesanan_item', ['selesai', 'sampai_tujuan'])
            ->sum('subtotal');

        $totalPesanan = DB::table('tb_detail_transaksi')
            ->where('toko_id', $tokoId)
            ->whereIn('status_pesanan_item', ['selesai', 'sampai_tujuan'])
            ->distinct('transaksi_id')
            ->count('transaksi_id');

        $totalPembeli = DB::table('tb_detail_transaksi as d')
            ->join('tb_transaksi as t', 'd.transaksi_id', '=', 't.id')
            ->where('d.toko_id', $tokoId)
            ->whereIn('d.status_pesanan_item', ['selesai', 'sampai_tujuan'])
            ->distinct('t.user_id')
            ->count('t.user_id');

        $kriteria = [
            'penjualan' => ['nilai' => $totalPenjualan, 'perbandingan' => 0],
            'pesanan' => ['nilai' => $totalPesanan, 'perbandingan' => 0],
            'tingkat_konversi' => ['nilai' => ($totalPembeli > 0) ? round(($totalPesanan / $totalPembeli) * 100, 2) : 0, 'perbandingan' => 0],
            'pengunjung' => ['nilai' => $totalPembeli, 'perbandingan' => 0]
        ];

        $tujuhHariLalu = now()->subDays(6)->startOfDay();

        $dataHarian = DB::table('tb_detail_transaksi as d')
            ->join('tb_transaksi as t', 'd.transaksi_id', '=', 't.id')
            ->selectRaw('DATE(t.tanggal_transaksi) as date, SUM(d.subtotal) as total_rp, COUNT(DISTINCT d.transaksi_id) as total_trx, COUNT(DISTINCT t.user_id) as total_user')
            ->where('d.toko_id', $tokoId)
            ->where('t.tanggal_transaksi', '>=', $tujuhHariLalu)
            ->groupBy('date')
            ->orderBy('date', 'ASC')
            ->get();

        $chart_labels = []; $chart_penjualan = []; $chart_pesanan = []; $chart_pengunjung = [];

        for ($i = 0; $i < 7; $i++) {
            $tanggalLabel = $tujuhHariLalu->copy()->addDays($i)->format('Y-m-d');
            $chart_labels[] = \Carbon\Carbon::parse($tanggalLabel)->format('d M');

            $dataHariIni = $dataHarian->firstWhere('date', $tanggalLabel);

            $chart_penjualan[] = $dataHariIni ? (int)$dataHariIni->total_rp : 0;
            $chart_pesanan[] = $dataHariIni ? (int)$dataHariIni->total_trx : 0;
            $chart_pengunjung[] = $dataHariIni ? (int)$dataHariIni->total_user : 0;
        }

        $chart_data = [
            'penjualan' => $chart_penjualan, 'pesanan' => $chart_pesanan, 'pengunjung' => $chart_pengunjung
        ];

        $saluran = [
            'halaman_produk' => ['nilai' => $totalPenjualan, 'perbandingan' => 0],
            'live' => ['nilai' => 0, 'perbandingan' => 0],
            'video' => ['nilai' => 0, 'perbandingan' => 0]
        ];

        $pembeliBerulang = DB::table('tb_detail_transaksi as d')
            ->join('tb_transaksi as t', 'd.transaksi_id', '=', 't.id')
            ->select('t.user_id')
            ->where('d.toko_id', $tokoId)
            ->groupBy('t.user_id')
            ->havingRaw('COUNT(DISTINCT d.transaksi_id) > 1')
            ->get()
            ->count();

        $pembeliBaru = max(0, $totalPembeli - $pembeliBerulang);

        $pembeli = [
            'pembeli_saat_ini_persen' => ($totalPembeli > 0) ? 100 : 0,
            'total_pembeli' => $totalPembeli,
            'pembeli_baru' => $pembeliBaru,
            'potensi_pembeli' => $totalPembeli * 3,
            'tingkat_pembeli_berulang' => ($totalPembeli > 0) ? round(($pembeliBerulang / $totalPembeli) * 100, 1) : 0
        ];

        $pembeli_donut_chart = ['baru' => $pembeliBaru, 'berulang' => $pembeliBerulang];

        return view('seller.performance', compact(
            'kriteria', 'chart_labels', 'chart_data', 'saluran', 'pembeli', 'pembeli_donut_chart'
        ));
    }

    // =========================================================================
    // 13. KESEHATAN TOKO (ASLI DARI DATABASE)
    // =========================================================================
    public function health()
    {
        $user = Auth::user();
        $toko = DB::table('tb_toko')->where('user_id', $user->id)->first();

        if (!$toko) {
            return redirect()->route('seller.dashboard')->with('error', 'Data toko tidak ditemukan.');
        }

        $totalPesananAll = DB::table('tb_detail_transaksi')->where('toko_id', $toko->id)->count();

        $pesananGagal = DB::table('tb_detail_transaksi')
            ->where('toko_id', $toko->id)
            ->whereIn('status_pesanan_item', ['dibatalkan', 'ditolak'])
            ->count();

        $persentaseGagal = ($totalPesananAll > 0) ? round(($pesananGagal / $totalPesananAll) * 100, 2) : 0;

        $produkDilarang = DB::table('tb_barang')
            ->where('toko_id', $toko->id)
            ->where('status_moderasi', 'rejected')
            ->count();

        $status_kesehatan = ($persentaseGagal > 10 || $produkDilarang > 0) ? "Perlu Perhatian" : "Sangat baik";

        $top_summary = [
            'pesanan_terselesaikan' => $pesananGagal, 
            'produk_dilarang' => $produkDilarang,
            'pelayanan_pembeli' => 0 
        ];

        $metrics = [
            'Pesanan Terselesaikan' => [
                ['nama' => 'Tingkat Pesanan Tidak Terselesaikan', 'sekarang' => $persentaseGagal . '%', 'target' => '<10.00%', 'sebelumnya' => '0.00%'],
                ['nama' => 'Tingkat Keterlambatan Pengiriman', 'sekarang' => '0.00%', 'target' => '<10.00%', 'sebelumnya' => '0.00%'],
                ['nama' => 'Masa Pengemasan', 'sekarang' => '0.00 hari', 'target' => '<2.00 hari', 'sebelumnya' => '0.00 hari'],
            ],
            'Produk yang Dilarang' => [
                ['nama' => 'Pelanggaran Produk Berat', 'sekarang' => $produkDilarang, 'target' => 0, 'sebelumnya' => 0],
                ['nama' => 'Produk Pre-order', 'sekarang' => '0.00%', 'target' => '<20.00%', 'sebelumnya' => '0.00%'],
            ],
            'Pelayanan Pembeli' => [
                ['nama' => 'Persentase Chat Dibalas', 'sekarang' => '0.00%', 'target' => '≥70.00%', 'sebelumnya' => '0.00%'],
            ]
        ];

        $poin_penalti_kuartal_ini = ($persentaseGagal > 10) ? 1 : 0;
        $pelanggaran_penalti = [
            'Pesanan Tidak Terpenuhi' => $pesananGagal,
            'Pengiriman Terlambat' => 0,
            'Produk yang Dilarang' => $produkDilarang,
            'Pelanggaran Lainnya' => 0,
        ];

        $masalah_perlu_diselesaikan = [
            'produk_bermasalah' => $produkDilarang,
            'keterlambatan_pengiriman' => 0,
        ];

        return view('seller.health', compact(
            'status_kesehatan', 'top_summary', 'metrics', 'poin_penalti_kuartal_ini',
            'pelanggaran_penalti', 'masalah_perlu_diselesaikan'
        ));
    }
    
    // =========================================================================
    // RINCIAN PESANAN (ORDER DETAILS)
    // =========================================================================
    public function detailPesanan($invoice)
    {
        $user = Auth::user();
        $toko = DB::table('tb_toko')->where('user_id', $user->id)->first();

        if (!$toko) {
            return redirect()->route('seller.dashboard')->with('error', 'Data toko tidak ditemukan.');
        }

        $transaksi = DB::table('tb_transaksi as t')
            ->leftJoin('tb_user as u', 't.user_id', '=', 'u.id')
            ->where('t.kode_invoice', $invoice)
            ->select('t.*', 'u.nama as nama_akun', 'u.email as email_akun', 'u.no_telepon as telp_akun')
            ->first();

        if (!$transaksi) {
            return redirect()->route('seller.orders.index')->with('error', 'Pesanan tidak ditemukan.');
        }

        $detailItems = DB::table('tb_detail_transaksi as d')
            ->leftJoin('tb_barang as b', 'd.barang_id', '=', 'b.id')
            ->where('d.transaksi_id', $transaksi->id)
            ->where('d.toko_id', $toko->id)
            ->select('d.*', 'b.gambar_utama', 'b.kode_barang')
            ->get();

        if ($detailItems->isEmpty()) {
            return redirect()->route('seller.orders.index')->with('error', 'Pesanan ini tidak memuat barang dari toko Anda.');
        }

        $totalBelanjaToko = $detailItems->sum('subtotal');
        $totalOngkirToko  = $detailItems->sum('biaya_pengiriman_item');
        $grandTotalToko   = $totalBelanjaToko + $totalOngkirToko;

        return view('seller.detail_pesanan', compact('transaksi', 'detailItems', 'totalBelanjaToko', 'totalOngkirToko', 'grandTotalToko'));
    }
}