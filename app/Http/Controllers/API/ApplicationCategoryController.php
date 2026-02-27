<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\ApplicationCategoryResource;
use App\Models\ApplicationCategory;
use App\Repositories\ApplicationCategoryRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ApplicationCategoryController extends Controller
{
    public function __construct(
        private ApplicationCategoryRepository $categoryRepo
){}

    public function index(){
        $categories = $this->categoryRepo->getAllWithAttributes();
        return ApplicationCategoryResource::collection($categories);
    }


    public function show(string $slug)
    {
        $category = $this->categoryRepo->findCategoryBySlug($slug);

        if (!$category) {
            return response()->json([
                'message' => 'Category not found.',
                'error_code' => 'CATEGORY_NOT_FOUND'
            ], 404);
        }

        $category->load('attributes');


        return new ApplicationCategoryResource($category);
    }

    public function store(Request $request)
    {
        // 1. Generate and merge the slug
        $request->merge(['slug' => Str::slug($request->name)]);

        // 2. Strict Validation Firewall
        $request->validate([
            'name' => [
                'required',
                Rule::unique('application_categories')->whereNull('deleted_at')
            ],
            'slug' => [
                'required',
                Rule::unique('application_categories')->whereNull('deleted_at')
            ],
            'icon' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',

            'attributes' => 'nullable|array',
            'attributes.*.label' => 'required_with:attributes|string|max:255',

            'attributes.*.type' => 'required_with:attributes|string|in:text,textarea,file',
        ], [
            'name.required' => 'Need category name',
            'name.unique' => 'This category name is already taken (even in trash)',
            'attributes.*.label.required_with' => 'Every attribute needs a label',
            'attributes.*.type.in' => 'Invalid input type selected.',
        ]);

        // 3. Database Transaction
        return DB::transaction(function () use ($request) {

            $category = $this->categoryRepo->create([
                'name' => $request->name,
                'slug' => $request->slug,
                'icon' => $request->icon,
                'description' => $request->description,
            ]);

            $attributes = $request->input('attributes', []);

            if (!empty($attributes)) {
                foreach ($attributes as $attr) {
                    $category->attributes()->create([
                        'label' => $attr['label'],
                        'type' => $attr['type'],
                        'is_required' => (bool) ($attr['is_required'] ?? false)
                    ]);
                }
            }
            return new ApplicationCategoryResource($category);
        });
    }

    public function update(Request $request, ApplicationCategory $applicationCategory)
    {

//        dd($request);

        if($applicationCategory->applications()->count() > 0){
            return response()->json(['message' => 'Category already in use'], 422);
        }

        $request->merge(['slug' => Str::slug($request->name)]);

        $validated = $request->validate([
            'name' => ['required', Rule::unique('application_categories')->ignore($applicationCategory->id)->whereNull('deleted_at')],
            'slug' => ['required', Rule::unique('application_categories')->ignore($applicationCategory->id)->whereNull('deleted_at')],
            'icon' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
            'attributes' => 'nullable|array',
            'attributes.*.id' => 'nullable|exists:category_attributes,id',
            'attributes.*.label' => 'required_with:attributes|string|max:255',
            'attributes.*.type' => 'required_with:attributes|string|in:text,textarea,file',
        ]);

        $applicationCategory->update($request->only(['name', 'slug', 'icon', 'description']));

        if ($request->has('attributes')) {
            $attributes = $request->attributes; // Standard PHP array

            // 1. Identify which IDs to keep
            $idsToKeep = [];
            foreach ($attributes as $attr) {
                if (!empty($attr['id'])) {
                    $idsToKeep[] = $attr['id'];
                }
            }

            // 2. DELETE: Remove attributes not in the "keep" list
            $applicationCategory->attributes()->whereNotIn('id', $idsToKeep)->delete();

            // 3. UPDATE & CREATE: Loop through all sent data
            foreach ($attributes as $attrData) {
                try {
                    if (!empty($attrData['id'])) {
                        // It has an ID, so UPDATE
                        $applicationCategory->attributes()
                            ->where('id', $attrData['id'])
                            ->update([
                                'label' => $attrData['label'],
                                'type'  => $attrData['type']
                            ]);
                    } else {
                        // No ID, so CREATE
                        $applicationCategory->attributes()->create([
                            'label' => $attrData['label'],
                            'type'  => $attrData['type']
                        ]);
                    }
                } catch (\Exception $e) {
                    // If the database rejects it, you will get a helpful message
                    return response()->json([
                        'error' => 'Database failure: ' . $e->getMessage(),
                        'data'  => $attrData
                    ], 500);
                }
            }
        }

        return response()->json([
            'message' => 'Category updated',
            'category' => $applicationCategory->load('attributes')
        ]);
    }

    public function toggleStatus(ApplicationCategory $applicationCategory)
    {
        $this->categoryRepo->toggleStatus($applicationCategory);
        return new ApplicationCategoryResource($applicationCategory);
    }

    public function destroy(ApplicationCategory $applicationCategory)
    {
        if ($applicationCategory->hasApplications()) {
            $applicationCategory->delete();

            return response()->json([
                'message' => 'Category soft-deleted .' . $applicationCategory->countApplications() . ' applications is affected.',
                'type' => 'warning',
                'soft_deleted' => true
            ], 200);
        }

        $applicationCategory->forceDelete();

        return response()->json(null, 204);
    }

}
