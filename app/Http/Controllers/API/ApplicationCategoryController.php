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
    )
    {
    }

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
                'name' => $validated['name'],
                'icon' => $validated['icon'],
                'description' => $validated['description'],
                'domain' => $domain,
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

    public function update(Request $request, $id)
    {
        // 1. ค้นหาข้อมูลตาม ID และ Domain (ความปลอดภัย)
        $applicationCategory = ApplicationCategory::where('id', $id)
            ->where('domain', auth()->user()->domain)
            ->firstOrFail();

        // 2. ตรวจสอบว่ามีการใช้งานไปหรือยัง
        if ($applicationCategory->applications()->count() > 0) {
            return response()->json([
                'message' => 'ไม่สามารถแก้ไขได้ เนื่องจากประเภทรางวัลนี้ถูกนำไปใช้ในใบสมัครแล้ว'
            ], 422);
        }

        // 3. Validation ตามโครงสร้างที่ Frontend ต้องการ
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('application_categories')
                    ->ignore($applicationCategory->id)
                    ->where('domain', auth()->user()->domain)
                    ->whereNull('deleted_at')
            ],
            'icon' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
            'attributes' => 'nullable|array',
            'attributes.*.id' => 'nullable|integer',
            'attributes.*.label' => 'required_with:attributes|string|max:255',
            'attributes.*.type' => 'required_with:attributes|string|in:text,textarea,file',
            'attributes.*.is_required' => 'nullable'
        ], [
            'name.required' => 'กรุณาระบุชื่อประเภทรางวัล',
            'name.unique' => 'ชื่อประเภทรางวัลนี้ถูกใช้งานแล้ว',
            'attributes.*.label.required_with' => 'กรุณาระบุชื่อเรียกคุณลักษณะ (Label)',
        ]);

        return DB::transaction(function () use ($applicationCategory, $validated) {
            // Update ข้อมูลหลัก
            $applicationCategory->update([
                'name' => $validated['name'],
                'icon' => $validated['icon'] ?? null,
                'description' => $validated['description'] ?? null,
            ]);

            // 4. Logic การจัดการ Attributes (Syncing)
            if (array_key_exists('attributes', $validated)) {
                $attributes = $validated['attributes'] ?? [];

                // ตรวจสอบ ID ของ Attribute เดิมที่มีอยู่
                $existingAttributeIds = $applicationCategory->attributes()
                    ->pluck('id')
                    ->map(fn($id) => (int)$id)
                    ->toArray();

                $idsToKeep = collect($attributes)
                    ->pluck('id')
                    ->filter()
                    ->map(fn($id) => (int)$id)
                    ->values()
                    ->toArray();

                // ป้องกันการส่ง ID ของหมวดหมู่อื่นมามั่ว
                foreach ($idsToKeep as $attrId) {
                    if (!in_array($attrId, $existingAttributeIds, true)) {
                        return response()->json([
                            'message' => 'ข้อมูลคุณลักษณะไม่ถูกต้อง',
                            'errors' => ['attributes' => ['คุณลักษณะบางรายการไม่ได้อยู่ในประเภทรางวัลนี้']]
                        ], 422);
                    }
                }

                // ลบตัวที่ไม่อยู่ในรายการส่งมาใหม่ (ลบออกจาก DB)
                $applicationCategory->attributes()
                    ->whereNotIn('id', $idsToKeep)
                    ->delete();

                // สร้างใหม่ หรือ อัปเดตตัวเดิม
                foreach ($attributes as $attrData) {
                    $payload = [
                        'label' => $attrData['label'],
                        'type' => $attrData['type'],
                        'is_required' => filter_var($attrData['is_required'] ?? false, FILTER_VALIDATE_BOOLEAN)
                    ];

                    if (!empty($attrData['id'])) {
                        $applicationCategory->attributes()
                            ->where('id', $attrData['id'])
                            ->update($payload);
                    } else {
                        $applicationCategory->attributes()->create($payload);
                    }
                }
            }

            // 5. เคลียร์ Cache หลังจากแก้ไขสำเร็จ
            if ($applicationCategory->isGlobal()) {
                Cache::forget($this->getGlobalCacheKey());
            } else {
                Cache::forget($this->getDomainCacheKey());
            }

            return response()->json([
                'message' => 'อัปเดตประเภทรางวัลเรียบร้อยแล้ว',
                'category' => $applicationCategory->load('attributes')
            ]);
        });
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

    public function destroy($id)
    {
        // ค้นหาข้อมูลตาม Domain
        $applicationCategory = ApplicationCategory::where('domain', auth()->user()->domain)
            ->findOrFail($id);

        if ($applicationCategory->hasApplications()) {
            // Soft Delete และเคลียร์ Cache
            $this->clearCategoryCache($applicationCategory);
            $applicationCategory->delete();

            return response()->json([
                'message' => 'ย้ายประเภทรางวัลลงถังขยะแล้ว เนื่องจากมีใบสมัครที่เกี่ยวข้องจำนวน ' . $applicationCategory->countApplications() . ' รายการ',
                'type' => 'warning',
                'soft_deleted' => true
            ], 200);
        }

        // Force Delete และเคลียร์ Cache
        $this->clearCategoryCache($applicationCategory);
        $applicationCategory->forceDelete();

        return response()->json(null, 204);
    }

    // Helper Function สำหรับเคลียร์ Cache เพื่อไม่ให้เขียนโค้ดซ้ำ
    private function clearCategoryCache(ApplicationCategory $category)
    {
        if ($category->isGlobal()) {
            Cache::forget($this->getGlobalCacheKey());
        } else {
            Cache::forget($this->getDomainCacheKey());
        }
    }
}
