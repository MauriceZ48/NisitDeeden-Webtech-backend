<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Models\ApplicationCategory;
use App\Repositories\ApplicationCategoryRepository;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function __construct(
        private ApplicationCategoryRepository $categoryRepo
){}

    public function index(){
        $categories = $this->categoryRepo->getActiveCategories();
        return CategoryResource::collection($categories);
    }


    public function show(ApplicationCategory $category)
    {
        $category->load('attributes');
        return new CategoryResource($category);
    }

}
