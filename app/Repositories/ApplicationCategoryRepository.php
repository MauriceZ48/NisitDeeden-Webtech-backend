<?php

namespace App\Repositories;

use App\Http\Controllers\ApplicationCategoryController;
use App\Models\ApplicationCategory;
use App\Repositories\Traits\SimpleCRUD;

class ApplicationCategoryRepository{

    use SimpleCRUD;

    protected $model;

    public function __construct(ApplicationCategory $model) {
        $this->model = $model;
    }

    public function findCategoryBySlug($slug) {
        return ApplicationCategory::with('attributes')->where('slug', $slug)->first();
    }

    public function toggleStatus(ApplicationCategory $category)
    {
        $category->update([
            'is_active' => !$category->is_active
        ]);

        return $category;
    }

    public function getActiveCategories()
    {
        return ApplicationCategory::where('is_active', true)
            ->orderBy('name')
            ->get();
    }
}
