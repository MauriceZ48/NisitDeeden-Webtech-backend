<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\ApplicationRoundResource;
use App\Models\ApplicationRound;
use App\Repositories\ApplicationRoundRepository;
use Illuminate\Http\Request;

class ApplicationRoundController extends Controller
{

    public function __construct(
        private ApplicationRoundRepository $roundRepo
    ){}
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $rounds = $this->roundRepo->getAllOrdered();
        return ApplicationRoundResource::collection($rounds);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
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
    public function show(ApplicationRound $round)
    {
        return new ApplicationRoundResource($round);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ApplicationRound $round)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ApplicationRound $round)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ApplicationRound $round)
    {
        //
    }
}
