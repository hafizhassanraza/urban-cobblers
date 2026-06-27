<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;

class AdminAnalyticsService
{
    public function revenueByMonth(int $months = 6): array
    {
        $results = [];

        for ($i = $months - 1; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $start = $date->copy()->startOfMonth();
            $end = $date->copy()->endOfMonth();

            $results[] = [
                'label' => $date->format('M Y'),
                'revenue' => (float) Order::whereNotIn('status', ['cancelled'])
                    ->whereBetween('created_at', [$start, $end])
                    ->sum('total'),
                'orders' => Order::whereBetween('created_at', [$start, $end])->count(),
            ];
        }

        return $results;
    }

    public function ordersByDay(int $days = 14): array
    {
        $results = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $start = now()->subDays($i)->startOfDay();
            $end = $start->copy()->endOfDay();

            $results[] = [
                'label' => $start->format('M d'),
                'orders' => Order::whereBetween('created_at', [$start, $end])->count(),
            ];
        }

        return $results;
    }

    public function salesByStatus(): array
    {
        return Order::query()
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();
    }

    public function topProducts(int $limit = 5, ?string $dateFrom = null, ?string $dateTo = null): array
    {
        $query = OrderItem::query()
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->whereNotIn('orders.status', ['cancelled']);

        if ($dateFrom) {
            $query->whereDate('orders.created_at', '>=', $dateFrom);
        }

        if ($dateTo) {
            $query->whereDate('orders.created_at', '<=', $dateTo);
        }

        return $query
            ->select('order_items.product_name', DB::raw('SUM(order_items.quantity) as total_qty'), DB::raw('SUM(order_items.total) as total_revenue'))
            ->groupBy('order_items.product_name')
            ->orderByDesc('total_qty')
            ->take($limit)
            ->get()
            ->toArray();
    }
}
