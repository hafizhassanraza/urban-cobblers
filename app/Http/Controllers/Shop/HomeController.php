<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Shop/Home', [
            'newArrivals' => Product::with('category')
                ->where('is_active', true)
                ->latest()
                ->take(10)
                ->get(),
            'categories' => Category::where('is_active', true)
                ->withCount(['products' => fn ($q) => $q->where('is_active', true)])
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get(),
            'readyToWear' => Product::with('category')
                ->where('is_active', true)
                ->where('is_ready_to_wear', true)
                ->inRandomOrder()
                ->take(8)
                ->get(),
            'saleProducts' => Product::with('category')
                ->where('is_active', true)
                ->whereNotNull('compare_price')
                ->whereColumn('compare_price', '>', 'price')
                ->take(10)
                ->get(),
        ]);
    }
}
