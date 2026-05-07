<?php

use Illuminate\Support\Facades\Broadcast;

// Channel bawaan Laravel
Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// ========================================================
// PERBAIKAN: Ruang Obrolan Pribadi (1-on-1)
// Channel disatukan untuk Customer & Seller agar saling terhubung
// ========================================================
Broadcast::channel('chat.room.{userId}.{storeId}', function ($user, $userId, $storeId) {

    // 1. Izinkan jika yang mengakses adalah Customer (Pembeli) pemilik chat ini
    if ((int) $user->id === (int) $userId) {
        return true;
    }

    // 2. Izinkan jika yang mengakses adalah Seller (Pemilik Toko)
    // Cek apakah user yang login memiliki store_id yang sama dengan channel
    if (isset($user->store_id) && (int) $user->store_id === (int) $storeId) {
        return true;
    }

    // (Opsional) Jika di database Anda ID User Seller sama dengan ID Toko,
    // gunakan pengecekan ini:
    if ((int) $user->id === (int) $storeId) {
        return true;
    }

    // Jika bukan pembeli dan bukan penjual yang bersangkutan, TOLAK AKSES! (Keamanan)
    return false;
});


// ========================================================
// NOTIFIKASI GLOBAL (Untuk Update Angka Unread di Sidebar)
// ========================================================

// Notifikasi global Penjual
Broadcast::channel('seller.{id}', function ($user, $id) {
    // Hanya izinkan seller itu sendiri yang bisa menerima notifnya
    return (int) $user->id === (int) $id;
});

// Notifikasi global Pembeli
Broadcast::channel('user.{id}', function ($user, $id) {
    // Hanya izinkan pembeli itu sendiri yang bisa menerima notifnya
    return (int) $user->id === (int) $id;
});
