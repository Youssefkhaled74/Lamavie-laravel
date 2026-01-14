<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NumberOfCleaners extends Model
{
    protected $table = 'number_of_cleaners';

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