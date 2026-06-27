<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Services\AdminAnalyticsService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportController extends Controller
{
    public function __construct(protected AdminAnalyticsService $analytics) {}

    public function orders(): StreamedResponse
    {
        $filename = 'orders-'.now()->format('Y-m-d').'.csv';

        return response()->streamDownload(function () {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Order Number', 'Customer', 'Email', 'Status', 'Carrier', 'Tracking', 'Subtotal', 'Shipping', 'Total', 'Shipped At', 'Delivered At', 'Date']);

            Order::with('user')
                ->latest()
                ->chunk(100, function ($orders) use ($handle) {
                    foreach ($orders as $order) {
                        fputcsv($handle, [
                            $order->order_number,
                            $order->user?->name ?? $order->shipping_name,
                            $order->shipping_email,
                            $order->status,
                            $order->carrier,
                            $order->tracking_number,
                            $order->subtotal,
                            $order->shipping,
                            $order->total,
                            $order->shipped_at?->toDateTimeString(),
                            $order->delivered_at?->toDateTimeString(),
                            $order->created_at->toDateTimeString(),
                        ]);
                    }
                });

            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    public function ordersPdf(Request $request)
    {
        $query = Order::with('user')->latest();

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $orders = $query->get();

        $pdf = Pdf::loadView('admin.reports.orders', [
            'orders' => $orders,
            'generatedAt' => now(),
            'filters' => $request->only(['date_from', 'date_to', 'status']),
        ])->setPaper('a4', 'landscape');

        return $pdf->download('orders-'.now()->format('Y-m-d').'.pdf');
    }

    public function salesPdf(Request $request)
    {
        $dateFrom = $request->input('date_from', now()->subDays(30)->toDateString());
        $dateTo = $request->input('date_to', now()->toDateString());

        $ordersQuery = Order::query()
            ->whereDate('created_at', '>=', $dateFrom)
            ->whereDate('created_at', '<=', $dateTo);

        $validOrders = (clone $ordersQuery)->whereNotIn('status', ['cancelled']);

        $pdf = Pdf::loadView('admin.reports.sales-summary', [
            'generatedAt' => now(),
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'summary' => [
                'total_orders' => (clone $ordersQuery)->count(),
                'total_revenue' => (float) (clone $validOrders)->sum('total'),
                'average_order' => (float) ((clone $validOrders)->avg('total') ?? 0),
                'cancelled_orders' => (clone $ordersQuery)->where('status', 'cancelled')->count(),
            ],
            'salesByStatus' => (clone $ordersQuery)
                ->select('status', DB::raw('count(*) as count'), DB::raw('sum(total) as revenue'))
                ->groupBy('status')
                ->get(),
            'topProducts' => $this->analytics->topProducts(10, $dateFrom, $dateTo),
            'revenueByMonth' => $this->analytics->revenueByMonth(6),
        ]);

        return $pdf->download('sales-report-'.now()->format('Y-m-d').'.pdf');
    }

    public function productsPdf()
    {
        $products = Product::with('category')
            ->orderBy('name')
            ->get();

        $pdf = Pdf::loadView('admin.reports.products', [
            'products' => $products,
            'generatedAt' => now(),
        ])->setPaper('a4', 'landscape');

        return $pdf->download('products-'.now()->format('Y-m-d').'.pdf');
    }
}
