<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserPoints extends Model
{
    protected $fillable = ['user_id', 'booking_id', 'points'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}