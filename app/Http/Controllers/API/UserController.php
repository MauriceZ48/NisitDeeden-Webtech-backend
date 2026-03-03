<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function __construct(
        private UserRepository $userRepo)
    {}

    public function index()
    {
        $users = $this->userRepo->getPaginatedUsersInDomain();
        return UserResource::collection($users);
    }

    public function allUsers()
    {
        $users = $this->userRepo->getAllUsers();
        return UserResource::collection($users);
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
    public function show(User $user)
    {
        if ($user->domain !== auth()->user()->domain) {
            return response()->json([
                'message' => 'Cannot inspect user in other domain.',
            ], 422);
        }
        return new UserResource($user);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        //
    }
}
