<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FabricType extends Model
{
    protected $table = 'fabric_types';

    protected $fillable = ['name', 'service_category_id', 'price'];

    protected $casts = [
        'name' => 'array',
    ];

    public function serviceCategory()
    {
        return $this->belongsTo(ServiceCategory::class);
    }
}