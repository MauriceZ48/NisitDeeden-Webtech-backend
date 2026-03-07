<?php

namespace App\Http\Controllers\API\Auth;

use App\Enums\Department;
use App\Enums\Domain;
use App\Enums\Faculty;
use App\Enums\UserPosition;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Enum;

class AuthenticateController extends Controller
{
    public function login(Request $request) {
        $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::query()->where('email', $request->email)->first();

        if (!$user || ! Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid Credentials'], 401);
        }

        $token = $user->createToken('auth_token', [$user->role])->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => [
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
            ]
        ]);
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|unique:users,email',
            'password'      => 'required|string|confirmed|min:8',
            'university_id' => 'required|string|unique:users,university_id',

            'faculty'       => ['required', new Enum(Faculty::class)],
            'department'    => ['required', new Enum(Department::class)],

            'photo'         => 'nullable|image|max:2048',
            'domain'        => ['required', new Enum(Domain::class)],
        ]);

        $positionEnum = UserPosition::STUDENT;

        $data['position'] = $positionEnum;
        $data['role']     = $positionEnum->getRole();
        $data['password'] = Hash::make($request->password);

        if ($request->hasFile('photo')) {
            $data['profile_path'] = $request->file('photo')->store('profile-photos', 'public');
        }

        $user = User::create($data);

        return response()->json([
            'message' => 'ลงทะเบียนผู้ใช้งานสำเร็จ',
            'user'    => $user
        ], 201);
    }
}
