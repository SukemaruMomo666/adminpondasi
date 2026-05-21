<?php

use Illuminate\Support\Facades\Route;

// --- IMPORT CONTROLLER UTAMA ---
use App\Http\Controllers\LandingController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController as FrontProductController;
use App\Http\Controllers\WebhookController;
use App\Http\Controllers\ChatAiController;
use App\Http\Controllers\KeranjangController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\ChatController; // <-- CHAT UNTUK CUSTOMER DI FRONTEND

// --- IMPORT CONTROLLER SELLER ---
use App\Http\Controllers\SellerController;
use App\Http\Controllers\Seller\DashboardController;
use App\Http\Controllers\Seller\ProductController as SellerProductController;
use App\Http\Controllers\Seller\ShopController;
use App\Http\Controllers\Seller\ChatController as SellerChatController; // <-- CHAT DEWA UNTUK SELLER

// --- IMPORT CONTROLLER ADMIN ---
use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\StoreController as AdminStoreController;
use App\Http\Controllers\Admin\ProductModerationController as AdminProductModerationController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\DisputeController as AdminDisputeController;
use App\Http\Controllers\Admin\ReportController as AdminReportController;
use App\Http\Controllers\Admin\PayoutController as AdminPayoutController;
use App\Http\Controllers\Admin\SettingController as AdminSettingController;
use App\Http\Controllers\Admin\LogisticSettingController as AdminLogisticSettingController;

/*
|--------------------------------------------------------------------------
| Web Routes - Pondasikita Enterprise Edition
|--------------------------------------------------------------------------
*/

// 1. LANDING PAGE & FRONTEND DETAIL
Route::get('/', [LandingController::class, 'index'])->name('home');
Route::get('/produk/{id}', [FrontProductController::class, 'detail'])->name('produk.detail');

