<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductReview;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function store(Request $request, Product $product): RedirectResponse
    {
        if (! $product->is_active) {
            abort(404);
        }

        if ($product->reviews()->where('user_id', Auth::id())->exists()) {
            return back()->with('error', 'You have already reviewed this product.');
        }

        $validated = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'title' => ['nullable', 'string', 'max:120'],
            'body' => ['required', 'string', 'min:10', 'max:2000'],
        ]);

        ProductReview::create([
            ...$validated,
            'product_id' => $product->id,
            'user_id' => Auth::id(),
            'is_verified_purchase' => $this->hasVerifiedPurchase($product->id),
            'is_approved' => true,
        ]);

        return back()->with('success', 'Thank you! Your review has been published.');
    }

    public function update(Request $request, ProductReview $review): RedirectResponse
    {
        if ($review->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'title' => ['nullable', 'string', 'max:120'],
            'body' => ['required', 'string', 'min:10', 'max:2000'],
        ]);

        $review->update($validated);

        return back()->with('success', 'Your review has been updated.');
    }

    public function destroy(ProductReview $review): RedirectResponse
    {
        if ($review->user_id !== Auth::id() && ! Auth::user()?->isAdmin()) {
            abort(403);
        }

        $review->delete();

        return back()->with('success', 'Review removed.');
    }

    protected function hasVerifiedPurchase(int $productId): bool
    {
        return OrderItem::query()
            ->where('product_id', $productId)
            ->whereHas('order', fn ($q) => $q
                ->where('user_id', Auth::id())
                ->where('status', 'delivered'))
            ->exists();
    }
}
