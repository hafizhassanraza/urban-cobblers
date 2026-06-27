<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CategoryController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Shop/Categories/Index', [
            'categories' => Category::where('is_active', true)
                ->withCount(['products' => fn ($q) => $q->where('is_active', true)])
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function show(Request $request, string $slug): Response
    {
        $category = Category::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $query = Product::with('category')
            ->where('category_id', $category->id)
            ->where('is_active', true);

        if ($request->filled('style')) {
            $style = $request->style;
            $query->where(function ($q) use ($style) {
                $q->where('name', 'like', "%{$style}%")
                    ->orWhere('slug', 'like', "%{$style}%");
            });
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $sort = $request->get('sort', 'latest');
        match ($sort) {
            'price_asc' => $query->orderBy('price'),
            'price_desc' => $query->orderByDesc('price'),
            'name' => $query->orderBy('name'),
            default => $query->latest(),
        };

        return Inertia::render('Shop/Categories/Show', [
            'category' => $category,
            'products' => $query->paginate(12)->withQueryString(),
            'filters' => $request->only(['search', 'sort', 'style']),
        ]);
    }
}
