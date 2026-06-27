<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CustomerController extends Controller
{
    public function index(Request $request): Response
    {
        $query = User::query()
            ->where('is_admin', false)
            ->withCount('orders')
            ->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        return Inertia::render('Admin/Customers/Index', [
            'customers' => $query->paginate(15)->withQueryString(),
            'filters' => $request->only('search'),
        ]);
    }

    public function show(User $customer): Response
    {
        abort_if($customer->is_admin, 404);

        $customer->loadCount('orders', 'reviews');

        return Inertia::render('Admin/Customers/Show', [
            'customer' => $customer,
            'orders' => Order::with('items')
                ->where('user_id', $customer->id)
                ->latest()
                ->paginate(10),
            'totalSpent' => Order::where('user_id', $customer->id)
                ->whereNotIn('status', ['cancelled'])
                ->sum('total'),
        ]);
    }
}
