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

    /**
     * Helper to get the current domain context
     */
    private function getDomain()
    {
        return auth()->user()?->domain;
    }

    public function countByRole(string $role): int
    {
        return $this->model::query()
            ->where('domain', $this->getDomain())
            ->where('role', $role)
            ->count();
    }

    public function getStudentsForSelection()
    {
        return $this->model::where('role', UserRole::STUDENT)
            ->where('domain', $this->getDomain())
            ->select(['id','name','email','university_id','faculty','department','profile_path', 'domain'])
            ->orderBy('name')
            ->get()
            ->append('profile_url');
    }

    public function getPaginatedUsersInDomain(int $perPage = 10)
    {
        return $this->model::query()
            ->where('domain', $this->getDomain())
            ->paginate($perPage);
    }

    public function getAllUsers(int $perPage = 10)
    {
        return $this->model::query()->paginate($perPage);
    }

}
