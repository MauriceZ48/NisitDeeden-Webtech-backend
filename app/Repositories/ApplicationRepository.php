<?php

namespace App\Repositories;

use App\Enums\ApplicationStatus;
use App\Models\Application;
use App\Models\ApplicationAttributeValue;
use App\Models\Attachment;
use App\Repositories\Traits\SimpleCRUD;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ApplicationRepository
{
    use SimpleCRUD;

    private string $model = Application::class;

    public function countByStatus(ApplicationStatus $status): int
    {
        return $this->model::where('status', $status->value)->count();
    }

    public function getFullApplicationsPaginated(int $perPage = 10)
    {
        return Application::with([
            'attributeValues.attribute',
            'attachments',
            'user',
            'applicationCategory'
        ])
            ->latest()
            ->paginate($perPage);
    }


    public function updateValue(Application $application, $id, $newValue)
    {
        $attributeValue = $application->attributeValues()
            ->with('attribute')
            ->findOrFail($id);

        // Handle File Replacement Logic
        if ($newValue instanceof UploadedFile) {
            // 1. Delete old file if it exists
            if ($attributeValue->attribute?->type === 'file' && $attributeValue->value) {
                Storage::disk('public')->delete($attributeValue->value);
            }

            // 2. Store new file
            $newValue = $newValue->store('applications/dynamic_submissions', 'public');
        }

        return $attributeValue->update(['value' => $newValue ?? '']);
    }

    public function deleteAttachments(array $attachmentIds, int $applicationId)
    {
        $attachments = Attachment::whereIn('id', $attachmentIds)
            ->where('application_id', $applicationId)
            ->get();

        foreach ($attachments as $file) {
            Storage::disk('public')->delete($file->file_path);
            $file->delete();
        }
    }


    public function createAttachment(Application $application, UploadedFile $file): Attachment
    {
        // 1. Physical Storage
        $path = $file->store('applications/attachments', 'public');

        // 2. Database Record
        return $application->attachments()->create([
            'file_path' => $path,
            'file_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
        ]);
    }

    public function clearApplicationData(Application $application)
    {
        // Ensure relations are fresh so we don't miss any files
        $application->load(['attributeValues.attribute', 'attachments']);

        foreach ($application->attributeValues as $value) {
            if ($value->attribute?->type === 'file' && $value->value) {
                Storage::disk('public')->delete($value->value);
            }
            $value->delete();
        }

        foreach ($application->attachments as $attachment) {
            Storage::disk('public')->delete($attachment->file_path);
            $attachment->delete();
        }
    }

    public function createAttributeValue(Application $application, int $attributeId, $value){
        if ($value instanceof UploadedFile) {
            $value = $value->store('applications/dynamic_submissions', 'public');
        }

        $application->attributeValues()->create([
            'category_attribute_id' => $attributeId,
            'value' => $value ?? '',
        ]);
    }


}
