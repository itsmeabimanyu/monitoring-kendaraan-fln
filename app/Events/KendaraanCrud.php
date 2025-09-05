<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class KendaraanCrud implements ShouldBroadcast
{
    use SerializesModels;

    public $action;       // add, edit, delete
    public $kendaraanId;  // ID kendaraan yang berubah
    public $data;         // Opsional: data kendaraan

    public function __construct(string $action, int $kendaraanId, $data = null)
    {
        $this->action = $action;
        $this->kendaraanId = $kendaraanId;
        $this->data = $data;
    }

    public function broadcastOn()
    {
        return new Channel('kendaraan.global');
    }

    public function broadcastAs()
    {
        return 'KendaraanCrud';
    }
}
