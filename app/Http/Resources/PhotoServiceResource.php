<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PhotoServiceResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'service_id' => $this->service_id,
            'file_path' => $this->file_path ? asset('storage/' . $this->file_path) : null,
            'photo_name' => $this->photo_name,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}