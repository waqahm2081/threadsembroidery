<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PlacementOption;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PlacementOptionController extends Controller
{
    public function index(): View
    {
        $placements = PlacementOption::query()->orderBy('sort_order')->orderBy('label')->get();

        return view('admin.bundles.placements.index', [
            'title' => 'Placement Options',
            'subtitle' => 'Branding add-ons used in the bundle builder.',
            'nav' => 'bundles',
            'tab' => 'placements',
            'placements' => $placements,
        ]);
    }

    public function create(): View
    {
        return view('admin.bundles.placements.form', [
            'title' => 'Add Placement',
            'subtitle' => 'Create a branding placement option.',
            'nav' => 'bundles',
            'tab' => 'placements',
            'placement' => new PlacementOption([
                'method' => 'both',
                'price' => 0,
                'is_active' => true,
                'included_in_bundle' => false,
                'sort_order' => (int) PlacementOption::query()->max('sort_order') + 1,
                'sizes' => [],
            ]),
            'mode' => 'create',
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validated($request);
        PlacementOption::query()->create($this->attributes($validated));

        return redirect()->route('admin.placements.index')->with('success', 'Placement created.');
    }

    public function edit(PlacementOption $placement): View
    {
        return view('admin.bundles.placements.form', [
            'title' => 'Edit Placement',
            'subtitle' => $placement->label,
            'nav' => 'bundles',
            'tab' => 'placements',
            'placement' => $placement,
            'mode' => 'edit',
        ]);
    }

    public function update(Request $request, PlacementOption $placement): RedirectResponse
    {
        $validated = $this->validated($request, $placement);
        $placement->fill($this->attributes($validated))->save();

        return redirect()->route('admin.placements.index')->with('success', 'Placement updated.');
    }

    public function destroy(PlacementOption $placement): RedirectResponse
    {
        $placement->delete();

        return redirect()->route('admin.placements.index')->with('success', 'Placement deleted.');
    }

    private function validated(Request $request, ?PlacementOption $placement = null): array
    {
        $request->merge([
            'is_active' => $request->boolean('is_active'),
            'included_in_bundle' => $request->boolean('included_in_bundle'),
        ]);

        return $request->validate([
            'slug' => ['nullable', 'string', 'max:80', 'alpha_dash', Rule::unique('placement_options', 'slug')->ignore($placement?->id)],
            'label' => ['required', 'string', 'max:120'],
            'method' => ['required', Rule::in(['both', 'print', 'embroidery'])],
            'price' => ['required', 'numeric', 'min:0'],
            'sizes_text' => ['nullable', 'string', 'max:500'],
            'included_in_bundle' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['boolean'],
        ]);
    }

    private function attributes(array $validated): array
    {
        $slug = $validated['slug'] ?? null;
        if (! $slug) {
            $slug = Str::slug($validated['label'], '_');
        }

        $sizes = array_values(array_filter(array_map('trim', preg_split('/[,|]+/', (string) ($validated['sizes_text'] ?? '')) ?: []), fn ($v) => $v !== ''));

        return [
            'slug' => $slug,
            'label' => $validated['label'],
            'method' => $validated['method'],
            'price' => $validated['price'],
            'sizes' => $sizes,
            'included_in_bundle' => $validated['included_in_bundle'] ?? false,
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active' => $validated['is_active'] ?? false,
        ];
    }
}
