<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SchoolController extends Controller
{
    public function index(Request $request)
    {
        $query = School::withCount(['allCategories as categories_count', 'allProducts as products_count']);

        if ($q = $request->get('q')) {
            $query->where('name', 'like', "%{$q}%");
        }

        $schools = $query->orderBy('sort_order')->orderBy('name')->paginate(25)->withQueryString();

        return view('admin.schools.index', compact('schools'));
    }

    public function create()
    {
        return view('admin.schools.form', ['school' => new School(), 'editing' => false]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $school = School::create($this->attributes($data, $request));

        return redirect()->route('admin.schools.edit', $school)->with('success', 'School created.');
    }

    public function edit(School $school)
    {
        return view('admin.schools.form', ['school' => $school, 'editing' => true]);
    }

    public function update(Request $request, School $school)
    {
        $data = $this->validated($request);
        $school->update($this->attributes($data, $request, $school));

        return redirect()->route('admin.schools.edit', $school)->with('success', 'School updated.');
    }

    public function destroy(School $school)
    {
        if ($school->logo_path) {
            Storage::disk('public')->delete($school->logo_path);
        }
        $school->delete();

        return redirect()->route('admin.schools.index')->with('success', 'School deleted.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:190'],
            'contact_email' => ['nullable', 'email', 'max:190'],
            'contact_phone' => ['nullable', 'string', 'max:40'],
            'address' => ['nullable', 'string', 'max:500'],
            'description' => ['nullable', 'string'],
            'accent_colour' => ['nullable', 'string', 'max:20'],
            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'logo' => ['nullable', 'image', 'max:3072'],
            'remove_logo' => ['nullable', 'boolean'],
        ]);
    }

    private function attributes(array $data, Request $request, ?School $school = null): array
    {
        $attrs = [
            'name' => $data['name'],
            'slug' => Str::slug($data['name']),
            'contact_email' => $data['contact_email'] ?? null,
            'contact_phone' => $data['contact_phone'] ?? null,
            'address' => $data['address'] ?? null,
            'description' => $data['description'] ?? null,
            'accent_colour' => $data['accent_colour'] ?? '#e85820',
            'is_active' => (bool) ($data['is_active'] ?? true),
            'sort_order' => (int) ($data['sort_order'] ?? 0),
        ];

        if ($request->boolean('remove_logo') && $school?->logo_path) {
            Storage::disk('public')->delete($school->logo_path);
            $attrs['logo_path'] = null;
        } elseif ($request->hasFile('logo')) {
            if ($school?->logo_path) {
                Storage::disk('public')->delete($school->logo_path);
            }
            $attrs['logo_path'] = $request->file('logo')->store('schools', 'public');
        } elseif ($school) {
            $attrs['logo_path'] = $school->logo_path;
        }

        return $attrs;
    }
}
