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

    public function getPaginatedUsersInDomain(
        ?string $q = null,
        ?string $role = null,
        ?string $faculty = null,
        ?string $department = null,
        ?string $position = null,
        int $perPage = 7
    ) {
        $authUser = auth()->user();
        $domain = $authUser->domain;

        $query = User::query()
            ->where('domain', $domain);

        if (!empty($q)) {
            $query->where(function ($subQuery) use ($q) {
                $subQuery->where('name', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%")
                    ->orWhere('university_id', 'like', "%{$q}%");
            });
        }

        if (!empty($role)) {
            $query->where('role', $role);
        }

        if (!empty($position)) {
            $query->where('position', $position);
        }

        if (!empty($faculty)) {
            $query->where('faculty', $faculty);
        }

        if (!empty($department)) {
            $query->where('department', $department);
        }

        return $query
            ->latest()
            ->paginate($perPage)
            ->withQueryString();
    }

    public function getAllUsers(int $perPage = 10)
    {
        return $this->model::query()->paginate($perPage);
    }

    public function getUserById(int $id)
    {
        return $this->model::query()->findOrFail($id);
    }

    public function createUser(array $data): User
    {
        return $this->model::query()->create($data);
    }

}
