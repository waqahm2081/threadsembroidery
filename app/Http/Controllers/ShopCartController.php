<?php

namespace App\Http\Controllers;

use App\Models\ShopCartItem;
use App\Services\ProductCatalogService;
use App\Services\ShopCartService;
use App\Services\ShopPricingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ShopCartController extends Controller
{
    public function __construct(
        private ShopCartService $cartService,
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
                    'preview_url' => $item->design_file_path
                        ? asset('storage/'.$item->design_file_path)
                        : null,
                ],
            ])
            ->all();

        return view('shop.cart', [
            'cart' => $cart,
            'items' => $cart->items,
            'subtotal' => $cart->subtotal(),
            'vat' => $cart->vatAmount(),
            'total' => $cart->total(),
            'designPreviews' => $designPreviews,
        ]);
    }

    private function enrichCartItem(ShopCartItem $item): void
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
                if ($colour) {
                    $productImage = $colour['image_front']
                        ?? (($colour['images'][0] ?? null));
                    if (! empty($colour['image_front'])) {
                        $customizerImage = $colour['image_front'];
                    }
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
            'decoration_methods' => ['nullable', 'array'],
            'decoration_methods.*' => ['string', 'in:print,embroidery'],
        ]);

        $item = $this->cartService->addItem($validated);

        return response()->json([
            'message' => 'Product added to cart.',
            'cart_item_id' => $item->id,
            'cart_count' => $this->cartService->itemCount(),
            'customizer_url' => $item->customizerUrl(),
            'customize_url' => route('shop.cart.customize', $item->id),
            'cart_url' => route('shop.cart'),
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
            'preview_image' => ['nullable', 'file', 'mimes:png,jpg,jpeg,svg,webp', 'max:40960'],
            'print_image' => ['nullable', 'file', 'mimes:png,jpg,jpeg,svg,webp', 'max:40960'],
            'artwork_file' => ['nullable', 'file', 'mimes:png,jpg,jpeg,svg,webp', 'max:40960'],
        ]);

        $designData = json_decode($validated['design_data'], true);
        if (! is_array($designData)) {
            $designData = [];
        }

        // Preserve previously selected methods if the payload omitted them
        if (empty($designData['decoration_methods']) && ! empty($item->design_data['decoration_methods'])) {
            $designData['decoration_methods'] = $item->design_data['decoration_methods'];
        }

        $placement = $designData['decoration_placement'] ?? null;
        $decorationFee = ShopPricingService::placementFee($placement);
        $designDir = 'shop-designs/'.$item->id;
        $assetPaths = $this->persistDesignAssets($designData, $designDir);

        if ($request->hasFile('artwork_file')) {
            $artworkPath = $request->file('artwork_file')->storeAs(
                $designDir,
                'artwork-'.Str::uuid().'.'.$request->file('artwork_file')->getClientOriginalExtension(),
                'public'
            );
            $designData['artwork'] = [
                'filename' => $request->file('artwork_file')->getClientOriginalName(),
                'asset_path' => $artworkPath,
            ];
            $assetPaths[] = $artworkPath;
        }

        $previewPath = null;
        if ($request->hasFile('preview_image')) {
            $previewPath = $request->file('preview_image')->storeAs(
                $designDir,
                'preview-'.Str::uuid().'.'.$request->file('preview_image')->getClientOriginalExtension(),
                'public'
            );
            $assetPaths[] = $previewPath;
        } elseif (! empty($designData['artwork']['asset_path'])) {
            $previewPath = $designData['artwork']['asset_path'];
        }

        $printPath = null;
        if ($request->hasFile('print_image')) {
            $printPath = $request->file('print_image')->storeAs(
                $designDir,
                'print-'.Str::uuid().'.'.$request->file('print_image')->getClientOriginalExtension(),
                'public'
            );
            $designData['print_file_path'] = $printPath;
            $assetPaths[] = $printPath;
        }

        $designJsonPath = $designDir.'/design-'.Str::uuid().'.json';
        Storage::disk('public')->put($designJsonPath, json_encode($designData, JSON_PRETTY_PRINT));

        $item->update([
            'design_data' => $designData,
            // Mockup preview for UI; print_file_path (in design_data) used for production download
            'design_file_path' => $previewPath ?? $printPath ?? $designJsonPath,
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
            'cart_url' => route('shop.cart'),
            'checkout_url' => route('shop.checkout'),
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

        $cart = $this->cartService->getCart();

        return response()->json([
            'message' => 'Item removed from cart.',
            'cart_count' => $cart->itemCount(),
            'subtotal' => $cart->subtotal(),
            'vat' => $cart->vatAmount(),
            'total' => $cart->total(),
        ]);
    }
}
