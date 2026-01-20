<?php

namespace App\Http\Controllers;

use App\Enums\ApplicationCategory;
use App\Enums\ApplicationStatus;
use App\Models\Application;
use App\Models\Attachment;
use App\Models\User;
use App\Repositories\ApplicationRepository;
use Illuminate\Http\Request;
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

        return view('applications.index', [
            'applications' => $applications
        ]);
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $userId = $request->query('user_id');

        $user = User::findOrFail($userId);

        return view('applications.form', [
            'application' => new Application(),
            'categories' => ApplicationCategory::cases(),
            'statuses' => ApplicationStatus::cases(),
            'user' => $user
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
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

        return redirect()->route('users.show', $request->user_id)
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
        return view('applications.form', [
            'application' => $application,
            'categories'  => ApplicationCategory::cases(),
            'statuses'    => ApplicationStatus::cases(),
            'user'         => $application->user
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Application $application)
    {
        $request->validate([
            'category' => ['required', new \Illuminate\Validation\Rules\Enum(ApplicationCategory::class)],
            'status'   => ['required', new \Illuminate\Validation\Rules\Enum(ApplicationStatus::class)],
        ]);

        $application->category = $request->category;
        $application->status = $request->status;

        $application->save();

        return redirect()->route('applications.show', ['application' => $application])
            ->with('success', 'Application updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Application $application)
    {
        foreach ($application->attachments as $attachment) {
            Storage::disk('public')->delete($attachment->file_path);
        }

        $application->delete();
        return redirect()->route('applications.index')->with('success', 'Deleted.');
    }
}
