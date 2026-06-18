<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StoreController extends Controller
{
    /**
     * Menampilkan Daftar Toko dengan Filter Status & Kasta (Tier)
     */
    public function index(Request $request)
    {
        $status_filter = $request->get('status', 'semua');
        $tier_filter = $request->get('tier', 'semua');
        $search = $request->get('search');

        // Statistik Komprehensif (Status Operasional & Kasta Toko)
        $stats = [
            'total' => DB::table('tb_toko')->count(),
            'pending' => DB::table('tb_toko')->where('status', 'pending')->count(),
            'active' => DB::table('tb_toko')->where('status', 'active')->count(),
            'suspended' => DB::table('tb_toko')->where('status', 'suspended')->count(),
            'official' => DB::table('tb_toko')->where('tier_toko', 'official_store')->count(),
            'power' => DB::table('tb_toko')->where('tier_toko', 'power_merchant')->count(),
            'regular' => DB::table('tb_toko')->where('tier_toko', 'regular')->count(),
        ];

        // Query Utama (Join User)
        $query = DB::table('tb_toko as t')
            ->join('tb_user as u', 't.user_id', '=', 'u.id')
            ->select(
                't.*', 
                'u.nama as nama_pemilik', 
                'u.email as email_pemilik',
                'u.no_telepon as telepon_pemilik',
                't.kota as kota_kabupaten'
            );

        // Filter Berdasarkan Status (Pending/Active/Suspended)
        if ($status_filter !== 'semua') {
            $query->where('t.status', $status_filter);
        }

        // Filter Berdasarkan Kasta Toko (Official/Power/Regular)
        if ($tier_filter !== 'semua') {
            $query->where('t.tier_toko', $tier_filter);
        }

        // Filter Pencarian (Nama Toko atau Nama Pemilik)
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('t.nama_toko', 'LIKE', "%$search%")
                  ->orWhere('u.nama', 'LIKE', "%$search%");
            });
        }

        // Tampilkan 12 toko per halaman
        $stores = $query->latest('t.created_at')->paginate(12)->withQueryString();

        return view('admin.stores.index', compact('stores', 'status_filter', 'tier_filter', 'search', 'stats'));
    }

    /**
     * Memproses Verifikasi Pendaftaran Toko Baru (Approve / Reject / Suspend)
     */
    public function verify(Request $request, $id)
    {
        $action = $request->action; // 'setujui' atau 'tolak'/'suspend'
        $status = ($action === 'setujui') ? 'active' : 'suspended';

        DB::table('tb_toko')->where('id', $id)->update([
            'status' => $status,
            'updated_at' => now()
        ]);

        $msg = ($action === 'setujui') 
            ? 'Toko berhasil diverifikasi dan diaktifkan!' 
            : 'Toko telah dibekukan / ditolak.';
            
        return back()->with('success', $msg);
    }

    /**
     * Menampilkan Daftar Pengajuan Kenaikan Tier (Upgrade Level)
     */
    public function tierApplications(Request $request)
    {
        $status_filter = $request->get('status', 'pending');
        
        $applications = DB::table('tb_pengajuan_tier as p')
            ->join('tb_toko as t', 'p.toko_id', '=', 't.id')
            ->join('tb_user as u', 't.user_id', '=', 'u.id')
            ->select('p.*', 't.nama_toko', 't.logo_toko', 'u.nama as nama_pemilik')
            ->when($status_filter !== 'semua', function($q) use ($status_filter) {
                return $q->where('p.status', $status_filter);
            })
            ->latest('p.created_at')
            ->paginate(15);

        return view('admin.stores.tier_applications', compact('applications', 'status_filter'));
    }

    /**
     * Memproses Pengajuan Kenaikan Tier (Approve / Reject / Revision)
     */
    public function processTierApplication(Request $request, $id)
    {
        $request->validate([
            'action' => 'required|in:approve,reject,revision',
            'alasan' => 'nullable|string'
        ]);

        $app = DB::table('tb_pengajuan_tier')->where('id', $id)->first();
        if (!$app) return back()->with('error', 'Pengajuan tidak ditemukan.');

        DB::beginTransaction();
        try {
            $status = 'pending';
            if ($request->action === 'approve') {
                $status = 'approved';
                // Update Tier Toko
                DB::table('tb_toko')->where('id', $app->toko_id)->update([
                    'tier_toko' => $app->tier_tujuan,
                    'updated_at' => now()
                ]);
            } elseif ($request->action === 'reject') {
                $status = 'rejected';
            } else {
                $status = 'revision';
            }

            DB::table('tb_pengajuan_tier')->where('id', $id)->update([
                'status' => $status,
                'alasan_admin' => $request->alasan,
                'updated_at' => now()
            ]);

            DB::commit();
            return back()->with('success', 'Pengajuan berhasil diproses.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memproses pengajuan: ' . $e->getMessage());
        }
    }

    /**
     * Memproses Perubahan Kasta / Tier Toko (Regular -> Power Merchant -> Official Store)
     */
    public function updateTier(Request $request, $id)
    {
        $request->validate([
            'tier_toko' => 'required|in:regular,power_merchant,official_store'
        ]);

        DB::table('tb_toko')->where('id', $id)->update([
            'tier_toko' => $request->tier_toko,
            'updated_at' => now()
        ]);

        $tierNames = [
            'regular' => 'Toko Reguler',
            'power_merchant' => 'Power Merchant',
            'official_store' => 'Official Store'
        ];

        return back()->with('success', 'Kasta mitra berhasil diubah menjadi ' . $tierNames[$request->tier_toko] . '!');
    }
}