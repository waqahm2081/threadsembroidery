<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BrandingEnquiry;
use App\Models\Order;
use App\Models\SchoolUniformOrder;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        [$from, $to] = $this->resolveDateRange($request);

        $recentOrders = Order::query()
            ->whereBetween('created_at', [$from, $to])
            ->latest()
            ->limit(8)
            ->get();

        $recentUniformOrders = SchoolUniformOrder::query()
            ->whereBetween('created_at', [$from, $to])
            ->latest()
            ->limit(8)
            ->get();

        return view('admin.dashboard', [
            'title' => 'Dashboard',
            'subtitle' => 'Overview of sales, orders, customers and enquiries.',
            'nav' => 'dashboard',
            'dateFrom' => $from->toDateString(),
            'dateTo' => $to->toDateString(),
            'dateRangeLabel' => $from->format('d M Y').' — '.$to->format('d M Y'),
            'stats' => [
                'total_sales' => (float) Order::query()
                    ->where('status', '!=', 'cancelled')
                    ->whereBetween('created_at', [$from, $to])
                    ->sum('total')
                    + (float) SchoolUniformOrder::query()
                        ->where('status', '!=', 'cancelled')
                        ->whereBetween('created_at', [$from, $to])
                        ->sum('total'),
                'total_orders' => Order::query()
                    ->whereBetween('created_at', [$from, $to])
                    ->count(),
                'total_customers' => User::query()
                    ->where('role', User::ROLE_CUSTOMER)
                    ->whereBetween('created_at', [$from, $to])
                    ->count(),
                'total_enquiries' => BrandingEnquiry::query()
                    ->whereBetween('created_at', [$from, $to])
                    ->count(),
            ],
            'recentOrders' => $recentOrders,
            'recentUniformOrders' => $recentUniformOrders,
            'salesChart' => $this->monthlySalesSeries(),
            'uniformSalesChart' => $this->monthlyUniformSalesSeries(),
        ]);
    }

    /**
     * @return array{0: Carbon, 1: Carbon}
     */
    private function resolveDateRange(Request $request): array
    {
        $defaultFrom = now()->startOfMonth()->startOfDay();
        $defaultTo = now()->endOfDay();

        $raw = trim((string) $request->get('range', ''));
        if ($raw === '') {
            return [$defaultFrom, $defaultTo];
        }

        // Accept "YYYY-MM-DD to YYYY-MM-DD" or "YYYY-MM-DD — YYYY-MM-DD"
        if (preg_match('/^(\d{4}-\d{2}-\d{2})\s*(?:to|—|-)\s*(\d{4}-\d{2}-\d{2})$/i', $raw, $m)) {
            try {
                $from = Carbon::parse($m[1])->startOfDay();
                $to = Carbon::parse($m[2])->endOfDay();
                if ($from->gt($to)) {
                    [$from, $to] = [$to->copy()->startOfDay(), $from->copy()->endOfDay()];
                }

                return [$from, $to];
            } catch (\Throwable) {
                // fall through to default
            }
        }

        return [$defaultFrom, $defaultTo];
    }

    private function monthlySalesSeries(): array
    {
        $months = $this->lastSixMonths();
        $driver = DB::getDriverName();
        $monthKey = $driver === 'sqlite'
            ? "strftime('%Y-%m', created_at)"
            : "DATE_FORMAT(created_at, '%Y-%m')";

        $rows = Order::query()
            ->selectRaw("{$monthKey} as ym, SUM(total) as amount")
            ->where('status', '!=', 'cancelled')
            ->where('created_at', '>=', $months[0]['start'])
            ->groupBy('ym')
            ->pluck('amount', 'ym');

        return [
            'labels' => array_column($months, 'label'),
            'values' => array_map(
                fn (array $month) => round((float) ($rows[$month['key']] ?? 0), 2),
                $months
            ),
        ];
    }

    private function monthlyUniformSalesSeries(): array
    {
        $months = $this->lastSixMonths();
        $driver = DB::getDriverName();
        $monthKey = $driver === 'sqlite'
            ? "strftime('%Y-%m', created_at)"
            : "DATE_FORMAT(created_at, '%Y-%m')";

        $rows = SchoolUniformOrder::query()
            ->selectRaw("{$monthKey} as ym, SUM(total) as amount")
            ->where('status', '!=', 'cancelled')
            ->where('created_at', '>=', $months[0]['start'])
            ->groupBy('ym')
            ->pluck('amount', 'ym');

        return [
            'labels' => array_column($months, 'label'),
            'values' => array_map(
                fn (array $month) => round((float) ($rows[$month['key']] ?? 0), 2),
                $months
            ),
        ];
    }

    /**
     * @return array<int, array{key: string, label: string, start: Carbon}>
     */
    private function lastSixMonths(): array
    {
        $months = [];
        $cursor = now()->startOfMonth()->subMonths(5);

        for ($i = 0; $i < 6; $i++) {
            $months[] = [
                'key' => $cursor->format('Y-m'),
                'label' => $cursor->format('M Y'),
                'start' => $cursor->copy()->startOfMonth(),
            ];
            $cursor->addMonth();
        }

        return $months;
    }
}
