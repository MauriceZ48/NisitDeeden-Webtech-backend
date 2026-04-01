<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\ApplicationCategoryResource;
use App\Models\ApplicationCategory;
use App\Repositories\ApplicationCategoryRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ApplicationCategoryController extends Controller
{
    public function __construct(
        private ApplicationCategoryRepository $categoryRepo
    ) {}

    private function getDomainCacheKey(): string
    {
        $domain = auth()->user()->domain?->value ?? 'default';
        return "categories.domain.{$domain}";
    }

    private function getGlobalCacheKey(): string
    {
        return "categories.global";
    }

    public function index()
    {
        $categories = Cache::remember($this->getDomainCacheKey(), 60 * 60 * 24, function () {
            return $this->categoryRepo->getAllWithAttributes();
        });
        return ApplicationCategoryResource::collection($categories);
    }

    public function indexForApplication()
    {
        $globalCategories = Cache::remember($this->getGlobalCacheKey(), 60 * 60 * 24, function () {
            return $this->categoryRepo->getGlobalCategoriesWithAttributes();
        });

        $domainCategories = Cache::remember($this->getDomainCacheKey(), 60 * 60 * 24, function () {
            return $this->categoryRepo->getAllWithAttributes();
        });

        $mergedCategories = $globalCategories->merge($domainCategories);
        $activeCategories = $mergedCategories->where('is_active', true);

        return ApplicationCategoryResource::collection($activeCategories);
    }

    public function show(ApplicationCategory $applicationCategory)
    {
        if (!$applicationCategory) {
            return response()->json([
                'message' => 'ไม่พบข้อมูลประเภทรางวัล',
                'error_code' => 'CATEGORY_NOT_FOUND'
            ], 404);
        }

        $applicationCategory->load('attributes');

        return new ApplicationCategoryResource($applicationCategory);
    }

    public function store(Request $request)
    {
        $domain = auth()->user()->domain;

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
            'name.required' => 'กรุณาระบุชื่อประเภทรางวัล',
            'name.unique' => 'ชื่อประเภทรางวัลนี้ถูกใช้งานแล้ว (รวมถึงในถังขยะ)',
            'attributes.*.label.required_with' => 'คุณลักษณะ (Attribute) ทุกรายการจำเป็นต้องมีชื่อเรียก (Label)',
            'attributes.*.type.in' => 'ประเภทข้อมูลที่เลือกไม่ถูกต้อง',
        ]);

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

            if ($category->isGlobal()) {
                Cache::forget($this->getGlobalCacheKey());
            } else {
                Cache::forget($this->getDomainCacheKey());
            }

            return new ApplicationCategoryResource($category);
        });
    }

    public function update(Request $request, ApplicationCategory $applicationCategory)
    {
        if ($applicationCategory->applications()->count() > 0) {
            return response()->json(['message' => 'ไม่สามารถแก้ไขได้ เนื่องจากประเภทรางวัลนี้ถูกนำไปใช้ในใบสมัครแล้ว'], 422);
        }

        $validated = $request->validate([
            'name' => ['required', Rule::unique('application_categories')->ignore($applicationCategory->id)->whereNull('deleted_at')],
            'icon' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
        ], [
            'name.required' => 'กรุณาระบุชื่อประเภทรางวัล',
            'name.unique' => 'ชื่อประเภทรางวัลนี้ถูกใช้งานแล้ว',
        ]);

        $applicationCategory->update([
            'name' => $validated['name'],
            'icon' => $validated['icon'],
            'description' => $validated['description'],
        ]);

        if ($applicationCategory->isGlobal()) {
            Cache::forget($this->getGlobalCacheKey());
        } else {
            Cache::forget($this->getDomainCacheKey());
        }

        return response()->json([
            'message' => 'อัปเดตข้อมูลประเภทรางวัลเรียบร้อยแล้ว',
            'category' => $applicationCategory->load('attributes')
        ]);
    }

    public function toggleStatus(ApplicationCategory $applicationCategory)
    {
        if ($applicationCategory->domain !== auth()->user()->domain) {
            return response()->json(['message' => 'คุณไม่มีสิทธิ์เข้าถึงข้อมูลของวิทยาเขตอื่น'], 403);
        }

        $this->categoryRepo->toggleStatus($applicationCategory);

        if ($applicationCategory->isGlobal()) {
            Cache::forget($this->getGlobalCacheKey());
        } else {
            Cache::forget($this->getDomainCacheKey());
        }

        return new ApplicationCategoryResource($applicationCategory);
    }

    public function destroy(ApplicationCategory $applicationCategory)
    {
        if ($applicationCategory->domain !== auth()->user()->domain) {
            return response()->json(['message' => 'คุณไม่มีสิทธิ์เข้าถึงข้อมูลของวิทยาเขตอื่น'], 403);
        }

        if ($applicationCategory->hasApplications()) {
            if ($applicationCategory->isGlobal()) {
                Cache::forget($this->getGlobalCacheKey());
            } else {
                Cache::forget($this->getDomainCacheKey());
            }

            $applicationCategory->delete();

            return response()->json([
                'message' => 'ย้ายประเภทรางวัลลงถังขยะแล้ว จากมีใบสมัครที่เกี่ยวข้องจำนวน ' . $applicationCategory->countApplications() . ' รายการ',
                'type' => 'warning',
                'soft_deleted' => true
            ], 200);
        }

        $applicationCategory->forceDelete();

        if ($applicationCategory->isGlobal()) {
            Cache::forget($this->getGlobalCacheKey());
        } else {
            Cache::forget($this->getDomainCacheKey());
        }

        return response()->json(null, 204);
    }
}
