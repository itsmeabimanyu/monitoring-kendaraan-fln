<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            ['username' => 'admin', 'password' => Hash::make('123')],
            ['username' => 'nurhidayatullah', 'password' => Hash::make('123')],
            ['username' => 'nilamsantikayani', 'password' => Hash::make('123')],
            ['username' => 'abubakar', 'password' => Hash::make('123')],
            ['username' => 'wiryawijaya', 'password' => Hash::make('123')],
            ['username' => 'uuttrisafain', 'password' => Hash::make('123')],
            ['username' => 'ariadhari', 'password' => Hash::make('123')],
            ['username' => 'ahmadjayasaputra', 'password' => Hash::make('123')],
            ['username' => 'dedeyusup', 'password' => Hash::make('123')],
            ['username' => 'fajarnugraha', 'password' => Hash::make('123')],
            ['username' => 'supardi', 'password' => Hash::make('123')],
            ['username' => 'hafizramadhan', 'password' => Hash::make('123')],
            ['username' => 'ajipuji', 'password' => Hash::make('123')],
            ['username' => 'bagasedi', 'password' => Hash::make('123')],
            ['username' => 'fajarsidik', 'password' => Hash::make('123')],
            ['username' => 'sunansusanto', 'password' => Hash::make('123')],
            ['username' => 'erwin', 'password' => Hash::make('123')],
            ['username' => 'kemalsuhayat', 'password' => Hash::make('123')],
            ['username' => 'warman', 'password' => Hash::make('123')],
            ['username' => 'diartamat', 'password' => Hash::make('123')],
            ['username' => 'johanessouhoka', 'password' => Hash::make('123')],
            ['username' => 'ripanpradesh', 'password' => Hash::make('123')],
            ['username' => 'andiyuhandi', 'password' => Hash::make('123')],
            ['username' => 'wahyuputra', 'password' => Hash::make('123')],
            ['username' => 'aguspurnairawan', 'password' => Hash::make('123')],
        ];

        DB::table('users')->insert($users);
    }
}
