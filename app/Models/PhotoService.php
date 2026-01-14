<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PhotoService extends Model
{
    protected $table = 'photo_services';

    protected $fillable = ['file_path', 'service_id', 'photo_name'];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}
