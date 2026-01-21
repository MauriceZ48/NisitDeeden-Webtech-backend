<?php

namespace App\Repositories;

use App\Enums\ApplicationStatus;
use App\Models\Application;
use App\Repositories\Traits\SimpleCRUD;
use Illuminate\Database\Eloquent\Collection;

class ApplicationRepository
{
    use SimpleCRUD;

    private string $model = Application::class;

    public function countByStatus(ApplicationStatus $status): int
    {
        return $this->model::where('status', $status->value)->count();
    }


}
