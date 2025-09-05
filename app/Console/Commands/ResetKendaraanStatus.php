<?php

namespace App\Console\Commands;

use App\Models\Kendaraan;
use Illuminate\Console\Command;

class ResetKendaraanStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */

    protected $signature = 'reset:kendaraan-status';
    protected $description = 'Reset status semua kendaraan menjadi Stand By setiap hari';

     public function handle()
    {
        Kendaraan::where('status', '!=', 'Stand By')->update(['status' => 'Stand By', 'tujuan' => '']);

        $this->info('Status kendaraan berhasil di-reset ke Stand By.');
    }
}
