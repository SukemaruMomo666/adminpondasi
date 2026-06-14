    <?php

    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Route;

    // Import semua Controller yang digunakan di API
    use App\Http\Controllers\AuthController;
    use App\Http\Controllers\LandingController;
    use App\Http\Controllers\CartController;
    use App\Http\Controllers\TransactionController; // Controller Web (Hati-hati jangan tertukar)
    use App\Http\Controllers\ProjectController;
    use App\Http\Controllers\ChatController;
    use App\Http\Controllers\Api\Mobile\UserController as MobileUserController;

    // Alias khusus untuk Controller Mobile
    use App\Http\Controllers\Api\Mobile\TransactionController as MobileTransaction;

    // ========================================================
    // 1. DATA PUBLIK (Bisa diakses tanpa login)
    // ========================================================

    // Landing Page Data (Digunakan di React Native Home Screen)
    Route::get('/landing-data', [LandingController::class, 'getApiData']);

    // Katalog Produk & Pencarian
    Route::get('/products', [LandingController::class, 'getAllProducts']);
    Route::get('/products/{id}', [LandingController::class, 'getProductDetail']);

    // Direktori Toko / Mitra
    Route::get('/stores', [LandingController::class, 'getStores']);
    Route::get('/stores/{slug}', [LandingController::class, 'getStoreDetail']);

    // ========================================================
    // 2. AUTENTIKASI
    // ========================================================
    // Endpoint untuk Register Mobile
    Route::post('/register', [AuthController::class, 'registerApi']);

    // Endpoint untuk Login Mobile (Mendapatkan Sanctum Token)
    Route::post('/login', [AuthController::class, 'loginApi']);

    // Endpoint untuk Login/Register via Google Mobile
    Route::post('/google-login', [AuthController::class, 'googleLoginApi']);

    // ========================================================
    // 3. PRIVATE ROUTES (Wajib Login & Menggunakan Sanctum Token)
    // ========================================================
    Route::middleware('auth:sanctum')->group(function () {

        // Mengambil data profil user yang sedang aktif
        Route::get('/me', function (Request $request) {
            return response()->json([
                'status' => 'success',
                'user' => $request->user()
            ], 200);
        });

        // Menghapus token dari database server saat Logout
        Route::post('/logout', function (Request $request) {
            $request->user()->currentAccessToken()->delete();
            return response()->json([
                'status' => 'success',
                'message' => 'Berhasil logout dari aplikasi mobile.'
            ], 200);
        });


        // Rute untuk List Toko Diikuti
        Route::get('/following-stores', [App\Http\Controllers\Api\UserController::class, 'followingStores']);
        
        // Rute untuk Tombol Ikuti / Batal Ikuti
        Route::post('/toko/follow', [App\Http\Controllers\Api\UserController::class, 'toggleFollow']);
       
        // ========================================================
        // FITUR PENGGUNA & TRANSAKSI
        // ========================================================

        // --- RUTE BARU: ALAMAT PENGIRIMAN & LEAFLET MAPS ---
        Route::get('/addresses', [LandingController::class, 'getUserAddresses']);
        Route::post('/addresses', [LandingController::class, 'storeUserAddress']);
        // Opsional untuk halaman Daftar Alamat (Set Utama & Hapus)
        Route::post('/addresses/{id}/set-utama', [LandingController::class, 'setUtamaAddress']);
        Route::delete('/addresses/{id}', [LandingController::class, 'deleteAddress']);

        // Keranjang Belanja
        Route::get('/cart', [CartController::class, 'index']);
        Route::post('/cart/add', [CartController::class, 'store']);

        // Transaksi / Checkout ---> SEKARANG MENGARAH KE KODE MIDTRANS YANG BENAR!
        Route::post('/checkout', [MobileTransaction::class, 'checkout']);

        Route::get('/checkout/data', [MobileTransaction::class, 'getCheckoutData']);
        Route::get('/cek-ongkir', [MobileTransaction::class, 'cekOngkir']);

        // Tambahkan di dalam group middleware 'auth:sanctum' atau di luar jika ingin public
Route::get('/biteship/areas', [App\Http\Controllers\Api\BiteshipController::class, 'searchArea']);
        
        // Riwayat Pesanan
        Route::get('/orders', [MobileTransaction::class, 'userOrders']);

        // Manajemen Proyek / RAB (Mandor POTA AI)
        Route::get('/proyek', [ProjectController::class, 'index']);

        // ========================================================
        // RUTE CHAT KHUSUS MOBILE APP (VIP SANCTUM)
        // ========================================================
        Route::get('/m-chat/contacts', [ChatController::class, 'getContacts']);
        Route::get('/m-chat/messages/{storeId}', [ChatController::class, 'getMessages']);
        Route::post('/m-chat/send', [ChatController::class, 'sendMessage']);

    // ========================================================
        // FITUR PENGGUNA (PROFIL & PASSWORD)
        // ========================================================
        Route::post('/profile/update', [MobileUserController::class, 'updateProfile']);
        Route::post('/profile/request-otp', [MobileUserController::class, 'requestPasswordOtp']);
        Route::post('/profile/password-otp', [MobileUserController::class, 'updatePasswordWithOtp']);

    });