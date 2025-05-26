<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class HistoryKendaraan extends Model
{
    protected $table = 'history_kendaraans';
    // Menentukan bahwa ID menggunakan UUID
    public $incrementing = false;
    protected $keyType = 'string'; // Gunakan string untuk UUID

    // Auto-generate UUID untuk setiap record baru
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid(); // Membuat UUID baru saat membuat record
            }
        });
    }

    // Daftar kolom yang dapat diisi
    protected $fillable = [
        'kendaraan_id',
        'nama_mobil',
        'nopol',
        'status',
        'nama_pemakai',
        'departemen',
        'driver',
        'tujuan',
        'keterangan',
        'pic_update'
    ];
}
