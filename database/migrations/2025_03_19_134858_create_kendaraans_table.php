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
        Schema::create('kendaraans', function (Blueprint $table) {
            $table->id();
            $table->string('nama_mobil');
            $table->string('nopol');
            $table->string('gambar_mobil')->nullable();
            $table->enum('status',['Stand By', 'Pergi', 'Perbaikan'])->default('Stand By');
            $table->boolean('isActive')->default(1);
            $table->boolean('isVisible')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kendaraans');
    }
};
