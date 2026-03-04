<?php

namespace App\Repositories;

use App\Http\Controllers\ApplicationCategoryController;
use App\Models\ApplicationCategory;
use App\Repositories\Traits\SimpleCRUD;

class ApplicationCategoryRepository{

    use SimpleCRUD;

    protected $model;

    private function getDomain()
    {
        return auth()->user()?->domain;
    }

    public function __construct(ApplicationCategory $model) {
        $this->model = $model;
    }

    public function findCategoryBySlug($slug) {
        return ApplicationCategory::with('attributes')
            ->where('slug', $slug)
            ->where('domain', $this->getDomain())
            ->first();
    }

    public function toggleStatus(ApplicationCategory $category)
    {
        // Security check: only toggle if it's the admin's domain
        if ($category->domain === $this->getDomain()) {
            $category->update(['is_active' => !$category->is_active]);
        }
        return $category;
    }

    public function getActiveCategoriesInDomain()
    {
        return ApplicationCategory::with('attributes')
            ->where('domain', $this->getDomain())
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
    }

    public function getWithAttributes(int $id): ApplicationCategory
    {
        return ApplicationCategory::with('attributes')
            ->where('domain', $this->getDomain())
            ->findOrFail($id);
    }

    public function getAllWithAttributes(){
        return ApplicationCategory::with('attributes')
            ->where('domain', $this->getDomain())
            ->get();
    }
}
