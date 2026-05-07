<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LandingController; // Pastikan Controller ini ada

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// ========================================================
// 1. DATA PUBLIK (Untuk Landing Page & Katalog)
// ========================================================

// Route utama untuk menarik semua data Landing Page (Banner, Flash Sale, Kategori, Toko) dalam 1 Request
Route::get('/landing-data', [LandingController::class, 'getApiData']);

// Katalog Produk & Pencarian
Route::get('/products', [LandingController::class, 'getAllProducts']);
Route::get('/products/{id}', [LandingController::class, 'getProductDetail']);

// Direktori Toko / Mitra
Route::get('/stores', [LandingController::class, 'getStores']);


// ========================================================
// 2. AUTENTIKASI (Login & Register)
// ========================================================
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);


// ========================================================
// 3. PRIVATE ROUTES (Membutuhkan Bearer Token / Sanctum)
// ========================================================
Route::middleware('auth:sanctum')->group(function () {

    // User Profile
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Keranjang & Transaksi
    Route::get('/cart', [CartController::class, 'index']);
    Route::post('/cart/add', [CartController::class, 'store']);
    Route::post('/checkout', [TransactionController::class, 'checkout']);

    // Manajemen Proyek / RAB (Jika ada fitur Mandor AI / POTA)
    Route::get('/proyek', [ProjectController::class, 'index']);
});
