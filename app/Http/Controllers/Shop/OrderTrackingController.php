<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class OrderTrackingController extends Controller
{
    public function index(Request $request): Response
    {
        return Inertia::render('Shop/Orders/Track', [
            'order' => null,
            'lookup' => $request->only(['order_number', 'email']),
        ]);
    }

    public function lookup(Request $request): Response|RedirectResponse
    {
        $validated = $request->validate([
            'order_number' => ['required', 'string', 'max:50'],
            'email' => ['required', 'email', 'max:255'],
        ]);

        $order = Order::with(['statusHistories.updatedBy'])
            ->where('order_number', strtoupper(trim($validated['order_number'])))
            ->whereRaw('LOWER(shipping_email) = ?', [strtolower(trim($validated['email']))])
            ->first();

        if (! $order) {
            return back()
                ->withInput()
                ->with('error', 'No order found with that order number and email address.');
        }

        return Inertia::render('Shop/Orders/Track', [
            'order' => $this->formatOrder($order),
            'lookup' => $validated,
        ]);
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
