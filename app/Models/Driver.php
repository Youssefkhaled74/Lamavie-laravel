<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Service;

class Driver extends Authenticatable
{
    use SoftDeletes;

    protected $table = 'drivers';

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function services()
    {
        return $this->belongsToMany(Service::class, 'driver_service')->withTimestamps();
    }
}