// 2. CUSTOMER JOURNEY (GROUPED)
Route::controller(PageController::class)->group(function () {
    // Katalog & Toko
    Route::get('/pages/produk', 'produk')->name('produk.index');
    Route::get('/pages/semua_toko', 'semuaToko')->name('toko.index');
    Route::get('/pages/toko', 'detailToko')->name('toko.detail');
    Route::get('/pages/search', 'search')->name('search');

    Route::post('/api/toko/follow', 'toggleFollow')->name('api.toko.follow');

    // Route untuk menampilkan halaman (Link yang dipanggil di halaman Login)
    Route::get('/lupa-password', [ForgotPasswordController::class, 'showLinkRequestForm'])
        ->name('password.request');

    // Route untuk menangani submit form email
    Route::post('/lupa-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])
        ->name('password.email');

    // Keranjang Belanja
    Route::get('/pages/keranjang', 'keranjang')->name('keranjang.index');
    Route::post('/api/keranjang/tambah', 'tambahKeranjang')->name('keranjang.tambah');
    Route::post('/api/keranjang/update', 'updateKeranjang')->name('keranjang.update');
    Route::post('/api/keranjang/hapus', 'hapusKeranjang')->name('keranjang.hapus');

    Route::middleware(['auth'])->group(function () {
        // Rute untuk tombol "+ Keranjang" (AJAX JSON)
        Route::post('/keranjang/tambah', [KeranjangController::class, 'tambah'])->name('keranjang.tambah');

        // Rute untuk tombol "Beli Sekarang" (Form Submit)
        Route::post('/checkout/langsung', [KeranjangController::class, 'checkoutLangsung'])->name('checkout.langsung');
    });

    // Checkout
    Route::match(['get', 'post'], '/checkout', 'checkout')->name('checkout');
    Route::post('/checkout/proses', 'prosesCheckout')->name('checkout.process');

    // Profil User
    Route::get('/profil-saya', 'profil')->name('profil.index');
    Route::get('/profil-saya/edit', 'editProfil')->name('profil.edit');
    
    // --- PERBAIKAN: RUTE FALLBACK UNTUK MENCEGAH ERROR METHOD GET SAAT REFRESH ---
    Route::get('/profil-saya/update', function() { return redirect()->route('profil.edit'); });
    // -------------------------------------------------------------------------------
    
    Route::post('/profil-saya/update', 'updateProfil')->name('profil.update');
    Route::get('/profil-saya/ganti-password', 'gantiPassword')->name('profil.password');
    Route::post('/profil-saya/ganti-password', 'updatePassword')->name('profil.password.update');

    // =========================================================================
    // FITUR ENTERPRISE: Status Pesanan, Lacak, & Siklus Aksi Transaksi
    // =========================================================================
    // FIX: Mengubah /pesanan-saya menjadi /pesanan agar Midtrans tidak 404
    Route::get('/pesanan', 'pesanan')->name('pesanan.index'); 
    Route::get('/pesanan/{kode_invoice}', 'lacakPesanan')->name('pesanan.lacak');
    
    // Aksi Interaktif Customer
    Route::post('/pesanan/batalkan', 'batalkanPesanan')->name('pesanan.batalkan');
    Route::post('/pesanan/terima', 'terimaPesanan')->name('pesanan.terima');
    Route::post('/pesanan/komplain', 'ajukanPengembalian')->name('pesanan.komplain');
    
    // Sinyal Realtime Midtrans (Auto-Update Status)
    Route::post('/payment/update-status', 'updatePaymentStatus')->name('payment.update_status');
    // =========================================================================
});

// 3. AUTHENTICATION SYSTEM
Route::controller(AuthController::class)->group(function () {
    // Customer Auth
    Route::get('/login', 'showLogin')->name('login');
    Route::post('/login', 'login')->name('login.process');
    Route::get('/register', 'showRegister')->name('register');
    Route::post('/register', 'register')->name('register.process');

    // Seller Auth
    Route::get('/seller/login', 'showLoginSeller')->name('seller.login');
    Route::post('/seller/login', 'loginSeller')->name('seller.login.process');
    Route::get('/seller/register', 'showRegisterSeller')->name('seller.register');
    Route::post('/seller/register', 'registerSeller')->name('seller.register.process');

    Route::post('/logout', 'logout')->name('logout');
});

// 4. SELLER CENTER (GOD LOGIC PROTECTED)
Route::middleware(['auth', 'role:seller'])->prefix('seller')->name('seller.')->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Product Management
    Route::get('/products/template', [SellerProductController::class, 'downloadTemplate'])->name('products.template');
    Route::post('/products/import', [SellerProductController::class, 'importExcel'])->name('products.import');
    Route::resource('products', SellerProductController::class)->except(['show']);
    Route::post('/products/toggle-status', [SellerProductController::class, 'toggleStatus'])->name('products.toggle');

    // Order & Logistics
    Route::prefix('orders')->name('orders.')->group(function() {
        Route::get('/', [SellerController::class, 'pesanan'])->name('index');
        Route::post('/update-status', [SellerController::class, 'updateOrderStatus'])->name('updateStatus');
        Route::post('/mass-update', [SellerController::class, 'massUpdateOrderStatus'])->name('massUpdate');
        Route::get('/return', [SellerController::class, 'pengembalian'])->name('return');
        Route::post('/return/process', [SellerController::class, 'processPengembalian'])->name('return.process');
        Route::get('/{invoice}/detail', [SellerController::class, 'detailPesanan'])->name('show');
    });

    // Shipping Settings
    Route::prefix('pengaturan')->name('pengaturan.')->group(function() {
        Route::get('/pengiriman', [SellerController::class, 'pengaturanPengiriman'])->name('pengiriman');
        Route::post('/pengiriman/store', [SellerController::class, 'storePengiriman'])->name('pengiriman.store');
        Route::post('/pengiriman/toggle', [SellerController::class, 'togglePengiriman'])->name('pengiriman.toggle');
        Route::delete('/pengiriman/{id}', [SellerController::class, 'destroyPengiriman'])->name('pengiriman.destroy');
    });

    // Promotions & Marketing
    Route::prefix('promotion')->name('promotion.')->group(function() {
        Route::get('/discounts', [SellerController::class, 'promosi'])->name('discounts');
        Route::post('/discounts/update', [SellerController::class, 'updateDiscount'])->name('discounts.update');
        Route::get('/vouchers', [SellerController::class, 'voucher'])->name('vouchers');
        Route::post('/vouchers/store', [SellerController::class, 'storeVoucher'])->name('vouchers.store');
        Route::post('/vouchers/toggle', [SellerController::class, 'toggleVoucher'])->name('vouchers.toggle');
        Route::delete('/vouchers/{id}', [SellerController::class, 'destroyVoucher'])->name('vouchers.destroy');
    });

    // Customer Service (Chat & Reviews)
    Route::prefix('service')->name('service.')->group(function() {
        Route::get('/chat', [SellerChatController::class, 'chat'])->name('chat');
        Route::get('/chat/list', [SellerChatController::class, 'getChatList'])->name('chat.list');
        Route::get('/chat/messages/{chatId}', [SellerChatController::class, 'getMessages'])->name('chat.messages');
        Route::post('/chat/send', [SellerChatController::class, 'sendMessage'])->name('chat.send');

        Route::get('/reviews', [SellerController::class, 'reviews'])->name('reviews');
        Route::post('/reviews/reply', [SellerController::class, 'replyReview'])->name('reviews.reply');
    });

    // Finance & Wallet
    Route::prefix('finance')->name('finance.')->group(function() {
        Route::get('/income', [SellerController::class, 'income'])->name('income');
        Route::post('/payout', [SellerController::class, 'requestPayout'])->name('payout');
        Route::get('/bank', [SellerController::class, 'bank'])->name('bank');
        Route::post('/bank/update', [SellerController::class, 'updateBank'])->name('bank.update');
        Route::post('/bank/destroy', [SellerController::class, 'destroyBank'])->name('bank.destroy');
    });

    // Data Analytics
    Route::prefix('data')->name('data.')->group(function() {
        Route::get('/performance', [SellerController::class, 'performance'])->name('performance');
        Route::get('/health', [SellerController::class, 'health'])->name('health');
    });

    // Shop Management
    Route::prefix('shop')->name('shop.')->group(function() {
        Route::get('/profile', [ShopController::class, 'profile'])->name('profile');
        Route::put('/profile/update', [ShopController::class, 'updateProfile'])->name('profile.update');

        Route::get('/decoration', [ShopController::class, 'decoration'])->name('decoration');
        Route::get('/decoration/editor', [ShopController::class, 'editor'])->name('decoration.editor');
        Route::get('/decoration/editor-desktop', [ShopController::class, 'editorDesktop'])->name('decoration.editor.desktop');
        Route::get('/decoration/template', [ShopController::class, 'templateSelection'])->name('decoration.template');
        Route::post('/decoration/update', [ShopController::class, 'updateDecoration'])->name('decoration.update');
        Route::post('/decoration/save', [ShopController::class, 'saveDecoration'])->name('decoration.save');

        Route::get('/settings', [ShopController::class, 'settings'])->name('settings');
        Route::put('/settings/update', [ShopController::class, 'updateSettings'])->name('settings.update');
    });

    // Point of Sale (POS)
    Route::prefix('pos')->name('pos.')->group(function() {
        Route::get('/', [SellerController::class, 'pos'])->name('index');
        Route::get('/api/products', [SellerController::class, 'getPosProducts'])->name('api.products');
        Route::get('/api/categories', [SellerController::class, 'getPosCategories'])->name('api.categories');
        Route::post('/api/checkout', [SellerController::class, 'processPosCheckout'])->name('api.checkout');
        Route::get('/print/{invoice}', [SellerController::class, 'printStruk'])->name('print');
    });
});

