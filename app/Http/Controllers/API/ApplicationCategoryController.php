<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\ApplicationCategoryResource;
use App\Models\ApplicationCategory;
use App\Repositories\ApplicationCategoryRepository;
use Illuminate\Http\Request;

class ApplicationCategoryController extends Controller
{
    public function __construct(
        private ApplicationCategoryRepository $categoryRepo
){}

    public function index(){
        $categories = $this->categoryRepo->getActiveCategories();
        return ApplicationCategoryResource::collection($categories);
    }


    public function show(ApplicationCategory $category)
    {
        $category->load('attributes');
        return new ApplicationCategoryResource($category);
    }

}
