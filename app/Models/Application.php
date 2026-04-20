<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    // Jika nama tabel di database bukan 'applications', aktifkan baris di bawah ini:
    // protected $table = 'nama_tabel_aplikasi_anda';

    protected $guarded = [];

    // Relasi balik (opsional): Satu aplikasi bisa memiliki banyak log aktivitas
    public function logs()
    {
        return $this->hasMany(LogActivity::class, 'app_id');
    }
}