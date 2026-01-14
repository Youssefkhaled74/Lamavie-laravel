<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lab extends Authenticatable
{
    use SoftDeletes;

    protected $table = 'labs';

    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'meta',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}
