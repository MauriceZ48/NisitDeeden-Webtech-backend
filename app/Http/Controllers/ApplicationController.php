<?php

namespace App\Http\Controllers;

use App\Enums\ApplicationCategory;
use App\Models\Application;
use App\Repositories\ApplicationRepository;
use Illuminate\Http\Request;

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
    public function create()
    {
        return view('applications.form', [
            'application' => new Application(),
            'categories' => ApplicationCategory::cases()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Application $application)
    {
        return view('applications.show', ['application' => $application]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Application $application)
    {
        return view('applications.form',
            ['application' => $application],
            ['categories' => ApplicationCategory::cases()]
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
