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
                'gambar_mobil' => 'Avanza_B 1026 FRK.jpeg',
                'status' => 'Stand By',
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'nama_mobil' => 'Xenia',
                'nopol' => 'B 1481 FIC',
                'gambar_mobil' => 'Xenia_B 1481 FIC.jpeg',
                'status' => 'Stand By',
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'nama_mobil' => 'Xenia',
                'nopol' => 'B 1268 FIC',
                'gambar_mobil' => 'Xenia_B 1268 FIC.jpeg',
                'status' => 'Stand By',
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'nama_mobil' => 'Xenia',
                'nopol' => 'B 1269 FIC',
                'gambar_mobil' => 'Xenia_B 1269 FIC.jpeg',
                'status' => 'Stand By',
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'nama_mobil' => 'Yaris',
                'nopol' => 'B 1341 BYT',
                'gambar_mobil' => 'Yaris_B 1341 BYT.jpeg',
                'status' => 'Stand By',
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'nama_mobil' => 'Gran Max',
                'nopol' => 'B 2470 FOK',
                'gambar_mobil' => 'Gran Max_B 2470 FOK.jpeg',
                'status' => 'Stand By',
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'nama_mobil' => 'Isuzu Traga',
                'nopol' => 'B 9706 FCM',
                'gambar_mobil' => 'Traga_B 9706 FCM.jpeg',
                'status' => 'Stand By',
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'nama_mobil' => 'Isuzu SKT',
                'nopol' => 'F 8320 GC',
                'gambar_mobil' => 'SKT_F 8320 GC.jpeg',
                'status' => 'Stand By',
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'nama_mobil' => 'Hiace Abu',
                'nopol' => 'F 7106 FC',
                'gambar_mobil' => 'Hiace Abu_F 7106 FC.png',
                'status' => 'Stand By',
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'nama_mobil' => 'Hiace Putih',
                'nopol' => 'B 7065 FDB',
                'gambar_mobil' => 'Hiace Putih_B 7065 FDB.png',
                'status' => 'Stand By',
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
        ]);
    }
}
