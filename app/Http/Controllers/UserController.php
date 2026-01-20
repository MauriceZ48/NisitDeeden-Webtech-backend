<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
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
    public function create()
    {
        return view('users.form', [
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
            'photo' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('photo')) {
            // Delete old photo if it exists
            if ($user->profile_path) {
                Storage::disk('public')->delete($user->profile_path);
            }

            // Store new photo
            $path = $request->file('photo')->store('profile-photos', 'public');
            $user->profile_path = $path;
        }

        $user->fill($validated);

        $user->save();

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
}
