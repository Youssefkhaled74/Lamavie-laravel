<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Service;
use Illuminate\Notifications\Notifiable;

class Driver extends Authenticatable
{
    use SoftDeletes;
    use Notifiable;

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

    /**
     * Route notifications for the Firebase channel.
     */
    public function routeNotificationForFirebase()
    {
        return $this->fcm_token ?? null;
    }
}
