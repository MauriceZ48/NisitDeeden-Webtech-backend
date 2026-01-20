<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Enum;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Simple search logic: if there is a 'search' query, filter by name or ID
        $query = User::query();

        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                ->orWhere('university_id', 'like', '%' . $request->search . '%');
        }

        // We paginate so the page doesn't get too long with your 50 seeded users
        $users = $query->latest()->paginate(15);

        return view('users.index', [
            'users' => $users
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
}
