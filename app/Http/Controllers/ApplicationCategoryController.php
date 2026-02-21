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
        DB::transaction(function () use ($request) {

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
                        // isset() is perfect here for handling HTML checkbox behavior
                        'is_required' => isset($attr['is_required']),
                    ]);
                }
            } else {
                // Changed to info() - an empty attribute list isn't necessarily an "error"
                Log::info("No dynamic attributes provided for category: {$category->name}");
            }
        });

        return redirect()->route('categories.index')
            ->with('success', 'Category created successfully!');
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


        $applicationCategory->forceDelete();

        return redirect()->route('categories.index')->with('success', 'Category permanently removed.');
    }
}
