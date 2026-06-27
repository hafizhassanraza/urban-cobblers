<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class OrderController extends Controller
{
    public function index(Request $request): Response
    {
        $baseQuery = Order::query()
            ->where('user_id', $request->user()->id)
            ->latest();

        $activeOrders = (clone $baseQuery)
            ->whereIn('status', Order::activeStatuses())
            ->get()
            ->map(fn (Order $order) => $this->formatOrderSummary($order, true));

        $pastOrders = (clone $baseQuery)
            ->whereNotIn('status', Order::activeStatuses())
            ->paginate(10)
            ->through(fn (Order $order) => $this->formatOrderSummary($order));

        return Inertia::render('Shop/Orders/Index', [
            'activeOrders' => $activeOrders,
            'pastOrders' => $pastOrders,
        ]);
    }

    public function show(Request $request, Order $order): Response
    {
        abort_unless($order->user_id === $request->user()->id, 403);

        $order->load(['statusHistories.updatedBy']);

        return Inertia::render('Shop/Orders/Show', [
            'order' => $this->formatOrder($order),
        ]);
    }

    protected function formatOrderSummary(Order $order, bool $withTracking = false): array
    {
        $summary = [
            ...$order->toArray(),
            'status_label' => Order::statusLabel($order->status),
        ];

        if ($withTracking) {
            $summary['carrier_label'] = Order::carrierLabel($order->carrier);
            $summary['tracking_url'] = $order->trackingUrl();
            $summary['status_progress'] = $order->statusProgress();
        }

        return $summary;
    }

    protected function formatOrder(Order $order): array
    {
        return [
            ...$order->toArray(),
            'status_label' => Order::statusLabel($order->status),
            'carrier_label' => Order::carrierLabel($order->carrier),
            'tracking_url' => $order->trackingUrl(),
            'status_progress' => $order->statusProgress(),
            'status_histories' => $order->statusHistories->map(fn ($h) => [
                'id' => $h->id,
                'status' => $h->status,
                'status_label' => Order::statusLabel($h->status),
                'note' => $h->note,
                'tracking_number' => $h->tracking_number,
                'carrier' => $h->carrier,
                'carrier_label' => Order::carrierLabel($h->carrier),
                'updated_by' => $h->updatedBy?->name,
                'created_at' => $h->created_at,
            ]),
        ];
    }
}
