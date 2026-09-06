<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bundle;
use App\Models\BundleColour;
use App\Models\SizeGuide;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class BundleController extends Controller
{
    public function index(): View
    {
        $bundles = Bundle::query()
            ->withCount(['includes', 'garments'])
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('admin.bundles.index', [
            'title' => 'Bundles',
            'subtitle' => 'Manage storefront bundle packages.',
            'nav' => 'bundles',
            'tab' => 'bundles',
            'bundles' => $bundles,
        ]);
    }

    public function create(): View
    {
        return view('admin.bundles.form', [
            'title' => 'Add Bundle',
            'subtitle' => 'Create a new package for the storefront.',
            'nav' => 'bundles',
            'tab' => 'bundles',
            'bundle' => new Bundle([
                'icon' => 'bi-box',
                'methods' => ['embroidery', 'print'],
                'is_active' => true,
                'sort_order' => (int) Bundle::query()->max('sort_order') + 1,
            ]),
            'mode' => 'create',
            'sizeGuides' => SizeGuide::query()->orderBy('sort_order')->get(),
            'allColours' => BundleColour::query()->where('is_active', true)->orderBy('sort_order')->orderBy('name')->get(),
            'includes' => [''],
            'garments' => [['category_slug' => '', 'qty' => 1, 'label' => '', 'size_guide_key' => '', 'colour_ids' => []]],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validated($request);
        $bundle = Bundle::query()->create($this->bundleAttributes($validated, null));
        $this->syncNested($bundle, $validated);

        return redirect()->route('admin.bundles.index')->with('success', 'Bundle created.');
    }

    public function edit(Bundle $bundle): View
    {
        $bundle->load(['includes', 'garments.colours']);

        $includes = $bundle->includes->pluck('text')->all();
        if ($includes === []) {
            $includes = [''];
        }

        $garments = $bundle->garments->map(fn ($g) => [
            'category_slug' => $g->category_slug,
            'qty' => $g->qty,
            'label' => $g->label,
            'size_guide_key' => $g->size_guide_key,
            'colour_ids' => $g->colours->pluck('id')->all(),
        ])->all();
        if ($garments === []) {
            $garments = [['category_slug' => '', 'qty' => 1, 'label' => '', 'size_guide_key' => '', 'colour_ids' => []]];
        }

        return view('admin.bundles.form', [
            'title' => 'Edit Bundle',
            'subtitle' => $bundle->name,
            'nav' => 'bundles',
            'tab' => 'bundles',
            'bundle' => $bundle,
            'mode' => 'edit',
            'sizeGuides' => SizeGuide::query()->orderBy('sort_order')->get(),
            'allColours' => BundleColour::query()->where('is_active', true)->orderBy('sort_order')->orderBy('name')->get(),
            'includes' => $includes,
            'garments' => $garments,
        ]);
    }

    public function update(Request $request, Bundle $bundle): RedirectResponse
    {
        $validated = $this->validated($request, $bundle);
        $bundle->fill($this->bundleAttributes($validated, $bundle))->save();
        $this->syncNested($bundle, $validated);

        return redirect()->route('admin.bundles.index')->with('success', 'Bundle updated.');
    }

    public function destroy(Bundle $bundle): RedirectResponse
    {
        $bundle->delete();

        return redirect()->route('admin.bundles.index')->with('success', 'Bundle deleted.');
    }

    private function validated(Request $request, ?Bundle $bundle = null): array
    {
        $request->merge([
            'featured' => $request->boolean('featured'),
            'popular' => $request->boolean('popular'),
            'is_active' => $request->boolean('is_active'),
            'methods' => array_values(array_filter((array) $request->input('methods', []))),
        ]);

        return $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'slug' => ['nullable', 'string', 'max:120', 'alpha_dash', Rule::unique('bundles', 'slug')->ignore($bundle?->id)],
            'tagline' => ['nullable', 'string', 'max:190'],
            'badge' => ['nullable', 'string', 'max:80'],
            'blurb' => ['nullable', 'string', 'max:2000'],
            'price' => ['required', 'numeric', 'min:0'],
            'save_amount' => ['nullable', 'numeric', 'min:0'],
            'methods' => ['required', 'array', 'min:1'],
            'methods.*' => ['in:embroidery,print'],
            'featured' => ['boolean'],
            'popular' => ['boolean'],
            'is_active' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'includes' => ['nullable', 'array'],
            'includes.*' => ['nullable', 'string', 'max:190'],
            'garments' => ['nullable', 'array'],
            'garments.*.category_slug' => ['nullable', 'string', 'max:80'],
            'garments.*.qty' => ['nullable', 'integer', 'min:1'],
            'garments.*.label' => ['nullable', 'string', 'max:120'],
            'garments.*.size_guide_key' => ['nullable', 'string', 'max:80'],
            'garments.*.colour_ids' => ['nullable', 'array'],
            'garments.*.colour_ids.*' => ['integer', 'exists:bundle_colours,id'],
        ]);
    }

    private function bundleAttributes(array $validated, ?Bundle $bundle = null): array
    {
        $slug = $validated['slug'] ?? null;
        if (! $slug) {
            $slug = Str::slug($validated['name']);
        }

        return [
            'name' => $validated['name'],
            'slug' => $slug,
            'icon' => $bundle?->icon ?: 'bi-box',
            'tagline' => $validated['tagline'] ?? null,
            'badge' => $validated['badge'] ?? null,
            'blurb' => $validated['blurb'] ?? null,
            'price' => $validated['price'],
            'save_amount' => $validated['save_amount'] ?? 0,
            'methods' => $validated['methods'],
            'featured' => $validated['featured'] ?? false,
            'popular' => $validated['popular'] ?? false,
            'is_active' => $validated['is_active'] ?? false,
            'sort_order' => $validated['sort_order'] ?? 0,
        ];
    }

    private function syncNested(Bundle $bundle, array $validated): void
    {
        $bundle->includes()->delete();
        foreach (array_values(array_filter($validated['includes'] ?? [], fn ($t) => filled($t))) as $i => $text) {
            $bundle->includes()->create(['text' => $text, 'sort_order' => $i]);
        }

        $bundle->garments()->delete();
        $order = 0;
        foreach ($validated['garments'] ?? [] as $garment) {
            if (! filled($garment['label'] ?? null) && ! filled($garment['category_slug'] ?? null)) {
                continue;
            }
            $row = $bundle->garments()->create([
                'category_slug' => $garment['category_slug'] ?: 'general',
                'qty' => max(1, (int) ($garment['qty'] ?? 1)),
                'label' => $garment['label'] ?: 'Garment',
                'size_guide_key' => $garment['size_guide_key'] ?: null,
                'sort_order' => $order++,
            ]);

            $colourIds = array_values(array_unique(array_map('intval', $garment['colour_ids'] ?? [])));
            if ($colourIds !== []) {
                $sync = [];
                foreach ($colourIds as $ci => $colourId) {
                    $sync[$colourId] = ['sort_order' => $ci];
                }
                $row->colours()->sync($sync);
            }
        }
    }
}
