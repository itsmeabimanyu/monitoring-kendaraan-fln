<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\KendaraanController;

// Redirect ke halaman monitoring utama
Route::get('/', function () {
    return redirect()->route('kendaraan');
});

// === Monitoring Kendaraan (user & GA, Security) ===
Route::get('/kendaraan', [KendaraanController::class, 'kendaraan'])->name('kendaraan');
Route::get('/monitoring-kendaraan', [KendaraanController::class, 'index'])->name('monitoring.kendaraan');
Route::get('/kendaraan/data', [KendaraanController::class, 'getData']); 

Route::middleware(['auth', 'role:Admin GA,Staff GA,Security'])->group(function () {
    Route::put('/kendaraan/update', [KendaraanController::class, 'update'])->name('kendaraan.update');
});

// === Area Login ===
Route::get('/login', [LoginController::class, 'index'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// === Admin Area (khusus GA) ===
Route::middleware(['auth', 'role:Admin GA,Staff GA'])->group(function () {

    // Dashboard
    Route::get('/admin', [AdminController::class, 'index'])->name('admin');

    // === Manajemen User ===
    Route::get('/users/list', [AdminController::class, 'listUsers'])->name('list.users');
    Route::get('/get-data-users', [AdminController::class, 'getDataUsers'])->name('list.users.data');
    Route::post('/users/ganti-password', [AdminController::class, 'gantiPassword'])->name('users.gantiPassword');
    Route::post('/users/tambah', [AdminController::class, 'tambahUsers'])->name('tambah.users');
    Route::put('/users/{id}/edit', [AdminController::class, 'editUsers'])->name('edit.users');
    Route::delete('/users/{id}/hapus', [AdminController::class, 'hapusUsers'])->name('hapus.users');

    // === Manajemen Kendaraan ===
    Route::get('/kendaraan/list', [AdminController::class, 'listKendaraan'])->name('list.kendaraan');
    Route::get('/get-data-kendaraan', [AdminController::class, 'getDataKendaraan'])->name('list.kendaraan.data');
    Route::post('/kendaraan/tambah', [AdminController::class, 'tambahKendaraan'])->name('tambah.kendaraan');
    Route::put('/kendaraan/{id}/edit', [AdminController::class, 'editKendaraan'])->name('edit.kendaraan');
    Route::delete('/kendaraan/{id}/hapus', [AdminController::class, 'hapusKendaraan'])->name('hapus.kendaraan');
    Route::put('/kendaraan/{id}/sembunyikan', [AdminController::class, 'sembunyikanKendaraan'])->name('sembunyikan.kendaraan');

    // === Riwayat Perubahan Status Kendaraan ===
    Route::get('/kendaraan/history', [AdminController::class, 'historyKendaraan'])->name('history.kendaraan');
    Route::get('/get-data-history-kendaraan', [AdminController::class, 'getDatahistoryKendaraan'])->name('history.kendaraan.data');
});
