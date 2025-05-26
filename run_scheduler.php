<?php

while (true) {
    echo "[" . date('Y-m-d H:i:s') . "] Menjalankan Laravel Scheduler...\n";
    
    // Jalankan scheduler Laravel
    shell_exec('php artisan schedule:run');

    // Tunggu 60 detik sebelum menjalankan lagi
    sleep(60);
}