// 5. ADMIN PANEL (ENTRANCE)
Route::get('/kunci-brankas-pks', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
Route::post('/kunci-brankas-pks', [AdminAuthController::class, 'login'])->name('admin.login.submit');

Route::prefix('portal-rahasia-pks')->name('admin.')->middleware(['admin'])->group(function () {

    // Dashboard & Leaderboard
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])
        ->name('dashboard')
        ->middleware('admin.role:super,finance,cs');

    Route::get('/dashboard/top-stores', [AdminDashboardController::class, 'topStores'])
        ->name('dashboard.top_stores')
        ->middleware('admin.role:super,finance,cs');

    // Customer Service & Store Management
    Route::middleware(['admin.role:super,cs'])->group(function () {
        Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
        Route::get('/users/export', [AdminUserController::class, 'exportCsv'])->name('users.export');
        Route::post('/users', [AdminUserController::class, 'store'])->name('users.store');
        Route::post('/users/{id}/update', [AdminUserController::class, 'update'])->name('users.update');
        Route::post('/users/{id}/toggle-ban', [AdminUserController::class, 'toggleBan'])->name('users.toggleBan');

        Route::get('/stores', [AdminStoreController::class, 'index'])->name('stores.index');
        Route::post('/stores/{id}/verify', [AdminStoreController::class, 'verify'])->name('stores.verify');
        Route::post('/stores/{id}/tier', [AdminStoreController::class, 'updateTier'])->name('stores.updateTier');

        Route::get('/products', [AdminProductModerationController::class, 'index'])->name('products.index');
        Route::get('/products/{id}', [AdminProductModerationController::class, 'show'])->name('products.show');
        Route::post('/products/{id}/process', [AdminProductModerationController::class, 'process'])->name('products.process');

        Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{id}', [AdminOrderController::class, 'show'])->name('orders.show');
        Route::get('/disputes', [AdminDisputeController::class, 'index'])->name('disputes.index');
        Route::post('/disputes/{id}/resolve', [AdminDisputeController::class, 'resolve'])->name('disputes.resolve');
    });

    // Finance & Global Settings
    Route::middleware(['admin.role:super,finance'])->group(function () {
        Route::get('/reports', [AdminReportController::class, 'index'])->name('reports.index');
        Route::get('/payouts', [AdminPayoutController::class, 'index'])->name('payouts.index');
        Route::post('/payouts/{id}/process', [AdminPayoutController::class, 'process'])->name('payouts.process');
        Route::get('/settings', [AdminSettingController::class, 'index'])->name('settings.index');
        Route::post('/settings/update', [AdminSettingController::class, 'update'])->name('settings.update');
        Route::post('/settings/sync-komerce', [AdminSettingController::class, 'syncKomerce'])->name('settings.syncKomerce');
        Route::get('/logistics', [AdminLogisticSettingController::class, 'index'])->name('logistics.index');
        Route::post('/logistics/update', [AdminLogisticSettingController::class, 'update'])->name('logistics.update');
    });
});

