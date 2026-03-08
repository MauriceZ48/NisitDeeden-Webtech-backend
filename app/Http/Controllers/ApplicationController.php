<?php

namespace App\Http\Controllers;

use App\Models\ApplicationCategory;
use App\Enums\ApplicationStatus;
use App\Enums\UserRole;
use App\Models\Application;
use App\Models\ApplicationRound;
use App\Models\Attachment;
use App\Models\User;
use App\Repositories\ApplicationCategoryRepository;
use App\Repositories\ApplicationRepository;
use App\Repositories\ApplicationRoundRepository;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Validation\ValidationException;

class ApplicationController extends Controller
{

    public function __construct(
        private ApplicationRepository         $applicationRepo,
        private UserRepository                $userRepo,
        private ApplicationCategoryRepository $categoryRepo,
        private ApplicationRoundRepository    $roundRepo,
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $status = $request->query('status'); // 👈 รับค่า Filter Status
        $domain = auth()->user()->domain;

        // 1. สร้าง Query Builder
        $query = Application::with(['user', 'applicationRound', 'applicationCategory'])
            ->where('domain', $domain);

        // 2. กรองข้อมูลตามคำค้นหา (Search)
        if ($q !== '') {
            $query->where(function ($qq) use ($q) {
                $qq->where('id', 'like', "%{$q}%")
                    ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$q}%")
                        ->orWhere('email', 'like', "%{$q}%")) // 👈 เพิ่มการค้นหาด้วย Email ตรงนี้
                    ->orWhereHas('applicationCategory', fn($c) => $c->where('name', 'like', "%{$q}%"));
            });
        }

        // 3. กรองข้อมูลตามสถานะ (Status Filter)
        if ($status) {
            $query->where('status', $status);
        }

        // 4. ดึงข้อมูลแบบแบ่งหน้า เรียงตามวันที่อัปเดตล่าสุด
        $applications = $query->latest('updated_at')->paginate(7);

        // 5. คำนวณสรุปผล (Summary)
        $totalCount = Application::where('domain', $domain)->count();
        $pendingCount = $this->applicationRepo->countByStatus(\App\Enums\ApplicationStatus::PENDING);
        $approvedCount = Application::where('domain', $domain)
            ->where('status', '!=', \App\Enums\ApplicationStatus::PENDING)
            ->where('status', '!=', \App\Enums\ApplicationStatus::REJECTED)
            ->count();
        $rejectedCount = $this->applicationRepo->countByStatus(\App\Enums\ApplicationStatus::REJECTED);

        // ถ้าเปิดหน้าเว็บปกติ (Refresh) ให้โหลดหน้าเต็ม
        return view('applications.index', compact('applications', 'totalCount', 'pendingCount', 'approvedCount', 'rejectedCount'));
    }


    /**
     * Show the form for creating a new resource.
     */

    public function create()
    {
        Gate::authorize('create', Application::class);

        $users = $this->userRepo->getStudentsForSelection();

        $categories = $this->categoryRepo->getActiveCategoriesInDomain();

        $formattedUsers = $users->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'university_id' => $user->university_id,
                'profile_url' => $user->profile_url,
                'faculty' => $user->faculty ? $user->faculty->label() : '-',
                'department' => $user->department ? $user->department->label() : '-',
            ];
        });

        return view('applications.create', [
            'users' => $formattedUsers,
            'categories' => $categories,
        ]);
    }

    public function showForm(Request $request, ApplicationCategory $applicationCategory)
    {

        $student_id = $request->query('student_id');
        $student = $this->userRepo->getById($student_id);

        $applicationCategory->load('attributes');

        return view('applications.form', [
            'student' => $student,
            'category' => $applicationCategory
        ]);

    }



    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        Gate::authorize('create', Application::class);

        $currentRound = $this->roundRepo->getActive();
        if (!$currentRound) {
            return back()->withErrors(['error' => 'There is no active application round at this time.']);
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

            return redirect()->route('applications.index')->with('success', 'Application submitted successfully!');
        });
    }

    /**
     * Display the specified resource.
     */
    public function show(Application $application)
    {
        return view('applications.show', [
            'application' => $application->load('attachments', 'applicationCategory')
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Application $application)
    {
        Gate::authorize('update', $application);

        $application->load([
            'user',
            'applicationCategory',
            'applicationRound',
            'attributeValues.attribute',
            'attachments'
        ]);

        return view('applications.edit', ['application' => $application]);
    }



    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Application $application)
    {
//        dd($request->all());
        Gate::authorize('update', $application);

        $request->validate([
            'status' => ['nullable', new Enum(ApplicationStatus::class)],
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
                    $this->applicationRepo->updateValueForBackend($application, $id, $val);
            }
        }

        // 4. Handle New General Attachments
        if ($request->hasFile('new_attachments')) {
            foreach ($request->file('new_attachments') as $file) {
                $this->applicationRepo->createAttachment($application, $file);
            }
        }

        $application->touch();

        return redirect()->route('applications.show', ['application' => $application])
            ->with('success', 'บันทึกการแก้ไขเรียบร้อยแล้ว!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Application $application)
    {
        Gate::authorize('delete', $application);

        $application->delete();

        return redirect()->route('applications.index')->with('success', 'Deleted.');
    }


}
