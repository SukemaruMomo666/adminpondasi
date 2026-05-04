<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

// PERHATIKAN: Kita pakai ShouldBroadcastNow biar pesannya langsung wusss tanpa ngantri!
class PesanBaruTerkirim implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message; // Data pesan yang dikirim (bisa teks, gambar, dll)
    public $storeId; // ID Toko tempat ngobrol

    public function __construct($message, $storeId)
    {
        $this->message = $message;
        $this->storeId = $storeId;
    }

    // Tentukan jalur khusus (Private Channel) agar obrolannya rahasia dan aman
    public function broadcastOn()
    {
        return new PrivateChannel('chat.toko.' . $this->storeId);
    }
}
