<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogActivity extends Model
{
    protected $guarded = [];

    const UPDATED_AT = null; 

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function application()
    {
        return $this->belongsTo(Application::class, 'app_id');
    }

    // Accessor untuk nama aplikasi
    public function getApplicationNameAttribute()
    {
        return $this->application ? $this->application->app_name : 'Dashboard SSO';
    }
}