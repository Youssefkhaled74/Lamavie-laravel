<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    protected $table = 'payments_method';

    protected $fillable = ['name', 'status'];

    protected $casts = [
        'name' => 'array',
        'status' => 'boolean',
    ];
}
