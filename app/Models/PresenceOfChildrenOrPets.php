<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PresenceOfChildrenOrPets extends Model
{
    protected $table = 'presence_of_children_or_pets';

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