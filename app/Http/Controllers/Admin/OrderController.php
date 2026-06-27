<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class OrderController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Order::with('user')->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                    ->orWhere('shipping_name', 'like', "%{$search}%")
                    ->orWhere('shipping_email', 'like', "%{$search}%")
                    ->orWhere('tracking_number', 'like', "%{$search}%");
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $statusCounts = Order::query()
            ->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        return Inertia::render('Admin/Orders/Index', [
            'orders' => $query->paginate(15)->withQueryString(),
            'statuses' => Order::statuses(),
            'statusCounts' => $statusCounts,
            'filters' => $request->only(['status', 'search', 'date_from', 'date_to']),
        ]);
    }

    public function show(Order $order): Response
    {
        $order->load(['items.product', 'user', 'statusHistories.updatedBy']);

        return Inertia::render('Admin/Orders/Show', [
            'order' => $this->formatOrder($order),
            'statuses' => Order::statuses(),
            'carriers' => Order::carriers(),
        ]);
    }

    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:'.implode(',', Order::statuses())],
            'tracking_number' => ['nullable', 'string', 'max:100'],
            'carrier' => ['nullable', 'in:'.implode(',', Order::carriers())],
            'note' => ['nullable', 'string', 'max:500'],
        ]);

        $this->applyStatusUpdate($order, $validated, $request->user());

        return back()->with('success', 'Order updated.');
    }

    public function update(Request $request, Order $order): RedirectResponse
    {
        $validated = $request->validate([
            'shipping_name' => ['required', 'string', 'max:255'],
            'shipping_email' => ['required', 'email', 'max:255'],
            'shipping_phone' => ['nullable', 'string', 'max:50'],
            'shipping_address' => ['required', 'string'],
            'shipping_city' => ['required', 'string', 'max:100'],
            'shipping_zip' => ['required', 'string', 'max:20'],
            'notes' => ['nullable', 'string', 'max:500'],
            'admin_notes' => ['nullable', 'string', 'max:2000'],
            'status' => ['required', 'in:'.implode(',', Order::statuses())],
            'tracking_number' => ['nullable', 'string', 'max:100'],
            'carrier' => ['nullable', 'in:'.implode(',', Order::carriers())],
            'note' => ['nullable', 'string', 'max:500'],
        ]);

        DB::transaction(function () use ($order, $validated, $request) {
            $statusChanged = $order->status !== $validated['status'];
            $trackingChanged = ($order->tracking_number ?? '') !== ($validated['tracking_number'] ?? '')
                || ($order->carrier ?? '') !== ($validated['carrier'] ?? '');

            $order->update([
                'shipping_name' => $validated['shipping_name'],
                'shipping_email' => $validated['shipping_email'],
                'shipping_phone' => $validated['shipping_phone'],
                'shipping_address' => $validated['shipping_address'],
                'shipping_city' => $validated['shipping_city'],
                'shipping_zip' => $validated['shipping_zip'],
                'notes' => $validated['notes'],
                'admin_notes' => $validated['admin_notes'],
            ]);

            if ($statusChanged || $trackingChanged) {
                $this->applyStatusUpdate($order, $validated, $request->user(), refreshOrder: false);
            } elseif (! empty($validated['note'])) {
                $order->recordStatus($order->status, $validated['note'], $request->user());
            }
        });

        return back()->with('success', 'Order saved.');
    }

    protected function applyStatusUpdate(Order $order, array $validated, $admin, bool $refreshOrder = true): void
    {
        if ($refreshOrder) {
            $order->refresh();
        }

        $previousStatus = $order->status;
        $newStatus = $validated['status'];

        DB::transaction(function () use ($order, $previousStatus, $newStatus, $validated, $admin) {
            if ($newStatus === 'cancelled' && $previousStatus !== 'cancelled') {
                $this->restoreStock($order);
            }

            if ($previousStatus === 'cancelled' && $newStatus !== 'cancelled') {
                $this->deductStock($order);
            }

            $updates = [
                'status' => $newStatus,
                'tracking_number' => $validated['tracking_number'] ?? $order->tracking_number,
                'carrier' => $validated['carrier'] ?? $order->carrier,
            ];

            if ($newStatus === 'shipped' && ! $order->shipped_at) {
                $updates['shipped_at'] = now();
            }

            if ($newStatus === 'delivered') {
                if (! $order->shipped_at) {
                    $updates['shipped_at'] = now();
                }
                if (! $order->delivered_at) {
                    $updates['delivered_at'] = now();
                }
            }

            if ($newStatus === 'cancelled') {
                $updates['shipped_at'] = null;
                $updates['delivered_at'] = null;
            }

            $order->update($updates);

            $note = $validated['note'] ?? $this->defaultStatusNote($previousStatus, $newStatus, $order);

            if ($previousStatus !== $newStatus || ! empty($validated['note']) || ! empty($validated['tracking_number'])) {
                $order->recordStatus($newStatus, $note, $admin, [
                    'tracking_number' => $order->tracking_number,
                    'carrier' => $order->carrier,
                ]);
            }
        });
    }

    protected function defaultStatusNote(string $previous, string $new, Order $order): ?string
    {
        if ($previous === $new) {
            return null;
        }

        return match ($new) {
            'processing' => 'Your order is being prepared in our workshop.',
            'shipped' => $order->tracking_number
                ? 'Your order has shipped. Tracking: '.$order->tracking_number
                : 'Your order has shipped.',
            'delivered' => 'Your order has been delivered. Thank you for shopping with us!',
            'cancelled' => 'This order has been cancelled.',
            default => 'Order status updated to '.Order::statusLabel($new).'.',
        };
    }

    protected function restoreStock(Order $order): void
    {
        foreach ($order->items as $item) {
            if ($item->product_id) {
                Product::where('id', $item->product_id)->increment('stock', $item->quantity);
            }
        }
    }

    protected function deductStock(Order $order): void
    {
        foreach ($order->items as $item) {
            if ($item->product_id) {
                Product::where('id', $item->product_id)->decrement('stock', $item->quantity);
            }
        }
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
