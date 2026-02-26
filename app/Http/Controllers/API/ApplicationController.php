<?php

namespace App\Http\Controllers\API;

use App\Enums\ApplicationStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\ApplicationResource;
use App\Models\Application;
use App\Models\ApplicationCategory;
use App\Repositories\ApplicationCategoryRepository;
use App\Repositories\ApplicationRepository;
use App\Repositories\ApplicationRoundRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;

class ApplicationController extends Controller
{
    public function __construct(
        private ApplicationRepository $applicationRepo,
        private ApplicationRoundRepository $RoundRepo,
        private ApplicationCategoryRepository $categoryRepo,
    ){}

    public function index(){
        $applications = $this->applicationRepo->getFullApplicationsPaginated();
        return ApplicationResource::collection($applications);

    }

    public function show(Application $application){
        $application->load('attributeValues.attribute', 'applicationRound', 'attachments', 'user', 'applicationCategory');
        return new ApplicationResource($application);
    }

    public function applicationsForHeadOfDepartment(){
        $applications = $this->applicationRepo->getPendingForHeadOfDepartment();
        return ApplicationResource::collection($applications);
    }

    public function applicationsForAssociateDean()
    {
        $applications = $this->applicationRepo->getPendingForAssociateDean();
        return ApplicationResource::collection($applications);
    }

    public function applicationsForDean()
    {
        $applications = $this->applicationRepo->getPendingForDean();
        return ApplicationResource::collection($applications);
    }

    public function applicationsForCommittee()
    {
        $applications = $this->applicationRepo->getPendingForCommittee();
        return ApplicationResource::collection($applications);
    }

    public function applicationsApprovedByCommittee()
    {
        $applications = $this->applicationRepo->getApprovedFormCommittee();
        return ApplicationResource::collection($applications);
    }

    public function applicationsRejected()
    {
        $applications = $this->applicationRepo->getAllRejectedApplications();
        return ApplicationResource::collection($applications);
    }


    public function store(Request $request)
    {
        //Gate::authorize('create', Application::class);

//         dd(auth()->user()->isAdmin());

        $currentRound = $this->RoundRepo->getActive();
        if (!$currentRound) {
            return response()->json([
                'message' => 'No active application round found.',
                'error_code' => 'NO_ACTIVE_ROUND'
            ], 422);
        }

        // 1. Determine Target User First
        $targetUserId = auth()->user()->isAdmin() ? $request->user_id : auth()->id();

        // 2. Adjust Rules (user_id is only required from the request if Admin is submitting)
        $rules = [
            'user_id' => auth()->user()->isAdmin() ? 'required|exists:users,id' : 'nullable',
            'category_id' => 'required|exists:application_categories,id',
            'values' => 'nullable|array',
            'attachments.*' => 'nullable|file|mimes:pdf,png,jpg,jpeg|max:5120',
        ];

        $category = $this->categoryRepo->getWithAttributes($request->category_id);

        // 3. Dynamically build rules
        foreach ($category->attributes as $attribute) {
            $fieldRules = [];
            $fieldRules[] = $attribute->is_required ? 'required' : 'nullable';

            if ($attribute->type === 'file') {
                array_push($fieldRules, 'file', 'mimes:pdf,png,jpg,jpeg', 'max:5120');
            } else {
                $fieldRules[] = 'string';
            }

            $rules["values.{$attribute->id}"] = $fieldRules;
        }

        $validatedData = $request->validate($rules);

        // 4. Wrap database logic in a Transaction
        return DB::transaction(function () use ($request, $targetUserId, $currentRound) {

            $application = Application::withTrashed()
                ->where('user_id', $targetUserId)
                ->where('application_round_id', $currentRound->id)
                ->first();

            if ($application) {
                if ($application->trashed()) {
                    $application->restore();

                    $this->applicationRepo->clearApplicationData($application);

                    $application->update([
                        'application_category_id' => $request->category_id,
                        'status' => ApplicationStatus::PENDING,
                        'rejection_reason' => null,
                    ]);


                } else {
                    throw ValidationException::withMessages([
                        'error' => 'An active application already exists for this user in this round.'
                    ]);
                }
            } else {
                $application = Application::create([
                    'user_id' => $targetUserId,
                    'application_round_id' => $currentRound->id,
                    'application_category_id' => $request->category_id,
                    'status' => ApplicationStatus::PENDING,
                ]);
            }

            // 5. Save Values
            if ($request->has('values')) {
                foreach ($request->values as $attributeId => $inputValue) {
                    $this->applicationRepo->createAttributeValue($application, $attributeId, $inputValue);
                }
            }

            // 6. Save Attachments
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $this->applicationRepo->createAttachment($application, $file);
                }
            }

            return new ApplicationResource($application);
        });
    }

    public function update(Request $request, Application $application)
    {
        Gate::authorize('update', $application);

        $request->validate([
            'status' => ['nullable', new \Illuminate\Validation\Rules\Enum(ApplicationStatus::class)],
            'rejection_reason' => ['nullable', 'string', 'max:1000'],
            'new_attachments.*' => ['nullable', 'file', 'max:5120'],
            'delete_attachments.*' => ['nullable', 'exists:attachments,id'],
        ]);

        // 1. Handle Deletions of General Attachments
        if ($request->filled('delete_attachments')) {
            $this->applicationRepo->deleteAttachments(
                $request->delete_attachments,
                $application->id
            );
        }

        // 2. Update Static Application Info
        $application->update($request->only(['status', 'rejection_reason']));

        // 3. Update Dynamic Attributes
        if ($request->has('values')) {
            foreach ($request->values as $id => $val) {
                $this->applicationRepo->updateValue($application, $id, $val);
            }
        }

        // 4. Handle New General Attachments
        if ($request->hasFile('new_attachments')) {
            foreach ($request->file('new_attachments') as $file) {
                $this->applicationRepo->createAttachment($application, $file);
            }
        }
        return new ApplicationResource($application);
    }

    public function destroy(Application $application){
        $application->delete();
        return response()->json(null, 204);
    }

}
