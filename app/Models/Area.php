<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    protected $table = 'areas';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'price_increase_percentage',
    ];

    protected $casts = [
        'name' => 'array',
        'price_increase_percentage' => 'float',
    ];

    public $timestamps = true;

    public function users()
    {
        return $this->hasMany(User::class, 'area_id');
    }

    /**
     * Calculate the adjusted price for this area given a base price.
     *
     * @param  float  $price
     * @return float
     */
    public function adjustedPrice(float $price): float
    {
        $percent = $this->price_increase_percentage ?? 0.0;

        $multiplier = 1 + (floatval($percent) / 100.0);

        return round($price * $multiplier, 2);
    }
}
