<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Import semua Controller yang digunakan di API
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\ProjectController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

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


// ========================================================
// 2. AUTENTIKASI
// ========================================================
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);


// ========================================================
// 3. PRIVATE ROUTES (Wajib Login & Menggunakan Sanctum Token)
// ========================================================
Route::middleware('auth:sanctum')->group(function () {

    // User Profile & Logout
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Keranjang Belanja
    Route::get('/cart', [CartController::class, 'index']);
    Route::post('/cart/add', [CartController::class, 'store']);

    // Transaksi / Checkout
    Route::post('/checkout', [TransactionController::class, 'checkout']);

    // Manajemen Proyek / RAB (Mandor POTA AI)
    Route::get('/proyek', [ProjectController::class, 'index']);

});
