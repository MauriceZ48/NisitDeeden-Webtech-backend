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


    public function show(ApplicationCategory $applicationCategory)
    {
        if (!$applicationCategory) {
            return response()->json([
                'message' => 'Category not found.',
                'error_code' => 'CATEGORY_NOT_FOUND'
            ], 404);
        }

        $applicationCategory->load('attributes');


        return new ApplicationCategoryResource($applicationCategory);
    }

    public function store(Request $request)
    {
        $domain = auth()->user()->domain;

        // 1. Validate only what the user SENDS
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('application_categories')
                    ->where('domain', $domain)
                    ->whereNull('deleted_at')
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
        return DB::transaction(function () use ($validated, $domain, $request) {
            $category = $this->categoryRepo->create([
                'name'        => $validated['name'],
                'icon'        => $validated['icon'],
                'description' => $validated['description'],
                'domain'      => $domain,
            ]);

            $attributes = $request->input('attributes', []);

            if (!empty($attributes)) {
                foreach ($attributes as $attr) {
                    $category->attributes()->create([
                        'label' => $attr['label'],
                        'type' => $attr['type'],
                        'is_required' => filter_var($attr['is_required'] ?? false, FILTER_VALIDATE_BOOLEAN)
                    ]);
                }
            }
            return new ApplicationCategoryResource($category);
        });
    }

    public function update(Request $request, ApplicationCategory $applicationCategory)
    {

        if($applicationCategory->applications()->count() > 0){
            return response()->json(['message' => 'Category already in use'], 422);
        }

        $validated = $request->validate([
            'name' => ['required', Rule::unique('application_categories')->ignore($applicationCategory->id)->whereNull('deleted_at')],
            'icon' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
            'attributes' => 'nullable|array',
            'attributes.*.id' => 'nullable|exists:category_attributes,id',
            'attributes.*.label' => 'required_with:attributes|string|max:255',
            'attributes.*.type' => 'required_with:attributes|string|in:text,textarea,file',
        ]);

        $applicationCategory->update([
            'name' => $request->name,
            'icon' => $request->icon,
            'description' => $request->description,
        ]);

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
        if ($applicationCategory->domain !== auth()->user()->domain) {
            return response()->json(['message' => 'Unauthorized domain access.'], 403);
        }

        $this->categoryRepo->toggleStatus($applicationCategory);
        return new ApplicationCategoryResource($applicationCategory);
    }

    public function destroy(ApplicationCategory $applicationCategory)
    {
        if ($applicationCategory->domain !== auth()->user()->domain) {
            return response()->json(['message' => 'Unauthorized domain access.'], 403);
        }

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
