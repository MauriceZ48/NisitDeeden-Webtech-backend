<?php

namespace App\Http\Controllers;

use App\Models\ApplicationCategory;
use App\Models\CategoryAttribute;
use App\Repositories\ApplicationCategoryRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
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
        $categories = $this->categoryRepo->getAll();
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
        $slug = Str::slug($request->name);
        $request->merge(['slug' => $slug]);

        $request->validate([
            'name' => [
                'required',
                Rule::unique('application_categories')->whereNull('deleted_at')
            ],
            'slug' => [
                'required',
                Rule::unique('application_categories')->whereNull('deleted_at')
            ],
            'icon' => 'required|image|max:2048',
            'attributes.*.label' => 'required',
        ], [
            'name.required' => 'Need category name',
            'name.unique' => 'This category name is already taken (even in trash)',
            'attributes.*.label.required' => 'Every attribute needs a label',
        ]);

         DB::transaction(function () use ($request) {
            $icon_path = $request->file('icon')->store('category_icons', 'public');

            $category = $this->categoryRepo->create([
                'name' => $request->name,
                'slug' => $request->slug,
                'icon' => $icon_path,
                'description' => $request->description,
            ]);

            $attributes = $request->input('attributes', []);

            if (!empty($attributes)) {
                foreach ($attributes as $index => $attr) {

                    $category->attributes()->create([
                        'label' => $attr['label'],
                        'type' => $attr['type'],
                        'is_required' => isset($attr['is_required']),
                    ]);
                }
            } else {
                \Log::error('CHECK: No attributes found in the request.');
            }

        });
        return redirect()->route('categories.index');

    }

    /**
     * Display the specified resource.
     */
    public function show(string $slug)
    {
        $category = $this->categoryRepo->findCategoryBySlug($slug);
        return view('categories.show', [
            'category' => $category,
            'attributes' => $category->attributes
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ApplicationCategory $applicationCategory)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ApplicationCategory $applicationCategory)
    {
        $this->categoryRepo->toggleStatus($applicationCategory);
        return redirect()->route('categories.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ApplicationCategory $applicationCategory)
    {
        if ($applicationCategory->hasApplications()) {
            $this->categoryRepo->delete($applicationCategory->id);
            return back()->with('warning', 'Category soft-deleted (data preserved).');
        }


        if ($applicationCategory->icon) {
            Storage::disk('public')->delete($applicationCategory->icon);
        }

        $applicationCategory->forceDelete();

        return redirect()->route('categories.index')->with('success', 'Category permanently removed.');
    }
}
