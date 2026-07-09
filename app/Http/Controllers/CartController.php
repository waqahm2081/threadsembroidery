<?php

namespace App\Http\Controllers;

use App\Services\CartService;
use App\Services\PricingService;
use App\Services\ProductCatalogService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CartController extends Controller
{
    public function __construct(
        private CartService $cartService,
        private ProductCatalogService $catalog,
    ) {
    }

    public function index(): View
    {
        $cart = $this->cartService->getCart();

        foreach ($cart->items as $item) {
            $this->enrichCartItem($item);
        }

        $designPreviews = $cart->items
            ->filter(fn ($item) => $item->is_customized && $item->design_data)
            ->mapWithKeys(fn ($item) => [
                $item->id => [
                    'title' => $item->product_title,
                    'colour_hex' => $item->colour_hex ?? ($item->design_data['colour']['hex'] ?? '#ffffff'),
                    'colour_name' => $item->colour_name ?? ($item->design_data['colour']['name'] ?? ''),
                    'customizer_image' => $item->customizer_image
                        ?? ($item->design_data['customizer_image'] ?? asset('assets/images/customizer/tshirtcustom.png')),
                    'customizer_image_back' => $item->design_data['customizer_image_back']
                        ?? asset('assets/images/customizer/tshirtcustom-back.png'),
                    'size_breakdown' => $item->size_breakdown,
                    'quantity' => $item->quantity,
                    'design_data' => $item->design_data,
                    'storage_base' => asset('storage'),
                ],
            ])
            ->all();

        return view('cart', [
            'cart' => $cart,
            'items' => $cart->items,
            'subtotal' => $cart->subtotal(),
            'vat' => $cart->vatAmount(),
            'total' => $cart->total(),
            'designPreviews' => $designPreviews,
        ]);
    }

    private function enrichCartItem(\App\Models\CartItem $item): void
    {
        $productImage = null;
        $customizerImage = asset('assets/images/customizer/tshirtcustom.png');

        $category = $this->catalog->resolveCategoryBySlug($item->category_slug);
        if ($category) {
            $product = $this->catalog->getProductDetail($item->style_code, $category['name']);
            if ($product) {
                $customizerImage = $product['customizer_image'];
                $colour = collect($product['colours'])->firstWhere('code', $item->colour_code)
                    ?? ($product['colours'][0] ?? null);
                if ($colour && ! empty($colour['images'])) {
                    $productImage = $colour['images'][0];
                }
            }
        }

        $item->setAttribute('product_image', $productImage);
        $item->setAttribute('customizer_image', $item->design_data['customizer_image'] ?? $customizerImage);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'style_code' => ['required', 'string'],
            'product_title' => ['required', 'string'],
            'category_slug' => ['required', 'string'],
            'product_slug' => ['required', 'string'],
            'colour_code' => ['nullable', 'string'],
            'colour_name' => ['nullable', 'string'],
            'colour_hex' => ['nullable', 'string'],
            'size' => ['nullable', 'string'],
            'size_breakdown' => ['nullable', 'array'],
            'size_breakdown.*.size' => ['required_with:size_breakdown', 'string'],
            'size_breakdown.*.qty' => ['required_with:size_breakdown', 'integer', 'min:1'],
            'size_breakdown.*.unit_price' => ['required_with:size_breakdown', 'numeric', 'min:0'],
            'quantity' => ['required', 'integer', 'min:1'],
            'unit_price' => ['required', 'numeric', 'min:0'],
            'logo_setup' => ['nullable', 'boolean'],
        ]);

        $item = $this->cartService->addItem($validated);

        return response()->json([
            'message' => 'Product added to cart.',
            'cart_item_id' => $item->id,
            'cart_count' => $this->cartService->itemCount(),
            'customizer_url' => $item->customizerUrl(),
        ]);
    }

    public function customize(Request $request, int $itemId): JsonResponse
    {
        $item = $this->cartService->findItem($itemId);

        if (! $item) {
            return response()->json(['message' => 'Cart item not found.'], 404);
        }

        $validated = $request->validate([
            'design_data' => ['required', 'json'],
            'unit_price' => ['required', 'numeric', 'min:0'],
            'decoration_fee' => ['nullable', 'numeric', 'min:0'],
            'quantity' => ['nullable', 'integer', 'min:1'],
            'size' => ['nullable', 'string'],
            'colour_name' => ['nullable', 'string'],
            'preview_image' => ['nullable', 'image', 'max:10240'],
        ]);

        $designData = json_decode($validated['design_data'], true);
        $placement = $designData['decoration_placement'] ?? null;
        $decorationFee = PricingService::placementFee($placement);
        $designDir = 'designs/'.$item->id;
        $assetPaths = $this->persistDesignAssets($designData, $designDir);

        $previewPath = null;
        if ($request->hasFile('preview_image')) {
            $previewPath = $request->file('preview_image')->storeAs(
                $designDir,
                'preview-'.Str::uuid().'.png',
                'public'
            );
        }

        $designJsonPath = $designDir.'/design-'.Str::uuid().'.json';
        Storage::disk('public')->put($designJsonPath, json_encode($designData, JSON_PRETTY_PRINT));

        $item->update([
            'design_data' => $designData,
            'design_file_path' => $previewPath ?? $designJsonPath,
            'design_assets' => $assetPaths,
            'unit_price' => $validated['unit_price'],
            'decoration_fee' => $decorationFee,
            'quantity' => $validated['quantity'] ?? $item->quantity,
            'size' => $validated['size'] ?? $item->size,
            'colour_name' => $validated['colour_name'] ?? $item->colour_name,
            'colour_hex' => $designData['colour']['hex'] ?? $item->colour_hex,
            'colour_code' => $designData['colour']['code'] ?? $item->colour_code,
            'is_customized' => true,
        ]);

        return response()->json([
            'message' => 'Design saved and added to cart.',
            'cart_count' => $this->cartService->itemCount(),
            'cart_url' => route('cart'),
            'checkout_url' => route('checkout'),
        ]);
    }

    private function persistDesignAssets(array &$designData, string $designDir): array
    {
        $assetPaths = [];

        if (! isset($designData['layers']) || ! is_array($designData['layers'])) {
            return $assetPaths;
        }

        foreach ($designData['layers'] as &$layer) {
            if (($layer['type'] ?? '') !== 'image' || empty($layer['src'])) {
                continue;
            }

            if (! str_starts_with($layer['src'], 'data:')) {
                if (! empty($layer['asset_path'])) {
                    $assetPaths[] = $layer['asset_path'];
                }
                continue;
            }

            if (! preg_match('/^data:image\/(\w+);base64,/', $layer['src'], $matches)) {
                continue;
            }

            $extension = $matches[1] === 'svg+xml' ? 'svg' : $matches[1];
            $data = base64_decode(substr($layer['src'], strpos($layer['src'], ',') + 1));

            if ($data === false) {
                continue;
            }

            $filename = 'artwork-layer-'.($layer['id'] ?? Str::uuid()).'.'.$extension;
            $path = $designDir.'/'.$filename;
            Storage::disk('public')->put($path, $data);

            $layer['asset_path'] = $path;
            unset($layer['src']);
            $assetPaths[] = $path;
        }
        unset($layer);

        return $assetPaths;
    }

    public function destroy(int $itemId): JsonResponse
    {
        if (! $this->cartService->removeItem($itemId)) {
            return response()->json(['message' => 'Cart item not found.'], 404);
        }

        return response()->json([
            'message' => 'Item removed from cart.',
            'cart_count' => $this->cartService->itemCount(),
        ]);
    }
}
