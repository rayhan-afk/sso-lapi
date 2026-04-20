<?php

// app/Models/Application.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    public $timestamps = false; // Karena di SQL kamu tidak ada kolom created_at/updated_at untuk tabel ini
    
    protected $table = 'applications';

    protected $fillable = [
    'app_name', 
    'client_id', 
    'keycloak_client_uuid',
    'client_secret', // Tambahkan ini
    'url_aplikasi', 
    'icon_aplikasi', 
    'status'
];

}