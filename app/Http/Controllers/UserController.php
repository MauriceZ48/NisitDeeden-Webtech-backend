<?php

namespace App\Http\Controllers;

use App\Enums\Department;
use App\Enums\Faculty;
use App\Enums\UserRole;
use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
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
        $faculty    = $request->string('faculty')->toString();
        $department = $request->string('department')->toString();
        $selectedId = $request->integer('selected');

        $query = User::query();

        if ($q !== '') {
            $query->where(function ($qq) use ($q) {
                $qq->where('name', 'like', "%{$q}%")
                    ->orWhere('university_id', 'like', "%{$q}%"); // or student_staff_id if that's your column
            });
        }

        if ($faculty !== '') {
            $query->where('faculty', $faculty);
        }

        if ($department !== '') {
            $query->where('department', $department);
        }

        $users = $query->orderBy('name')->paginate(10)->appends($request->query());
        $count = $this->userRepository->count();

        $selectedUser = $selectedId ? User::find($selectedId) : null;

        $faculties = User::query()->whereNotNull('faculty')->distinct()->orderBy('faculty')->pluck('faculty');
        $departments = User::query()->whereNotNull('department')->distinct()->orderBy('department')->pluck('department');

        return view('users.index', [
            'users'        => $users,
            'count'        => $count,
            'selectedUser' => $selectedUser,
            'q'            => $q,
            'faculty'      => $faculty,
            'department'   => $department,
            'faculties'    => $faculties,
            'departments'  => $departments,
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

        // handle upload
        if ($request->hasFile('profile_picture')) {
            $data['profile_picture_path'] = $request->file('profile_picture')
                ->store('profile_pictures', 'public');
        }
        $data['password'] = Hash::make('12345678');

        $user = User::create($data);

        return redirect()->route('users.index')->with('success', 'User created.');
    }

    private function validated(Request $request, ?int $userId = null): array
    {
        return $request->validate([
            'name' => ['required','string','max:255'],
            'email' => [
                'required','email','max:255',
                Rule::unique('users', 'email')->ignore($userId),
            ],
            'student_staff_id' => ['nullable','string','max:50'],
//            'role' => ['required', Rule::in(['student','staff','admin'])],
            'faculty' => ['nullable','string','max:255'],
            'department' => ['nullable','string','max:255'],

            'profile_picture' => ['nullable','image','max:2048'], // 2MB
            'remove_profile_picture' => ['nullable','boolean'],
        ]);
    }


    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        // We load the 'applications' relationship you just added to the User model
        return view('users.show', [
            'user' => $user->load('applications')
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        return view('users.edit', [
            'user' => $user,
            'roles' => UserRole::cases() // Pass Enum cases to the view for a dropdown
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'role' => ['required', new Enum(UserRole::class)],
            'faculty' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
            'university_id' => 'required|string|unique:users,university_id,' . $user->id,
        ]);

        $user->update($validated);

        return redirect()->route('users.show', $user)
            ->with('success', 'User updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        // Basic security: Don't let an admin delete themselves!
        if (auth()->id() === $user->id) {
            return back()->with('error', 'You cannot delete your own account.');
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