// 6. API, AJAX, & WEBHOOKS
Route::get('/api/biteship/search', [PageController::class, 'searchBiteshipAPI']); // <-- UPDATE API BITESHIP

Route::get('/api/cek-ongkir', [PageController::class, 'cekOngkir'])->name('api.cek.ongkir');
// Chat AI (POTA)
Route::post('/api/chat', [ChatAiController::class, 'handleChat'])->name('api.chat');

// --- RUTE CHAT CUSTOMER (HUBUNGI SELLER DARI FRONTEND) ---
Route::middleware(['auth'])->group(function () {
    Route::get('/api/chat/contacts', [ChatController::class, 'getContacts']);
    Route::get('/api/chat/messages/{storeId}', [ChatController::class, 'getMessages']);
    Route::post('/api/chat/send', [ChatController::class, 'sendMessage']);
    
    // === TAMBAHKAN INI: ROUTE PENJAGA PINTU MEDIA PRIVATE ===
    Route::get('/chat/media/{filename}', function ($filename) {
        $path = 'private_chats/' . $filename;

        // Cek apakah filenya benar-benar ada di storage lokal?
        if (!\Illuminate\Support\Facades\Storage::disk('local')->exists($path)) { 
            abort(404, 'File media tidak ditemukan.'); 
        }

        // Tampilkan file secara aman melalui backend
        return response()->file(storage_path('app/' . $path));
    })->name('chat.file');
    // ========================================================
});

// Webhook Midtrans (Payment Gateway) - Pengecualian CSRF Token
Route::post('/webhook/midtrans', [WebhookController::class, 'midtransHandler'])
    ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class])
    ->name('webhook.midtrans');

// 7. EXTERNAL & UTILS
Route::get('/auth/google', [\App\Http\Controllers\AuthController::class, 'redirectToGoogle'])->name('google.login');
Route::get('/auth/google/callback', [\App\Http\Controllers\AuthController::class, 'handleGoogleCallback']);

// ========================================================
// RUTE SAPU JAGAT (PEMBERSIH MEMORI SERVER HOSTINGER)
// ========================================================
Route::get('/bersih', function() {
    \Illuminate\Support\Facades\Artisan::call('optimize:clear');
    \Illuminate\Support\Facades\Artisan::call('route:clear');
    \Illuminate\Support\Facades\Artisan::call('config:clear');
    \Illuminate\Support\Facades\Artisan::call('cache:clear');
    return "<h1>Sapu Jagat Berhasil!</h1><p>Semua memori lama sudah dihapus. Silakan cek API sekarang.</p>";
}); 

Route::get('/buat-tabel-token-vip', function() {
    try {
        \Illuminate\Support\Facades\DB::statement("
            CREATE TABLE IF NOT EXISTS `personal_access_tokens` (
              `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
              `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
              `tokenable_id` bigint(20) unsigned NOT NULL,
              `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
              `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
              `abilities` text COLLATE utf8mb4_unicode_ci,
              `last_used_at` timestamp NULL DEFAULT NULL,
              `expires_at` timestamp NULL DEFAULT NULL,
              `created_at` timestamp NULL DEFAULT NULL,
              `updated_at` timestamp NULL DEFAULT NULL,
              PRIMARY KEY (`id`),
              UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
              KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");
        return 'JALUR VIP SUKSES! Tabel personal_access_tokens sudah dipaksa masuk ke database!';
    } catch (\Exception $e) {
        return 'Waduh gagal: ' . $e->getMessage();
    }
});