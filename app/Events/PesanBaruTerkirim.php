<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

// WAJIB PAKAI ShouldBroadcastNow biar langsung tembus tanpa antri!
class PesanBaruTerkirim implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;

    public function __construct($message)
    {
        $this->message = $message;
    }

    public function broadcastOn()
    {
        // 1. Ekstrak data dengan aman (Mendukung jika $message berupa Array maupun Eloquent Model)
        $userId = is_array($this->message)
            ? ($this->message['user_id'] ?? $this->message['customer_id'] ?? 0)
            : ($this->message->user_id ?? $this->message->customer_id ?? 0);

        $storeId = is_array($this->message)
            ? ($this->message['store_id'] ?? $this->message['toko_id'] ?? 0)
            : ($this->message->store_id ?? $this->message->toko_id ?? 0);

        $sender = is_array($this->message)
            ? ($this->message['sender'] ?? '')
            : ($this->message->sender ?? '');

        // Asumsi ID user penjual (seller_id) sama dengan store_id jika tidak didefinisikan spesifik
        $sellerId = is_array($this->message)
            ? ($this->message['seller_id'] ?? $storeId)
            : ($this->message->seller_id ?? $storeId);


        // 2. Tentukan Channel Tujuan (Arsitektur Sinkronisasi Baru)
        $channels = [
            // RUANG UTAMA: Wajib ditembak agar balon chat langsung muncul di layar kedua belah pihak
            new PrivateChannel("chat.room.{$userId}.{$storeId}")
        ];

        // RUANG NOTIFIKASI GLOBAL: Untuk update badge merah & list obrolan di sidebar
        if ($sender === 'user' || $sender === 'customer') {
            // Jika yang kirim Pelanggan, maka ping/notif ke channel global Penjual
            $channels[] = new PrivateChannel("seller.{$sellerId}");
        } else {
            // Jika yang kirim Penjual, maka ping/notif ke channel global Pelanggan
            $channels[] = new PrivateChannel("user.{$userId}");
        }

        return $channels;
    }

    // NAMA EVENT INI HARUS ADA BIAR JAVASCRIPT BISA BACA!
    public function broadcastAs()
    {
        return 'PesanBaruTerkirim';
    }
}
