<?php

namespace App\Http\Controllers;

use App\Enums\Department;
use App\Enums\Faculty;
use App\Enums\UserRole;
use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class UserController extends Controller
{
    public function __construct(
        private UserRepository $userRepository
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $q          = $request->string('q')->toString();
        $role       = $request->string('role')->toString();
        $selectedId = $request->integer('selected');

        $query = User::query();

        if ($q !== '') {
            $query->where(function ($qq) use ($q) {
                $qq->where('name', 'like', "%{$q}%")
                    ->orWhere('university_id', 'like', "%{$q}%");
            });
        }

        if ($role !== '') {
            $query->where('role', $role); // <-- change column name if yours is different
        }

        $users = $query->orderBy('name')->paginate(10)->appends($request->query());
        $count = $this->userRepository->count();

        $selectedUser = $selectedId ? User::find($selectedId) : null;

        $userCount  = $this->userRepository->countByRole('USER');
        $adminCount = $this->userRepository->countByRole('ADMIN');

        $roles = User::query()
            ->whereNotNull('role')
            ->distinct()
            ->orderBy('role')
            ->pluck('role');

        return view('users.index', [
            'users'        => $users,
            'count'        => $count,
            'selectedUser' => $selectedUser,
            'q'            => $q,
            'role'         => $role,
            'roles'        => $roles,
            'userCount'    => $userCount,
            'adminCount'   => $adminCount,
        ]);
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $faculties = Faculty::cases();
        $departments = [];

        if ($request->filled('faculty')) {
            $faculty = Faculty::from($request->faculty);

            $departments = array_filter(
                Department::cases(),
                fn (Department $d) => $d->faculty() === $faculty
            );
        }

        return view('users.form', [
            'user' => new User(),
            'faculties' => $faculties,
            'departments' => $departments,
            'selectedFaculty' => $request->faculty,
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);

        // Logic for profile_path during creation
        if ($request->hasFile('photo')) {
            $data['profile_path'] = $request->file('photo')->store('profile-photos', 'public');
        }

        $data['password'] = Hash::make('12345678');

        User::create($data);

        return redirect()->route('users.index')->with('success', 'User created.');
    }

    /**
     * Centralized validation logic
     */
    private function validated(Request $request, ?int $userId = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required', 'email', 'max:255',
                Rule::unique('users', 'email')->ignore($userId),
            ],
            'university_id' => [
                'required', 'string', 'max:50',
                Rule::unique('users', 'university_id')->ignore($userId),
            ],
            'role' => ['required', new Enum(UserRole::class)],
            'faculty' => ['nullable', new Enum(Faculty::class)],
            'department' => ['nullable', new Enum(Department::class)],
            'photo' => ['nullable', 'image', 'max:2048'], // Added photo validation
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        return view('users.show', [
            'user' => $user->load('applications')
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $faculties = Faculty::cases();

        return view('users.form', [
            'user' => $user,
            'faculties' => $faculties,
            'departments' => [], // JS will load based on faculty
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        // Use the centralized validated helper
        $validated = $this->validated($request, $user->id);

        // Handle the Remove Button
        if ($request->delete_photo == "1") {
            if ($user->profile_path) {
                Storage::disk('public')->delete($user->profile_path);
                $user->profile_path = null;
            }
        }

        if ($request->hasFile('photo')) {
            // Delete old photo if it exists
            if ($user->profile_path) {
                Storage::disk('public')->delete($user->profile_path);
            }

            // Store new photo under standardized name profile_path
            $user->profile_path = $request->file('photo')->store('profile-photos', 'public');
        }

        $user->fill($validated);
        $user->save();

        return redirect()->route('users.index')
            ->with('success', 'User updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        if (auth()->id() === $user->id) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        // Clean up the storage when user is deleted
        if ($user->profile_path) {
            Storage::disk('public')->delete($user->profile_path);
        }

        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'User deleted.');
    }

    public function departmentsByFaculty(Request $request)
    {
        $request->validate([
            'faculty' => ['required', new \Illuminate\Validation\Rules\Enum(Faculty::class)],
        ]);

        $faculty = Faculty::from($request->faculty);

        $departments = array_values(array_map(
            fn (Department $d) => [
                'value' => $d->value,
                'label' => $d->value,
            ],
            array_filter(
                Department::cases(),
                fn (Department $d) => $d->faculty() === $faculty
            )
        ));

        return response()->json($departments);
    }
}
