<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class Lab extends Authenticatable
{
    use SoftDeletes;
    use Notifiable;

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

    /**
     * Route notifications for the Firebase channel.
     */
    public function routeNotificationForFirebase()
    {
        return $this->fcm_token ?? null;
    }
}
