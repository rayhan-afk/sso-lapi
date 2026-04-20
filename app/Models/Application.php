<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    public $timestamps = false;

    protected $table = 'applications';

    protected $fillable = [
        'app_name', 
        'client_id', 
        'keycloak_client_uuid',
        'client_secret',
        'url_aplikasi', 
        'icon_aplikasi', 
        'status'
    ];

    // Relasi: satu aplikasi punya banyak log
    public function logs()
    {
        return $this->hasMany(LogActivity::class, 'app_id');
    }
}