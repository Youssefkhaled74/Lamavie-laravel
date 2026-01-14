<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarpetSize extends Model
{
    protected $table = 'carpet_size';

    protected $fillable = ['name', 'service_category_id', 'price'];

    protected $casts = [
        'name' => 'array',
    ];

    public function serviceCategory()
    {
        return $this->belongsTo(ServiceCategory::class);
    }
}