<?php

namespace App\Repositories;

use App\Models\Application;
use App\Repositories\Traits\SimpleCRUD;
use Illuminate\Database\Eloquent\Collection;

class ApplicationRepository
{
    use SimpleCRUD;

    private string $model = Application::class;
}
