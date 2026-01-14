<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EstimatedHours extends Model
{
    protected $table = 'estimated_hours';

    protected $fillable = ['name', 'service_category_id', 'price'];

    protected $casts = [
        'name' => 'array',
        'price' => 'decimal:2',
    ];

    public function serviceCategory()
    {
        return $this->belongsTo(ServiceCategory::class);
    }
}