<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('chat.toko.{storeId}', function ($user, $storeId) {
    // Untuk saat ini, kita izinkan semua user yang login untuk terhubung ke jalurnya
    return $user != null;
});
