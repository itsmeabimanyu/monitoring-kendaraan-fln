<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('history_kendaraans', function (Blueprint $table) {
            $table->uuid('id')->primary(); // Ganti id auto-increment menjadi uuid
            $table->foreignId('kendaraan_id')->constrained('kendaraans')->onDelete('cascade');
            $table->string('nama_mobil');
            $table->string('nopol');
            $table->enum('status', ['Stand By', 'Pergi', 'Perbaikan']);
            $table->string('nama_pemakai')->nullable();
            $table->string('departemen')->nullable();
            $table->string('driver')->nullable();
            $table->text('tujuan')->nullable();
            $table->string('keterangan')->nullable();
            $table->string('pic_update')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('history_kendaraans');
    }
};
