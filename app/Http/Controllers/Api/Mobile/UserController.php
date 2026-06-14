<?php

namespace App\Http\Controllers\Api\Mobile;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class UserController extends Controller
{
    public function updateProfile(Request $request)
    {
        try {
            $user = Auth::guard('sanctum')->user();
            if (!$user) {
                return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 401);
            }

            // Validasi: unique:tb_user,email,{id},id -> Ini format paling aman di Laravel
            $request->validate([
                'nama' => 'required|string|max:255',
                'email' => 'required|email|unique:tb_user,email,' . $user->id . ',id',
                'no_telepon' => 'nullable|string',
            ]);

            DB::table('tb_user')->where('id', $user->id)->update([
                'nama' => $request->nama,
                'email' => $request->email,
                'no_telepon' => $request->no_telepon,
                'updated_at' => \Carbon\Carbon::now(),
            ]);

            return response()->json([
                'status' => 'success', 
                'message' => 'Data profil berhasil diperbarui!'
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal.',
                'errors' => $e->errors() 
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error', 
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
            ], 500);
        }
    }

    // 2. REQUEST OTP KE EMAIL UNTUK GANTI PASSWORD
    public function requestPasswordOtp(Request $request)
    {
        try {
            $user = Auth::guard('sanctum')->user();

            // 1. Validasi input email dari React Native
            $request->validate([
                'email' => 'required|email'
            ]);

            // 2. Keamanan Ekstra: Cek apakah email yang diketik cocok dengan email di database
            if (strtolower($request->email) !== strtolower($user->email)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Alamat email yang Anda masukkan tidak cocok dengan akun ini.'
                ], 400);
            }

            // Generate 6 digit angka OTP acak
            $otp = rand(100000, 999999);

            // Simpan OTP ke database dan set expired 10 menit dari sekarang
            DB::table('tb_user')->where('id', $user->id)->update([
                'reset_token' => $otp,
                'reset_token_expires_at' => Carbon::now()->addMinutes(10)
            ]);

            // Kirim Email OTP ke user
            try {
                Mail::raw("Halo {$user->nama},\n\nKode OTP Anda untuk mengganti password adalah: {$otp}\n\nKode ini berlaku selama 10 menit. Jangan berikan kode ini kepada siapapun.", function ($message) use ($user) {
                    $message->to($user->email)
                            ->subject('Kode OTP Ganti Password - PondasiKita');
                });
            } catch (\Exception $e) {
                return response()->json([
                    'status' => 'success', 
                    'message' => 'Email gagal dikirim (SMTP belum diatur), TAPI OTP berhasil dibuat untuk testing.',
                    'otp_testing' => $otp // HAPUS INI JIKA SUDAH PRODUCTION!
                ], 200);
            }

            return response()->json([
                'status' => 'success', 
                'message' => 'Kode OTP telah dikirim ke email Anda.'
            ], 200);

        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    // 3. VALIDASI OTP & SIMPAN PASSWORD BARU
    public function updatePasswordWithOtp(Request $request)
    {
        try {
            $user = Auth::guard('sanctum')->user();

            $request->validate([
                'otp' => 'required',
                'new_password' => 'required|min:8|confirmed',
            ]);

            // Ambil data user terbaru untuk ngecek OTP
            $userData = DB::table('tb_user')->where('id', $user->id)->first();

            // Cek apakah OTP cocok dan belum expired
            if ($userData->reset_token !== $request->otp) {
                return response()->json(['status' => 'error', 'message' => 'Kode OTP salah.'], 400);
            }

            if (Carbon::now()->isAfter($userData->reset_token_expires_at)) {
                return response()->json(['status' => 'error', 'message' => 'Kode OTP sudah kedaluwarsa.'], 400);
            }

            // Jika lulus validasi, ganti password dan bersihkan token
            DB::table('tb_user')->where('id', $user->id)->update([
                'password' => Hash::make($request->new_password),
                'reset_token' => null,
                'reset_token_expires_at' => null,
                'updated_at' => Carbon::now(),
            ]);

            return response()->json(['status' => 'success', 'message' => 'Password berhasil diubah!']);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['status' => 'error', 'message' => 'Format password salah atau tidak cocok.'], 422);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
    public function followingStores(Request $request)
    {
        // Mengambil ID user yang sedang login dari token Sanctum
        $userId = $request->user()->id;

        $stores = DB::table('tb_toko_follower')
            ->join('tb_toko', 'tb_toko_follower.toko_id', '=', 'tb_toko.id')
            ->where('tb_toko_follower.user_id', $userId)
            ->select(
                'tb_toko.id', 
                'tb_toko.nama_toko', 
                'tb_toko.slug', 
                'tb_toko.logo_toko', 
                'tb_toko.kota', 
                'tb_toko.tier_toko'
            )
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $stores
        ]);
    }
}