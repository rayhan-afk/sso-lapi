<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    // 1. Beri tahu Laravel bahwa primary key adalah 'id' bertipe string (UUID), bukan auto-increment
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    // 2. Daftarkan kolom apa saja yang boleh diisi datanya
    protected $fillable = [
        'id',
        'nama',
        'email',
        'password',
        'jabatan',
        'is_active',
    ];

    // 3. Matikan kolom updated_at karena di tabelmu hanya ada created_at
    const UPDATED_AT = null;

    // Sembunyikan password saat data user dipanggil (best practice keamanan)
    protected $hidden = [
        'password',
    ];
}