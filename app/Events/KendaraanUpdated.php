<?php

namespace App\Events;

use App\Models\Kendaraan;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class KendaraanUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $kendaraan;

    public function __construct(Kendaraan $kendaraan)
    {
        $this->kendaraan = $kendaraan;
    }

    public function broadcastOn()
    {
        // Menggunakan public channel
        return new Channel('kendaraan.' . $this->kendaraan->id);
    }

    public function broadcastWith()
    {
        return [
            'id' => $this->kendaraan->id,
            'status' => $this->kendaraan->status,
            'updated_at' => $this->kendaraan->updated_at->format('Y-m-d H:i:s'),
        ];
    }

    public function broadcastAs()
    {
        return 'KendaraanUpdated';
    }
}
