<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class KendaraanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('kendaraans')->insert([
            [
             'nama_mobil' => 'Avanza', 
             'nopol' => 'B 1026 FRK',
             'status' => 'Stand By',
             'nama_pemakai' => '',
             'departemen' => '',
             'driver' => '',
             'tujuan' => '',
             'keterangan' => ''
            ],
            [
             'nama_mobil' => 'Xenia', 
             'nopol' => 'B 1481 FIC',
             'status' => 'Stand By',
             'nama_pemakai' => '',
             'departemen' => '',
             'driver' => '',
             'tujuan' => '',
             'keterangan' => ''
            ],
            [
             'nama_mobil' => 'Xenia', 
             'nopol' => 'B 1268 FIC',
             'status' => 'Stand By',
             'nama_pemakai' => '',
             'departemen' => '',
             'driver' => '',
             'tujuan' => '',
              'keterangan' => ''
            ],
            [
             'nama_mobil' => 'Xenia', 
             'nopol' => 'B 1269 FIC',
             'status' => 'Stand By',
             'nama_pemakai' => '',
             'departemen' => '',
             'driver' => '',
             'tujuan' => '',
              'keterangan' => ''
            ],
            [
             'nama_mobil' => 'Yaris', 
             'nopol' => 'B 1341 BYT',
             'status' => 'Stand By',
             'nama_pemakai' => '',
             'departemen' => '',
             'driver' => '',
             'tujuan' => '',
              'keterangan' => ''
            ],
            [
             'nama_mobil' => 'Gran Max', 
             'nopol' => 'B 2470 FOK',
             'status' => 'Stand By',
             'nama_pemakai' => '',
             'departemen' => '',
             'driver' => '',
             'tujuan' => '',
              'keterangan' => ''
            ],
            [
             'nama_mobil' => 'Isuzu Traga', 
             'nopol' => 'B 9706 FCM',
             'status' => 'Stand By',
             'nama_pemakai' => '',
             'departemen' => '',
             'driver' => '',
             'tujuan' => '',
              'keterangan' => ''
            ],
            [
             'nama_mobil' => 'Isuzu SKT', 
             'nopol' => 'F 8320 GC',
             'status' => 'Stand By',
             'nama_pemakai' => '',
             'departemen' => '',
             'driver' => '',
             'tujuan' => '',
              'keterangan' => ''
            ],
        ]);
    }
}
