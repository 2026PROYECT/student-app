<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class UserResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'        => $this->id,
            'name'      => $this->name,
            'lastname'  => $this->lastname,
            'surname'   => $this->surname,
            'email'     => $this->email,
            'role'      => $this->role,
            'saga_code' => $this->saga_code,
            'id_number' => $this->id_number,
            'career_id' => $this->career_id,
            'career'    => $this->career?->career, // ✅ show career name
            'semester'  => $this->semester,
            'is_active' => $this->is_active,
            'is_admin'  => $this->is_admin,
            // ✅ always return full URL for picture
            'picture' => $this->picture ? Storage::url($this->picture) : null,
            'created_at'=> $this->created_at,
            'updated_at'=> $this->updated_at,
        ];
    }
}
