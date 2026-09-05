<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShopOrder;
use App\Models\ShopOrderItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ShopOrderController extends Controller
{
    public const STATUSES = [
        'pending',
        'processing',
        'shipped',
        'delivered',
        'cancelled',
    ];

    public function index(Request $request): View
    {
        $q = trim((string) $request->get('q', ''));
        $status = $request->get('status');

        $orders = ShopOrder::query()
            ->with('user')
            ->withCount('items')
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($inner) use ($q) {
                    $inner->where('order_number', 'like', "%{$q}%")
                        ->orWhere('email', 'like', "%{$q}%")
                        ->orWhere('name', 'like', "%{$q}%")
                        ->orWhere('company_name', 'like', "%{$q}%")
                        ->orWhere('phone', 'like', "%{$q}%");
                });
            })
            ->when(in_array($status, self::STATUSES, true), fn ($query) => $query->where('status', $status))
            ->latest()
            ->paginate(25)
            ->withQueryString();

        return view('admin.shop-orders.index', [
            'title' => 'Shop Orders',
            'subtitle' => 'Product catalogue orders from the shop.',
            'nav' => 'shop-orders',
            'orders' => $orders,
            'q' => $q,
            'status' => $status,
            'statuses' => self::STATUSES,
        ]);
    }

    public function show(ShopOrder $shopOrder): View
    {
        $shopOrder->load(['items', 'user']);

        return view('admin.shop-orders.show', [
            'title' => 'Shop Order '.$shopOrder->order_number,
            'subtitle' => 'Placed '.optional($shopOrder->created_at)->format('d M Y H:i'),
            'nav' => 'shop-orders',
            'order' => $shopOrder,
            'statuses' => self::STATUSES,
        ]);
    }

    public function updateStatus(Request $request, ShopOrder $shopOrder): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(self::STATUSES)],
        ]);

        $shopOrder->status = $validated['status'];
        $shopOrder->save();

        return back()->with('success', 'Order status updated to '.$validated['status'].'.');
    }

    public function downloadDesign(ShopOrder $shopOrder, ShopOrderItem $item): StreamedResponse|RedirectResponse
    {
        abort_unless($item->shop_order_id === $shopOrder->id, 404);

        $candidates = array_filter([
            $item->design_data['print_file_path'] ?? null,
            $item->design_file_path,
        ]);

        // Prefer print-* files from assets (high-res production artwork)
        $assets = collect($item->design_assets ?? [])
            ->filter(fn ($asset) => is_string($asset) && Storage::disk('public')->exists($asset));

        $printAsset = $assets->first(fn ($asset) => str_contains(basename($asset), 'print-'));
        if ($printAsset) {
            array_unshift($candidates, $printAsset);
        }

        $path = collect($candidates)
            ->filter(fn ($p) => is_string($p) && Storage::disk('public')->exists($p))
            ->first();

        if (! $path) {
            $path = $assets->first();
        }

        if (! $path || ! Storage::disk('public')->exists($path)) {
            return back()->with('error', 'No downloadable design file found for this item.');
        }

        $extension = pathinfo($path, PATHINFO_EXTENSION) ?: 'bin';
        $isPrint = str_contains(basename($path), 'print-');
        $filename = sprintf(
            '%s-item-%d-%s.%s',
            $shopOrder->order_number,
            $item->id,
            $isPrint ? 'print-artwork' : 'design',
            $extension
        );

        return Storage::disk('public')->download($path, $filename);
    }
}
