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
            ['username' => 'flnga', 'nama_lengkap' => 'Flnga', 'password' => Hash::make('123456'), 'jabatan' => 'Admin GA'],
            ['username' => 'widiartip', 'nama_lengkap' => 'Widiarti Putri', 'password' => Hash::make('123456'), 'jabatan' => 'Staff GA'],
            ['username' => 'ades', 'nama_lengkap' => 'Ade Suyatna', 'password' => Hash::make('123456'), 'jabatan' => 'Staff GA'],
            ['username' => 'muhhajirin', 'nama_lengkap' => 'Muhhajirin', 'password' => Hash::make('123456'), 'jabatan' => 'Security'],
            ['username' => 'abubakar', 'nama_lengkap' => 'Abu Bakar', 'password' => Hash::make('123456'), 'jabatan' => 'Security'],
            ['username' => 'suratno', 'nama_lengkap' => 'Suratno', 'password' => Hash::make('123456'), 'jabatan' => 'Security'],
            ['username' => 'mikbalarief', 'nama_lengkap' => 'Mikbal Arief', 'password' => Hash::make('123456'), 'jabatan' => 'Security'],
            ['username' => 'haifanurulaini', 'nama_lengkap' => 'Haifa Nurul Aini', 'password' => Hash::make('123456'), 'jabatan' => 'Security'],
            ['username' => 'uuttrisafain', 'nama_lengkap' => 'Uut Tri Safain', 'password' => Hash::make('123456'), 'jabatan' => 'Security'],
            ['username' => 'ivannovianto', 'nama_lengkap' => 'Ivan Novianto', 'password' => Hash::make('123456'), 'jabatan' => 'Security'],
            ['username' => 'fajarnugraha', 'nama_lengkap' => 'Fajar Nugraha', 'password' => Hash::make('123456'), 'jabatan' => 'Security'],
            ['username' => 'muhammadnurulhuda', 'nama_lengkap' => 'Muhammad Nurul Huda', 'password' => Hash::make('123456'), 'jabatan' => 'Security'],
            ['username' => 'supriatna', 'nama_lengkap' => 'Supriatna', 'password' => Hash::make('123456'), 'jabatan' => 'Security'],
            ['username' => 'dadangsopian', 'nama_lengkap' => 'Dadang Sopian', 'password' => Hash::make('123456'), 'jabatan' => 'Security'],
            ['username' => 'susilabayuadji', 'nama_lengkap' => 'Susila Bayu Adji', 'password' => Hash::make('123456'), 'jabatan' => 'Security'],
            ['username' => 'andiyuhandi', 'nama_lengkap' => 'Andi Yuhandi', 'password' => Hash::make('123456'), 'jabatan' => 'Security'],
            ['username' => 'mohammadbayuputra', 'nama_lengkap' => 'Mohammad Bayu Putra', 'password' => Hash::make('123456'), 'jabatan' => 'Security'],
            ['username' => 'hendrojatmiko', 'nama_lengkap' => 'Hendro Jatmiko', 'password' => Hash::make('123456'), 'jabatan' => 'Security'],
            ['username' => 'ajissaputra', 'nama_lengkap' => 'Ajis Saputra', 'password' => Hash::make('123456'), 'jabatan' => 'Security'],
            ['username' => 'candradwiaryosaputro', 'nama_lengkap' => 'Candra Dwi Aryo Saputro', 'password' => Hash::make('123456'), 'jabatan' => 'Security'],
            ['username' => 'willymardian', 'nama_lengkap' => 'Willy Mardian', 'password' => Hash::make('123456'), 'jabatan' => 'Security'],
            ['username' => 'farid', 'nama_lengkap' => 'Farid', 'password' => Hash::make('123456'), 'jabatan' => 'Security'],
            ['username' => 'maditiajuliyanti', 'nama_lengkap' => 'Maditia Juliyanti', 'password' => Hash::make('123456'), 'jabatan' => 'Security'],
            ['username' => 'bagasedisantoso', 'nama_lengkap' => 'Bagas Edi Santoso', 'password' => Hash::make('123456'), 'jabatan' => 'Security'],
            ['username' => 'muhammadrizki', 'nama_lengkap' => 'Muhammad Rizki', 'password' => Hash::make('123456'), 'jabatan' => 'Security'],
            ['username' => 'muhammadrizalsaputra', 'nama_lengkap' => 'Muhammad Rizal Saputra', 'password' => Hash::make('123456'), 'jabatan' => 'Security'],
            ['username' => 'faturrachman', 'nama_lengkap' => 'Fatur Rachman', 'password' => Hash::make('123456'), 'jabatan' => 'Security'],
        ];        

        DB::table('users')->insert($users);
    }
}
