<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TypeOfStain extends Model
{
    protected $table = 'type_of_stain';


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
