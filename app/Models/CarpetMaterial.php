<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarpetMaterial extends Model
{
    protected $table = 'carpet_material';

    protected $fillable = ['name', 'service_category_id', 'price'];

    protected $casts = [
        'name' => 'array',
    ];

    public function serviceCategory()
    {
        return $this->belongsTo(ServiceCategory::class);
    }
}