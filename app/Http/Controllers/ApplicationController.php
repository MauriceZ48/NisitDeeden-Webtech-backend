<?php

namespace App\Http\Controllers;

use App\Enums\ApplicationCategory;
use App\Enums\ApplicationStatus;
use App\Enums\UserRole;
use App\Models\Application;
use App\Models\Attachment;
use App\Models\User;
use App\Repositories\ApplicationRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class ApplicationController extends Controller
{
    public function __construct(
        private ApplicationRepository $applicationRepository
    ) {}
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        $query = Application::query()
            ->with(['user']); // กัน N+1

        if ($q !== '') {
            $qLower = mb_strtolower($q);

            $query->where(function ($qq) use ($q, $qLower) {

                // ค้นหา ID (ถ้าพิมพ์เป็นตัวเลข)
                if (ctype_digit($q)) {
                    $qq->orWhere('id', (int) $q);
                }

                // ค้นหา category / status (enum string ใน DB)
                $qq->orWhereRaw('LOWER(category) LIKE ?', ["%{$qLower}%"])
                    ->orWhereRaw('LOWER(status) LIKE ?', ["%{$qLower}%"]);

                // ค้นหาข้อมูล user
                $qq->orWhereHas('user', function ($u) use ($qLower) {
                    $u->whereRaw('LOWER(name) LIKE ?', ["%{$qLower}%"])
                        ->orWhereRaw('LOWER(email) LIKE ?', ["%{$qLower}%"]);
                });
            });
        }

        $applications = $query
            ->latest()
            ->paginate(10)
            ->appends($request->query());

        // summary counts (จะนับแบบเดียวกับผลลัพธ์หลัง search ก็ได้)
        $totalCount = $this->applicationRepository->count(); // หรือ count ทั้งหมดจริงก็แยก query อีกชุด
        $pendingCount = Application::where('status', \App\Enums\ApplicationStatus::PENDING)->count();
        $approvedCount = Application::where('status', \App\Enums\ApplicationStatus::APPROVED)->count();

        return view('applications.index', compact(
            'applications',
            'totalCount',
            'pendingCount',
            'approvedCount'
        ));
    }


    /**
     * Show the form for creating a new resource.
     */

    public function create()
    {
        Gate::authorize('create', Application::class);

        $users = User::query()
            ->where('role', UserRole::USER)
            ->select(['id','name','email','university_id','faculty','department','profile_path'])
            ->orderBy('name')
            ->get()
            ->append('profile_url');

        return view('applications.form', [
            'users'      => $users,
            'categories' => ApplicationCategory::cases(),
            'statuses'   => ApplicationStatus::cases(), // ถ้าหน้า create ไม่ให้เลือก status ก็ส่งไว้เฉยๆ ได้
        ]);
    }




    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        Gate::authorize('create', Application::class);
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'category' => 'required',
            'attachments.*' => 'nullable|file|max:5120', //5MB
        ]);
        // Save in Application table
        $application = new Application();
        $application->user_id = $request->user_id;
        $application->category = $request->category;
        $application->status = ApplicationStatus::PENDING; // Set default status
        $application->save();

        // Save in Attachment table
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('applications/attachments', 'public');

                $attachment = new Attachment();
                $attachment->application_id = $application->id;
                $attachment->file_path = $path;
                $attachment->file_name = $file->getClientOriginalName();
                $attachment->mime_type = $file->getMimeType();
                $attachment->file_size = $file->getSize();
                $attachment->save();
            }
        }

        return redirect($request->input('return_url', url()->previous()))
            ->with('success', 'Application and attachments added!');

    }

    /**
     * Display the specified resource.
     */
    public function show(Application $application)
    {
        return view('applications.show', [
            'application' => $application->load('attachments')
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Application $application)
    {
        Gate::authorize('update', $application);
        $application->load(['user']);

        return view('applications.form', [
            'application' => $application,
            'users'       => collect([$application->user])->filter(), // only selected user
            'categories'  => ApplicationCategory::cases(),
            'statuses'    => ApplicationStatus::cases(),
        ]);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Application $application)
    {
        Gate::authorize('update', $application);
        $request->validate([
            'user_id' => ['required','exists:users,id'],
            'category' => ['required', new \Illuminate\Validation\Rules\Enum(ApplicationCategory::class)],
            'status' => ['nullable', new \Illuminate\Validation\Rules\Enum(ApplicationStatus::class)],
            'attachments.*' => ['nullable','file','max:5120'],
            'delete_attachments.*' => ['nullable', 'exists:attachments,id'], // Validate deletion IDs
        ]);

        // 1. Handle Deletions first
        if ($request->has('delete_attachments')) {
            $toDelete = Attachment::whereIn('id', $request->delete_attachments)
                ->where('application_id', $application->id) // Security check
                ->get();

            foreach ($toDelete as $file) {
                Storage::disk('public')->delete($file->file_path); // Remove physical file
                $file->delete(); // Remove DB record
            }
        }

        // 2. Update Application Info
        $application->category = $request->category;
        if ($request->has('status')) {
            $application->status = $request->status;
        }
        $application->save();

        // 3. Handle New Uploads
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('applications/attachments', 'public');

                $application->attachments()->create([
                    'file_path' => $path,
                    'file_name' => $file->getClientOriginalName(),
                    'mime_type' => $file->getMimeType(),
                    'file_size' => $file->getSize(),
                ]);
            }
        }

        return redirect()
            ->route('applications.index')
            ->with('success', 'Application and attachments added!');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Application $application)
    {
        Gate::authorize('delete', $application);
        foreach ($application->attachments as $attachment) {
            Storage::disk('public')->delete($attachment->file_path);
        }

        $application->delete();
        return redirect()->route('applications.index')->with('success', 'Deleted.');
    }


}
