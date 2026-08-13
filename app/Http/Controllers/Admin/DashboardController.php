<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BrandingEnquiry;
use App\Models\Order;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $recentOrders = Order::query()->latest()->limit(8)->get();
        $recentEnquiries = BrandingEnquiry::query()->latest()->limit(5)->get();

        return view('admin.dashboard', [
            'title' => 'Dashboard',
            'subtitle' => 'Overview of sales, orders, customers and enquiries.',
            'nav' => 'dashboard',
            'stats' => [
                'total_sales' => (float) Order::query()
                    ->where('status', '!=', 'cancelled')
                    ->sum('total'),
                'total_orders' => Order::query()->count(),
                'total_customers' => User::query()->where('role', User::ROLE_CUSTOMER)->count(),
                'total_enquiries' => BrandingEnquiry::query()->count(),
            ],
            'recentOrders' => $recentOrders,
            'recentEnquiries' => $recentEnquiries,
            'salesChart' => $this->monthlySalesSeries(),
            'enquiriesChart' => $this->monthlyEnquiriesSeries(),
        ]);
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

    private function monthlyEnquiriesSeries(): array
    {
        $months = $this->lastSixMonths();
        $driver = DB::getDriverName();
        $monthKey = $driver === 'sqlite'
            ? "strftime('%Y-%m', created_at)"
            : "DATE_FORMAT(created_at, '%Y-%m')";

        $rows = BrandingEnquiry::query()
            ->selectRaw("{$monthKey} as ym, COUNT(*) as total")
            ->where('created_at', '>=', $months[0]['start'])
            ->groupBy('ym')
            ->pluck('total', 'ym');

        return [
            'labels' => array_column($months, 'label'),
            'values' => array_map(
                fn (array $month) => (int) ($rows[$month['key']] ?? 0),
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
