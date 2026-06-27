<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Services\AdminAnalyticsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class ReportController extends Controller
{
    public function __construct(protected AdminAnalyticsService $analytics) {}

    public function index(Request $request): Response
    {
        $dateFrom = $request->input('date_from', now()->subDays(30)->toDateString());
        $dateTo = $request->input('date_to', now()->toDateString());

        $ordersQuery = Order::query()
            ->whereDate('created_at', '>=', $dateFrom)
            ->whereDate('created_at', '<=', $dateTo);

        $validOrdersQuery = (clone $ordersQuery)->whereNotIn('status', ['cancelled']);

        return Inertia::render('Admin/Reports/Index', [
            'filters' => [
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
            ],
            'summary' => [
                'total_orders' => (clone $ordersQuery)->count(),
                'total_revenue' => (float) (clone $validOrdersQuery)->sum('total'),
                'average_order' => (float) ((clone $validOrdersQuery)->avg('total') ?? 0),
                'cancelled_orders' => (clone $ordersQuery)->where('status', 'cancelled')->count(),
                'delivered_orders' => (clone $ordersQuery)->where('status', 'delivered')->count(),
            ],
            'revenueByMonth' => $this->analytics->revenueByMonth(6),
            'ordersByDay' => $this->analytics->ordersByDay(14),
            'salesByStatus' => (clone $ordersQuery)
                ->select('status', DB::raw('count(*) as count'))
                ->groupBy('status')
                ->pluck('count', 'status'),
            'topProducts' => OrderItem::query()
                ->join('orders', 'orders.id', '=', 'order_items.order_id')
                ->whereDate('orders.created_at', '>=', $dateFrom)
                ->whereDate('orders.created_at', '<=', $dateTo)
                ->whereNotIn('orders.status', ['cancelled'])
                ->select('order_items.product_name', DB::raw('SUM(order_items.quantity) as total_qty'), DB::raw('SUM(order_items.total) as total_revenue'))
                ->groupBy('order_items.product_name')
                ->orderByDesc('total_revenue')
                ->take(10)
                ->get(),
        ]);
    }
}
