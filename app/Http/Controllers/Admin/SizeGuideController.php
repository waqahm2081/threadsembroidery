<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SizeGuide;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SizeGuideController extends Controller
{
    public function index(): View
    {
        $guides = SizeGuide::query()->orderBy('sort_order')->orderBy('name')->get();

        return view('admin.size-charts.index', [
            'title' => 'Size Charts',
            'subtitle' => 'Manage measurement charts used by bundle garments.',
            'nav' => 'size-charts',
            'guides' => $guides,
        ]);
    }

    public function create(): View
    {
        return view('admin.size-charts.form', [
            'title' => 'Add Size Chart',
            'subtitle' => 'Edit the full size matrix below.',
            'nav' => 'size-charts',
            'guide' => new SizeGuide([
                'is_active' => true,
                'sort_order' => (int) SizeGuide::query()->max('sort_order') + 1,
                'sizes' => ['XS', 'S', 'M', 'L', 'XL', '2XL', '3XL', '4XL'],
                'rows' => [
                    ['label' => 'Chest (½)', 'values' => array_fill(0, 8, '')],
                    ['label' => 'Body length', 'values' => array_fill(0, 8, '')],
                    ['label' => 'Sleeve', 'values' => array_fill(0, 8, '')],
                ],
            ]),
            'mode' => 'create',
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validated($request);
        SizeGuide::query()->create($this->attributes($validated));

        return redirect()->route('admin.size-guides.index')->with('success', 'Size chart created.');
    }

    public function edit(SizeGuide $sizeGuide): View
    {
        return view('admin.size-charts.form', [
            'title' => 'Edit Size Chart',
            'subtitle' => $sizeGuide->name.' — edit the full matrix below.',
            'nav' => 'size-charts',
            'guide' => $sizeGuide,
            'mode' => 'edit',
        ]);
    }

    public function update(Request $request, SizeGuide $sizeGuide): RedirectResponse
    {
        $validated = $this->validated($request, $sizeGuide);
        $sizeGuide->fill($this->attributes($validated))->save();

        return redirect()->route('admin.size-guides.index')->with('success', 'Size chart updated.');
    }

    public function destroy(SizeGuide $sizeGuide): RedirectResponse
    {
        $sizeGuide->delete();

        return redirect()->route('admin.size-guides.index')->with('success', 'Size chart deleted.');
    }

    private function validated(Request $request, ?SizeGuide $guide = null): array
    {
        $request->merge(['is_active' => $request->boolean('is_active')]);

        return $request->validate([
            'key' => ['nullable', 'string', 'max:80', 'alpha_dash', Rule::unique('size_guides', 'key')->ignore($guide?->id)],
            'code' => ['required', 'string', 'max:40'],
            'name' => ['required', 'string', 'max:120'],
            'note' => ['nullable', 'string', 'max:2000'],
            'sizes' => ['required', 'array', 'min:1'],
            'sizes.*' => ['nullable', 'string', 'max:40'],
            'rows' => ['nullable', 'array'],
            'rows.*.label' => ['nullable', 'string', 'max:120'],
            'rows.*.values' => ['nullable', 'array'],
            'rows.*.values.*' => ['nullable', 'string', 'max:40'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['boolean'],
        ]);
    }

    private function attributes(array $validated): array
    {
        $sizes = [];
        foreach ($validated['sizes'] ?? [] as $size) {
            $size = trim((string) $size);
            if ($size !== '') {
                $sizes[] = $size;
            }
        }

        if ($sizes === []) {
            $sizes = ['S', 'M', 'L'];
        }

        $rows = [];
        foreach ($validated['rows'] ?? [] as $row) {
            $label = trim((string) ($row['label'] ?? ''));
            $rawValues = array_values($row['values'] ?? []);
            $values = [];
            $hasValue = false;

            foreach ($sizes as $i => $size) {
                $value = trim((string) ($rawValues[$i] ?? ''));
                if ($value !== '') {
                    $hasValue = true;
                    $values[] = is_numeric($value)
                        ? (str_contains($value, '.') ? (float) $value : (int) $value)
                        : $value;
                } else {
                    $values[] = '';
                }
            }

            if ($label === '' && ! $hasValue) {
                continue;
            }

            $rows[] = [
                'label' => $label !== '' ? $label : 'Measurement',
                'values' => $values,
            ];
        }

        $key = $validated['key'] ?? null;
        if (! $key) {
            $key = Str::lower(Str::slug($validated['code'], ''));
        }

        return [
            'key' => $key,
            'code' => $validated['code'],
            'name' => $validated['name'],
            'note' => $validated['note'] ?? null,
            'sizes' => $sizes,
            'rows' => $rows,
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active' => $validated['is_active'] ?? false,
        ];
    }
}
