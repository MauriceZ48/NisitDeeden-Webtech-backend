<?php

namespace App\Http\Controllers;

use App\Enums\ApplicationCategory;
use App\Enums\ApplicationStatus;
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
    public function index()
    {
        $applications = $this->applicationRepository->get();
        $totalCount = $this->applicationRepository->count();
        $approvedCount = $this->applicationRepository->countByStatus(ApplicationStatus::APPROVED);
        $pendingCount = $this->applicationRepository->countByStatus(ApplicationStatus::PENDING);

        return view('applications.index', [
            'totalCount' => $totalCount,
            'applications' => $applications,
            'approvedCount' => $approvedCount,
            'pendingCount' => $pendingCount,
        ]);
    }


    /**
     * Show the form for creating a new resource.
     */

    public function create()
    {
        Gate::authorize('create', Application::class);

        $users = User::query()
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
        ]);


        $application->category = $request->category;

        if ($request->has('status')) {
            $application->status = $request->status;
        }

        $application->save();

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

        return redirect()->route('applications.show', ['application' => $application])
            ->with('success', 'Application updated successfully!');
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
