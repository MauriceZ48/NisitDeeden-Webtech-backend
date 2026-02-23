<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class ApplicationResource extends JsonResource
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
            'status' => $this->status,
            'rejection_reason' => $this->rejection_reason,
//            'created_at' => $this->created_at,

            'submitted_at' => $this->created_at->translatedFormat('d M Y'),
            'submitted_time' => $this->created_at->format('H:i'),
            'timestamp' => $this->created_at->toIso8601String(),

            'user_id' => $this->user_id,
            'application_round_id' => $this->application_round_id,
            'application_category_id' => $this->application_category_id,

            'user' => new UserResource($this->whenLoaded('user')),
//            'round' => new ApplicationRoundResource($this->whenLoaded('applicationRound')),
            'category' => new ApplicationCategoryResource($this->whenLoaded('applicationCategory')),

            'attachments' => $this->whenLoaded('attachments', function () {
                return $this->attachments->map(function ($attachment) {
                    return [
                        'id' => $attachment->id,
                        'file_name' => $attachment->file_name,
                        'file_path' => Storage::disk('public')->url($attachment->file_path),
                        'mime_type' => $attachment->mime_type,
                        'file_size' => $attachment->file_size,
                    ];
                });
            }),

            'values' => $this->whenLoaded('attributeValues', function () {
                return $this->attributeValues->map(function ($attributeValue) {

                    $isFileType = $attributeValue->attribute?->type === 'file';

                    return [
                        'id' => $attributeValue->id,
                        'category_attribute_id' => $attributeValue->category_attribute_id,
                        'label' => $attributeValue->attribute?->label,

                        'value' => $isFileType
                            ? Storage::disk('public')->url($attributeValue->value)
                            : $attributeValue->value,

                        // Tell SvelteKit if it's a file so it knows whether to render text or a download button
                        'is_file' => $isFileType,
                    ];
                });
            }),


        ];
    }
}
