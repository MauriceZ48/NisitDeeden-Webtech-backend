<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ApplicationRoundResource extends JsonResource
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
            'semester_en' => $this->semester,
            'semester_th' => $this->semester->label(),
            'academic_year_en' => $this->academic_year,
            'academic_year_th' => $this->thai_academic_year,
            'start_time_en' => $this->start_time,
            'end_time_en' => $this->end_time,
            'start_time_th' => $this->start_time->toThaiDateTime(),
            'end_time_th' => $this->end_time->toThaiDateTime(),
            'status' => $this->status,
            'domain_en' => $this->domain,
            'domain_th' => $this->domain->label(),
        ];
    }
}
