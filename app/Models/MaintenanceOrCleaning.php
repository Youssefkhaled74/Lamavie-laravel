<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaintenanceOrCleaning extends Model
{
    use HasFactory;

    protected $table = 'maintenance_or_cleaning'; // Explicitly set the table name

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