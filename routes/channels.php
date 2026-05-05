<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Ruang obrolan Penjual (Seller)
Broadcast::channel('chat.{chatId}', function ($user, $chatId) {
    return true;
});

// Ruang obrolan Pembeli (Customer)
Broadcast::channel('chat.toko.{storeId}', function ($user, $storeId) {
    return true;
});

// Notifikasi global Penjual
Broadcast::channel('seller.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Notifikasi global Pembeli
Broadcast::channel('user.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});
