<?php

namespace App\Repositories;

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
}
