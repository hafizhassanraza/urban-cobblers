<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductReview;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class ProductController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Product::with('category')->where('is_active', true);

        if ($request->boolean('sale')) {
            $query->whereNotNull('compare_price')->whereColumn('compare_price', '>', 'price');
        }

        if ($request->boolean('ready')) {
            $query->where('is_ready_to_wear', true);
        }

        if ($request->filled('category')) {
            $query->whereHas('category', fn ($q) => $q->where('slug', $request->category));
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('short_description', 'like', "%{$search}%");
            });
        }

        $sort = $request->get('sort', 'latest');
        match ($sort) {
            'price_asc' => $query->orderBy('price'),
            'price_desc' => $query->orderByDesc('price'),
            'name' => $query->orderBy('name'),
            default => $query->latest(),
        };

        $pageTitle = 'Shop All Shoes';
        if ($request->boolean('sale')) {
            $pageTitle = 'Sale Collection';
        } elseif ($request->boolean('ready')) {
            $pageTitle = 'Ready to Wear';
        }

        return Inertia::render('Shop/Products/Index', [
            'products' => $query->paginate(12)->withQueryString(),
            'categories' => Category::where('is_active', true)->orderBy('sort_order')->get(),
            'filters' => $request->only(['category', 'search', 'sort', 'sale', 'ready']),
            'pageTitle' => $pageTitle,
        ]);
    }

    public function show(string $slug): Response
    {
        $product = Product::with('category')
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $related = Product::where('is_active', true)
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->take(4)
            ->get();

        $approvedReviews = $product->approvedReviews()
            ->with('user:id,name')
            ->latest()
            ->get()
            ->map(fn ($review) => [
                'id' => $review->id,
                'rating' => $review->rating,
                'title' => $review->title,
                'body' => $review->body,
                'is_verified_purchase' => $review->is_verified_purchase,
                'reviewer_name' => ProductReview::reviewerDisplayName($review->user->name),
                'created_at' => $review->created_at->toISOString(),
                'is_own' => Auth::id() === $review->user_id,
            ]);

        $reviewStats = [
            'average' => round((float) $product->approvedReviews()->avg('rating'), 1) ?: 0,
            'count' => $product->approvedReviews()->count(),
        ];

        $userReview = Auth::check()
            ? $product->reviews()->where('user_id', Auth::id())->first()
            : null;

        return Inertia::render('Shop/Products/Show', [
            'product' => $product,
            'relatedProducts' => $related,
            'reviews' => $approvedReviews,
            'reviewStats' => $reviewStats,
            'userReview' => $userReview ? [
                'id' => $userReview->id,
                'rating' => $userReview->rating,
                'title' => $userReview->title,
                'body' => $userReview->body,
                'is_approved' => $userReview->is_approved,
            ] : null,
        ]);
    }

    public function quickView(string $slug)
    {
        $product = Product::with('category')
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        return response()->json($product);
    }
}
