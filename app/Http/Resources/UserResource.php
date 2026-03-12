<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {


        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'university_id' => $this->university_id,
            'domain_en' => $this->domain,
            'domain_th' => $this->domain->label(),
            'faculty' => $this->faculty,
            'department' => $this->department,
            'role' => $this->role,
            'position_en' => $this->position,
            'position_th' => $this->position_thai,
            'profile_url' => asset($this->profile_url),
        ];
    }
}
