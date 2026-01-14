<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;


class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;
    use HasApiTokens;


    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'phone_verified_at',
        'email_verified_at',
        'profile_photo',
        'unique_code',
        'otp_code',
        'new_phone',
        'new_phone_verified_at',
        'points',
        'fcm_token',
        'language',
        'area_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string,string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'phone_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Relationship: user's assigned area
     */
    public function area()
    {
        return $this->belongsTo(Area::class, 'area_id');
    }
    /**
     * Route notifications for the Firebase channel.
     */
    public function routeNotificationForFirebase()
    {
        return $this->fcm_token;
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    protected static function booted()
    {
        static::creating(function ($user) {
            if (empty($user->unique_code)) {
                $user->unique_code = static::generateUniqueCode();
            }
        });
    }

    public static function generateUniqueCode($prefix = 'U', $length = 8)
    {
        // generate a reasonably short, URL-safe, unique code and ensure uniqueness in DB
        do {
            $rand = strtoupper(substr(bin2hex(random_bytes(4)), 0, $length));
            $code = $prefix . $rand;
        } while (static::where('unique_code', $code)->exists());

        return $code;
    }

    public function pointsHistory()
    {
        return $this->hasMany(UserPoints::class, 'user_id');
    }
}
