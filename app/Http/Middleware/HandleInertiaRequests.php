<?php

namespace App\Http\Middleware;

use App\Models\Category;
use App\Models\Order;
use App\Services\CartService;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    public function share(Request $request): array
    {
        $cart = app(CartService::class);

        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user(),
            ],
            'cartCount' => $cart->count(),
            'activeOrderCount' => fn () => $request->user()
                ? Order::where('user_id', $request->user()->id)
                    ->whereIn('status', Order::activeStatuses())
                    ->count()
                : 0,
            'navCategories' => fn () => Category::where('is_active', true)
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get(['id', 'name', 'slug']),
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
            ],
            'developer' => fn () => config('site.developer'),
        ];
    }
}
