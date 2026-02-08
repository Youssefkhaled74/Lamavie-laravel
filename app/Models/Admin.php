<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class Admin extends Authenticatable
{
    use Notifiable;
    use HasRoles;

    /**
     * Guard name for Spatie permission checks
     *
     * @var string
     */
    protected $guard_name = 'admin';

    protected $table = 'admins';

    protected $fillable = [
        'name',
        'email',
        'password',
        'fcm_token',
        'photo',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Route notifications for the Firebase channel.
     */
    public function routeNotificationForFirebase()
    {
        return $this->fcm_token;
    }
}
