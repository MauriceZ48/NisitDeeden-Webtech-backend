<?php

namespace App\Repositories;

use App\Enums\UserRole;
use App\Models\User;
use App\Repositories\Traits\SimpleCRUD;
use Illuminate\Database\Eloquent\Collection;

class UserRepository
{
    use SimpleCRUD;

    private string $model = User::class;

    public function countByRole(string $role): int
    {
        return $this->model::query()
            ->where('role', $role)
            ->count();
    }

    public function getStudentsForSelection()
    {
        return $this->model::where('role', UserRole::USER)
            ->select(['id','name','email','university_id','faculty','department','profile_path'])
            ->orderBy('name')
            ->get()
            ->append('profile_url');
    }

    public function getUserById(int $id): User
    {
        return $this->model::query()->findOrFail($id);
    }
}
