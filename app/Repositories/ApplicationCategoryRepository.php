<?php

namespace App\Repositories;

use App\Enums\Domain;
use App\Http\Controllers\ApplicationCategoryController;
use App\Models\ApplicationCategory;
use App\Repositories\Traits\SimpleCRUD;

class ApplicationCategoryRepository
{

    use SimpleCRUD;

    protected $model;

    private function getDomain()
    {
        return auth()->user()?->domain;
    }

    private function getVisibleDomains(): array
    {
        $userDomain = $this->getDomain();

        return [$userDomain, Domain::ALL];
    }

    public function __construct(ApplicationCategory $model)
    {
        $this->model = $model;
    }


    public function toggleStatus(ApplicationCategory $category)
    {
        // Security check: only toggle if it's the admin's domain
        if ($category->domain === $this->getDomain()) {
            $category->update(['is_active' => !$category->is_active]);
        }
        return $category;
    }

    // For admin in seperate domain
    public function getActiveCategoriesInDomain()
    {
        return ApplicationCategory::with('attributes')
            ->where('domain', $this->getDomain())
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
    }

    public function getAllWithAttributes()
    {
        return ApplicationCategory::with('attributes')
            ->withCount('applications')
            ->where('domain', $this->getDomain())
            ->get();
    }

    public function getGlobalCategoriesWithAttributes()
    {
        return ApplicationCategory::with('attributes')
            ->where('domain', Domain::ALL)
            ->get();
    }

    // For applications to see its domain and global domain
    public function getActiveCategoriesInDomainAndALL()
    {
        return ApplicationCategory::with('attributes')
            ->whereIn('domain', $this->getVisibleDomains())
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
    }

    public function getWithAttributes(int $id): ApplicationCategory
    {
        return ApplicationCategory::with('attributes')
            ->whereIn('domain', $this->getVisibleDomains())
            ->findOrFail($id);
    }
}
