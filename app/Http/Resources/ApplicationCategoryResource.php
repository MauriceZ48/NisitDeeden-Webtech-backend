<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ApplicationCategoryResource extends JsonResource
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
            'description' => $this->description,
            'icon' => $this->icon,
            'is_active' => (bool) $this->is_active,
            'domain_en' => $this->domain,
            'domain_th' => $this->domain->label(),

            'attributes' => $this->whenLoaded('attributes', function () {
                return $this->attributes->map(function ($attribute) {
                    return [
                        'id' => $attribute->id,
                        'label' => $attribute->label,
                        'type' => $attribute->type,
                        'is_required' => (bool) $attribute->is_required,
                    ];
                });
            }),
        ];
    }
}
