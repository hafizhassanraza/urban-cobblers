<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductReview;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ReviewController extends Controller
{
    public function index(Request $request): Response
    {
        $query = ProductReview::with(['product:id,name,slug', 'user:id,name,email'])
            ->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('body', 'like', "%{$search}%")
                    ->orWhereHas('product', fn ($p) => $p->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('status')) {
            $query->where('is_approved', $request->status === 'approved');
        }

        return Inertia::render('Admin/Reviews/Index', [
            'reviews' => $query->paginate(15)->withQueryString(),
            'filters' => $request->only(['search', 'status']),
        ]);
    }

    public function updateApproval(ProductReview $review): RedirectResponse
    {
        $review->update(['is_approved' => ! $review->is_approved]);

        return back()->with('success', 'Review visibility updated.');
    }

    public function destroy(ProductReview $review): RedirectResponse
    {
        $review->delete();

        return back()->with('success', 'Review deleted.');
    }
}
