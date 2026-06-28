<?php

use Illuminate\Support\Facades\Route;

// --- IMPORT CONTROLLER AUTH UMUM (Untuk fungsi Logout) ---
use App\Http\Controllers\AuthController;

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
| Web Routes - Pondasikita Admin Panel
|--------------------------------------------------------------------------
*/

// Redirect root domain langsung ke halaman login admin (Entrance Rahasia)
Route::get('/', function () {
    return redirect()->route('admin.login');
})->name('home');

// Rute Logout (Global)
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// 1. ADMIN PANEL (ENTRANCE)
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
        Route::get('/users/appeals', [AdminUserController::class, 'appeals'])->name('users.appeals');
        Route::post('/users/appeals/{id}/process', [AdminUserController::class, 'processAppeal'])->name('users.processAppeal');
        Route::get('/users/export', [AdminUserController::class, 'exportCsv'])->name('users.export');
        Route::post('/users', [AdminUserController::class, 'store'])->name('users.store');
        Route::post('/users/{id}/update', [AdminUserController::class, 'update'])->name('users.update');
        Route::post('/users/{id}/toggle-ban', [AdminUserController::class, 'toggleBan'])->name('users.toggleBan');

        Route::get('/stores', [AdminStoreController::class, 'index'])->name('stores.index');
        Route::get('/stores/tier-applications', [AdminStoreController::class, 'tierApplications'])->name('stores.tierApplications');
        Route::post('/stores/tier-applications/{id}/process', [AdminStoreController::class, 'processTierApplication'])->name('stores.processTierApplication');
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

// ========================================================
// RUTE SAPU JAGAT (PEMBERSIH MEMORI SERVER HOSTINGER)
// ========================================================
Route::get('/bersih', function() {
    \Illuminate\Support\Facades\Artisan::call('optimize:clear');
    \Illuminate\Support\Facades\Artisan::call('route:clear');
    \Illuminate\Support\Facades\Artisan::call('config:clear');
    \Illuminate\Support\Facades\Artisan::call('cache:clear');
    return "<h1>Sapu Jagat Berhasil!</h1><p>Semua memori lama sudah dihapus.</p>";
}); 