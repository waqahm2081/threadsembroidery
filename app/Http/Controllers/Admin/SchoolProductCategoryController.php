<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\SchoolProductCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SchoolProductCategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = SchoolProductCategory::with('school');

        if ($schoolId = $request->get('school_id')) {
            $query->where('school_id', $schoolId);
        }
        if ($q = $request->get('q')) {
            $query->where('name', 'like', "%{$q}%");
        }

        $categories = $query->orderBy('school_id')->orderBy('sort_order')->orderBy('name')->paginate(25)->withQueryString();
        $schools = School::orderBy('name')->get();

        return view('admin.schools.categories.index', compact('categories', 'schools'));
    }

    public function create(Request $request)
    {
        $schools = School::orderBy('name')->get();
        $selectedSchool = $request->get('school_id') ? School::find($request->get('school_id')) : null;

        return view('admin.schools.categories.form', [
            'category' => new SchoolProductCategory(),
            'schools' => $schools,
            'selectedSchool' => $selectedSchool,
            'editing' => false,
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        SchoolProductCategory::create($this->attributes($data));

        return redirect()->route('admin.school-categories.index')->with('success', 'Category created.');
    }

    public function edit(SchoolProductCategory $schoolCategory)
    {
        $schools = School::orderBy('name')->get();

        return view('admin.schools.categories.form', [
            'category' => $schoolCategory,
            'schools' => $schools,
            'selectedSchool' => $schoolCategory->school,
            'editing' => true,
        ]);
    }

    public function update(Request $request, SchoolProductCategory $schoolCategory)
    {
        $data = $this->validated($request);
        $schoolCategory->update($this->attributes($data));

        return redirect()->route('admin.school-categories.edit', $schoolCategory)->with('success', 'Category updated.');
    }

    public function destroy(SchoolProductCategory $schoolCategory)
    {
        $schoolCategory->delete();

        return redirect()->route('admin.school-categories.index')->with('success', 'Category deleted.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'school_id' => ['required', 'exists:schools,id'],
            'name' => ['required', 'string', 'max:190'],
            'description' => ['nullable', 'string', 'max:500'],
            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);
    }

    private function attributes(array $data): array
    {
        return [
            'school_id' => $data['school_id'],
            'name' => $data['name'],
            'slug' => Str::slug($data['name']),
            'description' => $data['description'] ?? null,
            'is_active' => (bool) ($data['is_active'] ?? true),
            'sort_order' => (int) ($data['sort_order'] ?? 0),
        ];
    }
}
