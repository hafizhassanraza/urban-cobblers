<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\ContactMessage;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductReview;
use App\Models\Setting;
use App\Models\User;
use App\Services\AdminAnalyticsService;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __construct(protected AdminAnalyticsService $analytics) {}

    public function index(): Response
    {
        $lowStockThreshold = Setting::getInt('low_stock_threshold', 5);
        $monthStart = now()->startOfMonth();

        return Inertia::render('Admin/Dashboard', [
            'stats' => [
                'products' => Product::count(),
                'active_products' => Product::where('is_active', true)->count(),
                'categories' => Category::count(),
                'orders' => Order::count(),
                'customers' => User::where('is_admin', false)->count(),
                'revenue' => Order::whereNotIn('status', ['cancelled'])->sum('total'),
                'revenue_month' => Order::whereNotIn('status', ['cancelled'])
                    ->where('created_at', '>=', $monthStart)
                    ->sum('total'),
                'pending_orders' => Order::where('status', 'pending')->count(),
                'low_stock' => Product::where('is_active', true)->where('stock', '<=', $lowStockThreshold)->count(),
                'pending_reviews' => ProductReview::where('is_approved', false)->count(),
                'unread_messages' => ContactMessage::where('is_read', false)->count(),
            ],
            'recentOrders' => Order::with('user')->latest()->take(8)->get(),
            'lowStockProducts' => Product::where('is_active', true)
                ->where('stock', '<=', $lowStockThreshold)
                ->orderBy('stock')
                ->take(5)
                ->get(['id', 'name', 'slug', 'stock', 'image']),
            'topProducts' => $this->analytics->topProducts(5),
            'revenueByMonth' => $this->analytics->revenueByMonth(6),
            'ordersByDay' => $this->analytics->ordersByDay(14),
            'salesByStatus' => $this->analytics->salesByStatus(),
        ]);
    }
}
