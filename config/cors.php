<?php
return [

    'defaults' => [
        'supports_credentials' => true,  // Setel ke true agar Pusher dan WebSockets bisa bekerja dengan baik
        'allowed_origins' => ['*'],  // Sesuaikan jika kamu ingin memperbolehkan domain tertentu
        'allowed_headers' => ['*'],  // Atau tentukan header yang diizinkan jika perlu
        'allowed_methods' => ['*'],  // Jika perlu, bisa sesuaikan
        'exposed_headers' => [],
        'max_age' => 0,
    ],

    'paths' => ['api/*', 'broadcasting/*'],  // Tambahkan broadcasting untuk menangani WebSocket / Pusher
];
