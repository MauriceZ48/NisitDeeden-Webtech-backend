<?php

namespace App\Http\Controllers;

use App\Models\ApplicationCategory;
use App\Models\CategoryAttribute;
use App\Repositories\ApplicationCategoryRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ApplicationCategoryController extends Controller
{
    public function __construct(
        private ApplicationCategoryRepository $categoryRepo
    ) {}
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = $this->categoryRepo->getAllWithAttributes();
        return view('categories.index', ['categories' => $categories]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('categories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
//        dd($request->all());
        $domain = auth()->user()->domain;
        // 1. Generate and merge the slug
        $request->merge(['slug' => Str::slug($request->name)]);

        // 2. Strict Validation Firewall
        $validated = $request->validate([
            'name' => [
                'required',
                Rule::unique('application_categories')
                    ->where('domain', $domain)
                    ->whereNull('deleted_at')
            ],
            'icon' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000', // Secured the description

            // Ensure attributes is an array if it exists
            'attributes' => 'nullable|array',
            'attributes.*.label' => 'required_with:attributes|string|max:255',

            // Whitelist the exact input types you support in your dynamic form
            'attributes.*.type' => 'required_with:attributes|string|in:text,textarea,file',
        ], [
            'name.required' => 'Need category name',
            'name.unique' => 'This category name is already taken (even in trash)',
            'attributes.*.label.required_with' => 'Every attribute needs a label',
            'attributes.*.type.in' => 'Invalid input type selected.',
        ]);

        // 3. Database Transaction
        DB::transaction(function () use ($request, $validated, $domain) {

            $category = $this->categoryRepo->create([
                'name' => $request->name,
                'icon' => $request->icon,
                'description' => $request->description,
                'domain' => auth()->user()->domain,
            ]);

            $attributes = $request->input('attributes', []);
            foreach ($attributes as $attr) {
                $category->attributes()->create([
                    'label' => $attr['label'],
                    'type' => $attr['type'],
                    'is_required' => isset($attr['is_required']),
                ]);
            }

        });

        return redirect()->route('categories.index')
            ->with('success', 'สร้างประเภทรางวัลสำเร็จแล้ว!');
    }

    /**
     * Display the specified resource.
     */
    public function show(ApplicationCategory $applicationCategory)
    {

        return view('categories.show', [
            'category' => $applicationCategory,
            'attributes' => $applicationCategory->attributes
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ApplicationCategory $applicationCategory)
    {

        return view('categories.edit', [
            'category' => $applicationCategory,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ApplicationCategory $applicationCategory)
    {
        $validated = $request->validate([
            'name' => ['required', Rule::unique('application_categories')->ignore($applicationCategory->id)->whereNull('deleted_at')],
            'icon' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);
        $applicationCategory->update([
            'name' => $request->name,
            'icon' => $request->icon,
            'description' => $request->description,
        ]);
        return redirect()->route('categories.show', $applicationCategory)
            ->with('success', 'อัปเดตข้อมูลประเภทรางวัลเรียบร้อยแล้ว');
    }

    public function toggleStatus(ApplicationCategory $applicationCategory){
        $this->categoryRepo->toggleStatus($applicationCategory);
        return redirect()->route('categories.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ApplicationCategory $applicationCategory)
    {
        if ($applicationCategory->hasApplications()) {
            $applicationCategory->delete(); // Soft Delete
            return back()->with('warning', 'ลบประเภทรางวัลแบบชั่วคราว มีผู้สมัครได้รับผลกระทบ ' . $applicationCategory->countApplications() . ' รายการ');
        }

        $applicationCategory->forceDelete(); // Hard Delete

        return redirect()->route('categories.index')->with('success', 'ลบประเภทรางวัลออกจากระบบอย่างถาวรแล้ว');
    }
}
