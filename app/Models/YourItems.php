<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class YourItems extends Model
{
    protected $table = 'your_items';

    protected $fillable = ['name', 'service_category_id', 'logo', 'price'];

    protected $casts = [
        'name' => 'array',
    ];

    public function serviceCategory()
    {
        return $this->belongsTo(ServiceCategory::class, 'service_category_id');
    }
}