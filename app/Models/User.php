<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable
{
    use Notifiable, HasFactory;

    /**
     * Konfigurasi Primary Key untuk UUID Keycloak
     */
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    /**
     * Atribut yang dapat diisi (Mass Assignable)
     */
    protected $fillable = [
        'id',
        'nama',
        'email',
        'password',
        'jabatan',
        'is_active',
        'role', 
    ];
 
    /**
     * Menonaktifkan updated_at jika tabelmu hanya punya created_at
     * Jika tabelmu punya keduanya, hapus baris ini.
     */
    const UPDATED_AT = null;

    /**
     * Atribut yang disembunyikan saat serialisasi (API/JSON)
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Casting tipe data
     */
    protected $casts = [
        'is_active' => 'boolean',
        'email_verified_at' => 'datetime',
    ];

    /**
     * RELASI: User memiliki banyak aplikasi melalui tabel pivot
     * Digunakan untuk mengatur: Kinan (Semua), Reja (Sista & Pemanis)
     */
public function applications()
{
    return $this->belongsToMany(
        Application::class, 
        'user_app_access', 
        'user_id', 
        'app_id'
    ); // HAPUS ->withTimestamps() di sini
}

    /**
     * ACCESSOR: Role
     * Memungkinkan pemanggilan $user->role di Blade secara konsisten
     */
    public function getRoleAttribute()
    {
        return $this->jabatan;
    }

    /**
     * HELPER: Cek akses aplikasi spesifik
     * Contoh: if($user->hasAccessTo('sista-pe5ip')) { ... }
     */
    public function hasAccessTo($client_id)
    {
        return $this->applications()->where('client_id', $client_id)->exists();
    }

    /**
     * HELPER: Cek apakah user adalah Admin
     */
    public function isAdmin()
    {
        return $this->jabatan === 'admin';
    }
}