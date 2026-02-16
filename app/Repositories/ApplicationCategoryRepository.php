<?php

namespace App\Repositories;

use App\Models\ApplicationCategory;
use App\Repositories\Traits\SimpleCRUD;

class ApplicationCategoryRepository{

    use SimpleCRUD;

    protected $model;

    public function __construct(ApplicationCategory $model) {
        $this->model = $model;
    }

    public function getActiveCategories()
    {
        return ApplicationCategory::where('is_active', true)->get();
    }
}
