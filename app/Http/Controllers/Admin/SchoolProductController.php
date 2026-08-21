<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\SchoolProduct;
use App\Models\SchoolProductCategory;
use App\Models\SchoolProductImage;
use App\Models\SchoolProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SchoolProductController extends Controller
{
    public function index(Request $request)
    {
        $query = SchoolProduct::with(['school', 'category', 'images'])
            ->withCount('variants')
            ->withMin('variants', 'price');

        if ($schoolId = $request->get('school_id')) {
            $query->where('school_id', $schoolId);
        }
        if ($q = $request->get('q')) {
            $query->where('name', 'like', "%{$q}%");
        }

        $products = $query->orderBy('school_id')->orderBy('sort_order')->orderBy('name')->paginate(25)->withQueryString();
        $schools = School::orderBy('name')->get();

        return view('admin.schools.products.index', compact('products', 'schools'));
    }

    public function create(Request $request)
    {
        $schools = School::with('allCategories')->orderBy('name')->get();
        $selectedSchool = $request->get('school_id') ? School::find($request->get('school_id')) : null;

        return view('admin.schools.products.form', [
            'product' => new SchoolProduct(),
            'schools' => $schools,
            'selectedSchool' => $selectedSchool,
            'editing' => false,
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $product = SchoolProduct::create($this->productAttributes($data));

        $this->syncVariants($product, $request->input('variants', []));
        $this->handleImages($product, $request);

        return redirect()->route('admin.school-products.edit', $product)->with('success', 'Product created.');
    }

    public function edit(SchoolProduct $schoolProduct)
    {
        $schoolProduct->load(['variants', 'images', 'school', 'category']);
        $schools = School::with('allCategories')->orderBy('name')->get();

        return view('admin.schools.products.form', [
            'product' => $schoolProduct,
            'schools' => $schools,
            'selectedSchool' => $schoolProduct->school,
            'editing' => true,
        ]);
    }

    public function update(Request $request, SchoolProduct $schoolProduct)
    {
        $data = $this->validated($request);
        $schoolProduct->update($this->productAttributes($data));

        $this->syncVariants($schoolProduct, $request->input('variants', []));
        $this->handleImages($schoolProduct, $request);

        return redirect()->route('admin.school-products.edit', $schoolProduct)->with('success', 'Product updated.');
    }

    public function destroy(SchoolProduct $schoolProduct)
    {
        foreach ($schoolProduct->images as $img) {
            Storage::disk('public')->delete($img->path);
        }
        $schoolProduct->delete();

        return redirect()->route('admin.school-products.index')->with('success', 'Product deleted.');
    }

    public function destroyImage(SchoolProductImage $image)
    {
        $productId = $image->school_product_id;
        Storage::disk('public')->delete($image->path);
        $image->delete();

        return redirect()->route('admin.school-products.edit', $productId)->with('success', 'Image removed.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'school_id' => ['required', 'exists:schools,id'],
            'school_product_category_id' => ['nullable', 'exists:school_product_categories,id'],
            'name' => ['required', 'string', 'max:190'],
            'description' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'variants' => ['nullable', 'array'],
            'variants.*.size' => ['required_with:variants', 'string', 'max:40'],
            'variants.*.price' => ['required_with:variants', 'numeric', 'min:0'],
            'images.*' => ['nullable', 'image', 'max:4096'],
        ]);
    }

    private function productAttributes(array $data): array
    {
        return [
            'school_id' => $data['school_id'],
            'school_product_category_id' => $data['school_product_category_id'] ?: null,
            'name' => $data['name'],
            'slug' => Str::slug($data['name']),
            'description' => $data['description'] ?? null,
            'is_active' => (bool) ($data['is_active'] ?? true),
            'sort_order' => (int) ($data['sort_order'] ?? 0),
        ];
    }

    private function syncVariants(SchoolProduct $product, array $variants): void
    {
        $keepIds = [];
        foreach ($variants as $i => $v) {
            if (empty($v['size']) || !isset($v['price'])) continue;
            $id = $v['id'] ?? null;
            if ($id) {
                $variant = SchoolProductVariant::find($id);
                if ($variant && $variant->school_product_id === $product->id) {
                    $variant->update(['size' => $v['size'], 'price' => $v['price'], 'sort_order' => $i]);
                    $keepIds[] = $variant->id;
                    continue;
                }
            }
            $new = $product->variants()->create(['size' => $v['size'], 'price' => $v['price'], 'sort_order' => $i]);
            $keepIds[] = $new->id;
        }
        $product->variants()->whereNotIn('id', $keepIds)->delete();
    }

    private function handleImages(SchoolProduct $product, Request $request): void
    {
        // Delete flagged
        $toDelete = $request->input('delete_images', []);
        if ($toDelete) {
            $imgs = SchoolProductImage::whereIn('id', $toDelete)->where('school_product_id', $product->id)->get();
            foreach ($imgs as $img) {
                Storage::disk('public')->delete($img->path);
                $img->delete();
            }
        }

        // Upload new
        if ($request->hasFile('images')) {
            $existing = $product->images()->count();
            foreach ($request->file('images') as $i => $file) {
                $path = $file->store('school-products/'.$product->id, 'public');
                SchoolProductImage::create([
                    'school_product_id' => $product->id,
                    'path' => $path,
                    'alt_text' => $product->name,
                    'is_primary' => ($existing === 0 && $i === 0),
                    'sort_order' => $existing + $i,
                ]);
                $existing++;
            }
        }

        // Set primary
        if ($primaryId = $request->input('primary_image_id')) {
            $product->images()->update(['is_primary' => false]);
            SchoolProductImage::where('id', $primaryId)->where('school_product_id', $product->id)->update(['is_primary' => true]);
        }
    }
}
