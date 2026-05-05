<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
// GANTI ShouldBroadcast JADI ShouldBroadcastNow
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
        // Tembak ke 4 channel sekaligus biar Penjual & Pembeli pasti dapat notif
        return [
            new PrivateChannel('chat.' . ($this->message['chat_id'] ?? 0)),
            new PrivateChannel('chat.toko.' . ($this->message['store_id'] ?? $this->message['toko_id'] ?? 0)),
            new PrivateChannel('seller.' . ($this->message['sender_id'] ?? 0)),
            new PrivateChannel('user.' . ($this->message['customer_id'] ?? 0)),
        ];
    }

    // NAMA EVENT INI HARUS ADA BIAR JAVASCRIPT BISA BACA!
    public function broadcastAs()
    {
        return 'PesanBaruTerkirim';
    }
}
