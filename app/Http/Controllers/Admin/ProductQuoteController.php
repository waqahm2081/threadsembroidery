<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductQuote;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProductQuoteController extends Controller
{
    public function index(): View
    {
        $quotes = ProductQuote::query()
            ->with('user')
            ->latest()
            ->paginate(20);

        return view('admin.product-quotes.index', [
            'title' => 'Product Quotes',
            'subtitle' => 'Saved customizer quotes with live preview.',
            'nav' => 'product-quotes',
            'quotes' => $quotes,
        ]);
    }

    public function show(ProductQuote $productQuote): View
    {
        $productQuote->load('user');

        return view('admin.product-quotes.show', [
            'title' => $productQuote->reference,
            'subtitle' => $productQuote->product_title,
            'nav' => 'product-quotes',
            'quote' => $productQuote,
            'marks' => $this->marksForDisplay($productQuote),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $payload = json_decode($request->input('payload', '{}'), true);
        if (! is_array($payload)) {
            return response()->json(['message' => 'Invalid quote payload.'], 422);
        }

        $request->validate([
            'preview' => ['nullable', 'image', 'max:5120'],
            'logo' => ['nullable', 'file', 'max:10240'],
        ]);

        $productImage = $payload['preview_image'] ?? $payload['product_image'] ?? null;

        $quote = new ProductQuote([
            'reference' => ProductQuote::generateReference(),
            'user_id' => $request->user()?->id,
            'product_slug' => $payload['product_slug'] ?? null,
            'product_title' => $payload['product_title'] ?? 'Custom product',
            'product_brand' => $payload['product_brand'] ?? null,
            'product_style_code' => $payload['product_style_code'] ?? null,
            'product_image' => $productImage,
            'colour_name' => $payload['colour_name'] ?? null,
            'colour_hex' => $payload['colour_hex'] ?? null,
            'method' => $payload['method'] ?? 'embroidery',
            'view' => $payload['view'] ?? 'front',
            'placements' => $payload['placements'] ?? [],
            'texts' => $payload['texts'] ?? [],
            'size_breakdown' => $payload['size_breakdown'] ?? [],
            'quantity' => (int) ($payload['quantity'] ?? 0),
            'garment_unit' => (float) ($payload['garment_unit'] ?? 0),
            'branding_unit' => (float) ($payload['branding_unit'] ?? 0),
            'text_unit' => (float) ($payload['text_unit'] ?? 0),
            'setup_fee' => (float) ($payload['setup_fee'] ?? 0),
            'unit_price' => (float) ($payload['unit_price'] ?? 0),
            'subtotal' => (float) ($payload['subtotal'] ?? 0),
            'logo_name' => $payload['logo_name'] ?? null,
        ]);
        $quote->save();

        $dir = 'product-quotes/'.$quote->id;

        if ($request->hasFile('logo')) {
            $quote->logo_path = $request->file('logo')->store($dir, 'public');
            $quote->logo_name = $request->file('logo')->getClientOriginalName() ?: $quote->logo_name;
        }

        // Prefer a server-built composite (garment + logos) over html2canvas (CORS breaks product photo)
        $composite = $this->buildCompositePreview($quote, $productImage);
        if ($composite) {
            $quote->preview_path = $composite;
        } elseif ($request->hasFile('preview')) {
            $quote->preview_path = $request->file('preview')->store($dir, 'public');
        }

        $quote->save();

        return response()->json([
            'message' => 'Quote saved.',
            'id' => $quote->id,
            'reference' => $quote->reference,
            'redirect' => route('admin.product-quotes.show', $quote),
        ]);
    }

    public function destroy(ProductQuote $productQuote): RedirectResponse
    {
        if ($productQuote->preview_path) {
            Storage::disk('public')->delete($productQuote->preview_path);
        }
        if ($productQuote->logo_path) {
            Storage::disk('public')->delete($productQuote->logo_path);
        }
        Storage::disk('public')->deleteDirectory('product-quotes/'.$productQuote->id);

        $productQuote->delete();

        return redirect()->route('admin.product-quotes.index')->with('success', 'Quote deleted.');
    }

    /**
     * @return array<int, array{label: string, x: float, y: float, w: float}>
     */
    private function marksForDisplay(ProductQuote $quote): array
    {
        $defaults = [
            'left_chest' => ['x' => 38, 'y' => 31, 'w' => 12],
            'right_chest' => ['x' => 62, 'y' => 31, 'w' => 12],
            'centre_chest' => ['x' => 50, 'y' => 45, 'w' => 22],
            'left_sleeve' => ['x' => 22, 'y' => 31, 'w' => 11],
            'right_sleeve' => ['x' => 78, 'y' => 31, 'w' => 11],
            'back_print' => ['x' => 50, 'y' => 42, 'w' => 34],
            'back_embroidery' => ['x' => 50, 'y' => 30, 'w' => 20],
        ];

        $byLabel = [
            'left chest' => 'left_chest',
            'right chest' => 'right_chest',
            'centre chest' => 'centre_chest',
            'center chest' => 'centre_chest',
            'left sleeve' => 'left_sleeve',
            'right sleeve' => 'right_sleeve',
            'back print' => 'back_print',
            'back embroidery' => 'back_embroidery',
        ];

        $marks = [];
        foreach ($quote->placements ?? [] as $placement) {
            $id = $placement['id'] ?? null;
            if (! $id && ! empty($placement['label'])) {
                $id = $byLabel[strtolower(trim($placement['label']))] ?? null;
            }
            $fallback = $defaults[$id] ?? ['x' => 50, 'y' => 40, 'w' => 14];
            $marks[] = [
                'label' => $placement['label'] ?? 'Logo',
                'x' => (float) ($placement['x'] ?? $fallback['x']),
                'y' => (float) ($placement['y'] ?? $fallback['y']),
                'w' => (float) ($placement['w'] ?? $fallback['w']),
            ];
        }

        return $marks;
    }

    private function buildCompositePreview(ProductQuote $quote, ?string $productImageUrl): ?string
    {
        if (! $productImageUrl || ! function_exists('imagecreatetruecolor')) {
            return null;
        }

        $garmentData = $this->fetchBinary($productImageUrl);
        if (! $garmentData) {
            return null;
        }

        $garment = @imagecreatefromstring($garmentData);
        if (! $garment) {
            return null;
        }

        $srcW = imagesx($garment);
        $srcH = imagesy($garment);
        $size = 900;
        $canvas = imagecreatetruecolor($size, $size);
        $white = imagecolorallocate($canvas, 255, 255, 255);
        imagefill($canvas, 0, 0, $white);

        $scale = min($size / $srcW, $size / $srcH);
        $drawW = (int) round($srcW * $scale);
        $drawH = (int) round($srcH * $scale);
        $offsetX = (int) (($size - $drawW) / 2);
        $offsetY = (int) (($size - $drawH) / 2);
        imagecopyresampled($canvas, $garment, $offsetX, $offsetY, 0, 0, $drawW, $drawH, $srcW, $srcH);
        imagedestroy($garment);

        $logo = null;
        if ($quote->logo_path && Storage::disk('public')->exists($quote->logo_path)) {
            $logo = @imagecreatefromstring(Storage::disk('public')->get($quote->logo_path));
        }

        if ($logo) {
            $logoW = imagesx($logo);
            $logoH = imagesy($logo);
            imagealphablending($canvas, true);
            imagesavealpha($canvas, true);

            foreach ($this->marksForDisplay($quote) as $mark) {
                $targetW = max(20, (int) round($size * ($mark['w'] / 100)));
                $targetH = (int) round($targetW * ($logoH / max(1, $logoW)));
                $cx = (int) round($size * ($mark['x'] / 100));
                $cy = (int) round($size * ($mark['y'] / 100));
                $dx = (int) ($cx - $targetW / 2);
                $dy = (int) ($cy - $targetH / 2);

                $scaled = imagecreatetruecolor($targetW, $targetH);
                imagealphablending($scaled, false);
                imagesavealpha($scaled, true);
                $transparent = imagecolorallocatealpha($scaled, 0, 0, 0, 127);
                imagefilledrectangle($scaled, 0, 0, $targetW, $targetH, $transparent);
                imagecopyresampled($scaled, $logo, 0, 0, 0, 0, $targetW, $targetH, $logoW, $logoH);
                imagecopy($canvas, $scaled, $dx, $dy, 0, 0, $targetW, $targetH);
                imagedestroy($scaled);
            }
            imagedestroy($logo);
        }

        ob_start();
        imagepng($canvas);
        $png = ob_get_clean();
        imagedestroy($canvas);

        if (! $png) {
            return null;
        }

        $path = 'product-quotes/'.$quote->id.'/preview.png';
        Storage::disk('public')->put($path, $png);

        return $path;
    }

    private function fetchBinary(string $url): ?string
    {
        try {
            $response = Http::timeout(25)
                ->withHeaders(['User-Agent' => 'ThreadsAdminPreview/1.0'])
                ->get($url);

            if (! $response->successful()) {
                return null;
            }

            return $response->body();
        } catch (\Throwable) {
            return null;
        }
    }
}
