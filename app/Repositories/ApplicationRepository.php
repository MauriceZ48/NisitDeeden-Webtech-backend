<?php

namespace App\Repositories;

use App\Enums\ApplicationStatus;
use App\Models\Application;
use App\Models\ApplicationAttributeValue;
use App\Models\Attachment;
use App\Models\User;
use App\Repositories\Traits\SimpleCRUD;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ApplicationRepository
{
    use SimpleCRUD;

    private string $model = Application::class;

    private function getDomain()
    {
        return auth()->user()?->domain;
    }

    public function countByStatus(ApplicationStatus $status): int
    {
        return $this->model::where('domain', $this->getDomain())
            ->where('status', $status->value)
            ->count();
    }

    public function getFullApplicationsInDomainPaginated(int $perPage = 10)
    {
        return Application::with([
            'attributeValues.attribute',
            'attachments',
            'applicationRound',
            'user',
            'applicationCategory'
        ])
            ->where('domain', $this->getDomain())
            ->latest()
            ->paginate($perPage);
    }


    public function updateValue(Application $application, $id, $newValue)
    {
        $attributeValue = $application->attributeValues()
            ->with('attribute')
            ->where('category_attribute_id', $id)
            ->firstOrNew([
                'application_id' => $application->id,
                'category_attribute_id' => $id,
            ]);

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

    public function updateValueForBackend(Application $application, $id, $newValue)
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

    public function createAttributeValue(Application $application, int $attributeId, $value)
    {
        if ($value instanceof UploadedFile) {
            $value = $value->store('applications/dynamic_submissions', 'public');
        }

        $application->attributeValues()->create([
            'category_attribute_id' => $attributeId,
            'value' => $value ?? '',
        ]);
    }

    public function getPendingForHeadOfDepartment(?int $categoryId = null, int $perPage = 10)
    {
        $user = auth()->user();

        return Application::with([
            'attributeValues.attribute',
            'applicationRound',
            'user',
            'applicationCategory'
        ])
            ->where('domain', $this->getDomain())
            ->where('status', ApplicationStatus::PENDING)
            ->whereHas('user', function ($query) use ($user) {
                $query->where('department', $user->department);
            })
            ->when($categoryId, function ($query) use ($categoryId) {
                $query->where('application_category_id', $categoryId);
            })
            ->latest()
            ->paginate($perPage);
    }

    public function getPendingForAssociateDean(?int $categoryId = null, ?string $department = null, int $perPage = 10)
    {
        $user = auth()->user();

        return Application::with([
            'attributeValues.attribute',
            'applicationRound',
            'user',
            'applicationCategory'
        ])
            ->where('domain', $this->getDomain())
            ->where('status', ApplicationStatus::APPROVED_BY_DEPARTMENT)
            ->whereHas('user', function ($query) use ($user, $department) {
                $query->where('faculty', $user->faculty);

                if ($department) {
                    $query->where('department', $department);
                }
            })
            ->when($categoryId, function ($query) use ($categoryId) {
                $query->where('application_category_id', $categoryId);
            })
            ->latest()
            ->paginate($perPage);
    }

    public function getPendingForDean(?int $categoryId = null, ?string $department = null, int $perPage = 10)
    {
        $user = auth()->user();

        return Application::with([
            'attributeValues.attribute',
            'applicationRound',
            'user',
            'applicationCategory'
        ])
            ->where('domain', $this->getDomain())
            ->where('status', ApplicationStatus::APPROVED_BY_ASSOCIATE_DEAN)
            ->whereHas('user', function ($query) use ($user, $department) {
                $query->where('faculty', $user->faculty);

                if ($department) {
                    $query->where('department', $department);
                }
            })
            ->when($categoryId, function ($query) use ($categoryId) {
                $query->where('application_category_id', $categoryId);
            })
            ->latest()
            ->paginate($perPage);
    }

    public function getPendingForCommittee(?int $categoryId = null, ?string $department = null, ?string $faculty = null, int $perPage = 10)
    {
        return Application::with([
            'attributeValues.attribute',
            'applicationRound',
            'user',
            'applicationCategory'
        ])
            ->where('status', ApplicationStatus::APPROVED_BY_DEAN)
            ->where('domain', $this->getDomain())
            ->whereHas('user', function ($query) use ($department, $faculty) {
                if ($faculty) {
                    $query->where('faculty', $faculty);
                }

                if ($department) {
                    $query->where('department', $department);
                }
            })
            ->when($categoryId, function ($query) use ($categoryId) {
                $query->where('application_category_id', $categoryId);
            })
            ->latest()
            ->paginate($perPage);
    }

    public function getApprovedFormCommittee(int $perPage = 10)
    {
        return Application::with([
            'attributeValues.attribute',
            'applicationRound',
            'user',
            'applicationCategory'
        ])
            ->where('status', ApplicationStatus::APPROVED_BY_COMMITTEE)
            ->where('domain', $this->getDomain())
            ->latest()
            ->paginate($perPage);
    }

    public function getAllRejectedApplications(int $perPage = 10)
    {
        return Application::with([
            'attributeValues.attribute',
            'applicationRound',
            'user',
            'applicationCategory'
        ])
            ->where('status', ApplicationStatus::REJECTED)
            ->where('domain', $this->getDomain())
            ->latest()
            ->paginate($perPage);
    }

    public function getApplicationsByUserId($userId, int $perPage = 10)
    {
        return Application::query()
            ->where('domain', $this->getDomain())
            ->where('user_id', $userId)
            ->with(['attributeValues.attribute',
                'applicationRound',
                'user',
                'applicationCategory'
            ])
            ->latest()
            ->paginate($perPage);
    }
}
