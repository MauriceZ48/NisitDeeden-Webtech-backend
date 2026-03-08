<?php

namespace App\Http\Controllers\API;

use App\Enums\Department;
use App\Enums\UserPosition;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function __construct(
        private UserRepository $userRepo
    ) {}

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
        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|max:255|unique:users,email',
            'university_id' => 'nullable|string|max:50|unique:users,university_id',
            'department'    => ['required', new Enum(Department::class)],
            'position'      => ['required', new Enum(UserPosition::class)],
            'photo'         => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $department = Department::from($validated['department']);
        $position = UserPosition::from($validated['position']);

        $data = [
            'name'          => $validated['name'],
            'email'         => $validated['email'],
            'password'      => Hash::make('password'),
            'university_id' => $validated['university_id'] ?? null,
            'department'    => $department,
            'faculty'       => $department->faculty(),
            'position'      => $position,
            'role'          => $position->getRole(),
            'domain'        => auth()->user()->domain,
        ];

        if ($request->hasFile('photo')) {
            $data['profile_path'] = $request->file('photo')->store('profile-photos', 'public');
        }

        $user = $this->userRepo->createUser($data);

        return (new UserResource($user))->response()->setStatusCode(201);
    }

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
        if ($user->domain !== auth()->user()->domain) {
            return response()->json([
                'message' => 'Invalid authorization: Domain mismatch.',
            ], 403);
        }

        if ($user->id !== auth()->id()) {
            return response()->json([
                'message' => 'Cannot update other users.',
            ], 403);
        }

        $request->validate([
            'photo'         => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'delete_photo'  => 'nullable'
        ]);

        //Handle Photo Removal
        if ($request->boolean('delete_photo')) {
            if ($user->profile_path) {
                Storage::disk('public')->delete($user->profile_path);
                $user->profile_path = null;
            }
        }

        // Handle New Photo Upload
        if ($request->hasFile('photo')) {
            if ($user->profile_path) {
                Storage::disk('public')->delete($user->profile_path);
            }
            $user->profile_path = $request->file('photo')->store('profile-photos', 'public');
        }

        $user->save();

        return new UserResource($user);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        //
    }
}
