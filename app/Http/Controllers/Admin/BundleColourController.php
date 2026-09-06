<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BundleColour;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class BundleColourController extends Controller
{
    public function index(): View
    {
        $colours = BundleColour::query()->orderBy('sort_order')->orderBy('name')->get();

        return view('admin.bundles.colours.index', [
            'title' => 'Bundle Colours',
            'subtitle' => 'Colour palette available for bundle garments.',
            'nav' => 'bundles',
            'tab' => 'colours',
            'colours' => $colours,
        ]);
    }

    public function create(): View
    {
        return view('admin.bundles.colours.form', [
            'title' => 'Add Colour',
            'subtitle' => 'Create a colour for bundle garments.',
            'nav' => 'bundles',
            'tab' => 'colours',
            'colour' => new BundleColour([
                'hex' => '#1c1c1c',
                'is_active' => true,
                'sort_order' => (int) BundleColour::query()->max('sort_order') + 1,
            ]),
            'mode' => 'create',
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        BundleColour::query()->create($this->attributes($this->validated($request)));

        return redirect()->route('admin.colours.index')->with('success', 'Colour created.');
    }

    public function edit(BundleColour $colour): View
    {
        return view('admin.bundles.colours.form', [
            'title' => 'Edit Colour',
            'subtitle' => $colour->name,
            'nav' => 'bundles',
            'tab' => 'colours',
            'colour' => $colour,
            'mode' => 'edit',
        ]);
    }

    public function update(Request $request, BundleColour $colour): RedirectResponse
    {
        $colour->fill($this->attributes($this->validated($request, $colour)))->save();

        return redirect()->route('admin.colours.index')->with('success', 'Colour updated.');
    }

    public function destroy(BundleColour $colour): RedirectResponse
    {
        $colour->delete();

        return redirect()->route('admin.colours.index')->with('success', 'Colour deleted.');
    }

    private function validated(Request $request, ?BundleColour $colour = null): array
    {
        $request->merge([
            'is_active' => $request->boolean('is_active'),
        ]);

        return $request->validate([
            'name' => ['required', 'string', 'max:80'],
            'slug' => ['nullable', 'string', 'max:80', 'alpha_dash', Rule::unique('bundle_colours', 'slug')->ignore($colour?->id)],
            'hex' => ['required', 'string', 'max:20'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['boolean'],
        ]);
    }

    private function attributes(array $validated): array
    {
        $slug = $validated['slug'] ?? null;
        if (! $slug) {
            $slug = Str::slug($validated['name']);
        }

        $hex = trim($validated['hex']);
        if ($hex !== '' && ! str_starts_with($hex, '#')) {
            $hex = '#'.$hex;
        }

        return [
            'name' => $validated['name'],
            'slug' => $slug,
            'hex' => strtolower($hex),
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active' => $validated['is_active'] ?? false,
        ];
    }
}
