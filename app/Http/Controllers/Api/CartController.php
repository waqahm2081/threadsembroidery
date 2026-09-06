<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CartService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct(private CartService $cartService)
    {
    }

    public function show()
    {
        return response()->json($this->cartService->toFrontend());
    }

    public function storeBundle(Request $request)
    {
        // FormData sends JSON fields as strings — decode before validation
        $sizes = $request->input('sizes');
        if (is_string($sizes)) {
            $sizes = json_decode($sizes, true) ?: [];
        }

        $addons = $request->input('addons', []);
        if (is_string($addons)) {
            $addons = json_decode($addons, true) ?: [];
        }

        $artworkLabels = $request->input('artwork_labels', []);
        if (is_string($artworkLabels)) {
            $artworkLabels = json_decode($artworkLabels, true) ?: [];
        }

        $libraryIds = $request->input('artwork_library', []);
        if (is_string($libraryIds)) {
            $libraryIds = json_decode($libraryIds, true) ?: [];
        }

        $garmentColours = $request->input('garment_colours', []);
        if (is_string($garmentColours)) {
            $garmentColours = json_decode($garmentColours, true) ?: [];
        }

        $request->merge([
            'sizes' => $sizes,
            'addons' => $addons,
            'artwork_labels' => $artworkLabels,
            'artwork_library' => $libraryIds,
            'garment_colours' => $garmentColours,
        ]);

        $data = $request->validate([
            'bundle_slug' => ['required', 'string'],
            'method' => ['required', 'in:embroidery,print'],
            'colour_name' => ['required', 'string', 'max:255'],
            'colour_hex' => ['nullable', 'string', 'max:20'],
            'garment_colours' => ['nullable', 'array'],
            'garment_colours.*.name' => ['nullable', 'string', 'max:80'],
            'garment_colours.*.hex' => ['nullable', 'string', 'max:20'],
            'sizes' => ['required', 'array'],
            'addons' => ['nullable', 'array'],
            'addons.*' => ['string'],
            'artwork_labels' => ['nullable', 'array'],
            'artwork_labels.*' => ['string'],
            'artwork_library' => ['nullable', 'array'],
            'artwork_library.*' => ['integer'],
        ]);

        $files = [];
        foreach ($request->allFiles() as $key => $file) {
            if (str_starts_with($key, 'artwork_')) {
                $files[substr($key, 8)] = $file;
            }
        }

        $request->validate([
            ...collect($files)->mapWithKeys(fn ($file, $slug) => [
                'artwork_'.$slug => ['file', 'max:10240', 'mimes:png,jpg,jpeg,svg,pdf'],
            ])->all(),
        ]);

        $item = $this->cartService->addBundle($data, $files, $data['artwork_library'] ?? []);

        return response()->json([
            'ok' => true,
            'item' => $item->toFrontend(),
            'cart' => $this->cartService->toFrontend(),
        ]);
    }

    public function destroyItem(int $itemId)
    {
        $this->cartService->removeItem($itemId);

        return response()->json([
            'ok' => true,
            'cart' => $this->cartService->toFrontend(),
        ]);
    }
}
