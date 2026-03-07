<?php

namespace App\Http\Controllers;

use App\Enums\Department;
use App\Enums\Faculty;
use App\Enums\UserPosition;
use App\Enums\UserRole;
use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class UserController extends Controller
{
    public function __construct(
        private UserRepository $userRepo
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $q = $request->string('q')->toString();
        $role = $request->string('role')->toString();
        $selectedId = $request->integer('selected');

        // Get current user's domain
        $domain = auth()->user()->domain;

        // 1. Filter main query by domain
        $query = User::query()->where('domain', $domain);

        if ($q !== '') {
            $query->where(function ($qq) use ($q) {
                $qq->where('name', 'like', "%{$q}%")
                    ->orWhere('university_id', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%");
            });
        }

        if ($role !== '') {
            $query->where('role', $role);
        }

        $users = $query->orderBy('name')->paginate(7)->appends($request->query());

        // 2. Filter total count and summary counts by domain
        $count = User::where('domain', $domain)->count();

        $userCount = User::where('domain', $domain)->where('role', UserRole::STUDENT)->count();
        $adminCount = User::where('domain', $domain)->where('role', UserRole::ADMIN)->count();
        $committeeCount = User::where('domain', $domain)->where('role', UserRole::COMMITTEE)->count();

        // 3. Ensure selected user is in the same domain (Security check)
        $selectedUser = $selectedId
            ? User::where('domain', $domain)->find($selectedId)
            : null;

        $roles = UserRole::cases();

        return view('users.index', compact(
            'users', 'count', 'selectedUser', 'q', 'role', 'roles',
            'userCount', 'adminCount', 'committeeCount'
        ));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        Gate::authorize('create', User::class);
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
        Gate::authorize('create', User::class);

        $data = $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|unique:users,email',
            'university_id' => 'required|string|unique:users,university_id',

            'position'      => ['required', new Enum(UserPosition::class)],
            'faculty'       => ['nullable', new Enum(Faculty::class)],
            'department'    => ['nullable', new Enum(Department::class)],

            'photo'         => 'nullable|image|max:2048'
        ]);

        $positionEnum = UserPosition::from($request->position);
        $data['role'] = $positionEnum->getRole();
        $data['domain'] = auth()->user()->domain;

        if ($request->hasFile('photo')) {
            $data['profile_path'] = $request->file('photo')->store('profile-photos', 'public');
        }

        $data['password'] = Hash::make('12345678');

        User::create($data);

        return redirect()->route('users.index')
            ->with('success', 'เพิ่มผู้ใช้งานตำแหน่ง ' . $positionEnum->label() . ' เรียบร้อยแล้ว');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {

        if ($user->domain !== auth()->user()->domain) {
            abort(403, 'You cannot view users from other campuses.');
        }

        return view('users.show', [
            'user' => $user->load('applications')
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        Gate::authorize('update', $user);
        $faculties = Faculty::cases();

        if ($user->domain !== auth()->user()->domain) {
            abort(403, 'You cannot edit users from other campuses.');
        }

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

        if ($user->domain !== auth()->user()->domain) {
            abort(403, 'You cannot update users from other campuses.');
        }

        // 1. Validate including the new 'position' field
        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => [
                'required',
                'email',
                Rule::unique('users')->ignore($user->id)
            ],
            'university_id' => [
                'required',
                Rule::unique('users')->ignore($user->id)
            ],
            'position'      => ['required', new Enum(UserPosition::class)],
            'faculty'       => ['nullable', new Enum(Faculty::class)],
            'department'    => ['nullable', new Enum(Department::class)],
            'photo'         => 'nullable|image|max:2048',
            'delete_photo'  => 'nullable|string'
        ]);

        // 2. Handle Photo Removal
        if ($request->delete_photo === "1") {
            if ($user->profile_path) {
                Storage::disk('public')->delete($user->profile_path);
                $user->profile_path = null;
            }
        }

        // 3. Handle New Photo Upload
        if ($request->hasFile('photo')) {
            if ($user->profile_path) {
                Storage::disk('public')->delete($user->profile_path);
            }
            $user->profile_path = $request->file('photo')->store('profile-photos', 'public');
        }

        $positionEnum = UserPosition::from($request->position);
        $user->role = $positionEnum->getRole();

        if (in_array($positionEnum, [UserPosition::STAFF, UserPosition::COMMITTEE_MEMBER])) {
            $validated['faculty'] = null;
            $validated['department'] = null;
        }
        elseif (in_array($positionEnum, [UserPosition::DEAN, UserPosition::ASSOCIATE_DEAN])) {
            $validated['department'] = null;
        }

        $user->fill($validated);
        $user->save();

        return redirect()->route('users.index')
            ->with('success', "อัปเดตข้อมูลผู้ใช้งานตำแหน่ง " . $positionEnum->label() . " เรียบร้อยแล้ว");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        Gate::authorize('delete', $user);
        if (auth()->id() === $user->id) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        if ($user->domain !== auth()->user()->domain) {
            back()->with('You cannot delete users from other campuses.');
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
            'faculty' => ['required', new Enum(Faculty::class)],
        ]);

        $faculty = Faculty::from($request->faculty);

        $departments = array_values(array_map(
            fn (Department $d) => [
                'value' => $d->value,
                'label' => $d->label(),
            ],
            array_filter(
                Department::cases(),
                fn (Department $d) => $d->faculty() === $faculty
            )
        ));

        return response()->json($departments);
    }
}
